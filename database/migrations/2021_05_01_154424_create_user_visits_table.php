<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_visits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('identity', ['id_card', 'drive_lisense', 'passport', 'student_card']);
            $table->string('identity_number');
            $table->boolean('overseas');
            $table->string('country')->nullable();
            $table->string('province');
            $table->string('district');
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('user_visits');
    }
}
