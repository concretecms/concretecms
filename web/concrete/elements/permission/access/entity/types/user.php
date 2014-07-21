<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? $url = $type->getAccessEntityTypeToolsURL(); ?>

<script type="text/javascript">
    (function() {
        var ccm_triggerSelectUser = function(uID, uName) {
            /* retrieve the peID for the selected group from ajax */
            $('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
            $.getJSON('<?=$url?>', {
                'uID': uID
            }, function(r) {
                $.fn.dialog.closeTop();
                $('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
                $('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
                Concrete.event.bind('UserSearchDialogSelectUser', function(event, data) {
                    ccm_triggerSelectUser(data.uID, data.uName);
                });
            });
        }
    }());
</script>
