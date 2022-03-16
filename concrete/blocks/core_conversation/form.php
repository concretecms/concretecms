<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\File\Service\Application as FileApplication;
use Concrete\Core\Form\Service\Widget\UserSelector;
use Concrete\Core\Support\Facade\Application;

/** @var Concrete\Block\CoreConversation\Controller $controller */
/** @var int $cnvID */
/** @var int $enablePosting */
/** @var bool $paginate */
/** @var int $itemsPerPage */
/** @var string $displayMode */
/** @var string|null $orderBy */
/** @var bool $enableOrdering */
/** @var bool $enableCommentRating */
/** @var bool $enableTopCommentReviews */
/** @var bool $displaySocialLinks */
/** @var int $reviewAggregateAttributeKey */
/** @var string $displayPostingForm */
/** @var string $addMessageLabel */
/** @var string $dateFormat */
/** @var string|null $customDateFormat */

/** @var array<string|int,string>|null $reviewAttributeKeys */
/** @var int $maxFilesGuest */
/** @var int $maxFilesRegistered */
/** @var int $maxFileSizeGuest */
/** @var int $maxFileSizeRegistered */
/** @var string $fileExtensions */
/** @var bool $attachmentsEnabled */
/** @var bool $attachmentOverridesEnabled */
/** @var bool|null $notificationOverridesEnabled */
/** @var bool $subscriptionEnabled */
/** @var \Concrete\Core\User\UserInfo[] $notificationUsers */
/** @var Concrete\Core\Form\Service\Form $form */

$app = Application::getFacadeApplication();
/** @var Concrete\Core\Config\Repository\Repository $config */
$config = $app->make(Repository::class);

/** @var Concrete\Core\File\Service\Application $helperFile */
$helperFile = $app->make(FileApplication::class);
/** @var Concrete\Core\Form\Service\Widget\UserSelector $userSelector */
$userSelector = $app->make(UserSelector::class);

if ($controller->getAction() === 'add') {
    $enablePosting = 1;
    $paginate = 1;
    $itemsPerPage = 50;
    $displayMode = 'threaded';
    $enableOrdering = 1;
    $enableCommentRating = 1;
    $enableTopCommentReviews = 0;
    $displaySocialLinks = 1;
    $displayPostingForm = 'top';
    $addMessageLabel = t('Add Message');
    $attachmentOverridesEnabled = 0;
    $attachmentsEnabled = 1;
    $fileAccessFileTypes = $config->get('conversations.files.allowed_types');

    //is nothing's been defined, display the constant value
    if (!$fileAccessFileTypes) {
        $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($config->get('concrete.upload.extensions'));
    } else {
        $fileAccessFileTypes = $helperFile->unserializeUploadFileExtensions($fileAccessFileTypes);
    }

    $maxFileSizeGuest = $config->get('conversations.files.guest.max_size');
    $maxFileSizeRegistered = $config->get('conversations.files.registered.max_size');
    $maxFilesGuest = $config->get('conversations.files.guest.max');
    $maxFilesRegistered = $config->get('conversations.files.registered.max');
    $fileExtensions = implode(',', $fileAccessFileTypes);
    $attachmentsEnabled = (int) ($config->get('conversations.attachments_enabled'));
    $notificationUsers = Conversation::getDefaultSubscribedUsers();
    $subscriptionEnabled = (int) ($config->get('conversations.subscription_enabled'));
}
$fileAccessFileTypesDenylist = $config->get('conversations.files.disallowed_types');

if ($fileAccessFileTypesDenylist === null) {
    $fileAccessFileTypesDenylist = $config->get('concrete.upload.extensions_denylist', $config->get('concrete.upload.extensions_blacklist'));
}
/** @var string[]|string|false|null $fileAccessFileTypesDenylist All of the possible returns from preg_* methods */
$fileAccessFileTypesDenylist = $helperFile->unSerializeUploadFileExtensions($fileAccessFileTypesDenylist);

if (empty($dateFormat)) {
    $dateFormat = 'default';
}

?>

<fieldset>
    <legend>
        <?php echo t('Message List') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('displayMode', t('Display Mode')); ?>

        <div class="form-check">
            <?php echo $form->radio('displayMode', 'threaded', $displayMode, ['name' => 'displayMode', 'id' => 'displayModeThreaded']) ?>
            <?php echo $form->label('displayModeThreaded', t('Threaded'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('displayMode', 'flat', $displayMode, ['name' => 'displayMode', 'id' => 'displayModeFlat']) ?>
            <?php echo $form->label('displayModeFlat', t('Flat'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('orderBy', t('Ordering')); ?>
        <?php echo $form->select('orderBy', ['date_asc' => t('Earliest First'), 'date_desc' => t('Most Recent First'), 'rating' => t('Highest Rated')], $orderBy ?? '') ?>

        <div class="form-check">
            <?php echo $form->checkbox('enableOrdering', 1, $enableOrdering) ?>
            <?php echo $form->label('enableOrdering', t('Display Ordering Option in Page'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Rating')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('enableCommentRating', 1, $enableCommentRating) ?>
            <?php echo $form->label('enableCommentRating', t('Enable Comment Rating'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('enableTopCommentReviews', 1, $enableTopCommentReviews) ?>
            <?php echo $form->label('enableTopCommentReviews', t('Turn Top-Level Posts into Reviews'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <?php if (isset($reviewAttributeKeys)) { ?>
        <div class="form-group" data-unhide="[name=enableTopCommentReviews]">
            <?php echo $form->label('reviewAggregateAttributeKey', t('Aggregate Ratings by Attribute')); ?>

            <?php if (count($reviewAttributeKeys) > 0) { ?>
                <?php echo $form->select('reviewAggregateAttributeKey', $reviewAttributeKeys, $reviewAggregateAttributeKey); ?>
            <?php } else { ?>
                <div class="alert alert-info">
                    <?php echo t('Create a page attribute of type "%s" in order to aggregate ratings.', tc('AttributeTypeName', 'Rating')) ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="form-group">
        <?php echo $form->label('displaySocialLinks', t('Social Sharing Links')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('displaySocialLinks', 1, $displaySocialLinks) ?>
            <?php echo $form->label('displaySocialLinks', t('Display social sharing links'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('paginate', t('Paginate Message List')); ?>

        <div class="form-check">
            <?php echo $form->radio('paginate', 0, $paginate, ['name' => 'paginate', 'id' => 'paginateNo']) ?>
            <?php echo $form->label('paginateNo', t('No, display all messages.'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('paginate', 1, $paginate, ['name' => 'paginate', 'id' => 'paginateYes']) ?>
            <?php echo $form->label('paginateYes', t('Yes, display only a sub-set of messages at a time.'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group" data-row="itemsPerPage">
        <?php echo $form->label('itemsPerPage', t('Messages Per Page')); ?>
        <?php echo $form->text('itemsPerPage', (string) $itemsPerPage, ['class' => 'span1']) ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Posting') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('addMessageLabel', t('Add Message Label')) ?>
        <?php echo $form->text('addMessageLabel', $addMessageLabel) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Enable Posting')); ?>

        <div class="form-check">
            <?php echo $form->radio('enablePosting', 1, $enablePosting, ['name' => 'enablePosting', 'id' => 'enablePostingYes']) ?>
            <?php echo $form->label('enablePostingYes', t('Yes, this conversation accepts messages and replies.'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('enablePosting', 0, $enablePosting, ['name' => 'enablePosting', 'id' => 'enablePostingNo']) ?>
            <?php echo $form->label('enablePostingNo', t('No, posting is disabled.'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Display Posting Form')); ?>

        <div class="form-check">
            <?php echo $form->radio('displayPostingForm', 'top', $displayPostingForm, ['name' => 'displayPostingForm', 'id' => 'displayPostingFormTop']) ?>
            <?php echo $form->label('displayPostingFormTop', t('Top'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('displayPostingForm', 'bottom', $displayPostingForm, ['name' => 'displayPostingForm', 'id' => 'displayPostingFormBottom']) ?>
            <?php echo $form->label('displayPostingFormBottom', t('Bottom'), ['class' => 'form-check-label']) ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <div class="form-group">
        <?php echo $form->label('', t('Date Format')); ?>

        <div class="form-check">
            <?php echo $form->radio('dateFormat', 'default', $dateFormat, ['name' => 'dateFormat', 'id' => 'dateFormatDefault']) ?>
            <?php echo $form->label('dateFormatDefault', t('Use Site Default.'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('dateFormat', 'elapsed', $dateFormat, ['name' => 'dateFormat', 'id' => 'dateFormatElapsed']) ?>
            <?php echo $form->label('dateFormatElapsed', t('Time elapsed since post.'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('dateFormat', 'custom', $dateFormat, ['name' => 'dateFormat', 'id' => 'dateFormatCustom']) ?>
            <?php echo $form->label('dateFormatCustom', t('Custom Date Format'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="form-group" data-row="customDateFormat">
            <?php echo $form->text('customDateFormat', $customDateFormat ?? '') ?>

            <div class="help-block">
                <?php echo sprintf(t('See the formatting options for date at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('File Attachment Management') ?>
    </legend>

    <p class="help-block">
        <?php echo t('Note: Entering values here will override global conversations file attachment settings for this block if you enable Attachment Overrides for this Conversation.') ?>
    </p>

    <div class="form-group">

        <div class="form-check">
            <?php echo $form->checkbox('attachmentOverridesEnabled', 1, $attachmentOverridesEnabled) ?>
            <?php echo $form->label('attachmentOverridesEnabled', t('Enable Attachment Overrides'), ['class' => 'form-check-label']) ?>
        </div>

        <div class="attachment-overrides">
            <div class="form-check">
                <?php echo $form->checkbox('attachmentsEnabled', 1, $attachmentsEnabled) ?>
                <?php echo $form->label('attachmentsEnabled', t('Enable Attachments'), ['class' => 'form-check-label']) ?>
            </div>
        </div>
    </div>

    <div class="form-group attachment-overrides">
        <?php echo $form->label('maxFileSizeGuest', t('Max Attachment Size for Guest Users. (MB)')); ?>

        <div class="controls">
            <?php echo $form->text('maxFileSizeGuest', $maxFileSizeGuest > 0 ? $maxFileSizeGuest : '') ?>
        </div>
    </div>

    <div class="form-group attachment-overrides">
        <?php echo $form->label('maxFileSizeRegistered', t('Max Attachment Size for Registered Users. (MB)')); ?>

        <div class="controls">
            <?php echo $form->text('maxFileSizeRegistered', $maxFileSizeRegistered > 0 ? $maxFileSizeRegistered : '') ?>
        </div>
    </div>

    <div class="form-group attachment-overrides">
        <?php echo $form->label('maxFilesGuest', t('Max Attachments Per Message for Guest Users.')); ?>

        <div class="controls">
            <?php echo $form->text('maxFilesGuest', $maxFilesGuest > 0 ? $maxFilesGuest : '') ?>
        </div>
    </div>

    <div class="form-group attachment-overrides">
        <?php echo $form->label('maxFilesRegistered', t('Max Attachments Per Message for Registered Users.')); ?>

        <div class="controls">
            <?php echo $form->text('maxFilesRegistered', $maxFilesRegistered > 0 ? $maxFilesRegistered : '') ?>
        </div>
    </div>

    <div class="form-group attachment-overrides">
        <?php echo $form->label('fileExtensions', t('Allowed File Extensions (Comma separated, no periods).')); ?>

        <div class="controls">
            <?php echo $form->textarea('fileExtensions', $fileExtensions) ?>

            <?php if (is_array($fileAccessFileTypesDenylist) && count($fileAccessFileTypesDenylist) > 0) { ?>
                <div class="text-muted small">
                    <?php echo t('These file extensions will always be blocked: %s', '<code>' . implode('</code>, <code>', $fileAccessFileTypesDenylist) . '</code>') ?>
                    <br/>
                    <?php echo t('If you want to unblock these extensions, you have to manually set the %s configuration key.', '<code>conversations.files.disallowed_types</code>') ?>
                </div>
            <?php } ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Notification') ?>
    </legend>

    <div class="form-group">
        <div class="form-check">
            <?php echo $form->checkbox('notificationOverridesEnabled', 1, $notificationOverridesEnabled ?? false) ?>
            <?php echo $form->label('notificationOverridesEnabled', t('Override Global Settings'), ['class' => 'form-check-label']) ?>
        </div>
    </div>

    <div class="form-group notification-overrides">
        <?php echo $form->label('notificationUsers', t('Users To Receive Conversation Notifications')); ?>
        <?php echo $userSelector->selectMultipleUsers('notificationUsers', $notificationUsers) ?>
    </div>

    <div class="form-group notification-overrides">
        <?php echo $form->label('', t('Subscribe Option')); ?>

        <div class="form-check">
            <?php echo $form->checkbox('subscriptionEnabled', 1, $subscriptionEnabled) ?>
            <?php echo $form->label('subscriptionEnabled', t('Yes, allow registered users to choose to subscribe to conversations.'), ['class' => 'form-check-label']) ?>
        </div>
    </div>
</fieldset>

<!--suppress ES6ConvertVarToLetConst, EqualityComparisonWithCoercionJS -->
<script>
    $(function () {
        $('[data-unhide]').each(function () {
            var me = $(this),
                selector = me.data('unhide'),
                watch = $(selector);

            watch.change(function () {
                if (watch.is(':checked')) {
                    me.show();
                } else {
                    me.hide();
                }
            }).trigger('change');
        });

        $('input[name=paginate]').on('change', function () {
            var pg = $('input[name=paginate]:checked');
            if (pg.val() == 1) {
                $('div[data-row=itemsPerPage]').show();
            } else {
                $('div[data-row=itemsPerPage]').hide();
            }
        }).trigger('change');

        $('input[name=dateFormat]').on('change', function () {
            var dateFormat = $('input[name=dateFormat]:checked');
            if (dateFormat.val() == 'custom') {
                $('div[data-row=customDateFormat]').show();
            } else {
                $('div[data-row=customDateFormat]').hide();
            }
        }).trigger('change');

        $('input[name=attachmentOverridesEnabled]').on('change', function () {
            var ao = $('input[name=attachmentOverridesEnabled]:checked');
            if (ao.val() == 1) {
                $('.attachment-overrides input, .attachment-overrides textarea').prop('disabled', false);
                $('.attachment-overrides label').removeClass('text-muted');
            } else {
                $('.attachment-overrides input, .attachment-overrides textarea').prop('disabled', true);
                $('.attachment-overrides label').addClass('text-muted');
            }
        }).trigger('change');

        $('input[name=notificationOverridesEnabled]').on('change', function () {
            var ao = $('input[name=notificationOverridesEnabled]:checked');
            if (ao.val() == 1) {
                $('.notification-overrides').show();
            } else {
                $('.notification-overrides').hide();
            }
        }).trigger('change');
    });
</script>
