var config = {
    paths: {
        quickDevBar:  'ADM_QuickDevBar/js/quickdevbar',
        filtertable:  'ADM_QuickDevBar/js/sunnywalker/jquery.filtertable.min',
        metadata:  'ADM_QuickDevBar/js/tablesorter/jquery.metadata',
        tablesorter:  'ADM_QuickDevBar/js/tablesorter/jquery.tablesorter.min',
        jqueryTabs: ['jquery-ui-modules/tabs', 'jquery/jquery-ui']
    },
    shim: {
        'quickDevBar': {
            deps: ['jquery']
        },
        'filtertable': {
            deps: ['jquery']
        },
        'metadata': {
            deps: ['jquery']
        },
        'tablesorter': {
            deps: ['jquery']
        }
    }
};
