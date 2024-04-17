<?php

namespace App\Util;

class InsuranceScoreLineInstance
{
    protected string $name;
    protected int $score;
    protected $ineligible = false;

    public function __construct(string $line, int $score)
    {
        $this->name = $line;
        $this->score = $score;
    }

    public function getLine(): string
    {
        return $this->name;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function ineligible(): bool
    {
        return $this->ineligible;
    }

    public function increaseScore(int $points): void
    {
        $this->score += $points;
    }

    public function decreaseScore(int $points): void
    {
        $this->score -= $points;
    }

    public function setIneligible(): void
    {
        $this->ineligible = true;
    }
}