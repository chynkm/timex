{{ Form::open(['route' => ['todos.store', $requirement->id]]) }}
@include('todos.todo', compact('todo'))
</form>

<div class="mb-1">&nbsp;</div>

@foreach ($todos as $todo)
    {{ Form::open(['route' => ['todos.update', $todo->id], 'method' => 'patch']) }}
    <div class="form-check mt-1">
        {{ Form::checkbox('completed', null, $todo->completed, [
            'class' => 'form-check-input',
            'onchange' => 'this.form.submit()']) }}
        <label class="form-check-label todo_label">
        <p class="todo_font"><strong>{{ $todo->task }} {{ $todo->completed ? '('.$todo->completed.')': null }}</strong></p>
        </label>
        <input width="100%" class="form-control todo_input d-none" type="text" name="task" placeholder="@lang('form.add_a_task')" value="{{ $todo->task }}">
    </div>
    </form>
@endforeach

@section('js')
<script type="text/javascript">
var APP = APP || {};
$(function() {
    APP.commontodo.init();
});

APP.commontodo = {
    init: function() {
        this.editTodo();
    },

    editTodo: function() {
        $('.todo_label').click(function() {
            $(this).addClass('d-none');
            $(this).next('.todo_input').removeClass('d-none');
        });
    },
};
</script>
@endsection
