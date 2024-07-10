@extends('layouts.master')

@section('content')
<div class="row">
    <h1>{{ $proposal->title }}</h1>
    <p>{{ $proposal->description }}</p>

    <div>
        <h2>Made by</h2>
        <p>{{ $proposal->applicant->name }}</p>
    </div>

    <div>
        <h2>Created at</h2>
        <p>{{ $proposal->created_at->format('F j, Y') }}</p>
    </div>
</div>
@endsection