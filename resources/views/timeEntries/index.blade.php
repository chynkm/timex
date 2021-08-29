@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            @lang('form.time_entries')
            <a href="{{ route('timeEntries.create') }}"
                role="button"
                class="btn btn-primary btn-sm pull-right"
                title="@lang('form.create_time_entry')">
                    <i class="fa fa-plus"></i>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('timeEntries.index')}}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="project_id">@lang('form.project')</label>
                            {{ Form::select(
                                'project_id',
                                $projects,
                                request('project_id'),
                                [
                                    'class' => 'form-control',
                                    'id' => 'project_id',
                                    'placeholder' => __('form.please_select')
                                ])
                            }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="requirement_id">@lang('form.requirement')</label>
                            {{ Form::select(
                                'requirement_id',
                                [],
                                null,
                                [
                                    'class' => 'form-control',
                                    'id' => 'requirement_id',
                                    'placeholder' => __('form.please_select_a_project')
                                ])
                            }}
                        </div>
                    </div>
                    <div class="col-md-2 mt-4 pt-1">
                        <button type="submit" class="btn btn-block btn-primary">@lang('form.search')</button>
                    </div>
                    <div class="col-md-2 mt-4 pt-1">
                        <a role="button" href="{{ route('timeEntries.index') }}" class="btn btn-block btn-secondary">@lang('form.reset')</a>
                    </div>
                </div>
            </form>
            <!-- to fill the select option after the select box is filled using ajax -->
            {{ Form::hidden('requirement_hidden', request('requirement_id'), ['id' => 'requirement_hidden']) }}
            <hr>
            @include('timeEntries.commonTimeEntry', ['timeEntries' => $timeEntries])
        </div>
    </div>
</div>
@endsection('content')

@section('js')
<script type="text/javascript">
var projectRequirementRoute = "{{ route('requirements.projectRequirement') }}";
var APP = APP || {};
$(function() {
    APP.timeEntryIndex.init();
});

APP.timeEntryIndex = {
    init: function() {
        this.projectChange();
    },

    getRequirements: function() {
        $.getJSON(projectRequirementRoute+'/'+$('#project_id').val())
            .done(function(data) {
                if (data.status) {
                    $('#requirement_id').html(data.html);
                    if (
                        $('#requirement_hidden').val().length &&
                        $('#requirement_id option[value='+$('#requirement_hidden').val()+']').length
                    ) {
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
};
</script>
@endsection
