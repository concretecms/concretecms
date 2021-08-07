var ConcretePageIndexer = {

    reindexPendingPages: function () {
        $.get(CCM_DISPATCHER_FILENAME + '/ccm/page/reindex_pending?ccm_token=' + CCM_SECURITY_TOKEN)
    }

}

global.ConcretePageIndexer = ConcretePageIndexer
