
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.element.Articles",
{
    // parent
    extend: 'Ext.form.Panel',

    // layout
    layout:
        {
            type: 'hbox',
            pack: 'start',
            align: 'stretch'
        },

    // css
    border: false,
    bodyPadding: 10,
    cls: 'shopware-form',    

    // alias
    alias: 'widget.atsdconfigurator-details-element-articles',
    itemId: 'atsdconfigurator-details-element-articles',
    
    // snippets
    snippets:
        {
            sourceGridTitle: "Verfügbare Artikel",
            targetGridTitle: "Zugeordnete Artikel",
            buttonAdd:       "Artikel hinzufügen",
            buttonRemove:    "Artikel entfernen",
            searchText:      "Suche..."
        },

    // columns
    sourceGridColumns:
        [
            {
                header: "ID",
                width: 60,
                dataIndex: 'id'
            },
            {
                header: "Artikel",
                flex: 1,
                dataIndex: 'articleNumber'
            },
            {
                header: "Name",
                flex: 3,
                dataIndex: 'articleName'
            }
        ],

    // columns
    targetGridColumns: [],
        
    // source configuration
    source:
        {
            alias:  "widget.atsdconfigurator-details-element-articles-source-grid",
            itemId: "atsdconfigurator-details-element-articles-source-grid"
        },
        
    // target configuration
    target:
        {
            alias:  "widget.atsdconfigurator-details-element-articles-target-grid",
            itemId: "atsdconfigurator-details-element-articles-target-grid"
        },
        
    // event action manes
    eventNames:
        {
            // default
            onItemsAdd:    "itemsAdd",
            onItemsRemove: "itemsRemove",
            onItemsSearch: "itemsSearch",

            // row editor
            updateArticle: "updateArticle"
        },
        
    // active bottom (pager) and top (search) toolbars
    showToolbars: true,

    
    
    // all shop grids
    itemsSourceGrid: null,
    itemsTargetGrid: null,
    
    // all stores
    itemsSourceStore: null,
    itemsTargetStore: null,
    
    // record
    record: null,
    
    

    // init    
    initComponent: function()
    {
        // get this
        var me = this;
        
        // register all events
        me.registerEvents();
        
        // get all form items
        me.items = me.getItems();
        
        // call the parent
        me.callParent( arguments );
    },




    //
    renderArticleQuantity: function( value, metaData, record )
    {
        // make it a quantity
        return value + "x";
    },




    // register all events
    registerEvents: function()
    {
        // get this
        var me = this;
        
        // add events - search only needed if we have search bar available
        this.addEvents( me.eventNames.onItemsAdd, me.eventNames.onItemsRemove, me.eventNames.onItemsSearch );

        // add row editor
        this.addEvents( me.eventNames.updateArticle );
    },
    
    
    

    // get alle form items
    getItems: function()
    {
        // get this
        var me = this;
        
        // create items
        var items =
            [
                // available shops
                me.getItemsSourceGrid(),
                // buttons
                me.getItemsButtons(),
                // allready mapped shops
                me.getItemsTargetGrid()
            ];

        // return the items
        return items;
    },
    
    
    


    // get the source grid
    getItemsSourceGrid: function()
    {
        // get this
        var me = this;
        
        // create the grid
        me.itemsSourceGrid = Ext.create( "Ext.grid.Panel",
            {
                // set title
                title: me.snippets.sourceGridTitle,
                // transmit the store
                store: me.itemsSourceStore,
                // design
                flex: 1,
                // set options
                viewConfig: { loadMask: false },
                // get columns
                columns: me.getSourceGridColumns(),
                // get checkbox model
                selModel: me.getItemsSelectionModel(),
                // set alias and id
                alias: me.source.alias,
                itemId: me.source.itemId
            }
        );
        
        // add toolbars?
        if ( me.showToolbars == true )
        {
            // add top toolbar
            me.itemsSourceGrid.addDocked( me.getTopToolbar( me.itemsSourceGrid ) );
            // add bottom toolbar
            me.itemsSourceGrid.addDocked( me.getBottomToolbar( me.itemsSourceStore ) );
        }
        
        // return the grid
        return me.itemsSourceGrid;
    },
    
    
    
    


    // get the target grid
    getItemsTargetGrid: function()
    {
        // get this
        var me = this;
        
        // create the grid
        me.itemsTargetGrid = Ext.create( "Ext.grid.Panel",
            {
                // set title
                title: me.snippets.targetGridTitle,
                // transmit the store
                store: me.itemsTargetStore,
                // design
                flex: 1,
                // set options
                // viewConfig: { loadMask: false },
                // get columns
                columns: me.getTargetGridColumns(),
                // get checkbox model
                selModel: me.getItemsSelectionModel(),
                // set alias and id
                alias: me.target.alias,
                itemId: me.target.itemId,
                // we need plugins
                plugins: [ me.getRowEditorPlugin() ],
                viewConfig:
                {
                    // set drag & drop plugin
                    plugins:
                        [
                            // create drag & drop plugin
                            Ext.create( "Ext.grid.plugin.DragDrop", { ddDrop: "articleGroup", dragGroup: "articleGroup", dropGroup: "articleGroup" } )
                        ],
                    // add listeners
                    listeners:
                    {
                        // scope
                        scope: me,
                        // drop listener
                        drop: me.onDragDropArticle
                    }
                }
            }
        );
        
        // add toolbars?
        if ( me.showToolbars == true )
        {
            // add top toolbar
            me.itemsTargetGrid.addDocked( me.getTopToolbar( me.itemsTargetGrid ) );
            // add bottom toolbar
            me.itemsTargetGrid.addDocked( me.getBottomToolbar( me.itemsTargetStore ) );
        }
        
        // return the grid
        return me.itemsTargetGrid;
    },







    //
    onDragDropArticle: function( node, data, record, position )
    {
        // get this
        var me = this;

        // to save the order
        var articles = [];

        // set counter
        var i = 1;

        // loop all elements
        me.itemsTargetGrid.getStore().each( function( record )
            {
                // set the order
                articles.push( [ record.get( "id" ), i++ ] );
            }
        );

        // disable this window
        me.setLoading( true );



        // send ajax request to save the position
        Ext.Ajax.request(
            {
                // our backend url
                url: "{url controller=AtsdConfigurator action=saveArticlePositions}",
                // parameters
                params:
                {
                    articles: Ext.JSON.encode( articles )
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
                    me.itemsTargetGrid.getStore().reload();
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







    // create a row editing plugin editor
    getRowEditorPlugin: function()
    {
        // get this
        var me = this;

        // create the plugin
        me.rowEditorPlugin = Ext.create( "Ext.grid.plugin.RowEditing",
            {
                clicksToEdit: 2,
                errorSummary: false,
                pluginId: "rowEditing",
                listeners:
                {
                    edit: function( rowEdit, data )
                    {
                        me.fireEvent( "updateArticle", data.record );
                    }
                }
            }
        );

        // return it
        return me.rowEditorPlugin;
    },






    // get the columns
    getSourceGridColumns: function()
    {
        // get this
        var me = this;

        // return columns
        return me.sourceGridColumns;
    },







    // get the columns
    getTargetGridColumns: function()
    {
        // get this
        var me = this;

        // return them
        var columns =
            [
                {
                    header: "ID",
                    width: 60,
                    dataIndex: 'id'
                },
                {
                    header: "Artikel",
                    flex: 1,
                    dataIndex: 'articleNumber'
                },
                {
                    header: "Name",
                    flex: 3,
                    dataIndex: 'articleName'
                },

                {
                    header: "Auswahl",
                    dataIndex: "quantitySelect",
                    width: 60,
                    renderer: me.renderArticleQuantitySelect,
                    editor:
                        {
                            xtype:         "combobox",
                            valueField:    "id",
                            displayField:  "name",
                            mode:          "local",
                            allowBlank:    false,
                            triggerAction: "all",
                            editable:      false,
                            store:         Ext.create( "Ext.data.Store",
                                {
                                    fields: [ "id", "name" ],
                                    data:
                                        [
                                            { id: 1, name: "Ja" },
                                            { id: 0, name: "Nein" }
                                        ]
                                }
                            )
                        }
                },

                {
                    header: "Multiplikator",
                    dataIndex: "quantityMultiply",
                    width: 60,
                    renderer: me.renderArticleQuantityMultiply,
                    editor:
                        {
                            xtype:         "combobox",
                            valueField:    "id",
                            displayField:  "name",
                            mode:          "local",
                            allowBlank:    false,
                            triggerAction: "all",
                            editable:      false,
                            store:         Ext.create( "Ext.data.Store",
                                {
                                    fields: [ "id", "name" ],
                                    data:
                                        [
                                            { id: 1, name: "Ja" },
                                            { id: 0, name: "Nein" }
                                        ]
                                }
                            )
                        }
                },

                {
                    header: "Anzahl",
                    width: 60,
                    dataIndex: 'quantity',
                    renderer: me.renderArticleQuantity,
                    editor:
                    {
                        xtype: "numberfield",
                        minValue: 1,
                        maxValue: 99
                    }
                }
            ];

        // return columns
        return columns;
    },



    //
    renderArticleQuantitySelect: function( value, metaData, record )
    {
        return ( value == 1 ) ? "Ja" : "Nein";
    },



    //
    renderArticleQuantityMultiply: function( value, metaData, record )
    {
        return ( value == 1 ) ? "Ja" : "Nein";
    },










    // get the "add" and "remove" buttons
    getItemsButtons: function()
    {
        // get this
        var me = this;
        
        // create add button
        var button_add = Ext.create( "Ext.Button",
            {
                tooltip: me.snippets.buttonAdd,
                cls: Ext.baseCSSPrefix + 'form-itemselector-btn',
                iconCls: Ext.baseCSSPrefix + 'form-itemselector-' + "add",
                disabled: false,
                navBtn: true,
                margin: "4 0 0 0",
                listeners:
                    {
                        scope: me,
                        click: function()
                            {
                                me.fireEvent( me.eventNames.onItemsAdd );
                            }
                    }
            }
        );
        
        // create remve button
        var button_remove = Ext.create( "Ext.Button",
            {
                tooltip: me.snippets.buttonRemove,
                cls: Ext.baseCSSPrefix + 'form-itemselector-btn',
                iconCls: Ext.baseCSSPrefix + 'form-itemselector-' + "remove",
                disabled: false,
                navBtn: true,
                margin: "4 0 0 0",
                listeners:
                    {
                        scope: me,
                        click: function()
                            {
                                me.fireEvent( me.eventNames.onItemsRemove );
                            }
                    }
            }
        );        

        // return the container
        return Ext.create('Ext.container.Container',
            {
                margins: '0 4',
                items:  [ button_add, button_remove ],
                width: 22,
                layout:
                    {
                        type: 'vbox',
                        pack: 'center'
                    }
            }
        );
    },    
    
    
    



    
    // create and return the selection model
    getItemsSelectionModel: function()
    {
        // create the model
        var selModel = Ext.create( "Ext.selection.CheckboxModel",
            {
            }
        );
        
        // and return it
        return selModel;
    },
    
    
    
    


    // get bottom toolbar
    getBottomToolbar: function( store )
    {
        // get this
        var me = this;

        // create the toolbar
        var toolbar = Ext.create( "Ext.toolbar.Paging",
            {
                store: store,
                displayInfo: true,
                dock: "bottom"
            }
        );

        // return the toolbar
        return toolbar;
    },
    
    
    
    


    // get top toolbar
    getTopToolbar: function( grid )
    {
        // get this
        var me = this;

        // create search field
        var searchField = Ext.create('Ext.form.field.Text',
            {
                name: 'searchfield',
                dock: 'top',
                cls: 'searchfield',
                width: 270,
                emptyText: me.snippets.searchText,
                enableKeyEvents: true,
                checkChangeBuffer: 500,
                listeners:
                    {
                        change: function( field, value )
                            {
                                me.fireEvent( me.eventNames.onItemsSearch, value, grid);
                            }
                    }
            }
        );

        // return the toolbar
        return Ext.create('Ext.toolbar.Toolbar',
            {
                ui: 'shopware-ui',
                padding: '2 0',
                items: [ '->', searchField, ' ' ]
            }
        );
    },    
        
        
        
        
        
        
        
        
        
        
        
        
    // this function includes the ajax call if an item is transfered from
    // source to target or the other way around    
    onItemsMove: function(
        // the grid we remove an item (may be our source or target grid)
        sourceGrid,
        // the grid we move an item to (may be our source or target grid)
        targetGrid,
        // window we set to loading (if not null)
        loadingWindow,
        // ajax url
        ajaxUrl,
        // the value of our record id (e.g. "1")
        ajaxParameterRecordValue,
        // snippets
        snippetSuccess,
        snippetFailureTitle,
        snippetFailureText,
        // log our response?
        logResponse
    )
    {
        // get this
        var me = this;
        
        // get selected shops
        var selection = sourceGrid.selModel.getSelection();
        
        // did we select any shop?
        if ( selection.length == 0 )
            // nope
            return;
        
        // collect our shop ids
        var ids = [];
        
        // loop through all selected shops
        Ext.each( selection, function( item )
            {
                // add id
                ids.push( item.data.id );
            }
        );


        
        // do we have a window to set to loading?
        if ( loadingWindow != null )
            // set it to loading
            loadingWindow.setLoading( true );



        // send ajax request to add shop
        Ext.Ajax.request(
        {
            // our backend url
            url: ajaxUrl,
            // set timeout to 2min
            timeout: 120000,
            // parameters
            params:
                {
                    // our ids
                    foreignIds: Ext.JSON.encode( ids ),
                    // our record id
                    recordId: ajaxParameterRecordValue
                },
            // success function
            success: function( response )
            {
                // log the response?
                if ( logResponse === true )
                    // do it
                    console.log( response );
                
                // do we have a window to set to loading?
                if ( loadingWindow != null )
                    // cancel loading
                    loadingWindow.setLoading( false );
                    
                // decode our response
                var result = Ext.JSON.decode( response.responseText );
                
                // not successful?
                if ( result.success == false )
                {
                    // show error message
                    Shopware.Notification.createStickyGrowlMessage(
                        {
                            title: snippetFailureTitle,
                            text:  snippetFailureText + " " + result.error
                        }
                    );
                    
                    // and return
                    return;
                }
                
                // activate loading for grids
                sourceGrid.setLoading( true );
                targetGrid.setLoading( true );
                
                // reload source grid
                sourceGrid.getStore().load(
                    {
                        callback: function()
                            {
                                sourceGrid.setLoading( false );
                            }
                    }
                );
                
                // reload target grid
                targetGrid.getStore().load(
                    {
                        callback: function()
                            {
                                targetGrid.setLoading( false );
                            }
                    }
                );
  
                // define message
                var message = snippetSuccess;
                message = Ext.String.format( message, result.counter );
                
                // show success message
                Shopware.Notification.createGrowlMessage( "", message );
            },
            failure: function( response )
            {
                // log the response?
                if ( logResponse === true )
                    // do it
                    console.log( response );
                    
                // cancel loading
                loadingWindow.setLoading( false );
                
                // get error
                var rawData = response.getProxy().getReader().rawData;

                // show error message
                Shopware.Notification.createStickyGrowlMessage(
                    {
                        title: snippetFailureTitle,
                        text:  snippetFailureText + " " + rawData.error
                    }
                )
            }
        });
    }
            

    
    
    
    
    
});



