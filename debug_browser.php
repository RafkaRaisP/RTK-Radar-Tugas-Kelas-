<?php
// Simulate EXACT browser form POST as ketua_rpl1 (user_id=6)
$client = new \GuzzleHttp\Client(['base_uri' => 'http://radar-tugas-kelas.test', 'allow_redirects' => false, 'cookies' => true]);

// Login
$loginPage = $client->get('/login');
$body = (string) $loginPage->getBody();
preg_match('/name="_token"\s+value="([^"]+)"/', $body, $m);
$csrf = $m[1];

$loginResp = $client->post('/login', ['form_params' => [
    '_token' => $csrf, 'username' => 'ketua_rpl1', 'password' => '77ce0684',
], 'headers' => ['Referer' => 'http://radar-tugas-kelas.test/login']]);

echo "Login: " . $loginResp->getStatusCode() . " → " . ($loginResp->getHeader('Location')[0] ?? 'none') . PHP_EOL;

// Get dashboard to grab CSRF + session
$dash = $client->get('/dashboard');
$dashBody = (string) $dash->getBody();
preg_match('/name="_token"\s+value="([^"]+)"/', $dashBody, $m2);
$csrf2 = $m2[1];

echo "Dashboard: " . $dash->getStatusCode() . " len=" . strlen($dashBody) . PHP_EOL;

// Check what error banner exists
if (strpos($dashBody, 'Terjadi kesalahan') !== false) {
    echo "*** VALIDATION ERROR BANNER FOUND IN DASHBOARD ***" . PHP_EOL;
    preg_match('/Terjadi kesalahan.*?<\/div>/s', $dashBody, $errMatch);
    echo strip_tags($errMatch[0] ?? '') . PHP_EOL;
}

// Check empty state
if (strpos($dashBody, 'Belum ada catatan') !== false) {
    echo "Tasks list is EMPTY (showing empty state)" . PHP_EOL;
}

// Check success
if (strpos($dashBody, 'Tugas berhasil') !== false) {
    echo "SUCCESS MESSAGE FOUND" . PHP_EOL;
}

// NOW: submit task exactly like browser would (with localToISO output)
$deadlineBrowser = date('Y-m-d\TH:i', strtotime('+3 days')); // What datetime-local shows
echo "Browser datetime-local value: $deadlineBrowser" . PHP_EOL;

// Simulate localToISO conversion (adds seconds + timezone offset +07:00 for WIB)
$deadlineISO = $deadlineBrowser . ':00+07:00';
echo "After localToISO: $deadlineISO" . PHP_EOL;

$createResp = $client->post('/tasks', [
    'form_params' => [
        '_token' => $csrf2,
        'title' => 'Tugas Dari Browser Sim',
        'subject' => 'Pemrograman Web',
        'deadline' => $deadlineISO,
        'description' => '',
    ],
    'headers' => ['Referer' => 'http://radar-tugas-kelas.test/dashboard'],
]);

echo "POST /tasks: " . $createResp->getStatusCode();
if ($createResp->hasHeader('Location')) echo " → " . $createResp->getHeader('Location')[0];
echo PHP_EOL;

// Check redirect response for error/flash
if ($createResp->getStatusCode() === 302) {
    $dash2 = $client->get('/dashboard');
    $dash2Body = (string) $dash2->getBody();
    if (strpos($dash2Body, 'Tugas Dari Browser Sim') !== false) {
        echo "SUCCESS: Task visible on dashboard!" . PHP_EOL;
    } else {
        echo "FAIL: Task NOT on dashboard after redirect" . PHP_EOL;
        if (strpos($dash2Body, 'Terjadi kesalahan') !== false) {
            preg_match('/Terjadi kesalahan.*?<\/div>/s', $dash2Body, $errMatch);
            echo "VALIDATION ERRORS: " . strip_tags($errMatch[0] ?? '') . PHP_EOL;
        }
        if (strpos($dash2Body, 'Belum ada catatan') !== false) {
            echo "Still showing empty state" . PHP_EOL;
        }
        // Count tasks
        preg_match_all('/task-card-item/', $dash2Body, $cards);
        echo "Task cards found: " . count($cards[0]) . PHP_EOL;
    }
}
