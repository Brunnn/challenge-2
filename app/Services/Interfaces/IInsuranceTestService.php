<?php

namespace App\Services\Interfaces;

use App\Enums\MaritalStatusEnum;
use App\Enums\HouseOwnershipEnum;
use App\Models\InsuranceTest;
use Illuminate\Validation\ValidationException;

interface IInsuranceTestService
{
    /**
     * @param int $age
     * @param int $dependents
     * @param HouseOwnershipEnum $houseOwnership
     * @param int $income
     * @param MaritalStatusEnum $maritalStatus
     * @param array $riskQuestions
     * @param null|array{year:int} $vehicle
     * @throws ValidationException
     */
    public function analyse($age, $dependents, $houseOwnership, $income, $maritalStatus, $riskQuestions, $vehicle): InsuranceTest;
}
