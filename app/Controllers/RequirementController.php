<?php

namespace App\Controllers;

use App\Models\Project;
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


        if(! isset($data['project_id'])) {
            $project = Project($this->container);
            $project->name = isset($data['project_name']) ? $data['project_name'] : null;
            if(! $project->validate()) {
                return $response->withJson(['status' => 'false', 'message' => $project->error], 422);
            }

        } else {
            return $response->withJson(['status' => 'false', 'message' => 'Please select a project.'], 422);
        }

        return $response->withJson(['status' => 'true', 'data' => $project->all()]);
    }

}
