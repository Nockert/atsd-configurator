
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.Main",
{

    // parent
    extend:'Enlight.app.Controller',

    // references
    refs:
        [
            { ref: "window", selector: "#atsdconfigurator-window" }
        ],
        
        
        
    // main window
    mainWindow: null,
        


    // controller init
    init: function()
    {
        // get this
        var me = this;

		

        // get the main window
        me.mainWindow = me.createMainWindow();

        // set me
        me.mainWindow.controller = me;

        // add controls
        me.addControls();
        
        // call parent init
        me.callParent( arguments );
        
        
        
        // return main window
        return me.mainWindow;
    },
    
    
    
    
    

    // register actions
    addControls: function()
    {
        // get this
        var me = this;

        // add controls
        me.control(
            {
            }
        );
        
        // done
        return;
    },
    
    
    
    
    // create the main window - including every view
    createMainWindow: function()
    {
        // get this
        var me = this;

        // get the view
        var window = me.getView( "MainWindow" ).create(
            {
            }
        ).show();
 
        // and return the window
        return window;
    }










});





