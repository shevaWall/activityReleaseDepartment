<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCompositsTableAddOrderIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('composits', function (Blueprint $table) {
            $table->unsignedInteger('order_index')->after('completed')->nullable();
        });
        $all_composits = \App\Models\Composit::all();
        foreach($all_composits as $k=> $composit){
            $composit->order_index = $composit->id;
            $composit->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('composits', function (Blueprint $table) {
            $table->dropColumn('order_index');
        });
    }
}
