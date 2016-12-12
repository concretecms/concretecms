<?php

use Concrete\Core\Config\Renderer;
use Concrete\Core\Config\RendererInvalidTypeException;

class RendererTest extends PHPUnit_Framework_TestCase
{
    public function testRendering()
    {
        $array = array(
            'string' => 'string',
            'array' => array(
                'test1',
                'test2',
                'test3',
            ),
            'associative array' => array(
                1 => 'test1',
                3 => 'test2',
                2 => 'test3',
            ),
            'special chars' => '!@#$%^&*()_+-={}[]:\'";|<>,./?`~',
            'other chars' => "\n\r\t\v\f",
            'unicode' => 'œ∑´†¥¨ˆπˆ¨†¬˚∆˙©ƒ∂ßåΩ≈ç√∫˜≤≥çæ…“‘¡™£¢∞§¶•ªº–≠',
            'parseable' => '$test {$test}',
            'int' => 1,
            'double' => 1.25,
            'boolean' => true,
        );

        $first_level = $array;
        $current_level = &$array;
        for ($i = 10; --$i;) {
            $current_level['depth_test'] = $first_level;
            $current_level = &$current_level['depth_test'];
        }

        $rendered = id(new Renderer($array))->render();

        /** @var Closure $closure */
        $closure = eval('return function(){' . substr($rendered, 5) . '};');

        $this->assertTrue($this->same($closure(), $array));
    }

    public function same($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key])) {
                return false;
            }
            if (is_scalar($value)) {
                if ($array2[$key] !== $value) {
                    return false;
                }
            } elseif (is_array($value)) {
                if (!$this->same($value, $array2[$key])) {
                    return false;
                }
            }
        }
        foreach ($array2 as $key => $value) {
            if (!isset($array1[$key])) {
                return false;
            }
            if (is_scalar($value)) {
                if ($array1[$key] !== $value) {
                    return false;
                }
            } elseif (is_array($value)) {
                if (!$this->same($value, $array1[$key])) {
                    return false;
                }
            }
        }

        return true;
    }

    public function testInvalidTypeClosure()
    {
        $renderer = new Renderer(
            array(
                'closure' => function () {
                },
            ));
        try {
            $renderer->render();
        } catch (RendererInvalidTypeException $e) {
            return;
        }
        $this->fail('Didn\'t detect invalid Closure type.');
    }

    public function testInvalidTypeObject()
    {
        $renderer = new Renderer(
            array(
                'object' => $this,
            ));
        try {
            $renderer->render();
        } catch (RendererInvalidTypeException $e) {
            return;
        }
        $this->fail('Didn\'t detect invalid Closure type.');
    }
}
