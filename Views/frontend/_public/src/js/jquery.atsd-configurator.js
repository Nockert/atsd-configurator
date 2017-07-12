;(function($) {

    // our plugin
    $.plugin( "atsdConfigurator", {

        // some default configuration values
        configuration:
            {
                // price defaults - will be set after parsing the default price
                priceFormat:
                    {
                        template:     null,
                        pre:          "",
                        post:         "",
                        thousandsSep: "",
                        decPoint:     ""
                    },

                // buybox price template
                priceTemplates:
                    {
                        default: null,
                        pseudo:  null
                    },

                // different selectors
                priceSelectors:
                    {
                        container: ".product--buybox .product--price, .atsd-configurator--actions--price .product--price",
                        discount:  "price--discount"
                    },

                // stock/delivery selector
                deliverySelector: ".product--buybox .product--delivery",

                // weight stuff
                weight:
                    {
                        selector: ".product--buybox .entry--content--weight",
                        unit:     null
                    },

                // fieldset summary stuff
                fieldsetSummary:
                    {
                        separator: "<br />"
                    },

                // article data
                article:
                    {
                        stockAvailable:    null,
                        stockNotAvailable: null
                    },

                // selector button templates
                selectorButton:
                    {
                        selected:   null,
                        selectable: null
                    },

                // show loading indicator on switching
                showLoadingIndicator: false,

                // info panel options
                infoPanel:
                    {
                        loadingIndicator: '<div class="atsd-configurator--info-panel--loading-indicator"><i class="emotion--loading-indicator"></i></div>',
                        ajaxUrl:          null
                    },

                // info modal options
                infoModal:
                    {
                        title:   "",
                        timeout: 600,
                        ajaxUrl: null
                    },

                // delivery status templates
                deliveryStatus:
                    {
                        available:    '<i class="delivery--status-icon delivery--status-available"></i>',
                        notAvailable: '<i class="delivery--status-icon delivery--status-not-available"></i>'
                    }
            },



        // on initialization
        init: function ()
        {
            // get this
            var me = this;

            // read from configuration
            me.readConfiguration();

            // parse the price template
            me.parsePriceTemplate();

            // set up article prices
            me.parseArticlePrices();

            // set up delivery status
            me.parseArticleDeliveryStatus();

            // startup the default selection
            me.parseCurrentSelection();

            // init the product slider and its configurations
            me.initProductSlider();

            // bind all events
            me.bindEvents();
        },



        // ...
        readConfiguration: function()
        {
            // get this
            var me = this;

            // get configuration array
            var configuration = atsdConfiguratorConfiguration;

            // get global configuration
            me.configuration.priceFormat.template      = configuration.priceTemplate;
            me.configuration.priceTemplates.default    = configuration.priceDefault;
            me.configuration.priceTemplates.pseudo     = configuration.pricePseudo;
            me.configuration.article.stockAvailable    = configuration.stockAvailable;
            me.configuration.article.stockNotAvailable = configuration.stockNotAvailable;
            me.configuration.selectorButton.selected   = configuration.selectorButtonSelected;
            me.configuration.selectorButton.selectable = configuration.selectorButtonSelectable;
            me.configuration.weight.unit               = configuration.weightUnit;
            me.configuration.infoPanel.ajaxUrl         = configuration.articleInfoAjaxUrl;
            me.configuration.infoModal.title           = configuration.infoModalTitle;
            me.configuration.infoModal.ajaxUrl         = configuration.articleInfoAjaxUrl;
        },



        // bind all events
        bindEvents: function ()
        {
            // get this
            var me = this;

            // on change
            me._on( me.$el.find( 'input[data-atsd-configurator-selector="true"]' ), 'change', $.proxy( me.onSelectionChange, me ) );

            // make the buttons clickable
            me._on( me.$el.find( 'button[data-atsd-configurator-selector-button="true"]' ), 'click', $.proxy( me.onSelectorButtonClick, me ) );

            // make the complete row for lists clickable
            me._on( me.$el.find( 'div[data-atsd-configurator-article-row="true"]' ), 'click', $.proxy( me.onArticleRowClick, me ) );

            // show a loading indicator on any form submit
            me._on( me.$el.find( 'form button' ), 'click', $.proxy( me.onFormButtonClick, me ) );

            // info panel
            me._on( me.$el.find( 'div[data-atsd-configurator-selector-info="true"]' ), 'click', $.proxy( me.onSelectorInfoClick, me ) );

            // slider article info button
            me._on( me.$el.find( '*[data-atsd-configurator-selector-info-button="true"]' ), 'click', $.proxy( me.onSelectorInfoButtonClick, me ) );

            // empty choice click for lists
            me._on( me.$el.find( 'div.atsd-configurator--article--list input[data-atsd-configurator-empty-choice-selector="true"]' ), 'click', $.proxy( me.onListEmptyChoiceClick, me ) );
            me._on( me.$el.find( 'div.atsd-configurator--article--list div.article--column--name[data-atsd-configurator-empty-choice-row="true"]' ), 'click', $.proxy( me.onListEmptyChoiceRowClick, me ) );

            // empty choice click for slider
            me._on( me.$el.find( 'div.product-slider--item[data-atsd-configurator-empty-choice="true"] button[data-atsd-configurator-empty-choice-selector-button="true"]' ), 'click', $.proxy( me.onSliderEmptyChoiceClick, me ) );

            // on quantity select change
            me._on( me.$el.find( 'select[data-atsd-configurator-article-quantity-selector="true"]' ), 'change', $.proxy( me.onArticleQuantityChange, me ) );
        },




        // ...
        onArticleQuantityChange: function ( event )
        {
            // get this
            var me = this;

            // parse everything again
            me.parseArticlePrices();
            me.parseArticleDeliveryStatus();
            me.parseCurrentSelection();
        },



        // ...
        onSliderEmptyChoiceClick: function ( event )
        {
            // get this
            var me = this;

            // get the button
            var button = $( event.currentTarget );

            // get the element id
            var elementId = button.attr( "data-atsd-configurator-empty-choice-selector-button-element-id" );

            // remove is--primary
            me.$el.find( 'button[data-atsd-configurator-selector-button="true"][data-atsd-configurator-selector-button-element-id="' + elementId + '"]' ).removeClass( "is--primary" );

            // uncheck every radio
            me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-element-id="' + elementId + '"]' ).prop( "checked", false );

            // parse the complete selection
            me.parseCurrentSelection();
        },



        // ...
        onArticleRowClick: function ( event )
        {
            // get this
            var me = this;

            // get the div
            var div = $( event.currentTarget );

            // get the article id
            var articleId = div.attr( "data-atsd-configurator-article-row-article-id" );

            // click the input field
            me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-article-id="' + articleId + '"]' ).click();
        },



        // ...
        onListEmptyChoiceRowClick: function ( event )
        {
            // get this
            var me = this;

            // get the div
            var div = $( event.currentTarget );

            // get the element id
            var elementId = div.attr( "data-atsd-configurator-empty-choice-row-element-id" );

            // deselect the radio button
            me.$el.find( 'input[data-atsd-configurator-empty-choice-selector="true"][data-atsd-configurator-empty-choice-selector-element-id="' + elementId + '"]' ).prop( "checked", false );

            // deselect everything
            me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-element-id="' + elementId + '"]' ).prop( "checked", false );

            // parse the complete selection
            me.parseCurrentSelection();
        },



        // ...
        onListEmptyChoiceClick: function ( event )
        {
            // get this
            var me = this;

            // get the div
            var radio = $( event.currentTarget );

            // uncheck it
            radio.prop( "checked", false );

            // get the element id
            var elementId = radio.attr( "data-atsd-configurator-empty-choice-selector-element-id" );

            // deselect everything
            me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-element-id="' + elementId + '"]' ).prop( "checked", false );

            // parse the complete selection
            me.parseCurrentSelection();
        },



        // ...
        onSelectorInfoClick: function ( event )
        {
            // get this
            var me = this;

            // get the div
            var div = $( event.currentTarget );

            // get the article id
            var articleId = div.attr( "data-atsd-configurator-selector-info-article-id" );

            // get the panel
            var panel = me.$el.find( 'div[data-atsd-configurator-selector-info-panel="true"][data-atsd-configurator-selector-info-panel-article-id="' + articleId + '"]' ).first();

            // toggle visibility
            panel.toggle( 500 );

            // did we not load it yet?!
            if ( panel.attr( "data-atsd-configurator-selector-info-panel-loaded" ) == "false" )
            {
                // set loading div
                panel.html( me.configuration.infoPanel.loadingIndicator );

                // make the ajax call to load the details
                $.ajax(
                    {
                        url:  me.configuration.infoModal.ajaxUrl.replace( "__articleId__", articleId ),
                        type: 'GET'
                    }
                ).done( function( response )
                    {
                        // set the panel
                        panel.html( response );
                    }
                );

                // mark this article as loaded
                panel.attr( "data-atsd-configurator-selector-info-panel-loaded", "true" );
            }
        },



        // ...
        onFormButtonClick: function ( event )
        {
            // open the loading indicator
            $.loadingIndicator.open( { 'closeOnClick': false } );
        },



        // ...
        onSelectorButtonClick: function ( event )
        {
            // get this
            var me = this;

            // get the button
            var button = $( event.currentTarget );

            // get parameters
            var articleId = button.attr( "data-atsd-configurator-selector-button-article-id" );
            var elementId = button.attr( "data-atsd-configurator-selector-button-element-id" );

            // is the button multiple
            var multiple = ( button.attr( "data-atsd-configurator-selector-button-is-multiple" ) == "true" );



            // is it not multiple and already checken?!
            if ( ( multiple == false ) && ( button.hasClass( "is--primary" ) ) )
                // we cant click it again
                return;



            // deactivate every other button if this is not multiple
            if ( multiple == false )
            {
                // deactivate every button for this element
                me.$el.find( 'button[data-atsd-configurator-selector-button="true"][data-atsd-configurator-selector-button-element-id="' + elementId + '"]' ).removeClass( "is--primary" );
                me.$el.find( 'button[data-atsd-configurator-selector-button="true"][data-atsd-configurator-selector-button-element-id="' + elementId + '"]' ).html( me.configuration.selectorButton.selectable );
            }



            // toggle the primary class
            button.toggleClass( "is--primary" );

            // set the new text
            button.html(
                button.hasClass( "is--primary" )
                    ? me.configuration.selectorButton.selected
                    : me.configuration.selectorButton.selectable
            );



            // now get the radio/checkbox
            var input = me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-article-id="' + articleId + '"]').first();

            // click it
            input.click();
        },






        // ...
        onSelectorInfoButtonClick: function ( event )
        {
            // get this
            var me = this;

            // get the button
            var button = $( event.currentTarget );

            // get parameters
            var articleId = button.attr( "data-atsd-configurator-selector-info-button-article-id" );

            // open our modal
            $.atsdConfiguratorAjaxModal.open(
                me.configuration.infoModal.title,
                me.configuration.infoModal.ajaxUrl.replace( "__articleId__", articleId )
            );
        },




        // ...
        onSelectionChange: function ( event )
        {
            // parse the complete selection
            this.parseCurrentSelection();
        },



        // init the product slider and its configurations
        initProductSlider: function ()
        {
            // add plugin
            StateManager.addPlugin( '*[data-atsd-configurator-product-slider="true"]', "atsdConfiguratorProductSlider" );
        },



        // parse the current selection and set everything like prices, images, summaries...
        parseCurrentSelection: function()
        {
            // get this
            var me = this;

            // do we want to show the loading indicator?
            if ( me.configuration.showLoadingIndicator == true )
                // start loading indicator
                $.loadingIndicator.open( { 'closeOnClick': false, 'animationSpeed': 1 } );

            // parse everything
            me.parseCurrentSelectionPrice();
            me.parseCurrentSelectionImages();
            me.parseCurrentSelectionSummaries();
            me.parseCurrentSelectionHiddenSelection();
            me.parseCurrentSelectionStock();
            me.parseCurrentSelectionWeight();

            // do we want to show the loading indicator?
            if ( me.configuration.showLoadingIndicator == true )
                // disable loading indicator
                $.loadingIndicator.close();
        },



        // ...
        parseCurrentSelectionWeight: function()
        {
            // get this
            var me = this;

            // current weight
            var weight = 0.0;

            // add main article
            weight += parseFloat( me.$el.attr( "data-atsd-configurator-main-article-weight" ) );

            // get every selected input
            me.$el.find( 'input[data-atsd-configurator-selector="true"]:checked' ).each ( function()
                {
                    // get the article
                    var article = me.getArticleById( $( this ).attr( "data-atsd-configurator-selector-article-id" ) );

                    // add weight
                    weight += parseFloat( article.attr( "data-atsd-configurator-article-weight" ) ) * me.getArticleQuantityByArticle( article );
                }
            );

            // set the weight info
            $( me.configuration.weight.selector ).html(
                me.formatNumber( weight, 4 ) + " " + me.configuration.weight.unit
            );
        },



        // ...
        parseCurrentSelectionStock: function()
        {
            // get this
            var me = this;

            // current max stock
            var stock = 999;

            // get every selected input
            me.$el.find( 'input[data-atsd-configurator-selector="true"]:checked' ).each ( function()
                {
                    // get the article
                    var article = me.getArticleById( $( this ).attr( "data-atsd-configurator-selector-article-id" ) );

                    // calculate article max stock
                    var articleStock = Math.floor( parseInt( article.attr( "data-atsd-configurator-article-stock" ) ) / me.getArticleQuantityByArticle( article ) );

                    // set minimum stock
                    stock = ( articleStock < stock ) ? articleStock : stock;
                }
            );

            // get the template by stock
            var template = ( stock > 0 )
                ? me.configuration.article.stockAvailable
                : me.configuration.article.stockNotAvailable;

            // set the stock info
            $( me.configuration.deliverySelector ).html( template );
        },



        // save the selection into the hidden input
        parseCurrentSelectionHiddenSelection: function()
        {
            // get this
            var me = this;

            // selected article ids
            var selected = [];

            // get every selected input
            me.$el.find( 'input[data-atsd-configurator-selector="true"]:checked' ).each ( function()
                {
                    // get the article id
                    var articleId = $( this ).attr( "data-atsd-configurator-selector-article-id" );

                    // get quantity
                    var quantity = me.getArticleQuantityById( articleId );

                    // add the article id
                    selected.push( articleId + ":" + quantity );
                }
            );

            // set every hidden input
            $( 'input[data-atsd-configurator-hidden-selection="true"]' ).val( selected.join( "," ) );
        },



        // ...
        parseCurrentSelectionImages: function()
        {
            // get this
            var me = this;

            // loop every element
            me.$el.find( 'div[data-atsd-configurator-element-image="true"][data-atsd-configurator-element-switch-image-on-selection="true"]' ).each ( function()
                {
                    // the element
                    var element = $( this );

                    // get the element id
                    var elementId = element.attr( "data-atsd-configurator-element-id" );

                    // default image
                    var image = element.attr( "data-atsd-configurator-element-default-image" );

                    // loop the selected articles for this element
                    me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-element-id="' + elementId + '"]:checked' ).each ( function()
                        {
                            // get the article
                            var article = me.getArticleById( $( this ).attr( "data-atsd-configurator-selector-article-id" ) );

                            // do we have an image?
                            if ( article.attr( "data-atsd-configurator-article-image" ) != "" )
                                // set the image
                                image = article.attr( "data-atsd-configurator-article-image" );
                        }
                    );

                    // set the img
                    element.find( "img" ).attr( "src", image );
                }
            );
        },



        // ...
        parseCurrentSelectionSummaries: function()
        {
            // get this
            var me = this;

            // loop every summary
            me.$el.find( 'div[data-atsd-configurator-fieldset-summary="true"]' ).each ( function()
                {
                    // save the fieldset
                    var fieldset = $( this );

                    // get data
                    var fieldsetId = fieldset.attr( "data-atsd-configurator-fieldset-id" );
                    var elementId  = fieldset.attr( "data-atsd-configurator-element-id" );

                    // articles here
                    var articles = [];

                    // loop every selected article for this element
                    me.$el.find( 'input[data-atsd-configurator-selector="true"][data-atsd-configurator-selector-element-id="' + elementId + '"]:checked' ).each ( function()
                        {
                            // get the article
                            var article = me.getArticleById( $( this ).attr( "data-atsd-configurator-selector-article-id" ) );

                            // add the name and quantity
                            articles.push(
                                ( me.getArticleQuantityByArticle( article ) > 1 )
                                    ? '<span class="article--quantity">(' + me.getArticleQuantityByArticle( article ).toString() + "x)</span> " + article.attr( "data-atsd-configurator-article-name" )
                                    : article.attr( "data-atsd-configurator-article-name" )
                            );
                        }
                    );

                    // article string or empty
                    var html = ( articles.length > 0 )
                        ? articles.join( me.configuration.fieldsetSummary.separator )
                        : "<span style='color: gray;'>---</span>";

                    // set the summary
                    fieldset.html( html );
                }
            );
        },





        // ...
        getArticlePriceForQuantity: function( json, quantity )
        {
            // create object from it
            var prices = JSON.parse( json );

            // loop them
            for ( var i in prices )
            {
                // get current price
                var price = prices[i];

                // the correct one? or the last one?
                if ( price.to == 0 || ( quantity >= price.from && quantity <= price.to ) )
                    // return it
                    return price.price;
            }

            // nothing found?! return first
            return prices[0].price;
        },




        // ...
        parseCurrentSelectionPrice: function()
        {
            // get this
            var me = this;

            // get config details
            var mainPrice = parseFloat( me.$el.attr( "data-atsd-configurator-main-article-price" ) );
            var rebate    = parseFloat( me.$el.attr( "data-atsd-configurator-rebate" ) );

            // start with the price of the main article with and without rebate
            var price       = mainPrice;
            var pseudoPrice = mainPrice;

            // get every selected input
            me.$el.find( 'input[data-atsd-configurator-selector="true"]:checked' ).each ( function()
                {
                    // get the article
                    var article = me.getArticleById( $( this ).attr( "data-atsd-configurator-selector-article-id" ) );

                    // get the quantity
                    var quantity = me.getArticleQuantityByArticle( article );

                    // get current article scaled price
                    var articlePrice = me.getArticlePriceForQuantity( article.attr( "data-atsd-configurator-article-prices" ), quantity );

                    // add prices
                    price       += quantity * parseFloat( articlePrice ) * ( ( 100 - rebate ) / 100 );
                    pseudoPrice += quantity * parseFloat( articlePrice );
                }
            );

            // format them
            var formattedPrice       = me.formatPrice( price );
            var formattedPseudoPrice = me.formatPrice( pseudoPrice );



            // get the template
            var template = ( ( rebate > 0 ) && ( price != pseudoPrice ) )
                ? me.configuration.priceTemplates.pseudo
                : me.configuration.priceTemplates.default;

            // insert variables
            template = template.replace( "#price#", formattedPrice );
            template = template.replace( "#pseudoPrice#", formattedPseudoPrice );
            template = template.replace( "#rebate#", rebate.toString() );

            // set the new price
            $( me.configuration.priceSelectors.container ).html( template );



            // is this rebated?
            if ( ( rebate > 0 ) && ( price != pseudoPrice ) )
                // add a html class
                $( me.configuration.priceSelectors.container ).addClass( me.configuration.priceSelectors.discount );
            // not rebated
            else
                // remove it
                $( me.configuration.priceSelectors.container ).removeClass( me.configuration.priceSelectors.discount );

        },




        // ...
        parseArticlePrices: function()
        {
            // get this
            var me = this;

            // get config details
            var rebate = parseFloat( me.$el.attr( "data-atsd-configurator-rebate" ) );

            // get every article
            me.$el.find( 'div[data-atsd-configurator-article="true"]' ).each ( function()
                {
                    // the article
                    var article = $( this );

                    // get the quantity
                    var quantity = me.getArticleQuantityByArticle( article );

                    // get the article price for it
                    var articlePrice = me.getArticlePriceForQuantity( article.attr( "data-atsd-configurator-article-prices" ), quantity );

                    // get the price
                    var price = quantity * parseFloat( articlePrice ) * ( ( 100 - rebate ) / 100 );

                    // set the price
                    article.find( '.price--placeholder' ).html(
                        me.formatPrice( price )
                    );
                }
            );
        },






        // ...
        parseArticleDeliveryStatus: function()
        {
            // get this
            var me = this;

            // get every article
            me.$el.find( 'div[data-atsd-configurator-article="true"]' ).each ( function()
                {
                    // the article
                    var article = $( this );

                    // get the quantity
                    var quantity = me.getArticleQuantityByArticle( article );

                    // get stock
                    var stock = article.attr( "data-atsd-configurator-article-stock" );

                    // set the price
                    article.find( '.delivery-status--placeholder' ).html(
                        ( quantity <= stock )
                            ? me.configuration.deliveryStatus.available
                            : me.configuration.deliveryStatus.notAvailable
                    );
                }
            );
        },





        // ...
        getArticleQuantityById: function( id )
        {
            // get the input
            return parseInt( $( '*[data-atsd-configurator-article-quantity-selector="true"][data-atsd-configurator-article-quantity-selector-article-id="' + id + '"]' ).val() );
        },



        // ...
        getArticleQuantityByArticle: function( article )
        {
            // call by id
            return this.getArticleQuantityById( article.attr( "data-atsd-configurator-article-id" ) );
        },




        // ...
        getArticleById: function( id )
        {
            // return by selector
            return this.$el.find( '[data-atsd-configurator-article="true"][data-atsd-configurator-article-id="' + id + '"]' ).first();
        },



        // ...
        parsePriceTemplate: function ()
        {
            // get this
            var me = this;

            // get the original
            var template = me.configuration.priceFormat.template;

            // split it the first time
            var main = template.split( "99" );

            // set after
            me.configuration.priceFormat.post = main[1];

            // split the left part by 345 to get decimal seperator
            var sub = main[0].split( "345" );

            // save it
            me.configuration.priceFormat.decPoint = sub[1];

            // left left part
            sub = sub[0].split( "12" );

            // set the rest
            me.configuration.priceFormat.pre = sub[0];
            me.configuration.priceFormat.thousandsSep = sub[1];
        },



        // format as a price
        formatPrice: function( price )
        {
            // return number format with currency
            return this.configuration.priceFormat.pre + this.formatNumber( price, 2 ) + this.configuration.priceFormat.post;
        },



        // parse a floating price and returns as string optional with currency
        formatNumber: function( number, decimals )
        {
            // get this
            var me = this;

            // round it
            number = Math.round( ( number * 100 ) ) / 100;

            // make it a string
            number = number.toString();

            // do we have a decimal point?
            if ( number.indexOf( "." ) < 0 )
                // add default
                number += ".0";

            // explode it by decimal
            var explode = number.split( "." );

            // fill after decimal with zeros
            for ( var i = 0; i < decimals - explode[1].length; i++ )
                // add it
                explode[1] = explode[1] + "0";

            // just check for 1 thousand seperator
            if ( explode[0].length > 3 )
                // set it new
                explode[0] = explode[0].slice( 0, explode[0].length - 3 ) + me.configuration.priceFormat.thousandsSep + explode[0].slice( -3 );

            // set the formatted price
            return explode[0] + me.configuration.priceFormat.decPoint + explode[1];
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



    // wait till the document is ready
    document.asyncReady( function() {
        // call our plugin
        StateManager.addPlugin( '*[data-atsd-configurator="true"]', "atsdConfigurator", {} );
    });

})(jQuery);
