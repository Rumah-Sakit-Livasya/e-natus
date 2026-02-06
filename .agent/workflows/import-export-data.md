---
description: Data Import & Export System
---

# Data Import/Export Workflow

## Overview

Sistem untuk import dan export data secara bulk menggunakan Excel/CSV files.

## Import System

### Supported Import Resources

Based on Filament Import feature, dapat import data untuk:

1. **Aset** (Assets)
2. **BMHP** (Medical Consumables)
3. **Participants** (MCU Participants)
4. **Employees**
5. **Other master data**

---

### Import Process

#### 1. Download Import Template

**Location:** Resource → Actions → Import

**Steps:**

1. Navigate to resource (e.g., Aset, BMHP)
2. Click "Import" button
3. Download Excel/CSV template
    - Template contains proper column headers
    - May include example data
    - Validation rules in description

---

#### 2. Prepare Import File

**Actor:** Data Entry Staff

**Steps:**

1. Open downloaded template
2. Fill in data:

    - Follow column format strictly
    - Use proper date format
    - Use valid option values
    - Don't change column headers

3. Validate data:

    - Check required fields
    - Verify data types
    - Ensure unique constraints

4. Save file (Excel .xlsx or CSV)

---

#### 3. Upload & Import

**Location:** Resource → Import

**Steps:**

1. Click "Import" button
2. Select file to upload
3. Map columns (if needed)
4. Review import preview
5. Confirm import

**System Process:**

```
[File Upload]
   ↓
[Validation]
   ├─ Column headers check
   ├─ Data type validation
   ├─ Required fields check
   └─ Unique constraints check
   ↓
   ├─[Valid]→ Create import job
   │            ↓
   │         [Process in background]
   │            ↓
   │         [Import rows]
   │            ↓
   │         [Success/Failed tracking]
   │
   └─[Invalid]→ Show errors
                 ↓
              [Fix & retry]
```

---

#### 4. Monitor Import Progress

**Location:** Imports Resource

**Features:**

-   View import jobs
-   Status: pending / processing / completed / failed
-   Progress percentage
-   Success count
-   Failed count
-   Error details

**Database Tables:**

-   `imports` - Import job metadata
-   `failed_import_rows` - Rows that failed validation/import

---

#### 5. Review Failed Rows

**Location:** Imports → View Failed Rows

**Steps:**

1. Open import job detail
2. View failed rows with error messages
3. Download failed rows as Excel
4. Fix errors
5. Re-import corrected data

---

## Export System

### Export Process

#### 1. Export Data

**Location:** Resource → Table Actions → Export

**Steps:**

1. Navigate to resource table
2. Optional: Apply filters to limit export
3. Click "Export" button
4. Select export format:

    - Excel (.xlsx)
    - CSV (.csv)
    - PDF (for reports)

5. Confirm export

---

#### 2. Export Job Processing

**System Process:**

```
[Export Request]
   ↓
[Create Export Job]
   ↓
[Background Processing]
   ├─ Query data
   ├─ Apply filters
   ├─ Format data
   └─ Generate file
   ↓
[Download Ready]
   ↓
[Notification to user]
```

**Database Table:**

-   `exports` - Export job metadata

---

#### 3. Download Export File

**Location:** Exports Resource → Download

**Steps:**

1. Receive notification when export ready
2. Navigate to Exports
3. Download generated file
4. File available for limited time (e.g., 24 hours)

---

## Import/Export Data Flow

```
[Manual Data Entry] ←→ [Bulk Import/Export]
         ↓                      ↓
   [Database] ←────────────────┘
         ↓
   [Reports & Analytics]
         ↓
   [Export for Sharing]
```

---

## Use Cases

### Import Use Cases:

1. **Initial Data Migration:**

    - Import aset dari sistem lama
    - Import BMHP catalog
    - Import employee master data

2. **Bulk Updates:**

    - Update asset prices
    - Update BMHP stock levels
    - Update employee information

3. **Regular Data Entry:**
    - Import MCU participant lists dari client
    - Import attendance records
    - Import lab results (if applicable)

### Export Use Cases:

1. **Reporting:**

    - Export project list untuk analysis
    - Export financial data untuk accounting
    - Export inventory untuk audit

2. **Backup:**

    - Regular data backup
    - Archive old data

3. **Sharing:**
    - Share data dengan stakeholders
    - Provide data to clients
    - Submit to regulatory bodies

---

## Error Handling

### Common Import Errors:

1. **Invalid Data Type:**

    ```
    Error: "Expected number, got text in column 'Price'"
    Fix: Ensure numeric fields contain only numbers
    ```

2. **Missing Required Field:**

    ```
    Error: "Field 'Nama Aset' is required"
    Fix: Fill in all required columns
    ```

3. **Duplicate Entry:**

    ```
    Error: "Kode Aset already exists"
    Fix: Ensure unique identifiers are unique
    ```

4. **Invalid Reference:**

    ```
    Error: "Category ID not found"
    Fix: Ensure foreign key references exist
    ```

5. **Date Format:**
    ```
    Error: "Invalid date format in column 'Date'"
    Fix: Use format: YYYY-MM-DD or DD/MM/YYYY
    ```

---

## Files Involved

**Models:**

-   Import handled by Filament Import feature
-   Export handled by Filament Export feature

**Database Tables:**

-   `imports`
-   `exports`
-   `failed_import_rows`

**Migrations:**

-   `database/migrations/2025_06_17_045018_create_imports_table.php`
-   `database/migrations/2025_06_17_045019_create_exports_table.php`
-   `database/migrations/2025_06_17_045020_create_failed_import_rows_table.php`

**Resources with Import/Export:**

-   `app/Filament/Resources/AsetResource.php`
-   `app/Filament/Resources/BmhpResource.php`
-   `app/Filament/Resources/ParticipantResource.php`
-   And other resources configured with import/export features

---

## Best Practices

### For Import:

1. Always download latest template
2. Don't modify column headers
3. Validate data before import
4. Start with small batch for testing
5. Keep backup of original data

### For Export:

1. Apply filters to limit data if needed
2. Choose appropriate format (Excel for data processing, PDF for sharing)
3. Verify exported data completeness
4. Download immediately (files may expire)

### For Both:

1. Check system capacity for large files
2. Schedule large imports/exports during off-peak hours
3. Monitor job progress
4. Keep audit trail of import/export activities
