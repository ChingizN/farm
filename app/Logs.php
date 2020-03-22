<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    public static function record($data)
	{
		Logs::insert(['action' => $data[0], 'day' => $data[1]]);
	}
}