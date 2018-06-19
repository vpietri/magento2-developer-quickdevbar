
/* */
define(["jquery",
        "jquery/ui",
         "filtertable",
         "metadata",
         "tablesorter",
         'mage/cookies'
], function($){
    
    /**
     * 
     * Events attached
     * 
     * All tabs
     * - quickdevbartabscreate
     * - quickdevbartabsbeforeactivate
     * - quickdevbartabsactivate
     * 
     * Ajax tabs
     * - quickdevbartabsbeforeload
     * - quickdevbartabsload
     * 
     */
    
    $.widget('mage.quickDevBarTabs', $.ui.tabs, {
        _create: function() {
            var qdbOption = this.element.attr('data-qdbtabs-option');
            if (qdbOption) {
                $.extend( this.options, JSON.parse(qdbOption) );
            }
            this._super();
        },
        
        load: function( index, event ) {
            index = this._getIndex( index );
            var that = this,
                tab = this.tabs.eq( index ),
                anchor = tab.find( ".ui-tabs-anchor" ),
                panel = this._getPanelForTab( tab ),
                eventData = {
                    tab: tab,
                    panel: panel
                };
            
            var anchorUrl = $( anchor ).attr( "data-ajax" );
            var rhash = /#.*$/;

            // not remote
            if ( typeof anchorUrl =='undefined' || anchorUrl.length < 1 ||  anchorUrl.replace( rhash, "" ).length<1) {
                return;
            }
            
            this.xhr = $.ajax( this._ajaxSettings( anchorUrl, event, eventData ) );

            // support: jQuery <1.8
            // jQuery <1.8 returns false if the request is canceled in beforeSend,
            // but as of 1.8, $.ajax() always returns a jqXHR object.
            if ( this.xhr && this.xhr.statusText !== "canceled" ) {
                tab.addClass( "ui-tabs-loading" );
                panel.attr( "aria-busy", "true" );

                this.xhr
                    .success(function( response ) {
                        // support: jQuery <1.8
                        // http://bugs.jquery.com/ticket/11778
                        setTimeout(function() {
                            panel.html( response );
                            that._trigger( "load", event, eventData );
                            
                            // Prevent tab to be load several times
                            $( anchor ).removeAttr( "data-ajax" );
                        }, 1 );
                    })
                    .complete(function( jqXHR, status ) {
                        // support: jQuery <1.8
                        // http://bugs.jquery.com/ticket/11778
                        setTimeout(function() {
                            if ( status === "abort" ) {
                                that.panels.stop( false, true );
                            }

                            tab.removeClass( "ui-tabs-loading" );
                            panel.removeAttr( "aria-busy" );

                            if ( jqXHR === that.xhr ) {
                                delete that.xhr;
                            }
                        }, 1 );
                    });
            }
        },
        
        _ajaxSettings: function( anchorUrl, event, eventData ) {
            var that = this;
            return {
                url: anchorUrl,
                beforeSend: function( jqXHR, settings ) {
                    return that._trigger( "beforeLoad", event,
                        $.extend( { jqXHR : jqXHR, ajaxSettings: settings }, eventData ) );
                }
            };
        },
    });
    
    $.widget("mage.treeView", {
        // default options
        options: {
          expandAll: true,
          treeClass: "qdbTree",
        },

        // The constructor
        _create: function() {
          this.element.addClass(this.options.treeClass);

          var self = this;

          
          this.element.find('li').each(function() {
            var li = $(this);
            li.prepend('<div class="node"></div>');
            li.contents().filter(function() {
              return this.nodeName=='UL';
            }).each(function() {
              var liParent = $(this).parent();
              var liNode = liParent.children('div.node')
              if (!liParent.data('ul')) {
                liNode.data('li', liParent);
                liNode.data('ul', liParent.find('ul').first());
                self._toggle(liNode, self.options.expandAll);
              }
            });
          });
          this.element.on('click', "div.node", $.proxy(this._handleNodeClick, this));
        },

        _toggle: function(node, expand) {
          var sub = node.data('ul') ? $(node.data('ul')) : false;
          if (sub) {
              if(typeof expand == 'undefined') {
                  sub.toggle();
              } else if(expand) {
                  sub.show();
              } else {
                  sub.hide();
              }
            var subVisibility = sub.is(":visible");
            node.toggleClass('expanded', subVisibility);
            node.toggleClass('collapsed', !subVisibility);
          }
        },

        _handleNodeClick: function(event) {
          event.stopPropagation();
          var node = $(event.target);
          if(event.target.nodeName=='DIV') {
              this._toggle(node)
              this._trigger("nodePostClick", event);
          }

        },

      });
    
      
    $.widget('mage.quickDevBar', {
        options: {
            css: false,
            appearance: "collapsed",
            toggleEffect: "drop",
            stripedClassname: "striped",
            classToStrip: "qdn_table.striped",
            classToFilter: "qdn_table.filterable",
            classToSort: "qdn_table.sortable"
        },

        _create: function() {
            
            $('<link/>', {
                rel: 'stylesheet',
                type: 'text/css',
                href: this.options.css
            }).appendTo('head');            
            
            
            /* Manage toggling toolbar */
            if(this.getVisibility()) {
                this.element.toggle(this.options.toggleEffect);
            }

            $('#qdb-bar-anchor').on('click', $.proxy(function(event) {
                event.preventDefault();
                this.setVisibility(!this.element.is(":visible"));
                this.element.toggle(this.options.toggleEffect);

            }, this));
            
            /* Apply ui.tabs widget */ 
            $('div.qdb-container').quickDevBarTabs({load:$.proxy(function(event, data){
                if($(data.panel)) {
                    this.applyTabPlugin('#' + $(data.panel).attr( "id" ));
                }
                }, this)}
            );

            this.applyTabPlugin('div.qdb-container');
            
            /* Manage ajax tabs */
            $('div.qdb-container').addClass('qdb-container-collapsed');
        },

        setVisibility: function(visible) {
            $.mage.cookies.set('qdb_visibility', visible);
        },


        getVisibility: function() {
            var visible = false;
            if(this.options.appearance == 'memorize') {
                visible = $.mage.cookies.get('qdb_visibility') === "true";
            } else if(this.options.appearance == 'expanded') {
                visible = true;
            }

            return visible;
        },
        
        applyTabPlugin: function(selector) {
            
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
