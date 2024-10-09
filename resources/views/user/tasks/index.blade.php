@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        @foreach ($tasks as $task)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $task->name }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">User: {{ $task->user->name }}</h6>
                    <p class="card-text"><strong>Description:</strong> {{ $task->description }}</p>
                    <p class="card-text"><strong>Category:</strong> {{ $task->category->name }}</p>
                    <p class="card-text"><strong>Deadline:</strong> {{ $task->deadline }}</p>
                    <p class="card-text"><strong>Estimated Time:</strong> {{ $task->estimated_time }}</p>
                    <p class="card-text"><strong>Effective Time:</strong> {{ $task->effective_time }}</p>
                    <p class="card-text"><strong>Status:</strong> {{ $task->status->name }}</p>
                    <p class="card-text"><strong>Priority:</strong> {{ $task->priority->name }}</p>
                    <p class="card-text"><strong>Moments:</strong>
                        @foreach ($task->moments as $singleMoment)
                            <span class="badge bg-secondary">{{ $singleMoment->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
