<?php

class CustomCssRecordTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $metadatas = array('Concrete\Core\Entity\StyleCustomizer\CustomCssRecord');

    public function testCustomStyleRecord()
    {
        $record = new \Concrete\Core\Entity\StyleCustomizer\CustomCssRecord();
        $record->setValue('body { display: none; }');
        $record->save();

        $this->assertEquals($record->getValue(), 'body { display: none; }');
        $this->assertEquals($record->getRecordID(), 1);

        $rec2 = \Concrete\Core\StyleCustomizer\CustomCssRecord::getByID(1);
        $this->assertEquals($record, $rec2);
    }
}
