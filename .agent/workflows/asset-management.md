---
description: Asset Management System
---

# Asset Management Workflow

## Overview

Sistem manajemen aset untuk mengelola asset internal RS yang dapat disewakan untuk project MCU.

## Alur Sistem

### 1. Asset Registration

**Actor:** Asset Manager
**Location:** Filament Dashboard → Aset → Create

**Steps:**

1. Klik "Create Aset"
2. Input data asset:

    - Kode Aset (unique identifier)
    - Nama Aset
    - Kategori
    - Type (jenis aset)
    - Serial Number
    - Harga Perolehan (acquisition cost)
    - **Rental Price** (harga sewa per hari/periode)
    - Status: available / unavailable / maintenance
    - Lokasi
    - Tanggal Perolehan
    - Kondisi
    - Notes

3. Upload foto/dokumentasi aset
4. Submit

**Database Actions:**

-   Insert ke table `asets`

**Import Feature:**

-   Support bulk import via Excel/CSV
-   `AsetResource` sudah configured untuk import

---

### 2. Asset Receipt (Penerimaan Aset Baru)

**Actor:** Asset Manager / Warehouse
**Location:** Asset Receipts → Create

**Steps:**

1. Create Asset Receipt:

    - Nomor Receipt
    - Tanggal Penerimaan
    - Supplier (optional)
    - Notes

2. Input Asset Receipt Items:

    - Select or create new asset
    - Quantity received
    - Condition on arrival
    - Notes

3. Submit receipt

**Database Actions:**

-   Insert ke `aset_receipts`
-   Insert items ke `asset_receipt_items`
-   Update asset inventory

---

### 3. Asset Allocation to Project

**Actor:** Project Manager
**Location:** Project Request Form → RAB Operasional

**Steps:**

1. Saat membuat/edit Project Request
2. Di section RAB Operasional Items
3. Pilih asset internal:

    - Select dari daftar `asets` dengan status 'available'
    - Quantity
    - Duration (days)
    - **Price auto-populated dari `rental_price`**
    - Total = rental_price × quantity × days

4. System marks asset as allocated

**Database Actions:**

-   Insert ke `rab_operasional_items` dengan:
    ```
    is_internal_rental = true
    asset_id = {selected_asset_id}
    price = {asset.rental_price}
    ```
-   Update `asets.status` → 'unavailable' (optional, tergantung bisnis logic)

---

### 4. Asset Return After Project

**Actor:** Project Manager / Asset Manager
**Location:** Project Request → RAB Closing

**Steps:**

1. Setelah project selesai
2. Di RAB Closing, record actual usage:

    - Actual days used
    - Condition on return
    - Any damages/issues

3. Submit RAB Closing
4. Asset status kembali ke 'available'

**Database Actions:**

-   Insert/update `rab_closing_operasional_items`
-   Update `asets.status` → 'available'

---

### 5. Asset Maintenance & Status Management

**Actor:** Asset Manager
**Location:** Aset → Edit

**Actions Available:**

-   Mark as unavailable:
    ```
    Route: GET /aset/{aset}/mark-unavailable
    ```
-   Update asset condition
-   Record maintenance history
-   Upload maintenance documents

---

### 6. Asset Printing & Reporting

**Actor:** Any authorized user
**Location:** Assets → Print

**Route:**

```
GET /print-assets
Controller: PrintController@printAssets
```

**Report includes:**

-   Asset list dengan status
-   Rental prices
-   Allocation history
-   Condition status

---

## Asset Status Flow

```
[New Asset]
   ↓
[Available]
   ↓
   ├─[Allocated to Project]→ Unavailable
   │     ↓                      ↓
   │  [Project Running]   [Maintenance]
   │     ↓                      ↓
   │  [Project Ends]            │
   │     ↓                      │
   └────[Available]←────────────┘
```

---

## Key Features

1. **Internal Rental System:**

    - Track rental revenue dari asset internal
    - Auto-calculate rental cost di RAB

2. **Asset Tracking:**

    - Location tracking
    - Condition monitoring
    - Maintenance history

3. **Integration dengan Project:**
    - Seamless allocation
    - Auto-pricing dari rental_price
    - Return tracking via RAB Closing

---

## Files Involved

**Models:**

-   `app/Models/Aset.php`
-   `app/Models/AsetReceipt.php`
-   `app/Models/AssetReceiptItem.php`

**Resources:**

-   `app/Filament/Resources/AsetResource.php`
-   `app/Filament/Resources/AsetReceiptResource.php`

**Controllers:**

-   `app/Http/Controllers/AsetController.php`
-   `app/Http/Controllers/PrintController.php`

**Migrations:**

-   `database/migrations/2025_06_19_125136_create_asets_table.php`
-   `database/migrations/2025_12_10_032435_add_type_and_serial_number_to_asets_table.php`
-   `database/migrations/2025_12_10_081942_add_rental_price_to_aset_table.php`

**Routes:**

```php
Route::get('/aset/{aset}/mark-unavailable', ...)
Route::get('/print-assets', [PrintController::class, 'printAssets'])
```
