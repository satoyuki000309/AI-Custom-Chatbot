<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Response;

class ChatExportController extends Controller
{
    public function export()
    {
        $chats = Chat::all();

        $csv = "ID,Visitor ID,IP Address,Message,Response,Created At\n";

        foreach ($chats as $chat) {
            $row = [
                $chat->id,
                $chat->visitor_id,
                $chat->ip_address,
                str_replace('"', '""', (string) $chat->message),
                str_replace('"', '""', (string) $chat->response),
                $chat->created_at,
            ];
            // Enclose each field in double quotes and join with commas
            $csv .= '"' . implode('","', array_map('strval', $row)) . '"' . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=chat_logs.csv');
    }
}
