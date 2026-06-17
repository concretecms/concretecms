<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

class TextVariable extends Variable
{

    public function getValue()
    {
        return "'" . parent::getValue() . "'";
    }


}
