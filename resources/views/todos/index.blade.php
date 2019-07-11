@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    @lang('form.todo') ({{ $requirement->project->name.' - '.$requirement->name }})
                </div>

                <div class="card-body">
                    {{ Form::open(['route' => ['todos.store', $requirement->id]]) }}
                    @include('todos.todo', compact('todo'))
                    </form>

                    @foreach ($todos as $todo)
                        {{ Form::open(['route' => ['todos.update', $todo->id], 'method' => 'patch']) }}
                        <div class="form-check mt-2">
                            {{ Form::checkbox('completed', null, $todo->completed, [
                                'class' => 'form-check-input',
                                'id' => 'todo_check_'.$todo->id,
                                'onchange' => 'this.form.submit()']) }}
                            <label class="form-check-label" for="todo_check_{{ $todo->id }}">
                            {{ $todo->task }}&nbsp;{{ $todo->completed ? '('.$todo->completed.')': null }}
                            </label>
                        </div>
                        </form>
                    @endforeach
                    {{ $todos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection('content')

