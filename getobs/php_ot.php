﻿<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Output observation data as CSV file
//----------------------------------------------------------
$data = $_POST['dataSend'];
$filename = $_POST['FileName'];
header("Content-type: text/csv");
header('Content-Disposition: attachment; filename="'.$filename.'";');
header("Pragma: no-cache");
header("Expires: 0");
function outputCSV($data) {
    $output = fopen("php://output", "w");
    /*foreach ($data as $row) {
        fputcsv($output, $row);
    }*/
	fwrite($output, $data);
    fclose($output);
}
$data = $_POST['dataSend'];
outputCSV($data);
?>
