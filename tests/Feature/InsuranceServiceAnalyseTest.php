<?php

namespace Tests\Unit;

use App\Models\InsuranceTest;
use Tests\Datasets\InsuranceProfilesDataset;
use App\Services\Interfaces\IInsuranceTestService;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class InsuranceServiceAnalyseTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {

        $dataset = InsuranceProfilesDataset::dataForService();
        $service = App::make(IInsuranceTestService::class);

        //The service should receive the analyse method and return anyhthing that is a valid Model instance
        foreach ($dataset as $item) {
            $result = $service->analyse($item['age'], $item['dependents'], $item['house'], $item['income'], $item['marital_status'], $item['risk_questions'], $item['vehicle']);
            $this->assertEquals(InsuranceTest::class, get_class($result));
        }
    }
}
