<?php

namespace App\Models;

use App\Enums\HouseOwnershipEnum;
use App\Enums\InsurancePlanEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceTest extends Model
{
    use HasFactory;


    protected $fillable = [
        'age',
        'dependents',
        'income',
        'house_ownership',
        'vehicle_year',
        'risk_questions',
        'auto_points',
        'disability_points',
        'home_points',
        'life_points',
        'auto',
        'disability',
        'home',
        'life',
    ];

    protected $casts = [
        'auto_points' => 'integer',
        'disability_points' => 'integer',
        'home_points' => 'integer',
        'life_points' => 'integer',
        'risk_questions' => 'array',
        'house_ownership' => HouseOwnershipEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'auto' => InsurancePlanEnum::class,
        'disability' => InsurancePlanEnum::class,
        'home' => InsurancePlanEnum::class,
        'life' => InsurancePlanEnum::class,
    ];
}
