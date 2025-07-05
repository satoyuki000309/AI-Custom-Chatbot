<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\QnA;
use App\Services\CustomMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $ip = $request->ip();
        $visitorId = $request->session()->getId();

        // Get context for custom messages
        $context = CustomMessageService::getContext($visitorId);

        // Check if there's a matching QnA first
        $qna = QnA::where('question', 'LIKE', '%' . $message . '%')->first();
        if ($qna) {
            $reply = $qna->answer;
        } else {
            // Use DeepSeek API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('DEEPSEEK_API_KEY'),
            ])->post('https://api.deepseek.com/v1/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ],
                ],
            ]);

            $aiReply = $response->json('choices.0.message.content');

            if ($aiReply) {
                $reply = $aiReply;
            } else {
                // Use custom fallback message if AI fails
                $reply = CustomMessageService::getFallbackMessage($context);
            }
        }

        // Save chat to DB
        Chat::create([
            'visitor_id' => $visitorId,
            'ip_address' => $ip,
            'message' => $message,
            'response' => $reply,
        ]);

        return response()->json([
            'reply' => $reply
        ]);
    }

    /**
     * Get welcome message for chat widget
     */
    public function getWelcomeMessage(Request $request)
    {
        $visitorId = $request->session()->getId();
        $context = CustomMessageService::getContext($visitorId);

        return response()->json([
            'message' => CustomMessageService::getWelcomeMessage($context)
        ]);
    }
}
