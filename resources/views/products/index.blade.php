@extends('layouts.backend')

@section('content')
<div class="block">
    <div class="block-header block-header-default d-flex justify-content-between">
        <h3 class="block-title">All Products</h3>
        <div>
            <a href="{{ route('sync.products') }}" class="btn btn-primary">Sync Products</a>
        </div>
    </div>
    
        <div class="block-content">
            <!-- Search Form -->
            <form action="" method="GET" >
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-alt" id="" name="search" value="{{ $search}}" placeholder="Search all products..">
                        <input type="hidden" name="auth" value="{{ Auth::user() }}">
                        <div class="input-group-append">
                            <button class="input-group-text bg-body border-0">
                                <i class="fa fa-search"></i>
                            </button>
                            <a href="{{ route('products.index') }}" class="input-group-text bg-danger border-0 btn text-white">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END Search Form -->
            @if(count($products) > 0)
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
                                                            <input type="hidden" name="club_id" value="{{ $club->club_id }}" >
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}" class>
                                                            @php
                                                                $reward = App\Reward::where('product_id', $product->id)->where('club_id', $club->club_id)->first();
                                                                $points = 0;
                                                                if($reward) {
                                                                    $points = $reward->reward_points;
                                                                }
                                                            @endphp
                                                            <input type="text" class="form-control points_{{ $club->club_id }}_{{ $product->id }}" name="reward_points" value="{{ $points }}">
                                                            <button class="btn btn-sm btn-alt-primary add-btn" style="padding: 7px; " type="button" data-club="{{ $club->club_id  }}" data-product="{{ $product->id }}" data-toggle="tooltip" title="Save">
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
    @else
        <p>No Products Found</p>
    @endif
</div>

@endsection

@section('js_after')
   <script>
    $(document).ready(function() {
        $('.add-btn').click(function(){
            
            var club_id = $(this).data('club');
            var product_id = $(this).data('product');
            var reward_points = $(`.points_${club_id}_${product_id}`).val();

            $.ajax({
                url: `/rewards`,
                data: { club_id : club_id, product_id : product_id, reward_points : reward_points},
                type: 'POST',
                success: function(res) {
                    var response = res.data;
                    console.log(response);
                    if(response == 'success') {
                        toastr.success("Reward Points added Successfully!");
                    }
                }
            });
           
        }); 
    });
   </script>
@endsection