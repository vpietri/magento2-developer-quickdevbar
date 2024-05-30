
/* */
define(["jquery",
        "mage/url",
        "jquery/ui-modules/widgets/tabs",
         "filtertable",
         "metadata",
         "tablesorter",
         'mage/cookies'
], function($,url){

    url.setBaseUrl(window.BASE_URL);
    //let link = url.build('foo/bar')


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
            // this.options.active=true;
            this.options.active=false;
            this.options.collapsible=true;
            this.options.activate=this.activate;
            this._super();
        },
        activate: function( event, eventData ) {
            let toShow = eventData.newPanel;
            //Look for sub tab widget, to activate first tab
            if ( toShow.length ) {
                let firstSubTabs = toShow.find('div.qdb-container');
                if(firstSubTabs.length && firstSubTabs.quickDevBarTabs('option', 'active') === false) {
                    firstSubTabs.quickDevBarTabs('option', 'active', 0);
                }
            }
        },
        load: function( index, event ) {
            index = this._getIndex( index );
            let that = this,
                tab = this.tabs.eq( index ),
                anchor = tab.find( ".ui-tabs-anchor" ),
                panel = this._getPanelForTab( tab ),
                eventData = {
                    tab: tab,
                    panel: panel
                };

            let anchorUrl = $( anchor ).attr( "data-ajax" );
            let rhash = /#.*$/;

            // If not an explicit click on tab
            // If not an anchorUrl with http
            if ( typeof anchorUrl =='undefined'
                || typeof event =='undefined'
                || anchorUrl.length < 1
                ||  anchorUrl.replace( rhash, "" ).length<1
            ) {
                return;
            }

            this.xhr = $.ajax( this._ajaxSettings( anchorUrl, event, eventData ) );
            // support: jQuery <1.8
            // jQuery <1.8 returns false if the request is canceled in beforeSend,
            // but as of 1.8, $.ajax() always returns a jqXHR object.
            if (this.xhr && this.xhr.statusText !== "canceled" ) {
                this._addClass( tab, "ui-tabs-loading" );
                panel.attr( "aria-busy", "true" );

                this.xhr
                    .done( function( response, status, jqXHR ) {
                        // support: jQuery <1.8
                        // http://bugs.jquery.com/ticket/11778
                        setTimeout(function() {
                            panel.html( response );
                            that._trigger( "load", event, eventData );

                            // Prevent tab to be load several times
                            $( anchor ).removeAttr( "data-ajax" );
                        }, 1 );
                    })
                    .always( function( jqXHR, status ) {
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
        /* */
        _ajaxSettings: function( anchorUrl, event, eventData ) {
            let that = this;
            return {
                url: anchorUrl,
                global: false,
                beforeSend: function( jqXHR, settings ) {
                    return that._trigger( "beforeLoad", event,
                        $.extend( { jqXHR : jqXHR, ajaxSettings: settings }, eventData ) );
                }
            };
        },
    });

    // $.widget("mage.treeView", {
    //     // default options
    //     options: {
    //       expandAll: true,
    //       treeClass: "qdbTree",
    //     },
    //
    //     // The constructor
    //     _create: function() {
    //       this.element.addClass(this.options.treeClass);
    //
    //       let self = this;
    //
    //
    //       this.element.find('li').each(function() {
    //         let li = $(this);
    //         li.prepend('<div class="node"></div>');
    //         li.contents().filter(function() {
    //           return this.nodeName=='UL';
    //         }).each(function() {
    //           let liParent = $(this).parent();
    //           let liNode = liParent.children('div.node')
    //           if (!liParent.data('ul')) {
    //             liNode.data('li', liParent);
    //             liNode.data('ul', liParent.find('ul').first());
    //             self._toggle(liNode, self.options.expandAll);
    //           }
    //         });
    //       });
    //       this.element.on('click', "div.node", $.proxy(this._handleNodeClick, this));
    //     },
    //
    //     _toggle: function(node, expand) {
    //       let sub = node.data('ul') ? $(node.data('ul')) : false;
    //       if (sub) {
    //           if(typeof expand == 'undefined') {
    //               sub.toggle();
    //           } else if(expand) {
    //               sub.show();
    //           } else {
    //               sub.hide();
    //           }
    //         let subVisibility = sub.is(":visible");
    //         node.toggleClass('expanded', subVisibility);
    //         node.toggleClass('collapsed', !subVisibility);
    //       }
    //     },
    //
    //     _handleNodeClick: function(event) {
    //       event.stopPropagation();
    //       let node = $(event.target);
    //       if(event.target.nodeName=='DIV') {
    //           this._toggle(node)
    //           this._trigger("nodePostClick", event);
    //       }
    //
    //     },
    //
    //   });


    $.widget('mage.quickDevBar', {
        options: {
            css: false,
            appearance: "collapsed",
            toggleEffect: "drop",
            stripedClassname: "striped",
            classToStrip: "qdb_table.striped",
            classToFilter: "qdb_table.filterable",
            classToSort: "qdb_table.sortable",
            qdpContentUrl: url.build('quickdevbar/index/ajax'),
            ajaxLoading: false
        },

        _create: function() {
            if(this.options.ajaxLoading){
                let that = this;
                $.ajax({
                        url: that.options.qdpContentUrl,
                        global: false,
                        success: function (data, textStatus, xhr) {
                            if(xhr.status===200) {
                                $('#qdb-bar').html(data).trigger('contentUpdated');
                                that._initQdb();
                            } else {
                                //console.error(xhr.status, 'QDB Error');
                                console.error(data, 'QDB Error');
                            }
                        }
                    }
                );
            } else {
                this._initQdb();
            }
        },


        _initQdb: function() {
            $('<link/>', {
                rel: 'stylesheet',
                type: 'text/css',
                href: this.options.css
            }).appendTo('head');
            /* Manage toggling toolbar */
            if(this.getVisibility()) {
                this.element.toggle(this.options.toggleEffect);
            }

            let qdbTheme = $.mage.cookies.get('qdb_theme');
            if(qdbTheme) {
                this.element.attr('data-theme', qdbTheme);
            }

            $('#qdb-bar-anchor').show().on('click', $.proxy(function(event) {
                event.preventDefault();
                this.setVisibility(!this.element.is(":visible"));
                this.element.toggle(this.options.toggleEffect);

            }, this));

            /* Apply ui.tabs widget */
            //$('div.qdb-container').quickDevBarTabs();

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
            options = {
                secure: window.cookiesConfig ? window.cookiesConfig.secure : false
            };

            //TODO: Fix cookie mix domain admin/front
            $.mage.cookies.set('qdb_visibility', visible ? 'true' : 'false', options);
        },

        getVisibility: function() {
            let visible = false;
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

            this.addIdeClickEvent(selector);

        },

        addIdeClickEvent: function(selector) {
            /* Add hyperlink on file path */
            $(selector + ' span[data-ide-file]:not([data-ide-file=""])').each(function() {
                let span = $(this);
                $(this).off('click').on('click', function (event) {
                    let ideFile = $(event.target).attr('data-ide-file');
                    $.get({
                        url: ideFile,
                        global: false,
                        fail: function (data, textStatus, xhr) {
                            console.error(data, 'QDB Error');
                        },
                    });
                });
            });
        },

    });
});
