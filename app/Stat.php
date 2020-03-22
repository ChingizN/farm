<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    public static function record($data)
	{
		Stat::insert(['day' => $data[0], 'sheeps' => $data[1], 'killed' => $data[2], 'activ' => $data[3], 'maxQ' => $data[4], 'minQ' => $data[5], 'max' => $data[6], 'min' => $data[7]]);
	}
}