<?php
/**
 * @var Concrete\Core\Attribute\View $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $akTextareaDisplayMode
 * @var string|null $value
 */

$value = $value ?? '';

// switch display type here
if ($akTextareaDisplayMode == 'text' || $akTextareaDisplayMode == '') { ?>

    <?php
    echo $form->textarea(
        $view->controller->field('value'),
        htmlspecialchars($value, ENT_QUOTES, APP_CHARSET),
        array('rows' => 5)
    );
    ?>

<?php } else {
    $requestValue = $form->getRequestValue($view->controller->field('value'));
    if (is_string($requestValue)) {
        $value = $requestValue;
    }

    /**
     * @TODO - add another option in the controller for creating a sanitized, simple editor, and make it use
     * `outputSimpleEditor`. Right now we can't just wholesale switch over to the simple editor because people
     * are complaining. This was originally changed for https://hackerone.com/reports/616770 but now must be changed
     * back to the standard editor, until we have an option in the textarea attribute type controller that lets people
     * opt in to the sanitized version
     */
    /*
    echo Core::make('editor')->outputSimpleEditor(
        $view->controller->field('value'),
        htmlspecialchars($value, ENT_QUOTES, APP_CHARSET)
    );*/
    echo Core::make('editor')->outputStandardEditor(
        $view->controller->field('value'),
        htmlspecialchars($value, ENT_QUOTES, APP_CHARSET)
    );

}
