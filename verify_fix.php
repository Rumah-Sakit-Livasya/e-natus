
use App\Models\User;
use App\Models\PriceChangeRequest;
use App\Models\RabOperasionalItem;
use App\Filament\Resources\PriceChangeRequestResource;
use Filament\Tables\Actions\Action;

// Create a request WITHOUT a requester
$item = RabOperasionalItem::first();
if (!$item) {
    echo "No RabOperasionalItem found.\n";
    return;
}

$req = PriceChangeRequest::create([
    'rab_operasional_item_id' => $item->id,
    'requested_by' => null, // Explicitly null
    'current_price' => 1000,
    'requested_price' => 2000,
    'reason' => 'test null requester',
    'status' => 'pending'
]);

echo "Created request ID: {$req->id} with null requester.\n";

// Simulate Approve Action
try {
    // We can't easily execute the Filament action closure directly from outside without mocking a lot.
    // But we can replicate the logic to ensure it doesn't crash.
    
    // Logic from the resource:
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
        echo "Notification sent (Unexpected for null requester!)\n";
    } else {
        echo "No requester, notification skipped (Expected)\n";
    }

    echo "Approval logic completed successfully.\n";

} catch (\Exception $e) {
    echo "Error during approval: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
