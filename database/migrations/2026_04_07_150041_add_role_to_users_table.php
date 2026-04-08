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
            $table->enum('role', ['student', 'instructor', 'admin'])->default('student')->after('email'); // this adds a new 'role' column to the 'users' table, which is an ENUM type that can only have the values 'student', 'instructor', or 'admin'. The default value is set to 'student', meaning that if no role is specified when a user is created, they will be assigned the 'student' role by default. The column is added after the 'email' column for better organization of the database schema.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role'); // this removes the 'role' column from the 'users' table, effectively reversing the changes made in the up() method. This allows you to roll back the migration if needed, ensuring that your database schema can be returned to its previous state without the 'role' column.
        });
    }
};
