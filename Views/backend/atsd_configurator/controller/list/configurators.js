
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.list.Configurators",
{

    // parent
    extend:'Enlight.app.Controller',

    // references
    refs:
        [
            { ref: "configuratorList", selector: "#atsdconfigurator-list-configurators" }
        ],
        
    // mainWindow
    mainWindow: null,
    


    // controller init
    init: function()
    {
        // get this
        var me = this;

        // save the main window in this controller
        me.mainWindow = me.getController( "Main" ).mainWindow;
        
        // add controls
        me.addControls();
    },
    
    
    
    
    

    // register actions
    addControls: function()
    {
        // get this
        var me = this;

        // add controls
        me.control(
            {
                // order list
                'atsdconfigurator-list-configurators':
                {
                    'editConfigurator':   me.onEditConfigurator,
                    'searchConfigurator': me.onSearchConfigurator,
                    'deleteConfigurator': me.onDeleteConfigurator,
                    'addConfigurator':    me.onAddConfigurator,
                    'updateConfigurator': me.onUpdateConfigurator
                }
            }
        );

        // done
        return;
    },








    // search
    onSearchConfigurator: function( search, grid )
    {
        // get this
        var me = this;

        // get the store from the list grid
        var store = grid.getStore();

        // go to 1st page
        store.currentPage = 1;

        // trim the search value
        search = Ext.String.trim( search );

        // set the search parameter
        store.getProxy().extraParams.search = search;

        // and reload the store
        store.load();
    },













    //
    onEditConfigurator: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // create a window
        var window = me.createConfiguratorDetailsWindow( record );

        // and show it
        window.show();

        // done
        return;
    },






    // create window
    createConfiguratorDetailsWindow: function( record )
    {
        // get this
        var me = this;

        // create the view
        var view = me.getView( "Shopware.apps.AtsdConfigurator.view.details.Window" ).create(
            {
                record: record
            }
        );

        // and return it
        return view;
    },








    //
    onUpdateConfigurator: function( record )
    {
        // get this
        var me = this;

        // enable loading
        me.getConfiguratorList().setLoading( true );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getConfiguratorList().setLoading( false );

                    // reload the store
                    me.getConfiguratorList().getStore().reload();
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getConfiguratorList().setLoading( false );

                    // get error
                    var rawData = result.getProxy().getReader().rawData;

                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: "Speichern fehlgeschlagen",
                            text:  "Fehlermeldung: " + rawData.error
                        }
                    );

                    // still reload the list
                    me.getConfiguratorList().getStore().reload();
                }
            });

        // done
        return;
    },






    //
    onAddConfigurator: function( view )
    {
        // get this
        var me = this;

        // get the values
        var name = me.getConfiguratorList().addName.getValue();

        // valid input?
        if ( name == "" || name == null )
        {
            // show error
            Shopware.Notification.createGrowlMessage( "Aktion abgebrochen", "Bitte geben Sie vollständige Daten an." );

            // done
            return;
        }

        // create a new object
        var configurator = Ext.create( "Shopware.apps.AtsdConfigurator.model.Configurator",
            {
                name:   name,
                rebate: 0
            }
        );

        // set the window to loading
        me.getConfiguratorList().setLoading( true );

        // try to save the record
        configurator.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getConfiguratorList().setLoading( false );

                    // reset values
                    me.getConfiguratorList().addName.setValue( null );

                    // reload the main store
                    me.getConfiguratorList().getStore().reload();

                    // output message
                    Shopware.Notification.createGrowlMessage( "Eintrag gespeichert", "Der Eintrag wurde erfolgreich gespeichert." );
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getConfiguratorList().setLoading( false );

                    // get error
                    var rawData = result.getProxy().getReader().rawData;

                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: "Aktion abgebrochen",
                            text:  "Der Eintrag konnte nicht gespeichert werden. Fehlermeldung: " + rawData.error
                        }
                    )
                }
            }
        );
    },






    //
    onDeleteConfigurator: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // ask if we should really delete the record
        Ext.MessageBox.confirm( "Konfigurator löschen", "Möchten Sie den Konfigurator <b>" + record.get( "name" ) + "</b> wirklich löschen?", function ( response )
            {
                // dont load the new template
                if ( response !== "yes" )
                    // just return
                    return;

                // set loading
                me.getConfiguratorList().setLoading( true );

                // destroy the record
                record.destroy(
                    {
                        callback: function( data, operation )
                        {
                            // disable loading
                            me.getConfiguratorList().setLoading( false );

                            // output message
                            Shopware.Notification.createGrowlMessage( "", "Der Konfigurator wurde erfolgreich gelöscht." );

                            // reload the store
                            grid.getStore().reload();
                        }
                    }
                )
            }
        );
    }






});





