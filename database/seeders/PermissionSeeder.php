<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Permissions for Users
        $userPermissions = [
            'users.index', 'users.create', 'users.edit', 'users.delete', 'users.show'
        ];

        // Permissions for Posts
        $postPermissions = [
            'posts.index', 'posts.create', 'posts.edit', 'posts.delete', 'posts.show', 'posts.publish'
        ];

        // Permissions for Categories
        $categoryPermissions = [
            'categories.index', 'categories.create', 'categories.edit', 'categories.delete'
        ];

        // Permissions for Comments
        $commentPermissions = [
            'comments.index', 'comments.approve', 'comments.reject', 'comments.delete'
        ];

        // Permissions for Media
        $mediaPermissions = [
            'media.index', 'media.upload', 'media.delete'
        ];

        // Dashboard permission
        $dashboardPermissions = ['dashboard.access'];

        // Create all permissions
        $allPermissions = array_merge(
            $userPermissions, $postPermissions, $categoryPermissions, 
            $commentPermissions, $mediaPermissions, $dashboardPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $author = Role::firstOrCreate(['name' => 'author']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());
        
        $adminPermissions = array_merge(
            $postPermissions, $categoryPermissions, $commentPermissions, 
            $mediaPermissions, $dashboardPermissions
        );
        $admin->givePermissionTo($adminPermissions);
        
        $editorPermissions = [
            'posts.index', 'posts.edit', 'posts.show', 
            'comments.index', 'comments.approve', 'comments.reject',
            'dashboard.access'
        ];
        $editor->givePermissionTo($editorPermissions);
        
        $authorPermissions = [
            'posts.index', 'posts.create', 'posts.edit', 'posts.show',
            'media.index', 'media.upload', 'dashboard.access'
        ];
        $author->givePermissionTo($authorPermissions);

        // Create Super Admin User
        $superAdminUser = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'status' => 'active'
        ]);

        $superAdminUser->assignRole('super-admin');
    }
}
