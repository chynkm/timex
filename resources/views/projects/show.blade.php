@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @lang('form.view_project')
                    <a href="{{ route('requirements.create', ['project' => $project->id]) }}"
                        role="button"
                        class="btn btn-primary btn-sm pull-right"
                        title="@lang('form.create_requirement')">
                            <i class="fa fa-plus"></i>
                    </a>
                </div>

                <div class="card-body">
                    <h1>{{ $project->name }}</h1>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('form.name')</th>
                                    <th scope="col">@lang('form.created_at')</th>
                                    <th scope="col">@lang('form.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($project->requirements as $key => $requirement)
                                <tr>
                                    <th scope="row">{{ ++$key }}</th>
                                    <td>{{ $requirement->name }}</td>
                                    <td>{{ $requirement->created_at }}</td>
                                    <td>
                                        <a href="{{ route('requirements.edit', ['requirement' => $requirement->id]) }}"
                                            role="button"
                                            class="btn btn-primary btn-sm">
                                                <i class="fa fa-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">@lang('form.no_requirements')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')
