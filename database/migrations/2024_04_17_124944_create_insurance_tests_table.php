<?php

use App\Enums\InsurancePlanEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insurance_tests', function (Blueprint $table) {
            $table->id();
            $table->integer("age")->default(0);
            $table->string("marital_status");
            $table->integer("dependents")->default(0);
            $table->integer("income")->default(0);
            $table->string("house_ownership");
            $table->string("vehicle_year")->nullable();
            $table->json("risk_questions");
            $table->integer("auto_points")->default(0);
            $table->integer("disability_points")->default(0);
            $table->integer("home_points")->default(0);
            $table->integer("life_points")->default(0);
            $table->string("auto")->default(InsurancePlanEnum::INELIGIBLE->value);
            $table->string("disability")->default(InsurancePlanEnum::INELIGIBLE->value);
            $table->string("home")->default(InsurancePlanEnum::INELIGIBLE->value);
            $table->string("life")->default(InsurancePlanEnum::INELIGIBLE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_tests');
    }
};
