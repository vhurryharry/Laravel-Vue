<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationshipPrivaciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_privacies', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedInteger('profile_id')->nullable();
            // $table->foreign('profile_id')->references('id')->on('profiles');
            // $table->morphs('r_privacy_resource');
            $table->string('name');
            $table->string('key');
            // $table->unsignedInteger('my_events_privacy_id')->nullable();
            // $table->foreign('my_events_privacy_id')->references('my_events_privacy_id')->on('profiles');
            // $table->unsignedInteger('my_communities_privacy_id')->nullable();
            // $table->foreign('my_communities_privacy_id')->references('my_communities_privacy_id')->on('profiles');
            // $table->unsignedInteger('my_friends_privacy_id')->nullable();
            // $table->foreign('my_friends_privacy_id')->references('my_friends_privacy_id')->on('profiles');
            // $table->unsignedInteger('my_likes_privacy_id')->nullable();
            // $table->foreign('my_likes_privacy_id')->references('my_likes_privacy_id')->on('profiles');
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
        Schema::dropIfExists('relationship_privacies');
    }
}
