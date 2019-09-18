<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentTreeMoneyToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->bigInteger('parent_id')->nullable()->default(0)->after('remember_token');
            $table->string('tree')->nullable()->after('parent_id');
            $table->decimal('money',10,2)->nullable()->default(0,00)->after('tree');
            $table->string('avatar')->nullable()->after('money');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('parent_id');
            $table->dropColumn('tree');
            $table->dropColumn('money');
            $table->dropColumn('avatar');
        });
    }
}
