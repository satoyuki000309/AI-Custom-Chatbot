<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AISettingsController extends Controller
{
    public function index()
    {
        // Get current settings from session or config
        $currentModel = session('ai_model', 'deepseek');
        $uploadedFiles = session('uploaded_files', []);

        return view('ai-settings.index', compact('currentModel', 'uploadedFiles'));
    }

    public function updateModel(Request $request)
    {
        $request->validate([
            'ai_model' => 'required|in:deepseek,mistral,custom'
        ]);

        session(['ai_model' => $request->ai_model]);

        return redirect()
            ->route('ai-settings.index')
            ->with('success', 'AI Model updated successfully!');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'training_file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240'  // 10MB max
        ]);

        $file = $request->file('training_file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Store file in storage/app/training-files
        $path = $file->storeAs('training-files', $fileName, 'local');

        // Get existing uploaded files
        $uploadedFiles = session('uploaded_files', []);

        // Add new file to the list
        $uploadedFiles[] = [
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'uploaded_at' => now()
        ];

        session(['uploaded_files' => $uploadedFiles]);

        return redirect()
            ->route('ai-settings.index')
            ->with('success', 'File uploaded successfully!');
    }

    public function deleteFile(Request $request)
    {
        $request->validate([
            'file_index' => 'required|integer'
        ]);

        $uploadedFiles = session('uploaded_files', []);
        $fileIndex = $request->file_index;

        if (isset($uploadedFiles[$fileIndex])) {
            $file = $uploadedFiles[$fileIndex];

            // Delete file from storage
            if (Storage::disk('local')->exists($file['path'])) {
                Storage::disk('local')->delete($file['path']);
            }

            // Remove from session
            unset($uploadedFiles[$fileIndex]);
            $uploadedFiles = array_values($uploadedFiles);  // Re-index array
            session(['uploaded_files' => $uploadedFiles]);

            return redirect()
                ->route('ai-settings.index')
                ->with('success', 'File deleted successfully!');
        }

        return redirect()
            ->route('ai-settings.index')
            ->with('error', 'File not found!');
    }

    public function clearAllFiles()
    {
        $uploadedFiles = session('uploaded_files', []);

        // Delete all files from storage
        foreach ($uploadedFiles as $file) {
            if (Storage::disk('local')->exists($file['path'])) {
                Storage::disk('local')->delete($file['path']);
            }
        }

        // Clear session
        session()->forget('uploaded_files');

        return redirect()
            ->route('ai-settings.index')
            ->with('success', 'All files cleared successfully!');
    }
}
