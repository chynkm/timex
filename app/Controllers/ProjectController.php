<?php

namespace App\Controllers;

use App\Models\Project;
use Psr\Container\ContainerInterface;

class ProjectController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function index($request, $response, $args)
    {
        $project = new Project($this->container);

        return $response->withJson(['status' => 'true', 'projects' => $project->all()]);
    }

}
