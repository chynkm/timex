<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
use Psr\Container\ContainerInterface;

class TimeEntryController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function store($request, $response, $args)
    {
        $data = $request->getQueryParams();

        $project = new Project($this->container);

        if(! isset($data['project_id'])) {
            $project->name = isset($data['project_name']) ? $data['project_name'] : null;
            if(! $project->validate()) {
                return $response->withJson(['status' => 'false', 'message' => $project->error], 422);
            }

            $project->save();
        } else {
            $project->id = intval($data['project_id']);
        }

        $requirement = new Requirement($this->container);
        $requirement->projectId = $project->id;

        if(! isset($data['requirement_id'])) {
            $requirement->name = isset($data['requirement_name']) ? $data['requirement_name'] : null;

            if(! $requirement->validate()) {
                return $response->withJson(['status' => 'false', 'message' => $requirement->error], 422);
            }

            $requirement->save();
        } else {
            $requirement->id = intval($data['requirement_id']);

            if(! $requirement->validate()) {
                return $response->withJson(['status' => 'false', 'message' => $requirement->error], 422);
            }
        }

        $timeEntry = new TimeEntry($this->container);
        $timeEntry->requirement_id = $requirement->id;
        $timeEntry->description = isset($data['description']) ? $data['description'] : '';
        $timeEntry->time = isset($data['time']) ? floatval($data['time']) : 0;
        $timeEntry->inr = isset($data['inr']) ? floatval($data['inr']) : 0;

        if(! $timeEntry->validate()) {
            return $response->withJson(['status' => 'false', 'message' => $timeEntry->error], 422);
        }

        $timeEntry->save();

        return $response->withJson(['status' => 'true', 'message' => 'The time entry has been saved successfully.']);
    }

    public function index($request, $response, $args)
    {
        $data = $request->getQueryParams();

        if(! isset($data['project_id'])) {
            return $response->withJson(['status' => 'false', 'message' => 'Please select a project.']);
        }

        $project = new Project($this->container);
        $project->id = intval($data['project_id']);

        return $response->withJson(['status' => 'true', 'data' => $project->getTimeEntries()]);
    }

    public function home($request, $response, $args)
    {
        return $this->container->view->render($response, 'home.php', [
            // 'name' => $args['name']
        ]);
    }
}
