<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule - {{ $examSchedule->course_name }} - {{ $examSchedule->batch_code }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 18px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-item strong {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        
        .exam-table th,
        .exam-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        
        .exam-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .exam-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .section-title {
            background-color: #333;
            color: white;
            padding: 10px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            text-align: center;
        }
        
        .approval-section {
            margin-top: 40px;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        
        .approval-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .approval-item {
            border: 1px solid #333;
            padding: 15px;
            text-align: center;
        }
        
        .approval-item h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            
            .exam-table {
                font-size: 10px;
            }
            
            .exam-table th,
            .exam-table td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EXAMINATION SCHEDULE</h1>
        <h2>{{ $examSchedule->course_name }} - {{ $examSchedule->batch_code }}</h2>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <strong>Examination Type:</strong>
                {{ $examSchedule->exam_type }} SEM: {{ $examSchedule->semester }}
            </div>
            <div class="info-item">
                <strong>Examination Period:</strong>
                {{ \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($examSchedule->exam_end_date)->format('d/m/Y') }}
            </div>
            <div class="info-item">
                <strong>Center:</strong>
                {{ $examSchedule->centre ? $examSchedule->centre->centre_name : 'Not specified' }}
            </div>
            <div class="info-item">
                <strong>File Number:</strong>
                {{ $examSchedule->file_no ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>Program Number:</strong>
                {{ $examSchedule->program_number ?? 'N/A' }}
            </div>
            <div class="info-item">
                <strong>Total Students:</strong>
                {{ $examSchedule->total_students ?? 'N/A' }}
            </div>
        </div>
    </div>

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
        <div class="section-title">ALL EXAMINATIONS</div>
        <table class="exam-table">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Date</th>
                    <th>NOS Code</th>
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
                        <td>{{ $module->exam_date ? $module->exam_date->format('d/m/Y') : '' }}</td>
                        <td>{{ $module->nos_code }}</td>
                        <td>{{ $module->module_name }}</td>
                        <td>{{ $module->venue }}</td>
                        <td>{{ $module->start_time ? $module->start_time->format('H:i') : '' }} - {{ $module->end_time ? $module->end_time->format('H:i') : '' }}</td>
                        <td>{{ $module->invigilator }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @if($theoryModules->count() > 0)
            <div class="section-title">THEORY EXAMINATIONS</div>
            <table class="exam-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date</th>
                        <th>NOS Code</th>
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
                            <td>{{ $module->exam_date ? $module->exam_date->format('d/m/Y') : '' }}</td>
                            <td>{{ $module->nos_code }}</td>
                            <td>{{ $module->module_name }}</td>
                            <td>{{ $module->venue }}</td>
                            <td>{{ $module->start_time ? $module->start_time->format('H:i') : '' }} - {{ $module->end_time ? $module->end_time->format('H:i') : '' }}</td>
                            <td>{{ $module->invigilator }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($practicalModules->count() > 0)
            <div class="section-title">PRACTICAL EXAMINATIONS</div>
            <table class="exam-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Date</th>
                        <th>NOS Code</th>
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
                            <td>{{ $module->exam_date ? $module->exam_date->format('d/m/Y') : '' }}</td>
                            <td>{{ $module->nos_code }}</td>
                            <td>{{ $module->module_name }}</td>
                            <td>{{ $module->venue }}</td>
                            <td>{{ $module->start_time ? $module->start_time->format('H:i') : '' }} - {{ $module->end_time ? $module->end_time->format('H:i') : '' }}</td>
                            <td>{{ $module->invigilator }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif

    <div class="approval-section">
        <div class="section-title">APPROVAL STATUS</div>
        <div class="approval-grid">
            <div class="approval-item">
                <h4>EXAM CELL</h4>
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
                            $examCellStatus = 'Rejected';
                            break;
                        case 'hold':
                            $examCellStatus = 'On Hold';
                            break;
                        default:
                            $examCellStatus = 'Not Submitted';
                    }
                @endphp
                <strong>{{ $examCellStatus }}</strong>
            </div>
            
            <div class="approval-item">
                <h4>TC HEAD</h4>
                @php
                    $tcHeadStatus = '';
                    if(in_array($examSchedule->status, ['tc_admin_approved', 'received'])) {
                        $tcHeadStatus = 'Approved';
                    } elseif($examSchedule->status === 'rejected') {
                        $tcHeadStatus = 'Rejected';
                    } else {
                        $tcHeadStatus = 'Pending';
                    }
                @endphp
                <strong>{{ $tcHeadStatus }}</strong>
            </div>
            
            <div class="approval-item">
                <h4>ASSESSMENT AGENCY</h4>
                @php
                    $aaStatus = '';
                    if($examSchedule->status === 'received') {
                        $aaStatus = 'Approved';
                    } elseif($examSchedule->status === 'rejected') {
                        $aaStatus = 'Rejected';
                    } else {
                        $aaStatus = 'Pending';
                    }
                @endphp
                <strong>{{ $aaStatus }}</strong>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a System Generated Exam Schedule</p>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 