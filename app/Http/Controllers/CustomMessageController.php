<?php

namespace App\Http\Controllers;

use App\Models\CustomMessage;
use App\Services\ActivityLogService;
use App\Services\CustomMessageService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomMessageController extends Controller
{
    /**
     * Display the custom messages index page
     */
    public function index(): View
    {
        $messages = CustomMessage::orderBy('message_type')
            ->orderBy('priority', 'desc')
            ->orderBy('name')
            ->paginate(15);

        $messageTypes = [
            'welcome' => 'Welcome Messages',
            'fallback' => 'Fallback Messages',
            'error' => 'Error Messages',
            'help' => 'Help Messages',
        ];

        return view('custom-messages.index', compact('messages', 'messageTypes'));
    }

    /**
     * Show the form for creating a new message
     */
    public function create(): View
    {
        $messageTypes = [
            'welcome' => 'Welcome Messages',
            'fallback' => 'Fallback Messages',
            'error' => 'Error Messages',
            'help' => 'Help Messages',
        ];

        $conditions = [
            'time_of_day' => [
                'morning' => 'Morning (6 AM - 12 PM)',
                'afternoon' => 'Afternoon (12 PM - 6 PM)',
                'evening' => 'Evening (6 PM - 6 AM)',
            ],
            'user_type' => [
                'new' => 'New Users',
                'returning' => 'Returning Users',
            ],
        ];

        return view('custom-messages.create', compact('messageTypes', 'conditions'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message_type' => 'required|in:welcome,fallback,error,help',
            'content' => 'required|string|max:1000',
            'priority' => 'integer|min:0|max:100',
            'is_active' => 'boolean',
            'language' => 'string|max:10',
            'conditions' => 'array',
        ]);

        $message = CustomMessage::create($request->all());

        // Log the creation
        ActivityLogService::logCreate($message, "Created custom message: '{$message->name}'");

        // Clear cache
        CustomMessageService::clearCache();

        return redirect()
            ->route('custom-messages.index')
            ->with('success', 'Custom message created successfully.');
    }

    /**
     * Show the form for editing a message
     */
    public function edit(CustomMessage $customMessage): View
    {
        $messageTypes = [
            'welcome' => 'Welcome Messages',
            'fallback' => 'Fallback Messages',
            'error' => 'Error Messages',
            'help' => 'Help Messages',
        ];

        $conditions = [
            'time_of_day' => [
                'morning' => 'Morning (6 AM - 12 PM)',
                'afternoon' => 'Afternoon (12 PM - 6 PM)',
                'evening' => 'Evening (6 PM - 6 AM)',
            ],
            'user_type' => [
                'new' => 'New Users',
                'returning' => 'Returning Users',
            ],
        ];

        return view('custom-messages.edit', compact('customMessage', 'messageTypes', 'conditions'));
    }

    /**
     * Update the specified message
     */
    public function update(Request $request, CustomMessage $customMessage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message_type' => 'required|in:welcome,fallback,error,help',
            'content' => 'required|string|max:1000',
            'priority' => 'integer|min:0|max:100',
            'is_active' => 'boolean',
            'language' => 'string|max:10',
            'conditions' => 'array',
        ]);

        $oldValues = $customMessage->toArray();
        $customMessage->update($request->all());

        // Log the update
        ActivityLogService::logUpdate(
            $customMessage,
            $oldValues,
            $customMessage->toArray(),
            "Updated custom message: '{$customMessage->name}'"
        );

        // Clear cache
        CustomMessageService::clearCache();

        return redirect()
            ->route('custom-messages.index')
            ->with('success', 'Custom message updated successfully.');
    }

    /**
     * Remove the specified message
     */
    public function destroy(CustomMessage $customMessage)
    {
        $name = $customMessage->name;
        $customMessage->delete();

        // Log the deletion
        ActivityLogService::logDelete($customMessage, "Deleted custom message: '{$name}'");

        // Clear cache
        CustomMessageService::clearCache();

        return redirect()
            ->route('custom-messages.index')
            ->with('success', 'Custom message deleted successfully.');
    }

    /**
     * Toggle message active status
     */
    public function toggleStatus(CustomMessage $customMessage)
    {
        $customMessage->update(['is_active' => !$customMessage->is_active]);

        // Log the status change
        $status = $customMessage->is_active ? 'activated' : 'deactivated';
        ActivityLogService::log(
            'update',
            "{$status} custom message: '{$customMessage->name}'",
            $customMessage
        );

        // Clear cache
        CustomMessageService::clearCache();

        return redirect()
            ->route('custom-messages.index')
            ->with('success', "Custom message {$status} successfully.");
    }

    /**
     * Preview message with context
     */
    public function preview(Request $request, CustomMessage $customMessage)
    {
        $context = [
            'time_of_day' => $request->input('time_of_day', 'morning'),
            'current_page' => $request->input('current_page', '/'),
            'is_returning' => $request->boolean('is_returning', false),
            'company_name' => config('app.name', 'Our Company'),
            'available_topics' => 'our services, pricing, contact information',
        ];

        $processedContent = $customMessage->processContent($context);

        return response()->json([
            'original' => $customMessage->content,
            'processed' => $processedContent,
            'context' => $context,
        ]);
    }

    /**
     * Test message selection
     */
    public function test(Request $request)
    {
        $context = [
            'time_of_day' => $request->input('time_of_day', 'morning'),
            'current_page' => $request->input('current_page', '/'),
            'is_returning' => $request->boolean('is_returning', false),
        ];

        $messageType = $request->input('message_type', 'welcome');

        $result = match ($messageType) {
            'welcome' => CustomMessageService::getWelcomeMessage($context),
            'fallback' => CustomMessageService::getFallbackMessage($context),
            'error' => CustomMessageService::getErrorMessage($context),
            'help' => CustomMessageService::getHelpMessage($context),
            default => 'Invalid message type',
        };

        return response()->json([
            'message' => $result,
            'context' => $context,
            'type' => $messageType,
        ]);
    }
}
