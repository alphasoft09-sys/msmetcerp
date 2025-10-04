# Exam Schedule Approval Workflow & Access Control

## Overview
This document outlines the new approval workflow and access control system for exam schedules. The system ensures that exam schedules are only visible to users based on their role and the current status of the schedule.

## User Roles & Access Levels

### 1. Faculty (Role 5)
- **Can see**: All their own exam schedules (all statuses)
- **Can create**: New exam schedules
- **Can edit**: Only draft and hold status schedules
- **Can submit**: Draft schedules for approval
- **Visibility**: Only sees their own schedules

### 2. Exam Cell (Role 3)
- **Can see**: Submitted and approved schedules from their TC
- **Required status**: `submitted`, `exam_cell_approved`, `tc_admin_approved`, `received`, `rejected`, `hold`
- **Cannot see**: Draft schedules (not yet submitted)
- **Actions**: Can approve, reject, or hold submitted schedules

### 3. TC Admin (Role 1)
- **Can see**: Exam cell approved schedules from their TC
- **Required status**: `exam_cell_approved`, `tc_admin_approved`, `received`, `rejected`, `hold`
- **Cannot see**: Draft or submitted schedules (not yet approved by Exam Cell)
- **Actions**: Can approve, reject, or hold exam cell approved schedules

### 4. TC Head (Role 2)
- **Can see**: Exam cell approved schedules from their TC
- **Required status**: `exam_cell_approved`, `tc_admin_approved`, `received`, `rejected`, `hold`
- **Cannot see**: Draft or submitted schedules (not yet approved by Exam Cell)
- **Actions**: Can approve, reject, or hold exam cell approved schedules

### 5. Assessment Agency (Role 4)
- **Can see**: TC approved schedules and completed Internal exams
- **Required status**: `tc_admin_approved`, `received`, `rejected`
- **Cannot see**: Draft, submitted, or exam cell approved schedules
- **Actions**: Can approve or reject TC approved Final/Special Final schedules (Internal exams are completed at TC level)

## Status Flow

### For Final and Special Final Exams:
```
Draft → Submitted → Exam Cell Approved → TC Admin/Head Approved → Assessment Agency Approved → Received
  ↓         ↓              ↓                      ↓                      ↓                ↓
Faculty   Exam Cell    TC Admin/Head        Assessment Agency        Final Status
```

### For Internal Exams:
```
Draft → Submitted → Exam Cell Approved → TC Admin/Head Approved → Received (Complete)
  ↓         ↓              ↓                      ↓                ↓
Faculty   Exam Cell    TC Admin/Head        Final Status
```

### Status Descriptions:
- **`draft`**: Created by faculty, not yet submitted
- **`submitted`**: Submitted by faculty for Exam Cell review
- **`exam_cell_approved`**: Approved by Exam Cell, visible to TC Admin/Head
- **`tc_admin_approved`**: Approved by TC Admin or TC Head
  - For Final/Special Final: Visible to Assessment Agency for final approval
  - For Internal: Final status (no Assessment Agency approval needed)
- **`received`**: Final approval status
  - For Final/Special Final: Approved by Assessment Agency
  - For Internal: Approved by TC Admin/Head (complete)
- **`rejected`**: Rejected at any stage
- **`hold`**: Put on hold for reschedule

## Internal Exam Workflow

### Special Rules for Internal Exams:
- **No Assessment Agency Approval**: Internal exams are completed after TC Admin/Head approval
- **Faster Processing**: Internal exams skip the Assessment Agency review step
- **Same Visibility**: Assessment Agency can still view completed Internal exams for reporting
- **Status Flow**: Internal exams go directly from `tc_admin_approved` to `received` status

### Implementation Logic:
```php
// When TC Admin/Head approves an Internal exam
if ($examSchedule->exam_type === 'Internal') {
    $nextStage = 'completed';
    $nextStatus = 'received'; // Direct to final status
} else {
    $nextStage = 'aa';
    $nextStatus = 'tc_admin_approved'; // Continue to Assessment Agency
}
```

## Access Control Implementation

### 1. Index Page (List View)
Each role sees only schedules they have permission to view based on status:

```php
// Faculty: All their schedules
$examSchedules = ExamSchedule::where('created_by', $user->id)

// Exam Cell: Submitted and approved schedules
$examSchedules = ExamSchedule::where('tc_code', $user->from_tc)
    ->whereIn('status', ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])

// TC Admin/Head: Exam cell approved schedules
$examSchedules = ExamSchedule::where('tc_code', $user->from_tc)
    ->whereIn('status', ['exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])

// Assessment Agency: TC approved schedules only
$examSchedules = ExamSchedule::whereIn('status', ['tc_admin_approved', 'received', 'rejected'])
```

### 2. Show Page (Detail View)
Same access control as index, with additional status checks:

```php
// Exam Cell cannot see draft schedules
if (!in_array($examSchedule->status, ['submitted', 'exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
    abort(403, 'This exam schedule is not yet submitted for review');
}

// TC Admin/Head cannot see draft or submitted schedules
if (!in_array($examSchedule->status, ['exam_cell_approved', 'tc_admin_approved', 'received', 'rejected', 'hold'])) {
    abort(403, 'This exam schedule is not yet approved by Exam Cell');
}

// Assessment Agency cannot see schedules not approved by TC
if (!in_array($examSchedule->status, ['tc_admin_approved', 'received', 'rejected'])) {
    abort(403, 'This exam schedule is not yet approved by TC Admin/Head');
}
```

### 3. Fullview Page (Print View)
Same access control as show page.

## Error Messages

### For Exam Cell:
- "This exam schedule is not yet submitted for review" (for draft schedules)

### For TC Admin/Head:
- "This exam schedule is not yet approved by Exam Cell" (for draft/submitted schedules)

### For Assessment Agency:
- "This exam schedule is not yet approved by TC Admin/Head" (for draft/submitted/exam_cell_approved schedules)

## Benefits

1. **Security**: Users only see schedules they should have access to
2. **Workflow Compliance**: Enforces proper approval sequence
3. **Data Isolation**: Prevents unauthorized access to sensitive information
4. **Clear Communication**: Informative error messages explain access restrictions
5. **Audit Trail**: Maintains proper approval chain

## Testing Scenarios

### Scenario 1: Faculty Creates Draft
- ✅ Faculty can see and edit
- ❌ Exam Cell cannot see
- ❌ TC Admin/Head cannot see
- ❌ Assessment Agency cannot see

### Scenario 2: Faculty Submits Schedule
- ✅ Faculty can see
- ✅ Exam Cell can see and approve
- ❌ TC Admin/Head cannot see
- ❌ Assessment Agency cannot see

### Scenario 3: Exam Cell Approves
- ✅ Faculty can see
- ✅ Exam Cell can see
- ✅ TC Admin/Head can see and approve
- ❌ Assessment Agency cannot see

### Scenario 4: TC Admin/Head Approves
- ✅ Faculty can see
- ✅ Exam Cell can see
- ✅ TC Admin/Head can see
- ✅ Assessment Agency can see and approve

### Scenario 5: Assessment Agency Approves (Final/Special Final)
- ✅ All roles can see (final status)

### Scenario 6: Internal Exam - TC Admin/Head Approves
- ✅ Faculty can see (completed)
- ✅ Exam Cell can see (completed)
- ✅ TC Admin/Head can see (completed)
- ✅ Assessment Agency can see (completed - no further approval needed)

## Implementation Notes

- All access control is implemented at the controller level
- Status checks are performed before loading any schedule data
- Error messages are user-friendly and explain the restriction
- The system maintains backward compatibility for existing functionality
- Logging is implemented for debugging and audit purposes 