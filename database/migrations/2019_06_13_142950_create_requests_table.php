<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->primary(['profile_id', 'requests_id', 'requests_type']);
            $table->unsignedInteger('profile_id');
            $table->unsignedInteger('requests_id');
            $table->unsignedInteger('sender_id')->nullable();;
            $table->string('requests_type');
            $table->string('response')->nullable();;
            $table->string('message')->nullable();;
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
        Schema::dropIfExists('requests');
    }
}
