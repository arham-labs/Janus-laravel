<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempOtp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_otp', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->integer('otp');
            $table->string('email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('service')->nullable();
            $table->dateTime('expire_at');
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
        Schema::dropIfExists('temp_otp');
    }
}
