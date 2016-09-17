
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.MainWindow",
{
    // parent
    extend: "Enlight.app.Window",

    // css 
    cls: Ext.baseCSSPrefix + "atsdconfigurator-window",

    // alias
    alias: "widget.atsdconfigurator-window",
    itemId: "atsdconfigurator-window",

    // no border
    border: false,

    // show window immediatly
    autoShow: true,

    // options
    maximizable: true,
    minimizable: true,

    // layout
    layout: "fit",
    
    // style
    width: 1100,
    height: 500,

    // title
    title: "Artikel Konfigurator",



    // main controller
    controller: null,

    // main list
    listGrid: null,
    listStore: null,



    // init
    initComponent:function ()
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
        // add event
        this.addEvents();
    },






    //
    createItems: function()
    {
        //
        var me = this;

        // create the list store
        me.listStore = Ext.create( "Shopware.apps.AtsdConfigurator.store.Configurators" );
        me.listStore.load();

        // create the list
        me.listGrid = Ext.create( "Shopware.apps.AtsdConfigurator.view.list.Configurators",
            {
                title: "Artikel Konfiguratoren",
                store: me.listStore
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

        // get the items
        var items =
            [
                Ext.create( "Ext.tab.Panel",
                    {
                        layout: "fit",
                        border: false,
                        region: "center",
                        items:
                            [
                                me.listGrid
                            ]
                    }
                )
            ];

        // return the items
        return items;
    }











});









