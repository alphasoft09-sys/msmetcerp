# File Number Generation System

## Overview

The File Number Generation System automatically generates unique 18-character file numbers for exam schedules when they are approved by the Assessment Agency (user_role = 4). This system ensures proper tracking and identification of approved exam schedules.

## File Number Format

**Format:** `FN2526BB2007259999`
**Total Length:** 18 characters

### Components Breakdown:

| Position | Length | Component | Example | Description |
|----------|--------|-----------|---------|-------------|
| 1-2 | 2 | Prefix | `FN` | Fixed prefix (always "FN") |
| 3-6 | 4 | Financial Year | `2526` | Financial year based on approval date |
| 7-8 | 2 | TC Short Code | `BB` | 2-letter TC short code from `tc_shot_code` table |
| 9-14 | 6 | Approval Date | `200725` | Date in DDMMYY format |
| 15-18 | 4 | Serial Number | `9999` | Sequential number (resets per financial year per TC) |

## Financial Year Calculation

The financial year runs from **March to February**:

### Rules:
- **March–December**: Use `current_year + next_year_last_two_digits`
  - Example: August 2025 → `2526`
- **January–February**: Use `previous_year_last_two_digits + current_year_last_two_digits`
  - Example: February 2025 → `2425`

### Examples:
| Date | Financial Year | Calculation |
|------|----------------|-------------|
| March 1, 2025 | `2526` | 2025 + 26 |
| August 15, 2025 | `2526` | 2025 + 26 |
| January 15, 2025 | `2425` | 24 + 25 |
| February 28, 2025 | `2425` | 24 + 25 |

## Serial Number Logic

### Rules:
1. **Reset per financial year**: Each new financial year starts from `0001`
2. **Per TC**: Each TC has its own sequence
3. **Incremental**: Last 4 digits increment sequentially
4. **Padded**: Always 4 digits with leading zeros

### Example Sequence for TC "BB":
```
FN2526BB2007250001  (First approval in FY 2526)
FN2526BB2007250002  (Second approval in FY 2526)
FN2526BB2007250003  (Third approval in FY 2526)
FN2527BB2007250001  (First approval in FY 2527 - reset)
```

## Database Schema

### exam_schedules Table
```sql
ALTER TABLE exam_schedules 
ADD COLUMN file_no VARCHAR(18) NULL UNIQUE AFTER status,
ADD INDEX idx_file_no (file_no);
```

### tc_shot_code Table
```sql
-- Existing table structure
CREATE TABLE tc_shot_code (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tc_code VARCHAR(255) NOT NULL,
    shot_code VARCHAR(2) NOT NULL
);
```

## Implementation Details

### FileNumberService Class

**Location:** `app/Services/FileNumberService.php`

#### Key Methods:

1. **`generateFileNumber(ExamSchedule $examSchedule)`**
   - Main method to generate file number
   - Checks if file number already exists
   - Generates all components and validates length
   - Returns null on error

2. **`getFinancialYear(Carbon $date)`**
   - Calculates financial year based on date
   - Private method for internal use

3. **`getTcShortCode(string $tcCode)`**
   - Fetches TC short code from database
   - Returns 'XX' as fallback if not found

4. **`getNextSerialNumber(string $tcCode, string $financialYear)`**
   - Finds latest file number for TC and financial year
   - Extracts and increments serial number
   - Returns padded 4-digit string

5. **`validateFileNumber(string $fileNumber)`**
   - Validates file number format and length
   - Checks all components for correctness

6. **`parseFileNumber(string $fileNumber)`**
   - Parses file number into components
   - Returns array with all parts

### Integration Points

#### ExamScheduleController
**Location:** `app/Http/Controllers/ExamScheduleController.php`

**Method:** `approve(Request $request, $id)`

```php
// Generate file number if Assessment Agency is approving (final approval)
if ($user->user_role === 4 && $nextStatus === 'received') {
    $fileNumber = FileNumberService::generateFileNumber($examSchedule);
    if ($fileNumber) {
        $updateData['file_no'] = $fileNumber;
        \Log::info('File number generated for exam schedule', [
            'exam_schedule_id' => $examSchedule->id,
            'file_no' => $fileNumber
        ]);
    }
}
```

#### ExamSchedule Model
**Location:** `app/Models/ExamSchedule.php`

```php
protected $fillable = [
    // ... existing fields ...
    'file_no',
];
```

## User Interface

### Exam Schedules Index View
**Location:** `resources/views/admin/exam-schedules/index.blade.php`

#### File Number Column Display:
```php
<td>
    @if($schedule->file_no)
        <span class="badge bg-success text-white font-monospace">
            {{ $schedule->file_no }}
        </span>
    @else
        <span class="text-muted">
            <i class="bi bi-clock me-1"></i>
            Pending
        </span>
    @endif
</td>
```

## Workflow

### File Number Generation Process:

1. **Exam Schedule Creation**
   - Faculty creates exam schedule
   - `file_no` field is `NULL`

2. **Approval Chain**
   - Exam Cell approves → `file_no` remains `NULL`
   - TC Admin/Head approves → `file_no` remains `NULL`
   - Assessment Agency approves → **File number generated**

3. **File Number Assignment**
   - Only triggered when `user_role === 4` (Assessment Agency)
   - Only triggered when `nextStatus === 'received'`
   - Generates unique 18-character file number
   - Updates `exam_schedules.file_no` field
   - Logs the generation for audit trail

### Error Handling:

1. **TC Short Code Not Found**
   - Uses 'XX' as fallback
   - Logs warning message

2. **File Number Already Exists**
   - Returns existing file number
   - Logs warning message

3. **Generation Failure**
   - Returns null
   - Logs error with details
   - Schedule approval continues without file number

## Logging

### Log Messages:

#### Success:
```
File number generated successfully
exam_schedule_id: 123
file_number: FN2526BB2007250001
components: {
    financial_year: "2526",
    tc_short_code: "BB",
    date_formatted: "200725",
    serial_number: "0001"
}
```

#### Warnings:
```
TC short code not found
tc_code: "INVALID_TC"

File number already exists for exam schedule
exam_schedule_id: 123
existing_file_no: "FN2526BB2007250001"
```

#### Errors:
```
Generated file number length is incorrect
exam_schedule_id: 123
file_number: "FN2526BB200725"
length: 14

Error generating file number
exam_schedule_id: 123
error: "Database connection failed"
```

## Testing

### Manual Testing Steps:

1. **Create Exam Schedule**
   - Faculty creates new exam schedule
   - Verify `file_no` is `NULL`

2. **Exam Cell Approval**
   - Exam Cell approves schedule
   - Verify `file_no` remains `NULL`

3. **TC Admin Approval**
   - TC Admin approves schedule
   - Verify `file_no` remains `NULL`

4. **Assessment Agency Approval**
   - Assessment Agency approves schedule
   - Verify `file_no` is generated and displayed
   - Check format: `FN2526BB2007250001`

5. **Multiple Approvals**
   - Approve multiple schedules for same TC
   - Verify serial numbers increment: `0001`, `0002`, `0003`

6. **Financial Year Change**
   - Test with different dates
   - Verify financial year calculation
   - Verify serial number reset

### Validation Tests:

1. **File Number Format**
   - Length must be exactly 18 characters
   - Must start with "FN"
   - Financial year must be 4 digits
   - TC short code must be 2 letters
   - Date must be 6 digits
   - Serial number must be 4 digits

2. **Uniqueness**
   - Each file number must be unique
   - Database constraint prevents duplicates

3. **Serial Number Logic**
   - Must reset per financial year
   - Must increment per TC
   - Must be padded to 4 digits

## Security Considerations

1. **Access Control**
   - Only Assessment Agency can trigger file number generation
   - File numbers are read-only once generated

2. **Data Integrity**
   - Unique constraint on `file_no` field
   - Validation before database insertion
   - Error handling for edge cases

3. **Audit Trail**
   - All file number generations are logged
   - Includes user, timestamp, and details

## Future Enhancements

1. **File Number Search**
   - Add search functionality by file number
   - Add filter by financial year

2. **File Number Reports**
   - Generate reports by TC and financial year
   - Export file number data

3. **File Number Validation**
   - Add admin interface to validate file numbers
   - Add bulk validation tools

4. **File Number History**
   - Track file number changes
   - Maintain audit trail of modifications

## Troubleshooting

### Common Issues:

1. **File Number Not Generated**
   - Check if user is Assessment Agency (role 4)
   - Check if status is 'received'
   - Check logs for error messages

2. **Invalid File Number Format**
   - Verify TC short code exists in database
   - Check financial year calculation
   - Validate serial number logic

3. **Duplicate File Numbers**
   - Check database constraints
   - Verify serial number generation logic
   - Check for race conditions

### Debug Commands:

```bash
# Check exam schedules with file numbers
php artisan tinker --execute="echo 'Schedules with file numbers: ' . App\Models\ExamSchedule::whereNotNull('file_no')->count();"

# Check TC shot codes
php artisan tinker --execute="echo 'TC shot codes: ' . App\Models\TcShotCode::count();"

# View recent logs
tail -f storage/logs/laravel.log | grep "File number"
``` 