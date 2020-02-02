@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
        @forelse ($projects as $project)
            @foreach ($project->requirements as $requirement)
            <div class="card mb-2">
                <div class="card-header">
                    {{ $project->name.' - '.$requirement->name }}
                    <a href="{{ route('todos.index', ['requirement' => $requirement->id]) }}"
                        role="button"
                        class="btn btn-primary btn-sm pull-right">
                            <i class="fa fa-check-square"></i>
                    </a>
                </div>

                <div class="card-body">
                @include('todos.commonTodo', [
                    'requirement' => $requirement,
                    'todos' => $requirement->todos,
                    'todo' => $todo,
                ])
                </div>
            </div>
            @endforeach
        @empty
        <h4>@lang('form.no_todos_yet')</h4>
        @endforelse
        </div>
    </div>
</div>
@endsection('content')

