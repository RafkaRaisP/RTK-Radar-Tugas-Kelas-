<?php
// Check the actual rendered form action in the dashboard
$client = new \GuzzleHttp\Client([
    'base_uri' => 'http://Radar-Tugas-Kelas.test',
    'allow_redirects' => false,
    'cookies' => true,
    'http_errors' => false,
]);

// Login
$loginPage = $client->get('/login');
$body = (string) $loginPage->getBody();
preg_match('/name="_token"\s+value="([^"]+)"/', $body, $m);
$client->post('/login', [
    'form_params' => ['_token' => $m[1], 'username' => 'ketua_rpl1', 'password' => '77ce0684'],
    'headers' => ['Referer' => 'http://Radar-Tugas-Kelas.test/login'],
]);

// Get dashboard
$dash = (string) $client->get('/dashboard')->getBody();

// Extract the add task form
preg_match('/<form[^>]*id="addTaskForm"[^>]*>(.*?)<\/form>/s', $dash, $formMatch);
if ($formMatch) {
    echo "Form found!" . PHP_EOL;
    // Extract action
    preg_match('/action="([^"]+)"/', $formMatch[0], $actionMatch);
    echo "Action: " . ($actionMatch[1] ?? 'NOT FOUND') . PHP_EOL;
    // Extract method
    preg_match('/method="([^"]+)"/', $formMatch[0], $methodMatch);
    echo "Method: " . ($methodMatch[1] ?? 'NOT FOUND') . PHP_EOL;
    // Check for CSRF token
    preg_match('/name="_token"\s+value="([^"]+)"/', $formMatch[0], $tokenMatch);
    echo "CSRF token: " . ($tokenMatch ? substr($tokenMatch[1], 0, 20) . '...' : 'NOT FOUND') . PHP_EOL;
    // Check for deadline input
    echo "Has deadline input: " . (strpos($formMatch[0], 'name="deadline"') !== false ? 'YES' : 'NO') . PHP_EOL;
    echo "Has title input: " . (strpos($formMatch[0], 'name="title"') !== false ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo "Form NOT found in HTML!" . PHP_EOL;
    // Check if addTaskForm id exists anywhere
    echo "addTaskForm in page: " . (strpos($dash, 'addTaskForm') !== false ? 'YES' : 'NO') . PHP_EOL;
    // Check if modal exists
    echo "addModal in page: " . (strpos($dash, 'addModal') !== false ? 'YES' : 'NO') . PHP_EOL;
    // Check for role
    preg_match('/role="([^"]+)"/', $dash, $roleMatch);
    echo "User role found: " . ($roleMatch[1] ?? 'none') . PHP_EOL;
    // Show snippet around tasks.store
    $pos = strpos($dash, 'tasks.store');
    if ($pos !== false) {
        echo "tasks.store context: ..." . substr($dash, $pos - 50, 150) . "..." . PHP_EOL;
    }
}
