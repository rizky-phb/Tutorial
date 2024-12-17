<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('tugas_id')->nullable()->after('id');
            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['tugas_id']);
            $table->dropColumn('tugas_id');
        });
    }
};
