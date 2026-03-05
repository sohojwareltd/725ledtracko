<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('orderdetails') || !Schema::hasColumn('orderdetails', 'Barcode')) {
            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $database = DB::getDatabaseName();
        $column = DB::selectOne(
            "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'orderdetails' AND COLUMN_NAME = 'Barcode' LIMIT 1",
            [$database]
        );

        $currentType = strtolower((string) ($column->DATA_TYPE ?? ''));
        $numericTypes = [
            'tinyint',
            'smallint',
            'mediumint',
            'int',
            'bigint',
            'decimal',
            'numeric',
            'float',
            'double',
            'real',
        ];

        if (in_array($currentType, $numericTypes, true)) {
            $previousModeRow = DB::selectOne('SELECT @@SESSION.sql_mode AS mode');
            $previousMode = (string) ($previousModeRow->mode ?? '');

            try {
                // Legacy imports contain zero-datetime values that can break ALTER in strict mode.
                DB::statement("SET SESSION sql_mode = ''");
                DB::statement('ALTER TABLE orderdetails MODIFY Barcode VARCHAR(50) NULL');
            } finally {
                DB::statement('SET SESSION sql_mode = ?', [$previousMode]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally no-op. Downgrading Barcode back to numeric would lose leading digits.
    }
};
