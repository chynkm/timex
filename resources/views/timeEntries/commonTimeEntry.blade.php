<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">@lang('form.project')</th>
                <th scope="col">@lang('form.requirement')</th>
                <th scope="col">@lang('form.description')</th>
                <th scope="col">@lang('form.created_at')</th>
                <th scope="col">@lang('form.time')</th>
                <th scope="col">@lang('form.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($timeEntries as $timeEntry)
            <tr>
                <td>{{ $timeEntries->perPage() * ($timeEntries->currentPage()-1) + $loop->iteration }}</td>
                <td>{{ $timeEntry->requirement->project->name }}</td>
                <td>{{ $timeEntry->requirement->name }}</td>
                <td>{!! nl2br($timeEntry->description) !!}</td>
                <td>{{ $timeEntry->created_at->format('d-m-Y') }}</td>
                <td>{{ number_format($timeEntry->time, 2, '.', '') }}</td>
                <td>
                    <a href="{{ route('timeEntries.edit', ['timeEntry' => $timeEntry->id]) }}"
                        role="button"
                        class="btn btn-primary btn-sm">
                            <i class="fa fa-pencil"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    {{ request()->route()->getName() == 'timeEntries.index' ? __('form.no_time_entries') : __('form.no_entries_for_today') }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $timeEntries->links() }}
