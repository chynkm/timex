<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Repositories\TimeEntryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function __construct(TimeEntryRepository $timeEntryRepository)
    {
        $this->timeEntryRepository = $timeEntryRepository;
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'requirement_id' => 'required|exists:requirements,id',
            'description' => 'required|min:3|max:65534'
        ]);

        $project = Project::find($request->project_id);
        if (auth()->user()->isNot($project->user)) {
            abort(403);
        }

        $requirement = $project->requirements
            ->firstWhere('id', $request->requirement_id);

        if (is_null($requirement)) {
            abort(403);
        }

        $this->timeEntryRepository->save($request);
    }

    public function create()
    {
        $timeEntries = TimeEntry::where('created_at', '>=', Carbon::today())->get();
        return view('timeEntries.create', compact('timeEntries'));
    }
}
