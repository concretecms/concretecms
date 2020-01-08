var ConcretePageIndexer = {

    reindexPendingPages: function () {
        $.get(CCM_TOOLS_PATH + '/reindex_pending_pages?ccm_token=' + CCM_SECURITY_TOKEN);
    }

}

global.ConcretePageIndexer = ConcretePageIndexer;
