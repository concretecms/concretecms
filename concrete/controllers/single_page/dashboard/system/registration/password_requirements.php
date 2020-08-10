<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;

class PasswordRequirements extends DashboardPageController
{
    public const CONFIG_PREFIX = 'concrete.user.password';

    public function view()
    {
        $config = $this->app->make('config')->get(self::CONFIG_PREFIX);

        $this->set('min', (int) max(0, array_get($config, 'minimum', 0)));
        $this->set('max', (int) max(0, array_get($config, 'maximum', 0)) ?: null);

        $this->set('specialCharacters', (int) max(0, array_get($config, 'required_special_characters', 0)));
        $this->set('upperCase', (int) max(0, array_get($config, 'required_upper_case', 0)));
        $this->set('lowerCase', (int) max(0, array_get($config, 'required_lower_case', 0)));
        $this->set('passwordReuse', (int) max(0, array_get($config, 'reuse', 0)));
        if (!array_key_exists('customRegex', $this->getSets())) {
            $this->set('customRegex', (array) array_get($config, 'custom_regex', []));
        }
    }

    public function save()
    {
        if (!$this->token->validate('save_password_requirements')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $this->validate($this->request);

        if ($this->error->has()) {
            $this->setCustomRegexForView();

            return $this->view();
        }

        $args = $this->request->request->all();
        $prefix = self::CONFIG_PREFIX;

        $config = $this->app->make('config');
        $config->save($prefix . '.minimum', $this->int($args, 'min'));
        $config->save($prefix . '.maximum', $this->int($args, 'max'));
        $config->save($prefix . '.required_special_characters', $this->int($args, 'specialCharacters'));
        $config->save($prefix . '.required_upper_case', $this->int($args, 'upperCase'));
        $config->save($prefix . '.required_lower_case', $this->int($args, 'lowerCase'));
        $config->save($prefix . '.reuse', $this->int($args, 'passwordReuse'));

        $regex = array_get($args, 'regex', []);
        $regexDesc = array_get($args, 'regex_desc', []);
        $regexWidthDesc = array_combine($regex, $regexDesc);

        $regexRequirements = array_get($args, 'regexRequirements', []);
        $config->save($prefix . '.custom_regex', array_merge($regexWidthDesc, $regexRequirements));

        $this->flash('success', t('Password Options successfully saved.'));

        return $this->buildRedirect($this->action());
    }

    public function reset()
    {
        if (!$this->token->validate('restore_defaults')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }

        $prefix = self::CONFIG_PREFIX;

        $config = $this->app->make('config');

        $item = $config->get($prefix);

        unset($item['minimum'], $item['maximum'], $item['required_special_characters'], $item['required_upper_case'], $item['required_lower_case'], $item['reuse'], $item['custom_regex']);

        $config->save($prefix, $item);

        $this->flash('success', t('Password Options successfully reset to default values.'));

        return $this->buildRedirect($this->action());
    }

    protected function validate(Request $request)
    {
        $result = true;
        $regex = $request->request->get('regex', []);
        foreach ($regex as $key => $value) {
            if (!$this->validateRegex($value)) {
                $this->error->add('Invalid custom regex', "regex[{$key}]");
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Normalize a given number.
     *
     * @param array $args
     * @param string $key
     *
     * @return int
     */
    protected function int(array $args, $key)
    {
        return max(0, (int) array_get($args, $key, 0));
    }

    /**
     * Check if a given regular expression is valid.
     *
     * @param $regex
     *
     * @return bool
     */
    protected function validateRegex($regex)
    {
        set_error_handler(function () {}, -1);
        try {
            // If this test returns false it means we have invalid regex
            return @preg_match($regex, null) !== false;
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Store in the 'customRegex' "set" the previously specified regular expressions.
     * That way, users won't loose what they already typed in case of problems.
     */
    protected function setCustomRegexForView(): void
    {
        $post = $this->request->request;
        $regex = $post->get('regex');
        $regexDesc = $post->get('regex_desc');
        if (is_array($regex) && is_array($regexDesc) && count($regex) === count($regexDesc)) {
            $this->set('customRegex', array_combine($regex, $regexDesc));
        }
    }
}
