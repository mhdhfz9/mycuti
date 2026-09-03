<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The plain `ic_number` column is replaced with an encrypted value
        // (via the model's `encrypted` cast) plus a deterministic HMAC hash
        // used to look users up at login, since the ciphertext itself can't
        // be queried with `WHERE ic_number = ?`. Dropping and re-adding
        // (rather than `->change()`) avoids a doctrine/dbal dependency and
        // is safe here since this is still pre-launch, seed-only data.
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['ic_number']);
            $table->dropColumn('ic_number');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->text('ic_number')->after('email');
            $table->string('ic_number_hash', 64)->unique()->after('ic_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['ic_number_hash']);
            $table->dropColumn(['ic_number', 'ic_number_hash']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('ic_number', 12)->unique()->after('email');
        });
    }
};
