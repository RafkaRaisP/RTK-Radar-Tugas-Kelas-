<?php
// Check all ketua/sekre users' class_name
$users = \App\Models\User::where('role', '!=', 'siswa')->where('role', '!=', 'admin')->get();
foreach ($users as $u) {
    echo "id={$u->id} name={$u->name} role={$u->role} class_name=" . var_export($u->class_name, true) . PHP_EOL;
}

echo PHP_EOL . "CLASS_OPTIONS: " . implode(', ', \App\Models\User::CLASS_OPTIONS) . PHP_EOL;

// Simulate exact store() flow for ketua_rpl1
auth()->loginUsingId(6); // ketua_rpl1
$user = auth()->user();
echo PHP_EOL . "Logged in as: {$user->name} role={$user->role} class_name={$user->class_name}" . PHP_EOL;

// Try creating a task exactly as the controller would
$validated = [
    'title' => 'Tugas Simulasi Browser',
    'subject' => 'Pemrograman Web',
    'deadline' => '2026-08-24T10:00:00+07:00',
    'description' => null,
];

echo "deadline parsed: " . \Carbon\Carbon::parse($validated['deadline'])->toIso8601String() . PHP_EOL;
echo "now: " . \Carbon\Carbon::now()->toIso8601String() . PHP_EOL;
echo "deadline >= now: " . (\Carbon\Carbon::parse($validated['deadline'])->gte(\Carbon\Carbon::now()) ? 'YES' : 'NO') . PHP_EOL;

$task = \App\Models\Task::create([
    'user_id' => $user->id,
    'class_name' => $user->class_name,
    ...$validated,
]);
echo "Created task id={$task->id}" . PHP_EOL;
