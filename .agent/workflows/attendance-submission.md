---
description: Attendance Tracking & Submission System
---

# Attendance & Submission Workflow

## Overview

Sistem manajemen kehadiran petugas/SDM pada project MCU.

## Alur Sistem

### 1. Project Attendance Planning

**Actor:** Project Manager
**Location:** Project Request → Assigned Employees

**Steps:**

1. Saat create/edit Project Request
2. Assign SDM/Employees yang akan terlibat:

    - Select employees
    - Data disimpan di `project_requests.sdm_ids` (JSON array)

3. Define project period:
    - Start period
    - End period
    - Expected attendance days

---

### 2. Daily Attendance Recording

**Actor:** Project Supervisor / HR
**Location:** Project Request → Attendance Tab

**Steps:**

1. Navigate to project attendance
2. Record daily attendance:

    - Tanggal
    - Employee/Petugas
    - Status: present / absent / late / sick / leave
    - Check-in time
    - Check-out time
    - Location
    - Notes

3. Submit attendance data

**Database Actions:**

-   Insert ke `project_attendances` table

```sql
Fields:
- project_request_id
- employee_id
- date
- status
- check_in_time
- check_out_time
- notes
- created_by
```

---

### 3. Attendance Submission (Formal Submission)

**Actor:** Project Manager / HR
**Location:** Attendance Submissions → Create

**Purpose:** Formal submission of attendance records for approval/payment processing

**Steps:**

1. Create Attendance Submission:

    - Submission date
    - Project terkait (optional)
    - Period (start - end date)
    - Employee/group
    - Total days
    - Status: draft / submitted / approved / rejected

2. Attach attendance records:

    - Link to project_attendances
    - Summary of attendance
    - Total working days
    - Total overtime (if any)

3. Submit for approval

**Database Actions:**

-   Insert ke `attendance_submissions` table

---

### 4. Attendance Approval Workflow

**Actor:** HR Manager / Finance

**Steps:**

1. Review attendance submission
2. Verify with project attendance records
3. Check for anomalies:

    - Missing attendance
    - Duplicate entries
    - Abnormal patterns

4. Actions:
    - **Approve:** Proceed to payment processing
    - **Reject:** Return with notes for correction

**Status Flow:**

```
[Draft]
   ↓
[Submitted]
   ↓
   ├─[Approved] → Payment Processing
   │
   └─[Rejected] → Revision Required → Submitted
```

---

### 5. Attendance Recap & Reporting

**Location:** Project Request → Attendance Recap

**Livewire Component:** `ProjectAttendanceRecap`
**File:** `resources/views/livewire/project-attendance-recap.blade.php`

**Features:**

-   Visual attendance calendar
-   Summary by employee:
    -   Total present days
    -   Total absent days
    -   Attendance percentage
-   Export to Excel/PDF
-   Comparison with RAB Fee planning

---

### 6. Integration with RAB Fee Items

**Purpose:** Calculate actual fee based on attendance

**Flow:**

1. **Planning (RAB Fee Items):**

    - Planned days for each employee
    - Planned rate per day
    - Planned total fee

2. **Actual (Project Attendance):**

    - Record daily attendance
    - Count actual working days

3. **Closing (RAB Closing Fee Items):**
    - Compare planned vs actual days
    - Calculate actual fee:
        ```
        actual_fee = actual_days × daily_rate
        ```
    - Calculate variance
    - Process payment

---

## Attendance Data Flow

```
[Project Approved]
   ↓
[Assign Employees]
   ↓
[Project Execution Starts]
   ↓
[Daily Attendance Recording]
   ├─ Manual input
   ├─ Mobile app (future)
   └─ Biometric integration (future)
   ↓
[Attendance Submission]
   ↓
[Approval Workflow]
   ↓
[Approved]
   ↓
   ├─[RAB Closing] (fee calculation)
   └─[Payment Processing]
```

---

## Attendance Types/Status

1. **Present:** Normal working day
2. **Absent:** Not present without valid reason
3. **Late:** Present but after designated time
4. **Sick Leave:** Absent with sick certificate
5. **Annual Leave:** Planned leave
6. **Permission:** Absent with permission
7. **Overtime:** Extra hours beyond normal

---

## Reports Available

1. **Daily Attendance Report:**

    - Who's present/absent today
    - By project or by employee

2. **Monthly Attendance Summary:**

    - Attendance statistics
    - Grouped by project/employee

3. **Attendance vs RAB Comparison:**

    - Planned days vs actual days
    - Fee implications

4. **Overtime Report:**
    - Overtime hours
    - Overtime pay calculation

---

## Files Involved

**Models:**

-   `app/Models/ProjectAttendance.php`
-   `app/Models/AttendanceSubmission.php`
-   `app/Models/Employee.php`
-   `app/Models/SDM.php`

**Resourcesources:**

-   `app/Filament/Resources/AttendanceSubmissionResource.php`

**Livewire:**

-   `app/Livewire/ProjectAttendanceRecap.php`

**Views:**

-   `resources/views/livewire/project-attendance-recap.blade.php`

**Migrations:**

-   `database/migrations/2025_07_01_030051_create_project_attendances_table.php`
-   `database/migrations/2025_07_21_135418_add_notes_to_project_attendances_table.php`
-   `database/migrations/2025_07_21_143123_create_attendance_submissions_table.php`
-   `database/migrations/2025_07_01_023902_create_employees_table.php`
