@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('form.add_time_entry')</div>

                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">@lang('form.name')</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="@lang('form.enter_project_name')">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('form.submit')</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">@lang('form.todays_time_entries')</div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">@lang('form.project')</th>
                                <th scope="col">@lang('form.requirement')</th>
                                <th scope="col">@lang('form.description')</th>
                                <th scope="col">@lang('form.time')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($timeEntries as $timeEntry)
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                                <td>{{ $timeEntry->description }}</td>
                                <td>{{ $timeEntry->time }}</td>
                            </tr>
                            @empty
                            <tr><td>@lang('form.no_entries_for_today')</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')

