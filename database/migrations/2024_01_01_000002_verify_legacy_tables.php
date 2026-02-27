<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Documents the pre-existing legacy schema for reference.
 *
 * Uses raw SHOW TABLES LIKE queries instead of Schema::hasTable() to avoid
 * Windows MySQL case-sensitivity issues with information_schema lookups.
 *
 * Only creates tables that are genuinely absent. Never touches existing tables.
 */
return new class extends Migration
{
    /**
     * Check table existence via SHOW TABLES — avoids information_schema
     * case-sensitivity problems on Windows MySQL.
     */
    private function tableExists(string $table): bool
    {
        $results = DB::select("SHOW TABLES LIKE '{$table}'");
        return count($results) > 0;
    }

    public function up(): void
    {
        if (! $this->tableExists('metadata')) {
            Schema::create('metadata', function (Blueprint $table) {
                $table->id();
                $table->string('category', 100);
                $table->string('value', 255);
                $table->integer('sort')->default(0);
                $table->tinyInteger('isActive')->default(1);
            });
        }

        if (! $this->tableExists('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->nullable();
                $table->string('contact', 100)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('emailAddress', 100)->nullable();
                $table->string('address', 255)->nullable();
                $table->tinyInteger('isActive')->default(1);
            });
        }

        if (! $this->tableExists('gages')) {
            Schema::create('gages', function (Blueprint $table) {
                $table->id();
                $table->string('gageNumber', 50)->unique();
                $table->string('description', 255)->nullable();
                $table->string('serialNumber', 100)->nullable();
                $table->string('modelNumber', 100)->nullable();
                $table->string('nistNumber', 100)->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('statusId')->nullable();
                $table->unsignedBigInteger('typeId')->nullable();
                $table->unsignedBigInteger('locationId')->nullable();
                $table->unsignedBigInteger('manufacturerId')->nullable();
                $table->unsignedBigInteger('ownerId')->nullable();
                $table->unsignedBigInteger('unitMeasureId')->nullable();
                $table->unsignedBigInteger('frequencyUnitId')->nullable();
                $table->unsignedBigInteger('supplierId')->nullable();
                $table->integer('frequency')->nullable();
                $table->date('dateDue')->nullable();
                $table->tinyInteger('isActive')->default(1);
            });
        }

        if (! $this->tableExists('calibrations')) {
            Schema::create('calibrations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gageId');
                $table->date('dateCalibrated')->nullable();
                $table->unsignedBigInteger('calibrationById')->nullable();
                $table->unsignedBigInteger('calibrationTypeId')->nullable();
                $table->unsignedBigInteger('calibrationStatusId')->nullable();
                $table->unsignedBigInteger('foundConditionId')->nullable();
                $table->text('results')->nullable();
                $table->text('actionRequired')->nullable();
                $table->text('findings')->nullable();
                $table->string('temperature', 50)->nullable();
                $table->string('humidity', 50)->nullable();
                $table->integer('frequency')->nullable();
                $table->unsignedBigInteger('frequencyUnitId')->nullable();
                $table->string('certificateNumber', 100)->nullable();
                $table->string('certificateFilename', 255)->nullable();
                $table->tinyInteger('isPassed')->default(0);
                $table->string('timeRequired', 50)->nullable();
            });
        }

        if (! $this->tableExists('configurations')) {
            Schema::create('configurations', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
            });
        }

        if (! $this->tableExists('procedures')) {
            Schema::create('procedures', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->tinyInteger('isActive')->default(1);
            });
        }
    }

    public function down(): void
    {
        // Never drop legacy tables — data cannot be recovered.
    }
};
