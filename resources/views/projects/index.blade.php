@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Projects</div>

                <div class="card-body">
                    @forelse ($projects as $project)
                    <ul>
                        <li><a href="{{ route('projects.show', ['project' => $project->id]) }}">{{ $project->name }}</a></li>
                    </ul>
                    @empty
                    <p>No projects</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')

