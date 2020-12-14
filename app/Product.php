<?php

namespace App;

use App\Reward;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function getImgAttribute() {
        if($this->image == "null") {
            return "https://lunawood.com/wp-content/uploads/2018/02/placeholder-image.png";
        }
        else{
            $image = json_decode($this->image);
            return $image->src;
        }
    }

    
}
