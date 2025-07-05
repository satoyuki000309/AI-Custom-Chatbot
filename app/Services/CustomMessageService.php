<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\CustomMessage;
use Illuminate\Support\Facades\Cache;

class CustomMessageService
{
    /**
     * Get welcome message based on context
     */
    public static function getWelcomeMessage(array $context = []): string
    {
        $cacheKey = 'welcome_message_' . md5(json_encode($context));

        return Cache::remember($cacheKey, 300, function () use ($context) {
            $messages = CustomMessage::byType('welcome')
                ->active()
                ->byLanguage(app()->getLocale())
                ->byPriority()
                ->get();

            foreach ($messages as $message) {
                if ($message->matchesConditions($context)) {
                    return $message->processContent($context);
                }
            }

            // Default welcome message if none found
            return self::getDefaultWelcomeMessage($context);
        });
    }

    /**
     * Get fallback message based on context
     */
    public static function getFallbackMessage(array $context = []): string
    {
        $cacheKey = 'fallback_message_' . md5(json_encode($context));

        return Cache::remember($cacheKey, 300, function () use ($context) {
            $messages = CustomMessage::byType('fallback')
                ->active()
                ->byLanguage(app()->getLocale())
                ->byPriority()
                ->get();

            foreach ($messages as $message) {
                if ($message->matchesConditions($context)) {
                    return $message->processContent($context);
                }
            }

            // Default fallback message if none found
            return self::getDefaultFallbackMessage($context);
        });
    }

    /**
     * Get error message
     */
    public static function getErrorMessage(array $context = []): string
    {
        $messages = CustomMessage::byType('error')
            ->active()
            ->byLanguage(app()->getLocale())
            ->byPriority()
            ->get();

        foreach ($messages as $message) {
            if ($message->matchesConditions($context)) {
                return $message->processContent($context);
            }
        }

        return "I'm sorry, but I encountered an error. Please try again later.";
    }

    /**
     * Get help message
     */
    public static function getHelpMessage(array $context = []): string
    {
        $messages = CustomMessage::byType('help')
            ->active()
            ->byLanguage(app()->getLocale())
            ->byPriority()
            ->get();

        foreach ($messages as $message) {
            if ($message->matchesConditions($context)) {
                return $message->processContent($context);
            }
        }

        return "I'm here to help! You can ask me questions about our services, pricing, or contact information.";
    }

    /**
     * Get context for message selection
     */
    public static function getContext(string $visitorId = null): array
    {
        $context = [
            'time_of_day' => self::getTimeOfDay(),
            'current_page' => request()->path(),
            'is_returning' => false,
            'company_name' => config('app.name', 'Our Company'),
            'available_topics' => self::getAvailableTopics(),
        ];

        // Check if returning user
        if ($visitorId) {
            $context['is_returning'] = Chat::where('visitor_id', $visitorId)->exists();
        }

        return $context;
    }

    /**
     * Get time of day
     */
    private static function getTimeOfDay(): string
    {
        $hour = now()->hour;

        if ($hour >= 6 && $hour < 12)
            return 'morning';
        if ($hour >= 12 && $hour < 18)
            return 'afternoon';
        return 'evening';
    }

    /**
     * Get available topics for suggestions
     */
    private static function getAvailableTopics(): string
    {
        $topics = [
            'our services',
            'pricing information',
            'contact details',
            'business hours',
            'technical support'
        ];

        return implode(', ', $topics);
    }

    /**
     * Get default welcome message
     */
    private static function getDefaultWelcomeMessage(array $context = []): string
    {
        $timeGreeting = match ($context['time_of_day'] ?? 'general') {
            'morning' => 'Good morning',
            'afternoon' => 'Good afternoon',
            'evening' => 'Good evening',
            default => 'Hello'
        };

        $returningGreeting = $context['is_returning'] ?? false
            ? 'Welcome back! '
            : 'Welcome! ';

        return $timeGreeting . '! ' . $returningGreeting
            . "I'm your AI assistant. How can I help you today?";
    }

    /**
     * Get default fallback message
     */
    private static function getDefaultFallbackMessage(array $context = []): string
    {
        $topics = $context['available_topics'] ?? 'our services, pricing, or contact information';

        return "I'm not sure I understood that. Could you try rephrasing your question? "
            . 'I can help you with ' . $topics . '.';
    }

    /**
     * Clear message cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}
