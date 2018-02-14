;(function($) {

    // quickview plugin
    $.plugin( "atsdConfiguratorQuickview", {


        // on initialization
        init: function ()
        {
            // get this
            var me = this;

            // bind all events
            me.bindEvents();
        },






        // bind all events
        bindEvents: function ()
        {
            // get this
            var me = this;

            // on quickview mousover thumbnail
            me._on( me.$el.find( 'img.atsd-configurator-quickview-thumbnail-image' ), 'click', $.proxy( me.onClickThumbnail, me ) );
        },


        onClickThumbnail: function( event )
        {
            var me = this,
                $targetTumbnail = $(event.target),
                $targetDiv = $targetTumbnail.parent(),
                thumbnailSrc = $targetDiv.data("atsd-quickview-thumbnail"),
                $image = $("img.atsd-configurator--quickview-images-image"),
                imgSrc = $image.attr('srcset'),
                $oldDiv = $("div").find('[data-atsd-quickview-thumbnail="' + imgSrc + '"]');

            //unmark old thumbnail
            $oldDiv.css("border-color", "#000000");
            //mark target thumbnail
            $targetDiv.css("border-color", "#d9400b");

            //show image
            $image.attr( 'srcset', thumbnailSrc );
        },

        // on destroy
        destroy: function()
        {
            // get this
            var me = this;

            // call the parent
            me._destroy();
        }

    });


})(jQuery);
