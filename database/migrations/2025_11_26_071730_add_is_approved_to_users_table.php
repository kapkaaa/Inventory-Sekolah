<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }
        });

        // Approve all existing users to avoid breaking existing functionality
        // Use 'user' as default role if role column doesn't exist for some records
        User::whereNull('role')->update(['role' => 'user']);
        User::where('role', 'user')->update(['is_approved' => true]);
        User::whereNull('is_approved')->update(['is_approved' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_approved']);
        });
    }
};