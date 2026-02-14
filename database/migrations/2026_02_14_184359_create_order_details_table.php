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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idOrder')->constrained('orders', 'idOrder')->onDelete('cascade');
            $table->string('Barcode', 100)->unique();
            $table->string('ModuleModel', 100)->nullable();
            $table->string('Damage', 255)->nullable();
            $table->timestamp('DateReceived')->nullable();
            $table->string('ReceivedBy', 100)->nullable();
            $table->timestamp('DateRepair')->nullable();
            $table->string('RepairedBy', 100)->nullable();
            $table->text('RepairNotes')->nullable();
            $table->enum('QCStatus', ['Passed', 'Rejected', 'Pending'])->nullable();
            $table->timestamp('QCDate')->nullable();
            $table->string('QCAgent', 100)->nullable();
            $table->text('QCNotes')->nullable();
            $table->integer('RepairTime')->nullable()->comment('Minutes');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('Barcode');
            $table->index('idOrder');
            $table->index('QCStatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
