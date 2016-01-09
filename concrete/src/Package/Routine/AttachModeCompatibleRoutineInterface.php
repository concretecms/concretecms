<?php
namespace Concrete\Core\Package\Routine;

/**
 * Interface AttachModeCompatibleRoutineInterface
 *
 * `StartingPointInstallRoutine`s that implement this interface will not be skipped when the install command runs in
 * attach mode. Attach mode is used and a new concrete5 instance is attached to an already installed database.
 *
 * @package Concrete\Core\Package
 */
interface AttachModeCompatibleRoutineInterface
{

}
