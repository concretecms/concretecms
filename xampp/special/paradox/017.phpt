--TEST--
Update a single field in a record
--SKIPIF--
<?php if (!extension_loaded("paradox")) print "skip"; ?>
--POST--
--GET--
--INI--
--FILE--
<?php 
$dirname = dirname($_SERVER["SCRIPT_FILENAME"]);
$fp = fopen($dirname."/px017.db", "w+");
$pxdoc = new paradox_db();
$fields = array(array("col1", "D"), array("col2", "A", 10));
@$pxdoc->create_fp($fp, $fields);
$pxdoc->set_tablename("testtabelle");
for($i=1; $i<=8; $i++) {
	$pxdoc->insert_record(array($i*100000, "i=".$i));
}
$rec = $pxdoc->retrieve_record(1);
print_r($rec);
$rec["col1"]++;
$rec["col2"] = "updated";
$pxdoc->update_record($rec, 1);
print_r($pxdoc->retrieve_record(1));
$pxdoc->close();
fclose($fp);
unlink($dirname."/px017.db");
?>
--EXPECT--
Array
(
    [col1] => 200000
    [col2] => i=2
)
Array
(
    [col1] => 200001
    [col2] => updated
)

