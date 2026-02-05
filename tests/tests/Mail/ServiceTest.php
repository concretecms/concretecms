<?php

namespace Concrete\Tests\Mail;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Mail\Service;
use Concrete\Tests\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\RawMessage;

class ServiceTest extends TestCase
{
    /** @var Repository|null */
    private $config;

    /** @var MailerInterface|null */
    private $mailer;

    /** @var Service|null */
    private $service;

    private $configData = [];

    public function setUp(): void
    {
        $this->configData = ['concrete' => ['email' => ['enabled' => true]]];
        $this->config = \Mockery::mock(Repository::class);
        $this->config->shouldReceive('get')->andReturnUsing(function ($key) {
            return array_get($this->configData, $key);
        });
        $this->mailer = \Mockery::mock(MailerInterface::class);

        $this->service = new Service($this->config, $this->mailer);
    }

    protected function tearDown(): void
    {
        $this->mailer = null;
        $this->service = null;
    }

    /**
     * @covers \Concrete\Core\Mail\Service::reset
     */
    public function testReset(): void
    {
        $initialProperties = $this->protectedProperties();

        $this->service->load('../../tests/assets/Mail/test_template');
        $this->service->setAdditionalHeaders(['foo', 'baz']);
        $this->service->addParameter('foo', 'baz');
        $this->service->to('foo@bar', 'bar');
        $this->service->replyto('foo@bar', 'baz');
        $this->service->cc('foo@bar', 'bar');
        $this->service->bcc('foo@bar', 'bar');
        $this->service->addRawAttachment('foo', 'foo.baz');
        $this->service->setTesting(true);
        $this->service->setIsThrowOnFailure(true);

        $this->assertNotSame($initialProperties, $this->protectedProperties());
        $this->service->reset();

        $this->assertSame($initialProperties, $this->protectedProperties());
        $this->assertEquals('foo', 'foo');
    }

    private function protectedProperties(): array
    {
        $allProperties = [];
        $reflection = new \ReflectionClass($this->service);
        $properties = $reflection->getProperties();
        foreach ($properties as $property) {
            if ($property->isProtected() || $property->isPrivate()) {
                $property->setAccessible(true);
                $allProperties[$property->getName()] = $property->getValue($this->service);
            }
        }

        if ($allProperties['email'] instanceof Email) {
            try {
                $allProperties['email'] = $allProperties['email']->toString();
            } catch (\Throwable $e) {
                $allProperties['email'] = '';
            }
        }

        return $allProperties;
    }

    /**
     * @covers \Concrete\Core\Mail\Service::__destruct
     */
    public function testDestructHidesTransportExceptions(): void
    {
        $this->expectNotToPerformAssertions();

        try {
            // This will immediately destruct causing the anonymous class __destruct to throw
            new Service($this->config, new class() implements MailerInterface {
                public function __destruct()
                {
                    throw new \RuntimeException('Destruct exception');
                }

                public function send(RawMessage $message, ?Envelope $envelope = null): void {}
            });
        } catch (\Throwable $e) {
            $this->fail('Transport destruct exception wasn\'t caught: ' . $e->getMessage());
        }
    }

    /**
     * @dataProvider trueFalseProvider
     */
    public function testBuildingFullEmail(bool $resetAfter): void
    {
        $initialProperties = $this->protectedProperties();
        /** @var Email|null $message */
        $message = null;
        $this->mailer->expects('send')->with(\Mockery::capture($message))->once();


        // Build the email
        $this->service->from('from@baz');
        $this->service->addParameter('foo', 'baz');
        $this->service->to('to@bar', 'bar');
        $this->service->to('to2@bar', 'bar');
        $this->service->replyto('replyto@bar', 'baz');
        $this->service->replyto('replyto2@bar', 'baz');
        $this->service->cc('cc@bar', 'bar');
        $this->service->cc('cc2@bar', 'bar');
        $this->service->bcc('bcc@bar', 'bar');
        $this->service->bcc('bcc2@bar', 'bar');
        $this->service->addRawAttachment('foo', 'foo.baz');
        $this->service->setSubject('Subject');
        $this->service->setBody('Text Body');
        $this->service->setBodyHTML('<b>HTML Body</b>');

        $this->service->sendMail($resetAfter);

        if ($resetAfter) {
            $this->assertSame($initialProperties, $this->protectedProperties());
        } else {
            $this->assertNotSame($initialProperties, $this->protectedProperties());
        }

        $this->assertNotNull($message);
        $this->assertEquals(['"bar" <bcc@bar>', '"bar" <bcc2@bar>'], $this->addressesToStrings(...$message->getBcc()));
        $this->assertEquals(['"bar" <cc@bar>', '"bar" <cc2@bar>'], $this->addressesToStrings(...$message->getCc()));
        $this->assertEquals(['"baz" <replyto@bar>', '"baz" <replyto2@bar>'], $this->addressesToStrings(...$message->getReplyTo()));
        $this->assertEquals(['from@baz'], $this->addressesToStrings(...$message->getFrom()));
        $this->assertEquals(['"bar" <to@bar>', '"bar" <to2@bar>'], $this->addressesToStrings(...$message->getTo()));
        $this->assertEquals('Subject', $message->getSubject());
        $this->assertStringContainsString('Text Body', $message->getTextBody());
        $this->assertStringContainsString('HTML Body', $message->getHtmlBody());
    }

    public static function trueFalseProvider(): array
    {
        return [[true], [false]];
    }

    private function addressesToStrings(Address ...$addresses): array
    {
        return array_map(function (Address $address) {
            return $address->toString();
        }, $addresses);
    }

    public function testSupportsAttachmentAttributes(): void
    {
        $email = $this->service->getEmail();
        $this->assertEmpty($email->getAttachments());

        $this->service->addRawAttachment('foo', 'foo.baz', 'application/x-baz');
        $this->assertEquals(
            $this->rn(<<<BASIC
                Content-Type: application/x-baz; name=foo.baz
                Content-Transfer-Encoding: base64
                Content-Disposition: attachment; name=foo.baz; filename=foo.baz
                
                Zm9v
                BASIC),
            $email->getAttachments()[0]->toString()
        );

        $this->service->addRawAttachmentWithHeaders('foo', 'foo.baz', [
            'mimetype' => 'foo/baz',
            'disposition' => 'inline',
            'encoding' => '8bit',
            'charset' => 'chars',
            'description' => 'desc',
            'location' => 'loc',
            'language' => 'lang',
        ]);
        $this->assertEquals(
            $this->rn(<<<BASIC
                Content-Type: foo/baz; charset=chars; name=foo.baz
                Content-Description: desc
                Content-Location: loc
                Content-Language: lang
                Content-Transfer-Encoding: 8bit
                Content-Disposition: inline; name=foo.baz; filename=foo.baz
                
                foo
                BASIC),
            $email->getAttachments()[1]->toString()
        );
    }

    private function rn(string $string): string
    {
        return str_replace("\n", "\r\n", $string);
    }
}