<?php

namespace Tests\Unit;

use App\Repositories\TimeEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimeEntryRepositoryTest extends TestCase
{
    /**
     * @dataProvider timeEntryProvider
     */
    public function test_time_difference($input, $output, $message)
    {
        $timeEntryRepository = new TimeEntryRepository;
        $result = $timeEntryRepository->getTimeInDecimals($input['start'], $input['end']);
        $this->assertEquals($output, $result, $message);
    }

    public function timeEntryProvider()
    {
        $attributes1 = ['start' => 800, 'end' => 815];
        $attributes2 = ['start' => 1000, 'end' => 1042];
        $attributes3 = ['start' => '000', 'end' => '021'];
        $attributes4 = ['start' => 1212, 'end' => 1224];
        $attributes5 = ['start' => 1300, 'end' => 1551];

        return [
            [$attributes1, 0.25, "['start' => 800, 'end' => 815]"],
            [$attributes2, 0.70, "['start' => 1000, 'end' => 1042]"],
            [$attributes3, 0.35, "['start' => 000, 'end' => 021]"],
            [$attributes4, 0.20, "['start' => 1212, 'end' => 1224]"],
            [$attributes5, 2.85, "['start' => 1300, 'end' => 1551]"],
        ];
    }
}
