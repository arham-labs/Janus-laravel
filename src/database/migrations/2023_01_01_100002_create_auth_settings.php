<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('model_name')->default('User')->comment('model name');
            $table->bigInteger('model_id');
            $table->string('user_type')->default('app_user')->comment('app_user for device users, web_user for website users,admin for web admin users');
            $table->integer('user_status')->default(0)->comment('0 for pending , 1 for active,2 for block,3 for delete');
            $table->timestamp('registration_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_logout_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_settings');
    }
}
