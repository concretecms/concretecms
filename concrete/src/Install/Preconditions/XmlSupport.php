<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Foundation\Environment\FunctionInspector;
use Concrete\Core\Install\PreconditionInterface;
use Concrete\Core\Install\PreconditionResult;

class XmlSupport implements PreconditionInterface
{
    /**
     * The FunctionInspector instance.
     *
     * @var FunctionInspector
     */
    protected $functionInspector;

    /**
     * Initialize the instance.
     *
     * @param FunctionInspector $functionInspector the FunctionInspector instance
     */
    public function __construct(FunctionInspector $functionInspector)
    {
        $this->functionInspector = $functionInspector;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('XML Support');
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'xml_support';
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $result = new PreconditionResult();
        if (!(
            $this->functionInspector->functionAvailable('xml_parse')
            && $this->functionInspector->functionAvailable('simplexml_load_file')
        )) {
            $result
                ->setState(PreconditionResult::STATE_FAILED)
                ->setMessage(t('concrete5 requires PHP XML Parser and SimpleXML extensions'))
            ;
        }

        return $result;
    }
}
