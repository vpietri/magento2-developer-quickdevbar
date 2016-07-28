
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

            this.applyTabPlugin('div.qdb-container');
            
            /* Manage ajax tabs */
            $('div.qdb-container').addClass('qdb-container-collapsed');
            
            /* For ajax tabs */
            this.pluginAppliedFor = {};
            $('.qdb-ui-tabs .ui-tabs-nav li.use-ajax').on( "dimensionsChanged", $.proxy(function( event) {
                var tab = $(event.target);
                var tabId = event.target.id;
                
                /* Use MutationObserver to applyPlugin */
                if(typeof this.pluginAppliedFor[tabId] == 'undefined') {
                    MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
                    this.observer = new MutationObserver( $.proxy(function(mutations) {
                        //console.log(this.pluginAppliedFor, 'new observer: ' + tabId);
                        mutations.forEach( $.proxy(function(mutation) {
                            // fired when a mutation occurs
                            //console.log(mutation, typeof this.pluginAppliedFor[tabId]);
                            if(mutation.type=='attributes') {
                                this.pluginAppliedFor[tabId] = true;
                                this.applyTabPlugin('#'+mutation.target.id, true);
                            }
                        }, this));
                    }, this));
                    
                    // pass in the target node, as well as the observer options
                    this.observer.observe($('#panel-' + tabId)[0], { attributes: true});
                }
                
                /* Prevent multiple ajax calls */
                if(tab.find("[data-ajax=true]").attr("href") && tab.hasClass('ui-tabs-active')) {
                    tab.find("[data-ajax=true]").removeAttr("href");
                }
            }, this));
        },
        
        applyTabPlugin: function(selector, observer) {
            
            if(observer && this.observer) {
                // Stop observing
                this.observer.disconnect();      
            }
            
            /* Apply enhancement on table */
            
            /* classToStrip: Set odd even class on tr */
            $(selector + ' table.' + this.options.classToStrip + ' tr:even').addClass(this.options.stripedClassname);
            
            /* classToFilter: Set filter input */
            $(selector + ' table.' + this.options.classToFilter).filterTable({
                label: 'Search filter:',
                minRows: 10,
                visibleClass: '', 
                callback: $.proxy(function(term, table) {
                    table.find('tr').removeClass(this.options.stripedClassname).filter(':visible:even').addClass(this.options.stripedClassname);
                }, this)
            });
            
            /* classToSort: Set sort on thead */
            $(selector + ' table.' + this.options.classToSort).tablesorter(); 
            
            /* Set special class to last element of the tree in layout tab */
            $(selector + ' ul.tree li:last-child').addClass('last');
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