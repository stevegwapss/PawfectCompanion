<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAdoptionApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });
    }

    public function down()
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}