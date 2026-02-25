<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking purchase items data...\n";

// Check purchase #7 items
$items = DB::table('bmhp_purchase_items')->where('bmhp_purchase_id', 7)->get();

echo "Found " . $items->count() . " items for purchase #7:\n";

foreach ($items as $item) {
    echo "Item ID: {$item->id}, BMHP ID: {$item->bmhp_id}, Total PCS: {$item->total_pcs}, Qty: {$item->qty}, Purchase Type: {$item->purchase_type}\n";
    
    // Check if BMHP exists
    $bmhp = DB::table('bmhp')->where('id', $item->bmhp_id)->first();
    if ($bmhp) {
        echo "  -> BMHP: {$bmhp->name}, Current Stock: {$bmhp->stok_sisa}\n";
    } else {
        echo "  -> BMHP NOT FOUND!\n";
    }
}

echo "\nDone.\n";
