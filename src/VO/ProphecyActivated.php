<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO;

use AdnanMula\KeyforgeGameLogParser\VO\Shared\Event;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Itemsito;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Source;
use AdnanMula\KeyforgeGameLogParser\VO\Shared\Turn;

final readonly class ProphecyActivated extends Itemsito
{
    public function __construct(string $player, Turn $turn, string $value)
    {
        parent::__construct($player, $turn, Source::PLAYER, $value);
    }

    public function type(): Event
    {
        return Event::PROPHECY_ACTIVATED;
    }
}
