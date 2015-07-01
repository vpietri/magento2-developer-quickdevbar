
/* */
define(["jquery",
         "jquery/jquery.tabs",
         "filtertable"
], function($){
    
    $.widget('mage.quickDevBar', {
        options: {
            toggleEffect: "drop",
            stripedClassname: "striped",
            classToStrip: "qdn_table.striped",
            classToFilter: "qdn_table.filterable"
        },

        _create: function() {
            this.element.toggle(this.options.toggleEffect);

            $('#qdb-bar-anchor').on('click', $.proxy(function(event) {
                event.preventDefault();
                this.element.toggle(this.options.toggleEffect);
            }, this));
            
            $('table.' + this.options.classToStrip + ' tr:even').addClass(this.options.stripedClassname);
            $('table.' + this.options.classToFilter).filterTable({
                label: 'Search filter:',
                minRows: 10,
                visibleClass: '', 
                callback: $.proxy(function(term, table) {
                    table.find('tr').removeClass(this.options.stripedClassname).filter(':visible:even').addClass(this.options.stripedClassname);
                }, this)
            });
            
            
            var loadedTab = {};
            $('.qdb-ui-tabs > .ui-tabs-nav > li.use-ajax').on( "beforeOpen", function( event) {
                console.log(event.target, 'beforeOpen');
                if (!loadedTab[event.target]) {
                    loadedTab[event.target] = true;
                }
            });
            
            $('div.qdb-container').addClass('qdb-container-collapsed');
            $('.qdb-ui-tabs > .ui-tabs-nav > li').on( "dimensionsChanged", function( event) {
                if ($('div.qdb-container')) {
                    if( $(event.target).hasClass('ui-tabs-active')) {
                        $('div.qdb-container').removeClass('qdb-container-collapsed');
                    } else {
                        $('div.qdb-container').addClass('qdb-container-collapsed');
                    }
                }
            });
        }
    });
});