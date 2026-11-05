<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Legacy admins.type values => type_mappings.type names they were meant to be
    private array $legacyTypes = [
        1 => 'Farm Admin',
        2 => 'Farm Owner',
        3 => 'Supervisor',
        4 => 'Farmer',
    ];

    public function up(): void
    {
        // Parent type spelling used by the Type Mapping seed
        DB::table('type_mappings')
            ->where('parent_type', 'Admins')
            ->update(['parent_type' => 'Admin']);

        $legacyIds = array_keys($this->legacyTypes);

        $hasLegacyAdmins = DB::table('admins')->whereIn('type', $legacyIds)->exists();
        if (!$hasLegacyAdmins) {
            return;
        }

        $mappingIds = [];
        foreach ($this->legacyTypes as $legacy => $name) {
            $id = DB::table('type_mappings')->where('type', $name)->orderBy('id')->value('id');

            if (!$id) {
                throw new RuntimeException("Type mapping '{$name}' is missing. Run the Type Mapping seed before this migration.");
            }

            $mappingIds[$legacy] = $id;
        }

        // Single UPDATE so no row is remapped twice
        $case = 'CASE type';
        foreach ($mappingIds as $legacy => $id) {
            $case .= ' WHEN ' . (int) $legacy . ' THEN ' . (int) $id;
        }
        $case .= ' END';

        DB::table('admins')
            ->whereIn('type', $legacyIds)
            ->update(['type' => DB::raw($case)]);
    }

    public function down(): void
    {
        $mappingToLegacy = [];
        foreach ($this->legacyTypes as $legacy => $name) {
            $id = DB::table('type_mappings')->where('type', $name)->orderBy('id')->value('id');
            if ($id) {
                $mappingToLegacy[$id] = $legacy;
            }
        }

        if (!empty($mappingToLegacy)) {
            $case = 'CASE type';
            foreach ($mappingToLegacy as $id => $legacy) {
                $case .= ' WHEN ' . (int) $id . ' THEN ' . (int) $legacy;
            }
            $case .= ' END';

            DB::table('admins')
                ->whereIn('type', array_keys($mappingToLegacy))
                ->update(['type' => DB::raw($case)]);
        }

        DB::table('type_mappings')
            ->where('type', 'Farm Admin')
            ->where('parent_type', 'Admin')
            ->update(['parent_type' => 'Admins']);
    }
};
