<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->integer('current_number')->nullable();
            $table->integer('next_number')->nullable(); 
            $table->string('password')->nullable(); 
            $table->longText('image_path')->nullable(); 
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
