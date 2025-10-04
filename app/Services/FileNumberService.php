<?php

namespace App\Services;

use App\Models\ExamSchedule;
use App\Models\TcShotCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FileNumberService
{
    /**
     * Generate a unique file number for an exam schedule
     *
     * @param ExamSchedule $examSchedule
     * @return string|null
     */
    public static function generateFileNumber(ExamSchedule $examSchedule): ?string
    {
        try {
            // Check if file_no already exists
            if ($examSchedule->file_no) {
                Log::warning('File number already exists for exam schedule', [
                    'exam_schedule_id' => $examSchedule->id,
                    'existing_file_no' => $examSchedule->file_no
                ]);
                return $examSchedule->file_no;
            }

            // Get current date (approval date)
            $approvalDate = Carbon::now();
            
            // Generate file number components
            $financialYear = self::getFinancialYear($approvalDate);
            $tcShortCode = self::getTcShortCode($examSchedule->tc_code);
            $dateFormatted = $approvalDate->format('dmy');
            $serialNumber = self::getNextSerialNumber($examSchedule->tc_code, $financialYear);

            // Construct file number
            $fileNumber = "FN{$financialYear}{$tcShortCode}{$dateFormatted}{$serialNumber}";

            // Validate file number length
            if (strlen($fileNumber) !== 18) {
                Log::error('Generated file number length is incorrect', [
                    'exam_schedule_id' => $examSchedule->id,
                    'file_number' => $fileNumber,
                    'length' => strlen($fileNumber)
                ]);
                return null;
            }

            Log::info('File number generated successfully', [
                'exam_schedule_id' => $examSchedule->id,
                'file_number' => $fileNumber,
                'components' => [
                    'financial_year' => $financialYear,
                    'tc_short_code' => $tcShortCode,
                    'date_formatted' => $dateFormatted,
                    'serial_number' => $serialNumber
                ]
            ]);

            return $fileNumber;

        } catch (\Exception $e) {
            Log::error('Error generating file number', [
                'exam_schedule_id' => $examSchedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get financial year based on date
     * Financial year is from March to February
     *
     * @param Carbon $date
     * @return string
     */
    public static function getFinancialYear(Carbon $date): string
    {
        $year = $date->year;
        $month = $date->month;

        // If current date is between March–December, use current year + next year last two digits
        if ($month >= 3) {
            $nextYear = $year + 1;
            return substr($year, -2) . substr($nextYear, -2);
        }
        
        // If current date is between January–February, use previous year last two digits + current year last two digits
        $prevYear = $year - 1;
        return substr($prevYear, -2) . substr($year, -2);
    }

    /**
     * Get TC short code from tc_shot_code table
     *
     * @param string $tcCode
     * @return string
     */
    public static function getTcShortCode(string $tcCode): string
    {
        $tcShotCode = TcShotCode::where('tc_code', $tcCode)->first();
        
        if (!$tcShotCode) {
            Log::warning('TC short code not found', ['tc_code' => $tcCode]);
            return 'XX'; // Default fallback
        }

        return strtoupper($tcShotCode->shot_code);
    }

    /**
     * Get next serial number for the TC and financial year
     *
     * @param string $tcCode
     * @param string $financialYear
     * @return string
     */
    public static function getNextSerialNumber(string $tcCode, string $financialYear): string
    {
        // Get TC short code for more specific filtering
        $tcShortCode = self::getTcShortCode($tcCode);
        
        // Get all file numbers for this specific TC and financial year
        // Filter by the exact pattern: FN + financial year + TC short code
        $fileNumbers = ExamSchedule::where('tc_code', $tcCode)
            ->whereNotNull('file_no')
            ->where('file_no', 'LIKE', "FN{$financialYear}{$tcShortCode}%")
            ->pluck('file_no');

        if ($fileNumbers->isEmpty()) {
            // No previous record found for this TC and financial year, start from 0001
            Log::info('Starting new serial number sequence', [
                'tc_code' => $tcCode,
                'tc_short_code' => $tcShortCode,
                'financial_year' => $financialYear
            ]);
            return '0001';
        }

        // Extract serial numbers and find the highest one
        $maxSerial = 0;
        $latestFileNo = '';
        
        foreach ($fileNumbers as $fileNo) {
            $serial = (int) substr($fileNo, -4);
            if ($serial > $maxSerial) {
                $maxSerial = $serial;
                $latestFileNo = $fileNo;
            }
        }
        
        // Increment and pad to 4 digits
        $nextSerial = $maxSerial + 1;
        
        Log::info('Generated next serial number', [
            'tc_code' => $tcCode,
            'tc_short_code' => $tcShortCode,
            'financial_year' => $financialYear,
            'last_file_no' => $latestFileNo,
            'last_serial' => $maxSerial,
            'next_serial' => $nextSerial,
            'all_file_numbers' => $fileNumbers->toArray()
        ]);
        
        return str_pad($nextSerial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate file number format
     *
     * @param string $fileNumber
     * @return bool
     */
    public static function validateFileNumber(string $fileNumber): bool
    {
        // Check length
        if (strlen($fileNumber) !== 18) {
            return false;
        }

        // Check prefix
        if (substr($fileNumber, 0, 2) !== 'FN') {
            return false;
        }

        // Check financial year (4 digits)
        $financialYear = substr($fileNumber, 2, 4);
        if (!is_numeric($financialYear)) {
            return false;
        }

        // Check TC short code (2 characters)
        $tcShortCode = substr($fileNumber, 6, 2);
        if (!ctype_alpha($tcShortCode)) {
            return false;
        }

        // Check date (6 digits)
        $date = substr($fileNumber, 8, 6);
        if (!is_numeric($date)) {
            return false;
        }

        // Check serial number (4 digits)
        $serial = substr($fileNumber, 14, 4);
        if (!is_numeric($serial)) {
            return false;
        }

        return true;
    }

    /**
     * Build file number components used for constructing a file number
     *
     * @param ExamSchedule $examSchedule
     * @param Carbon|null $approvalDate Optionally provide an approval date; defaults to now()
     * @return array{financial_year:string, tc_short_code:string, date_formatted:string, serial_number:string}
     */
    public static function getComponents(ExamSchedule $examSchedule, ?Carbon $approvalDate = null): array
    {
        $date = $approvalDate ?? Carbon::now();

        $financialYear = self::getFinancialYear($date);
        $tcShortCode = self::getTcShortCode($examSchedule->tc_code);
        $dateFormatted = $date->format('dmy');
        
        // Get the next serial number for the current date and TC
        $serialNumber = self::getNextSerialNumber($examSchedule->tc_code, $financialYear);

        return [
            'financial_year' => $financialYear,
            'tc_short_code' => $tcShortCode,
            'date_formatted' => $dateFormatted,
            'serial_number' => $serialNumber,
        ];
    }

    /**
     * Parse file number components
     *
     * @param string $fileNumber
     * @return array|null
     */
    public static function parseFileNumber(string $fileNumber): ?array
    {
        if (!self::validateFileNumber($fileNumber)) {
            return null;
        }

        return [
            'prefix' => substr($fileNumber, 0, 2),
            'financial_year' => substr($fileNumber, 2, 4),
            'tc_short_code' => substr($fileNumber, 6, 2),
            'date' => substr($fileNumber, 8, 6),
            'serial_number' => substr($fileNumber, 14, 4)
        ];
    }
} 