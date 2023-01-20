<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('first_name')->nullable();
            $table->dropColumn('last_name')->nullable();
            $table->dropColumn('mobile')->nullable();
            $table->dropColumn('username')->nullable();
        });
    }
}
