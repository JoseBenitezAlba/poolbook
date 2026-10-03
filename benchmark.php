<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Cita;
use App\Models\User;
use Carbon\Carbon;

// Insert dummy data if Cita table is empty
if (Cita::count() == 0) {
    for ($i = 0; $i < 50; $i++) {
        $user = User::create([
            'name' => 'User ' . $i,
            'email' => 'user' . $i . '@example.com',
            'password' => bcrypt('password'),
        ]);
        Cita::create([
            'title' => 'Test Cita ' . $i,
            'start' => Carbon::now(),
            'end' => Carbon::now()->addHour(),
            'user_id' => $user->id,
            'resource_id' => '1',
            'day_of_week' => 1,
            'date' => Carbon::now()->toDateString(),
        ]);
    }
}

DB::enableQueryLog();

$start = microtime(true);

// This matches the query in ReservasController@index after optimization
$citas = Cita::with('user')->get();

$count = 0;
foreach ($citas as $cita) {
    if ($cita->user) {
        $count++;
    }
}

$end = microtime(true);

$queries = DB::getQueryLog();

echo "Execution time: " . (($end - $start) * 1000) . " ms\n";
echo "Number of queries: " . count($queries) . "\n";
