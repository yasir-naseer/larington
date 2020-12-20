<?php namespace App\Jobs;

use App\User;
use stdClass;
use App\Product;
use App\ErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;

class ProductsUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $this->shopDomain = ShopDomain::fromNative($this->domain);

    
        try{
    
            $shop = User::where('name', $this->shopDomain)->first();
          
            if (Product::where('id', $this->data->id)->exists()) {
                $p = Product::find($this->data->id);
            } else {
                $p = new Product();
            }

             $log = new ErrorLog();
            $log->message = 'hiii';
            $log->save();


            $log = new ErrorLog();
            $log->message = 'DAF'.$this->data->title;
            $log->save();

            $log = new ErrorLog();
            $log->message = json_encode($this->data->image->src);
            $log->save();

            $log = new ErrorLog();
            $log->message = 'dasf'.json_encode($this->data->image);
            $log->save();

            
            $log = new ErrorLog();
            $log->message = 'dsf'.$shop->id;
            $log->save();


    
            $p->id = $this->data->id;
            $p->title = $this->data->title;
            $p->image = json_encode($this->data->image);
            $p->store_id = $shop->id;
            $p->save(); 

            $log = new ErrorLog();
            $log->message = 'werw';
            $log->save();
           

            
        }
        catch(\Exception $e)
        {
            $log = new ErrorLog();
            $log->message = "Crap";
            $log->save();
        }
    }
}
