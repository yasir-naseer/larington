<?php

namespace App\Http\Controllers;

use App\Club;
use App\Reward;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shop = Auth::user();
        $products = Product::where('store_id', $shop->id)->latest()->paginate(5);
        return view('products.index')->with('products', $products);
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

        $clubs = array_unique(Reward::whereIn('product_id', $products_ids)->pluck('club_id')->toArray());

        $data = [];
        if(count($clubs) > 0) {
            foreach($clubs as $club) {
                array_push($data, [
                    'club_name' => Club::where('club_id',$club)->first()->club_name,
                    'club_id' => Club::where('club_id',$club)->first()->club_id,
                    'points' => Reward::where('club_id', $club)->whereIn('product_id', $products_ids)->sum('reward_points')
                ]);
            }
        }
                      
        return view('cart.clubs')
        ->with('data', $data)
        ->render();
     }

     public function cartApplyPoints(Request $request) {
        $response = Http::asForm()->post('https://larington.com/api/', [
            'command' => 'acceptpoints',
            'platform' => '',
            'posted_sid' => 'av0mcbl2guf1b2pql88c0d0bl6',
            'club_id' => 93,
            'company_id' => 146,
            'memberphoneoremail' => 'yasirnaseer.0@gmail.com'
        ]);

        dd($response->body());

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
        else {
        }
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
}
