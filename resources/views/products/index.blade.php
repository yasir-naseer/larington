@extends('layouts.backend')

@section('content')
<div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">All Products</h3>
                        </div>
                        <div class="block-content">
                            <!-- Search Form -->
                            <form action="be_pages_ecom_products.html" method="POST" onsubmit="return false;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-alt" id="one-ecom-products-search" name="one-ecom-products-search" placeholder="Search all products..">
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-body border-0">
                                                <i class="fa fa-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- END Search Form -->

                            <!-- All Products Table -->
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-left" style="width: 100px;">Product</th>
                                            <th class="d-none d-md-table-cell"></th>
                                            <th class="text-left">Add Reward Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                        <tr>
                                            <td class="text-center font-size-sm">
                                                <img src="{{ $product->img }}" alt="" style="width: 90px; height: auto">
                                            </td>
                                            <td class="d-none d-md-table-cell font-size-sm">
                                                <a>{{ $product->title }}</a>
                                            </td>
                                            
                                            <td class="text-left font-size-sm">
                                                @if(count(auth()->user()->clubs)>0)
                                                    <table class="table">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Club Name</th>
                                                                <th>Reward Points</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach(auth()->user()->clubs as $club)
                                                                <tr>
                                                                    <td>{{ $club->club_name }}</td>
                                                                    <td>
                                                                        <form class="form-inline" action="{{ route('rewards.store') }}" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="club_id" value="{{ $club->club_id }}">
                                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                            @php
                                                                                $reward = App\Reward::where('product_id', $product->id)->where('club_id', $club->club_id)->first();
                                                                                $points = 0;
                                                                                if($reward) {
                                                                                    $points = $reward->reward_points;
                                                                                }
                                                                            @endphp
                                                                            <input type="text" class="form-control" name="reward_points" value="{{ $points }}">
                                                                            <button class="btn btn-sm btn-alt-primary" style="padding: 7px; " type="submit" data-toggle="tooltip" title="Save">
                                                                                <i class="fa fa-fw fa-plus text-dark"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end">
                                    {{ $products->links() }}
                                </div>
                            </div>
                            <!-- END All Products Table -->
                        </div>
                    </div>

@endsection