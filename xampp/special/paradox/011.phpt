--TEST--
Reading a record from a paradox database by using retrieve_record() 
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/simpletest.db", "r");
$pxdoc = new paradox_db();
$pxdoc->open_fp($fp);
print_r($pxdoc->retrieve_record(0));
$pxdoc->close();
fclose($fp);
?>
--EXPECT--
Array
(
    [col1] => 2
)
