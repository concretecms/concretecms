<?php

class CustomCssRecordTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array('StyleCustomizerCustomCssRecords');

    public function testCustomStyleRecord()
    {
        $record = new \Concrete\Core\StyleCustomizer\CustomCssRecord();
        $record->setValue('body { display: none; }');
        $record->save();

        $this->assertEquals($record->getValue(), 'body { display: none; }');
        $this->assertEquals($record->getRecordID(), 1);

        $rec2 = \Concrete\Core\StyleCustomizer\CustomCssRecord::getByID(1);
        $this->assertEquals($record, $rec2);
    }
}
