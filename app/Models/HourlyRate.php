<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HourlyRate extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
