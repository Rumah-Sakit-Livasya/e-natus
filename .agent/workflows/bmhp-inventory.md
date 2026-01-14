---
description: BMHP (Bahan Medis Habis Pakai) Inventory Management
---

# BMHP Inventory Management Workflow

## Overview

Sistem manajemen inventory untuk Bahan Medis Habis Pakai (consumable medical supplies).

## Alur Sistem

### 1. BMHP Master Data Registration

**Actor:** Pharmacy / Warehouse Manager
**Location:** BMHP → Create

**Steps:**

1. Register new BMHP item:

    - Kode BMHP
    - Nama item
    - Kategori (obat, alat kesehatan, reagent, dll)
    - Unit (box, pcs, vial, dll)
    - Harga satuan
    - **Min Stock** (minimum stock level)
    - Supplier default
    - Notes

2. Submit BMHP data

**Database Actions:**

-   Insert ke `bmhps` table

---

### 2. Stock Management

#### a) Stock Receipt (Penerimaan Stock)

**Actor:** Pharmacy Staff
**Location:** BMHP Stock Opnames → Create

**Steps:**

1. Create Stock Opname:

    - Tanggal
    - Type: Receipt / Adjustment / Usage
    - Notes

2. Input items:

    - BMHP item
    - Quantity received/adjusted
    - Batch number
    - Expiry date
    - Supplier
    - Notes

3. Submit stock opname

**Database Actions:**

-   Insert ke `bmhp_stock_opnames`
-   Update stock di `bmhps.current_stock`

**Notification Trigger:**

```
Notification: StockOpnameCreated
Recipients: Warehouse Manager, Pharmacist
```

---

#### b) Stock Usage (Project Usage)

**Actor:** Project Manager
**Location:** Project Request → BMHP Planning

**Steps:**

1. Saat create Project Request
2. Di RAB, tentukan BMHP yang dibutuhkan:

    - Select BMHP item
    - Quantity needed
    - Estimated price

3. Saat project execution:
    - Record actual BMHP usage
    - Update stock levels

**Database Actions:**

-   Planning: Insert ke `project_bmhp` atau embedded di RAB
-   Usage: Insert ke `rab_closing_bmhp_items`
-   Stock deduction dari `bmhps.current_stock`

---

### 3. Stock Monitoring & Alerts

**Features:**

1. **Low Stock Alert:**

    - System checks `current_stock` vs `min_stok`
    - If `current_stock < min_stok`, trigger alert
    - Show in dashboard
    - Notification to procurement

2. **Expiry Date Monitoring:**

    - Track items approaching expiry
    - Alert for items expiring soon
    - Prevent usage of expired items

3. **Stock Movement Report:**
    - Track all stock in/out
    - Audit trail
    - Usage by project
    - Stock aging analysis

---

### 4. Stock Opname (Physical Count)

**Actor:** Pharmacy Manager
**Location:** BMHP Stock Opnames

**Steps:**

1. Periodic physical stock count
2. Compare system stock vs actual count
3. Create Stock Opname with adjustments:

    - Items with discrepancies
    - Quantity difference
    - Reason (damage, expired, theft, count error)
    - Approver

4. Submit for approval

**Status Flow:**

```
[Draft] → [Submitted] → [Approved] → [Completed]
```

**Database Actions:**

-   Insert `bmhp_stock_opnames` with status
-   After approval: Update `bmhps.current_stock`

---

## BMHP Data Flow

```
[BMHP Master Data]
   ↓
[Stock Receipt]
   ├─ From Procurement
   ├─ From Donation
   └─ From Transfer
   ↓
[Stock Available]
   ↓
[Usage Scenarios]
   ├─ Project MCU (via RAB Closing)
   ├─ Internal Hospital Use
   └─ Patient Direct Use
   ↓
[Stock Reduction]
   ↓
[Low Stock Alert]
   ↓
[Procurement Request]
   ↓
[Stock Receipt] (cycle repeats)
```

---

## Integration Points

### 1. With Project Request

-   BMHP planning di RAB
-   Track actual usage di RAB Closing
-   Cost allocation to project

### 2. With Procurement

-   Auto-create procurement request for low stock items
-   Link procurement items to BMHP
-   Update stock upon receipt

### 3. With Finance

-   BMHP cost tracking
-   Budget vs actual BMHP spending
-   Inventory valuation

---

## Reports Available

1. **Stock Level Report:**

    - Current stock all items
    - Items below min stock
    - Stock value

2. **Stock Movement Report:**

    - Receipts
    - Usage
    - Adjustments
    - By date range

3. **Usage by Project:**

    - BMHP consumption per project
    - Cost per project

4. **Expiry Alert Report:**
    - Items expiring soon
    - Expired items

---

## Files Involved

**Models:**

-   `app/Models/Bmhp.php`
-   `app/Models/BmhpStockOpname.php`
-   `app/Models/ProjectBmhp.php`
-   `app/Models/RabClosingBmhpItem.php`

**Resources:**

-   `app/Filament/Resources/BmhpResource.php`
-   `app/Filament/Resources/BmhpStockOpnameResource.php`

**Notifications:**

-   `app/Notifications/StockOpnameCreated.php`

**Migrations:**

-   `database/migrations/2025_09_06_024053_create_bmhps_table.php`
-   `database/migrations/2025_10_04_193531_add_min_stok_to_bmhp_table.php`
-   `database/migrations/2025_10_06_064849_add_status_to_bmhp_stock_opnames_table.php`
-   `database/migrations/2025_10_04_192218_add_user_id_to_bmhp_stock_opnames_table.php`
