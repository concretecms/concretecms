--TEST--
Reading a simple paradox database by using the object oriented Interface
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
print_r($pxdoc->get_info());
$pxdoc->close();
fclose($fp);
?>
--EXPECT--
Array
(
    [fileversion] => 70
    [tablename] => test.db
    [numrecords] => 1
    [numfields] => 1
    [headersize] => 2048
    [maxtablesize] => 2
    [numdatablocks] => 1
    [numindexfields] => 0
    [codepage] => 1252
)
