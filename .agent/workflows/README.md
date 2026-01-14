# E-Natus System Workflows Index

## Daftar Workflow yang Tersedia

Berikut adalah dokumentasi lengkap alur sistem E-Natus (Hospital Management System for MCU):

### 1. [Project Request & Approval](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/project-request-approval.md)

**Sistem persetujuan project MCU dengan 2 tingkat approval**

-   Pembuatan project request
-   Approval Level 1 (Manager)
-   Approval Level 2 (Director)
-   Status tracking & notifications

### 2. [RAB & RAB Closing](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/rab-budget-closing.md)

**Perencanaan dan penutupan anggaran project**

-   RAB Planning (budget planning)
-   RAB Operasional, Fee, BMHP items
-   RAB Closing process
-   Variance analysis (planned vs actual)

### 3. [Asset Management](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/asset-management.md)

**Manajemen aset internal rumah sakit**

-   Asset registration & tracking
-   Asset receipt process
-   Internal rental system
-   Asset allocation to projects
-   Rental price management

### 4. [MCU Examinations](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/mcu-examinations.md)

**Sistem pemeriksaan medis (9 jenis pemeriksaan)**

-   Audiometry (tes pendengaran)
-   Drug Test (tes narkoba)
-   EKG (elektrokardiogram)
-   Lab Check (pemeriksaan laboratorium)
-   Spirometry (tes fungsi paru)
-   Rontgen/X-Ray
-   Treadmill (tes jantung)
-   USG Abdomen
-   USG Mammae

### 5. [Procurement System](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/procurement-system.md)

**Pengadaan barang dan jasa**

-   Procurement request creation
-   Vendor/supplier selection
-   Purchase order process
-   Item receipt & verification
-   Invoice & payment processing

### 6. [BMHP Inventory Management](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/bmhp-inventory.md)

**Manajemen Bahan Medis Habis Pakai**

-   BMHP master data registration
-   Stock receipt & usage tracking
-   Low stock alerts
-   Stock opname (physical count)
-   Integration with projects

### 7. [Notification System](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/notification-system.md)

**Sistem notifikasi real-time**

-   Project request notifications
-   Approval notifications
-   Price change notifications
-   Stock opname notifications
-   Filament UI integration

### 8. [Attendance & Submissions](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/attendance-submission.md)

**Tracking kehadiran petugas pada project**

-   Daily attendance recording
-   Attendance submissions
-   Approval workflow
-   Attendance recap & reporting
-   Integration with RAB Fee

### 9. [Price Change Requests](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/price-change-request.md)

**Permintaan perubahan harga dari vendor**

-   Price change request submission
-   Review & approval process
-   Price update implementation
-   Impact analysis

### 10. [Project Realisasi](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/project-realisasi.md)

**Tracking realisasi keuangan project**

-   Realisasi creation
-   Actual vs planned comparison
-   Variance analysis
-   Financial reporting
-   Integration with RAB Closing

### 11. [Import/Export Data](file:///media/supernova/DATA1/IT/App/e-natus/.agent/workflows/import-export-data.md)

**Bulk data import dan export**

-   Import templates & process
-   Data validation
-   Export formats (Excel, CSV, PDF)
-   Error handling
-   Failed row management

---

## Alur Utama Sistem

### Complete Project Lifecycle:

```
1. [Project Request Created]
         ↓
2. [Approval Level 1] → Notification
         ↓
3. [Approval Level 2] → Notification
         ↓
4. [Project Approved]
         ↓
5. [Procurement] (if needed)
         ↓
6. [Add Participants]
         ↓
7. [MCU Examinations]
         ├─ Audiometry
         ├─ Drug Test
         ├─ EKG
         ├─ Lab Check
         ├─ Spirometry
         ├─ Rontgen
         ├─ Treadmill
         ├─ USG Abdomen
         └─ USG Mammae
         ↓
8. [Track Attendance]
         ↓
9. [Use Assets & BMHP]
         ↓
10. [Create RAB Closing]
          ↓
11. [Financial Comparison]
          ↓
12. [Project Completed]
```

---

## Module Dependencies

### Core Modules (Independent):

-   User & Permission Management
-   Categories & Templates
-   Regions & Clients
-   Employees & SDM

### Master Data Modules:

-   Asset Management
-   BMHP Inventory
-   Vendor/Supplier Management
-   Templates & Categories

### Operational Modules:

-   Project Request & Approval (depends on: Clients, Users)
-   RAB & RAB Closing (depends on: Project Request, Assets, BMHP)
-   Procurement (depends on: Vendors)
-   MCU Examinations (depends on: Participants, Project Request)
-   Attendance (depends on: Project Request, Employees)

### Supporting Modules:

-   Notification System (triggered by all modules)
-   Import/Export (supports all modules)
-   Price Change Requests (depends on: Vendors)

---

## Permissions & Roles

### Key Permissions:

1. **approve_project_level_1** - Approver tingkat 1
2. **approve_project_level_2** - Approver tingkat 2
3. **manage_procurement** - Procurement manager
4. **manage_inventory** - Inventory/BMHP manager
5. **manage_assets** - Asset manager
6. **medical_staff** - Dokter, perawat, lab technician
7. **finance** - Finance department

### Typical User Roles:

-   **Super Admin** - Full access
-   **Director** - Level 2 approver, view all reports
-   **Manager** - Level 1 approver, manage projects
-   **Staff** - Create requests, data entry
-   **Medical Staff** - MCU examinations
-   **Finance** - Financial tracking, payments
-   **Warehouse** - Inventory, assets, procurement

---

## Technical Stack

**Framework:** Laravel 10+ with Filament 3
**Database:** MySQL/PostgreSQL
**Frontend:** Livewire, Alpine.js
**Charts:** Chart.js (for reporting)
**Import/Export:** Filament Excel
**Notifications:** Laravel Notifications + Filament Notifications

---

## Getting Help

Untuk informasi lebih detail tentang setiap workflow, silakan klik link workflow yang sesuai di atas.

Setiap workflow documentation mencakup:

-   Overview & purpose
-   Step-by-step process
-   Database schema involved
-   Related files & code
-   Integration points
-   Best practices
