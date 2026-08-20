<?php

namespace App\Enums;

enum BotName: string
{
    case GptBot = 'gptbot';
    case OaiSearchBot = 'oai_searchbot';
    case ClaudeBot = 'claudebot';
    case PerplexityBot = 'perplexitybot';
    case GoogleExtended = 'google_extended';
    case Googlebot = 'googlebot';
    case Bingbot = 'bingbot';
    case Other = 'other';

    /** Map a raw User-Agent substring to a BotName, for crawler-visit logging (Phase 6). */
    public static function fromUserAgent(string $userAgent): ?self
    {
        return match (true) {
            str_contains($userAgent, 'GPTBot') => self::GptBot,
            str_contains($userAgent, 'OAI-SearchBot') => self::OaiSearchBot,
            str_contains($userAgent, 'ClaudeBot') => self::ClaudeBot,
            str_contains($userAgent, 'PerplexityBot') => self::PerplexityBot,
            str_contains($userAgent, 'Google-Extended') => self::GoogleExtended,
            str_contains($userAgent, 'Googlebot') => self::Googlebot,
            str_contains($userAgent, 'bingbot') => self::Bingbot,
            default => null,
        };
    }
}
