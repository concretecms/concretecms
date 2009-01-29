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
$fp = fopen($dirname."/px012.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "S"), array("col2", "I"));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
$pxdoc->close();
fclose($fp);
unlink($dirname."/px012.db");
?>
--EXPECT--
