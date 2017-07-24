
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.element.Window",
{
    // parent
    extend: "Enlight.app.Window",

    // css 
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-element-window",

    // alias
    alias: "widget.atsdconfigurator-details-element-window",
    itemId: "atsdconfigurator-details-element-window",

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
    width: 1100,
    height: 500,
    
    // title
    title: "Element bearbeiten",
    
    // stateful
    stateful : true,

    // modal
    modal: true,
    

    
    // our element record
    record: null,

    // detail form
    detailsForm: undefined,
    articlesPanel: undefined,



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
        me.detailsForm = Ext.create( "Shopware.apps.AtsdConfigurator.view.details.element.Details",
            {
                title: "Details",
                record: me.record
            }
        );

        // create the store for available shops
        me.articlesAvailableStore = Ext.create( "Shopware.apps.AtsdConfigurator.store.configurators.fieldsets.elements.articles.Available" );
        me.articlesAvailableStore.getProxy().extraParams = { elementId: me.record.get( "id" ) };
        me.articlesAvailableStore.load();

        // create the store for assigned shops
        me.articlesAssignedStore = Ext.create( "Shopware.apps.AtsdConfigurator.store.configurators.fieldsets.elements.articles.Assigned" );
        me.articlesAssignedStore.getProxy().extraParams = { elementId: me.record.get( "id" ) };
        me.articlesAssignedStore.load();

        // create articles panel
        me.articlesPanel = Ext.create( "Shopware.apps.AtsdConfigurator.view.details.element.Articles",
            {
                title: "Artikel",
                record: me.record,
                itemsSourceStore: me.articlesAvailableStore,
                itemsTargetStore: me.articlesAssignedStore
            }
        );

        // done
        return;
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









