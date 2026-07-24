<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminProjectStatusesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropAllTables();

        Schema::create('admins', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique()->nullable();
            $table->string('password');
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->string('vat_country_code')->nullable();
            $table->string('vat_number')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('created_from')->nullable();
            $table->timestamps();
        });

        Schema::create('projects', function ($table) {
            $table->id();
            $table->string('project_name');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('admin_project_statuses', function ($table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_syncs_multiple_project_status_rows_for_admin(): void
    {
        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'username' => 'testadmin',
            'password' => Hash::make('password'),
            'type' => Admin::ADMIN,
            'status' => 'Active',
            'created_by' => null,
            'parent_id' => null,
            'vat_country_code' => 'DE',
            'vat_number' => '1234',
            'created_from' => 1,
        ]);

        $projectOne = Project::create([
            'project_name' => 'Project One',
            'created_by' => $admin->id,
        ]);
        $projectTwo = Project::create([
            'project_name' => 'Project Two',
            'created_by' => $admin->id,
        ]);

        $admin->syncProjectStatuses([
            ['project_id' => $projectOne->id, 'status' => 'Active'],
            ['project_id' => $projectTwo->id, 'status' => 'Inactive'],
        ]);

        $this->assertCount(2, $admin->fresh()->projectStatuses);
        $this->assertDatabaseHas('admin_project_statuses', [
            'admin_id' => $admin->id,
            'project_id' => $projectOne->id,
            'status' => 'Active',
        ]);
        $this->assertDatabaseHas('admin_project_statuses', [
            'admin_id' => $admin->id,
            'project_id' => $projectTwo->id,
            'status' => 'Inactive',
        ]);

        $admin->syncProjectStatuses([
            ['project_id' => $projectOne->id, 'status' => 'Inactive'],
        ]);

        $this->assertCount(1, $admin->fresh()->projectStatuses);
        $this->assertDatabaseMissing('admin_project_statuses', [
            'admin_id' => $admin->id,
            'project_id' => $projectTwo->id,
        ]);
    }
}
