<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('notifications', function (Blueprint $table) {

    if (!Schema::hasColumn('notifications', 'spot_id')) {

        $table->unsignedBigInteger('spot_id')->nullable();

    }

    if (!Schema::hasColumn('notifications', 'comment_id')) {

        $table->unsignedBigInteger('comment_id')->nullable();

    }

});
}

public function down()
{
    Schema::table('notifications', function ($table) {

        $table->dropColumn('spot_id');

        $table->dropColumn('comment_id');

    });
}
};
