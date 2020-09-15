<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privacies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('profile_id')->nullable();
            $table->foreign('profile_id')->references('id')->on('profiles');
            // $table->unique(['privacy_resource_type', 'privacy_resource_id', 'profile_id']);
            // $table->primary(['privacy_resource_type', 'privacy_resource_id', 'name']);
            $table->morphs('privacy_resource');
            $table->string('name');
            $table->string('key');
            $table->unsignedInteger('privacy_id')->nullable();
            $table->foreign('privacy_id')->references('id')->on('privacy_values');
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
        Schema::dropIfExists('privacies');
    }
}
