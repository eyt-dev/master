<?php

use App\Helpers\ProjectHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $mappings = [
        ['type' => 'Farm Admin',     'url' => 'http://add2care.de/farm-admin',     'parent_type' => 'Admin'],
        ['type' => 'Farm Owner',     'url' => 'http://add2care.de/farm-owner',     'parent_type' => 'Public Vendors'],
        ['type' => 'Supervisor',     'url' => 'http://add2care.de/supervisor',     'parent_type' => 'Private Vendors'],
        ['type' => 'Farmer',         'url' => 'http://add2care.de/farmer',         'parent_type' => 'Users'],
        ['type' => 'Feed Supplier',  'url' => 'http://add2care.de/feed-supplier',  'parent_type' => 'Admin'],
        ['type' => 'Chick Supplier', 'url' => 'http://add2care.de/Chick-supplier', 'parent_type' => 'Admin'],
        ['type' => 'Slaughter',      'url' => 'http://add2care.de/slaughter',      'parent_type' => 'Admin'],
        ['type' => 'Egg Wholesaler', 'url' => 'http://add2care.de/egg-wholesaler', 'parent_type' => 'Admin'],
    ];

    public function up(): void
    {
        $projectId = ProjectHelper::getAdd2FarmProjectId();

        if (!$projectId) {
            return;
        }

        foreach ($this->mappings as $mapping) {
            $exists = DB::table('type_mappings')
                ->where('project_id', $projectId)
                ->where('type', $mapping['type'])
                ->where('url', $mapping['url'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('type_mappings')->insert([
                'project_id'  => $projectId,
                'type'        => $mapping['type'],
                'url'         => $mapping['url'],
                'parent_type' => $mapping['parent_type'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        $projectId = ProjectHelper::getAdd2FarmProjectId();

        if (!$projectId) {
            return;
        }

        DB::table('type_mappings')
            ->where('project_id', $projectId)
            ->whereIn('url', array_column($this->mappings, 'url'))
            ->delete();
    }
};
