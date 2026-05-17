<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Project;
use App\Models\SetSalary;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class cekHariTest extends TestCase
{
    use RefreshDatabase;

    public function test_cek_hari()
    {
        Http::fake([
            '*' => Http::response([], 200)
        ]);

        // buat project dulu
        $project = Project::create([
            'name' => 'Project Test',
        ]);

        SetSalary::create([
            'project_id' => $project->id,
            'workdays' => [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
            ],
        ]);

        $result = \App\Helpers\PayrollHelpers::cekHari(
            '2026-05-27',
            $project->id
        );

        $this->assertEquals('Holiday', $result);
    }
}
