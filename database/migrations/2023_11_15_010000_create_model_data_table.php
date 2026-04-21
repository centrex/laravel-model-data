<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create('model_datas', function (Blueprint $table) {
            $table->string('key', 32)->primary();
            $table->string('data_type')->default('data');
            $table->morphs('model');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id', 'data_type']);
        });        
    }

    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('model_datas');
    }
};
