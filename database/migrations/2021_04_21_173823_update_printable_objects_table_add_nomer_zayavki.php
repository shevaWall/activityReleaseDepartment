<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePrintableObjectsTableAddNomerZayavki extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printable_objects', function (Blueprint $table) {
            $table->unsignedInteger('nomerZayavki')->after('cipher')->nullable();
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
            $table->dropColumn('nomerZayavki');
        });
    }
}
