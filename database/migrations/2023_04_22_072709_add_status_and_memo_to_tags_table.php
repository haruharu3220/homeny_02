<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            //
            $table->enum('status', ['未決定', '検討中', '決定'])->default('未決定')->after('name'); // statusカラムを追加、デフォルト値は'未決定'
            $table->text('memo')->after('status')->nullable(); // memoカラムを追加、初期値はNULL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('status'); // statusカラムを削除
            $table->dropColumn('memo'); // memoカラムを削除
        });
    }
};
