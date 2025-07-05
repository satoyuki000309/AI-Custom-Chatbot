<?php

namespace Database\Seeders;

use App\Models\CustomMessage;
use Illuminate\Database\Seeder;

class CustomMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            // Welcome Messages
            [
                'name' => 'Morning Welcome',
                'message_type' => 'welcome',
                'content' => "Good morning! Welcome to {{company_name}}. I'm here to help you with {{available_topics}}. How can I assist you today?",
                'conditions' => ['time_of_day' => 'morning'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Afternoon Welcome',
                'message_type' => 'welcome',
                'content' => "Good afternoon! Welcome to {{company_name}}. I'm here to help you with {{available_topics}}. How can I assist you today?",
                'conditions' => ['time_of_day' => 'afternoon'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Evening Welcome',
                'message_type' => 'welcome',
                'content' => "Good evening! Welcome to {{company_name}}. I'm here to help you with {{available_topics}}. How can I assist you today?",
                'conditions' => ['time_of_day' => 'evening'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Returning User Welcome',
                'message_type' => 'welcome',
                'content' => "Welcome back! It's great to see you again. How can I help you today?",
                'conditions' => ['user_type' => 'returning'],
                'priority' => 15,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'New User Welcome',
                'message_type' => 'welcome',
                'content' => "Welcome to {{company_name}}! I'm your AI assistant. I can help you with {{available_topics}}. What would you like to know?",
                'conditions' => ['user_type' => 'new'],
                'priority' => 15,
                'is_active' => true,
                'language' => 'en',
            ],
            // Fallback Messages
            [
                'name' => 'General Fallback',
                'message_type' => 'fallback',
                'content' => "I'm not sure I understood that. Could you try rephrasing your question? I can help you with {{available_topics}}.",
                'conditions' => [],
                'priority' => 5,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Morning Fallback',
                'message_type' => 'fallback',
                'content' => "Good morning! I didn't quite catch that. Could you rephrase your question? I'm here to help with {{available_topics}}.",
                'conditions' => ['time_of_day' => 'morning'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Evening Fallback',
                'message_type' => 'fallback',
                'content' => "Good evening! I didn't quite understand that. Could you try asking in a different way? I can help with {{available_topics}}.",
                'conditions' => ['time_of_day' => 'evening'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            // Error Messages
            [
                'name' => 'General Error',
                'message_type' => 'error',
                'content' => "I'm sorry, but I encountered an error. Please try again later.",
                'conditions' => [],
                'priority' => 5,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Service Error',
                'message_type' => 'error',
                'content' => "I'm experiencing technical difficulties right now. Please try again in a few moments.",
                'conditions' => [],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
            // Help Messages
            [
                'name' => 'General Help',
                'message_type' => 'help',
                'content' => "I'm here to help! You can ask me questions about {{available_topics}}. Just type your question and I'll do my best to assist you.",
                'conditions' => [],
                'priority' => 5,
                'is_active' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Morning Help',
                'message_type' => 'help',
                'content' => "Good morning! I'm here to help you with {{available_topics}}. What would you like to know?",
                'conditions' => ['time_of_day' => 'morning'],
                'priority' => 10,
                'is_active' => true,
                'language' => 'en',
            ],
        ];

        foreach ($messages as $message) {
            CustomMessage::create($message);
        }
    }
}
