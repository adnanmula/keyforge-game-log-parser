<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\Parser\Processor;

use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;
use Symfony\Component\DomCrawler\Crawler;

final class LogPreprocessor
{
    /** @return array<string> */
    public function execute(string|array $log, ParseType $parseType): array
    {
        if (ParseType::PLAIN === $parseType) {
            if (false === is_string($log)) {
                throw new \InvalidArgumentException('Log must be a string when using plain type');
            }

            $messages = explode(\PHP_EOL, $log);
        } elseif (ParseType::ARRAY === $parseType) {
            if (false === is_array($log)) {
                throw new \InvalidArgumentException('Log must be an array when using array type');
            }

            $messages = $log;
        } else {
            if (false === is_string($log)) {
                throw new \InvalidArgumentException('Log must be a string when using html type');
            }

            $crawler = new Crawler($log);
            $htmlMessages = $crawler->filter('div.message:not(.chat-bubble)');
            $messages = [];
            foreach ($htmlMessages as $htmlMessage) {
                $messages[] = $htmlMessage->textContent;
            }
        }

        $filteredMessages = [];

        foreach ($messages as $message) {
            $message = trim($message);
            $message = preg_replace('/\s+/', ' ', $message) ?? $message;

            if ('' === $message) {
                continue;
            }

            if (preg_match("/is attempting to switch manual mode on/", $message)) {
                continue;
            }

            if (preg_match("/allows enabling manual mode/", $message)) {
                continue;
            }

            if (preg_match("/switches manual mode (on|off)/", $message)) {
                continue;
            }

            if (preg_match("/ manually /", $message)) {
                continue;
            }

            if (preg_match("/mutes spectators/", $message)) {
                continue;
            }

            $filteredMessages[] = $message;
        }

        return $filteredMessages;
    }
}
