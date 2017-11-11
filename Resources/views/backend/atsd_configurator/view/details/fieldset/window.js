
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.fieldset.Window",
{
    // parent
    extend: "Enlight.app.Window",

    // css 
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-fieldset-window",

    // alias
    alias: "widget.atsdconfigurator-details-fieldset-window",
    itemId: "atsdconfigurator-details-fieldset-window",

    // no border
    border: false,

    // show window immediatly
    autoShow: true,

    // options
    maximizable: false,
    minimizable: false,

    // layout
    layout: "fit",

    // style
    width: 800,
    height: 250,
    
    // title
    title: "Gruppe bearbeiten",
    
    // stateful
    stateful : true,

    // modal
    modal: true,
    

    
    // our fieldset record
    record: null,

    // detail form
    detailsForm: undefined,



    // init
    initComponent: function()
    {
        // get
        var me = this;
        
        // register all events
        me.registerEvents();

        // create items
        me.createItems();

        // set alle views
        me.items = me.getItems();

        // call parent
        me.callParent( arguments );
    },





    
    // register all events
    registerEvents: function()
    {
    },








    //
    createItems: function()
    {
        // get this
        var me = this;

        // create detail form
        me.detailsForm = Ext.create( "Shopware.apps.AtsdConfigurator.view.details.fieldset.Details",
            {
                title: "Details",
                record: me.record
            }
        );
    },










    // get all views
    getItems: function()
    {
        // get this
        var me = this;
        
        // get all window items
        var items =
            [
                Ext.create( "Ext.tab.Panel",
                    {
                        layout: 'fit',
                        region: 'center',
                        autoscroll: true,
                        items:
                            [
                                me.detailsForm,
                                me.articlesPanel
                            ]
                    }
                )
            ];
        
        // return the items
        return items;
    }
    
    


    
    
});









