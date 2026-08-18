<?php

namespace Concrete\Core\Api;

/**
 * Implemented by the controllers (for example the block type controllers) that want to describe to the
 * API clients the structure of the value they accept.
 *
 * When a controller doesn't implement this interface, the API describes its value by inspecting the
 * database table and the CIF export declarations of the controller: that's just an approximation of what
 * the save() method actually accepts, so implementing this interface is the way to be authoritative.
 */
interface ApiValueSchemaInterface
{
    /**
     * Get the JSON Schema (as an array) describing the value accepted by this controller.
     *
     * @return array
     */
    public function getApiValueSchema(): array;
}
