
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.details.element.Details",
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
            { ref: "elementWindow", selector: "#atsdconfigurator-details-element-window" },
            { ref: "detailsForm",   selector: "#atsdconfigurator-details-element-details" }
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
                'atsdconfigurator-details-element-details':
                {
                    saveElement:  me.onSaveElement,
                    closeElement: me.onCloseElement
                }
            }
        );
        
        // done
        return;
    },






    //
    onCloseElement: function()
    {
        // get this
        var me = this;

        // destroy the window
        me.getElementWindow().destroy();
    },




    //
    onSaveElement: function( view, record )
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
        me.getElementWindow().setLoading( true );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getElementWindow().setLoading( false );

                    // reload the main store
                    me.getElementGrid().getStore().reload();

                    // output message
                    Shopware.Notification.createGrowlMessage( "", "Element erfolgreich gespeichert." );

                    // destroy the window
                    me.getElementWindow().destroy();
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getElementWindow().setLoading( false );

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





