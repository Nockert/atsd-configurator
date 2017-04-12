
;(function ($) {

    // use strict mode
    "use strict";



    // ...
    $.atsdConfiguratorAjaxModal = {

        // our loading indicator
        loadingIndicator: '<div class="atsd-configurator--info-modal--loading-indicator"><i class="emotion--loading-indicator"></i></div>',



        // ...
        open: function( title, url, options )
        {
            // get this
            var me = this;

            // force options to be an object
            if ( typeof options == "undefined" ) options = {};

            // overwrite defaults
            options.mode = "content";
            options.title = title;
            options.additionalClass = "atsd-configurator--info-modal";

            // open modal with loading indicator
            $.modal.open(
                me.loadingIndicator,
                options
            );

            // load the url via ajax
            $.ajax(
                url,
                {
                    data: { isXHR: 1 },
                    success: function ( response ) { $.modal.setContent( response ); }
                }
            );
        }



    };

})(jQuery);



