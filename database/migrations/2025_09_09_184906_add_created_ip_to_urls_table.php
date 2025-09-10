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
            $table->string('created_ip', 45)->nullable()->after('user_id'); // IPv6 support
            $table->index(['created_ip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('urls', function (Blueprint $table) {
            $table->dropIndex(['created_ip']);
            $table->dropColumn('created_ip');
        });
    }
};
