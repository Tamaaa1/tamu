<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AgendaController;
use App\Models\Agenda;
use App\Models\MasterDinas;

// Include the Composer autoloader
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create test data
$dinas = MasterDinas::first();
$agenda = Agenda::first();

if (!$dinas || !$agenda) {
    echo "Error: No dinas or agenda data found. Please run the seeder first.\n";
    exit(1);
}

// Create a test request
$request = new Request([
    'nama' => 'Test Participant',
    'jabatan' => 'Test Position',
    'no_hp' => '08123456789',
    'dinas_id' => $dinas->dinas_id,
    'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
    'agenda_id' => $agenda->id
]);

// Test the controller method
$controller = new AgendaController();
$response = $controller->registerParticipant($request);

echo "Response: \n";
print_r($response->getData());
echo "\n";

// Check if data was saved
$participantCount = \App\Models\AgendaDetail::where('nama', 'Test Participant')->count();
echo "Participants with name 'Test Participant': " . $participantCount . "\n";
