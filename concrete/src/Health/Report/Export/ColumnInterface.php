<?php
namespace Concrete\Core\Health\Report\Export;

interface ColumnInterface
{

    /**
     * @return string
     */
    public function getKey(): string;


    /**
     * @return string
     */
    public function getDisplayName(): string;

}
