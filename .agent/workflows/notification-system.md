---
description: Notification System & Event Triggers
---

# Notification System Workflow

## Overview

Sistem notifikasi mengelola pemberitahuan real-time kepada users menggunakan Laravel Notifications dan Filament Notifications.

## Notification Types

### 1. Project Request Created

**Trigger:** Setelah ProjectRequest dibuat
**Recipients:** Users dengan permission `approve_project_level_1`
**Notification Class:** `ProjectRequestCreated`

**Content:**

```
Title: "Project Request Baru Menunggu Persetujuan"
Message: "Project {project_name} dari {requester_name} memerlukan persetujuan Level 1"
Action: Link ke detail project request
```

**Code Location:**

```php
// app/Models/ProjectRequest.php - created() event
static::created(function ($projectRequest) {
    if ($projectRequest->approval_level_1_status === 'pending') {
        $users = User::permission('approve_project_level_1')->get();
        foreach ($users as $user) {
            $user->notify(new ProjectRequestCreated($projectRequest));
        }
    }
});
```

---

### 2. Project Level 2 Approval Required

**Trigger:** Setelah Level 1 approved
**Recipients:** Users dengan permission `approve_project_level_2`
**Notification Class:** `ProjectRequestLevel2Approval`

**Content:**

```
Title: "Project Request Membutuhkan Persetujuan Level 2"
Message: "Project {project_name} telah disetujui Level 1, butuh persetujuan Level 2"
Action: Link ke detail project request
```

---

### 3. Project Request Approved

**Trigger:** Setelah Level 2 approved
**Recipients:** Project Requester (creator)
**Notification Class:** `ProjectRequestApprovedNotification`

**Content:**

```
Title: "Project Request Anda Disetujui"
Message: "Project {project_name} telah disetujui sepenuhnya"
Action: Link ke detail project
```

---

### 4. Project Request Rejected

**Trigger:** Jika Level 1 atau Level 2 reject
**Recipients:** Project Requester
**Notification Class:** `ProjectRequestRejectedNotification`

**Content:**

```
Title: "Project Request Anda Ditolak"
Message: "Project {project_name} ditolak pada Level {X}"
Reason: {rejection_notes}
Action: Link ke detail project
```

---

### 5. Price Change Request

**Trigger:** Saat vendor/supplier submit price change
**Recipients:** Admin / Procurement Manager
**Notification Class:** `PriceChangeRequestNotification`

**Content:**

```
Title: "Permintaan Perubahan Harga"
Message: "Perubahan harga dari {vendor} untuk item {item}"
Action: Link ke price change request
```

---

### 6. Price Change Response

**Trigger:** Setelah admin approve/reject price change
**Recipients:** Vendor/Supplier yang mengajukan
**Notification Class:** `PriceChangeResponseNotification`

---

### 7. Stock Opname Created

**Trigger:** Saat BMHP Stock Opname dibuat
**Recipients:** Warehouse Manager / Pharmacist
**Notification Class:** `StockOpnameCreated`

**Content:**

```
Title: "Stock Opname Baru"
Message: "Stock Opname BMHP telah dibuat, silakan review"
```

---

## Notification Flow

```
[Event Triggered]
   ↓
[Notification Dispatched]
   ↓
[Database Storage]
   ├─ Table: notifications
   ├─ Fields: id, type, notifiable_id, notifiable_type, data, read_at
   ↓
[Filament UI]
   ├─ Bell Icon (badge count)
   ├─ Notification Panel
   └─ Database Notifications Component
   ↓
[User Actions]
   ├─ Mark as Read
   ├─ Click to View Detail
   └─ Clear Notification
```

---

## Technical Implementation

### Storage

**Table:** `notifications` (Laravel default)

```sql
id, type, notifiable_id, notifiable_type, data (JSON), read_at, created_at, updated_at
```

### Notification Model

**File:** `app/Models/Notification.php`

```php
class Notification extends Model
{
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
```

### Notification Resource

**File:** `app/Filament/Resources/NotificationResource.php`

-   Manage notifications via Filament admin panel
-   View all notifications
-   Mark as read/unread
-   Delete notifications

---

## Viewing Notifications

### 1. Via Filament UI

**Location:** Top navigation bar → Bell icon

**Features:**

-   Real-time notification count
-   Dropdown panel with recent notifications
-   Click to navigate to related record
-   Mark as read functionality

### 2. Via Notification Page

**Route:** `/notifications`
**Controller:** `NotificationController@index`

**Features:**

-   List all notifications
-   Filter by read/unread
-   Bulk mark as read
-   Delete notifications

---

## Debug & Monitoring

### Check Notifications in Database

**Route:** `GET /debug/current-user-notifications`
**Returns:**

```json
{
  "user_id": 1,
  "user_name": "John Doe",
  "total_notifications": 10,
  "unread_notifications": 3,
  "latest_notifications": [...]
}
```

### Log Monitoring

```php
\Log::info('Notification sent', [
    'project_id' => $projectRequest->id,
    'user_id' => $user->id,
    'user_name' => $user->name
]);
```

---

## Files Involved

**Notifications:**

-   `app/Notifications/ProjectRequestCreated.php`
-   `app/Notifications/ProjectRequestLevel2Approval.php`
-   `app/Notifications/ProjectRequestApprovedNotification.php`
-   `app/Notifications/ProjectRequestRejectedNotification.php`
-   `app/Notifications/PriceChangeRequestNotification.php`
-   `app/Notifications/PriceChangeResponseNotification.php`
-   `app/Notifications/StockOpnameCreated.php`

**Model:**

-   `app/Models/Notification.php`

**Resource:**

-   `app/Filament/Resources/NotificationResource.php`

**Controller:**

-   `app/Http/Controllers/NotificationController.php`

**Views:**

-   `resources/views/vendor/filament-notifications/notifications.blade.php`
-   `resources/views/vendor/filament-notifications/database-notifications.blade.php`

**Livewire:**

-   `app/Livewire/DatabaseNotifications.php`

**Migration:**

-   `database/migrations/2025_06_14_051911_create_notifications_table.php`
