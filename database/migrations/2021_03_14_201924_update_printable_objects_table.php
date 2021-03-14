<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePrintableObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printable_objects', function (Blueprint $table) {
//            $table->string('mobile_number',20)->nullable();
            $table->date('deadline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('printable_objects', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
}
