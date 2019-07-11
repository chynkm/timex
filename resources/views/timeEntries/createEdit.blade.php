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
                                {{ Form::select('project_id',
                                    $projects,
                                    isset($timeEntry->id) ? $timeEntry->requirement->project_id : null,
                                    [
                                        'class' => 'form-control',
                                        'id' => 'project_id',
                                        'placeholder' => __('form.please_select')
                                    ])
                                }}
                                @error('project_id')
                                <div class="error invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('form.requirement')</label>
                                {{ Form::select('requirement_id', [], null, ['class' => 'form-control', 'id' => 'requirement_id', 'placeholder' => __('form.please_select_a_project')]) }}
                                @error('requirement_id')
                                <div class="error invalid-feedback">{{ $message }}</div>
                                @enderror
                                {{ Form::hidden('requirement_hidden', isset($timeEntry->id) ? $timeEntry->requirement_id : null, ['id' => 'requirement_hidden']) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">@lang('form.description')</label>
                        {{ Form::textarea('description', isset($timeEntry->id) ? $timeEntry->description : null, ['class' => 'form-control', 'id' => 'description', 'placeholder' => __('form.enter_time_entry'), 'rows' => 4]) }}
                        @error('description')
                        <div class="error invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @if (isset($timeEntry->id))
                    <div class="form-group">
                        <label for="name">@lang('form.time')</label>
                        {{ Form::text('time', $timeEntry->time, ['class' => 'form-control', 'placeholder' => __('form.enter_time')]) }}
                        @error('time')
                        <div class="error invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>

                <div class="card-footer text-muted">
                    <button type="submit" class="btn btn-primary">@lang('form.submit')</button>
                    <a role="button" href="{{ route('timeEntries.index') }}" class="btn btn-danger">@lang('form.cancel')</a>
                </div>

                </form>
            </div>

            @unless (isset($timeEntry->id))
            @include('timeEntries.commonTimeEntry', ['timeEntries' => $timeEntries])
            @endunless
        </div>
    </div>
</div>
@endsection('content')

@section('js')
<script type="text/javascript">
var projectRequirementRoute = "{{ route('requirements.projectRequirement') }}";
var APP = APP || {};
$(function() {
    APP.timeEntry.init();
});

APP.timeEntry = {
    init: function() {
        this.projectChange();
        this.highlightError();
    },

    getRequirements: function() {
        $.getJSON(projectRequirementRoute+'/'+$('#project_id').val())
            .done(function(data) {
                if (data.status) {
                    $('#requirement_id').html(data.html);
                    if ($('#requirement_hidden').val().length &&
                        $('#requirement_id option[value='+$('#requirement_hidden').val()+']').length) {
                        $('#requirement_id').val($('#requirement_hidden').val());
                    }
                }
            });
    },

    projectChange: function() {
        var self = this;
        $('#project_id').change(function() {
            self.getRequirements();
        });

        if ($('#project_id').val().length) {
            self.getRequirements();
        }
    },

    highlightError: function() {
        // highlight error input fields, if any
        if ($('.error').length) {
            $('.error').closest('.form-group')
                .find('input, textarea, select')
                .addClass('is-invalid');
        }
    },
};
</script>
@endsection

