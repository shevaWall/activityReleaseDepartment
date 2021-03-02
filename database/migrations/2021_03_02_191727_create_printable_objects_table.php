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
            $table->unsignedInteger('status_id');
            $table->unsignedInteger('count_pd');
            $table->unsignedInteger('count_rd');
            $table->unsignedInteger('count_ii');
            $table->boolean('original_documents');
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
