<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKapiAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kapi_app', function (Blueprint $table) {
            $table->increments('id');
            $table->string('guard')->nullable();
            $table->integer('user')->nullable();
            $table->string('app_type')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('approve')->default(false);
            $table->boolean('block')->default(false);
            $table->string('key')->nullable();
            $table->string('secret')->nullable();
            $table->string('osecret')->nullable();
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('uri')->nullable();
            $table->string('redirect_uri')->nullable();
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
        Schema::dropIfExists('api_app');
    }
}
