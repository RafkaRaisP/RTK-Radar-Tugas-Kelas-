<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RadarTest extends TestCase
{
    use RefreshDatabase;

    public function test_radar_page_loads(): void
    {
        $this->get('/radar')->assertStatus(200);
    }

    public function test_radar_shows_tasks(): void
    {
        $user = User::factory()->create();
        $task = \App\Models\Task::create([
            'user_id' => $user->id,
            'class_name' => 'XI RPL 1',
            'title' => 'Test Task',
            'subject' => 'Matematika',
            'deadline' => now()->addDays(3),
        ]);

        $this->get('/radar')->assertSee('Test Task');
    }

    public function test_radar_filters_by_class(): void
    {
        $this->get('/radar?class_name=XI+RPL+1')->assertStatus(200);
    }
}
