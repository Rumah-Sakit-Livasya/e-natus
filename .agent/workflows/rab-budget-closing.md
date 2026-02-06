---
description: RAB (Budget) Planning & RAB Closing Process
---

# RAB & RAB Closing Workflow

## Overview

Sistem RAB (Rencana Anggaran Biaya) mengelola perencanaan dan penutupan anggaran project MCU.

## Alur Sistem

### 1. RAB Planning (saat create Project Request)

**Actor:** Project Creator
**Location:** Project Request Form → RAB Tab

**Steps:**

1. Buka form Project Request
2. Navigasi ke tab RAB
3. Input RAB items:

**a) RAB Operasional Items:**

-   Item dari Asset Internal (is_internal_rental = true)
-   Item dari Vendor Rental (is_vendor_rental = true)
-   Item operasional lainnya
-   Fields: description, quantity, unit, price, total

**b) RAB Fee Items:**

-   Fee untuk SDM/Petugas
-   Fields: description, quantity, unit, price, total

**c) BMHP Planning:**

-   Bahan Medis Habis Pakai yang dibutuhkan
-   Fields: bmhp_id, quantity, unit, estimated_price

**Database Actions:**

-   Insert ke `rab_operasional_items`
-   Insert ke `rab_fee_items`
-   Simpan di JSON `project_requests.rab_data`

---

### 2. RAB Closing (setelah project approved)

**Actor:** Project Manager
**Location:** Project Request → Actions → Create RAB Closing

**Prerequisites:**

-   Project status = 'approved' (fully approved)
-   Project telah selesai/hampir selesai

**Steps:**

1. Klik "Create RAB Closing" dari project detail
2. System membuat RAB Closing dengan auto-populate dari RAB Planning
3. Review dan update data realisasi:

**a) RAB Closing Operasional Items:**

```
- description (dari planning)
- planned_quantity vs actual_quantity
- planned_price vs actual_price
- planned_total vs actual_total
- notes
- attachment (bukti pengeluaran)
```

**b) RAB Closing Fee Petugas Items:**

```
- employee/petugas yang terlibat
- planned_days vs actual_days
- planned_rate vs actual_rate
- planned_total vs actual_total
- notes
- attachment
```

**c) RAB Closing BMHP Items:**

```
- bmhp_id
- planned_quantity vs actual_quantity
- planned_price vs actual_price
- planned_total vs actual_total
- notes
```

4. Upload dokumentasi project:

    - Photos
    - Reports
    - Supporting documents

5. Calculate totals:

    - Total Planned Budget
    - Total Actual Spending
    - Variance (over/under budget)

6. Submit RAB Closing

**Database Actions:**

-   Insert ke `rab_closings`
-   Insert items ke `rab_closing_operasional_items`
-   Insert items ke `rab_closing_fee_petugas_items`
-   Insert items ke `rab_closing_bmhp_items`
-   Update project status

---

### 3. RAB Closing Review & Print

**Actor:** Finance / Management
**Location:** RAB Closings → View → Print

**Steps:**

1. Review RAB Closing data
2. Compare planned vs actual
3. Check variance and notes
4. Approve/request revision
5. Print RAB Closing report

**Route:**

```
GET /rab-closings/{record}/print
Controller: RabClosingController@print
```

---

## Data Flow

```
[Project Request Created]
   ↓
[RAB Planning]
   ├─ RAB Operasional Items
   ├─ RAB Fee Items
   └─ BMHP Planning
   ↓
[Project Approved]
   ↓
[Project Execution]
   ↓
[Create RAB Closing]
   ├─ Copy from RAB Planning
   ├─ Input Actual Data
   ├─ Calculate Variance
   └─ Upload Documentation
   ↓
[RAB Closing Review]
   ↓
[Print Report]
```

---

## Key Tables & Relations

**Tables:**

-   `rab_operasional_items` (planning)
-   `rab_fee_items` (planning)
-   `rab_closings` (closing header)
-   `rab_closing_operasional_items` (closing detail)
-   `rab_closing_fee_petugas_items` (closing detail)
-   `rab_closing_bmhp_items` (closing detail)
-   `rab_attachments` (documentation)

**Relations:**

```
ProjectRequest
  ↓ hasMany
  ├─ RabOperasionalItem (planning)
  ├─ RabFeeItem (planning)
  └─ RabClosing
       ↓ hasMany
       ├─ RabClosingOperasionalItem
       ├─ RabClosingFeePetugasItem
       └─ RabClosingBmhpItem
```

---

## Files Involved

**Models:**

-   `app/Models/RencanaAnggaranBiaya.php`
-   `app/Models/RabOperasionalItem.php`
-   `app/Models/RabFeeItem.php`
-   `app/Models/RabClosing.php`
-   `app/Models/RabClosingOperasionalItem.php`
-   `app/Models/RabClosingFeePetugasItem.php`
-   `app/Models/RabClosingBmhpItem.php`
-   `app/Models/RabAttachment.php`

**Resources:**

-   `app/Filament/Resources/RabClosingResource.php`

**Controllers:**

-   `app/Http/Controllers/RabClosingController.php`

**Views:**

-   `resources/views/print/rab.blade.php`
-   `resources/views/print/rab-closing.blade.php`
