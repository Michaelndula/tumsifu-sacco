<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavingsPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->nullable();
            $table->decimal('savings_amount', 28, 8)->default(0);
            $table->unsignedInteger('interval')->default(0);
            $table->unsignedInteger('times')->default(0);
            $table->decimal('giveable_amount', 28, 8)->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savings_plans');
    }
}
