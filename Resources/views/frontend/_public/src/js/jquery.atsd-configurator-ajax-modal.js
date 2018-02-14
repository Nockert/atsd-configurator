
;(function ($) {

    // use strict mode
    "use strict";



    // ...
    $.atsdConfiguratorAjaxModal = {

        // our loading indicator
        loadingIndicator: '<div class="atsd-configurator--info-modal--loading-indicator"><i class="emotion--loading-indicator"></i></div>',



        // ...
        open: function( title, url, showData )
        {
            // get this
            var me = this,
                options = {},
                data = {};

            // overwrite defaults
            options.mode = "content";
            options.title = title;
            options.additionalClass = "atsd-configurator--info-modal";

            //check if quickview is selected
            if( typeof showData !== "undefined" && showData.quickview == true )
            {
                //prepare data for view
                data = { quickview: showData.quickview, showDescription: showData.showDescription, showAttributes: showData.showAttributes, isXHR: 1};

                //should description and/or attributes be shown
                if(showData.showDescription == true || showData.showAttributes == true)
                {
                    //adapt modal width
                    var modalWidth = 1200
                }
                else
                {
                    //adapt modal width
                    modalWidth = 600;
                }

            }
            else
            {
                //prepare data
                data = { isXHR: 1};

                //adapt width
                modalWidth = 600
            }

            // open modal with loading indicator
            $.modal.open(
                me.loadingIndicator,
                {
                    width: modalWidth,
                    height: 600
                },
                options
            );

            // load the url via ajax
            $.ajax(
                url,
                {
                    data: data,
                    success: function ( response )
                    {
                        $.modal.setContent( response );

                        // call quickview plugin
                        StateManager.addPlugin( '*[data-atsd-configurator-quickview="true"]', "atsdConfiguratorQuickview" );

                    }
                }
            );
        }



    };

})(jQuery);



