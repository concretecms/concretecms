<?php

namespace Concrete\Tests\Validation;

use Concrete\Core\Validation\BannedWord\Service;
use PHPUnit_Framework_TestCase;

class BannedWordTest extends PHPUnit_Framework_TestCase
{
    public function testHasBannedWords()
    {
        $service = new Service();
        $service->setBannedWords(['lorem', 'NSECT', 'perché', '建築家']);
        $haystacks = [
            'Lorem ipsum dolor sit amet',
            'consectetur adipiscing elit',
            'Tuttavia, perché voi intendiate da dove sia nato tutto questo errore',
            '人間の喜びを築く建築家の実践的な教えを詳しく説明しよう'
        ];
        foreach ($haystacks as $haystack) {
            $this->assertTrue((bool) $service->hasBannedWords($haystack), sprintf('Has Banned Word check failed with %s', $haystack));
        }
        $service->setBannedWords(['Duis', 'aute', 'spiegherò', '憤り']);
        foreach ($haystacks as $haystack) {
            $this->assertFalse((bool) $service->hasBannedWords($haystack), sprintf('Has not Banned Word check failed with %s', $haystack));
        }
    }
}