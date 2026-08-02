<?php

namespace Tests\Unit;

use App\Models\Beneficiary;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Migrant;
use App\Models\Returnee;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_has_beneficiaries()
    {
        $branch = Branch::factory()->create();
        $beneficiary = Beneficiary::factory()->create(['branch_id' => $branch->id]);

        $this->assertTrue($branch->beneficiaries->contains($beneficiary));
    }

    public function test_beneficiary_has_migrants()
    {
        $beneficiary = Beneficiary::factory()->create();
        $migrant = Migrant::factory()->create(['beneficiary_id' => $beneficiary->id]);

        $this->assertTrue($beneficiary->migrants->contains($migrant));
    }

    public function test_migrant_has_returnee()
    {
        $migrant = Migrant::factory()->create();
        $returnee = Returnee::factory()->create([
            'migrant_id' => $migrant->id,
            'beneficiary_id' => $migrant->beneficiary_id,
        ]);

        $this->assertNotNull($returnee->migrant);
        $this->assertNotNull($returnee->beneficiary);
    }

    public function test_user_has_staff()
    {
        $staff = Staff::factory()->create();

        $this->assertNotNull($staff->user);
        $this->assertNotNull($staff->branch);
    }

    public function test_country_has_migrants()
    {
        $country = Country::factory()->create();
        $migrant = Migrant::factory()->create(['destination_country_id' => $country->id]);

        $this->assertTrue($country->migrants->contains($migrant));
    }
}
