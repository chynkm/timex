@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ isset($timeEntry->id) ? __('form.edit_time_entry') : __('form.create_time_entry') }}</div>

                @if (isset($timeEntry->id))
                <form action="{{ route('timeEntries.update', ['timeEntry' => $timeEntry->id]) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                @else
                <form action="{{ route('timeEntries.store') }}" method="POST">
                @endif

                <div class="card-body">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('form.project')</label>
                                <select name="project_id" class="form-control" id="project_id">
                                    <option value="">@lang('form.please_select')</option>
                                    @foreach ($projects as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('form.requirement')</label>
                                <select name="requirement_id" class="form-control" id="requirement_id">
                                    <option value="">@lang('form.please_select_a_project')</option>
                                </select>
                                @error('requirement_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">@lang('form.description')</label>
                        <textarea id="description"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            placeholder="@lang('form.enter_time_entry')"
                            rows="4">{{ $timeEntry->description }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <button type="submit" class="btn btn-primary">@lang('form.submit')</button>
                    <a role="button" href="{{ route('timeEntries.index') }}" class="btn btn-danger">@lang('form.cancel')</a>
                </div>

                </form>
            </div>

            @include('timeEntries.commonTimeEntry', ['timeEntries' => $timeEntries])
        </div>
    </div>
</div>
@endsection('content')

@section('js')
<script type="text/javascript">
var projectRequirementRoute = "{{ route('requirements.projectRequirement') }}";
$(function(){

$('#project_id').change(function() {
    $.getJSON(projectRequirementRoute+'/'+$(this).val())
        .done(function(data) {
            if (data.status) {
                $('#requirement_id').html(data.html);
            }
        });
});

});
</script>
@endsection

