<div>
    <h2>Reward Points</h2>

    @php
        $rewards = App\Reward::where('product_id', $product->id)->get();
        $data = [];

        if($rewards) {
            foreach($rewards as $reward) {
                $club = App\Club::where('club_id',$reward->club_id)->first();
                if($club) {
                    array_push($data, [
                        'culb_name' => $club->club_name,
                        'points' => $reward->reward_points
                    ]);
                }
            }
        }    
    @endphp


    @if(count($data) > 0)
    <table>
        <thead>
            <tr>
                <th>Club Name</th>
                <th>Reward Points</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td>
                        {{ $item['culb_name'] }} 
                    </td>
                    <td>
                        {{$item['points'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No Reward Points Found</p>
    @endif
</div>