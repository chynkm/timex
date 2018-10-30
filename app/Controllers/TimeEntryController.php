<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Requirement;
use App\Models\TimeEntry;
use Psr\Container\ContainerInterface;

class TimeEntryController
{
    protected $container;
    protected $health = 5;

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

        $project = new Project($this->container);
        if (isset($data['project_id'])) {
            $project->id = intval($data['project_id']);
        }

        return $response->withJson(['status' => 'true', 'timeEntries' => $project->getTimeEntries()]);
    }

    public function home($request, $response, $args)
    {
        return $this->container->view->render($response, 'home.php', [
            // 'name' => $args['name']
        ]);
    }

    public function stats($request, $response, $args)
    {
        $sql = "SELECT
            SUM(IF(project_id <> $this->health, time, 0)) total_time,
            SUM(IF(project_id <> $this->health, time*hourly_rate, 0)) total_rate,
            SUM(IF(project_id = $this->health, time, 0)) health_total_time,
            SUM(IF(MONTH(te.created_at) = MONTH(CURRENT_DATE()) AND YEAR(te.created_at) = YEAR(CURRENT_DATE()) AND project_id <> $this->health, time, 0)) current_month_time,
            SUM(IF(MONTH(te.created_at) = MONTH(CURRENT_DATE()) AND YEAR(te.created_at) = YEAR(CURRENT_DATE()) AND project_id <> $this->health, time*hourly_rate, 0)) current_month_rate,
            SUM(IF(MONTH(te.created_at) = MONTH(CURRENT_DATE()) AND YEAR(te.created_at) = YEAR(CURRENT_DATE()) AND project_id = $this->health, time, 0)) current_month_health_time
            FROM time_entries te
            JOIN requirements r on r.id = te.requirement_id";

        $stmt = $this->container->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetchObject();

        if($row) {
            $stats = [
                'total_time' => $row->total_time,
                'total_rate' => round($row->total_rate),
                'health_total_time' => $row->health_total_time,
                'current_month_time' => $row->current_month_time,
                'current_month_rate' => round($row->current_month_rate),
                'current_month_health_time' => round($row->current_month_health_time),
            ];
        } else {
            $stats = [
                'total_time' => 0,
                'total_rate' => 0,
                'health_total_time' => 0,
                'current_month_time' => 0,
                'current_month_rate' => 0,
                'current_month_health_time' => 0,
            ];
        }

        return $response->withJson(['status' => 'true', 'stats' => $stats]);
    }
}
