<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('idOrder');
            $table->string('OrderName', 255);
            $table->timestamp('OrderDate')->useCurrent();
            $table->string('CustomerPhone', 20)->nullable();
            $table->string('CustomerEmail', 100)->nullable();
            $table->integer('TotalModules')->default(0);
            $table->integer('TotModulesReceived')->default(0);
            $table->enum('Status', ['Created', 'Dropped off', 'In Process', 'Done', 'Inactive'])->default('Created');
            $table->text('Notes')->nullable();
            $table->string('CreatedBy', 100)->nullable();
            $table->timestamp('DateDroppedOff')->nullable();
            $table->timestamp('DateCompleted')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('Status');
            $table->index('OrderDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
