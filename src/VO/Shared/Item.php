<?php declare(strict_types=1);

namespace AdnanMula\KeyforgeGameLogParser\VO\Shared;

use JsonSerializable;

interface Item extends JsonSerializable
{
    public static function fromArray(array $array): self;
    public function type(): Event;
    public function player(): string;
    public function turn(): Turn;
    public function value(): int|string|array;
}
