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
    Schema::table('appointments', function (Blueprint $table) {
        $table->unique('adoption_application_id');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('appointments', function (Blueprint $table) {
        $table->dropUnique(['adoption_application_id']);
    });
}
};
