<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\JobOpportunity;
use App\Models\GraduateData;
use App\Models\Nomination;
use App\Models\PartnershipDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'graduate'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'graduate'
        ]);
    }

    /** @test */
    public function it_returns_available_roles()
    {
        $roles = User::getAvailableRoles();

        $this->assertIsArray($roles);
        $this->assertArrayHasKey('admin', $roles);
        $this->assertArrayHasKey('graduate', $roles);
        $this->assertArrayHasKey('training_coordinator', $roles);
        $this->assertArrayHasKey('partnership_officer', $roles);
        $this->assertArrayHasKey('career_guidance_officer', $roles);
        $this->assertArrayHasKey('company', $roles);
    }

    /** @test */
    public function it_checks_admin_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($graduate->isAdmin());
    }

    /** @test */
    public function it_checks_graduate_role()
    {
        $graduate = User::factory()->create(['role' => 'graduate']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($graduate->isGraduate());
        $this->assertFalse($admin->isGraduate());
    }

    /** @test */
    public function it_checks_training_coordinator_role()
    {
        $coordinator = User::factory()->create(['role' => 'training_coordinator']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($coordinator->isTrainingCoordinator());
        $this->assertFalse($admin->isTrainingCoordinator());
    }

    /** @test */
    public function it_checks_partnership_officer_role()
    {
        $officer = User::factory()->create(['role' => 'partnership_officer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($officer->isPartnershipOfficer());
        $this->assertFalse($admin->isPartnershipOfficer());
    }

    /** @test */
    public function it_checks_career_guidance_officer_role()
    {
        $officer = User::factory()->create(['role' => 'career_guidance_officer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($officer->isCareerGuidanceOfficer());
        $this->assertFalse($admin->isCareerGuidanceOfficer());
    }

    /** @test */
    public function it_checks_company_role()
    {
        $company = User::factory()->create(['role' => 'company']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($company->isCompany());
        $this->assertFalse($admin->isCompany());
    }

    /** @test */
    public function it_can_manage_companies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $partnership = User::factory()->create(['role' => 'partnership_officer']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->canManageCompanies());
        $this->assertTrue($partnership->canManageCompanies());
        $this->assertFalse($graduate->canManageCompanies());
    }

    /** @test */
    public function it_can_manage_job_opportunities()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $partnership = User::factory()->create(['role' => 'partnership_officer']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->canManageJobOpportunities());
        $this->assertTrue($partnership->canManageJobOpportunities());
        $this->assertFalse($graduate->canManageJobOpportunities());
    }

    /** @test */
    public function it_can_manage_graduates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $careerOfficer = User::factory()->create(['role' => 'career_guidance_officer']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->canManageGraduates());
        $this->assertTrue($careerOfficer->canManageGraduates());
        $this->assertFalse($graduate->canManageGraduates());
    }

    /** @test */
    public function it_can_manage_nominations()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $careerOfficer = User::factory()->create(['role' => 'career_guidance_officer']);
        $partnership = User::factory()->create(['role' => 'partnership_officer']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->canManageNominations());
        $this->assertTrue($careerOfficer->canManageNominations());
        $this->assertTrue($partnership->canManageNominations());
        $this->assertFalse($graduate->canManageNominations());
    }

    /** @test */
    public function it_can_add_graduates()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $careerOfficer = User::factory()->create(['role' => 'career_guidance_officer']);
        $graduate = User::factory()->create(['role' => 'graduate']);

        $this->assertTrue($admin->canAddGraduates());
        $this->assertTrue($careerOfficer->canAddGraduates());
        $this->assertFalse($graduate->canAddGraduates());
    }

    /** @test */
    public function it_returns_readable_role_name()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $this->assertEquals('مدير النظام', $user->role_name);
    }

    /** @test */
    public function it_has_company_relationship()
    {
        $user = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Company::class, $user->company);
        $this->assertEquals($company->id, $user->company->id);
    }

    /** @test */
    public function it_has_created_job_opportunities_relationship()
    {
        $user = User::factory()->create();
        $job = JobOpportunity::factory()->create(['created_by' => $user->id]);

        $this->assertCount(1, $user->createdJobOpportunities);
        $this->assertEquals($job->id, $user->createdJobOpportunities->first()->id);
    }

    /** @test */
    public function it_has_added_graduates_relationship()
    {
        $user = User::factory()->create();
        $graduate = GraduateData::factory()->create(['added_by' => $user->id]);

        $this->assertCount(1, $user->addedGraduates);
        $this->assertEquals($graduate->id, $user->addedGraduates->first()->id);
    }

    /** @test */
    public function it_can_scope_by_role()
    {
        User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'graduate']);
        
        $admins = User::byRole('admin')->get();
        
        $this->assertCount(1, $admins);
        $this->assertEquals('admin', $admins->first()->role);
    }

    /** @test */
    public function it_can_scope_active_users()
    {
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);
        
        $activeUsers = User::active()->get();
        
        $this->assertCount(1, $activeUsers);
        $this->assertTrue($activeUsers->first()->is_active);
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $user = User::factory()->create([
            'skills' => ['PHP', 'Laravel', 'JavaScript'],
            'gpa' => 3.75,
            'graduation_year' => 2023,
            'is_active' => true
        ]);

        $this->assertIsArray($user->skills);
        $this->assertEquals(['PHP', 'Laravel', 'JavaScript'], $user->skills);
        $this->assertEquals(3.75, $user->gpa);
        $this->assertEquals(2023, $user->graduation_year);
        $this->assertTrue($user->is_active);
    }

    /** @test */
    public function it_calculates_counts_correctly()
    {
        $user = User::factory()->create();
        
        JobOpportunity::factory()->count(3)->create(['created_by' => $user->id]);
        GraduateData::factory()->count(2)->create(['added_by' => $user->id]);
        Nomination::factory()->count(4)->create(['nominated_by' => $user->id]);
        PartnershipDocument::factory()->count(1)->create(['uploaded_by' => $user->id]);

        $this->assertEquals(3, $user->job_opportunities_count);
        $this->assertEquals(2, $user->graduates_added_count);
        $this->assertEquals(4, $user->nominations_count);
        $this->assertEquals(1, $user->uploaded_documents_count);
    }
}
?>