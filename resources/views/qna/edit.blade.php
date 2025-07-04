@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Q&A</h2>
    <form action="{{ route('qna.update', $qna->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Question</label>
            <input type="text" name="question" value="{{ $qna->question }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Answer</label>
            <textarea name="answer" class="form-control" rows="4" required>{{ $qna->answer }}</textarea>
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
