@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Q&A List</h2>
    <a href="{{ route('qna.create') }}" class="btn btn-primary mb-2">Add New Q&A</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Actions</th>
        </tr>
        @foreach($qnas as $qna)
        <tr>
            <td>{{ $qna->id }}</td>
            <td>{{ $qna->question }}</td>
            <td>{{ $qna->answer }}</td>
            <td>
                <a href="{{ route('qna.edit', $qna->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('qna.destroy', $qna->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this Q&A?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    {{ $qnas->links() }}
</div>
@endsection
