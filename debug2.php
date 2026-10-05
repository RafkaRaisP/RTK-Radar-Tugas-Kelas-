<?php
// Verify password for ketua_rpl1
$user = \App\Models\User::where('username', 'ketua_rpl1')->first();
echo "User: " . ($user ? $user->name : 'NOT FOUND') . PHP_EOL;
if ($user) {
    echo "Hash: {$user->password}" . PHP_EOL;
    echo "Check '77ce0684': " . (\Hash::check('77ce0684', $user->password) ? 'PASS' : 'FAIL') . PHP_EOL;
}

// Check all users
$all = \App\Models\User::all();
echo PHP_EOL . "All users:" . PHP_EOL;
foreach ($all as $u) {
    echo "  {$u->username} ({$u->name}) role={$u->role} class={$u->class_name}" . PHP_EOL;
}
