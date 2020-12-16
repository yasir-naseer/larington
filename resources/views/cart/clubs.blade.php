@if(count($data) > 0)
<table>
    <thead>
        <tr>
            <th>Club Name</th>
            <th style="text-align:right;">Points</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                <td>
                    {{ $item['club_name'] }}
                </td>
                <td style="text-align:right;">
                    {{ $item['points']}}
                </td>
            </tr>
            
        @endforeach
            <tr>
               <td>
                    
               </td>
               <td style="text-align:right;" class="apply-point-div">
                   <form action="{{ route('cart.apply.points') }}" method="POST">
                        <select name="club_id" id="" class="point-apply-club">
                            @foreach($data as $item)
                                <option value="{{ $item['club_id'] }}"> {{ $item['club_name'] }} </option>
                            @endforeach
                        </select>
                        <input class="point-applied" type="number" placeholder="Enter number of points to spend">
                        <button type="button" class="point-apply-btn" style="background: #d9534f;padding: 10px 15px;color: white;border: 0;">Pay with points</button>
                   </form>
               </td>
            </tr>
    </tbody>
</table>
@else
    <div style="text-align: center;">
        <p>No Reward Points found for current cart</p>
    </div>
@endif
