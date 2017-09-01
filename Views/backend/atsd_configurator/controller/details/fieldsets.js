
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.details.Fieldsets",
{

    // parent
    extend:'Enlight.app.Controller',

    // references
    refs:
        [
            { ref: "detailWindow", selector: "#atsdconfigurator-details-window" },
            { ref: "fieldsetGrid", selector: "#atsdconfigurator-details-fieldsets" },
            { ref: "elementGrid",  selector: "#atsdconfigurator-details-elements" },
            { ref: "listGrid",     selector: "#atsdconfigurator-list-configurators" }
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
                'atsdconfigurator-details-fieldsets':
                {
                    'addFieldset':    me.onAddFieldset,
                    'selectFieldset': me.onSelectFieldset,
                    'deleteFieldset': me.onDeleteFieldset,
                    'updateFieldset': me.onUpdateFieldset,
                    'editFieldset':   me.onEditFieldset
                }
            }
        );
    },






    //
    onDeleteFieldset: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // ask if we should really delete the record
        Ext.MessageBox.confirm( "Gruppe löschen", "Möchten Sie die Gruppe <b>" + record.get( "name" ) + "</b> wirklich löschen?", function ( response )
            {
                // dont load the new template
                if ( response !== "yes" )
                    // just return
                    return;

                // destroy the record
                record.destroy(
                    {
                        callback: function( data, operation )
                        {
                            // output message
                            Shopware.Notification.createGrowlMessage( "", "Die Gruppe wurde erfolgreich gelöscht." );

                            // reload the store
                            grid.getStore().reload();

                            // disable elements grid
                            me.getElementGrid().setDisabled( true );
                        }
                    }
                )
            }
        );
    },











    //
    onSelectFieldset: function( grid, view, selected )
    {
        // get this
        var me = this;

        // any selected?
        if ( selected.length == 0 )
            // nothing to do
            return;

        // selected record
        var record = selected[0];

        // set the fieldset in the view
        me.getFieldsetGrid().selectedFieldset = record;

        // load the store
        me.getElementGrid().getStore().getProxy().extraParams.fieldsetId = record.get( "id" );
        me.getElementGrid().getStore().load();

        // activate it (might be disabled for first fieldset)
        me.getElementGrid().setDisabled( false );

        // set current record
        me.getElementGrid().fieldsetRecord = record;

        // clear input fields
        // me.getArticlesWindow().documentsGrid.addDocumentKey.setValue( null );

        // done
        return;
    },





    //
    onUpdateFieldset: function( record )
    {
        // get this
        var me = this;

        // enable loading
        me.getDetailWindow().setLoading( true );

        // disable element grid
        me.getElementGrid().setDisabled( false );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

                    // reload the store
                    me.getFieldsetGrid().getStore().reload();
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

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
                    me.getFieldsetGrid().getStore().reload();
                }
            });
    },





    //
    onAddFieldset: function( view )
    {
        // get this
        var me = this;

        // get the values
        var description = me.getFieldsetGrid().addDescription.getValue();
        var name        = me.getFieldsetGrid().addName.getValue();
        var mediaFile   = me.getFieldsetGrid().addMediaFile.getValue();

        // valid input?
        if ( description == null || description == "" || name == "" || name == null )
        {
            // show error
            Shopware.Notification.createGrowlMessage( "Aktion abgebrochen", "Bitte geben Sie vollständige Daten an." );

            // done
            return;
        }

        // create a new object
        var state = Ext.create( "Shopware.apps.AtsdConfigurator.model.configurator.Fieldset",
            {
                description:    description,
                name:           name,
                mediaFile:      mediaFile,
                configuratorId: me.getFieldsetGrid().record.get( "id" )
            }
        );

        // set the window to loading
        me.getDetailWindow().setLoading( true );

        // try to save the record
        state.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

                    // reset values
                    me.getFieldsetGrid().addDescription.setValue( null );
                    me.getFieldsetGrid().addName.setValue( null );
                    me.getFieldsetGrid().addMediaFile.setValue( null );

                    // reload the main store
                    me.getFieldsetGrid().getStore().reload();

                    // output message
                    Shopware.Notification.createGrowlMessage( "Eintrag gespeichert", "Der Eintrag wurde erfolgreich gespeichert." );
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

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





    // ...
    onEditFieldset: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // create a window
        var window = me.createDetailsWindow( record );

        // and show it
        window.show();
    },





    // create window
    createDetailsWindow: function( record )
    {
        // get this
        var me = this;

        // create the view
        return me.getView( "Shopware.apps.AtsdConfigurator.view.details.fieldset.Window" ).create(
            {
                record: record
            }
        );
    }




});





