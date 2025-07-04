<?php

namespace App\Http\Controllers;

use App\Models\QnA;
use Illuminate\Http\Request;

class QnAController extends Controller
{
    public function index()
    {
        $qnas = QnA::latest()->paginate(10);
        return view('qna.index', compact('qnas'));
    }

    public function create()
    {
        return view('qna.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        QnA::create($request->all());
        return redirect()->route('qna.index')->with('success', 'Q&A created successfully.');
    }

    public function edit(QnA $qna)
    {
        return view('qna.edit', compact('qna'));
    }

    public function update(Request $request, QnA $qna)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $qna->update($request->all());
        return redirect()->route('qna.index')->with('success', 'Q&A updated successfully.');
    }

    public function destroy(QnA $qna)
    {
        $qna->delete();
        return redirect()->route('qna.index')->with('success', 'Q&A deleted successfully.');
    }
}
