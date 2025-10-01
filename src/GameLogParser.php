<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser;

use AdnanMula\KeyforgeGameLogParser\Event\Event;
use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Event\Source;
use AdnanMula\KeyforgeGameLogParser\Event\Turn;
use AdnanMula\KeyforgeGameLogParser\Event\Moment;
use AdnanMula\KeyforgeGameLogParser\Game\Game;
use AdnanMula\KeyforgeGameLogParser\Game\Player;
use Symfony\Component\DomCrawler\Crawler;

final class GameLogParser
{
    public function execute(string|array $log, ParseType $parseType = ParseType::PLAIN): Game
    {
        $messages = $this->messages($log, $parseType);
        [$player1, $player2] = $this->extractPlayerInfo(...$messages);
        $game = new Game($player1, $player2, 0, $messages);

        foreach ($messages as $index => $message) {
            $this->checkFirstTurn($game, $message);
            $this->checkTurnOne($game, $message);
            $this->checkLength($game, $message);
            $this->checkAmber($game, $index, $message);
            $this->checkKeysForged($game, $index, $message);
            $this->checkHouses($game, $index, $message);
            $this->checkCardsDrawn($game, $index, $message);
            $this->checkCardsDiscarded($game, $index, $message);
            $this->checkCardsPlayed($game, $index, $message);
            $this->checkAmberStolen($game, $index, $message);
            $this->checkReap($game, $index, $message);
            $this->checkFight($game, $index, $message);
            $this->checkExtraTurn($game, $index, $message);
            $this->checkTokens($game, $index, $message);
            $this->checkProphecies($game, $index, $message);
            $this->checkWinner($game, $message);
            $this->checkConcede($game, $message);
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
            if (preg_match("/is shuffling their deck\s*$/", $message)) {
                continue;
            }

            if (preg_match("/manual mode/", $message)) {
                continue;
            }

            if (preg_match("/manually/", $message)) {
                continue;
            }

            if (preg_match("/(Draw|Ready|Main|House)\s*phase -/", $message)) {
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
            $deck1 = 'Unknown';
            $deck2 = 'Unknown';

            foreach ($messages as $message) {
                $matches = [];

                if (preg_match('/^\s*(\w+)\s+has connected to the game server\s*$/u', $message, $matches)) {
                    if (null === $playerName1) {
                        $playerName1 = trim($matches[1]);
                    } else {
                        $playerName2 = trim($matches[1]);
                    }
                }

                if (null !== $playerName1 && null !== $playerName2) {
                    break;
                }
            }

            if (null === $playerName1 || null === $playerName2) {
                throw new \Exception('Malformed or incomplete log');
            }
        }

        return [
            new Player(name: $playerName1, deck: $deck1),
            new Player(name: $playerName2, deck: $deck2),
        ];
    }

    private function checkTurnOne(Game $game, string $message): void
    {
        if ($game->length > 0 || null === $game->first()) {
            return;
        }

        $playerGoingFirst = $game->first()->escapedName();

        $pattern = "/^Key phase - $playerGoingFirst/";

        if (preg_match($pattern, $message)) {
            $game->updateLength(1);
        }
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

    private function checkKeysForged(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+forges the\s+([^\s]+)\s+key\s*,\s*paying\s+(\d+)\s+Æmber/";

        if (preg_match($pattern, $message, $matches)) {
            $currentAmber = $game->player($matches[1])?->timeline->filter(EventType::AMBER_OBTAINED)?->last()?->value() ?? 0;
            $cost = (int) $matches[3];
            $remaining = max(0, $currentAmber - $cost);

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::KEY_FORGED,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $matches[2],
                    [
                        'amberCost' => (int) $matches[3],
                        'amberRemaining' => $remaining,
                    ],
                ),
            );
        }
    }

    private function checkAmber(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)\s+($player1|$player2):\s+(\d+)\s+Æmber\s+\((\d+) keys?\)/";

        if (preg_match($pattern, $message, $matches)) {
            $currentPlayer1 = $game->player($matches[1]);
            $player1Last = $currentPlayer1?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment1 = $player1Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged1 = 0;

            if (null !== $currentPlayer1) {
                foreach ($currentPlayer1->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged1 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[1],
                    new Turn($game->length, $turnMoment1, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                    [
                        'keys' => (int) $matches[3],
                        'delta' => (int) $matches[2] - ($player1Last?->value() ?? 0) + $adjustKeyForged1,
                    ],
                ),
            );


            $currentPlayer2 = $game->player($matches[4]);
            $player2Last = $currentPlayer2?->timeline->filter(EventType::AMBER_OBTAINED)->last();
            $turnMoment2 = $player2Last?->turn()->value() !== $game->length ? Moment::START : Moment::END;
            $adjustKeyForged2 = 0;

            if (null !== $currentPlayer2) {
                foreach ($currentPlayer2->timeline->filter(EventType::KEY_FORGED)->items() as $keyForged) {
                    if ($keyForged->turn()->value() === $game->length) {
                        $adjustKeyForged2 += $keyForged->payload['amberCost'];
                    }
                }
            }

            $game->player($matches[4])?->timeline->add(
                new Event(
                    EventType::AMBER_OBTAINED,
                    $matches[4],
                    new Turn($game->length, $turnMoment2, $index),
                    Source::UNKNOWN,
                    (int) $matches[5],
                    [
                        'keys' => (int) $matches[6],
                        'delta' => (int) $matches[5] - ($player2Last?->value() ?? 0) + $adjustKeyForged2,
                    ],
                ),
            );
        }
    }

    private function checkHouses(Game $game, int $index, string $message): void
    {
        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+chooses\s+(\w*)\s+as their active house this turn\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::HOUSE_CHOSEN,
                    $matches[1],
                    new Turn($game->length, Moment::START, $index),
                    Source::UNKNOWN,
                    $matches[2],
                ),
            );
        }
    }

    private function checkCardsDrawn(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+draws\s+(\d+)\s+card/";

        if (preg_match($pattern, $message, $matches)) {
            $game->player($matches[1])?->timeline->add(
                new Event(
                    EventType::CARDS_DRAWN,
                    $matches[1],
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    (int) $matches[2],
                ),
            );
        }
    }

    private function checkCardsDiscarded(Game $game, int $index, string $message): void
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
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern3, $message, $matches)) {
            $player = $matches[1];
            /** @var list<string> $cards */
            $cards = preg_split('/\s*(?:,|\band\b)\s*/i', $matches[2]);
            $discardCount = count(array_filter(array_map('trim', $cards)));
        } elseif (preg_match($pattern4, $message, $matches)) {
            $player = $matches[2];
            $discardCount = 1;
            $source = Source::OPPONENT;
        }

        if ($player !== null && $discardCount > 0) {
            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_DISCARDED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    $source,
                    $discardCount,
                ),
            );
        }
    }

    private function checkCardsPlayed(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+plays\s+(.+)$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::CARDS_PLAYED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    [$card],
                ),
            );
        }
    }

    private function checkAmberStolen(Game $game, int $index, string $message): void
    {
        $matches = [];
        $matches2 = [];
        $matches3 = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*from\s*($player1|$player2)\s*$/";
        $pattern2 = "/^($player1|$player2)\s+uses\s+(.+)\s+to steal\s+(\d+)\s+Æmber\s*$/";
        $pattern3 = "/^($player1|$player2)\s+uses\s+(.+)\s+to\s+(.*)\s+and steal an Æmber\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $value = (int) $matches[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern2, $message, $matches2)) {
            $player = $matches2[1];
            $card = trim($matches2[2]);
            $value = (int) $matches2[3];

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $card],
                ),
            );
        } elseif (preg_match($pattern3, $message, $matches3)) {
            $player = $matches3[1];
            $card = trim($matches3[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::AMBER_STOLEN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    1,
                    ['trigger' => $card],
                ),
            );
        }
    }

    private function checkFight(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make\s+(.+)fight(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $trigger = trim($matches[2]);
            $value = trim($matches[3]);
            $target = trim($matches[4]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FIGHT,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $value,
                    ['trigger' => $trigger, 'target' => $target],
                ),
            );
        }
    }

    private function checkExtraTurn(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/($player1|$player2)\s+uses\s+(.+)\s+to take another turn after this one\s*(.*)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $trigger = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::EXTRA_TURN,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    1,
                    ['trigger' => $trigger],
                ),
            );
        }
    }

    private function checkReap(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to reap with\s+(.+)\s*$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);
            $card2 = trim($matches[3]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::REAP,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::PLAYER,
                    $card2,
                    ['trigger' => $card],
                ),
            );
        }
    }

    private function checkTokens(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $pattern = "/^($player1|$player2)\s+uses\s+(.+)\s+to make a token creature\s*.*$$/";

        if (preg_match($pattern, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::TOKEN_CREATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }
    }

    private function checkProphecies(Game $game, int $index, string $message): void
    {
        $matches = [];

        $player1 = $game->player1->escapedName();
        $player2 = $game->player2->escapedName();

        $patternFate = "/^($player1|$player2)\s+resolves the fate effect of\s+(.+)$/";
        $patternActivate = "/^($player1|$player2)\s+activates their prophecy\s+(.+)$/";
        $patternFulfilled = "/^($player1|$player2)\s+uses\s+(.+)\s+to fulfill its prophecy$/";

        if (preg_match($patternFate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::FATE_RESOLVED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternActivate, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_ACTIVATED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
            );
        }

        if (preg_match($patternFulfilled, $message, $matches)) {
            $player = $matches[1];
            $card = trim($matches[2]);

            $game->player($player)?->timeline->add(
                new Event(
                    EventType::PROPHECY_FULFILLED,
                    $player,
                    new Turn($game->length, Moment::BETWEEN, $index),
                    Source::UNKNOWN,
                    $card,
                ),
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
}
