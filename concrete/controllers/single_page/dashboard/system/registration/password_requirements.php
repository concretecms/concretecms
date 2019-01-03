<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Http\Request;
use Concrete\Core\Page\Controller\DashboardPageController;

class PasswordRequirements extends DashboardPageController
{

    const CONFIG_PREFIX = 'concrete.user.password';

    public function view()
    {

        $config = $this->app['config'][self::CONFIG_PREFIX];

        $this->set('max', (int)max(0, array_get($config, 'maximum', 0)));
        $this->set('min', (int)max(0, array_get($config, 'minimum', 0)));

        $this->set('specialCharacters', (int)max(0, array_get($config, 'required_special_characters', 0)));
        $this->set('upperCase', (int)max(0, array_get($config, 'required_upper_case', 0)));
        $this->set('lowerCase', (int)max(0, array_get($config, 'required_lower_case', 0)));
        $this->set('passwordReuse', (int)max(0, array_get($config, 'reuse', 0)));
        $this->set('customRegex', (array)array_get($config, 'custom_regex', []));
        $this->set('saveAction', $this->action('save'));


    }

    protected function validate(Request $request)
    {
        $args = $request->request->all();
        $regex = array_get($args, 'regex', []);

        foreach ($regex as $key => $value) {
            if (!$this->validateRegex($value)) {
                $this->error->add('Invalid custom regex', "regex[$key]");
            }
        }

        return !$this->error->has();
    }

    public function save()
    {
        if (!$this->validate($this->request)) {
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
        $this->redirect('/dashboard/system/registration/password_requirements');
    }

    public function reset()
    {
        if (!$this->token->validate('restore_defaults')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            $this->view();
        } else {
            $prefix = self::CONFIG_PREFIX;

            $config = $this->app->make('config');

            $item = $config->get($prefix);
            unset($item['minimum']);
            unset($item['maximum']);
            unset($item['required_special_characters']);
            unset($item['required_upper_case']);
            unset($item['required_lower_case']);
            unset($item['reuse']);
            unset($item['custom_regex']);

            $config->save($prefix, $item);

            $this->flash('success', t('Password Options successfully reset to default values.'));
            $this->redirect('/dashboard/system/registration/password_requirements');
        }
    }

    /**
     * Normalize a given number
     *
     * @param array $args
     * @param string $key
     *
     * @return mixed
     */
    protected function int(array $args, $key)
    {
        return max(0, (int)array_get($args, $key, 0));
    }

    /**
     * Check if a given regular expression is valid
     *
     * @param $regex
     *
     * @return bool
     */
    protected function validateRegex($regex)
    {
        // If this test returns false it means we have invalid regex
        return @preg_match($regex, null) !== false;
    }

}
