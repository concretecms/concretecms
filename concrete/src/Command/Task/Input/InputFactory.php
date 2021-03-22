<?php

namespace Concrete\Core\Command\Task\Input;

use Concrete\Core\Command\Task\Input\Definition\Definition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputInterface as ConsoleInputInterface;

class InputFactory
{

    /**
     * @param $method
     * @return array
     */
    private function getRequestData($request, $method): array
    {
        $vars = $method == Request::METHOD_POST ? $request->request->all() : $request->query->all();
        return $vars;
    }

    public function createFromRequest(Request $request, Definition $definition = null): InputInterface
    {
        $input = new Input();
        if (!$definition) {
            return $input;
        }

        $data = $this->getRequestData($request, Request::METHOD_POST);
        foreach($definition->getFields() as $field) {
            $loadedField = $field->loadFieldFromRequest($data);
            if ($loadedField) {
                if ($field->isValid($loadedField)) {
                    $input->addField($loadedField);
                }
            }
        }

        return $input;
    }

    public function createFromConsoleInput(ConsoleInputInterface $consoleInput, Definition $definition = null): InputInterface
    {
        $input = new Input();
        if (!$definition) {
            return $input;
        }

        foreach($definition->getFields() as $field) {
            $loadedField = $field->loadFieldFromConsoleInput($consoleInput);
            if ($loadedField) {
                if ($field->isValid($loadedField)) {
                    $input->addField($loadedField);
                }
            }
        }

        return $input;
    }


}
