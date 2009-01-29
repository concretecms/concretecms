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
$fp = fopen($dirname."/px013.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "S"), array("col2", "I"));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=-3; $i<=3; $i++) {
	$pxdoc->insert_record(array($i, $i));
}
print_r($pxdoc->retrieve_record(1));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px013.db");
?>
--EXPECT--
Array
(
    [col1] => -2
    [col2] => -2
)

