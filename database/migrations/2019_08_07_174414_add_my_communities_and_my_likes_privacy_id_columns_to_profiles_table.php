<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMyCommunitiesAndMyLikesPrivacyIdColumnsToProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->unsignedInteger('my_communities_privacy_id')->nullable();
            $table->foreign('my_communities_privacy_id')->references('id')->on('r_privacies_values');

            $table->unsignedInteger('my_likes_privacy_id')->nullable();
            $table->foreign('my_likes_privacy_id')->references('id')->on('r_privacies_values');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('my_communities_privacy_id');
            $table->dropColumn('my_likes_privacy_id');
        });
    }
}
