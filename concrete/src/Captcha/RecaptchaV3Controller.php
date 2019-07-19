<?php

namespace Concrete\Core\Captcha;

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Support\Facade\Log;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Http\Request;

class RecaptchaV3Controller extends AbstractController implements CaptchaInterface

{

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

        $assetList = AssetList::getInstance();

        $assetUrl = 'https://www.google.com/recaptcha/api.js?onload=RecaptchaV3&render=explicit';

        $assetList->register('javascript', 'recaptcha_api', $assetUrl, array('local' => false));
        $assetList->register('javascript', 'recaptcha_render', 'js/captcha/recaptchav3.js', array(), 'recaptcha_v3');


        $assetList->registerGroup(
            'recaptcha_v3',
            array(
                array('javascript', 'recaptcha_render'),
                array('javascript', 'recaptcha_api'),
            )
        );

        $responseAssets = ResponseAssetGroup::get();
        $responseAssets->requireAsset('recaptcha_v3');

        echo '<div id="' . uniqid('hwh') . '" class="grecaptcha-box recaptcha-v3" data-sitekey="' . Config::get('recaptchaV3.site_key') . '" data-badge="' . Config::get('recaptchaV3.position') . '"></div>';

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

        $iph = '';

        if (Config::get('hw_recaptcha.sendIP') == "yes") {
            $iph = (string)$app->make(IPService::class)->getRequestIPAddress();
        }

        $qsa = http_build_query(
            array(
                'secret' => Config::get('recaptchaV3.secret_key'),
                'remoteip' => $iph,
                'response' => $this->request->request->get('g-recaptcha-response')
            )
        );

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify?' . $qsa);

        if (Config::get('concrete.proxy.host') != null) {
            curl_setopt($ch, CURLOPT_PROXY, Config::get('concrete.proxy.host'));
            curl_setopt($ch, CURLOPT_PROXYPORT, Config::get('concrete.proxy.port'));

            // Check if there is a username/password to access the proxy
            if (Config::get('concrete.proxy.user') != null) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, Config::get('concrete.proxy.user') . ':' . Config::get('concrete.proxy.password')
                );
            }
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Config::get('app.curl.verifyPeer'));

        $response = curl_exec($ch);

        if ($response !== false) {
            $data = json_decode($response, true);


            if (isset($data['error-codes']) && (in_array('missing-input-secret', $data['error-codes']) || in_array('invalid-input-secret', $data['error-codes']))) {
                Log::addError(t('The reCAPTCHA secret parameter is invalid or malformed.'));
            }

            if ($data['success'] == true && $data['score'] > Config::get('recaptchaV3.score') && $data['action'] == 'submit') {
                return true;
            } else {
                if (Config::get('hw_recaptcha.logscore') == 1 && Config::get('hw_recaptcha.score') >= $data['score']) {
                    $formmessage = $r->request->all();
                    if (isset($formmessage['recaptcha_key'])) {
                        unset($formmessage['recaptcha_key']);
                    }
                    if (isset($formmessage['recaptcha_position'])) {
                        unset($formmessage['recaptcha_position']);
                    }
                    if (isset($formmessage['g-recaptcha-response'])) {
                        unset($formmessage['g-recaptcha-response']);
                    }
                    Log::addError(t('reCAPTCHAv3 captcha blocked as score returned (' . $data['score'] . ') is below the threshold set (' . Config::get('hw_recaptcha.score') . ') %s', var_export($formmessage, true)));
                }
                return false;

            }
        } else {
            Log::addError(t('Error loading reCAPTCHA: %s', curl_error($ch)));
            return false;
        }
    }


    public function saveOptions($data)
    {

        Config::save('recaptchaV3.site_key', $data['site']);
        Config::save('recaptchaV3.secret_key', $data['secret']);
        Config::save('recaptchaV3.score', $data['score']);
        Config::save('recaptchaV3.position', $data['position']);
        Config::save('recaptchaV3.logscore', $data['logscore']);
        Config::save('recaptchaV3.sendIP', $data['sendip']);
    }
}
