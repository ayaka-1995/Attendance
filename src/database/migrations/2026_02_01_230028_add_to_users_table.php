<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin_status')->default(false);//そのユーザーが管理者かどうか、
                                                           //falseがデフォルト→一般ユーザーとして登録される
            $table->string('attendance_status')->default('勤務外');//今このユーザーが勤務中かどうかの"現在状態"
        });//新規登録した瞬間は「まだ勤務していない」
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
        });
    }
}
