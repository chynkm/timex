<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class Requirement
{
    protected $container;
    public $id, $projectId, $name, $createdAt, $error;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function save()
    {
        $stmt = $this->container->db->prepare("INSERT INTO requirements(project_id, name) VALUES(:project_id, :name)");
        $stmt->bindParam(':project_id', $this->projectId);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        $this->id = $this->container->db->lastInsertId();
    }

    public function validate()
    {
        return $this->verifyRequirementIdBelongsToProjectId() &&
            $this->projectIdRequired() &&
            $this->nameRequired() &&
            $this->nameExists();
    }

    protected function projectIdRequired()
    {
        if($this->projectId == 0) {
            $this->error = 'Please select a project for the requirement.';
            return false;
        }

        return true;
    }

    protected function nameRequired()
    {
        if(! $this->id && strlen($this->name) == 0) {
            $this->error = 'Please provide a name for the requirement.';
            return false;
        }

        return true;
    }

    protected function verifyRequirementIdBelongsToProjectId()
    {
        if($this->id) {
            $stmt = $this->container->db->prepare("SELECT count(id) requirement_count FROM requirements WHERE project_id = :project_id and id = :id");
            $stmt->bindParam(':project_id', $this->projectId);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            $row = $stmt->fetchObject();

            if($row->requirement_count == 0) {
                $this->error = 'This requirement name is not associated with this Project. Please create a new requirement.';
                return false;
            }
        }

        return true;
    }

    protected function nameExists()
    {
        $stmt = $this->container->db->prepare("SELECT count(id) requirement_count FROM requirements WHERE project_id = :project_id and name = :name");
        $stmt->bindParam(':project_id', $this->projectId);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();
        $row = $stmt->fetchObject();

        if($row->requirement_count) {
            $this->error = 'A requirement already exists with the same name. Please try with a new name.';
            return false;
        }

        return true;
    }

    public function all($projectId)
    {
        $stmt = $this->container->db->prepare("SELECT id, name FROM requirements WHERE project_id = :project_id ORDER BY name");
        $stmt->bindParam(':project_id', $projectId);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject()) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        return $data;
    }

    public function find()
    {
        $stmt = $this->container->db->prepare("SELECT poject_id, name, created_at project_count FROM requirements WHERE id = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetchObject();

        if($row) {
            $this->projectId = $row->project_id;
            $this->name = $row->name;
            $this->created_at = $row->created_at;
            return $this;
        }

        return null;
    }

}

