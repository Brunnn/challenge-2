<?php

namespace App\Services;

use App\Enums\HouseOwnershipEnum;
use App\Models\InsuranceTest;
use App\Enums\InsurancePlanEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Util\InsuranceScoreAnalyser;
use App\Services\Interfaces\IInsuranceTestService;

class InsuranceTestService implements IInsuranceTestService
{


    public function analyse($age, $dependents, $houseOwnership, $income, $maritalStatus, $riskQuestions, $vehicle): InsuranceTest
    {
        $baseScore = array_sum($riskQuestions);

        //Mapeia os planos de seguro e os thresholds de risco para cada linha de seguro
        $analyser = new InsuranceScoreAnalyser($baseScore, ['auto', 'disability', 'home', 'life']);
        $analyser->mapScoreThresholds([
            'auto' => [0 => InsurancePlanEnum::ECONOMIC, 1 => InsurancePlanEnum::STANDARD, 3 => InsurancePlanEnum::ADVANCED],
            'disability' => [0 => InsurancePlanEnum::ECONOMIC, 1 => InsurancePlanEnum::STANDARD, 3 => InsurancePlanEnum::ADVANCED],
            'home' => [0 => InsurancePlanEnum::ECONOMIC, 1 => InsurancePlanEnum::STANDARD, 3 => InsurancePlanEnum::ADVANCED],
            'life' => [0 => InsurancePlanEnum::ECONOMIC, 1 => InsurancePlanEnum::STANDARD, 3 => InsurancePlanEnum::ADVANCED],
        ]);

        //Se o usuário não tem renda, veiculo ou casa, ele é inelegivel para invalidez, seguros auto e residencial, respectivamente.
        $analyser->applyRule(function (Collection $insuranceLines) use ($income, $vehicle, $houseOwnership) {
            if ($income <= 0) {
                $insuranceLines->get('disability')->setIneligible();
            }
            if ($vehicle === null) {
                $insuranceLines->get('auto')->setIneligible();
            }
            if ($houseOwnership == HouseOwnershipEnum::NONE) {
                $insuranceLines->get('home')->setIneligible();
            }
        });

        //Se o usuário tem mais de 60 anos, ele é inelegivel para invalidez e seguro de vida.
        $analyser->applyRule(function (Collection $insuranceLines) use ($age) {
            if ($age > 60) {
                $insuranceLines->get('disability')->setIneligible();
                $insuranceLines->get('life')->setIneligible();
            }
        });

        //Se o usuário tem menos de 30 anos, diminua 2 pontos de risco de todas as linhas de seguro. Se ele tiver entre 30 e 40, diminua 1.
        $analyser->applyRule(function (Collection $insuranceLines) use ($age) {
            $ageDiscount = $age < 30 ? 2 : ($age < 40 ? 1 : 0);
            $insuranceLines->each(fn ($line) => $line->decreaseScore($ageDiscount));
        });

        //Se a renda for superior a 200k, diminua 1 ponto de risco de todas as linhas de seguro.
        $analyser->applyRule(function (Collection $insuranceLines) use ($income) {
            if ($income > 200000) {
                $insuranceLines->each(fn ($line) => $line->decreaseScore(1));
            }
        });

        //Se a casa do usuário é alugada, adicione 1 ponto de risco no seguro residencial e de invalidez
        $analyser->applyRule(function (Collection $insuranceLines) use ($houseOwnership) {
            if ($houseOwnership == HouseOwnershipEnum::RENTED) {
                $insuranceLines->get('home')->increaseScore(1);
                $insuranceLines->get('disability')->increaseScore(1);
            }
        });

        //Se o usuário tem dependentes, adicione 1 ponto em ambos os riscos de invalidez e vida.
        $analyser->applyRule(function (Collection $insuranceLines) use ($dependents) {
            if ($dependents > 0) {
                $insuranceLines->get('disability')->increaseScore(1);
                $insuranceLines->get('life')->increaseScore(1);
            }
        });

        //Se o usuario for casado, adicione 1 ponto em vida, e remova 1 ponto em invalidez.
        $analyser->applyRule(function (Collection $insuranceLines) use ($maritalStatus) {
            if ($maritalStatus == MaritalStatusEnum::MARRIED) {
                $insuranceLines->get('disability')->decreaseScore(1);
                $insuranceLines->get('life')->increaseScore(1);
            }
        });

        //Se o veiculo dele tiver sido produzido nos ultimos 5 anos, adicione 1 ponto no veiculo.
        $analyser->applyRule(function (Collection $insuranceLines) use ($vehicle) {
            if ($vehicle !== null && $vehicle['year'] >= date('Y') - 5) {
                $insuranceLines->get('auto')->increaseScore(1);
            }
        });

        //Persiste o resultado no banco de dados
        return DB::transaction(function () use ($age, $dependents, $houseOwnership, $income, $maritalStatus, $riskQuestions, $vehicle, $analyser) {
            $insuranceTest = new InsuranceTest();
            $insuranceTest->age = $age;
            $insuranceTest->dependents = $dependents;
            $insuranceTest->income = $income;
            $insuranceTest->house_ownership = $houseOwnership;
            $insuranceTest->vehicle_year = $vehicle['year'] ?? null;
            $insuranceTest->risk_questions = $riskQuestions;
            $insuranceTest->marital_status = $maritalStatus;

            //Pontos
            $scores = $analyser->getAllScores();
            $insuranceTest->auto_points = $scores['auto'];
            $insuranceTest->disability_points = $scores['disability'];
            $insuranceTest->home_points = $scores['home'];
            $insuranceTest->life_points = $scores['life'];

            //Planos
            $results = $analyser->getResults();

            $insuranceTest->auto = $results['auto'];
            $insuranceTest->disability = $results['disability'];
            $insuranceTest->home = $results['home'];
            $insuranceTest->life = $results['life'];

            if (!app()->runningUnitTests())
                $insuranceTest->save();

            return $insuranceTest;
        });
    }
}
