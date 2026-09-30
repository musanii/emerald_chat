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
        // 1. Create Departments
        $departments = collect(['Engineering', 'Human Resources', 'Marketing', 'Product Strategy'])->map(function ($deptName) {
            return Department::create([
                'name' => $deptName,
                'slug' => Str::slug($deptName),
                'description' => "Official channel hub for {$deptName}",
            ]);
        });

        // 2. Create Admin User explicitly attached to the first department
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ameraldchat.app',
            'department_id' => $departments->first()->id,
        ]);

        // 3. Create regular users and assign departments
        $users = User::factory(10)->create()->each(function ($user) use ($departments) {
            $user->update([
                'department_id' => $departments->random()->id,
            ]);
        });

        $allUsers = $users->push($admin);

        // 4. Create Default & Custom Channels per Department
        $departments->each(function ($dept) use ($allUsers) {
            // Get users belonging to this department
            $deptUsers = $allUsers->where('department_id', $dept->id);

            // Public channel
            $generalChannel = Channel::create([
                'department_id' => $dept->id,
                'name' => "{$dept->slug}-general",
                'slug' => "{$dept->slug}-general",
                'type' => 'public',
                'description' => "General discussion for {$dept->name}",
            ]);

            // Private channel
            $privateChannel = Channel::create([
                'department_id' => $dept->id,
                'name' => "{$dept->slug}-leads",
                'slug' => "{$dept->slug}-leads",
                'type' => 'private',
                'description' => "Leadership channel for {$dept->name}",
            ]);

            // Attach department members to public & private channels
            $deptUsers->each(function ($user) use ($generalChannel, $privateChannel) {
                $generalChannel->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
                $privateChannel->users()->syncWithoutDetaching([$user->id => ['role' => 'member']]);
            });

            // 5. Populate Root Messages and Thread Replies
            if ($deptUsers->isNotEmpty()) {
                $rootMessages = Message::factory(5)->create([
                    'channel_id' => $generalChannel->id,
                    'user_id' => $deptUsers->random()->id,
                ]);

                $rootMessages->each(function ($parentMessage) use ($generalChannel, $deptUsers) {
                    Message::factory(rand(2, 4))->create([
                        'channel_id' => $generalChannel->id,
                        'user_id' => $deptUsers->random()->id,
                        'parent_id' => $parentMessage->id,
                    ]);
                });
            }
        });

        // 6. Seed Direct Message (DM) Channels
        $otherUsers = $allUsers->where('id', '!=', $admin->id);

        foreach ($otherUsers->take(3) as $otherUser) {
            $dmChannel = Channel::create([
                'department_id' => $admin->department_id,
                'name' => $otherUser->name,
                'slug' => "dm-{$admin->id}-{$otherUser->id}",
                'type' => 'direct',
                'description' => "Direct message conversation",
            ]);

            $dmChannel->users()->attach([
                $admin->id => ['role' => 'member'],
                $otherUser->id => ['role' => 'member'],
            ]);
        }
    }
}