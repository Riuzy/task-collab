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
    Schema::table('tasks', function (Blueprint $table) {
        // Kita hapus foreign key dan kolomnya karena sudah pindah ke tabel pivot
        $table->dropForeign(['user_id']); 
        $table->dropColumn('user_id');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->constrained();
    });
}
};