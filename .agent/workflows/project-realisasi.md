---
description: Project Realisasi & Financial Tracking
---

# Project Realisasi Workflow

## Overview

Sistem untuk tracking realisasi keuangan project terhadap RAB yang telah direncanakan.

## Alur Sistem

### 1. Create Realisasi (After Project Execution)

**Actor:** Project Manager / Finance
**Location:** Project Request → Realisasi RAB → Create

**Route:**

```
GET /project-requests/{project}/realisasi-rab/create
Controller: ProjectRealisationController@create
```

**Prerequisites:**

-   Project approved
-   Project dalam tahap execution atau completed
-   RAB telah dibuat

**Steps:**

1. Klik "Create Realisasi RAB"
2. System load RAB items yang telah direncanakan
3. Input realisasi untuk setiap item:

**RAB Operasional Items:**

```
- Planned: description, qty, price, total
- Actual: actual_qty, actual_price, actual_total
- Variance: planned - actual
- Notes: penjelasan variance
- Attachment: bukti pengeluaran
```

**RAB Fee Items:**

```
- Planned: employee, days, rate, total
- Actual: actual_days, actual_rate, actual_total
- Variance
- Notes
- Attendance reference
```

**BMHP Items:**

```
- Planned: item, qty, price
- Actual: actual_qty, actual_price
- Variance
- Notes
```

4. Upload documentation:

    - Invoices
    - Receipts
    - Photos
    - Reports

5. Submit realisasi

**Database Actions:**

-   Insert ke `realisations` table
-   Insert items ke `realisation_rab_items`
    Note: Seems to be integrated with RAB Closing system

---

### 2. Realisation Review

**Actor:** Finance Manager / Project Director

**Steps:**

1. Review realisasi vs RAB
2. Analyze variances:

    - Over budget (actual > planned)
    - Under budget (actual < planned)
    - Reasons for variance

3. Check documentation:

    - Invoice validity
    - Receipt authenticity
    - Proper approvals

4. Approval/Rejection decision

---

### 3. Financial Comparison & Analysis

**Location:** Project Finance Comparison Page

**Route:**

```
GET /dashboard/project-comparison/{record}
Page: ProjectFinanceComparison
```

**Features:**

1. **Side-by-side Comparison:**

    - RAB (Planned) vs Realisasi (Actual)
    - Per category breakdown
    - Variance analysis

2. **Visual Charts:**

    - Budget utilization
    - Spending by category
    - Timeline of expenses

3. **Summary Metrics:**
    - Total planned budget
    - Total actual spending
    - Total variance (Rp & %)
    - Budget utilization rate

---

### 4. Project Financial Reporting

**Route:**

```
GET /print-realisasi-rab/{project}
Controller: PrintController@printRealisasiRab
View: resources/views/print/project-realisasi-rab.blade.php
```

**Report includes:**

-   Project header info
-   RAB summary
-   Realisasi summary
-   Detailed item-by-item comparison
-   Variance analysis
-   Total financial summary
-   Approvals & signatures

---

## Data Flow

```
[RAB Created]
   ↓
[Project Execution]
   ↓
[Track Actual Expenses]
   ├─ Procurement costs
   ├─ Attendance/Fee actual
   └─ BMHP usage actual
   ↓
[Create Realisasi]
   ├─ Input actual data
   ├─ Upload proof/documentation
   └─ Calculate variance
   ↓
[Realisasi Review]
   ↓
[Approval]
   ↓
[Financial Comparison]
   ↓
[Project Closure]
```

---

## Integration with RAB Closing

**Note:** The system seems to have both:

-   **Realisasi System** (older/separate tracking)
-   **RAB Closing System** (newer/integrated)

**RAB Closing** (recommended approach):

-   More comprehensive
-   Integrated with RAB planning
-   Built-in variance tracking
-   Better documentation handling

**Current Implementation:**

-   `RabClosing` is the primary system
-   `Realisation` might be legacy or for different purposes
-   Check with business users for clarification

---

## Variance Categories

**Favorable Variance (Under Budget):**

-   Actual < Planned
-   Reasons:
    -   Better negotiation
    -   Efficient resource usage
    -   Scope reduction

**Unfavorable Variance (Over Budget):**

-   Actual > Planned
-   Reasons:
    -   Price increases
    -   Scope creep
    -   Inefficiencies
    -   Unplanned expenses

**Requires Investigation:**

-   Large variances (>10%)
-   Unexplained differences
-   Missing documentation

---

## Files Involved

**Models:**

-   `app/Models/Realisation.php`
-   `app/Models/RealisationRabItem.php`
-   `app/Models/RabClosing.php` (newer system)

**Resources:**

-   `app/Filament/Resources/RealisationResource.php`
-   `app/Filament/Resources/RabClosingResource.php`

**Controllers:**

-   `app/Http/Controllers/ProjectRealisationController.php`
-   `app/Http/Controllers/PrintController.php`

**Pages:**

-   `app/Filament/Pages/ProjectFinanceComparison.php`

**Views:**

-   `resources/views/project-realisasi/create.blade.php`
-   `resources/views/print/project-realisasi-rab.blade.php`

**Livewire:**

-   `app/Livewire/ProjectRealisasi/CreateRealisasiRab.php`

**Migrations:**

-   `database/migrations/2025_06_18_152009_create_realisations_table.php`
-   `database/migrations/2025_06_23_161605_create_realisation_rab_items_table.php`

**Routes:**

```php
Route::get('/project-requests/{project}/realisasi-rab/create', ...)
Route::post('/realisation-rab-items', ...)
Route::get('/print-realisasi-rab/{project}', ...)
Route::get('/dashboard/project-comparison/{record}', ...)
```
