<?php

/** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\File\Search\Result\Result;

$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
/** @var Result $result */
?>

<div data-search="files" class="ccm-ui">
    <?php
        /** @noinspection PhpUnhandledExceptionInspection */
        View::element('files/search', ['result' => $result])
    ?>
</div>

<script type="text/javascript">
    $(function () {
        $('div[data-search=files]').concreteFileManager({
            result: <?php echo json_encode($result->getJSONObject()); ?>,
            selectMode: 'choose',
            upload_token: '<?php echo $token->generate(); ?>'
        });
    });
</script>
