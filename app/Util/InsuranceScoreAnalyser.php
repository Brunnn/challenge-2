<?php


namespace App\Util;

use App\Enums\InsurancePlanEnum;
use Illuminate\Support\Collection;

/**
 * Classe que analisa o score de linhas de seguro de acordo com regras definidas
 */
class InsuranceScoreAnalyser
{
    /**
     * Linhas de seguro a serem analisadas
     */
    protected Collection $insuranceLines;

    /**
     * Limiares de score para cada linha de seguro, contendo cada um o plano de seguro correspondente caso elegível
     */
    protected array $thresholds = [];

    /**
     * @param int $baseScore Score base para todas as linhas de seguro
     * @param string[] $insuranceLines Linhas de seguro a serem analisadas
     */
    public function __construct(int $baseScore, $insuranceLines)
    {
        $this->insuranceLines = collect($insuranceLines)->mapWithKeys(fn ($line) => [$line => new InsuranceScoreLineInstance($line, $baseScore)]);
    }

    /**
     * @param array $thresholds define os Limiares de score para cada linha de seguro.
     */
    public function mapScoreThresholds($thresholds): void
    {
        //Em um cenário ideal, deveria ser feita uma validação dos thresholds para garantir que todos os planos de seguro estão definidos
        //Para simplificar, assumimos que os thresholds estão corretos

        $this->thresholds = $thresholds;
    }

    /**
     * Aplica regras de elegibilidade para as linhas de seguro
     * @param callable $rule callback executado para aplicar regras de elegibilidade
     */
    public function applyRule(callable $rule): void
    {
        $rule($this->insuranceLines);
    }

    /**
     * Retorna todos o scores das linhas de seguro
     */
    public function getAllScores(): array
    {
        return $this->insuranceLines->map(fn ($line) => $line->getScore())->toArray();
    }

    /**
     * Retorna o resultado de elegibilidade de cada linha de seguro
     */
    public function getResults(): array
    {

        return $this->insuranceLines->map(function ($line) {
            if ($line->ineligible())
                return InsurancePlanEnum::INELIGIBLE;
            
            $thresholds = isset($this->thresholds[$line->getLine()]) ? $this->thresholds[$line->getLine()] : [];
            $score = $line->getScore();

            //Organiza as chaves de threshold em ordem crescente
            ksort($thresholds);

            foreach ($thresholds as $threshold => $plan) {
                if ($score <= $threshold) {
                    return $plan;
                }
            }
        })->toArray();
    }
}
