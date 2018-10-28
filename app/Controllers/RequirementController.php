<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Requirement;
use Psr\Container\ContainerInterface;

class RequirementController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function index($request, $response, $args)
    {
        $data = $request->getQueryParams();

        if(isset($data['project_id'])) {
            $project = new Project($this->container);
            $project->id = $data['project_id'];
            $project = $project->find();

            if(is_null($project)) {
                return $response->withJson([
                    'status' => 'false',
                    'projectId' => 'Sorry, we were unable to find the project.'
                ], 422);
            }
        } else {
            return $response->withJson(['status' => 'false', 'projectId' => 'Please select a project.'], 422);
        }

        return $response->withJson(['status' => 'true', 'requirements' => (new Requirement($this->container))->all($project->id)]);
    }

}
