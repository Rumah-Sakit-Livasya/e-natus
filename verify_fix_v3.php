
use App\Models\User;
use App\Models\PriceChangeRequest;
use App\Models\RabOperasionalItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 1. Disable FK checks
Schema::disableForeignKeyConstraints();

// 2. Create a request with a non-existent user ID
$item = RabOperasionalItem::first();
if (!$item) {
    echo "No RabOperasionalItem found.\n";
    return;
}

$nonExistentUserId = 999999;
$req = PriceChangeRequest::create([
    'rab_operasional_item_id' => $item->id,
    'requested_by' => $nonExistentUserId,
    'current_price' => 1000,
    'requested_price' => 2000,
    'reason' => 'test orphaned requester',
    'status' => 'pending'
]);

echo "Created request ID: {$req->id} with non-existent requester ID: {$nonExistentUserId}\n";

// 3. Re-enable FK checks
Schema::enableForeignKeyConstraints();

// Verify relation is null
$req = PriceChangeRequest::find($req->id);
if (!$req->requester) {
    echo "Confirmed: Request has no requester object.\n";
} else {
    echo "Warning: Requester exists? " . $req->requester->id . "\n";
}

// 4. Simulate Approve Action
try {
    $record = $req;
    $data = ['review_notes' => 'Approved via script'];
    
    // Update the price
    $record->rabOperasionalItem->update([
        'harga_sewa' => $record->requested_price,
        'total' => $record->rabOperasionalItem->qty_aset * $record->requested_price,
    ]);

    // Update request status
    $record->update([
        'status' => 'approved',
        'reviewed_by' => User::first()->id,
        'reviewed_at' => now(),
        'review_notes' => $data['review_notes'] ?? null,
    ]);

    // Notify requester - THIS IS THE FIX
    if ($record->requester) {
        $record->requester->notify(
            new \App\Notifications\PriceChangeResponseNotification($record)
        );
        echo "Notification sent (Unexpected!)\n";
    } else {
        echo "No requester, notification skipped (Expected)\n";
    }

    echo "Approval logic completed successfully.\n";

} catch (\Exception $e) {
    echo "Error during approval: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
