@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ (isset($project) ? __('form.edit_project') : __('form.create_project')) }}</div>

                @if (isset($project))
                <form action="{{ route('projects.update', ['project' => $project->id]) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                @else
                <form action="{{ route('projects.store') }}" method="POST">
                @endif

                <div class="card-body">
                    @csrf
                    <div class="form-group">
                        <label for="name">@lang('form.name')</label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            placeholder="@lang('form.enter_project_name')"
                            value="{{ old('name', isset($project) ? $project->name : null) }}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <button type="submit" class="btn btn-primary">@lang('form.submit')</button>
                    <a role="button" href="{{ route('projects.index') }}" class="btn btn-danger">@lang('form.cancel')</a>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection('content')

