<?php

namespace App\Repositories;

use App\Models\TimeEntry;

class TimeEntryRepository
{
    public function save($timeEntryData)
    {
        $descriptions = [];
        $totalTime = 0;
        $description = preg_split('/([0-9]{3,4})/',
            $timeEntryData->description,
            -1,
            PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i <= (count($description) - 2); $i += 2) {
            $time = round($this->getTimeInDecimals($description[$i], $description[$i+2]), 2);
            $descriptions[] = $description[$i].' - '.$description[$i+2].' '.trim($description[$i+1]).' ('.number_format($time, 2, '.', '').')';
            $totalTime += $time;
        }

        TimeEntry::create([
            'requirement_id' => $timeEntryData->requirement_id,
            'hourly_rate_id' => 1, // get latest from DB
            'description' => implode("\n", $descriptions),
            'time' => round($totalTime, 2),
        ]);
    }

    public function getTimeInDecimals(string $start, string $end)
    {
        $start = strlen($start) == 3 ? '0'.$start : $start;
        $end = strlen($end) == 3 ? '0'.$end : $end;
        $start = strtotime($start);
        $end = strtotime($end);

        return round(abs($end - $start) / (60*60), 2);
    }
}
