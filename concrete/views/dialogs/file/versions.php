<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Validation\CSRF\Token;

/**
 * @var Concrete\Core\Permission\Checker $fp
 * @var Concrete\Core\Entity\File\File $f
 * @var Concrete\Core\Localization\Service\Date $dh
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\File\Version|null $fv
 */

$urlResolver = app(ResolverManagerInterface::class);
$token = app(Token::class);

$canPreviewFileVersion = (bool) $fp->canEditFileProperties();
$canDeleteFileVersion = (bool) $fp->canEditFileContents();

$versions = [];
foreach ($f->getFileVersions() as $version) {
    $versions[] = [
        'id' => (int) $version->getFileVersionID(),
        'filename' => (string) $version->getFilename(),
        'title' => (string) $version->getTitle(),
        'comments' => $version->getVersionLogComments(),
        'author' => (string) $version->getAuthorName(),
        'dateAdded' => $dh->formatDateTime($version->getDateAdded(), true),
        'previewUrl' => $canPreviewFileVersion ? (string) $urlResolver->resolve(['/dashboard/files/details', 'preview_version', $f->getFileID(), $version->getFileVersionID()]) : '',
        'deleteToken' => $canDeleteFileVersion ? $token->generate("version/delete/{$version->getFileID()}/{$version->getFileVersionID()}") : '',
        'isGoingToBeDeleted' => false,
    ];
}
?>
<div class="ccm-ui" id="ccm-file-versions">
    <div v-if="fileVersions.length === 0" class="alert alert-info">
        <?= t('No version found.') ?>
    </div>
    <table v-else class="table">
        <colgroup>
            <col width="1" />
            <col width="150" />
            <col width="150" />
            <col />
            <col />
            <col />
            <col width="1" v-if="canPreviewFileVersion" width="1" />
            <col width="1" v-if="canDeleteFileVersion" width="1" />
        </colgroup>
        <thead>
            <tr>
                <th></th>
                <th><?= t('Filename') ?></th>
                <th><?= t('Title') ?></th>
                <th><?= t('Comments') ?></th>
                <th><?= t('Creator') ?></th>
                <th><?= t('Added On') ?></th>
                <th v-if="canPreviewFileVersion"></th>
                <th v-if="canDeleteFileVersion"></th>
        </thead>
        <tbody>
            <tr v-for="fileVersion in fileVersions" v-bind:key="fileVersion.id" ref="fileVersions" v-bind:class="{'table-success': fileVersion.id === activeFileVersionID}">
                <td>
                    <input type="radio" name="fvID" v-bind:value="fileVersion.id" v-model="activeFileVersionID" v-bind:disabled="busy || fileVersion.isGoingToBeDeleted" />
                </td>
                <td style="word-wrap: break-word">{{ fileVersion.filename }}</td>
                <td style="word-wrap: break-word">{{ fileVersion.title }}</td>
                <td>
                    <span v-if="fileVersion.comments.length !== 0"><?= t('Updated ') ?></span>
                    {{ fileVersion.comments.join(', ') }}
                </td>
                <td>{{ fileVersion.author }}</td>
                <td>{{ fileVersion.dateAdded }}</td>
                <td v-if="canPreviewFileVersion">
                    <i v-if="fileVersion.id === activeFileVersionID" class="me-2 fas fa-search" style="opacity: 0.2"></i>
                	<a v-else class="me-2 ccm-hover-icon" v-bind:href="fileVersion.previewUrl">
                        <i class="fas fa-search"></i>
                    </a>
                </td>
                <td v-if="canDeleteFileVersion">
                    <i v-if="fileVersion.id === activeFileVersionID" class="fas fa-trash-alt" style="opacity: 0.2"></i>
                    <a v-else class="ccm-hover-icon" href="#" v-on:click.prevent="deleteFileVersion(fileVersion)">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script>
$(document).ready(function() {

new Vue({
    el: '#ccm-file-versions',
    data() {
        return <?= json_encode([
            'busy' => false,
            'fileID' => (int) $f->getFileID(),
            'activeFileVersionID' => $fv ? (int) $fv->getFileVersionID() : null,
            'storedActiveFileVersionID' => $fv ? (int) $fv->getFileVersionID() : null,
            'originalActiveFileVersionID' => $fv ? (int) $fv->getFileVersionID() : null,
            'fileVersions' => $versions,
            'canPreviewFileVersion' => $canPreviewFileVersion,
            'canDeleteFileVersion' => $canDeleteFileVersion,
        ]) ?>;
    },
    watch: {
        activeFileVersionID() {
            if (this.activeFileVersionID === this.storedActiveFileVersionID) {
                return;
            }
            this.busy = true;
            $.concreteAjax({
                url: <?= json_encode((string) $urlResolver->resolve(['/ccm/system/file/approve_version'])) ?>,
                data: {
                    fID: this.fileID,
                    fvID: this.activeFileVersionID,
                },
                success: () => {
                    this.busy = false;
                    this.storedActiveFileVersionID = this.activeFileVersionID;
                },
                error: (xhr) => {
                    this.busy = false;
                    this.activeFileVersionID = this.storedActiveFileVersionID;
                    ConcreteAlert.error({
                        message: ConcreteAjaxRequest.renderErrorResponse(xhr, true),
                    });
                },                
            });
        },
    },
    mounted() {
        $(this.$el).closest('.ui-dialog').on('dialogclose', () => {
            if (this.storedActiveFileVersionID !== this.originalActiveFileVersionID) {
                window.location.reload();
            }
        });
    },
    methods: {
        deleteFileVersion: function(fileVersion) {
            if (this.busy || fileVersion.isGoingToBeDeleted) {
                return false;
            }
            fileVersion.isGoingToBeDeleted = true;
            $.concreteAjax({
                url: <?= json_encode((string) $urlResolver->resolve(['/ccm/system/file/delete_version'])) ?>,
                data: {
                    fID: this.fileID,
                    fvID: fileVersion.id,
                    <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: fileVersion.deleteToken,
                },
                success: () => {
                    const $row = $(this.$refs.fileVersions[this.fileVersions.indexOf(fileVersion)]);
                    $row.queue(() => {
                        $row.addClass('animated fadeOutDown').dequeue();
                    }).delay(500).queue(() => {
                        $row.dequeue();
                        this.fileVersions.splice(this.fileVersions.indexOf(fileVersion), 1);
                    });
                },
                error: (xhr) => {
                    fileVersion.isGoingToBeDeleted = false;
                    ConcreteAlert.error({
                        message: ConcreteAjaxRequest.renderErrorResponse(xhr, true),
                    });
                },                
            });
        },
    },
});

});
</script>
