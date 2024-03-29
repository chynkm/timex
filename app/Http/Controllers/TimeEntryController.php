<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeEntrySave;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Repositories\ProjectRepository;
use App\Repositories\TimeEntryRepository;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function __construct(TimeEntryRepository $timeEntryRepo, ProjectRepository $projectRepo)
    {
        $this->timeEntryRepo = $timeEntryRepo;
        $this->projectRepo = $projectRepo;
    }

    public function store(TimeEntrySave $request)
    {
        $this->timeEntryRepo
            ->save(null, $request);

        return redirect()->route('timeEntries.create')
            ->with('alert', [
                'class' => 'success',
                'message' => __('form.time_entry_saved_successfully'),
            ]);
    }

    public function create()
    {
        $timeEntry = new TimeEntry;
        $timeEntries = $this->timeEntryRepo->todaysEntries();
        $projects = $this->projectRepo
            ->all()
            ->pluck('name', 'id');

        return view('timeEntries.createEdit', compact(
            'projects',
            'timeEntries',
            'timeEntry'
        ));
    }

    public function edit(TimeEntry $timeEntry)
    {
        $projects = $this->projectRepo
            ->all()
            ->pluck('name', 'id');

        return view('timeEntries.createEdit', compact(
            'projects',
            'timeEntry'
        ));
    }

    public function update(TimeEntry $timeEntry, TimeEntrySave $request)
    {
        $this->timeEntryRepo
            ->save($timeEntry, $request);

        return redirect()->route('timeEntries.index')
            ->with('alert', [
                'class' => 'success',
                'message' => __('form.time_entry_saved_successfully'),
            ]);
    }

    public function index(Request $request)
    {
        $timeEntries = $this->timeEntryRepo->all($request);
        $projects = $this->projectRepo
            ->all()
            ->pluck('name', 'id');

        return view('timeEntries.index', compact('projects', 'timeEntries'));
    }
}
