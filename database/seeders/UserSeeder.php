<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('🔐 Seeding Admin Users...');

        // Create Super Admin User
        $superAdmin = $this->createUser([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');
        $this->command->info('✓ Super Admin created: super@admin.com');

        // Create Admin User
        $admin = $this->createUser([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $this->command->info('✓ Admin created: admin@admin.com');

        // Create Editor User
        $editor = $this->createUser([
            'name' => 'Editor User',
            'email' => 'editor@admin.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $editor->assignRole('editor');
        $this->command->info('✓ Editor created: editor@admin.com');

        // Create Author User
        $author = $this->createUser([
            'name' => 'Author User',
            'email' => 'author@admin.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $author->assignRole('author');
        $this->command->info('✓ Author created: author@admin.com');

        // Create Viewer User
        $viewer = $this->createUser([
            'name' => 'Viewer User',
            'email' => 'viewer@admin.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');
        $this->command->info('✓ Viewer created: viewer@admin.com');

        // Create additional demo users (optional)
        if (app()->environment('local', 'development')) {
            for ($i = 1; $i <= 5; $i++) {
                $user = $this->createUser([
                    'name' => "Demo User $i",
                    'email' => "demo$i@example.com",
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('viewer');
            }
            $this->command->info('✓ Demo users created (demo1@example.com - demo5@example.com)');
        }

        $this->command->info('🎉 All admin users created successfully!');
        $this->command->line('');
        $this->command->warn('Default password for all users: password123');
        $this->command->warn('Please change passwords in production!');
    }

    /**
     * Create user with error handling
     *
     * @param array $userData
     * @return User
     */
    private function createUser(array $userData): User
    {
        // Check if user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        
        if ($existingUser) {
            $this->command->warn("⚠ User {$userData['email']} already exists, skipping...");
            return $existingUser;
        }

        return User::create($userData);
    }
}