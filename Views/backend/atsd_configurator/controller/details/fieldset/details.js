
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.details.fieldset.Details",
{

    // parent
    extend:'Enlight.app.Controller',

    // references
    refs:
        [
            { ref: "detailWindow",  selector: "#atsdconfigurator-details-window" },
            { ref: "fieldsetGrid",  selector: "#atsdconfigurator-details-fieldsets" },
            { ref: "elementGrid",   selector: "#atsdconfigurator-details-elements" },
            { ref: "listGrid",      selector: "#atsdconfigurator-list-configurators" },
            { ref: "fieldsetWindow", selector: "#atsdconfigurator-details-fieldset-window" },
            { ref: "detailsForm",   selector: "#atsdconfigurator-details-fieldset-details" }
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
                'atsdconfigurator-details-fieldset-details':
                {
                    saveFieldset:  me.onSaveFieldset,
                    closeFieldset: me.onCloseFieldset
                }
            }
        );
    },






    //
    onCloseFieldset: function()
    {
        // get this
        var me = this;

        // destroy the window
        me.getFieldsetWindow().destroy();
    },




    //
    onSaveFieldset: function( view, record )
    {
        // get this
        var me = this;

        // is the form valid
        if ( view.getForm().isValid() == false )
            // nothing to do here
            return;

        // save the detail data
        view.getForm().updateRecord( record );



        // enable loading
        me.getFieldsetWindow().setLoading( true );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getFieldsetWindow().setLoading( false );

                    // reload the main store
                    me.getFieldsetGrid().getStore().reload();
                    me.getElementGrid().setDisabled( true );

                    // output message
                    Shopware.Notification.createGrowlMessage( "", "Gruppe erfolgreich gespeichert." );

                    // destroy the window
                    me.getFieldsetWindow().destroy();
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getFieldsetWindow().setLoading( false );

                    // get error
                    var rawData = result.getProxy().getReader().rawData;

                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: "Speichern fehlgeschlagen",
                            text:  "Fehlermeldung: " + rawData.error
                        }
                    )
                }
            });
    }







});





