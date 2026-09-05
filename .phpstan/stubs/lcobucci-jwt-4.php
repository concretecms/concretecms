<?php

declare(strict_types=1);

/*
 * concrete/composer.json accepts both lcobucci/jwt 3.4.6 and ^4.1, and the code chooses at runtime (with class_exists)
 * between the 3.x API (Builder, Parser, Signer\Key) and the 4.x one (Token\Builder, Token\Parser, Encoding\JoseEncoder,
 * Encoding\ChainedFormatter, Signer\Key\InMemory).
 * The version installed by composer.lock is 3.4.6, so PHPStan can't see the classes that only exist in 4.x: this file
 * declares them (only the members used by the core), so that the 4.x branches can be analysed too.
 * It's listed in scanFiles (not in stubFiles, since stubs only override the PHPDoc of existing symbols), and the symbols
 * existing in both versions (Builder, Parser, Token, Signer, Signer\Key) are deliberately not declared here: PHPStan
 * takes them from the installed version.
 */

namespace Lcobucci\JWT;

defined('C5_EXECUTE') or die('Access Denied.');

interface Encoder
{
    /**
     * @param mixed $data
     */
    public function jsonEncode($data): string;

    public function base64UrlEncode(string $data): string;
}

interface Decoder
{
    /**
     * @return mixed
     */
    public function jsonDecode(string $json);

    public function base64UrlDecode(string $data): string;
}

interface ClaimsFormatter
{
    /**
     * @param array<string, mixed> $claims
     *
     * @return array<string, mixed>
     */
    public function formatClaims(array $claims): array;
}

namespace Lcobucci\JWT\Encoding;

use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Decoder;
use Lcobucci\JWT\Encoder;

final class JoseEncoder implements Encoder, Decoder
{
}

final class ChainedFormatter implements ClaimsFormatter
{
    public function __construct(ClaimsFormatter ...$formatters)
    {
    }

    public static function default(): self
    {
    }

    public static function withUnixTimestampDates(): self
    {
    }
}

namespace Lcobucci\JWT\Token;

use DateTimeImmutable;
use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Decoder;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;

final class Builder
{
    public function __construct(Encoder $encoder, ClaimsFormatter $claimFormatter)
    {
    }

    public function permittedFor(string ...$audiences): self
    {
    }

    public function expiresAt(DateTimeImmutable $expiration): self
    {
    }

    public function identifiedBy(string $id): self
    {
    }

    public function issuedAt(DateTimeImmutable $issuedAt): self
    {
    }

    public function issuedBy(string $issuer): self
    {
    }

    public function canOnlyBeUsedAfter(DateTimeImmutable $notBefore): self
    {
    }

    public function relatedTo(string $subject): self
    {
    }

    /**
     * @param mixed $value
     */
    public function withHeader(string $name, $value): self
    {
    }

    /**
     * @param mixed $value
     */
    public function withClaim(string $name, $value): self
    {
    }

    public function getToken(Signer $signer, Key $key): Token
    {
    }
}

final class Parser
{
    public function __construct(Decoder $decoder)
    {
    }

    public function parse(string $jwt): Token
    {
    }
}
