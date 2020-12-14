<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\ProductsController;

class AfterAuthorizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $product;

    public function __construct()
    {
        $this->product = new ProductsController();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $shop = Auth::user();

        $response = $shop->api()->rest('GET', '/admin/themes.json');

        foreach($response['body']['container']['themes'] as $theme) {

            $asset_not_find = true;
            $assets = $shop->api()->rest('GET',  '/admin/themes/' . $theme['id'] . '/assets.json');
            $assets = $assets['body']['container']['assets'];
            foreach ($assets as $asset) {
                if ($asset['key'] == 'snippets/larington.liquid') {
                    $asset_not_find = false;
                }
            }
            if ($asset_not_find) {
                $data = [
                    "asset" => [
                        "key" => "snippets/larington.liquid",
                        "value" => file_get_contents(asset('js/larington.liquid'))
                    ]
                ];
                $create_snippet_file = $shop->api()->rest('PUT', '/admin/themes/' . $theme['id'] . '/assets.json', $data);
            }
            $asset = $shop->api()->rest('GET', '/admin/themes/' . $theme['id'] . '/assets.json',[
                'asset[key]'=>'layout/theme.liquid'
            ]);
            $theme_liquid = $asset['body']['container']['asset']['value'];
            if (stripos($theme_liquid, "larington") == true) {
            } else {

                $head = explode('</body>', $theme_liquid);
                $content = $head[0] . "\n {% include 'larington' %} \n </body>" . $head[1];
                $data = [
                    "asset" => [
                        "key" => "layout/theme.liquid",
                        "value" => $content
                    ]
                ];
                $update_theme_file = $shop->api()->rest('PUT',  '/admin/themes/' . $theme['id'] . '/assets.json', $data);
            }

        }
        
        $this->product->storeProducts();
    }
}
