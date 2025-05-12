<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Absensi Online');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->time('check_in_start')->default('07:00:00');
            $table->time('check_out_start')->default('16:00:00');
            $table->string('whitelisted_ips')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
