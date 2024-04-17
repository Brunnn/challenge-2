<?php

namespace Tests\Feature;

use GuzzleHttp\Promise\Each;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Datasets\InsuranceProfilesDataset;
use Tests\TestCase;

class InsuranceAnalyseEndpointTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $data = InsuranceProfilesDataset::dataForHttp();

        foreach ($data as $item) {
            $response = $this->post('/api/insurance/analyse', $item["data"]);
            $response->assertStatus($item["status"]);
        }
    }
}
