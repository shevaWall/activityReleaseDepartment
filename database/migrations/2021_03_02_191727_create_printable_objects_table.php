<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintableObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printable_objects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cipher')->nullable();
            $table->string('scan_img')->nullable();
            $table->string('object_owner')->nullable();
            $table->unsignedInteger('status_id')->default('1');
            $table->unsignedInteger('count_pd')->nullable();
            $table->unsignedInteger('count_rd')->nullable();
            $table->unsignedInteger('count_ii')->nullable();
            $table->boolean('original_documents')->nullable()->default(false);
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
        Schema::dropIfExists('printable_objects');
    }
}
