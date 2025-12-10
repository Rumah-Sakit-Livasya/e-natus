
use App\Models\User;
use App\Models\PriceChangeRequest;
use App\Models\RabOperasionalItem;
use App\Notifications\PriceChangeResponseNotification;
use Illuminate\Support\Facades\DB;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    return;
}

$item = RabOperasionalItem::first();
if (!$item) {
    echo "No RabOperasionalItem found.\n";
    return;
}

echo "Using User: {$user->id} and Item: {$item->id}\n";

try {
    $req = PriceChangeRequest::create([
        'rab_operasional_item_id' => $item->id,
        'requested_by' => $user->id,
        'current_price' => 1000,
        'requested_price' => 2000,
        'reason' => 'test debug',
        'status' => 'approved'
    ]);
    echo "Request created: {$req->id}\n";

    $user->notify(new PriceChangeResponseNotification($req));
    echo "Notification sent.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$count = DB::table('notifications')->where('type', PriceChangeResponseNotification::class)->count();
echo "Count in DB: " . $count . "\n";

$latest = DB::table('notifications')->latest()->first();
echo "Latest type: " . ($latest->type ?? 'None') . "\n";
echo "Latest data: " . ($latest->data ?? 'None') . "\n";
