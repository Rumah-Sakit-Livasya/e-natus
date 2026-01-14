---
description: Price Change Request & Approval System
---

# Price Change Request Workflow

## Overview

Sistem untuk mengelola permintaan perubahan harga dari vendor/supplier.

## Alur Sistem

### 1. Price Change Request (by Vendor)

**Actor:** Vendor / Supplier
**Location:** Price Change Requests → Create

**Scenario:**

-   Vendor ingin mengajukan perubahan harga untuk item/service tertentu
-   Bisa karena: inflasi, kenaikan bahan baku, perubahan kebijakan, dll

**Steps:**

1. Create Price Change Request:

    - Vendor/Supplier ID
    - Item/Service yang affected
    - Current price
    - Requested new price
    - Effective date
    - Reason/justification
    - Supporting documents (optional)

2. Submit request

**Database Actions:**

-   Insert ke `price_change_requests` table

```sql
Fields:
- vendor_id
- item_description
- current_price
- requested_price
- percentage_change (auto-calculated)
- effective_date
- reason
- status (pending/approved/rejected)
- attachment
```

**Notification Trigger:**

```
Notification: PriceChangeRequestNotification
Recipients: Procurement Manager, Finance Manager
Content: "Vendor {vendor_name} mengajukan perubahan harga untuk {item}"
```

---

### 2. Price Change Review

**Actor:** Procurement Manager / Finance
**Location:** Price Change Requests → View/Edit

**Steps:**

1. Receive notification
2. Review request:

    - Check current price vs requested price
    - Calculate percentage increase/decrease
    - Review reason & justification
    - Check supporting documents
    - Compare with market price
    - Evaluate impact on ongoing/future projects

3. Analysis:
    - Affected projects
    - Budget impact
    - Alternative vendors
    - Negotiation possibility

---

### 3. Price Change Decision

**Actor:** Procurement Manager (with authority)

**Actions:**

**a) APPROVE:**

```
Database Update:
- status = 'approved'
- approved_price = requested_price (or negotiated price)
- approved_by = {user_id}
- approved_at = {timestamp}
- approval_notes

Actions:
- Update vendor price in system
- Update affected procurement items
- Notify finance for budget adjustment

Notification Trigger:
- Notification: PriceChangeResponseNotification
- Recipients: Vendor/Supplier
- Content: "Perubahan harga Anda disetujui"
```

**b) REJECT:**

```
Database Update:
- status = 'rejected'
- rejected_by = {user_id}
- rejected_at = {timestamp}
- rejection_reason

Notification Trigger:
- Notification: PriceChangeResponseNotification
- Recipients: Vendor/Supplier
- Content: "Perubahan harga Anda ditolak"
- Include: rejection reason
```

**c) NEGOTIATE:**

```
- Status: 'under_negotiation'
- Counter-offer price
- Schedule meeting/discussion
- Request additional information
```

---

### 4. Price Update Implementation

**Actor:** System / Procurement Staff

**After Approval:**

1. Update vendor price list
2. Update affected quotations
3. Update ongoing procurement items (if applicable)
4. Notify relevant departments
5. Update contracts if needed

---

## Price Change Request Status Flow

```
[Created]
   ↓
[Pending Review]
   ↓
   ├─[Under Negotiation]
   │     ↓
   │  [Revised Request]
   │     ↓
   ├─[Approved]
   │     ↓
   │  [Price Updated]
   │     ↓
   │  [Completed]
   │
   └─[Rejected]
         ↓
      [Closed]
```

---

## Integration Points

### 1. With Vendor Management

-   Link to vendor/supplier database
-   Track price change history
-   Vendor performance evaluation

### 2. With Procurement

-   Impact analysis on pending procurement
-   Update procurement item prices
-   Re-calculate procurement budgets

### 3. With Project RAB

-   Check if price change affects approved RAB
-   Notify project managers of impact
-   Option to revise RAB if significant

### 4. With Finance

-   Budget adjustment notifications
-   Cost forecasting updates
-   Price trend analysis

---

## Reports & Analytics

1. **Price Change History:**

    - All price changes by vendor
    - Trend analysis
    - Frequency of changes

2. **Vendor Price Comparison:**

    - Compare prices across vendors
    - Identify best value vendors

3. **Impact Analysis:**
    - Projects affected by price changes
    - Budget variance due to price changes

---

## Files Involved

**Model:**

-   `app/Models/PriceChangeRequest.php`
-   `app/Models/VendorRental.php`
-   `app/Models/Supplier.php`

**Resource:**

-   `app/Filament/Resources/PriceChangeRequestResource.php`
-   `app/Filament/Resources/VendorRentalResource.php`

**Notifications:**

-   `app/Notifications/PriceChangeRequestNotification.php`
-   `app/Notifications/PriceChangeResponseNotification.php`

**Migration:**

-   `database/migrations/2025_12_01_060428_create_vendor_rentals_table.php`
-   `database/migrations/2025_12_01_063413_create_price_change_requests_table.php`
