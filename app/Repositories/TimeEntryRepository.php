<?php

namespace App\Repositories;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TimeEntryRepository
{
    public function __construct()
    {
        $this->hourlyRateRepository = new HourlyRateRepository;
    }

    public function save($timeEntry, $timeEntryData)
    {
        if (is_null($timeEntry)) {
            $descriptions = [];
            $totalTime = 0;
            $description = preg_split('/([0-9]{3,4})/',
                $timeEntryData->description,
                -1,
                PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

            if (is_numeric($description[count($description) - 1])) {
                for ($i = 0; $i <= (count($description) - 2); $i += 2) {
                    $time = round($this->getTimeInDecimals($description[$i], $description[$i+2]), 2);
                    $descriptions[] = $description[$i].' - '.$description[$i+2].' '.trim($description[$i+1]).' ('.number_format($time, 2, '.', '').')';
                    $totalTime += $time;
                }
            }

            if (empty($descriptions)) {
                $descriptions[] = trim($timeEntryData->description);
            }
        }

        $timeEntryArray = [
            'requirement_id' => $timeEntryData->requirement_id,
            'hourly_rate_id' => $this->hourlyRateRepository->currentRateId(),
            'description' => $timeEntry ? $timeEntryData->description : implode("\n", $descriptions),
            'time' => $timeEntry ? $timeEntryData->time : round($totalTime, 2),
        ];

        if ($timeEntry) {
            return $timeEntry->update($timeEntryArray);
        }

        $timeEntryArray['user_id'] = Auth::id();
        return TimeEntry::create($timeEntryArray);
    }

    public function getTimeInDecimals(string $start, string $end)
    {
        $start = strlen($start) == 3 ? '0'.$start : $start;
        $end = strlen($end) == 3 ? '0'.$end : $end;
        $start = strtotime($start);
        $end = strtotime($end);

        return round(abs($end - $start) / (60*60), 2);
    }

    public function all()
    {
        return Auth::user()
            ->timeEntries()
            ->latest()
            ->paginate(config('env.page_limit'));
    }

    public function todaysEntries()
    {
        return Auth::user()
            ->timeEntries()
            ->where('created_at', '>=', Carbon::today())
            ->latest()
            ->paginate(config('env.page_limit'));
    }

}
