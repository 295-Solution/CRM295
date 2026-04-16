<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');
            $table->dropColumn('nomor_penawaran');
            $table->dropColumn('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('nomor_penawaran')->nullable();
            $table->text('keterangan')->nullable();
        });
    }
};
