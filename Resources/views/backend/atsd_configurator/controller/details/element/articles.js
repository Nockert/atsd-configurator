
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.controller.details.element.Articles",
{

    // parent
    extend: "Enlight.app.Controller",

    // references
    refs:
        [
            { ref: "articlesWindow",        selector: "#atsdconfigurator-details-element-window" },
            { ref: "articlesPanel",         selector: "#atsdconfigurator-details-element-articles" },
            { ref: "articlesGridAvailable", selector: "#atsdconfigurator-details-element-articles-source-grid" },
            { ref: "articlesGridAssigned",  selector: "#atsdconfigurator-details-element-articles-target-grid" }
        ],
        
    // main window
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
        
        // call parent
        me.callParent( arguments );
        
        // done
        return;
    },
    
    
    

    // register actions
    addControls: function()
    {
        // get this
        var me = this;

        // add controls
        me.control(
            {
                'atsdconfigurator-details-element-articles':
                    {
                        itemsAdd:      me.onArticleAdd,
                        itemsRemove:   me.onArticleRemove,
                        itemsSearch:   me.onArticleSearch,
                        updateArticle: me.onUpdateArticle
                    }
            }
        );
        
        // done
        return;
    },





    //
    onUpdateArticle: function( record )
    {
        // get this
        var me = this;

        // enable loading
        me.getArticlesPanel().setLoading( true );

        // try to save the model
        record.save(
            {
                // successful delete
                success: function( result )
                {
                    // disable loading
                    me.getArticlesPanel().setLoading( false );

                    // reload the store
                    me.getArticlesGridAssigned().getStore().reload();
                },
                // failed
                failure: function( result, operation )
                {
                    // disable loading
                    me.getArticlesPanel().setLoading( false );

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
                    me.getArticlesGridAssigned().getStore().reload();
                }
            });

        // done
        return;
    },





    // 
    onArticleSearch: function( search, grid )
    {
        // get this
        var me = this;

        // load it
        grid.setLoading( true );
        
        // get the store from the list grid
        var store = grid.getStore();
        
        // go to 1st page
        store.currentPage = 1;
        
        // trim the search value
        search = Ext.String.trim( search );
        
        // set the search parameter
        store.getProxy().extraParams.search = search;
        
        // and reload the store
        store.load(
            {
                // after load finished
                callback : function( records, options, success )
                {
                    // disable loading
                    grid.setLoading( false );
                }
            }
        );
    },
            
                    
                    



        
    //
    onArticleAdd: function()
    {
        // get this
        var me = this;
        
        // get our shop view
        var view = me.getArticlesPanel();

        // make our ajax call
        view.onItemsMove(
            me.getArticlesGridAvailable(),
            me.getArticlesGridAssigned(),
            me.getArticlesWindow(),
            "{url controller=AtsdConfigurator action=addElementArticle}",
            me.getArticlesWindow().record.get( "id" ),
            "<b>[0]</b> Artikel hinzugefügt",
            "Aktion fehlgeschlagen",
            "Artikel konnte(n) nicht zugeordnet werden.<br />Fehlermeldung:",
            false
        );
        
        // and we are done
        return;
    },
    
                        
        
            
    //
    onArticleRemove: function()
    {
        // get this
        var me = this;
        
        // get our shop view
        var view = me.getArticlesPanel();



        // ask if we should really delete the record
        Ext.MessageBox.confirm( "Artikel entfernen", "Möchten Sie die markierten Artikel wirklich entfernen?<br><b>Hinweis: </b>Selektoren, die diese Artikel beinhalten, werden möglicherweise ungültig.", function ( response )
            {
                // dont load the new template
                if ( response !== "yes" )
                    // just return
                    return;

                // make our ajax call
                view.onItemsMove(
                    me.getArticlesGridAssigned(),
                    me.getArticlesGridAvailable(),
                    me.getArticlesWindow(),
                    "{url controller=AtsdConfigurator action=removeElementArticle}",
                    me.getArticlesWindow().record.get( "id" ),
                    "<b>[0]</b> Artikel entfernt",
                    "Aktion fehlgeschlagen",
                    "Artikel konnte(n) nicht entfernt werden.<br />Fehlermeldung:",
                    false
                );
            }
        );


        
        // and we are done
        return;
    }
    
                        
            
    
    
    
    
});





