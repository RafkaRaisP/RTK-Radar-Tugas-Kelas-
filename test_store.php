<?php
// Test 1: Can we create a task?
$user = \App\Models\User::where('username', 'ketua_rpl1')->first();
echo "User: {$user->username} (id={$user->id}, role={$user->role}, class={$user->class_name})" . PHP_EOL;

$deadline = \Carbon\Carbon::parse('2026-08-25T10:00:00+07:00')->utc();
echo "Deadline (UTC): " . $deadline->toIso8601String() . PHP_EOL;
echo "Is future? " . ($deadline->isFuture() ? 'YES' : 'NO') . PHP_EOL;

$task = \App\Models\Task::create([
    'user_id' => $user->id,
    'class_name' => $user->class_name,
    'title' => 'Test Task dari Seeder',
    'subject' => 'Matematika',
    'deadline' => '2026-08-25T10:00:00+07:00',
    'description' => 'Ini tugas test',
]);
echo "Task created! id={$task->id}" . PHP_EOL;

// Test 2: Verify it saved
$check = \App\Models\Task::find($task->id);
echo "Task in DB: {$check->title} | deadline={$check->deadline->toIso8601String()}" . PHP_EOL;

// Test 3: Can we query it?
$count = \App\Models\Task::where('class_name', 'XI RPL 1')->count();
echo "Tasks for XI RPL 1: {$count}" . PHP_EOL;
