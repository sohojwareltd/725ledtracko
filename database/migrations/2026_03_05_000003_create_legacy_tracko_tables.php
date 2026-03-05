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
        if (!Schema::hasTable('company')) {
            Schema::create('company', function (Blueprint $table): void {
                $table->id('idCompany');
                $table->string('CompanyName', 255)->unique();
            });
        }

        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table): void {
                $table->id('idModule');
                $table->string('ModuleName', 255)->unique();
            });
        }

        if (!Schema::hasTable('companymodules')) {
            Schema::create('companymodules', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idModules');
                $table->unsignedBigInteger('idCompany');
                $table->unique(['idModules', 'idCompany']);
                $table->index('idCompany');
                $table->index('idModules');
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table): void {
                $table->id('idOrder');
                $table->string('CompanyName', 255);
                $table->string('idUser', 100)->nullable();
                $table->string('idUserLastUpdated', 100)->nullable();
                $table->unsignedInteger('TotModulesCaptured')->default(0);
                $table->unsignedInteger('TotModulesReceived')->default(0);
                $table->string('OrderStatus', 50)->default('Created');
                $table->timestamp('DateOrderCaptured')->nullable();
                $table->timestamp('DateLastModification')->nullable();
                $table->timestamp('DateOrderReceived')->nullable();
                $table->timestamp('DateDroppedOff')->nullable();
                $table->string('Location', 150)->nullable();
                $table->date('duedate')->nullable();

                $table->index('OrderStatus');
                $table->index('CompanyName');
                $table->index('DateOrderCaptured');
            });
        }

        if (!Schema::hasTable('orderdetails')) {
            Schema::create('orderdetails', function (Blueprint $table): void {
                $table->id('idOrderDetail');
                $table->unsignedBigInteger('idOrder');
                $table->string('ModuleModel', 255)->nullable();
                $table->string('Barcode', 50);
                $table->string('Damage', 255)->nullable();
                $table->string('RepairArea', 255)->nullable();
                $table->timestamp('DateReceived')->nullable();
                $table->timestamp('DateRepair')->nullable();
                $table->string('repairer', 100)->nullable();
                $table->string('QCStatus', 100)->nullable();
                $table->timestamp('DateQC')->nullable();
                $table->string('QCAgent', 100)->nullable();
                $table->string('QCRejectedArea', 255)->nullable();

                $table->unique(['idOrder', 'Barcode']);
                $table->index('Barcode');
                $table->index('DateRepair');
                $table->index('DateQC');
                $table->index('repairer');
                $table->index('QCAgent');

                $table->foreign('idOrder')->references('idOrder')->on('orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('useraudit')) {
            Schema::create('useraudit', function (Blueprint $table): void {
                $table->id('idAudit');
                $table->string('User', 100);
                $table->timestamp('Date')->nullable();
                $table->string('AuditDescription', 255);
                $table->string('IPAddress', 45)->nullable();
                $table->string('ActionType', 100)->nullable();

                $table->index('User');
                $table->index('Date');
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table): void {
                $table->id('Id_Message');
                $table->text('Message')->nullable();
                $table->text('Message2')->nullable();
                $table->text('Message3')->nullable();
                $table->timestamp('Date')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('useraudit');
        Schema::dropIfExists('orderdetails');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('companymodules');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('company');
    }
};
