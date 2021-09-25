<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Client\Client as HttpClient;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Permission\IPService;
use Concrete\Core\View\View;
use Exception;
use Psr\Log\LogLevel;

class RecaptchaV3Controller extends AbstractController implements CaptchaInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Response error code: The secret parameter is missing.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_SECRET_MISSING = 'missing-input-secret';

    /**
     * Response error code: The secret parameter is invalid or malformed.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_SECRET_INVALID = 'invalid-input-secret';

    /**
     * Response error code: The response parameter is missing.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_RESPONSE_MISSING = 'missing-input-response';

    /**
     * Response error code: The response parameter is invalid or malformed.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_RESPONSE_INVALID = 'invalid-input-response';

    /**
     * Response error code: The request is invalid or malformed.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_REQUEST_INVALID = 'bad-request';

    /**
     * Response error code: The response is no longer valid: either is too old or has been used previously.
     *
     * @var string
     *
     * @see https://developers.google.com/recaptcha/docs/verify#error-code-reference
     */
    const ERRCODE_RESPONSE_TIMEOUT = 'timeout-or-duplicate';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\LoggerAwareInterface::getLoggerChannel()
     */
    public function getLoggerChannel()
    {
        return Channels::CHANNEL_SPAM;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::display()
     */
    public function display()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::showInput()
     */
    public function showInput()
    {
        $config = $this->app->make('config');
        $assetList = AssetList::getInstance();

        $assetUrl = $config->get('captcha.recaptcha_v3.url.javascript_asset');

        $assetList->register('javascript', 'recaptcha_api', $assetUrl, ['local' => false]);

        $assetList->registerGroup(
            'recaptcha_v3',
            [
                ['javascript', 'recaptcha_api'],
            ]
        );

        $responseAssets = ResponseAssetGroup::get();

        $isInlineCodeAlreadyInjected = false;

        foreach ($responseAssets->getRequiredAssetsToOutput() as $asset) {
            if ($asset->getAssetHandle() === "recaptcha_api") {
                $isInlineCodeAlreadyInjected = true;
                break;
            }
        }

        $responseAssets->requireAsset('recaptcha_v3');

        if (!$isInlineCodeAlreadyInjected) {
            $js = <<<EOL
<script type="text/javascript">
if (typeof window.RecaptchaV3 === "undefined") {
    window.RecaptchaV3 = function () {
        $('.recaptcha-v3').each(function () {
            $(this).data("clientId", grecaptcha.render(
                $(this).attr('id'),
                {
                    sitekey: $(this).data('sitekey'),
                    badge: $(this).data('badge'),
                    size: 'invisible'
                }
            ));
        });
		
        function recaptchaReady() {
            grecaptcha.ready(function () {
                $('.recaptcha-v3').each(function () {
                    grecaptcha.execute(
                        $(this).data("clientId"),
                        {
                            action: 'submit'
                        }
                    );
                });
            });
        }
		recaptchaReady();
		setInterval(recaptchaReady, 110000);
    };
}
</script>
EOL;

            // add inline script with recaptcha code to the footer
            View::getInstance()->addFooterItem($js);
        }

        echo sprintf(
            "<div id=\"%s\" class=\"grecaptcha-box recaptcha-v3\" data-sitekey=\"%s\" data-badge=\"%s\"></div>",
            uniqid('hwh'),
            h($config->get('captcha.recaptcha_v3.site_key')),
            h($config->get('captcha.recaptcha_v3.position'))
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::label()
     */
    public function label()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Captcha\CaptchaInterface::check()
     */
    public function check()
    {
        $config = $this->app->make('config');
        $queryString = http_build_query(
            [
                'secret' => $config->get('captcha.recaptcha_v3.secret_key'),
                'remoteip' => $config->get('captcha.recaptcha_v3.send_ip') ? (string)$this->app->make(
                    IPService::class
                )->getRequestIPAddress() : '',
                'response' => $this->request->request->get('g-recaptcha-response'),
            ]
        );
        $verifyUrl = $config->get('captcha.recaptcha_v3.url.verify');
        $verifyUrl .= (strpos($verifyUrl, '?') === false ? '?' : '&') . $queryString;

        $httpClient = $this->app->make(HttpClient::class);

        try {
            $response = $httpClient->get($verifyUrl);
        } catch (Exception $x) {
            $this->logger->alert(t('Error loading reCAPTCHA: %s', $x->getMessage()));

            return false;
        }
        $data = @json_decode($response->getBody(), true);
        if (!is_array($data)) {
            $this->logger->alert(t('Error loading reCAPTCHA: %s', t('invalid response')));

            return false;
        }

        if (!empty($data['error-codes'])) {
            switch (true) {
                case in_array(static::ERRCODE_SECRET_MISSING, $data['error-codes']):
                case in_array(static::ERRCODE_SECRET_INVALID, $data['error-codes']):
                    $logLevel = LogLevel::ALERT;
                    break;
                default:
                    // Don't ring the bells in case the client is sending mangled data: it's likely to happen in case of spammers
                    $logLevel = LogLevel::NOTICE;
                    break;
            }
            $this->logger->log($logLevel, t('Errors in reCAPTCHA validation: %s', implode(', ', $data['error-codes'])));
        }

        $score = array_get($data, 'score');
        if (!is_numeric($score)) {
            // This should happen only when 'error-codes' is not empty, so we already logged the error(s).
            return false;
        }
        $score = (float)$score;
        $minimumScore = $config->get('captcha.recaptcha_v3.score');
        if (array_get($data, 'action') === 'submit' && array_get(
                $data,
                'success'
            ) === true && $score >= $minimumScore) {
            return true;
        }

        if ($config->get('captcha.recaptcha_v3.log_score') && $score < $minimumScore) {
            $this->logger->notice(
                t('reCAPTCHA V3 blocked as score returned (%1$s) is below the threshold (%2$s)', $score, $minimumScore)
            );
        }

        return false;
    }

    public function saveOptions($data)
    {
        $data = (is_array($data) ? $data : []) + [
                'site_key' => '',
                'secret_key' => '',
                'score' => 0.5,
                'position' => 'bottomright',
                'log_score' => false,
                'send_ip' => false,
            ];
        $config = $this->app->make('config');
        $config->save('captcha.recaptcha_v3.site_key', (string)$data['site_key']);
        $config->save('captcha.recaptcha_v3.secret_key', (string)$data['secret_key']);
        $config->save('captcha.recaptcha_v3.score', (float)$data['score']);
        $config->save('captcha.recaptcha_v3.position', (string)$data['position']);
        $config->save('captcha.recaptcha_v3.log_score', (bool)$data['log_score']);
        $config->save('captcha.recaptcha_v3.send_ip', (bool)$data['send_ip']);
    }
}