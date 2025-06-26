<?php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('paths');
            $table->dropColumn('events'); // Remove events from DB as they'll be stored in files
        });
    }

    public function down()
    {
        Schema::table('user_sessions', function (Blueprint $table) {
            $table->json('events')->nullable();
            $table->dropColumn('file_path');
        });
    }
};