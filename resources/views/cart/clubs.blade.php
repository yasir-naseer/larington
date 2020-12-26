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
               <td style="text-align:right; padding-right:0;" class="apply-point-div">
                   <form action="{{ route('cart.apply.points') }}" method="POST">
                        <label for="">Choose Reward club</label>
                        <select name="club_id" id="" class="point-apply-club">
                            @foreach($data as $item)
                                <option value="{{ $item['club_id'] }}"> {{ $item['club_name'] }} </option>
                            @endforeach
                        </select>
                        <br><br>
                        <input class="member" type="text" placeholder="Enter your email/phone">
                        <br><br>
                        <button type="button" class="check-point-apply-btn" style="background: #d9534f;padding: 10px 15px;color: white;border: 0;">Pay with points</button>
                        
                        <div class="points-section" style="display:none;">
                            <input class="point-applied" type="number" min="2" placeholder="Enter number of points to spend">
                            <button type="button" class="point-apply-btn" style="background: #d9534f;padding: 10px 15px;color: white;border: 0;">Pay with points</button>
                        </div>
                   </form>
                   <br>
                   <span style="font-size: 14px; color: #d9534f; display:none;" class="request-error"></span>
               </td>
               <td style="text-align:right; display:none;" class="apply-pin-div">
                   <form action="{{ route('cart.apply.pin') }}" method="POST">
                        <input type="hidden" class="club_id" val="">
                        <input class="point-applied"  val="" type="hidden">
                        <input class="member" type="hidden" val="">
                        <input class="pin" type="text" placeholder="Enter your PIN">
                        <button type="button" class="pin-apply-btn" style="background: #53a993;padding: 10px 15px;color: white;border: 0;">Apply PIN</button>
                   </form>
                   <br>
                   <span style="font-size: 14px; color: #53a993;" class="request-success"></span>
               </td>
               
            </tr>
    </tbody>
</table>
@else
    <div style="text-align: center;">
        <p>No Reward Points found for current cart</p>
    </div>
@endif
