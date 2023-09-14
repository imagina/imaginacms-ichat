<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewAttributesToMessagesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('ichat__messages', function (Blueprint $table) {
        $table->string('external_id', 100)->nullable()->after('reply_to_id');
        $table->integer('status')->default(1)->unsigned()->after('reply_to_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('ichat__messages', function (Blueprint $table) {
      $table->dropColumn('external_id');
    });
  }
}
