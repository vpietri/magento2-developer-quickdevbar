
/* */
require(["jquery",
         /*"jquery/jquery.ui",*/
         "jquery/jquery.tabs"
], function($){
    'use strict';
    
    $(function() {
        
            var toggleEffect = "blind" /** Vertical */
            var toggleEffect = "slide" /** Horizontal left*/
            var toggleEffect = "drop" /** Horizontal left and disappear*/
                
            $('#qdb-bar').toggle(toggleEffect);

            $('#qdb-bar-anchor').click(function(event) {
                event.preventDefault();
                $('#qdb-bar').toggle(toggleEffect);
            });
           
    });
});