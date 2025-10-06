@extends('layouts.goi-meta')

@section('content')
<div class="container-fluid py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Back Button -->
                <div class="goi-back-section mb-4">
                    <a href="{{ route('public.exam-schedules') }}" class="goi-back-btn">
                        <i class="fas fa-arrow-left me-2"></i>Back to Exam Schedules
                    </a>
                </div>

                <!-- Exam Schedule Document -->
                <div class="goi-schedule-document">
                    <div align="center" id="printableArea">
                        <table class="MsoTableGrid" border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'> 
                            <tr style='height:auto'>
                                <td colspan=3 valign=top style='border:none; border-bottom:solid windowtext 1.0pt; padding:5mm; height:auto; min-height:80px;'>
                                    <div class="header-layout-container">
                                        @if(isset($headerLayout) && isset($headerLayout->protected_url))
                                            <img src="{{ $headerLayout->protected_url }}" class="header-layout-image" alt="Header Layout" data-protected="true">
                                        @elseif(isset($headerLayout))
                                            <img src="{{ $headerLayout->header_layout_url }}" class="header-layout-image" alt="Header Layout" data-protected="true">
                                        @else
                                            <img src="{{ asset('msme_logo/favicon-96x96.png') }}" class="header-layout-image" alt="Default Logo" data-protected="true">
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr style='height:34.0pt'>
                                <td colspan=3 valign=top style='border:solid windowtext 1.0pt; border-top:none; padding:0cm 5.4pt; height:34.0pt'>
                                    <p class=MsoNormal>It is hereby informed to all <b>{{ $examSchedule->course_name }} - {{ $examSchedule->batch_code }}</b> Batch, that 
                                    @php
                                        $examTypeText = '';
                                        switch($examSchedule->exam_type) {
                                            case 'Internal':
                                                $examTypeText = 'Internal';
                                                break;
                                            case 'Final':
                                                $examTypeText = 'Final';
                                                break;
                                            case 'Special Final':
                                                $examTypeText = 'Special Final Exam';
                                                break;
                                            default:
                                                $examTypeText = 'Error';
                                        }
                                    @endphp
                                    {{ $examTypeText }} SEM: {{ $examSchedule->semester }} Examination is going to be held from <b>{{ \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('d/m/Y') }}</b> as per the following schedule below:
                                    @if($examSchedule->file_no)
                                        <br><strong>File Number: {{ $examSchedule->file_no }}</strong>
                                    @endif
                                    </p>
                                    <p>TC Name: <strong>{{ $tcName }}</strong></p>
                                    <p>Center: 
                                        @php
                                            $centerText = '';
                                            if ($examSchedule->centre) {
                                                $centerText = $examSchedule->centre->centre_name;
                                            } else {
                                                $centerText = 'Centre not specified'; // Fallback text
                                            }
                                        @endphp
                                                        {{ $centerText }} / Program no: {{ $examSchedule->program_number }} /   
                                        @if($examSchedule->centre && $examSchedule->centre->address)
                                        @endif
                                        Notional Hours: {{ $examSchedule->qualification->qf_total_hour ?? 'N/A' }} / NSQF: {{ $examSchedule->qualification->level ?? 'N/A' }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                                    <p class=MsoNormal>EXAMINATION DATE</p>
                                </td>
                                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                                    <p class=MsoNormal>from <b>{{ \Carbon\Carbon::parse($examSchedule->exam_start_date)->format('d/m/Y') }}</b></p>
                                </td>
                                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                                    <p class=MsoNormal>to <b>{{ \Carbon\Carbon::parse($examSchedule->exam_end_date)->format('d/m/Y') }}</b></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                                    <div>
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
                                                            <td class="serial-number">{{ $index + 1 }}</td>
                                                            <td class="date-cell">{{ $module->exam_date ? $module->exam_date->format('d/m/Y') : '' }}</td>
                                                            <td><span class="nos-code">{{ $module->nos_code }}</span></td>
                                                            <td>{{ isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code }}</td>
                                                            <td>{{ $module->venue }}</td>
                                                            <td>{{ $module->start_time ? $module->start_time->format('H:i') : '' }} - {{ $module->end_time ? $module->end_time->format('H:i') : '' }}</td>
                                                            <td>{{ $module->invigilator }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                    <div>
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
                                                            <td class="serial-number">{{ $index + 1 }}</td>
                                                            <td class="date-cell">{{ $module->exam_date ? $module->exam_date->format('d/m/Y') : '' }}</td>
                                                            <td><span class="nos-code">{{ $module->nos_code }}</span></td>
                                                            <td>{{ isset($qualificationModules[$module->nos_code]) ? $qualificationModules[$module->nos_code]->module_name : $module->nos_code }}</td>
                                                            <td>{{ $module->venue }}</td>
                                                            <td>{{ $module->start_time ? $module->start_time->format('H:i') : '' }} - {{ $module->end_time ? $module->end_time->format('H:i') : '' }}</td>
                                                            <td>{{ $module->invigilator }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr style='height:39.7pt'>
                                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                                    <p align=center><b>CO-ORDINATOR</b></p>
                                    
                                    @if($coordinatorSignature)
                                        <div class="signature-protected signature-container">
                                            {!! $coordinatorSignature !!}
                                            <div class="signature-watermark">PROTECTED</div>
                                        </div>
                                    @else
                                        <p style="text-align:center; font-size: 12px; margin-top: 5px;">
                                            @if($coordinatorUser)
                                                {{ $coordinatorUser->name }}
                                            @else
                                                {{ $examSchedule->exam_coordinator }}
                                            @endif
                                        </p>
                                    @endif
                                </td>
                                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                                    <p align=center><b>TC EXAM CELL</b></p>
                                    @php
                                        $examCellStatus = '';
                                        
                                        switch($examSchedule->status) {
                                            case 'draft':
                                            case 'submitted':
                                                $examCellStatus = 'Scheduled';
                                                break;
                                            case 'exam_cell_approved':
                                            case 'tc_admin_approved':
                                            case 'received':
                                                $examCellStatus = 'Verified';
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
                                            <div class="signature-protected signature-container">
                                                {!! $examCellSignature !!}
                                                <div class="signature-watermark">PROTECTED</div>
                                            </div>
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
                                    <p align=center><b>TC APPROVING AUTHORITY</b></p>
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
                                            <div class="signature-protected signature-container">
                                                {!! $managerSignature !!}
                                                <div class="signature-watermark">PROTECTED</div>
                                            </div>
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
                        
                        @if($examSchedule->students()->exists())
                            <!-- <a href="#" 
                               id="studentListLink" 
                               target="_blank">
                                CLICK HERE TO DOWNLOAD ELIGIBLE STUDENT LIST (CSV)
                            </a> -->
                        @endif
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <div style="margin: 10px; font-size: 14px; color: #333; text-align: center;">
                            <strong>
                                Generated on: {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} ({{ \Carbon\Carbon::now()->format('F') }})
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body {
        display: block;
        margin: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        min-height: 100vh;
    }

    .goi-schedule-document {
        max-width: 1200px;
        width: 100%;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
        box-sizing: border-box;
        min-height: auto;
        max-height: none;
        overflow: visible;
        padding-bottom: 50px;
    }

    /* Ensure proper page layout for print dialog */
    body {
        overflow-x: auto;
        min-width: 100vw;
    }

    .container-fluid {
        width: 100%;
        max-width: none;
        overflow-x: visible;
    }

    .container {
        width: 100%;
        max-width: none;
        overflow-x: visible;
    }

    .goi-back-section {
        margin-bottom: 20px;
    }

    .goi-back-btn {
        background-color: #fff !important;
        color: #000 !important;
        border: 2px solid #000 !important;
        font-weight: bold;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
    }

    .goi-back-btn:hover {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        text-decoration: none;
    }

    /* Print orientation buttons styling */
    .print-orientation-buttons {
        margin: 15px 0;
        text-align: center;
        padding: 15px;
        background: #fff !important;
        border-radius: 8px;
        border: 2px solid #000 !important;
    }

    .print-orientation-buttons h6 {
        color: #000 !important;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .print-orientation-buttons button {
        margin: 5px;
        min-width: 150px;
        background-color: #fff !important;
        color: #000 !important;
        border: 2px solid #000 !important;
        font-weight: bold;
        padding: 10px 20px;
    }

    .print-orientation-buttons button:hover {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        border: 2px solid #000 !important;
    }

    @media print {
        .print-orientation-buttons {
            display: none !important;
        }
    }

    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        
        .header-layout-container {
            min-height: 80px;
            max-height: 120px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .header-layout-image {
            max-height: 80px;
            max-width: 100%;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }

        .goi-schedule-document {
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
            font-size: 11px;
        }
        
        .exam-table th,
        .exam-table td {
            padding: 8px 10px;
            font-size: 11px;
        }
    }

    .goi-schedule-document table, 
    .goi-schedule-document td, 
    .goi-schedule-document th {
        border: 1px solid black;
    }

    .goi-schedule-document table {
        border-collapse: collapse;
        width: 100%;
        margin: 10px 0;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
    }

    /* Responsive table for different orientations */
    @media print {
        .table-responsive {
            overflow: visible !important;
        }
        
        table {
            width: 100% !important;
            max-width: none !important;
            table-layout: auto !important;
        }
        
        /* Adjust font sizes for better readability in both orientations */
        .exam-table th,
        .exam-table td {
            font-size: 10px;
            padding: 6px 8px;
            word-wrap: break-word;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Ensure tables fit properly in landscape */
        .exam-table {
            font-size: 9px;
        }
        
        .exam-table th,
        .exam-table td {
            font-size: 9px;
            padding: 4px 6px;
        }
        
        /* Header image optimization for print */
        .header-layout-container {
            width: 100% !important;
            max-width: none !important;
        }
        
        .header-layout-image {
            max-width: 100% !important;
            max-height: 80px !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
        }
        
        /* Ensure table cell doesn't constrain the image */
        table tr:first-child td {
            height: auto !important;
            min-height: 80px !important;
            padding: 5mm !important;
        }
        
        /* Fixed signature dimensions for print */
        .signature-container {
            width: 120px !important;
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            margin: 0 auto !important;
        }
        
        .signature-container img,
        .signature-container canvas {
            max-width: 100% !important;
            max-height: 100% !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
        }
        
        /* Center signature-protected containers in print */
        .signature-protected {
            margin: 0 auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #fff !important;
            border: 1px solid #000 !important;
        }
        
        .signature-container img,
        .signature-container canvas {
            filter: grayscale(100%) contrast(120%) !important;
            -webkit-filter: grayscale(100%) contrast(120%) !important;
        }
    }

    .goi-schedule-document th {
        background-color: #fff !important;
        font-weight: 600;
        padding: 12px 16px;
        text-align: center;
        font-size: 14px;
        color: #000 !important;
        border-color: #000 !important;
    }

    .goi-schedule-document td {
        padding: 12px 16px;
        text-align: center;
        font-size: 14px;
        vertical-align: middle;
    }

    .goi-schedule-document tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .goi-schedule-document tr:hover {
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
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }

    /* Additional styling for better readability */
    .exam-table th {
        background-color: #fff !important;
        font-weight: 600;
        padding: 8px 12px;
        text-align: center;
        font-size: 11px;
        color: #000 !important;
        border-color: #000 !important;
    }

    .exam-table td {
        padding: 8px 12px;
        text-align: center;
        font-size: 11px;
        vertical-align: middle;
        background-color: #fff !important;
        color: #000 !important;
        border-color: #000 !important;
    }

    .exam-table tr:nth-child(even) {
        background-color: #fff !important;
    }

    .exam-table tr:hover {
        background-color: #fff !important;
    }

    /* Serial number styling */
    .serial-number {
        font-weight: 600;
        color: #000 !important;
    }

    /* Date styling */
    .date-cell {
        font-weight: 500;
        color: #000 !important;
    }

    /* NOS code styling */
    .nos-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #000 !important;
        background-color: #fff !important;
        padding: 4px 8px;
        border-radius: 4px;
    }

    /* Responsive styles */
    @media (max-width: 1200px) {
        .goi-schedule-document {
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

    /* Ensure print dialog has enough space */
    @media screen {
        html, body {
            width: 100%;
            min-width: 100vw;
            overflow-x: auto;
        }
        
        .container-fluid {
            width: 100%;
            min-width: 100vw;
            overflow-x: visible;
        }
        
        .goi-schedule-document {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
        }
    }

    @media (max-width: 768px) {
        .goi-schedule-document {
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
            padding: 3px 0;
        }

        .header-layout-image {
            max-height: 40px;
        }
    }

    /* Signature protection styles */
    .signature-protected {
        position: relative;
        display: flex;
        background: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(0, 0, 0, 0.05) 10px,
            rgba(0, 0, 0, 0.05) 20px
        );
        border: 1px solid #000;
        padding: 5px;
        border-radius: 3px;
        width: 130px;
        height: 70px;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background-color: #fff !important;
    }

    .signature-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 8px;
        color: rgba(0, 0, 0, 0.4);
        font-weight: bold;
        z-index: 2;
        pointer-events: none;
        user-select: none;
        white-space: nowrap;
        text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.8);
    }

    .signature-container {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
        width: 120px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin: 0 auto;
    }

    /* Signature image sizing */
    .signature-container img,
    .signature-container canvas {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        filter: grayscale(100%) contrast(120%);
        -webkit-filter: grayscale(100%) contrast(120%);
    }

    /* Disable image dragging */
    .signature-protected img,
    canvas[data-protected="true"] {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
        pointer-events: none;
    }

    /* Canvas protection styles */
    canvas[data-protected="true"] {
        display: block;
        margin: 0 auto;
    }

    /* Black and White Official Document Styling - Only for exam schedule document */
    .goi-schedule-document * {
        color: #000 !important;
        background-color: transparent !important;
    }
    
    .goi-schedule-document table {
        background-color: #fff !important;
        color: #000 !important;
    }
    
    .goi-schedule-document td, 
    .goi-schedule-document th {
        background-color: #fff !important;
        color: #000 !important;
        border-color: #000 !important;
    }
    
    .goi-schedule-document .btn {
        background-color: #fff !important;
        color: #000 !important;
        border: 1px solid #000 !important;
    }
    
    .goi-schedule-document .btn:hover {
        background-color: #f0f0f0 !important;
        color: #000 !important;
    }

    /* Hide elements when printing */
    @media print {
        .no-print {
            display: none !important;
        }
        
        #studentListLink {
            display: none !important;
        }
        
        /* Hide Government header and navigation when printing */
        .goi-header-unique {
            display: none !important;
        }
        
        .goi-navigation {
            display: none !important;
        }
        
        .goi-footer {
            display: none !important;
        }
        
        /* Hide back button when printing */
        .goi-back-section {
            display: none !important;
        }
        
        /* Hide the main container padding and margins for print */
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .container {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        /* Make the exam schedule document take full page */
        .goi-schedule-document {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background-color: white !important;
        }
        
        /* Hide any other header elements */
        header {
            display: none !important;
        }
        
        nav {
            display: none !important;
        }
        
        /* Ensure only the schedule content is visible */
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Print orientation functionality
function printWithOrientation(orientation = 'portrait') {
    // Ensure page layout is proper before printing
    document.body.style.overflow = 'visible';
    document.documentElement.style.overflow = 'visible';
    
    // Create a style element to set print orientation
    const style = document.createElement('style');
    style.id = 'print-orientation-style';
    
    // Remove any existing print orientation style
    const existingStyle = document.getElementById('print-orientation-style');
    if (existingStyle) {
        document.head.removeChild(existingStyle);
    }
    
    style.textContent = `
        @media print {
            @page {
                size: A4 ${orientation};
                margin: 15mm;
            }
            
            body {
                margin: 0;
                padding: 0;
            }
            
            .goi-schedule-document {
                margin: 0 !important;
                padding: 15mm !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background-color: white !important;
                width: 100% !important;
                max-width: none !important;
            }
            
            table {
                width: 100% !important;
                font-size: ${orientation === 'landscape' ? '10px' : '11px'};
            }
            
            .exam-table th,
            .exam-table td {
                padding: ${orientation === 'landscape' ? '4px 6px' : '6px 8px'};
                font-size: ${orientation === 'landscape' ? '9px' : '10px'};
            }
        }
    `;
    
    document.head.appendChild(style);
    
    // Small delay to ensure styles are applied
    setTimeout(() => {
        window.print();
    }, 100);
    
    // Remove the style after printing
    setTimeout(() => {
        const styleToRemove = document.getElementById('print-orientation-style');
        if (styleToRemove) {
            document.head.removeChild(styleToRemove);
        }
        // Restore original overflow settings
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    }, 2000);
}

// Add print buttons for different orientations
const container = document.querySelector('.goi-schedule-document');
if (container) {
    const orientationButtons = document.createElement('div');
    orientationButtons.className = 'print-orientation-buttons';
    orientationButtons.innerHTML = `
        <h6 style="margin-bottom: 15px; color: #000 !important; font-weight: bold;">Choose Print Orientation:</h6>
        <button onclick="printWithOrientation('portrait')" style="background-color: #fff !important; color: #000 !important; border: 2px solid #000 !important; font-weight: bold; padding: 10px 20px; margin: 5px; min-width: 150px;">
            🖨️ Print Portrait
        </button>
        <button onclick="printWithOrientation('landscape')" style="background-color: #fff !important; color: #000 !important; border: 2px solid #000 !important; font-weight: bold; padding: 10px 20px; margin: 5px; min-width: 150px;">
            🖨️ Print Landscape
        </button>
        <button onclick="window.print()" style="background-color: #fff !important; color: #000 !important; border: 2px solid #000 !important; font-weight: bold; padding: 10px 20px; margin: 5px; min-width: 150px;">
            🖨️ Print (Browser Default)
        </button>
    `;
    container.appendChild(orientationButtons);
}
</script>
@endpush
@endsection