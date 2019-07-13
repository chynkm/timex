<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    public function test_a_todo_belongs_to_a_requirement()
    {
        $todo = factory('App\Models\Todo')->create();

        $this->assertInstanceOf('App\Models\Requirement', $todo->requirement);
    }

    public function test_a_todo_belongs_to_a_user()
    {
        $todo = factory('App\Models\Todo')->create();

        $this->assertInstanceOf('App\Models\User', $todo->user);
    }

}
