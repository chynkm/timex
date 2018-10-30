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

    protected function nameRequired()
    {
        if(strlen($this->name) == 0) {
            $this->error = 'Please provide a name for the project.';
            return false;
        }

        return true;
    }

    protected function nameExists()
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

    public function getTimeEntries()
    {
        $sql = "SELECT te.id, p.name project_name, r.name requirement_name, description, time, inr,
            te.created_at FROM time_entries te
            JOIN requirements r ON r.id = te.requirement_id
            JOIN projects p ON p.id = r.project_id";

        if(isset($this->id)) {
            $sql .= " WHERE project_id = :project_id";
        }

        $sql .= " ORDER BY created_at desc";

        $stmt = $this->container->db->prepare($sql);

        if(isset($this->id)) {
            $stmt->bindParam(':project_id', $this->id);
        }

        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetchObject()) {
            $data[] = [
                'id' => $row->id,
                'project_name' => $row->project_name,
                'requirement_name' => $row->requirement_name,
                'description' => nl2br($row->description),
                'time' => $row->time,
                'inr' => $row->inr,
                'created_at' => date('d-m-Y H:i', strtotime($row->created_at)),
            ];
        }

        return $data;
    }

    public function all()
    {
        $stmt = $this->container->db->prepare("SELECT id, name FROM projects ORDER BY name");
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
        $stmt = $this->container->db->prepare("SELECT name, created_at FROM projects WHERE id = :id");
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetchObject();

        if($row) {
            $this->name = $row->name;
            $this->createdAt = $row->created_at;
            return $this;
        }

        return null;
    }

}
