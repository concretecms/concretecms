<?php

namespace Concrete\Core\Attribute\Key\RequestLoader;

use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Symfony\Component\HttpFoundation\Request;

class TextareaRequestLoader extends StandardRequestLoader
{

    /**
     * @param \Concrete\Core\Entity\AttributeKey\TextareaAttributeKey $key
     * @param Request $request
     */
    public function load(AttributeKey $key, Request $request)
    {
        parent::load($key, $request);
        $key->setMode($request->request->get('akTextareaDisplayMode'));
    }

}
