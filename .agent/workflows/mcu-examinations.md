---
description: MCU Examination & Medical Check Management
---

# MCU Examination Workflow

## Overview

Sistem pemeriksaan MCU (Medical Check-Up) mencakup berbagai jenis pemeriksaan medis untuk participants.

## Jenis Pemeriksaan

### 1. Audiometry Check (Tes Pendengaran)

**Resource:** `AudiometryCheckResource`
**Model:** `AudiometryCheck`
**Route:** `GET /audiometry-checks/{record}/print`

**Fields:**

-   Participant info
-   Test date
-   Right ear results (frequencies, thresholds)
-   Left ear results
-   Audiogram data
-   Doctor notes
-   Conclusion

---

### 2. Drug Test (Tes Narkoba)

**Resource:** `DrugTestResource`
**Model:** `DrugTest`
**Route:** `GET /drug-tests/{record}/print`

**Fields:**

-   Participant info
-   Sample collection date/time
-   Sample type (urine/blood)
-   Test results (various substances)
-   Positive/Negative indicators
-   Doctor verification
-   Conclusion

---

### 3. EKG Check (Elektrokardiogram)

**Resource:** `EkgCheckResource`
**Model:** `EkgCheck`
**Route:** `GET /ekg-checks/{record}/print`

**Fields:**

-   Participant info
-   Test date
-   Heart rate
-   Rhythm
-   EKG interpretation
-   Abnormalities (if any)
-   EKG image/file
-   Doctor analysis
-   Conclusion

---

### 4. Lab Check (Pemeriksaan Laboratorium)

**Resource:** `LabCheckResource`
**Model:** `LabCheck`
**Route:** `GET /lab-checks/{record}/print`

**Categories:**

-   Hematology (darah lengkap)
-   Chemistry (fungsi hati, ginjal, gula darah, lipid profile)
-   Urine analysis
-   Hepatitis markers
-   Other specialized tests

**Fields:**

-   Participant info
-   Sample collection date
-   Test results dengan reference ranges
-   Abnormal flags
-   Lab technician notes
-   Doctor interpretation

---

### 5. Spirometry Check (Tes Fungsi Paru)

**Resource:** `SpirometryCheckResource`
**Model:** `SpirometryCheck`
**Route:** `GET /spirometry-checks/{record}/print`

**Fields:**

-   Participant info
-   Test date
-   FVC (Forced Vital Capacity)
-   FEV1 (Forced Expiratory Volume)
-   FEV1/FVC ratio
-   Peak flow
-   Spirometry graph/chart
-   Interpretation
-   Doctor conclusion

---

### 6. Rontgen/X-Ray Check

**Resource:** `RontgenCheckResource`
**Model:** `RontgenCheck`
**Route:** `GET /rontgen-checks/{record}/print`

**Fields:**

-   Participant info
-   X-ray date
-   X-ray type (chest, abdomen, etc.)
-   X-ray image files
-   Radiologist findings
-   Abnormalities detected
-   Diagnosis
-   Recommendations

---

### 7. Treadmill Check (Tes Jantung)

**Resource:** `TreadmillCheckResource`
**Model:** `TreadmillCheck`
**Route:** `GET /treadmill-checks/{record}/print`

**Fields:**

-   Participant info
-   Test date
-   Protocol used (Bruce, modified Bruce, etc.)
-   Duration
-   Maximum heart rate achieved
-   Blood pressure responses
-   ECG changes
-   Symptoms during test
-   Conclusion & recommendations

---

### 8. USG Abdomen Check

**Resource:** `UsgAbdomenCheckResource`
**Model:** `UsgAbdomenCheck`
**Route:** `GET /usg-abdomen-checks/{record}/print`

**Fields:**

-   Participant info
-   USG date
-   Organs examined (liver, gallbladder, pancreas, kidney, spleen)
-   Findings for each organ
-   USG images
-   Abnormalities
-   Radiologist interpretation
-   Diagnosis

---

### 9. USG Mammae Check

**Resource:** `UsgMammaeCheckResource`
**Model:** `UsgMammaeCheck`
**Route:** `GET /usg-mammae-checks/{record}/print`

**Fields:**

-   Participant info
-   USG date
-   Right breast findings
-   Left breast findings
-   Masses/lesions detected
-   USG images
-   BIRADS classification
-   Recommendations

---

## General Workflow

### 1. Participant Registration

**Prerequisites:**

-   Project Request approved
-   Participants added to project

**Location:** Project Request → Participants Tab

**Steps:**

1. Add participant:

    - Personal info (NIK, nama, gender, DOB)
    - Contact info
    - Company/Client
    - MCU package
    - Photo

2. Submit participant data

**Database:** `participants` table

---

### 2. Examination Process

**Actor:** Medical Staff (Doctor, Nurse, Lab Technician, Radiologist)

**For each examination type:**

1. **Select Participant:**

    - Dari daftar participants di project
    - View participant details

2. **Perform Examination:**

    - Navigate to specific examination resource
    - Create new examination record
    - Link to participant
    - Input examination data/results

3. **Upload Supporting Files:**

    - Images (X-ray, USG, EKG)
    - Documents
    - Lab reports

4. **Doctor Review & Interpretation:**

    - Review results
    - Add medical interpretation
    - Determine conclusion (normal/abnormal)
    - Add recommendations

5. **Save & Print:**
    - Save examination record
    - Print examination report

**Database Actions:**

-   Insert ke table pemeriksaan terkait (e.g., `lab_checks`, `rontgen_checks`)

---

### 3. MCU Result Compilation

**Actor:** MCU Coordinator
**Location:** MCU Results → Create/Edit

**Steps:**

1. Select participant
2. System aggregates all examination results
3. Review complete MCU data:

    - Physical examination
    - All lab results
    - All imaging results
    - All specialized tests

4. Doctor's overall conclusion:

    - Fit to work / Not fit
    - Limitations/restrictions
    - Follow-up recommendations

5. Generate complete MCU report

**Database:** `mcu_results` table

---

### 4. Report & Summary

**Route:** `GET /participants/{participant}/print-summary`
**Controller:** `ParticipantReportController@printSummary`

**Report includes:**

-   Participant demographics
-   All examination results
-   Summary of findings
-   Doctor's conclusion
-   Recommendations

---

## Data Flow

```
[Project Approved]
   ↓
[Add Participants]
   ↓
[Examinations]
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
[MCU Result Compilation]
   ↓
[Final Report]
   ↓
[Print Summary]
```

---

## Files Involved

**Resources:**

-   `app/Filament/Resources/ParticipantResource.php`
-   `app/Filament/Resources/McuResultResource.php`
-   `app/Filament/Resources/AudiometryCheckResource.php`
-   `app/Filament/Resources/DrugTestResource.php`
-   `app/Filament/Resources/EkgCheckResource.php`
-   `app/Filament/Resources/LabCheckResource.php`
-   `app/Filament/Resources/SpirometryCheckResource.php`
-   `app/Filament/Resources/RontgenCheckResource.php`
-   `app/Filament/Resources/TreadmillCheckResource.php`
-   `app/Filament/Resources/UsgAbdomenCheckResource.php`
-   `app/Filament/Resources/UsgMammaeCheckResource.php`

**Models:**

-   `app/Models/Participant.php`
-   `app/Models/McuResult.php`
-   `app/Models/McuAttachment.php`
-   Plus models untuk setiap jenis pemeriksaan

**Controllers:**

-   `app/Http/Controllers/ParticipantReportController.php`
-   Controllers untuk setiap jenis pemeriksaan (print)

**Views:**

-   `resources/views/pemeriksaan/*.blade.php` (print templates)
-   `resources/views/pemeriksaan/partials/*-patient-header.blade.php`
