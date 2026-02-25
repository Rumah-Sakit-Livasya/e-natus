<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking all purchases and their items...\n\n";

// Check all purchases
$purchases = DB::table('bmhp_purchases')->orderBy('id', 'desc')->limit(5)->get();

foreach ($purchases as $purchase) {
    echo "Purchase #{$purchase->id} - Status: {$purchase->status} - Created: {$purchase->created_at}\n";
    
    $items = DB::table('bmhp_purchase_items')->where('bmhp_purchase_id', $purchase->id)->get();
    echo "  -> Items count: " . $items->count() . "\n";
    
    foreach ($items as $item) {
        echo "     Item ID: {$item->id}, BMHP ID: {$item->bmhp_id}, Total PCS: {$item->total_pcs}\n";
    }
    echo "\n";
}

echo "Done.\n";
