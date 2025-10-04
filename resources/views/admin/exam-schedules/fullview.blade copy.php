<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule of {{ $examSchedule->course_name }}-{{ $examSchedule->batch_code }}</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0;
            font-family: Arial, sans-serif;
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
            size: auto;
            margin: 0;
        }

        @media print {
            .header-layout-container {
                min-height: 80px;
                padding: 10px 0;
            }

            .header-layout-image {
                max-height: 80px;
                max-width: 100%;
            }

            .container {
                max-width: none;
                margin: 0;
                padding: 10px;
                border: none;
                box-shadow: none;
                background-color: white;
            }
        }

        table, td, th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            width: 95%; /* Make the table responsive */
            margin: 20px auto;
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
        }

        th {
            text-align: center;
        }

        td {
            text-align: center;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }

            .styled {
                font-size: 0.9rem;
            }

            table {
                font-size: 0.8rem; /* Smaller font for smaller screens */
            }

            .header-layout-container {
                min-height: 60px;
                padding: 5px 0;
            }

            .header-layout-image {
                max-height: 60px;
            }
        }

        @media (max-width: 400px) {
            .styled {
                padding: 0 10px;
                font-size: 0.8rem;
            }

            table {
                font-size: 0.7rem; /* Even smaller font for very small screens */
            }

            .header-layout-container {
                min-height: 50px;
                padding: 3px 0;
            }

            .header-layout-image {
                max-height: 50px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div align="center" id="printableArea">
        <table class="MsoTableGrid" border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'> 
            <tr style='height:65.2pt'>
                <td colspan=3 valign=top style='border:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt; height:auto; min-height:80px;'>
                    <div class="header-layout-container">
                        @if(isset($headerLayout))
                            <img src="{{ $headerLayout->header_layout_url }}" class="header-layout-image" alt="Header Layout">
                        @else
                            <img src="{{ asset('msme_logo/favicon-96x96.png') }}" class="header-layout-image" alt="Default Logo">
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
                                                {{ $centerText }} / File no: {{ $examSchedule->file_no }} / Program no: {{ $examSchedule->program_number }}<br>
                        @if($examSchedule->centre && $examSchedule->centre->address)<br>
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
                            <table border="1" align="center" style="width: 100%; height: 100%" >
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
                    </div>
                    <div>
                        @if($practicalModules->count() > 0)
                            <p><strong>Practical</strong></p>
                            <table border="1" align="center" style="width: 100%; height: 100%" >
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
                    </div>
                </td>
            </tr>
            <tr style='height:39.7pt'>
                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p align=center><b>CO-ORDINATOR</b></p>
                    <p align=center>{{ $examSchedule->exam_coordinator }}</p>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                    <p align=center><b>EXAM CELL</b></p>
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
    <div>
        <input style="text-align:center;" class="styled" type="button" onclick="printDiv('printableArea')" value="Print Exam Schedule" /><br><br><br>
    </div>
</div>

<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print(); 
    document.body.innerHTML = originalContents;
}

document.addEventListener('DOMContentLoaded', function() {
    const studentListLink = document.getElementById('studentListLink');
    if (studentListLink) {
        studentListLink.onclick = function() {
            this.href = this.getAttribute('href'); // Set the href dynamically
        };
    }
});
</script>

</body>
</html> 