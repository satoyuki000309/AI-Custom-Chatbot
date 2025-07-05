<?php

namespace App\Http\Controllers;

use App\Models\QnA;
use App\Services\ActivityLogService;
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

        $qna = QnA::create($request->all());

        // Log the creation
        ActivityLogService::logCreate($qna, "Created new Q&A: '{$qna->question}'");

        return redirect()->route('dashboard')->with('success', 'Q&A created successfully.');
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

        $oldValues = $qna->toArray();
        $qna->update($request->all());

        // Log the update
        ActivityLogService::logUpdate(
            $qna,
            $oldValues,
            $qna->toArray(),
            "Updated Q&A: '{$qna->question}'"
        );

        return redirect()->route('dashboard')->with('success', 'Q&A updated successfully.');
    }

    public function destroy(QnA $qna)
    {
        $question = $qna->question;
        $qna->delete();

        // Log the deletion
        ActivityLogService::logDelete($qna, "Deleted Q&A: '{$question}'");

        return redirect()->route('dashboard')->with('success', 'Q&A deleted successfully.');
    }
}
