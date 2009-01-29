--TEST--
Create an new paradox with 100 records (Short, Long)
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/px003.db", "w+");
$fields = array(array("col1", "S"), array("col2", "I"));
@px_create_fp($pxdoc, $fp, $fields);
px_set_tablename($pxdoc, "testtabelle");
for($i=-50; $i<50; $i++) {
	$rec = array($i, -$i);
	px_put_record($pxdoc, $rec);
}
px_close($pxdoc);
px_delete($pxdoc);
unlink($dirname."/px003.db");
?>
--EXPECT--
