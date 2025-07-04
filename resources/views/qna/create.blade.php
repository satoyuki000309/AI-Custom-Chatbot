@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Q&A</h2>
    <form action="{{ route('qna.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Question</label>
            <input type="text" name="question" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Answer</label>
            <textarea name="answer" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
