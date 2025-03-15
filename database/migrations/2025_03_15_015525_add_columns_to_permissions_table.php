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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->after('name')->nullable();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('sequential_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('sequential_id');
            $table->dropColumn('description');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('sequential_id');
        });
    }
};
