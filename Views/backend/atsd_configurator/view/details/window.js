
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.Window",
{
    // parent
    extend: "Enlight.app.Window",

    // css 
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-window",

    // alias
    alias: "widget.atsdconfigurator-details-window",
    itemId: "atsdconfigurator-details-window",

    // no border
    border: false,

    // show window immediatly
    autoShow: true,

    // options
    maximizable: true,
    minimizable: false,

    // layout
    layout:
    {
        align: 'stretch',
        type: 'hbox'
    },

    // style
    width: 1100,
    height: 500,
    
    // title
    title: "Konfigurator bearbeiten",
    
    // stateful
    stateful : true,

    // modal
    modal: true,
    

    
    // our configurator record
    record: null,

    // fieldset stuff
    fieldsetGrid: undefined,
    fieldsetStore: undefined,

    // element stuff
    elementGrid: undefined,
    elementStore: undefined,



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

        // create the store
        me.fieldsetStore = Ext.create( "Shopware.apps.AtsdConfigurator.store.configurators.Fieldsets" );
        me.fieldsetStore.getProxy().extraParams.configuratorId = me.record.get( "id" );
        me.fieldsetStore.load();

        // create the store
        me.elementStore = Ext.create( "Shopware.apps.AtsdConfigurator.store.configurators.fieldsets.Elements" );
        me.elementStore.getProxy().extraParams.fieldsetId = 0;
        me.elementStore.load();

        // create the article view
        me.fieldsetGrid = Ext.create( "Shopware.apps.AtsdConfigurator.view.details.Fieldsets",
            {
                title: "Gruppen",
                flex: 1,
                record: me.record,
                store: me.fieldsetStore,
                viewConfig:
                {
                    // set drag & drop plugin
                    plugins:
                        [
                            // create drag & drop plugin
                            Ext.create( "Ext.grid.plugin.DragDrop", { ddDrop: "fieldsetGroup", dragGroup: "fieldsetGroup", dropGroup: "fieldsetGroup" } )
                        ],
                    // add listeners
                    listeners:
                    {
                        // scope
                        scope: me,
                        // drop listener
                        drop: me.onDragDropFieldset
                    }
                }
            }
        );

        // document view
        me.elementGrid = Ext.create( "Shopware.apps.AtsdConfigurator.view.details.Elements",
            {
                title: "Elemente",
                flex: 1,
                disabled: true,
                record: me.record,
                store: me.elementStore,
                viewConfig:
                {
                    // set drag & drop plugin
                    plugins:
                        [
                            // create drag & drop plugin
                            Ext.create( "Ext.grid.plugin.DragDrop", { ddDrop: "elementGroup", dragGroup: "elementGroup", dropGroup: "elementGroup" } )
                        ],
                    // add listeners
                    listeners:
                    {
                        // scope
                        scope: me,
                        // drop listener
                        drop: me.onDragDropElement
                    }
                }
            }
        );
    },






    //
    onDragDropFieldset: function( node, data, record, position )
    {
        // get this
        var me = this;

        // to save the order
        var fieldsets = [];

        // set counter
        var i = 1;

        // loop all elements
        me.fieldsetGrid.getStore().each( function( record )
            {
                // set the order
                fieldsets.push( [ record.get( "id" ), i++ ] );
            }
        );

        // disable this window
        me.setLoading( true );

        // disable element window
        me.elementGrid.setDisabled( true );



        // send ajax request to save the position
        Ext.Ajax.request(
            {
                // our backend url
                url: "{url controller=AtsdConfigurator action=saveFieldsetPositions}",
                // parameters
                params:
                {
                    fieldsets: Ext.JSON.encode( fieldsets )
                },
                // success function
                success: function( response )
                {
                    // decode our response
                    var result = Ext.JSON.decode( response.responseText );

                    // did we get an error?
                    if ( result.success == false )
                    {
                        // show error message
                        Shopware.Notification.createStickyGrowlMessage(
                            {
                                title: "Aktion fehlgeschlagen",
                                text:  "Ein unbekannter Fehler ist aufgetreten.<br>Fehlermeldung: " + result.error
                            }
                        );

                        // and done
                        return;
                    }

                    // disable loading
                    me.setLoading( false );

                    // reload the grid
                    me.fieldsetGrid.getStore().reload();
                },
                // ajax call failed
                failure: function( response )
                {
                    // get error
                    var rawData = response.getProxy().getReader().rawData;

                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: "Aktion fehlgeschlagen",
                            text:  "Ein unbekannter Fehler ist aufgetreten.<br>Fehlermeldung: " + rawData.error
                        }
                    );

                    // enable this window
                    me.setLoading( false );
                }
            });



        // done
        return;
    },







    //
    onDragDropElement: function( node, data, record, position )
    {
        // get this
        var me = this;

        // to save the order
        var elements = [];

        // set counter
        var i = 1;

        // loop all elements
        me.elementGrid.getStore().each( function( record )
            {
                // set the order
                elements.push( [ record.get( "id" ), i++ ] );
            }
        );

        // disable this window
        me.setLoading( true );



        // send ajax request to save the position
        Ext.Ajax.request(
            {
                // our backend url
                url: "{url controller=AtsdConfigurator action=saveElementPositions}",
                // parameters
                params:
                {
                    elements: Ext.JSON.encode( elements )
                },
                // success function
                success: function( response )
                {
                    // decode our response
                    var result = Ext.JSON.decode( response.responseText );

                    // did we get an error?
                    if ( result.success == false )
                    {
                        // show error message
                        Shopware.Notification.createStickyGrowlMessage(
                            {
                                title: "Aktion fehlgeschlagen",
                                text:  "Ein unbekannter Fehler ist aufgetreten.<br>Fehlermeldung: " + result.error
                            }
                        );

                        // and done
                        return;
                    }

                    // disable loading
                    me.setLoading( false );

                    // reload the grid
                    me.elementGrid.getStore().reload();
                },
                // ajax call failed
                failure: function( response )
                {
                    // get error
                    var rawData = response.getProxy().getReader().rawData;

                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: "Aktion fehlgeschlagen",
                            text:  "Ein unbekannter Fehler ist aufgetreten.<br>Fehlermeldung: " + rawData.error
                        }
                    );

                    // enable this window
                    me.setLoading( false );
                }
            });



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
                me.fieldsetGrid,
                me.elementGrid
            ];
        
        // return the items
        return items;
    }
    
    


    
    
});









