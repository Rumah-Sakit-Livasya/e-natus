<?php

namespace App\Console\Commands;

use App\Models\ProjectRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixDeductedStock extends Command
{
    protected $signature = 'stock:fix-deduction';
    protected $description = 'Fix stock deduction for approved projects that were not processed';

    public function handle()
    {
        $this->info('Starting stock deduction fix...');
        
        // Get all fully approved projects that haven't had stock deducted
        $projects = ProjectRequest::where(function($query) {
                $query->where('approval_level_1_status', 'approved')
                      ->where('approval_level_2_status', 'approved');
            })
            ->where('bmhp_stock_deducted', false)
            ->with('projectBmhp.bmhp')
            ->get();
            
        $this->info("Found {$projects->count()} projects to process");
        
        foreach ($projects as $project) {
            try {
                $project->deductBmhpStock();
                $this->info("✓ Processed project: {$project->name} (ID: {$project->id})");
                
                Log::info('Stock deduction fixed via command', [
                    'project_id' => $project->id,
                    'project_name' => $project->name
                ]);
            } catch (\Exception $e) {
                $this->error("✗ Failed to process project {$project->id}: {$e->getMessage()}");
                Log::error('Stock deduction fix failed', [
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info('Stock deduction fix completed!');
        return 0;
    }
}
