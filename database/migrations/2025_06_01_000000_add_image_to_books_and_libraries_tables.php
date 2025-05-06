<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToBooksAndLibrariesTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('image')->nullable()->after('published_at');
        });

        Schema::table('libraries', function (Blueprint $table) {
            $table->string('image')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('libraries', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
