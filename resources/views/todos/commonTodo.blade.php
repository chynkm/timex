{{ Form::open(['route' => ['todos.store', $requirement->id]]) }}
@include('todos.todo', compact('todo'))
</form>

<div class="mb-1">&nbsp;</div>

@foreach ($todos as $todo)
    {{ Form::open(['route' => ['todos.update', $todo->id], 'method' => 'patch']) }}
    <div class="form-check mt-1">
        {{ Form::checkbox('completed', null, $todo->completed, [
            'class' => 'form-check-input',
            'id' => 'todo_check_'.$todo->id,
            'onchange' => 'this.form.submit()']) }}
        <label class="form-check-label" for="todo_check_{{ $todo->id }}">
        <p class="todo_font"><strong>{{ $todo->task }} {{ $todo->completed ? '('.$todo->completed.')': null }}</strong></p>
        </label>
    </div>
    </form>
@endforeach
