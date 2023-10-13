<?php
namespace Concrete\Core\Install\StartingPoint\Controller;

interface ControllerInterface
{

    public function getHandle(): string;

    public function getName(): string;

    public function getThumbnail(): ?string;

    public function providesThumbnails(): bool;

    /**
     * @return string[]|string
     */
    public function getDescription();


}
