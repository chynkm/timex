@extends('layout')

@section('content')

@foreach ($projects as $project)
    <ul>
        <li>{{ $project->name }}</li>
    </ul>
@endforeach

@endsection('content')

