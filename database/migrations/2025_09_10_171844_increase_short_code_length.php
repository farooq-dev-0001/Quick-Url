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
        Schema::table('urls', function (Blueprint $table) {
            // Increase short_code length to accommodate prefixes (20 chars prefix + 1 dash + 6 random = 27 chars max)
            $table->string('short_code', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            // Revert back to original length
            $table->string('short_code', 10)->change();
        });
    }
};
