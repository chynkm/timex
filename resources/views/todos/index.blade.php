@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @lang('form.todo') ({{ $requirement->project->name.' - '.$requirement->name }})
                </div>

                <div class="card-body">
                    @include('todos.commonTodo', [
                        'requirement' => $requirement,
                        'todos' => $todos,
                        'todo' => $todo,
                    ])
                    {{ $todos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')

