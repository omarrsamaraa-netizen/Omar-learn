<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fill the roles table, point every user at a role, and retire the old
     * role string column so role_id is the only place a role is recorded.
     */
    public function up(): void
    {
        /**
         * create_roles_table ran before its name column was written into the file,
         * so older databases have a roles table without one.
         */
        if (! Schema::hasColumn('roles', 'name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('name')->unique();
            });
        }

        $roleIds = collect([Role::ADMIN, Role::CUSTOMER])
            ->mapWithKeys(fn (string $name): array => [
                $name => DB::table('roles')->where('name', $name)->value('id')
                    ?? DB::table('roles')->insertGetId([
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]),
            ]);

        if (Schema::hasColumn('users', 'role')) {
            foreach (DB::table('users')->whereNull('role_id')->get(['id', 'role']) as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role_id' => $roleIds[strtolower((string) $user->role)] ?? $roleIds[Role::CUSTOMER]]);
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }

        DB::table('users')->whereNull('role_id')->update(['role_id' => $roleIds[Role::CUSTOMER]]);
    }

    /**
     * Put the role string column back, filled in from the linked role.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default(Role::CUSTOMER);
            });
        }

        foreach (DB::table('roles')->get(['id', 'name']) as $role) {
            DB::table('users')->where('role_id', $role->id)->update(['role' => $role->name]);
        }
    }
};
