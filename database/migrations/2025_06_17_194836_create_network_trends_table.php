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
        Schema::create('network_trends', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->string('interface');
            $table->unsignedBigInteger('clock');
            $table->timestamp('timestamp')->nullable();
            $table->double('in_avg');
            $table->double('out_avg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_trends');
    }
};
