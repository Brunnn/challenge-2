<?php

namespace App\Http\Requests;

use App\Enums\MaritalStatusEnum;
use App\Enums\HouseOwnershipEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class AnalyseInsuranceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'age' => 'required|integer|min:0',
            'dependents' => 'required|integer|min:0',
            'income' => 'required|integer|min:0',
            'marital_status' => ['required', new Enum(MaritalStatusEnum::class)],
            'risk_questions' => 'required|array|size:3',
            'risk_questions.*' => 'required|boolean',
            'house' => 'nullable|array',
            'house.house_ownership' => ['required_with:house', new Enum(HouseOwnershipEnum::class)],
            'vehicle' => 'nullable|array',
            'vehicle.year' => 'required_with:vehicle|integer|min:0',
        ];
    }
}
