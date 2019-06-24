@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('form.view_project')</div>

                <div class="card-body">
                    <h1>{{ $project->name }}</h1>
                </div>

                @forelse ($project->requirements as $requirement)
                    <li>{{ $requirement->name }}</li>
                @empty
                    <li>@lang('form.no_requirements')</li>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection('content')
