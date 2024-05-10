<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FriendshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all()->take(6);

        Friendship::create([
            'user_id' => $users[1]->id,
            'friend_id' => $users[2]->id,
            'status' => 'accepted'
        ]);
        Friendship::create([
            'user_id' => $users[2]->id,
            'friend_id' => $users[3]->id,
            'status' => 'pending'
        ]);
        Friendship::create([
            'user_id' => $users[3]->id,
            'friend_id' => $users[4]->id,
            'status' => 'rejected'
        ]);
    }
}
