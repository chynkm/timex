<?php

namespace App\Models;

use Psr\Container\ContainerInterface;

class TimeEntry
{
    protected $container;
    public $id, $requirement_id, $description, $time, $inr, $createdAt, $error;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
    }

    public function save()
    {
        $stmt = $this->container->db->prepare("INSERT INTO time_entries(requirement_id, description, time, inr) VALUES(:requirement_id, :description, :time, :inr)");
        $stmt->bindParam(':requirement_id', $this->requirement_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':time', $this->time);
        $stmt->bindParam(':inr', $this->inr);
        $stmt->execute();
        $this->id = $this->container->db->lastInsertId();
    }

    public function validate()
    {
        return $this->requirementIdRequired() && $this->descriptionRequired() && $this->timeRequired();
    }

    public function requirementIdRequired()
    {
        if($this->requirement_id == 0) {
            $this->error = 'Please select a requirement for the time entry.';
            return false;
        }

        return true;
    }

    public function descriptionRequired()
    {
        if(strlen($this->description) == 0) {
            $this->error = 'Please provide a description.';
            return false;
        }

        return true;
    }

    public function timeRequired()
    {
        if($this->time == 0) {
            $this->error = 'Please provide a time value.';
            return false;
        }

        return true;
    }

}
