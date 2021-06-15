<?php

/** @noinspection PhpDeprecationInspection */
/** @noinspection PhpUnusedParameterInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\File;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\View\View;
use HtmlObject\Element;
use HtmlObject\Input;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Legacy\FilePermissions;

/** @noinspection PhpUnhandledExceptionInspection */
View::element(
    'files/add_to_sets',

    [
        'displayFileSet' => function ($fileset) use ($files) {
            /** @var Set $fileset */
            /** @var File[] $files */

            $fp = FilePermissions::getGlobal();

            foreach ($files as $f) {
                /** @noinspection PhpUndefinedMethodInspection */
                if (!$fp->canAddFileType(strtolower($f->getExtension()))) {
                    return false;
                }
            }

            return true;
        },

        'getCheckbox' => function ($fileset) use ($files) {
            /** @var Set $fileset */
            /** @var File[] $files */

            $app = Application::getFacadeApplication();
            /** @var Identifier $idHelper */
            $idHelper = $app->make(Identifier::class);

            $id = "checkbox-" . $idHelper->getString();

            $checkbox = new Input('checkbox');
            $checkbox->setAttribute('data-set', $fileset->getFileSetID());
            $checkbox->addClass("form-check-input");
            $checkbox->setAttribute("id", $id);

            $input = new Input('hidden', 'fsID:' . $fileset->getFileSetID(), 0);
            $input->setAttribute('data-set-input', $fileset->getFileSetID());

            $found = 0;

            foreach ($files as $f) {
                if ($f->inFileSet($fileset)) {
                    ++$found;
                }
            }

            /** @noinspection PhpStatementHasEmptyBodyInspection */
            if ($found == 0) {
                // nothing
            } elseif ($found == count($files)) {
                $checkbox->setAttribute('checked', 'checked');
                $checkbox->setAttribute('value', '2');
            } else {
                $checkbox->setAttribute('indeterminate', '1');
                $checkbox->addClass('tristate');
                $checkbox->setAttribute('value', '1');
            }

            $label = new Element('label');
            $label->addClass("form-check-label");
            $label->setAttribute("for", $id);
            $label->setValue($fileset->getFileSetDisplayName());

            $div = new Element('div');

            $div->addClass("form-check");

            /** @noinspection PhpParamsInspection */
            $div->appendChild($checkbox)->appendChild($label)->appendChild($input);

            return $div;
        },
    ]
);
?>

<script type="text/javascript">
    $(function () {
        $('#ccm-file-set-list input.tristate').tristate({
            change: function (state) {
                let $input = $('input[data-set-input=' + $(this).attr('data-set') + ']');

                if (state === null) {
                    $input.val(1);
                } else if (state === true) {
                    $input.val(2);
                } else if (state !== true) {
                    $input.val(0);
                }
            }
        });
        $('#ccm-file-set-list input[type=checkbox]:not(".tristate")').on('change', function () {
            let $input = $('input[data-set-input=' + $(this).attr('data-set') + ']');

            if ($(this).is(':checked')) {
                $input.val(2);
            } else {
                $input.val(0);
            }
        });
    });
</script>
