<?php


namespace App\Util;

use App\Enums\HouseOwnershipEnum;
use App\Enums\InsurancePlanEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Support\Collection;

class InsuranceScoreAnalyser
{

    /**
     * @var Collection<InsuranceScoreLineInstance>
     */
    protected Collection $insuranceLines;

    protected array $thresholds = [];

    public function __construct(int $baseScore, array $insuranceLines)
    {
        $this->insuranceLines = collect($insuranceLines)->mapWithKeys(fn ($line) => [$line => new InsuranceScoreLineInstance($line, $baseScore)]);
    }

    public function mapScoreThresholds(array $thresholds): void
    {
        $this->thresholds = $thresholds;
    }

    /**
     * @param callable(Collection<InsuranceScoreLineInstance>): void $rule
     */
    public function applyRule(callable $rule): void
    {
        $rule($this->insuranceLines);
    }

    public function getAllScores(): array
    {
        return $this->insuranceLines->map(fn ($line) => $line->getScore())->toArray();
    }

    public function getResults(): array
    {
        return $this->insuranceLines->map(function ($line) {
            if ($line->ineligible())
                return InsurancePlanEnum::INELIGIBLE;
            
            $thresholds = isset($this->thresholds[$line->getLine()]) ? $this->thresholds[$line->getLine()] : [];
            $score = $line->getScore();

            //Sort thresholds keys since they represent the needed score to get that plan
            ksort($thresholds);

            foreach ($thresholds as $threshold => $plan) {
                if ($score <= $threshold) {
                    return $plan;
                }
            }
        })->toArray();
    }
}
