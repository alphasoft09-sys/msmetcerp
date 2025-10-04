<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\StudentLogin;
use App\Models\TcStudent;
use App\Services\DynamicTableService;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $tcCode;
    protected $results = [
        'total' => 0,
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];

    public function __construct($tcCode)
    {
        $this->tcCode = $tcCode;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $this->results['total'] = $collection->count();
        
        foreach ($collection as $row) {
            try {
                $this->processRow($row);
                $this->results['success']++;
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->results['errors'][] = [
                    'row' => $this->results['total'] - $collection->count() + 1,
                    'error' => $e->getMessage(),
                    'data' => $row->toArray()
                ];
            }
        }
    }

    /**
     * Process a single row of data
     */
    protected function processRow($row)
    {
        // Clean and validate the data
        $data = $this->cleanRowData($row);
        
        // Validate the data
        $validator = Validator::make($data, [
            'ProgName' => 'required|string|max:255',
            'RefNo' => 'required|string|max:255',
            'RollNo' => 'required|string|max:255',
            'Name' => 'required|string|max:255',
            'FatherName' => 'required|string|max:255',
            'DOB' => 'required|date',
            'Gender' => 'required|in:Male,Female,Other',
            'Category' => 'required|string|max:50',
            'Minority' => 'boolean',
            'MinorityType' => 'nullable|string|max:100',
            'EducationName' => 'required|string|max:255',
            'Address' => 'required|string',
            'City' => 'required|string|max:100',
            'State' => 'required|string|max:100',
            'District' => 'required|string|max:100',
            'Country' => 'required|string|max:100',
            'Pincode' => 'required|string|max:10',
            'MobileNo' => 'required|string|max:15',
            'PhoneNo' => 'nullable|string|max:15',
            'Email' => 'nullable|email|max:255',
            'TraineeFee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }

        // Check for duplicate RefNo and RollNo
        $tcStudent = TcStudent::forTc($this->tcCode);
        
        $existingRefNo = DB::table($tcStudent->getTable())
            ->where('RefNo', $data['RefNo'])
            ->exists();
            
        if ($existingRefNo) {
            throw new \Exception("Reference Number '{$data['RefNo']}' already exists");
        }

        $existingRollNo = DB::table($tcStudent->getTable())
            ->where('RollNo', $data['RollNo'])
            ->exists();
            
        if ($existingRollNo) {
            throw new \Exception("Roll Number '{$data['RollNo']}' already exists");
        }

        // Insert the student record
        DB::table($tcStudent->getTable())->insert($data);

        // Create login credentials if email is provided
        if (!empty($data['Email'])) {
            $password = Hash::make('password123'); // Default password
            
            StudentLogin::create([
                'name' => $data['Name'],
                'email' => $data['Email'],
                'password' => $password,
                'tc_code' => $this->tcCode,
                'phone' => $data['MobileNo'],
                'roll_number' => $data['RollNo'],
                'class' => $data['ProgName']
            ]);
        }
    }

    /**
     * Clean and format row data
     */
    protected function cleanRowData($row)
    {
        $data = [];
        
        // Map Excel column names to database column names
        $columnMapping = [
            'progname' => 'ProgName',
            'refno' => 'RefNo',
            'rollno' => 'RollNo',
            'name' => 'Name',
            'fathername' => 'FatherName',
            'dob' => 'DOB',
            'gender' => 'Gender',
            'category' => 'Category',
            'minority' => 'Minority',
            'minoritytype' => 'MinorityType',
            'minority type' => 'MinorityType',
            'educationname' => 'EducationName',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'district' => 'District',
            'country' => 'Country',
            'pincode' => 'Pincode',
            'mobileno' => 'MobileNo',
            'phoneno' => 'PhoneNo',
            'email' => 'Email',
            'traineefee' => 'TraineeFee'
        ];

        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim($key));
            
            if (isset($columnMapping[$cleanKey])) {
                $dbColumn = $columnMapping[$cleanKey];
                $data[$dbColumn] = $this->cleanValue($value, $dbColumn);
            }
        }

        return $data;
    }

    /**
     * Clean individual values based on column type
     */
    protected function cleanValue($value, $column)
    {
        $value = trim($value);
        
        if (empty($value)) {
            return null;
        }

        switch ($column) {
            case 'DOB':
                // Convert various date formats to Y-m-d
                if (preg_match('/(\d{1,2})\/(\w+)\/(\d{4})/', $value, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = $matches[3];
                    
                    $monthMap = [
                        'january' => 1, 'jan' => 1,
                        'february' => 2, 'feb' => 2,
                        'march' => 3, 'mar' => 3,
                        'april' => 4, 'apr' => 4,
                        'may' => 5,
                        'june' => 6, 'jun' => 6,
                        'july' => 7, 'jul' => 7,
                        'august' => 8, 'aug' => 8,
                        'september' => 9, 'sep' => 9,
                        'october' => 10, 'oct' => 10,
                        'november' => 11, 'nov' => 11,
                        'december' => 12, 'dec' => 12
                    ];
                    
                    $monthNum = $monthMap[strtolower($month)] ?? 1;
                    return sprintf('%04d-%02d-%02d', $year, $monthNum, $day);
                }
                return $value;
                
            case 'Gender':
                $gender = strtolower($value);
                if (in_array($gender, ['male', 'm'])) return 'Male';
                if (in_array($gender, ['female', 'f'])) return 'Female';
                if (in_array($gender, ['other', 'o'])) return 'Other';
                return 'Male'; // Default
                
            case 'Minority':
                $minority = strtolower($value);
                if (in_array($minority, ['yes', 'y', 'true', '1'])) return true;
                if (in_array($minority, ['no', 'n', 'false', '0'])) return false;
                return false; // Default
                
            case 'TraineeFee':
                // Remove currency symbols and convert to numeric
                $fee = preg_replace('/[^\d.]/', '', $value);
                return floatval($fee) ?: 0.00;
                
            case 'MobileNo':
            case 'PhoneNo':
                // Remove non-numeric characters
                return preg_replace('/[^\d]/', '', $value);
                
            case 'Pincode':
                // Remove non-numeric characters
                return preg_replace('/[^\d]/', '', $value);
                
            default:
                return $value;
        }
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Validation rules for the import
     */
    public function rules(): array
    {
        return [
            'progname' => 'required',
            'refno' => 'required',
            'rollno' => 'required',
            'name' => 'required',
            'fathername' => 'required',
            'dob' => 'required',
            'gender' => 'required',
            'category' => 'required',
            'educationname' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'district' => 'required',
            'country' => 'required',
            'pincode' => 'required',
            'mobileno' => 'required',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'progname.required' => 'Program Name is required',
            'refno.required' => 'Reference Number is required',
            'rollno.required' => 'Roll Number is required',
            'name.required' => 'Student Name is required',
            'fathername.required' => 'Father Name is required',
            'dob.required' => 'Date of Birth is required',
            'gender.required' => 'Gender is required',
            'category.required' => 'Category is required',
            'educationname.required' => 'Education Name is required',
            'address.required' => 'Address is required',
            'city.required' => 'City is required',
            'state.required' => 'State is required',
            'district.required' => 'District is required',
            'country.required' => 'Country is required',
            'pincode.required' => 'Pincode is required',
            'mobileno.required' => 'Mobile Number is required',
        ];
    }
} 