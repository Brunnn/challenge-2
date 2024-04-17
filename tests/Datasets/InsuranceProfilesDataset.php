<?php

namespace Tests\Datasets;

use App\Enums\MaritalStatusEnum;
use App\Enums\HouseOwnershipEnum;

class InsuranceProfilesDataset
{
    public static function dataForService()
    {

        return [
            [
                "age" => 35,
                "dependents" => 0,
                "house" => HouseOwnershipEnum::NONE,
                "income" => 0,
                "marital_status" => MaritalStatusEnum::MARRIED,
                "risk_questions" => [false, true, true],
                "vehicle" => null
            ],
            [
                "age" => 35,
                "dependents" => 2,
                "house" => HouseOwnershipEnum::OWNED,
                "income" => 0,
                "marital_status" => MaritalStatusEnum::SINGLE,
                "risk_questions" => [false, true, true],
                "vehicle" => [
                    "year" => 2018
                ]
            ],

            [
                "age" => 24,
                "dependents" => 2,
                "house" => HouseOwnershipEnum::RENTED,
                "income" => 300000,
                "marital_status" => MaritalStatusEnum::SINGLE,
                "risk_questions" => [false, true, true],
                "vehicle" => null
            ],

            [
                "age" => 24,
                "dependents" => 2,
                "house" => HouseOwnershipEnum::NONE,
                "income" => 100000,
                "marital_status" => MaritalStatusEnum::MARRIED,
                "risk_questions" => [false, false, true],
                "vehicle" => null
            ],
        ];
    }
    public static function dataForHttp(): array
    {
        return [

            [
                "data" => [
                    "age" => 35,
                    "dependents" => 0,
                    "house" => null,
                    "income" => 0,
                    "marital_status" => "single",
                    "risk_questions" => [false, true, true],
                    "vehicle" => null
                ],
                "status" => 200
            ],
            [
                "data" =>  [
                    "age" => 35,
                    "dependents" => 2,
                    "house" => [
                        "house_ownership" => "owned"
                    ],
                    "income" => 0,
                    "marital_status" => "single",
                    "risk_questions" => [false, true, true],
                    "vehicle" => [
                        "year" => 2018
                    ]
                ],
                "status" => 200
            ],
            [
                "data" => [
                    "age" => 24,
                    "dependents" => 2,
                    "house" => [
                        "house_ownership" => "error"
                    ],
                    "income" => 300000,
                    "marital_status" => "single",
                    "risk_questions" => [false, true, true],
                    "vehicle" => null
                ],
                "status" => 422
            ],
            [
                "data" => [
                    "age" => 24,
                    "dependents" => -1,
                    "house" => null,
                    "income" => 100000,
                    "marital_status" => "married",
                    "risk_questions" => [false, false, true],
                    "vehicle" => null
                ],
                "status" => 422
            ],
        ];
    }
}
