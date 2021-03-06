<?php

namespace App\Http\Controllers;

use App\Club;
use App\User;
use App\Reward;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProductsController extends Controller
{
    public function __construct() {
        $this->middleware('auth.shopify')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $shop = Auth::user();
        

        $products = Product::where('store_id', $shop->id)->newQuery();

        if($request->has('search')) {
            $products->where('title', 'LIKE', '%' . $request->input('search') . '%');
        }
        $products = $products->latest()->paginate(20);

        return view('products.index')->with('products', $products)->with('search', $request->input('search'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function getClubsForProduct($id) {
       $product = Product::find($id);
       return view('products.club')->with('product',$product)->render();
    }

    public function getClubsForCart(Request $request) {

        $products_ids = $request->products;
        $quantities = $request->quantities;

        $clubs = array_unique(Reward::whereIn('product_id', $products_ids)->pluck('club_id')->toArray());
    
        $data = [];
        if(count($clubs) > 0) {
            foreach($clubs as $club) {
                $c = Club::where('club_id',$club)->first();
                $point = 0;
                if($c){
                    foreach($products_ids as $index => $products) {
                        $point = $point + Reward::where('club_id', $club)->where('product_id', $products)->sum('reward_points') * $quantities[$index];                        
                    }
                    array_push($data, [
                        'club_name' => $c->club_name,
                        'club_id' => $c->club_id,  
                        'points' => $point
                    ]);  
                } 
            }
        }
                      
        return view('cart.clubs')
        ->with('data', $data)
        ->render();
     }

     public function cartApplyPoints(Request $request) {
        $result =explode("_",$request->club_id);
        $c_id = $result[0];
        $points_for_callback = $result[1];
        
        $club = Club::where('club_id', $c_id)->first();
        $user = User::find($club->store_id);

        $response = Http::asForm()->post('https://larington.com/api/', [
            'command' => 'acceptpoints',
            'platform' => '',
            'posted_sid' => $user->merchant_token,
            'clubid' => $club->club_id,
            'merchid' => $club->company_id,
            'memberphoneoremail' => $request->member,
            'points' => $request->points
        ]);

        
        $result = json_decode($response->body(), 1);


        if($result['res'] == 2) {
            return response()->json([
                'success' => $result['message'],
                'club_id' => $club->club_id,
                'member' => $request->member,
                'points' => $request->points,
                'points_for_callback' => $points_for_callback
            ]);
        }
        else if($result['res'] == -1) {
            return response()->json(['error' => $result['message']]);
        }
        else {
            return response()->json(['error' => $result['res']]);
        }
     }

     public function cartApplyPin(Request $request) {
        $club = Club::where('club_id', $request->club_id)->first();
        $user = User::find($club->store_id);

        $response = Http::asForm()->post('https://larington.com/api/', [
            'command' => 'acceptpoints',
            'platform' => '',
            'posted_sid' => $user->merchant_token,
            'clubid' => $club->club_id,
            'merchid' => $club->company_id,
            'memberphoneoremail' => $request->member,
            'points' => $request->points,
            'consumerpin' => $request->pin
        ]);
        
        $result = json_decode($response->body(), 1);

        if($result['res'] == 1) {

            $discount_amount = $result['resarray']['valueofpoints'];
            $discount_code = $this->createDiscount($user,$discount_amount);
            return response()->json([
                'success' => $result['message']. "Redirecting To Checkout...",
                'club_id' => $club->club_id,
                'points' => $request->points,
                'discount_code' => $discount_code,
                'member' => $request->member
            ]);
        }
        else if($result['res'] == -1) {
            return response()->json(['error' => $result['message']]);
        }
     }

     public function submitOrder(Request $request) {

        // $shop = User::find(1);
        // $products = $shop->api()->rest('GET', '/admin/orders.json');

        // dd($products);

          $response = Http::asForm()->post('https://larington.com/api/', [
            'command' => 'issuepoints',
            'platform' => 'shopifyapp',
            'posted_sid' => 'kn0pj65lenn7m9ccospjfo8gg6',
            'clubid' => '93',
            'merchid' => '146',
            'memberphoneoremail' => 'yasirnaseer.0@gmail.com',
            'points' => '1000'
        ]);

        dd($response->body());
     }


     public function createDiscount($user, $discount_amount)
     {
        
        $data = [
            "price_rule"=> [
                "title"=> "larington_dis".rand(1,1000),
                "target_type"=> "line_item",
                "target_selection"=> "all",
                "allocation_method"=> "across",
                "value_type"=> "fixed_amount",
                "value"=> '-'.$discount_amount,
                "customer_selection"=> "all",
                'starts_at'=> now()
            ]
        ];

        $response = $user->api()->rest('POST', '/admin/price_rules.json', $data);
        $price_rule = $response['body']['container']['price_rule'];

        $data = [
            "discount_code"=> [
                "code"=> $price_rule['title']
            ]
        ];

        $response = $user->api()->rest('POST', '/admin/price_rules/'.$price_rule['id'].'/discount_codes.json', $data);

        return $price_rule['title'];
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function storeProducts($next = null)
    {
        $shop = Auth::user();
        $products = $shop->api()->rest('GET', '/admin/products.json', [
            'limit' => 250,
            'page_info' => $next
        ]);


        if(!$products['errors']){
            foreach ($products['body']['container']['products'] as $product) {
                $this->createProduct($product);
            }
    
            if (isset($products['link']['next'])) {
                $this->storeProducts($products['link']['next']);
            }
        }
        
        return redirect()->back()->with('success', 'Products Synced Successfully');
    }

    public function createProduct($product)
    {
        if (Product::where('id', $product['id'])->exists()) {
            $p = Product::find($product['id']);
        } else {
            $p = new Product();
        }

        $p->id = $product['id'];
        $p->title = $product['title'];
        $p->image = json_encode($product['image']);
        $p->store_id = Auth::user()->id;
        $p->save();
    }

    public function orderPlace() {
        $shop = User::find(4);
        $orders = $shop->api()->rest('GET', '/admin/orders.json');

        $o = $orders['body']['container']['orders'][0];

        dd($o);
       

    }
}
