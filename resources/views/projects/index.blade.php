@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @lang('form.projects')
                    <a href="{{ route('projects.create') }}"
                        role="button"
                        class="btn btn-primary btn-sm pull-right"
                        title="@lang('form.create_project')">
                            <i class="fa fa-plus"></i>
                    </a>
                </div>

                <div class="card-body">
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
                                @forelse ($projects as $project)
                                <tr>
                                    <td>{{ $projects->perPage() * ($projects->currentPage()-1) + $loop->iteration }}</td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ $project->created_at }}</td>
                                    <td>
                                        <a href="{{ route('projects.edit', ['project' => $project->id]) }}"
                                            role="button"
                                            class="btn btn-primary btn-sm"
                                            title="@lang('form.edit_project')">
                                                <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="{{ route('projects.show', ['project' => $project->id]) }}"
                                            role="button"
                                            class="btn btn-primary btn-sm"
                                            title="@lang('form.view_project')">
                                                <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">@lang('form.no_projects')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')

