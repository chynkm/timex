@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ (isset($requirement) ? __('form.edit_requirement') : __('form.create_requirement')) }}</div>

                @if (isset($requirement))
                <form action="{{ route('requirements.update', ['requirement' => $requirement->id]) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                @else
                <form action="{{ route('requirements.store', ['project' => $project->id]) }}" method="POST">
                @endif

                <div class="card-body">
                    @csrf
                    <div class="form-group">
                        <label for="name">@lang('form.name')</label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            placeholder="@lang('form.enter_requirement_name')"
                            value="{{ isset($requirement) ? $requirement->name : null }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <button type="submit" class="btn btn-primary">@lang('form.submit')</button>
                    <a role="button"
                        href="{{ route('projects.show', ['project' => isset($project) ? $project->id : $requirement->project->id]) }}"
                        class="btn btn-danger">@lang('form.cancel')</a>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection('content')

