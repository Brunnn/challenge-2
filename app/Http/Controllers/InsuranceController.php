<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\MaritalStatusEnum;
use App\Enums\HouseOwnershipEnum;
use App\Http\Requests\AnalyseInsuranceRequest;
use App\Http\Resources\InsuranceTestResource;
use App\Services\Interfaces\IInsuranceTestService;
use Illuminate\Support\Facades\DB;

class InsuranceController extends Controller
{
    public function __construct(protected IInsuranceTestService $insuranceTestService)
    {
    }
    public function analyse(AnalyseInsuranceRequest $request)
    {
        $age = $request->input('age');
        $dependents = $request->input('dependents');
        $income = $request->input('income');
        $maritalStatus = MaritalStatusEnum::from($request->input('marital_status'));
        $riskQuestions = $request->input('risk_questions');
        $houseOwnership = $request->input("house") ? HouseOwnershipEnum::from($request->input('house')['house_ownership']) : HouseOwnershipEnum::NONE;
        $vehicle = $request->input("vehicle");

        $insuranceTest = $this->insuranceTestService->analyse(
            $age,
            $dependents,
            $houseOwnership,
            $income,
            $maritalStatus,
            $riskQuestions,
            $vehicle
        );
        return new InsuranceTestResource($insuranceTest);
    }
}
