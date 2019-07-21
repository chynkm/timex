<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

    public function test_a_todo_has_many_todo_histories()
    {
        $todo = factory('App\Models\Todo')->create();

        $this->assertInstanceOf(Collection::class, $todo->todoHistories);
    }

}
