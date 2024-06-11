<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('ichat__conversations', function (Blueprint $table) {
      $table->string('provider_type')->nullable()->after('entity_id');
      $table->string('provider_id')->nullable()->after('entity_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('ichat__conversations', function (Blueprint $table) {
      $table->dropColumn('provider_id');
      $table->dropColumn('provider_type');
    });
  }
};
