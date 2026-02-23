<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BmhpTemplateExport;

class GenerateBmhpTemplate extends Command
{
    protected $signature = 'bmhp:template';
    protected $description = 'Generate template Excel untuk import data BMHP';

    public function handle()
    {
        $filename = 'bmhp_template_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = storage_path('app/public/' . $filename);
        
        Excel::store(new BmhpTemplateExport(), $filename, 'public');
        
        $this->info("Template berhasil dibuat: {$filename}");
        $this->info("Lokasi file: {$filepath}");
        $this->info("URL download: " . url('storage/' . $filename));
        
        return 0;
    }
}
