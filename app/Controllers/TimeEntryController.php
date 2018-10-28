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
        $data = $request->getParsedBody();

        $project = new Project($this->container);

        if($data['projectId'] == '') {
            return $response->withJson(['status' => 'false', 'projectId' => 'Please select a project'], 422);
        }

        if($data['requirementId'] == '') {
            return $response->withJson(['status' => 'false', 'requirementId' => 'Please select a requirement'], 422);
        }

        if($data['timeBegin'] == '') {
            return $response->withJson(['status' => 'false', 'timeBegin' => 'Please enter a start time'], 422);
        }

        if($data['timeEnd'] == '') {
            return $response->withJson(['status' => 'false', 'timeEnd' => 'Please enter an end time'], 422);
        }

        if($data['description'] == '') {
            return $response->withJson(['status' => 'false', 'description' => 'Please enter the description'], 422);
        }

        $project->id = intval($data['projectId']);

        $requirement = new Requirement($this->container);
        $requirement->projectId = $project->id;
        $requirement->id = intval($data['requirementId']);

        if(! $requirement->validate()) {
            return $response->withJson(['status' => 'false', 'requirementId' => $requirement->error], 422);
        }

        $timeEntry = new TimeEntry($this->container);
        $timeEntry->requirement_id = $requirement->id;
        $timeEntry->description = isset($data['description']) ? $data['description'] : '';
        $timeEntry->time = isset($data['time']) ? floatval($data['time']) : 0;
        $timeEntry->inr = isset($data['inr']) ? floatval($data['inr']) : 0;

        if(! $timeEntry->validate()) {
            return $response->withJson(['status' => 'false', 'timeEnd' => $timeEntry->error], 422);
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
