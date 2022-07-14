<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;

class ExportOptions extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('csvAddBom', (bool) $config->get('concrete.export.csv.include_bom'));
        $this->set('datetimeFormat', $config->get('concrete.export.csv.datetime_format'));

        $predefinedConstants = [
            'ATOM' => 'ATOM (' . date(DATE_ATOM) . ')',
            'COOKIE' => 'COOKIE (' . date(DATE_COOKIE) . ')',
            'RFC822' => 'RFC822 (' . date(DATE_RFC822) . ')',
            'RFC850' => 'RFC850 (' . date(DATE_RFC850) . ')',
            'RFC1036' => 'RFC1036 (' . date(DATE_RFC1036) . ')',
            'RFC1123' => 'RFC1123 (' . date(DATE_RFC1123) . ')',
            'RFC7231' => 'RFC7231 (' . date(DATE_RFC7231) . ')',
            'RFC2822' => 'RFC2822 (' . date(DATE_RFC2822) . ')',
            'RFC3339' => 'RFC3339 (' . date(DATE_RFC3339) . ')',
            'RFC3339_EXTENDED' => 'RFC3339_EXTENDED (' . date(DATE_RFC3339_EXTENDED) . ')',
            'RSS' => 'RSS (' . date(DATE_RSS) . ')',
            'W3C' => 'W3C (' . date(DATE_W3C) . ')',
        ];
        $this->set('predefinedConstants', $predefinedConstants);
    }

    public function submit()
    {
        $post = $this->request->request;
        $config = $this->app->make('config');
        if (!$this->token->validate('ccm-export-options')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config->save('concrete.export.csv.include_bom', (bool) $post->get('csvAddBom'));
        $config->save('concrete.export.csv.datetime_format', $post->get('datetimeFormat'));
        $this->flash('success', t('Options saved successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
    }
}
