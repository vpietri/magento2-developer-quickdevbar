
/* */
define(["jquery",
         "magetabs",
         "filtertable",
         "metadata",
         "tablesorter"
], function($){
    
    $.widget('mage.quickDevBar', {
        options: {
            toggleEffect: "drop",
            stripedClassname: "striped",
            classToStrip: "qdn_table.striped",
            classToFilter: "qdn_table.filterable",
            classToSort: "qdn_table.sortable"
        },

        _create: function() {
            /* Manage toggling toolbar */
            this.element.toggle(this.options.toggleEffect);
            $('#qdb-bar-anchor').on('click', $.proxy(function(event) {
                event.preventDefault();
                this.element.toggle(this.options.toggleEffect);
            }, this));

            /* Apply enhancement on table */
            
            /* classToStrip: Set odd even class on tr */
            $('table.' + this.options.classToStrip + ' tr:even').addClass(this.options.stripedClassname);
            
            /* classToFilter: Set filter input */
            $('table.' + this.options.classToFilter).filterTable({
                label: 'Search filter:',
                minRows: 10,
                visibleClass: '', 
                callback: $.proxy(function(term, table) {
                    table.find('tr').removeClass(this.options.stripedClassname).filter(':visible:even').addClass(this.options.stripedClassname);
                }, this)
            });
            
            /* classToSort: Set sort on thead */
            $('table.' + this.options.classToSort).tablesorter(); 
            
            /* Set special class to last element of the tree in layout tab */
            $('ul.tree li:last-child').addClass('last');
            
            /* Manage ajax tabs */
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
        },
        
        /**
         * https://wiki.eclipse.org/Eclipse_Web_Interface
         */
        callJsEclipseSocEWI: function(file, line)
        {
            var url= 'http://localhost:34567/?command=org.eclipse.soc.ewi.examples.commands.openfile&path='+file+'&line='+line;
            try
            {
              var xhr_object = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
              xhr_object.open("post", url, false);
              xhr_object.send();
            }
            catch(e)
            {
              //uncaught exception: Component returned failure code: 0x80004005 (NS_ERROR_FAILURE) [nsIXMLHttpRequest.send]
              //console.log(e);
              if( e.name!='NS_ERROR_FAILURE' && e.result!=2147500037)
                window.location = url;
            }
        }        
    });
});