<?php
namespace Concrete\Attribute\Rating;

use View;
use Loader;

class Service
{
    public function outputDisplay($value)
    {
        $v = View::getInstance();
        $v->requireAsset('core/rating');

        $html = '';
        $star1 = 'fa-star-o';
        $star2 = 'fa-star-o';
        $star3 = 'fa-star-o';
        $star4 = 'fa-star-o';
        $star5 = 'fa-star-o';

        if ($value > 4) {
            $star1 = 'fa-star-half-o';
        }
        if ($value > 14) {
            $star1 = 'fa-star';
        }
        if ($value > 24) {
            $star2 = 'fa-star-half-o';
        }
        if ($value > 34) {
            $star2 = 'fa-star';
        }
        if ($value > 44) {
            $star3 = 'fa-star-half-o';
        }
        if ($value > 54) {
            $star3 = 'fa-star';
        }
        if ($value > 64) {
            $star4 = 'fa-star-half-o';
        }
        if ($value > 74) {
            $star4 = 'fa-star';
        }
        if ($value > 84) {
            $star5 = 'fa-star-half-o';
        }
        if ($value > 94) {
            $star5 = 'fa-star';
        }
        $html .= '<div class="ccm-attribute ccm-attribute-rating ccm-rating">';
        $html .= '<div class="fa ' . $star1 . '"><a href="javascript:void(0)"></a></div>';
        $html .= '<div class="fa ' . $star2 . '"><a href="javascript:void(0)"></a></div>';
        $html .= '<div class="fa ' . $star3 . '"><a href="javascript:void(0)"></a></div>';
        $html .= '<div class="fa ' . $star4 . '"><a href="javascript:void(0)"></a></div>';
        $html .= '<div class="fa ' . $star5 . '"><a href="javascript:void(0)"></a></div>';
        $html .= '</div>';

        return $html;
    }

    public function output($field, $value)
    {
        $v = View::getInstance();
        $v->requireAsset('core/rating');

        $form = Loader::helper("form");
        $v = $form->getRequestValue($field);
        if ($v !== false) {
            $value = $v;
        }

        $sanitized = preg_replace('/[^A-Za-z0-9]/i', '', $field);
        $html = '<div class="ccm-attribute ccm-attribute-rating ccm-rating" data-rating-field-name="' . $sanitized . '" data-score="' . $value . '"></div>';
        $html .= "<script type=\"text/javascript\">
            $(function() {
                $('div[data-rating-field-name={$sanitized}]').awesomeStarRating({
                    'name': \"{$field}\"
                });
            });</script>";

        return $html;

        /*
        $html = '';
        $checked1 = ($value == 20) ? 'checked' : '';
        $checked2 = ($value == 40) ? 'checked' : '';
        $checked3 = ($value == 60) ? 'checked' : '';
        $checked4 = ($value == 80) ? 'checked' : '';
        $checked5 = ($value == 100) ? 'checked' : '';


        $html .= "<div class=\"ccm-rating\" id=\"ccm-rating-{$field}\">
            <input name=\"{$field}\" type=\"radio\" value=\"20\" {$checked1} {$disabled}/>
            <input name=\"{$field}\" type=\"radio\" value=\"40\" {$checked2} {$disabled}/>
            <input name=\"{$field}\" type=\"radio\" value=\"60\" {$checked3} {$disabled} />
            <input name=\"{$field}\" type=\"radio\" value=\"80\" {$checked4} {$disabled}/>
            <input name=\"{$field}\" type=\"radio\" value=\"100\" {$checked5} {$disabled}/>
        </div>";
        if ($includeJS) {
            $html .= "<script type=\"text/javascript\">
                $(function() {
                    $('input[name=\"{$field}\"]').rating();
                });
                </script>";
        }
        return $html;
        */
    }
}
