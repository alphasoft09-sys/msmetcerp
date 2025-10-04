<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Schedule of <?=$COURSE?>-<?=$BATCHCODE?></title>
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

        table, td, th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            width: 95%; /* Make the table responsive */
            margin: 20px auto;
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
        }

        @media (max-width: 400px) {
            .styled {
                padding: 0 10px;
                font-size: 0.8rem;
            }

            table {
                font-size: 0.7rem; /* Even smaller font for very small screens */
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div align="center" id="printableArea">
        <table class="MsoTableGrid" border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'> 
            <tr style='height:65.2pt'>
                <td colspan=3 valign=top style='border:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt; height:65.2pt'>
                    <p class=MsoNormal><img width=1034 height=94 src="../logo.png" style="max-width: 100%; height: auto;"></p>
                </td>
            </tr>
            <tr style='height:34.0pt'>
                <td colspan=3 valign=top style='border:solid windowtext 1.0pt; border-top:none; padding:0cm 5.4pt; height:34.0pt'>
                    <p class=MsoNormal>It is hereby informed to all <b><?=$COURSE?> - <?=$BATCHCODE?></b> Batch, that 
                    <?php 
                        echo ($examtype == 1) ? 'Internal' :  (($examtype == 2) ? 'Final' : (($examtype == 3) ? 'Special Final Exam' : 'Error'));
                    ?> SEM: <?=$semester?> Examination is going to be held from <b><?=$STARTDATE?></b> as per the following schedule below:</p>
                    <p>Center: 
                        <?php 
                        echo ($center == 1) ? "Bhubaneswar" : (($center == 2) ? "Berhampur" : (($center == 3) ? "Rayagada" : (($center == 4) ? "Keonjhar" : (($center == 5) ? "Bhawanipatna" : 'BB')))); 
                        ?> / File no: <?=$file_no?> / Program no: <?=$rowa[14]?><br> Notional Hours: <?=$rowa[15]?>/ NSQF Level: <?=$rowa[16]?>
                    </p>
                </td>
            </tr>
            <tr>
                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p class=MsoNormal>EXAMINATION DATE</p>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p class=MsoNormal>from <b><?=$STARTDATE?></b></p>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; border-bottom:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p class=MsoNormal>to <b><?=$ENDDATE?></b></p>
                </td>
            </tr>
            <tr>
                <td colspan=3 valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <div>
                        <?php
                        $sql2 = "SELECT * FROM theory WHERE idd='$hostid'";
                        $resultttt = mysqli_query($link, $sql2);
                        if(mysqli_num_rows($resultttt) > 0){
                            echo "<p><strong>Theory</strong></p>";
                            echo '<table border="1" align="center" style="width: 100%; height: 100%" >';
                            echo "<thead><tr><th>Exam date</th><th>NOs CODE</th><th>Subject</th><th>Venue</th><th>Timing</th><th>Invigilator</th></tr></thead><tbody>";
                            
                            while($row=mysqli_fetch_array($resultttt)){
                                echo "<tr>";
                                echo "<td>" . $row['tdate'] . "</td>";
                                echo "<td>" . $row['NOs_CODE'] . "</td>";
                                echo "<td>" . $row['tsubject'] . "</td>";
                                echo "<td>" . $row['tvenue'] . "</td>";
                                echo "<td>" . $row['tstime'] . " - " . $row['tetime'] . "</td>";
                                echo "<td>" . $row['invigilator'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } 
                        ?>
                    </div>
                    <div>
                        <?php
                        $sql3 = "SELECT * FROM pratical WHERE id='$hostid'";
                        $resultuL = mysqli_query($link, $sql3);
                        if(mysqli_num_rows($resultuL) > 0){
                            echo "<p><strong>Practical</strong></p>";
                            echo '<table border="1" align="center" style="width: 100%; height: 100%" >';
                            echo "<thead><tr><th>Exam date</th><th>NOs CODE</th><th>Subject</th><th>Venue</th><th>Timing</th><th>Invigilator</th></tr></thead><tbody>";
                            
                            while($row=mysqli_fetch_array($resultuL)){
                                echo "<tr>";
                                echo "<td>" . $row['pdate'] . "</td>";
                                 echo "<td>" . $row['NOs_CODE'] . "</td>";
                                echo "<td>" . $row['psubject'] . "</td>";
                                echo "<td>" . $row['pvenue'] . "</td>";
                                echo "<td>" . $row['pstime'] . " - " . $row['petime'] . "</td>";
                                echo "<td>" . $row['invigilator'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr style='height:39.7pt'>
                <td valign=top style='border:solid windowtext 1.0pt; padding:0cm 5.4pt'>
                    <p align=center><b>CO-ORDINATOR</b></p>
                    <p align=center><?=$RAE1?></p>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                    <p align=center><b>EXAM CELL</b></p>
                    <?php
                    $sqlO = "SELECT * FROM approvetable WHERE id='$hostid'";
                    $resultT = mysqli_query($link, $sqlO);
                    $rowR = mysqli_fetch_array($resultT);
                    $RATE1 = $rowR[1];
                    
                    if ($RATE1 == 0) {
                        echo '<input style="text-align:center;" type="submit" value="Scheduled" disabled></br>';
                    } elseif ($RATE1 == 1) {
                        echo '<button style="text-align:center;" type="submit" disabled>Hold</button></br>';
                    } elseif ($RATE1 == 2 || $RATE1 == 3 || $RATE1 == 4) {
                        echo '<input style="text-align:center;" type="submit" value="' . ($RATE1 == 2 ? "Verified" : ($RATE1 == 3 ? "Re-scheduled" : "Approved")) . '" disabled></br>';
                        echo '<img style="text-align:center;" src="https://exam.cttcbbsralumni.com/teacher/faculty/index/examcellSign.png" alt="PRANAB KUMAR CHOWDHURY" width="auto" height="50px">';
                    } elseif ($RATE1 == 5) {
                        echo '<button style="text-align:center;" type="submit" disabled>Exam Rejected</button></br>';
                    } else {
                        echo "Error";
                    }
                    ?>
                </td>
                <td valign=top style='border:solid windowtext 1.0pt; border-left:none; padding:0cm 5.4pt'>
                    <p align=center><b>MANAGER</b></p>
                    <?php
                    $sqlO = "SELECT * FROM approvetable WHERE id='$hostid'";
                    $resultT = mysqli_query($link, $sqlO);
                    $rowR = mysqli_fetch_array($resultT);
                    $RATE1 = $rowR[1];
                    
                    if ($RATE1 == 4) {
                        echo '<input style="text-align:center;" type="submit" value="Approved" disabled></br>';
                        echo '<img style="text-align:center;" src="https://exam.cttcbbsralumni.com/teacher/admin/manager/index/manager.png" alt="" width="auto" height="50px">';
                    } elseif ($RATE1 == 5) {
                        echo '<button style="text-align:center;" type="submit" disabled>Exam Rejected</button></br>';
                    } else {
                        echo "Not approved yet";
                    }
                    ?>
                </td>
            </tr>
        </table>
        <p align=center>This is a System Generated Exam Schedule</p>
       <?php 
        $quy = mysqli_query($link,"SELECT * FROM schedule WHERE id='$hostid'"); 
        $rw = mysqli_fetch_assoc($quy);
        $ntn= $rw['studentdetails'];
        $n1="https://exam.cttcbbsralumni.com/teacher/faculty/index/createexam/$ntn";
        ?>

<a href="#" id="studentListLink" data-url="<?=$n1?>" download="<?=$COURSE?>-<?=$BATCHCODE?>-Student List" target="_blank">CLICK HERE TO SEE ELIGIBLE STUDENT LIST</a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('studentListLink').onclick = function() {
        this.href = this.getAttribute('data-url'); // Set the href dynamically from data attribute
    };
});
</script>


    </div>
    <div>
        <input style="text-align:center;" class="styled" type="button" onclick="printDiv('printableArea')" value="Print Exam Schedule" /><br><br><br>
    </div>
    <?PHP

?>

</div>

<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print(); 
    document.body.innerHTML = originalContents;
}
</script>

</body>
</html>