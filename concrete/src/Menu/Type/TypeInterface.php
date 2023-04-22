<?php

namespace Concrete\Core\Menu\Type;

interface TypeInterface
{

    public function getDriverHandle(): string;

    public function getName(): string;

    public function getTreeTypeHandle();

}
