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
        // Entities - aumentar campos que serão cifrados
        Schema::table('entities', function (Blueprint $table) {
            $table->text('tax_number')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('mobile')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('iban')->nullable()->change();
        });

        // Contacts - aumentar campos que serão cifrados
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();
            $table->text('mobile')->nullable()->change();
            $table->text('email')->nullable()->change();
        });

        // Bank Accounts - aumentar campos que serão cifrados
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->text('iban')->nullable()->change();
            $table->text('swift_bic')->nullable()->change();
        });

        // Users - aumentar mobile (email não é cifrado)
        Schema::table('users', function (Blueprint $table) {
            $table->text('mobile')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para VARCHAR (não recomendado após cifrar dados)
        Schema::table('entities', function (Blueprint $table) {
            $table->string('tax_number', 50)->nullable()->change();
            $table->string('phone', 20)->nullable()->change();
            $table->string('mobile', 20)->nullable()->change();
            $table->string('email', 100)->nullable()->change();
            $table->string('iban', 34)->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
            $table->string('mobile', 20)->nullable()->change();
            $table->string('email', 100)->nullable()->change();
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->change();
            $table->string('swift_bic', 11)->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 20)->nullable()->change();
        });
    }
};
