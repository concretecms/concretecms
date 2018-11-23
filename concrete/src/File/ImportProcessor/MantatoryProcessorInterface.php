<?php

namespace Concrete\Core\File\ImportProcessor;

/**
 * The interface that file processors should implement when the exceptions thrown by their process() method means that the file shouldn't be imported.
 */
interface MantatoryProcessorInterface extends ProcessorInterface
{
}
