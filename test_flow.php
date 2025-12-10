
use App\Models\User;
use App\Models\PriceChangeRequest;
use App\Models\RabOperasionalItem;
use App\Filament\Resources\PriceChangeRequestResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

// 1. Setup Users
$requester = User::where('email', 'like', '%requester%')->first();
if (!$requester) {
    $requester = User::create([
        'name' => 'Test Requester',
        'email' => 'requester_' . uniqid() . '@example.com',
        'password' => bcrypt('password'),
    ]);
}

$approver = User::role('super-admin')->first();
if (!$approver) {
    $approver = User::first(); // Fallback
}

echo "Requester: {$requester->name} ({$requester->id})\n";
echo "Approver: {$approver->name} ({$approver->id})\n";

// 2. Create Request
$item = RabOperasionalItem::first();
$req = PriceChangeRequest::create([
    'rab_operasional_item_id' => $item->id,
    'requested_by' => $requester->id,
    'current_price' => 1000,
    'requested_price' => 5000,
    'reason' => 'Need more money',
    'status' => 'pending'
]);

echo "Request created: {$req->id}\n";

// 3. Approve Request (Simulate Admin Action)
// We manually trigger the notification logic that is in the Resource
$req->update([
    'status' => 'approved',
    'reviewed_by' => $approver->id,
    'reviewed_at' => now(),
    'review_notes' => 'Approved by script',
]);

if ($req->requester) {
    $req->requester->notify(
        new \App\Notifications\PriceChangeResponseNotification($req)
    );
    echo "Notification sent to requester.\n";
}

// 4. Check Notifications for Requester
$count = $requester->unreadNotifications()->count();
echo "Unread notifications for requester: {$count}\n";

$latest = $requester->notifications()->latest()->first();
if ($latest) {
    echo "Latest Notification Type: {$latest->type}\n";
    echo "Latest Notification Data: " . json_encode($latest->data) . "\n";
} else {
    echo "No notifications found.\n";
}
