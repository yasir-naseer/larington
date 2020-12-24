<?php

namespace App\Http\Controllers;

use App\Club;
use App\Reward;
use App\Product;
use App\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MerchantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $response = Http::asForm()->post('https://larington.com/api/', [
            'command' => 'gettoken',
            'platform' => 'shopifyapp',
            'username' => $request->email,
            'password' => $request->password
        ]);

        $result = json_decode($response->body());

        if($result->user_id == 0) {
            return redirect()->back()->with('error', 'Login Failed');
        }
        else {
            $user = Auth::user();
            $user->merchant_id = $result->user_id;
            $user->sid = $result->sid;
            $user->merchant_token = $result->token;
            $user->save();

            $response = Http::asForm()->post('https://larington.com/api/', [
                'command' => 'getclubs',
                'platform' => 'shopifyapp',
                'posted_sid' => $user->merchant_token,
            ]);

            $company = json_decode($response->body(), 1);


            if(array_key_exists('message', $company)) {
                return redirect()->back()->with('error', "No companies or clubs available. Please complete your profile on larington.com");
            }
            else {

                if(Club::where('store_id', Auth::user()->id)->exists())
                {
                    Club::where('store_id', Auth::user()->id)->delete();
                }

                foreach($company['companies'] as $company) {
                    foreach($company['clubs'] as $c) {
                    
                        $club = new Club();
                        $club->club_id = $c['clubid'];
                        $club->company_id = $company['companyid'];
                        $club->company_name = $company['dtitlos'];
                        $club->club_name = $c['clubname'];
                        //$club->deposited_amount = $c['depositedamount'];
                        $club->store_id = Auth::user()->id;
                        $club->save();
                    }
                }
            }

            $products = Product::where('store_id', Auth::user()->id)->paginate(10);
            $clubs = Club::where('store_id', Auth::user()->id)->get();

            return view('products.index')
            ->with('products', $products)
            ->with('clubs', $clubs)
            ->with('success', 'Login Successful');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function show(Merchant $merchant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function edit(Merchant $merchant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Merchant $merchant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Merchant  $merchant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Merchant $merchant)
    {
        //
    }
}
