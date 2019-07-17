{{ Form::open(['route' => ['todos.store', $requirement->id]]) }}
@include('todos.todo', compact('todo'))
</form>

<div class="mb-1">&nbsp;</div>

@foreach ($todos as $todo)
    {{ Form::open(['route' => ['todos.update', $todo->id], 'method' => 'patch']) }}
    <i class="fa fa-pencil todo_edit"></i>
    <div class="form-check mt-1 ml-1 todo_checkbox">
        {{ Form::checkbox('completed', null, $todo->completed, [
            'class' => 'form-check-input',
            'id' => 'todo_check_'.$todo->id,
            'onchange' => 'this.form.submit()']) }}
        <label class="form-check-label todo_label" for="todo_check_{{ $todo->id }}">
        <p class="todo_font"><strong>{{ $todo->task }} {{ $todo->completed ? '('.$todo->completed.')': null }}</strong></p>
        </label>
        <input class="form-control todo_input d-none" type="text" name="task" placeholder="@lang('form.add_a_task')" value="{{ $todo->task }}">
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
        $('.todo_edit').click(function() {
            $(this).next('.todo_checkbox').find('.todo_label').addClass('d-none');
            $(this).next('.todo_checkbox').find('.todo_input').removeClass('d-none');
        });
    },
};
</script>
@endsection
