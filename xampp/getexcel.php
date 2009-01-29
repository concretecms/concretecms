<?php
	include_once "Spreadsheet/Excel/Writer.php";

	$xls =& new Spreadsheet_Excel_Writer();
	$xls->send("test.xls");
	$format =& $xls->addFormat();
	$format->setBold();
	$format->setColor("blue");
	$sheet =& $xls->addWorksheet('Test XLS');
	$sheet->write(0, 0, 1, 0);
	$sheet->write(0, 1, 2, 0);
	$sheet->writeString(1, 0, "XAMPP:", 0);
	$sheet->writeString(1, 1, $_POST['value'], $format);
	$xls->close();
	exit;
?>
