<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Department;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //1. Create Departments

        $departments = collect(['Engineering', 'Human Resources', 'Marketing', 'Product Strategy'])->map(function ($deptName) {
            return Department::create([
                'name' => $deptName,
                'slug' => Str::slug($deptName),
                'description' => "Official channel hub for {$deptName}",
            ]);
        });

        //Create Users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ameraldchat.app',
        ]);

        $users = User::factory(10)->create();
        $allUsers = $users->push($admin);

        //Assign users across departments.
        $allUsers->each(function ($user) use ($departments) {
            $user->update(['department_id' => $departments->random()->id]);
        });

        // 3. Create Default & Custom Channels per Department
        $departments->each(function ($dept) use ($allUsers) {
            // Public department announcement channel
            $generalChannel = Channel::create([
                'department_id' => $dept->id,
                'name' => "{$dept->slug}-general",
                'slug' => "{$dept->slug}-general",
                'type' => 'public',
                'description' => "General discussion for {$dept->name}",
            ]);

            // Private team channel
            $privateChannel = Channel::create([
                'department_id' => $dept->id,
                'name' => "{$dept->slug}-leads",
                'slug' => "{$dept->slug}-leads",
                'type' => 'private',
                'description' => "Leadership channel for {$dept->name}",
            ]);
            // Attach users to channels via pivot table
            $allUsers->each(function ($user) use ($generalChannel, $privateChannel) {
                $generalChannel->users()->attach($user->id, ['role' => 'member']);
                if (rand(0, 1)) {
                    $privateChannel->users()->attach($user->id, ['role' => 'member']);
                }
            });

            // 4. Populate Root Messages and Thread Replies
            $rootMessages = Message::factory(5)->create([
                'channel_id' => $generalChannel->id,
                'user_id' => $allUsers->random()->id,
            ]);

            $rootMessages->each(function ($parentMessage) use ($generalChannel, $allUsers) {
                // Create 2-4 thread replies per root message
                Message::factory(rand(2, 4))->create([
                    'channel_id' => $generalChannel->id,
                    'user_id' => $allUsers->random()->id,
                    'parent_id' => $parentMessage->id,
                ]);
            });
        });
    }
}
