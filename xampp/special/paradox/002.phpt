--TEST--
Create an new paradox database
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$pxdoc = px_new();
$fp = fopen($dirname."/px002.db", "w+");
$fields = array(array("col1", "S"), array("col2", "I"));
@px_create_fp($pxdoc, $fp, $fields);
px_set_tablename($pxdoc, "testtabelle");
px_close($pxdoc);
px_delete($pxdoc);
unlink($dirname."/px002.db");
?>
--EXPECT--
