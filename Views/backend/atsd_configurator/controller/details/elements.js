
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.details.Elements",
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
                'atsdconfigurator-details-elements':
                {
                    'addElement':    me.onAddElement,
                    'deleteElement': me.onDeleteElement,
                    'editElement':   me.onEditElement,
                    'updateElement': me.onUpdateElement
                }
            }
        );
    },






    //
    onDeleteElement: function( scope, grid, rowIndex, colIndex, button )
    {
        // get this
        var me = this;

        // get the store
        var store = grid.getStore();

        // get the record
        var record = store.getAt( rowIndex );

        // ask if we should really delete the record
        Ext.MessageBox.confirm( "Element löschen", "Möchten Sie das Element <b>" + record.get( "name" ) + "</b> wirklich löschen?", function ( response )
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
                            Shopware.Notification.createGrowlMessage( "", "Das Element wurde erfolgreich gelöscht." );

                            // reload the store
                            grid.getStore().reload();
                        }
                    }
                )
            }
        );
    },








    //
    onUpdateElement: function( record )
    {
        // get this
        var me = this;

        // enable loading
        me.getDetailWindow().setLoading( true );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

                    // reload the store
                    me.getElementGrid().getStore().reload();
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
                    me.getElementGrid().getStore().reload();
                }
            });

        // done
        return;
    },





    //
    onAddElement: function( view )
    {
        // get this
        var me = this;

        // get the values
        var description = me.getElementGrid().addDescription.getValue();
        var name        = me.getElementGrid().addName.getValue();

        // valid input?
        if ( description == null || description == "" || name == "" || name == null )
        {
            // show error
            Shopware.Notification.createGrowlMessage( "Aktion abgebrochen", "Bitte geben Sie vollständige Daten an." );

            // done
            return;
        }

        // create a new object
        var element = Ext.create( "Shopware.apps.AtsdConfigurator.model.configurator.fieldset.Element",
            {
                description: description,
                name:        name,
                templateId:  1,
                fieldsetId:  me.getElementGrid().fieldsetRecord.get( "id" )
            }
        );

        // set the window to loading
        me.getDetailWindow().setLoading( true );

        // try to save the record
        element.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getDetailWindow().setLoading( false );

                    // reset values
                    me.getElementGrid().addDescription.setValue( null );
                    me.getElementGrid().addName.setValue( null );

                    // reload the main store
                    me.getElementGrid().getStore().reload();

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





    //
    onEditElement: function( scope, grid, rowIndex, colIndex, button )
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

        // done
        return;
    },






    // create window
    createDetailsWindow: function( record )
    {
        // get this
        var me = this;

        // create the view
        var view = me.getView( "Shopware.apps.AtsdConfigurator.view.details.element.Window" ).create(
            {
                record: record
            }
        );

        // and return it
        return view;
    }







});





