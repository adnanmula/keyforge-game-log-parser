# KeyForge Game Log Parser

Parse The Crucible game logs into a structured model.

## Requirements
- PHP >= 8.4

## Installation
Install via Composer:

```bash
composer require adnanmula/keyforge-game-log-parser
```

## Quick start

```php
<?php

use AdnanMula\KeyforgeGameLogParser\Event\EventType;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;

$log = file_get_contents('path/to/log.txt');

$parser = new GameLogParser();
$game = $parser->execute($log, ParseType::PLAIN);

$winner = $game->winner()?->name;
$keysForged = $game->timeline()->filter(EventType::KEY_FORGED)->count();
$reapsByPlayer1 = $game->player1->timeline->filter(EventType::REAP)->count();
$firstCreatureUsedToReapByPlayer1 = $game->player1->timeline->filter(EventType::REAP)->at(0)->value;
```

### Input formats
You can parse logs provided in 3 different formats via `ParseType`:
- `ParseType::PLAIN` (default): a single string containing the full log, what you get when use tco's copy to clipboard button.
- `ParseType::ARRAY`: an array of strings containing the log messages, one per line.
- `ParseType::HTML`: the HTML markup of the log view.

```php
<?php

use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use AdnanMula\KeyforgeGameLogParser\Parser\ParseType;

$parser = new GameLogParser();

$plainString = '
    Alice brings Deck A to The Crucible
    Bob brings Deck B to The Crucible
    ...
';

// PLAIN
$game1 = $parser->execute($plainString, ParseType::PLAIN);

// ARRAY
$messages = [
    'Alice brings Deck A to The Crucible',
    'Bob brings Deck B to The Crucible',
    // ...
];
$game2 = $parser->execute($messages, ParseType::ARRAY);

// HTML
$html = '<div class="message">Alice brings Deck A to The Crucible</div>...';
$game3 = $parser->execute($html, ParseType::HTML);
```

## Exploring results
`execute()` returns a `Game` object with the following useful members:

- `player1`, `player2` (AdnanMula\KeyforgeGameLogParser\Game\Player)
  - `name`, `deck`, `isFirst`, `isWinner`, `hasConceded`
  - `timeline` (AdnanMula\KeyforgeGameLogParser\Game\Timeline)
- `winner(): ?Player`
- `loser(): ?Player`
- `first(): ?Player` (the player who took the first turn)
- `timeline(): Timeline` (combined, time-ordered events from both players)

Timelines are collections with helper methods:
- `filter(EventType ...$events): Collection`
- `count()`, `first()`, `last()`, `at(int $i)`, `items(): array`

Example queries:

```php
<?php

use AdnanMula\KeyforgeGameLogParser\Event\EventType;

// Checks declared across the whole game
$checks = $game->timeline()->filter(EventType::CHECK_DECLARED)->items();

// Player-specific timeline queries
$p1Checks = $game->player1->timeline->filter(EventType::CHECK_DECLARED)->items();
$p2Keys   = $game->player2->timeline->filter(EventType::KEY_FORGED)->items();
$reapsAndFights   = $game->player2->timeline->filter(EventType::REAP, EventType::FIGHT)->items();

etc...
```

## Events
The library categorizes log messages into typed events via `EventType` enum:

- AMBER_OBTAINED
- AMBER_STOLEN
- CARDS_DISCARDED
- CARDS_DRAWN
- CARDS_PLAYED
- CARD_USED
- HOUSE_CHOSEN
- KEY_FORGED
- FIGHT
- REAP
- EXTRA_TURN
- TOKEN_CREATED
- PROPHECY_ACTIVATED
- PROPHECY_FULFILLED
- FATE_RESOLVED
- TIDE_RAISED
- CHAINS_ADDED
- CHAINS_REDUCED
- PLAYER_CONCEDED
- CHECK_DECLARED
