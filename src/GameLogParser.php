<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\VO\AmberObtained;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDiscarded;
use AdnanMula\KeyforgeGameLogParser\VO\CardsDrawn;
use AdnanMula\KeyforgeGameLogParser\VO\CardsPlayed;
use AdnanMula\KeyforgeGameLogParser\VO\HouseChosen;
use AdnanMula\KeyforgeGameLogParser\VO\KeyForged;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Source;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\TurnMoment;
use Symfony\Component\DomCrawler\Crawler;

final class GameLogParser
{
    public function execute(string|array $log, ParseType $parseType = ParseType::PLAIN): Game
    {
        $messages = $this->messages($log, $parseType);
        [$player1, $player2] = $this->extractPlayerInfo(...$messages);
        $game = new Game($player1, $player2, 1, $messages);

        foreach ($messages as $message) {
            $this->checkLength($game, $message);
            $this->checkFirstTurn($game, $message);
            $this->checkCardsDrawn($game, $message);
            $this->checkCardsDiscarded($game, $message);
            $this->checkCardsPlayed($game, $message);
            $this->checkKeysForged($game, $message);
            $this->checkAmber($game, $message);
            $this->checkWinner($game, $message);
            $this->checkConcede($game, $message);
            $this->checkHouses($game, $message);
        }

        return $game;
    }

    private function messages(string|array $log, ParseType $parseType): array
    {
        if (ParseType::PLAIN === $parseType) {
            if (false === is_string($log)) {
                throw new \InvalidArgumentException('Log must be an string when using plain or html type');
            }

            $messages = explode(\PHP_EOL, $log);
        } elseif (ParseType::ARRAY === $parseType) {
            if (false === is_array($log)) {
                throw new \InvalidArgumentException('Log must be an array when using array type');
            }

            $messages = $log;
        } else {
            $crawler = new Crawler($log);
            $htmlMessages = $crawler->filter('div.message:not(.chat-bubble)');
            $messages = [];
            foreach ($htmlMessages as $htmlMessage) {
                $messages[] = $htmlMessage->textContent;
            }
        }

        $filteredMessages = [];

        foreach ($messages as $message) {
            if (preg_match("/has connected to the game server\s*$/", $message)) {
                continue;
            }

            if (preg_match("/is shuffling their deck\s*$/", $message)) {
                continue;
            }

            if (preg_match("/manual mode/", $message)) {
                continue;
            }

            if (preg_match("/manually/", $message)) {
                continue;
            }

            if (preg_match("/(Key|Draw|Ready|Main|House)\s*phase -/", $message)) {
                continue;
            }

            if (preg_match("/^End of turn/", $message)) {
                continue;
            }

            if (preg_match("/readies their\s*cards/", $message)) {
                continue;
            }

            if (preg_match("/mutes spectators/", $message)) {
                continue;
            }

            if (preg_match("/declares check/i", $message)) {
                continue;
            }

            $filteredMessages[] = $message;
        }

        return $filteredMessages;
    }

    /** @return array<Player> */
    private function extractPlayerInfo(string ...$messages): array
    {
        $playerName1 = null;
        $playerName2 = null;
        $deck1 = null;
        $deck2 = null;

        $pattern = '/^(\w+)\s+brings\s+(.*?)\s+to The Crucible\s*$/u';
        $matches = [];

        if (preg_match($pattern, $messages[0], $matches)) {
            $playerName1 = $matches[1];
            $deck1 = $matches[2];
        }

        if (preg_match($pattern, $messages[1], $matches)) {
            $playerName2 = $matches[1];
            $deck2 = $matches[2];
        }

        if (null === $playerName1 || null === $playerName2 || null === $deck1 || null === $deck2) {
            throw new \Exception('Malformed or incomplete log');
        }

        return [
            new Player(name: $playerName1, deck: $deck1),
            new Player(name: $playerName2, deck: $deck2),
        ];
    }

    private function checkLength(Game $game, string $message): void
    {
        $matches = [];

        if (preg_match('/Turn (\d+) -/i', $message, $matches)) {
            $game->updateLength(max($game->length, (int) $matches[1]));
        }
    }

    private function checkFirstTurn(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2) won the flip and is first player/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsFirst(true);
        }
    }

    private function checkCardsDrawn(Game $game, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+draws\s+(\d+)\s+card/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->cardsDrawn->add(
                new CardsDrawn($matches[1], new Turn($game->length, TurnMoment::BETWEEN), (int) $matches[2]),
            );
        }
    }

    private function checkCardsDiscarded(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $player = null;
        $discardCount = 0;
        $source = Source::PLAYER;
        $matches = [];

        $pattern1 = "/($player1|$player2) uses .*? to discard the top (\d+) cards/i";
        $pattern2 = "/($player1|$player2) uses .*? to discard (.+?)(?:$| and| to| at| from)/i";
        $pattern3 = "/($player1|$player2) discards (.+?)(?:$| and| to| due| at)/i";
        $pattern4 = "/($player1|$player2) uses .*? to discard a card.*?from ($player1|$player2)'s hand/i";

        if (preg_match($pattern1, $message, $matches)) {
            $player = $matches[1];
            $discardCount = (int) $matches[2];
        } elseif (preg_match($pattern2, $message, $matches)) {
            $player = $matches[1];
            /** @var array $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern3, $message, $matches)) {
            $player = $matches[1];
            /** @var array $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern4, $message, $matches)) {
            $player = $matches[2];
            $discardCount = 1;
            $source = Source::OPPONENT;
        }

        if ($player !== null && $discardCount > 0) {
            $game->player($player)?->cardsDiscarded->add(
                new CardsDiscarded($player, new Turn($game->length, TurnMoment::BETWEEN), $source, $discardCount),
            );
        }
    }

    private function checkCardsPlayed(Game $game, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+plays\s+(.+)$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = $matches[2];

            $game->player($player)?->cardsPlayed->add(
                new CardsPlayed($player, new Turn($game->length, TurnMoment::BETWEEN), [$card]),
            );
        }
    }

    private function checkKeysForged(Game $game, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+forges the\s+([^\s]+)\s+key\s*,\s*paying\s+(\d+)\s+Æmber/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->keysForged->add(
                new KeyForged($matches[1], new Turn($game->length, TurnMoment::BETWEEN), $matches[2], (int) $matches[3], 0),
            );
        }
    }

    private function checkAmber(Game $game, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)\s+($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)/";

        if (preg_match($pattern, $message, $matches)) {
            $player1Last = $game->player($matches[1])?->amberObtained->last();
            $turnMoment1 = $player1Last?->turn()->value() !== $game->length ? TurnMoment::START : TurnMoment::END;

            $game->player($matches[1])?->amberObtained->add(
                new AmberObtained($matches[1], new Turn($game->length, $turnMoment1), (int) $matches[3], (int) $matches[2]),
            );

            $player2Last = $game->player($matches[4])?->amberObtained->last();
            $turnMoment2 = $player2Last?->turn()->value() !== $game->length ? TurnMoment::START : TurnMoment::END;

            $game->player($matches[4])?->amberObtained->add(
                new AmberObtained($matches[4], new Turn($game->length, $turnMoment2), (int) $matches[6], (int) $matches[5]),
            );
        }
    }

    private function checkWinner(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) has won the game\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateIsWinner(true);
        }
    }

    private function checkConcede(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2) concedes\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->updateHasConceded(true);
        }
    }

    private function checkHouses(Game $game, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+chooses\s+(\w*)\s+as their active house this turn\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->housesPlayed->add(
                new HouseChosen($matches[1], new Turn($game->length, TurnMoment::START), $matches[2]),
            );
        }
    }
}
