<?php
// Test POST /tasks with proper session handling
$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://Radar-Tugas-Kelas.test',
    'allow_redirects' => false,
    'cookies' => true,
    'http_errors' => false,
]);

// 1. GET /login
$loginPage = $client->get('/login');
$body = (string) $loginPage->getBody();
preg_match('/name="_token"\s+value="([^"]+)"/', $body, $m);
$csrf = $m[1] ?? '';
echo "CSRF token: " . substr($csrf, 0, 20) . "..." . PHP_EOL;

// 2. POST /login
$loginResp = $client->post('/login', [
    'form_params' => ['_token' => $csrf, 'username' => 'ketua_rpl1', 'password' => '77ce0684'],
    'headers' => ['Referer' => 'http://Radar-Tugas-Kelas.test/login'],
]);
echo "Login: " . $loginResp->getStatusCode() . PHP_EOL;

// 3. GET /dashboard
$dash = $client->get('/dashboard');
echo "Dashboard: " . $dash->getStatusCode() . " len=" . strlen((string)$dash->getBody()) . PHP_EOL;
$dashBody = (string)$dash->getBody();
preg_match('/name="_token"\s+value="([^"]+)"/', $dashBody, $m2);
$csrf2 = $m2[1] ?? '';
echo "Dashboard CSRF: " . substr($csrf2, 0, 20) . "..." . PHP_EOL;

// 4. POST /tasks  
$deadlineISO = date('Y-m-d\TH:i:s', strtotime('+5 days')) . '+07:00';
echo "Deadline: $deadlineISO" . PHP_EOL;

$createResp = $client->post('/tasks', [
    'form_params' => [
        '_token' => $csrf2,
        'title' => 'Tugas Test Debug',
        'subject' => 'Bahasa Indonesia',
        'deadline' => $deadlineISO,
        'description' => 'test',
    ],
    'headers' => ['Referer' => 'http://Radar-Tugas-Kelas.test/dashboard'],
]);
echo "POST /tasks: " . $createResp->getStatusCode() . PHP_EOL;
if ($createResp->hasHeader('Location')) {
    echo "Redirect: " . $createResp->getHeader('Location')[0] . PHP_EOL;
}
echo "Body (first 200): " . substr((string)$createResp->getBody(), 0, 200) . PHP_EOL;

// 5. Check DB
echo "Tasks in DB: " . \App\Models\Task::count() . PHP_EOL;
