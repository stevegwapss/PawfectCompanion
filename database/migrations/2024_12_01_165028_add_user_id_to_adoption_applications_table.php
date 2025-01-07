<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUserIdToAdoptionApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('adoption_applications', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
        });

        // Update existing records to set a default user ID
        DB::table('adoption_applications')->update(['user_id' => 1]);

        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}