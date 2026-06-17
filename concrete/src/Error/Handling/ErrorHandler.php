<?php

namespace Concrete\Core\Error\Handling;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\Handling\ErrorRenderer\ConcreteErrorRenderer;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Logging\Handler\ErrorEnhancer\UserMessageExceptionEnhancer;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\ErrorHandler\ErrorHandler as SymfonyErrorHandler;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\CliErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ErrorHandler extends SymfonyErrorHandler
{
    private const DISPLAY_MESSAGE = 'message';
    private const DISPLAY_GENERIC = 'generic';

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var callable[]
     */
    protected $exceptionListeners = [];

    /**
     * @var bool
     */
    protected $isRenderingException = false;

    public function __construct(LoggerInterface $logger, Repository $config)
    {
        $this->config = $config;
        $errorConfiguration = $config->get('concrete.error.handling');
        $thrownErrors = E_ALL;
        if (!($errorConfiguration['warning']['halt'] ?? false)) {
            $thrownErrors = $thrownErrors & ~E_WARNING & ~E_CORE_WARNING & ~E_USER_WARNING;
        }
        if (!($errorConfiguration['notice']['halt'] ?? false)) {
            $thrownErrors = $thrownErrors & ~E_NOTICE & ~E_USER_NOTICE;
        }
        // Note: "deprecated" is not checked for thrown errors here because symfony will not
        // let deprecated errors trigger full exceptions

        $levels = [];
        if (!empty($errorConfiguration['error']['logLevel'])) {
            $levels += $this->fillErrorLevelsFromConfig([
                E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR, E_USER_ERROR
            ], $errorConfiguration, 'error');
        }
        if (!empty($errorConfiguration['warning']['logLevel'])) {
            $levels += $this->fillErrorLevelsFromConfig([
                E_WARNING, E_USER_WARNING, E_COMPILE_WARNING, E_CORE_WARNING
            ], $errorConfiguration, 'warning');
        }
        if (!empty($errorConfiguration['notice']['logLevel'])) {
            $levels += $this->fillErrorLevelsFromConfig([E_NOTICE, E_USER_NOTICE], $errorConfiguration, 'notice');
        }
        if (!empty($errorConfiguration['deprecated']['logLevel'])) {
            $levels += $this->fillErrorLevelsFromConfig([E_DEPRECATED, E_USER_DEPRECATED], $errorConfiguration, 'deprecated');
        }

        parent::__construct(null,true);
        $this->setDefaultLogger($logger,  $levels);
        $this->throwAt($thrownErrors, true);
    }

    /**
     * Add a callable to be invoked when we have an exception.
     * If the callable returns true we won't do anything.
     *
     * @return $this
     */
    public function addExceptionListener(callable $listener): self
    {
        $this->exceptionListeners[] = $listener;

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Symfony\Component\ErrorHandler\ErrorHandler::getErrorEnhancers()
     */
    protected function getErrorEnhancers(): iterable
    {
        yield new UserMessageExceptionEnhancer();
        foreach (parent::getErrorEnhancers() as $enhancer) {
            yield $enhancer;
        }
    }

    /**
     * Mostly ported from SymfonyErrorHandler::renderException, but that method cannot be overridden, and the classes
     * that do the rendering are hard-coded within the method, so we need to swap out the entire method in order to
     * override the Cli and Html error renderers.
     *
     * @param \Throwable $exception
     * @return void
     */
    protected function renderConcreteException(\Throwable $exception): void
    {
        if ($this->isRenderingException) {
            $this->outputRawEmergencyException($exception);

            return;
        }

        $this->isRenderingException = true;
        try {
            foreach ($this->exceptionListeners as $exceptionListener) {
                if ($exceptionListener($exception) === true) {
                    return;
                }
            }

            try {
                $flattenException = $this->createRenderer()->render($exception);
            } catch (\Throwable $renderingException) {
                try {
                    $flattenException = $this->createEmergencyFlattenException($exception);
                } catch (\Throwable $emergencyRenderingException) {
                    $this->outputRawEmergencyException($exception);

                    return;
                }
            }

            $this->outputFlattenException($flattenException);
        } finally {
            $this->isRenderingException = false;
        }
    }

    protected function createRenderer(): ErrorRendererInterface
    {
        return \in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) ? new CliErrorRenderer() : app(ConcreteErrorRenderer::class, ['config' => $this->config]);
    }

    protected function createEmergencyFlattenException(\Throwable $exception): FlattenException
    {
        if ($this->shouldRenderEmergencyJson()) {
            $payload = $exception instanceof UserMessageException ? $exception->jsonSerialize() : [
                'error' => true,
                'errors' => [$this->getEmergencyErrorMessage($exception)],
            ];

            return $this->createFlattenExceptionFromString(
                $exception,
                (string) (json_encode($payload) ?: '{"error":true,"errors":["An error occurred while processing this request."]}'),
                'application/json; charset=' . $this->getEmergencyCharset()
            );
        }

        $html = sprintf(
            '<!DOCTYPE html><html><head><meta charset="%1$s"><title>%2$s</title></head><body><h1>%2$s</h1><p>%3$s</p></body></html>',
            $this->escapeForHtml($this->getEmergencyCharset()),
            $this->escapeForHtml('An unexpected error occurred.'),
            $this->escapeForHtml($this->getEmergencyErrorMessage($exception))
        );

        return $this->createFlattenExceptionFromString(
            $exception,
            $html,
            'text/html; charset=' . $this->getEmergencyCharset()
        );
    }

    protected function createFlattenExceptionFromString(\Throwable $exception, string $content, string $contentType): FlattenException
    {
        $flattenException = FlattenException::createFromThrowable($exception, null, ['Content-Type' => $contentType]);
        $flattenException->setAsString($content);

        return $flattenException;
    }

    protected function outputFlattenException(FlattenException $exception): void
    {
        if ($this->shouldOutputHttpHeaders()) {
            http_response_code($exception->getStatusCode());

            foreach ($exception->getHeaders() as $name => $value) {
                header($name . ': ' . $value, false);
            }
        }

        echo $exception->getAsString();
    }

    protected function outputRawEmergencyException(\Throwable $exception): void
    {
        $charset = $this->getEmergencyCharset();
        if ($this->shouldRenderEmergencyJson()) {
            if ($this->shouldOutputHttpHeaders()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=' . $charset, false);
            }
            echo '{"error":true,"errors":["An error occurred while processing this request."]}';

            return;
        }

        if ($this->shouldOutputHttpHeaders()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=' . $charset, false);
        }
        echo sprintf(
            '<!DOCTYPE html><html><head><meta charset="%1$s"><title>%2$s</title></head><body><h1>%2$s</h1><p>%3$s</p></body></html>',
            $this->escapeForHtml($charset),
            $this->escapeForHtml('An unexpected error occurred.'),
            $this->escapeForHtml('An error occurred while processing this request.')
        );
    }

    protected function shouldRenderEmergencyJson(): bool
    {
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? (string) $_SERVER['HTTP_ACCEPT'] : '';

        return stripos($accept, 'json') !== false;
    }

    protected function getEmergencyErrorMessage(\Throwable $exception): string
    {
        if ($exception instanceof UserMessageException) {
            return $exception->getMessage();
        }

        return $this->getEmergencyDisplaySetting() === self::DISPLAY_MESSAGE ? $exception->getMessage() : 'An error occurred while processing this request.';
    }

    protected function getEmergencyDisplaySetting(): string
    {
        try {
            $setting = (string) $this->config->get('concrete.error.display.guests', self::DISPLAY_GENERIC);
        } catch (\Throwable $x) {
            return self::DISPLAY_GENERIC;
        }

        return $setting === self::DISPLAY_MESSAGE ? self::DISPLAY_MESSAGE : self::DISPLAY_GENERIC;
    }

    protected function getEmergencyCharset(): string
    {
        return defined('APP_CHARSET') ? APP_CHARSET : 'UTF-8';
    }

    protected function shouldOutputHttpHeaders(): bool
    {
        return !headers_sent() && !\in_array(\PHP_SAPI, ['cli', 'phpdbg'], true);
    }

    protected function escapeForHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $this->getEmergencyCharset());
    }

    private function fillErrorLevelsFromConfig(array $errors, array $errorConfiguration, string $key): array
    {
        $levels = [];
        foreach ($errors as $errorCode) {
            $level = $errorConfiguration[$key]['logLevel'] ?? 'INFO';
            $const = LogLevel::class . '::' . $level;
            $levels[$errorCode] = defined($const) ? constant($const) : LogLevel::INFO;
        }
        return $levels;
    }
}
