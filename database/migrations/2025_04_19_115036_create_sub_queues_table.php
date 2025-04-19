<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sub_queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->integer('queue_number');
            $table->integer('current_number')->default(1);
            $table->integer('next_number')->default(2);
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('queues')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_queues');
    }
};
