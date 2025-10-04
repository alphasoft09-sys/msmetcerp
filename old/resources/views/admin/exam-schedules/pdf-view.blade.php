<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule of {{ $examSchedule->course_name }}-{{ $examSchedule->batch_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            width: 100%; /* Make it responsive */
            box-sizing: border-box; /* Include padding and border in width calculation */
        }

        .styled {
            border: 0;
            line-height: 2.5;
            padding: 0 20px;
            font-size: 1rem;
            text-align: center;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            border-radius: 10px;
            background-color: rgba(220, 0, 0, 1);
            background-image: linear-gradient(to top left, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2) 30%, rgba(0, 0, 0, 0));
            box-shadow: inset 2px 2px 3px rgba(255, 255, 255, 0.6), inset -2px -2px 3px rgba(0, 0, 0, 0.6);
        }

        .styled:hover {
            background-color: rgba(255, 0, 0, 1);
        }

        .styled:active {
            box-shadow: inset -2px -2px 3px rgba(255, 255, 255, 0.6), inset 2px 2px 3px rgba(0, 0, 0, 0.6);
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .header-layout-container {
                min-height: 100px;
                padding: 10px 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .header-layout-image {
                max-height: 100px;
                max-width: 300px;
                width: auto;
                height: auto;
                display: block;
                margin: 0 auto;
            }

            .container {
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 15mm;
                border: none;
                border-radius: 0;
                box-shadow: none;
                background-color: white;
                min-height: auto;
                max-height: none;
                overflow: visible;
            }
            
            table {
                font-size: 10px;
            }
            
            .exam-table th,
            .exam-table td {
                padding: 6px 8px;
                font-size: 10px;
            }
        }

        table, td, th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            width: 100%; /* Full width for landscape */
            margin: 10px 0;
            font-family: 'Inter', sans-serif;
            font-size: 12px; /* Smaller font for landscape */
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            padding: 8px 12px;
            text-align: center;
            font-size: 11px;
        }

        td {
            padding: 8px 12px;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #e9ecef;
        }

        /* Header layout styling */
        .header-layout-container {
            text-align: center;
            padding: 10px 0;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-layout-image {
            max-width: 100%;
            max-height: 80px;
            width: auto;
            height: auto;
        }

        /* Responsive styles */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                margin: 10px;
                padding: 15px;
            }

            table {
                font-size: 10px;
            }
            
            .exam-table th,
            .exam-table td {
                padding: 6px 8px;
                font-size: 10px;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin: 5px;
                padding: 10px;
            }
            
            table {
                font-size: 9px;
            }
            
            .exam-table th,
            .exam-table td {
                padding: 4px 6px;
                font-size: 9px;
            }
            
            .header-layout-container {
                min-height: 40px;
                padding: 5px 0;
            }

            .header-layout-image {
                max-height: 40px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div align="center" id="printableArea">
        @php
            $headerLayout = \App\Models\TcHeaderLayout::where('tc_id', $examSchedule->tc_code)->first();
            $examCellUser = null;
            $managerUser = null;
            $examCellSignature = null;
            $managerSignature = null;
            
            if($examSchedule->exam_cell_approved_by) {
                $examCellUser = \App\Models\User::find($examSchedule->exam_cell_approved_by);
            }
            
            if($examSchedule->tc_admin_approved_by) {
                $managerUser = \App\Models\User::find($examSchedule->tc_admin_approved_by);
            }
            
            // Get signatures if available
            if($examCellUser && $examCellUser->signature) {
                $examCellSignature = $examCellUser->signature;
            }
            
            if($managerUser && $managerUser->signature) {
                $managerSignature = $managerUser->signature;
            }
        @endphp

        @if($headerLayout && $headerLayout->header_image)
            <div class="header-layout-container">
                <img src="{{ Storage::url($headerLayout->header_image) }}" alt="Header" class="header-layout-image">
            </div>
        @endif

        <table border="1" align="center" style="width: 100%; height: 100%">
            <tr>
                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p align=center><b>EXAMINATION TYPE</b></p>
                    <input style="text-align:center;" type="submit" value="{{ $examSchedule->exam_type }} SEM: {{ $examSchedule->semester }}" disabled>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                    <p align=center><b>EXAMINATION PERIOD</b></p>
                    <input style="text-align:center;" type="submit" value="{{ \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($examSchedule->exam_end_date)->format('d/m/Y') }}" disabled>
                </td>
            </tr>
            <tr>
                <td valign=top style='border:solid windowtext 1.0pt; border-top:none; padding:0cm 5.4pt'>
                    <p align=center><b>CENTER</b></p>
                    <input style="text-align:center;" type="submit" value="{{ $examSchedule->centre ? $examSchedule->centre->centre_name : 'Not specified' }}" disabled>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-top:none; padding:0cm 5.4pt'>
                    <p align=center><b>FILE NUMBER</b></p>
                    <input style="text-align:center;" type="submit" value="{{ $examSchedule->file_no ?? 'N/A' }}" disabled>
                </td>
            </tr>
            <tr>
                <td valign=top style='border:solid windowtext 1.0pt; border-top:none; padding:0cm 5.4pt'>
                    <p align=center><b>PROGRAM NUMBER</b></p>
                    <input style="text-align:center;" type="submit" value="{{ $examSchedule->program_number ?? 'N/A' }}" disabled>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-top:none; padding:0cm 5.4pt'>
                    <p align=center><b>TOTAL STUDENTS</b></p>
                    <input style="text-align:center;" type="submit" value="{{ $examSchedule->total_students ?? 'N/A' }}" disabled>
                </td>
            </tr>
        </table>

        @php
            $theoryModules = $examSchedule->modules->filter(function($module) {
                return $module->is_theory === true;
            });
            $practicalModules = $examSchedule->modules->filter(function($module) {
                return $module->is_theory === false;
            });
            
            // If no modules are categorized, show all modules in one section
            $allModules = null;
            if ($theoryModules->count() === 0 && $practicalModules->count() === 0) {
                $allModules = $examSchedule->modules;
            }
        @endphp

        @if($allModules)
            <p><strong>All Examinations</strong></p>
            <table class="exam-table" border="1" align="center" style="width: 100%; height: 100%" >
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Exam date</th>
                        <th>NOs CODE</th>
                        <th>Subject</th>
                        <th>Venue</th>
                        <th>Timing</th>
                        <th>Invigilator</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allModules as $index => $module)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($module->exam_date)->format('d/m/Y') }}</td>
                            <td>{{ $module->nos_code }}</td>
                            <td>{{ $module->module_name }}</td>
                            <td>{{ $module->venue }}</td>
                            <td>{{ $module->start_time }} - {{ $module->end_time }}</td>
                            <td>{{ $module->invigilator }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            @if($theoryModules->count() > 0)
                <p><strong>Theory</strong></p>
                <table class="exam-table" border="1" align="center" style="width: 100%; height: 100%" >
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Exam date</th>
                            <th>NOs CODE</th>
                            <th>Subject</th>
                            <th>Venue</th>
                            <th>Timing</th>
                            <th>Invigilator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($theoryModules as $index => $module)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($module->exam_date)->format('d/m/Y') }}</td>
                                <td>{{ $module->nos_code }}</td>
                                <td>{{ $module->module_name }}</td>
                                <td>{{ $module->venue }}</td>
                                <td>{{ $module->start_time }} - {{ $module->end_time }}</td>
                                <td>{{ $module->invigilator }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($practicalModules->count() > 0)
                <p><strong>Practical</strong></p>
                <table class="exam-table" border="1" align="center" style="width: 100%; height: 100%" >
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Exam date</th>
                            <th>NOs CODE</th>
                            <th>Subject</th>
                            <th>Venue</th>
                            <th>Timing</th>
                            <th>Invigilator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($practicalModules as $index => $module)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($module->exam_date)->format('d/m/Y') }}</td>
                                <td>{{ $module->nos_code }}</td>
                                <td>{{ $module->module_name }}</td>
                                <td>{{ $module->venue }}</td>
                                <td>{{ $module->start_time }} - {{ $module->end_time }}</td>
                                <td>{{ $module->invigilator }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif

        <table border="1" align="center" style="width: 100%; height: 100%">
            <tr>
                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p align=center><b>EXAM CELL</b></p>
                    @php
                        $examCellStatus = '';
                        
                        switch($examSchedule->status) {
                            case 'submitted':
                                $examCellStatus = 'Pending';
                                break;
                            case 'exam_cell_approved':
                            case 'tc_admin_approved':
                            case 'received':
                                $examCellStatus = 'Approved';
                                break;
                            case 'rejected':
                                $examCellStatus = 'Exam Rejected';
                                break;
                            case 'hold':
                                $examCellStatus = 'Hold';
                                break;
                            default:
                                $examCellStatus = 'Error';
                        }
                    @endphp
                    
                    @if($examCellStatus === 'Scheduled')
                        <input style="text-align:center;" type="submit" value="{{ $examCellStatus }}" disabled></br>
                    @elseif($examCellStatus === 'Hold')
                        <button style="text-align:center;" type="submit" disabled>{{ $examCellStatus }}</button></br>
                    @elseif($examCellStatus === 'Verified')
                        <input style="text-align:center;" type="submit" value="{{ $examCellStatus }}" disabled></br>
                        @if($examCellSignature)
                            {!! $examCellSignature !!}
                        @else
                            <p style="text-align:center; font-size: 12px; margin-top: 5px;">
                                @if($examCellUser)
                                    {{ $examCellUser->name }}
                                @else
                                    Exam Cell
                                @endif
                            </p>
                        @endif
                    @elseif($examCellStatus === 'Exam Rejected')
                        <button style="text-align:center;" type="submit" disabled>{{ $examCellStatus }}</button></br>
                    @else
                        Error
                    @endif
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                    <p align=center><b>MANAGER</b></p>
                    @php
                        $managerStatus = '';
                        
                        if($examSchedule->status === 'tc_admin_approved' || $examSchedule->status === 'received') {
                            $managerStatus = 'Approved';
                        } elseif($examSchedule->status === 'rejected') {
                            $managerStatus = 'Exam Rejected';
                        } else {
                            $managerStatus = 'Not approved yet';
                        }
                    @endphp
                    
                    @if($managerStatus === 'Approved')
                        <input style="text-align:center;" type="submit" value="{{ $managerStatus }}" disabled></br>
                        @if($managerSignature)
                            {!! $managerSignature !!}
                        @else
                            <p style="text-align:center; font-size: 12px; margin-top: 5px;">
                                @if($managerUser)
                                    {{ $managerUser->name }}
                                @else
                                    TC Head
                                @endif
                            </p>
                        @endif
                    @elseif($managerStatus === 'Exam Rejected')
                        <button style="text-align:center;" type="submit" disabled>{{ $managerStatus }}</button></br>
                    @else
                        {{ $managerStatus }}
                    @endif
                </td>
            </tr>
        </table>
        <p align=center>This is a System Generated Exam Schedule</p>
        
        @if($examSchedule->student_details_file)
            <a href="{{ Storage::url($examSchedule->student_details_file) }}" 
               id="studentListLink" 
               download="{{ $examSchedule->course_name }}-{{ $examSchedule->batch_code }}-Student List" 
               target="_blank">
                CLICK HERE TO SEE ELIGIBLE STUDENT LIST
            </a>
        @endif
    </div>
</div>
</body>
</html> 