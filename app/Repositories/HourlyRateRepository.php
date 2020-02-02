<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class HourlyRateRepository
{
    public function currentRate()
    {
        $hourlyRate = Auth::user()
            ->hourlyRates()
            ->latest('id')
            ->first();

        return $hourlyRate ? $hourlyRate->rate : 0;
    }

    public function all()
    {
        return Auth::user()
            ->hourlyRates;
    }

    public function currentRateId()
    {
        $hourlyRate = Auth::user()
            ->hourlyRates()
            ->latest('id')
            ->first();

        return $hourlyRate ? $hourlyRate->id : 0;
    }
}
