<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'message_type',
        'content',
        'conditions',
        'is_active',
        'priority',
        'language',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get messages by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Get active messages only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get messages by language
     */
    public function scopeByLanguage($query, $language = 'en')
    {
        return $query->where('language', $language);
    }

    /**
     * Get messages ordered by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get message type label
     */
    public function getMessageTypeLabelAttribute(): string
    {
        return match ($this->message_type) {
            'welcome' => 'Welcome Message',
            'fallback' => 'Fallback Message',
            'error' => 'Error Message',
            'help' => 'Help Message',
            default => ucfirst($this->message_type),
        };
    }

    /**
     * Check if message matches conditions
     */
    public function matchesConditions(array $context = []): bool
    {
        if (empty($this->conditions)) {
            return true;  // No conditions = always match
        }

        foreach ($this->conditions as $condition => $value) {
            switch ($condition) {
                case 'time_of_day':
                    $hour = now()->hour;
                    if ($value === 'morning' && ($hour < 6 || $hour >= 12))
                        return false;
                    if ($value === 'afternoon' && ($hour < 12 || $hour >= 18))
                        return false;
                    if ($value === 'evening' && ($hour < 18 || $hour >= 6))
                        return false;
                    break;

                case 'user_type':
                    if ($value === 'new' && isset($context['is_returning']) && $context['is_returning'])
                        return false;
                    if ($value === 'returning' && isset($context['is_returning']) && !$context['is_returning'])
                        return false;
                    break;

                case 'page_context':
                    if (isset($context['current_page']) && $context['current_page'] !== $value)
                        return false;
                    break;
            }
        }

        return true;
    }

    /**
     * Process message content with variables
     */
    public function processContent(array $variables = []): string
    {
        $content = $this->content;

        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }
}
