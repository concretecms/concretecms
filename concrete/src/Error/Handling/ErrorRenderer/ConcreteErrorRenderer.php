<?php

namespace Concrete\Core\Error\Handling\ErrorRenderer;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\Error\HtmlAwareErrorInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Permission\Checker;
use Concrete\Core\View\ErrorView;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ConcreteErrorRenderer implements ErrorRendererInterface
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Permission\Checker
     */
    protected $checker;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    public function __construct(Repository $config, Checker $checker, Request $request)
    {
        $this->config = $config;
        $this->checker = $checker;
        $this->request = $request;
    }

    public function render(\Throwable $exception): FlattenException
    {
        return $this->isClientExpectingJson() ? $this->renderJson($exception) : $this->renderHtml($exception);
    }

    protected function isClientExpectingJson(): bool
    {
        try {
            $format = $this->request->getPreferredFormat();
        } catch (\Throwable $x) {
            return false;
        }
        return $format === 'json' || $format === 'jsonld';
    }

    /**
     * @return string 'debug' to show debug information, 'message' to show the exception message, something else to display a generic message
     */
    protected function getSetting(): string
    {
        if ($this->checker->canViewDebugErrorInformation()) {
            return (string) $this->config->get('concrete.error.display.privileged', '');
        }
        return (string) $this->config->get('concrete.error.display.guests', '');
    }

    protected function renderJson(\Throwable $exception): FlattenException
    {
        if ($exception instanceof UserMessageException) {
            $data = $exception->jsonSerialize();
        } else {
            $setting = $this->getSetting();
            if (in_array($setting, ['debug', 'message', true])) {
                $data = [
                    'error' => true,
                    'errors' => [$exception->getMessage()],
                ];
                if ($exception instanceof HtmlAwareErrorInterface) {
                    if ($exception->messageContainsHtml()) {
                        $data['htmlErrorIndexes'] = [0];
                    }
                }
                if ($setting === 'debug') {
                    $data['trace'] = $exception->getTrace();
                }
            } else {
                $data = [
                    'error' => true,
                    'errors' => [t('An error occurred while processing this request.')],
                ];
            }
        }
        $finalException = FlattenException::createFromThrowable($exception, null, ['Content-Type' => 'application/json; charset=' . APP_CHARSET]);
        $finalException->setAsString(json_encode($data));

        return $finalException;
    }

    protected function renderHtml(\Throwable $exception): FlattenException
    {
        if ($exception instanceof UserMessageException) {
            $setting = 'message';
        } else {
            $setting = $this->getSetting();
        }
        if ($setting === 'debug') {
            // Let's load the symfony debug html renderer, but then use Dom/Xpath to remove symfony-specific
            // things that we can't control. Ideally we'd have a lot more control here but we don't so let's
            // hack away the things that we can.
            $htmlErrorRenderer = new HtmlErrorRenderer(true);
            $originalHtml = $htmlErrorRenderer->render($exception)->getAsString();
            $document = new \DOMDocument();
            set_error_handler(static function() {}, -1);
            try {
                $document->loadHTML($originalHtml);
                $xpath = new \DOMXPath($document);
                // Remove symfony header
                foreach ($xpath->query('//header') as $header) {
                    $header->parentNode->removeChild($header);
                }
                // Remove symfony illustration
                foreach ($xpath->query('//*[contains(attribute::class, "exception-illustration")]') as $illustration) {
                    $illustration->parentNode->removeChild($illustration);
                }
                $html = $document->saveHTML();
            } finally {
                restore_error_handler();
            }
        } else {
            $o = new \stdClass;
            $o->title = t('An unexpected error occurred.');
            if ($setting === 'message') {
                if ($exception instanceof HtmlAwareErrorInterface && $exception->messageContainsHtml()) {
                    $o->content = $exception->getMessage();
                } else {
                    $o->content = nl2br(h($exception->getMessage()));
                }
            } else {
                $o->content = t('An error occurred while processing this request.');
            }
            $ve = new ErrorView($o);
            $html = $ve->render();
        }
        $finalException = FlattenException::createFromThrowable($exception, null, ['Content-Type' => 'text/html; charset=' . APP_CHARSET]);
        $finalException->setAsString($html);

        return $finalException;
    }
}
