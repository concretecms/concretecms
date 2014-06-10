var tags ={
    init:function(){
        this.showHideDisplayType();

        $('input[name="displayMode"]').change(function() {
            tags.showHideDisplayType();
        });

    },

    showHideDisplayType:function() {
        if($('#displayMode1').prop('checked')) {
            $('#ccm-tags-display-cloud').hide();
            $('#ccm-tags-display-page').show();
        } else {
            $('#ccm-tags-display-page').hide();
            $('#ccm-tags-display-cloud').show();
        }
    }
}