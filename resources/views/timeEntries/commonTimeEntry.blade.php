<div class="card mt-4">
    <div class="card-header">
        @if (request()->route()->getName() == 'timeEntries.index')
            @lang('form.time_entries')
            <a href="{{ route('timeEntries.create') }}"
                role="button"
                class="btn btn-primary btn-sm pull-right"
                title="@lang('form.create_time_entry')">
                    <i class="fa fa-plus"></i>
            </a>
        @else
            @lang('form.todays_time_entries')
        @endif
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">@lang('form.project')</th>
                        <th scope="col">@lang('form.requirement')</th>
                        <th scope="col">@lang('form.description')</th>
                        <th scope="col">@lang('form.time')</th>
                        <th scope="col">@lang('form.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($timeEntries as $key => $timeEntry)
                    <tr>
                        <th scope="row">{{ $key++ }}</th>
                        <td>{{ $timeEntry->requirement->project->name }}</td>
                        <td>{{ $timeEntry->requirement->name }}</td>
                        <td>{{ $timeEntry->description }}</td>
                        <td>{{ $timeEntry->time }}</td>
                        <td>
                            <a href="{{ route('timeEntries.edit', ['timeEntry' => $timeEntry->id]) }}"
                                role="button"
                                class="btn btn-default">
                                    <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            {{ request()->route()->getName() == 'timeEntries.index' ? __('form.no_time_entries') : __('form.no_entries_for_today') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
