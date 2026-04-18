<?php

use App\Domain\HelpDesk\Data\TaskUpsertData;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Request::create('/helpdesk/tasks/upsert', 'POST', [
    'issue_id' => 7843,
    'complexity' => 'Low',
    'notes' => 'Test task',
]);

$data = TaskUpsertData::validateAndCreate($request->all());

echo "DTO ID: " . ($data->id ?? 'NULL') . PHP_EOL;
echo "DTO ISSUE_ID: " . ($data->issue_id) . PHP_EOL;
