<?php

namespace Database\Seeders;

use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestRecruitmentSeeder extends Seeder
{
    public function run(): void
    {
        $userId = DB::table('users')->value('id');
        if (! $userId) {
            $userId = DB::table('users')->insertGetId(['id' => 1, 'name' => 'Demo User', 'email' => 'demo@example.com']);
        }

        $profileId = DB::table('job_profiles')->value('id');
        if (! $profileId) {
            $profileId = DB::table('job_profiles')->insertGetId(['name' => 'Demo Profile', 'division_id' => 1, 'status' => 'Active']);
        }

        $r = Recruitment::create([
            'user_id' => $userId,
            'profile_id' => $profileId,
            'approver' => 'boss@example.com',
            'city' => 'Bogotá',
            'qty' => 3,
            'contract' => 'Remote',
            'work_mode' => 'Hybrid',
            'cause' => 'Extra Worker',
            'srange' => '3M - 5M',
            'others' => "- Laravel\n- Vue.js\n- Docker",
            'resources' => [['name' => 'Laptop PC i7'], ['name' => 'VPN']],
            'status' => 'approval',
            'complexity' => 15,
            'created_at' => now(),
        ]);

        DB::table('recruitment_candidates')->insert([
            ['recruitment_id' => $r->id, 'name' => 'Camilo V', 'email' => 'camilo@test.com', 'status' => 'interviewing', 'concept' => 'Good tech stack.'],
            ['recruitment_id' => $r->id, 'name' => 'Sara D', 'email' => 'sara@test.com', 'status' => 'hired', 'concept' => 'Accepted offer. Start date pending.'],
        ]);
    }
}
