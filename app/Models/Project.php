<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class Project
{
    protected $container;
    public $id, $name, $createdAt, $error;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function save()
    {
        $stmt = $this->container->db->prepare("INSERT INTO projects(name) VALUES(:name)");
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        $this->id = $this->container->db->lastInsertId();
    }

    public function validate()
    {
        return $this->nameRequired() && $this->nameExists();
    }

    public function nameRequired()
    {
        if(strlen($this->name) == 0) {
            $this->error = 'Please provide a name for the project.';
            return false;
        }

        return true;
    }

    public function nameExists()
    {
        $stmt = $this->container->db->prepare("SELECT count(id) project_count FROM projects WHERE name = :name");
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        $row = $stmt->fetchObject();

        if($row->project_count) {
            $this->error = 'A project already exists with the same name. Please try with a new name.';
            return false;
        }

        return true;
    }

}
