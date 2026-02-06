---
description: Procurement & Vendor Management System
---

# Procurement Workflow

## Overview

Sistem procurement mengelola pengadaan barang dan jasa untuk project MCU.

## Alur Sistem

### 1. Create Procurement Request

**Actor:** Procurement Staff
**Location:** Procurements → Create

**Prerequisites:**

-   Project Request approved
-   RAB telah dibuat

**Steps:**

1. Create Procurement:

    - Nomor Procurement (auto/manual)
    - Tanggal Request
    - Project terkait (optional)
    - Requestor
    - Priority
    - Due date

2. Submit procurement request

**Database Actions:**

-   Insert ke `procurements` table

---

### 2. Add Procurement Items

**Actor:** Procurement Staff
**Location:** Procurement → Items Tab

**Steps:**

1. Add items yang akan diprocure:

    - Item name/description
    - Specification
    - Quantity
    - Unit
    - Estimated price
    - Supplier (optional, bisa pilih nanti)
    - Notes

2. Calculate total estimated budget

**Database Actions:**

-   Insert ke `procurement_items` table

---

### 3. Vendor/Supplier Selection

**Actor:** Procurement Manager

**Steps:**

1. Request quotations dari suppliers:

    - Send RFQ (Request for Quotation)
    - Collect quotations
    - Compare prices & quality

2. Select supplier untuk each item:
    - Update procurement item dengan selected supplier
    - Confirm price
    - Confirm delivery date

**Database Actions:**

-   Update `procurement_items.supplier_id`
-   Update `procurement_items.confirmed_price`

---

### 4. Purchase Order (PO) Creation

**Actor:** Procurement Manager

**Steps:**

1. Generate Purchase Order:

    - PO Number
    - Supplier details
    - Items list
    - Prices
    - Terms & conditions
    - Payment terms
    - Delivery schedule

2. Approval workflow (if required)
3. Send PO to supplier

---

### 5. Item Receipt & Verification

**Actor:** Warehouse / Receiving Staff
**Location:** Procurement → Update Item Status

**Steps:**

1. Receive items from supplier
2. Verify:

    - Quantity received
    - Quality check
    - Match with PO

3. Update item status:
    - Status: pending → received / partially_received
    - Actual quantity received
    - Receipt date
    - Notes (any discrepancies)

**Route:**

```
PATCH /procurement-items/{id}/update-status
Controller: ProcurementItemController@updateStatus
```

**Database Actions:**

-   Update `procurement_items.status`
-   Update `procurement_items.received_quantity`
-   Update `procurement_items.received_date`

---

### 6. Invoice & Payment Processing

**Actor:** Finance

**Steps:**

1. Receive supplier invoice
2. Match invoice dengan:

    - Purchase Order
    - Goods Receipt
    - Prices

3. Process payment sesuai terms
4. Record payment

---

## Procurement Item Status Flow

```
[Created]
   ↓
[Pending] (waiting for supplier)
   ↓
[Ordered] (PO sent to supplier)
   ↓
[In Transit] (supplier shipped)
   ↓
   ├─[Received] (all items received)
   │
   └─[Partially Received] (sebagian received)
         ↓
      [Received] (completed)
```

---

## Integration with Other Modules

### 1. Project Request Integration

-   Procurement can be linked to project
-   Items dari RAB dapat di-procure
-   Track procurement cost vs RAB budget

### 2. Asset Management Integration

-   Assets yang diprocure dapat registered
-   Automatic asset creation dari procurement items (untuk capital goods)

### 3. BMHP Integration

-   BMHP items yang diprocure update stock
-   Integration dengan inventory management

---

## Vendor/Supplier Management

**Resource:** `SupplierReceiptResource`
**Location:** Suppliers → Manage

**Features:**

-   Supplier database
-   Contact information
-   Product/service categories
-   Performance rating
-   Payment terms
-   Price history

---

## Files Involved

**Models:**

-   `app/Models/Procurement.php`
-   `app/Models/ProcurementItem.php`
-   `app/Models/Supplier.php`

**Resources:**

-   `app/Filament/Resources/ProcurementResource.php`
-   `app/Filament/Resources/SupplierReceiptResource.php`

**Controllers:**

-   `app/Http/Controllers/ProcurementItemController.php`

**Livewire:**

-   `app/Livewire/ProcurementItemsTable.php`

**Views:**

-   `resources/views/livewire/procurement-items-table.blade.php`
-   `resources/views/tables/columns/procurement-header.blade.php`

**Migrations:**

-   `database/migrations/2025_06_18_151931_create_procurements_table.php`
-   `database/migrations/2025_06_18_151948_create_procurement_items_table.php`
-   `database/migrations/2025_06_12_060227_create_suppliers_table.php`

**Routes:**

```php
Route::patch('/procurement-items/{id}/update-status',
    [ProcurementItemController::class, 'updateStatus'])
```
