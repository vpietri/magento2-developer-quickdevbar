
/* */
define(["jquery",
         /*"jquery/jquery.ui",*/
         "jquery/jquery.tabs",
         "filtertable"
], function($){
    
    $.widget('mage.quickDevBar', {
        options: {
            toggleEffect: "drop",
            stripedClassname: "striped",
            classToStrip: "qdn_table.striped",
            classToFilter: "qdn_table.filterable"
            // toggleEffect = "blind" /** Vertical */
            // toggleEffect = "slide" /** Horizontal left*/
            // toggleEffect = "drop" /** Horizontal left and disappear*/
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
            $('.ui-tabs-nav li.use-ajax').on( "beforeOpen", function( event) {
                if (!loadedTab[event.target]) {
                    loadedTab[event.target] = true;
                    console.log(event.target, 'loaded');
                }
            });
            
        }
    });
});