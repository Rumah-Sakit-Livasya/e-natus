<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProjectRequest;
use Illuminate\Support\Facades\Log;

echo "Starting stock deduction fix...\n";

// Get all fully approved projects that haven't had stock deducted
$projects = ProjectRequest::where(function($query) {
        $query->where('approval_level_1_status', 'approved')
              ->where('approval_level_2_status', 'approved');
    })
    ->where('bmhp_stock_deducted', false)
    ->with('projectBmhp.bmhp')
    ->get();
    
echo "Found {$projects->count()} projects to process\n";

foreach ($projects as $project) {
    try {
        $project->deductBmhpStock();
        echo "✓ Processed project: {$project->name} (ID: {$project->id})\n";
        
        Log::info('Stock deduction fixed via script', [
            'project_id' => $project->id,
            'project_name' => $project->name
        ]);
    } catch (\Exception $e) {
        echo "✗ Failed to process project {$project->id}: {$e->getMessage()}\n";
        Log::error('Stock deduction fix failed', [
            'project_id' => $project->id,
            'error' => $e->getMessage()
        ]);
    }
}

echo "Stock deduction fix completed!\n";
