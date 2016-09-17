
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator",
{
    // name
    name: "Shopware.apps.AtsdConfigurator",

    // parent
    extend: "Enlight.app.SubApplication",
 
    // bulkload
    bulkLoad: true,
 
    // loadpath
    loadPath: '{url controller="AtsdConfigurator" action="load"}',
 
 
 
    // views
    views:
        [
            "MainWindow",
            "list.Configurators",
            "details.Window",
            "details.Elements",
            "details.Fieldsets",
            "details.element.Window"
        ],
    
    // stores
    stores:
        [
            "Configurators",
            "Templates",
            "configurators.fieldsets.elements.articles.Available",
            "configurators.fieldsets.elements.articles.Assigned"
        ],
    
    // models
    models:
        [
            "Configurator",
            "Template",
            "configurator.fieldset.element.article.Available",
            "configurator.fieldset.element.article.Assigned"
        ],
    
    // controllers
    controllers:
        [
            "Main",
            "list.Configurators",
            "details.Elements",
            "details.Fieldsets",
            "details.element.Details",
            "details.element.Articles"
        ],
 
 
    
    // launch the app
    launch: function()
    {
        // get this
        var me = this;

        // get the controller
        var mainController = me.getController( "Main" );
        
        // return the main window
        return mainController.mainWindow;
    }
    
});


