<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule of {{ $examSchedule->course_name }}-{{ $examSchedule->batch_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: block;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
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
            size: A4;
            margin: 15mm;
        }

        @media print {
            @page {
                size: A4;
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
                font-size: 11px;
            }
            
            .exam-table th,
            .exam-table td {
                padding: 8px 10px;
                font-size: 11px;
            }
        }

        table, td, th {
            border: 1px solid black;
        }

        table {
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
            }
            
            /* Adjust font sizes for better readability in both orientations */
            .exam-table th,
            .exam-table td {
                font-size: 10px;
                padding: 6px 8px;
                word-wrap: break-word;
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

        th {
            background-color: #fff !important;
            font-weight: 600;
            padding: 12px 16px;
            text-align: center;
            font-size: 14px;
            color: #000 !important;
            border-color: #000 !important;
        }

        td {
            padding: 12px 16px;
            text-align: center;
            font-size: 14px;
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

        /* Print button styles */
        .styled {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
        }

        .styled:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .styled:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

                /* Print protection - add watermarks in print */
        @media print {
            .signature-watermark { 
                display: block !important; 
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 12px;
                font-weight: bold;
                color: rgba(255, 0, 0, 0.7);
                background: rgba(255, 255, 255, 0.9);
                padding: 2px 6px;
                border-radius: 3px;
                z-index: 1000;
                pointer-events: none;
            }
            .signature-protected { 
                position: relative;
                background: rgba(255, 255, 255, 0.95) !important; 
                border: 2px solid #dc3545 !important; 
                border-radius: 5px;
                padding: 5px;
            }
            
            /* Add print watermark to signatures */
            .signature-container::before {
                content: "";
                position: absolute;
                top: 5px;
                right: 5px;
                background: rgba(220, 53, 69, 0.9);
                color: white;
                padding: 2px 6px;
                font-size: 10px;
                font-weight: bold;
                border-radius: 3px;
                z-index: 1001;
            }
            
            /* Add diagonal watermark overlay */
            .signature-container::after {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 10px,
                    rgba(220, 53, 69, 0.1) 10px,
                    rgba(220, 53, 69, 0.1) 20px
                );
                pointer-events: none;
                z-index: 999;
            }
            
            /* Hide print button when printing */
            button[onclick*="window.print"] {
                display: none !important;
            }
            
            /* Ensure proper page layout */
            body {
                margin: 0;
                padding: 0;
            }
            
            /* Black and White Print Optimization */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                color: #000 !important;
                background-color: transparent !important;
            }
            
            body {
                background-color: #fff !important;
                color: #000 !important;
            }
            
            table, td, th {
                background-color: #fff !important;
                color: #000 !important;
                border-color: #000 !important;
            }
            
            /* Add page watermark */
            body::before {
                content: "OFFICIAL DOCUMENT - DO NOT COPY";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 24px;
                font-weight: bold;
                color: rgba(220, 53, 69, 0.3);
                z-index: 1;
                pointer-events: none;
                white-space: nowrap;
            }
        }

        /* Disable text selection globally */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-tap-highlight-color: transparent;
        }

        /* Allow text selection only for specific elements */
        .allow-select {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }

        /* Black and White Official Document Styling */
        * {
            color: #000 !important;
            background-color: transparent !important;
        }
        
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
        
        table {
            background-color: #fff !important;
            color: #000 !important;
        }
        
        td, th {
            background-color: #fff !important;
            color: #000 !important;
            border-color: #000 !important;
        }
        
        .btn {
            background-color: #fff !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        
        .btn:hover {
            background-color: #f0f0f0 !important;
            color: #000 !important;
        }
        
        .alert {
            background-color: #fff !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        
        .modal-content {
            background-color: #fff !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        
        .modal-header {
            background-color: #fff !important;
            color: #000 !important;
            border-bottom: 1px solid #000 !important;
        }
        
        .modal-body {
            background-color: #fff !important;
            color: #000 !important;
        }
        
        .modal-footer {
            background-color: #fff !important;
            color: #000 !important;
            border-top: 1px solid #000 !important;
        }

        /* Hide elements when printing */
        @media print {
            .no-print {
                display: none !important;
            }
            
            #studentListLink {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<!-- Security Notice -->
<!-- <div style="
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #dc3545;
    color: white;
    text-align: center;
    padding: 8px;
    font-size: 12px;
    font-weight: bold;
    z-index: 9999;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
">
    🔒 SECURE DOCUMENT - Developer tools, right-click, and keyboard shortcuts are disabled. This page is protected against unauthorized access.
</div> -->

<div class="container" style="margin-top: 40px;">
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
                        @php
                            $theoryModules = $examSchedule->modules->where('is_theory', true);
                            $practicalModules = $examSchedule->modules->where('is_theory', false);
                        @endphp
                        
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
        <!-- <p align=center style="font-size: 10px; color: #666; margin-top: 10px;">
            <strong>⚠️ Signature Protection:</strong> Digital signatures on this document are protected against unauthorized copying and misuse. 
            Any attempt to copy, download, or manipulate these signatures may constitute forgery and is strictly prohibited.
            <br><strong>Security:</strong> All signature access is logged and monitored. Direct URL access is blocked.
        </p> -->
        
        @if($examSchedule->students()->exists())
            @php
                $downloadRoute = '';
                switch($user->user_role) {
                    case 5: // Faculty
                        $downloadRoute = route('admin.faculty.exam-schedules.download-eligible-students', $examSchedule->id);
                        break;
                    case 3: // Exam Cell
                        $downloadRoute = route('admin.exam-cell.exam-schedules.download-eligible-students', $examSchedule->id);
                        break;
                    case 1: // TC Admin
                        $downloadRoute = route('admin.tc-admin.exam-schedules.download-eligible-students', $examSchedule->id);
                        break;
                    case 2: // TC Head
                        $downloadRoute = route('admin.tc-head.exam-schedules.download-eligible-students', $examSchedule->id);
                        break;
                    case 4: // Assessment Agency
                        $downloadRoute = route('admin.aa.exam-schedules.download-eligible-students', $examSchedule->id);
                        break;
                    default:
                        $downloadRoute = route('admin.faculty.exam-schedules.download-eligible-students', $examSchedule->id);
                }
            @endphp
            <a href="{{ $downloadRoute }}" 
               id="studentListLink" 
               target="_blank">
                CLICK HERE TO DOWNLOAD ELIGIBLE STUDENT LIST (CSV)
            </a>
        @endif
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <!-- <a href="{{ route('admin.aa.exam-schedules.download', $examSchedule->id) }}" 
           class="styled" 
           style="text-decoration: none; display: inline-block; margin: 10px;">
            📄 Download Exam Schedule (PDF)
        </a>
        
        <a href="{{ route('admin.aa.exam-schedules.download-excel', $examSchedule->id) }}" 
           class="styled" 
           style="text-decoration: none; display: inline-block; margin: 10px; background-color: #28a745;">
            📊 Download Exam Schedule (Excel)
        </a> -->

        <div style="margin: 10px; font-size: 14px; color: #333; text-align: center;">
            <strong>
                Generated on: {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }} ({{ \Carbon\Carbon::now()->format('F') }})
            </strong>
        </div>

        
        <button onclick="console.log('Print button clicked'); window.print();" class="styled" style="margin: 10px; background-color: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#0056b3'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';" onmouseout="this.style.backgroundColor='#007bff'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
            🖨️ Print Exam Schedule
        </button>
    </div>
</div>

<script>
// Global print protection function - must be defined before DOM loads
function addPrintProtection() {
    console.log('Adding print protection to signatures...');
    
    // Add canvas overlays to signatures
    const signatures = document.querySelectorAll('.signature-container img, .signature-container canvas');
    signatures.forEach((signature, index) => {
        const container = signature.closest('.signature-container');
        if (container) {
            // Create canvas overlay
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // Set canvas size to match signature
            canvas.width = signature.offsetWidth;
            canvas.height = signature.offsetHeight;
            canvas.style.position = 'absolute';
            canvas.style.top = '0';
            canvas.style.left = '0';
            canvas.style.pointerEvents = 'none';
            canvas.style.zIndex = '1000';
            
            // Add watermark text
            ctx.font = '12px Arial';
            ctx.fillStyle = 'rgba(220, 53, 69, 0.8)';
            ctx.textAlign = 'center';
            ctx.fillText('PROTECTED', canvas.width / 2, canvas.height / 2);
            
            // Add diagonal lines
            ctx.strokeStyle = 'rgba(220, 53, 69, 0.3)';
            ctx.lineWidth = 1;
            for (let i = 0; i < canvas.width; i += 20) {
                ctx.beginPath();
                ctx.moveTo(i, 0);
                ctx.lineTo(i + 20, canvas.height);
                ctx.stroke();
            }
            
            // Add to container
            container.style.position = 'relative';
            container.appendChild(canvas);
            
            console.log(`Added protection to signature ${index + 1}`);
        }
    });
    
    // Add timestamp watermark
    const timestamp = new Date().toLocaleString();
    const timestampDiv = document.createElement('div');
    timestampDiv.style.position = 'fixed';
    timestampDiv.style.bottom = '10px';
    timestampDiv.style.right = '10px';
    timestampDiv.style.background = 'rgba(220, 53, 69, 0.9)';
    timestampDiv.style.color = 'white';
    timestampDiv.style.padding = '5px 10px';
    timestampDiv.style.fontSize = '12px';
    timestampDiv.style.fontWeight = 'bold';
    timestampDiv.style.borderRadius = '3px';
    timestampDiv.style.zIndex = '10000';
    timestampDiv.textContent = `Printed: ${timestamp}`;
    document.body.appendChild(timestampDiv);
    
    console.log('Print protection added successfully');
}

// Make function globally accessible
window.addPrintProtection = addPrintProtection;

// Global print function - must be defined before DOM loads
function printDocument() {
    try {
        console.log('Print function called - starting print process...');
        
        // Temporarily disable all protection for printing
        const originalKeydown = document.onkeydown;
        const originalContextmenu = document.oncontextmenu;
        const originalSelectstart = document.onselectstart;
        const originalDragstart = document.ondragstart;
        const originalCopy = document.oncopy;
        const originalPaste = document.onpaste;
        
        // Remove all event listeners temporarily
        document.onkeydown = null;
        document.oncontextmenu = null;
        document.onselectstart = null;
        document.ondragstart = null;
        document.oncopy = null;
        document.onpaste = null;
        
        // Hide security notice during print
        const securityNotice = document.querySelector('div[style*="background: #dc3545"]');
        if (securityNotice) {
            securityNotice.style.display = 'none';
        }
        
        // Hide any protection alerts
        const alerts = document.querySelectorAll('div[style*="position: fixed"][style*="z-index: 10000"]');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
        
        // Enable text selection for print
        document.body.style.userSelect = 'auto';
        document.body.style.webkitUserSelect = 'auto';
        document.body.style.mozUserSelect = 'auto';
        document.body.style.msUserSelect = 'auto';
        
        console.log('Print preparation complete - opening print dialog...');
        
        // Print
        window.print();
        
        console.log('Print dialog opened - restoring protection...');
        
        // Restore protection after printing
        setTimeout(() => {
            document.onkeydown = originalKeydown;
            document.oncontextmenu = originalContextmenu;
            document.onselectstart = originalSelectstart;
            document.ondragstart = originalDragstart;
            document.oncopy = originalCopy;
            document.onpaste = originalPaste;
            
            // Restore security notice
            if (securityNotice) {
                securityNotice.style.display = 'block';
            }
            
            // Restore text selection blocking
            document.body.style.userSelect = 'none';
            document.body.style.webkitUserSelect = 'none';
            document.body.style.mozUserSelect = 'none';
            document.body.style.msUserSelect = 'none';
            
            // Show success message using simple alert
            alert('✅ Print dialog opened successfully!');
            console.log('Print process completed successfully');
        }, 2000);
        
    } catch (error) {
        console.error('Print error:', error);
        alert('❌ Error opening print dialog. Please try again.');
    }
}

// Make function globally accessible
window.printDocument = printDocument;

// Add fallback function in case of issues
window.fallbackPrint = function() {
    console.log('Fallback print function called');
    try {
        window.print();
        alert('✅ Print dialog opened using fallback method!');
    } catch (error) {
        console.error('Fallback print error:', error);
        // Try direct print as last resort
        try {
            console.log('Trying direct print as last resort...');
            window.print();
            alert('✅ Print dialog opened using direct method!');
        } catch (directError) {
            console.error('Direct print also failed:', directError);
            alert('❌ Print failed. Please try refreshing the page.');
        }
    }
};


            
            // Verify functions are accessible
            console.log('printDocument function defined:', typeof printDocument);
            console.log('window.printDocument function defined:', typeof window.printDocument);
            console.log('fallbackPrint function defined:', typeof window.fallbackPrint);
            console.log('addPrintProtection function defined:', typeof addPrintProtection);
            console.log('window.addPrintProtection function defined:', typeof window.addPrintProtection);
            
            // Test function immediately
            if (typeof printDocument === 'function') {
                console.log('✅ Print function is available and ready to use');
            } else {
                console.error('❌ Print function is not available');
            }
            
            if (typeof window.fallbackPrint === 'function') {
                console.log('✅ Fallback print function is available and ready to use');
            } else {
                console.error('❌ Fallback print function is not available');
            }
            
            if (typeof addPrintProtection === 'function') {
                console.log('✅ Print protection function is available and ready to use');
            } else {
                console.error('❌ Print protection function is not available');
            }
            
            if (typeof window.addPrintProtection === 'function') {
                console.log('✅ Global print protection function is available and ready to use');
            } else {
                console.error('❌ Global print protection function is not available');
            }

// Main print handler function
window.handlePrint = function() {
    console.log('handlePrint function called');
    
    // Try the main print function first
    if (typeof printDocument === 'function') {
        try {
            console.log('Calling printDocument()...');
            printDocument();
        } catch (error) {
            console.error('printDocument failed:', error);
            // Try fallback
            if (typeof window.fallbackPrint === 'function') {
                console.log('Trying fallback print...');
                window.fallbackPrint();
            } else {
                console.error('Both print functions failed');
                alert('❌ Print function not available. Please refresh the page.');
            }
        }
    } else if (typeof window.fallbackPrint === 'function') {
        console.log('printDocument not available, using fallback...');
        window.fallbackPrint();
    } else {
        console.error('No print functions available');
        alert('❌ Print function not available. Please refresh the page.');
    }
};

// Comprehensive page protection JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Disable right-click context menu globally
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        showProtectionAlert('Right-click is disabled on this page.');
        return false;
    });

    // Disable keyboard shortcuts for developer tools
    document.addEventListener('keydown', function(e) {
        // F12 key
        if (e.key === 'F12') {
            e.preventDefault();
            showProtectionAlert('Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+I (Chrome DevTools)
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            showProtectionAlert('Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+J (Chrome Console)
        if (e.ctrlKey && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            showProtectionAlert('Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+C (Chrome Element Inspector)
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            showProtectionAlert('Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+U (View Source)
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            showProtectionAlert('Page source viewing is disabled.');
            return false;
        }

        // Ctrl+Shift+U (Firefox View Source)
        if (e.ctrlKey && e.shiftKey && e.key === 'U') {
            e.preventDefault();
            showProtectionAlert('Page source viewing is disabled.');
            return false;
        }

        // Ctrl+S (Save Page)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            showProtectionAlert('Page saving is disabled.');
            return false;
        }

        // Ctrl+P (Print) - Allow printing through our custom function
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            // Simple print
            window.print();
            return false;
        }

        // PrintScreen key
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            showProtectionAlert('Screenshots are disabled on this page.');
            return false;
        }

        // Escape key (to prevent closing dialogs)
        if (e.key === 'Escape') {
            e.preventDefault();
            return false;
        }

        // Additional shortcuts for comprehensive blocking
        // Ctrl+Shift+E (Firefox DevTools)
        if (e.ctrlKey && e.shiftKey && e.key === 'E') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+K (Firefox Console)
        if (e.ctrlKey && e.shiftKey && e.key === 'K') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+M (Firefox Responsive)
        if (e.ctrlKey && e.shiftKey && e.key === 'M') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+A (Firefox Network)
        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+O (Firefox Debugger)
        if (e.ctrlKey && e.shiftKey && e.key === 'O') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+S (Firefox Style Editor)
        if (e.ctrlKey && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+Z (Firefox Performance)
        if (e.ctrlKey && e.shiftKey && e.key === 'Z') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+Y (Firefox Storage)
        if (e.ctrlKey && e.shiftKey && e.key === 'Y') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+Shift+H (Firefox Application)
        if (e.ctrlKey && e.shiftKey && e.key === 'H') {
            e.preventDefault();
            showProtectionAlert('⚠️ Developer tools are disabled on this page.');
            return false;
        }

        // Ctrl+F (Find)
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            showProtectionAlert('⚠️ Find is disabled on this page.');
            return false;
        }

        // Ctrl+A (Select All)
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            showProtectionAlert('⚠️ Select all is disabled on this page.');
            return false;
        }

        // Ctrl+X (Cut)
        if (e.ctrlKey && e.key === 'x') {
            e.preventDefault();
            showProtectionAlert('⚠️ Cut is disabled on this page.');
            return false;
        }

        // Ctrl+C (Copy)
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            showProtectionAlert('⚠️ Copy is disabled on this page.');
            return false;
        }

        // Ctrl+V (Paste)
        if (e.ctrlKey && e.key === 'v') {
            e.preventDefault();
            showProtectionAlert('⚠️ Paste is disabled on this page.');
            return false;
        }

        // Ctrl+Z (Undo)
        if (e.ctrlKey && e.key === 'z') {
            e.preventDefault();
            showProtectionAlert('⚠️ Undo is disabled on this page.');
            return false;
        }

        // Ctrl+Y (Redo)
        if (e.ctrlKey && e.key === 'y') {
            e.preventDefault();
            showProtectionAlert('⚠️ Redo is disabled on this page.');
            return false;
        }

        // Ctrl+D (Bookmark)
        if (e.ctrlKey && e.key === 'd') {
            e.preventDefault();
            showProtectionAlert('⚠️ Bookmarking is disabled on this page.');
            return false;
        }

        // Ctrl+L (Address bar)
        if (e.ctrlKey && e.key === 'l') {
            e.preventDefault();
            showProtectionAlert('⚠️ Address bar access is disabled on this page.');
            return false;
        }

        // Ctrl+R (Refresh)
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            showProtectionAlert('⚠️ Refresh is disabled on this page.');
            return false;
        }

        // Ctrl+Shift+R (Hard Refresh)
        if (e.ctrlKey && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            showProtectionAlert('⚠️ Hard refresh is disabled on this page.');
            return false;
        }

        // Ctrl+W (Close Tab)
        if (e.ctrlKey && e.key === 'w') {
            e.preventDefault();
            showProtectionAlert('⚠️ Closing tab is disabled on this page.');
            return false;
        }

        // Ctrl+T (New Tab)
        if (e.ctrlKey && e.key === 't') {
            e.preventDefault();
            showProtectionAlert('⚠️ New tab is disabled on this page.');
            return false;
        }

        // Ctrl+N (New Window)
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            showProtectionAlert('⚠️ New window is disabled on this page.');
            return false;
        }

        // Ctrl+Shift+N (New Incognito Window)
        if (e.ctrlKey && e.shiftKey && e.key === 'N') {
            e.preventDefault();
            showProtectionAlert('⚠️ New incognito window is disabled on this page.');
            return false;
        }
    });

    // Disable drag and drop globally (but allow images to load)
    document.addEventListener('dragstart', function(e) {
        // Allow images to load but prevent dragging
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
        e.preventDefault();
        return false;
    });

    // Disable text selection globally (but allow some elements)
    document.addEventListener('selectstart', function(e) {
        // Allow selection in specific areas if needed
        if (e.target.closest('.allow-select')) {
            return true;
        }
        e.preventDefault();
        return false;
    });

    // Disable copy functionality
    document.addEventListener('copy', function(e) {
        e.preventDefault();
        showProtectionAlert('Copying is disabled on this page.');
        return false;
    });

    // Disable cut functionality
    document.addEventListener('cut', function(e) {
        e.preventDefault();
        showProtectionAlert('Cutting is disabled on this page.');
        return false;
    });

    // Disable paste functionality
    document.addEventListener('paste', function(e) {
        e.preventDefault();
        showProtectionAlert('Pasting is disabled on this page.');
        return false;
    });

    // Enhanced developer tools detection
    let devtools = {
        open: false,
        orientation: null
    };

    // Method 1: Size-based detection
    setInterval(function() {
        const threshold = 160;
        const widthThreshold = window.outerWidth - window.innerWidth > threshold;
        const heightThreshold = window.outerHeight - window.innerHeight > threshold;
        
        if (widthThreshold || heightThreshold) {
            if (!devtools.open) {
                devtools.open = true;
                devtools.orientation = widthThreshold ? 'vertical' : 'horizontal';
                showProtectionAlert('⚠️ Developer tools detected! This page is protected.');
                // Block access completely
                document.body.innerHTML = '<div style="text-align:center; padding:50px; font-size:24px; color:red; background:white; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">⚠️ ACCESS BLOCKED - Developer tools detected</div>';
            }
        } else {
            devtools.open = false;
            devtools.orientation = null;
        }
    }, 300);

    // Method 2: Performance-based detection
    function detectDevTools() {
        const start = performance.now();
        debugger;
        const end = performance.now();
        if (end - start > 100) {
            showProtectionAlert('⚠️ Developer tools detected! This page is protected.');
            document.body.innerHTML = '<div style="text-align:center; padding:50px; font-size:24px; color:red; background:white; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">⚠️ ACCESS BLOCKED - Developer tools detected</div>';
        }
    }
    
    setInterval(detectDevTools, 500);

    // Method 3: Console detection
    let devtoolsOpen = false;
    const devtoolsCheck = {
        open: false,
        orientation: null
    };

    setInterval(() => {
        const threshold = 160;
        if (window.outerHeight - window.innerHeight > threshold || window.outerWidth - window.innerWidth > threshold) {
            if (!devtoolsOpen) {
                devtoolsOpen = true;
                showProtectionAlert('⚠️ Developer tools detected! This page is protected.');
                document.body.innerHTML = '<div style="text-align:center; padding:50px; font-size:24px; color:red; background:white; position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;">⚠️ ACCESS BLOCKED - Developer tools detected</div>';
            }
        } else {
            devtoolsOpen = false;
        }
    }, 300);

    // Disable console access completely
    console.log = function() {};
    console.warn = function() {};
    console.error = function() {};
    console.info = function() {};
    console.debug = function() {};
    console.trace = function() {};
    console.table = function() {};
    console.group = function() {};
    console.groupEnd = function() {};
    console.time = function() {};
    console.timeEnd = function() {};
    console.count = function() {};
    console.clear = function() {};
    console.dir = function() {};
    console.dirxml = function() {};
    console.assert = function() {};
    console.profile = function() {};
    console.profileEnd = function() {};
    console.timeStamp = function() {};
    console.timeline = function() {};
    console.timelineEnd = function() {};
    console.memory = function() {};
    console.markTimeline = function() {};
    console.measure = function() {};
    console.takeHeapSnapshot = function() {};
    console.pause = function() {};
    console.resume = function() {};

    // Override window.open to prevent new windows
    const originalWindowOpen = window.open;
    window.open = function() {
        showProtectionAlert('⚠️ Opening new windows is disabled on this page.');
        return null;
    };

    // Override window.location to prevent navigation
    const originalLocation = window.location;
    Object.defineProperty(window, 'location', {
        get: function() {
            return originalLocation;
        },
        set: function(value) {
            showProtectionAlert('⚠️ Navigation is disabled on this page.');
            return false;
        }
    });

    // Prevent iframe embedding
    if (window.self !== window.top) {
        window.top.location = window.self.location;
    }

    // Additional protection against source viewing
    // Override document.documentElement.outerHTML
    Object.defineProperty(document.documentElement, 'outerHTML', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return '<html><body>⚠️ ACCESS DENIED - HTML source is protected</body></html>';
        }
    });

    // Override document.documentElement.innerHTML
    Object.defineProperty(document.documentElement, 'innerHTML', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return '<body>⚠️ ACCESS DENIED - HTML source is protected</body>';
        }
    });

    // Override document.body.outerHTML
    Object.defineProperty(document.body, 'outerHTML', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return '<body>⚠️ ACCESS DENIED - HTML source is protected</body>';
        }
    });

    // Override document.body.innerHTML
    Object.defineProperty(document.body, 'innerHTML', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return '⚠️ ACCESS DENIED - HTML source is protected';
        }
    });

    // Override document.documentElement
    Object.defineProperty(document, 'documentElement', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return {
                outerHTML: '⚠️ ACCESS DENIED - HTML source is protected',
                innerHTML: '⚠️ ACCESS DENIED - HTML source is protected'
            };
        }
    });

    // Override document.body
    Object.defineProperty(document, 'body', {
        get: function() {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return {
                outerHTML: '⚠️ ACCESS DENIED - HTML source is protected',
                innerHTML: '⚠️ ACCESS DENIED - HTML source is protected'
            };
        }
    });

    // Override document.getElementsByTagName
    const originalGetElementsByTagName = document.getElementsByTagName;
    document.getElementsByTagName = function(tagName) {
        if (tagName.toLowerCase() === 'html' || tagName.toLowerCase() === 'body') {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return [];
        }
        return originalGetElementsByTagName.call(this, tagName);
    };

    // Override document.querySelector
    const originalQuerySelector = document.querySelector;
    document.querySelector = function(selector) {
        if (selector === 'html' || selector === 'body') {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return null;
        }
        return originalQuerySelector.call(this, selector);
    };

    // Override document.querySelectorAll
    const originalQuerySelectorAll = document.querySelectorAll;
    document.querySelectorAll = function(selector) {
        if (selector === 'html' || selector === 'body') {
            showProtectionAlert('⚠️ HTML source access is disabled on this page.');
            return [];
        }
        return originalQuerySelectorAll.call(this, selector);
    };

    // Override alert to prevent bypassing
    const originalAlert = window.alert;
    window.alert = function(message) {
        if (message.includes('Developer tools') || message.includes('disabled')) {
            originalAlert.call(window, message);
        }
    };

    // Function to show protection alerts
    function showProtectionAlert(message) {
        // Create custom alert
        const alertDiv = document.createElement('div');
        alertDiv.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #dc3545;
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 10000;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            max-width: 400px;
            word-wrap: break-word;
        `;
        alertDiv.innerHTML = `
            <div style="margin-bottom: 15px;">⚠️ SECURITY ALERT</div>
            <div style="margin-bottom: 15px;">${message}</div>
            <div style="font-size: 12px; opacity: 0.8;">
                This page is protected against unauthorized access and copying.
            </div>
            <button onclick="this.parentElement.remove()" style="
                margin-top: 15px;
                padding: 8px 16px;
                background: white;
                color: #dc3545;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
            ">OK</button>
        `;
        document.body.appendChild(alertDiv);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Enhanced signature protection (works with global protection)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.signature-protected') || e.target.closest('.signature-container')) {
            showProtectionAlert('⚠️ This signature is protected and cannot be copied or downloaded.');
            e.preventDefault();
            return false;
        }
    });

    // Prevent URL copying from image elements
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG' && e.target.hasAttribute('data-protected')) {
            e.preventDefault();
            alert('⚠️ This image URL is protected and cannot be copied.');
            return false;
        }
    });

    // Disable image dragging for protected images
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG' && e.target.hasAttribute('data-protected')) {
            e.preventDefault();
            return false;
        }
    });

    // Prevent opening protected images in new tabs
    document.addEventListener('click', function(e) {
        if ((e.target.tagName === 'IMG' || e.target.tagName === 'CANVAS') && e.target.hasAttribute('data-protected')) {
            e.preventDefault();
            showProtectionAlert('⚠️ This image is protected and cannot be opened in new tabs.');
            return false;
        }
    });

    // Prevent copying image URLs from inspect element
    document.addEventListener('contextmenu', function(e) {
        if ((e.target.tagName === 'IMG' || e.target.tagName === 'CANVAS') && e.target.hasAttribute('data-protected')) {
            e.preventDefault();
            showProtectionAlert('⚠️ This image URL is protected and cannot be copied.');
            return false;
        }
    });

    // Disable image dragging for protected images
    document.addEventListener('dragstart', function(e) {
        if ((e.target.tagName === 'IMG' || e.target.tagName === 'CANVAS') && e.target.hasAttribute('data-protected')) {
            e.preventDefault();
            showProtectionAlert('⚠️ This image is protected and cannot be dragged.');
            return false;
        }
    });

    // Add timestamp to prevent caching
    function addTimestampToProtectedImages() {
        const protectedImages = document.querySelectorAll('img[data-protected="true"]');
        protectedImages.forEach(img => {
            const timestamp = new Date().getTime();
            const separator = img.src.includes('?') ? '&' : '?';
            img.src = img.src + separator + '_t=' + timestamp;
        });
    }

    // Convert protected images to canvas to prevent URL copying
    function protectImagesWithCanvas() {
        const protectedImages = document.querySelectorAll('img[data-protected="true"]');
        protectedImages.forEach(img => {
            // Wait for image to load
            if (img.complete) {
                convertImageToCanvas(img);
            } else {
                img.addEventListener('load', function() {
                    convertImageToCanvas(img);
                });
            }
        });
    }

    function convertImageToCanvas(img) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Set canvas size to match image
        canvas.width = img.naturalWidth || img.width;
        canvas.height = img.naturalHeight || img.height;
        
        // Copy image styles
        canvas.style.cssText = img.style.cssText;
        canvas.className = img.className;
        canvas.setAttribute('data-protected', 'true');
        canvas.setAttribute('alt', img.alt);
        
        // Draw image to canvas
        ctx.drawImage(img, 0, 0);
        
        // Add subtle watermark
        ctx.fillStyle = 'rgba(255, 0, 0, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Replace image with canvas
        img.parentNode.replaceChild(canvas, img);
    }

    // Call protection functions on page load
    addTimestampToProtectedImages();
    setTimeout(protectImagesWithCanvas, 1000); // Wait for images to load

    // Print function is now defined globally at the top of the script

    // Log page load for security monitoring
    console.log('Secure exam schedule page loaded with full protection enabled');
});
    const studentListLink = document.getElementById('studentListLink');
    if (studentListLink) {
        studentListLink.onclick = function() {
            this.href = this.getAttribute('href');
        };
    }

    // Print orientation functionality
    function printWithOrientation(orientation = 'portrait') {
        // Create a style element to set print orientation
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                @page {
                    size: A4 ${orientation};
                    margin: 15mm;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Print the page
        window.print();
        
        // Remove the style after printing
        setTimeout(() => {
            document.head.removeChild(style);
        }, 1000);
    }

    // Add print buttons for different orientations
    const printButton = document.querySelector('button[onclick*="window.print"]');
    if (printButton) {
        // Create orientation buttons
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
        
        // Insert after the original print button
        printButton.parentNode.insertBefore(orientationButtons, printButton.nextSibling);
    } else {
        // Fallback: Add orientation buttons to the container if print button not found
        const container = document.querySelector('.container');
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
    }
});
</script>

</body>
</html> 