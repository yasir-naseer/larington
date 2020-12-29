<?php namespace App\Jobs;

use App\Club;
use App\User;
use stdClass;
use App\ErrorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;

class OrdersCreateJob implements ShouldQueue
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
        
        try{
            
            foreach($this->data->note_attributes as $attribute) {
                if($attribute->name == 'club_id') {
                    $club_id = $attribute->value;
                }
                if($attribute->name == 'points') {
                    $points = $attribute->value;
                }
                if($attribute->name == 'email_phone') {
                    $customer_email_phone = $attribute->value;
                }
            }
     
            $club = Club::where('club_id', $club_id)->first();
            $user = User::find($club->store_id);
     
            $response = Http::asForm()->post('https://larington.com/api/', [
                'command' => 'issuepoints',
                'platform' => 'shopifyapp',
                'posted_sid' => $user->merchant_token,
                'clubid' => $club->club_id,
                'merchid' => $club->company_id,
                'memberphoneoremail' => $customer_email_phone,
                'points' => $points,
            ]);
        }
        catch(\Exception $e) {
            $log = new ErrorLog();
            $log->message = $e->getMessage();
            $log->save();
        }
        
    }
}
