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
        Schema::table('users', function (Blueprint $table) {
             $table->string('username')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('password');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
               $table->dropColumn(['username', 'phone', 'bio', 'avatar', 'status', 'last_login_at']);
        });
    }
};
