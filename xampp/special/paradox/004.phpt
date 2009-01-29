--TEST--
Create an new paradox database with 100 records (Double, Boolean, Alpha)
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/px004.db", "w+");
$fields = array(array("col1", "N"), array("col2", "L"), array("col3", "A", 15));
@px_create_fp($pxdoc, $fp, $fields);
px_set_tablename($pxdoc, "testtabelle");
for($i=-50; $i<50; $i++) {
	$rec = array($i*0.001, $i<0, "Nummer $i");
	px_put_record($pxdoc, $rec);
}
px_close($pxdoc);
px_delete($pxdoc);
unlink($dirname."/px004.db");
?>
--EXPECT--
