<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->primary(['profile_id', 'invitations_id', 'invitations_type']);
            $table->unsignedInteger('profile_id');
            $table->unsignedInteger('invitations_id');
            $table->unsignedInteger('sender_id')->nullable();
            $table->string('invitations_type');
            $table->string('response')->nullable();
            $table->string('message')->nullable();
            $table->date('date_sent')->nullable();
            $table->date('date_accepted')->nullable();
            $table->date('date_refused')->nullable();
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
        Schema::dropIfExists('invitations');
    }
}
