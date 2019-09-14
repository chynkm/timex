<div class="row">
    <div class="col-md-10 col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="completed" onchange="this.form.submit()">
            <input class="form-control" type="text" name="task" placeholder="@lang('form.add_a_task')" autocomplete="off" autofocus>
        </div>
    </div>
    <div class="col-md-1 col-6">
        {{ Form::select('impact', config('env.impacts'), 'high', ['class' => 'form-control']) }}
    </div>
    <div class="col-md-1 col-6">
        {{ Form::select('complexity', config('env.complexities'), 'easy', ['class' => 'form-control']) }}
    </div>
</div>
