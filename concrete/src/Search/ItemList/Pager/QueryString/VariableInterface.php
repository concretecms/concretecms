<?php
namespace Concrete\Core\Search\ItemList\Pager\QueryString;

interface VariableInterface
{

    /**
     * @return string
     */
    function getVariable();

    /**
     * @return string
     */
    function getValue();

    /**
     * @return mixed
     */
    function getName();


}