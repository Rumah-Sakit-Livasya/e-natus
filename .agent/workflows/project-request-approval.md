---
description: Project Request & 2-Level Approval Process
---

# Project Request & Approval Workflow

## Overview

Sistem Project Request mengelola pengajuan proyek MCU (Medical Check-Up) dengan proses persetujuan 2 tingkat.

## Alur Sistem

### 1. Pembuatan Project Request

**Actor:** User (Staff)
**Location:** Filament Dashboard → Project Requests → Create

**Steps:**

1. User membuka form Project Request
2. Mengisi data project:

    - Kode Project (auto-generated: MCU###/BULAN_ROMAWI/TAHUN)
    - Nama Project
    - Client (relasi ke tabel clients)
    - Deskripsi
    - Tanggal (due_date, start_period, end_period)
    - Status awal: `approval_level_1_status = 'pending'`

3. Mengisi RAB (Rencana Anggaran Biaya):

    - **RAB Operasional Items**: Aset, Vendor Rental, atau item operasional lainnya
    - **RAB Fee Items**: Fee untuk petugas/SDM
    - **BMHP Items**: Bahan Medis Habis Pakai

4. Assign SDM (Sumber Daya Manusia):

    - Pilih employees yang akan terlibat
    - Data disimpan di `sdm_ids` (JSON array)

5. Submit Project Request

**Database Actions:**

-   Insert ke table `project_requests`
-   Insert RAB items ke `rab_operasional_items`, `rab_fee_items`
-   Model Event: `ProjectRequest::created()` triggered

**Notification Trigger:**

```php
// Setelah project dibuat, notifikasi dikirim ke users dengan permission 'approve_project_level_1'
Users with permission 'approve_project_level_1' receive notification:
- Notification: ProjectRequestCreated
- Data: project details, requester info
```

---

### 2. Approval Level 1

**Actor:** User dengan permission `approve_project_level_1`
**Location:** Notifications → View Project Request

**Steps:**

1. Approver Level 1 menerima notifikasi
2. Membuka detail Project Request
3. Review data project dan RAB
4. Actions:
    - **APPROVE** atau **REJECT**

**Jika APPROVE:**

```
Database Update:
- approval_level_1_status = 'approved'
- approval_level_1_by = {user_id}
- approval_level_1_at = {timestamp}
- approval_level_2_status = 'pending' (otomatis)
- overall_status tetap 'pending'

Notification Trigger:
- Send notification ke users dengan permission 'approve_project_level_2'
- Notification: ProjectRequestLevel2Approval
```

**Jika REJECT:**

```
Database Update:
- approval_level_1_status = 'rejected'
- approval_level_1_by = {user_id}
- approval_level_1_at = {timestamp}
- overall_status = 'rejected'
- rejection_notes = {alasan penolakan}

Notification Trigger:
- Send notification ke requester (user yang buat project)
- Notification: ProjectRequestRejectedNotification
```

---

### 3. Approval Level 2

**Actor:** User dengan permission `approve_project_level_2`
**Location:** Notifications → View Project Request

**Prerequisites:**

-   `approval_level_1_status = 'approved'`
-   `approval_level_2_status = 'pending'`

**Steps:**

1. Approver Level 2 menerima notifikasi
2. Membuka detail Project Request
3. Review data project dan RAB
4. Actions:
    - **APPROVE** atau **REJECT**

**Jika APPROVE:**

```
Database Update:
- approval_level_2_status = 'approved'
- approval_level_2_by = {user_id}
- approval_level_2_at = {timestamp}
- overall_status = 'approved'

Notification Trigger:
- Send notification ke requester
- Notification: ProjectRequestApprovedNotification

Next Actions Available:
- Manage Participants
- Manage Attendance
- Create RAB Closing
- Create Procurement
```

**Jika REJECT:**

```
Database Update:
- approval_level_2_status = 'rejected'
- approval_level_2_by = {user_id}
- approval_level_2_at = {timestamp}
- overall_status = 'rejected'
- rejection_notes = {alasan penolakan}

Notification Trigger:
- Send notification ke requester
- Notification: ProjectRequestRejectedNotification
```

---

## Status Flow Diagram

```
[Created]
   ↓ (auto set)
approval_level_1_status = 'pending'
   ↓
[Notification to Level 1 Approvers]
   ↓
   ├─[APPROVED]→ approval_level_2_status = 'pending'
   │                ↓
   │         [Notification to Level 2 Approvers]
   │                ↓
   │         ├─[APPROVED]→ overall_status = 'approved'
   │         │               (Project can proceed)
   │         │
   │         └─[REJECTED]→ overall_status = 'rejected'
   │                       (Project terminated)
   │
   └─[REJECTED]→ overall_status = 'rejected'
                 (Project terminated)
```

---

## Helper Methods

**Model: ProjectRequest**

```php
isPendingLevel1Approval()  // Check if pending Level 1
isPendingLevel2Approval()  // Check if Level 1 approved, Level 2 pending
isFullyApproved()          // Check if both levels approved
isRejected()               // Check if any level rejected
```

---

## Routes & Controllers

**Approval Actions:**

```
POST /project-requests/{id}/approve
POST /project-requests/{id}/reject
Controller: ProjectRequestActionController
```

---

## Permissions Required

1. **Create Project Request:** Any authenticated user
2. **Approve Level 1:** Permission `approve_project_level_1`
3. **Approve Level 2:** Permission `approve_project_level_2`
4. **View All Projects:** Based on Filament policy

---

## Files Involved

**Models:**

-   `app/Models/ProjectRequest.php`
-   `app/Models/User.php`
-   `app/Models/Client.php`

**Resources:**

-   `app/Filament/Resources/ProjectRequestResource.php`

**Controllers:**

-   `app/Http/Controllers/ProjectRequestActionController.php`

**Notifications:**

-   `app/Notifications/ProjectRequestCreated.php`
-   `app/Notifications/ProjectRequestLevel2Approval.php`
-   `app/Notifications/ProjectRequestApprovedNotification.php`
-   `app/Notifications/ProjectRequestRejectedNotification.php`

**Migrations:**

-   `database/migrations/2025_06_14_034641_create_project_requests_table.php`
-   `database/migrations/2025_12_10_012602_add_approval_levels_to_project_requests_table.php`
