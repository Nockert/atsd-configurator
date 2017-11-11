
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.list.Configurators",
{
    // parent
    extend: "Ext.grid.Panel",

    // no border
    border: false,
    
    // css 
    cls: Ext.baseCSSPrefix + "atsdconfigurator-list-configurators",

    // alias
    alias: "widget.atsdconfigurator-list-configurators",
	itemId: "atsdconfigurator-list-configurators",
	
	// columns
	columns: null,

    // toolbar input
    addName: null,

	
    
    // store
    store: undefined,



    // component init
    initComponent: function()
    {
        // get this
        var me = this;

        // set the store
        me.store = Ext.create( "Shopware.apps.AtsdConfigurator.store.Configurators" );
        me.store.load();
        
        // register all events
        me.registerEvents();
        
        // get the selection model
        me.selModel = me.getSelectionModel();
        
        // get the columns
        me.columns = me.getColumns();

        // get top toolbar
        me.tbar = me.getTopToolbar();

        // get bottom toolbar
        me.bbar = me.getBottomToolbar();

        // create an editor and save it as plugin
        me.plugins = [ me.getRowEditorPlugin() ];

        // call the parent
        me.callParent( arguments );
    },
    
    
	
	
	
    
    // register all events
    registerEvents: function()
    {
		// add events
        this.addEvents( "searchConfigurator", "editConfigurator", "deleteConfigurator", "addConfigurator", "updateConfigurator" );
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
                        me.fireEvent( "updateConfigurator", data.record );
                    }
                }
            }
        );

        // return it
        return me.rowEditorPlugin;
    },




    // create and return the selection model
    getSelectionModel: function()
    {
        // get this
        var me = this;

        // create the model
        var selModel = Ext.create( "Ext.selection.CheckboxModel",
            {
                // register listeners
                listeners:
                    {
                        // on change
                        selectionchange: function( view, selected )
                            {
                            }
                    }
            }
        );
        
        // and return it
        return selModel;
    },






    //
    getTopToolbar: function()
    {
        // get this
        var me = this;

        // create the search field
        var searchField = Ext.create('Ext.form.field.Text',
            {
                name: 'searchfield',
                dock: 'top',
                cls: 'searchfield',
                width: 150,
                emptyText: "Suche...",
                enableKeyEvents: true,
                checkChangeBuffer: 500,
                listeners: {
                    change: function( field, value )
                    {

                        me.fireEvent( "searchConfigurator", value, me );
                    }
                }
            }
        );

        // create the button
        var addButton = Ext.create('Ext.button.Button',
            {
                iconCls:'sprite-plus-circle',
                text: "Neuen Konfigurator anlegen",
                //margin: "0 4 4 6",
                handler: function()
                {
                    me.fireEvent( "addConfigurator", me );
                }
            }
        );

        // flag
        me.addName = Ext.create( "Ext.form.field.Text",
            {
                name: 'addName',
                margin: "0 4 4 6",
                emptyText: "Name eingeben...",
                width: 200
            }
        );

        // the complete toolbar
        var toolbar = Ext.create( "Ext.toolbar.Toolbar",
            {
                ui: 'shopware-ui',
                padding: '2 0',
                items:
                    [
                        me.addName,
                        addButton,
                        "->",
                        searchField,
                        " "
                    ]
            }
        );

        // and return it
        return toolbar;
    },







    //
    getBottomToolbar: function()
    {
        // get this
        var me = this;

        // create the toolbar
        var toolbar = Ext.create( "Ext.toolbar.Paging",
            {
                store: me.store,
                displayInfo: true
            }
        );

        // and return it
        return toolbar;
    },






    //
    getColumns: function()
	{
        //
        var me = this;

        //
		var columns =
		    [
                {
                    header: "ID",
                    dataIndex: "id",
                    sortable: true,
                    width: 60
                },
                {
                    header: "Name",
                    dataIndex: "name",
                    sortable: true,
                    editor:
                    {
                        xtype: "textfield"
                    },
                    flex: 3
                },
                {
                    header: "Rabatt",
                    dataIndex: "rebate",
                    sortable: true,
                    editor:
                    {
                        xtype: "numberfield",
                        minValue: 0,
                        maxValue: 99
                    },
                    flex: 1,
                    renderer: me.renderRebate
                },
                {
                    header: "Artikel",
                    dataIndex: "articleNumber",
                    sortable: true,
                    renderer: me.renderArticleNumber,
                    editor:
                    {
                        xtype: "textfield",
                        emptyText: "Artikelnummer eingeben..."
                    },
                    flex: 2
                },
                {
                    header: "Kostenlos",
                    dataIndex: "chargeArticle",
                    sortable: true,
                    width: 120,
                    renderer: me.renderChargeArticle,
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
                                            { id: 1, name: "Nein" },
                                            { id: 0, name: "Ja" }
                                        ]
                                }
                            )
                        }
                },
                {
                    header: "",
                    xtype: "actioncolumn",
                    width: 80,
                    items:
                        [
                            {
                                iconCls : 'sprite-minus-circle-frame',
                                action  : 'delete',
                                cls     : 'deleteButton',
                                tooltip : 'Konfigurator löschen',
                                handler: function(grid, rowIndex, colIndex, button)
                                {
                                    me.fireEvent( "deleteConfigurator", me, grid, rowIndex, colIndex, button );
                                }

                            },
                            {
                                iconCls : 'sprite-pencil',
                                tooltip : 'Details einsehen',
                                handler: function( grid, rowIndex, colIndex, button )
                                {
                                    me.fireEvent( "editConfigurator", me, grid, rowIndex, colIndex, button );
                                }
                            },
                            {
                                iconCls : 'sprite-document-copy',
                                tooltip : 'Konfigurator kopieren',
                                handler: function( grid, rowIndex, colIndex, button )
                                {
                                    me.fireEvent( "copyConfigurator", me, grid, rowIndex, colIndex, button );
                                }
                            }

                        ]
                }
            ];

        //
		return columns;
    },



    //
    renderArticleNumber: function( value, metaData, record )
    {
        // no article number set
        if ( value == "" )
        {
            return '<span style="color: #cccccc">Kein Artikel zugeordnet</span>';
        }

        // we have a number but no name?!
        // -> wont ever happen since we only save the article id
        /*
        if ( record.get( "articleName" ) == "" )
        {
            return '<span style="color: #e74c3c">Ungültige Artikelnummer</span>';
        }
        */

        return record.get( "articleName" );
    },


    //
    renderRebate: function( value, metaData, record )
    {
        return value + "%";
    },



    //
    renderChargeArticle: function( value, metaData, record )
    {
        return ( value == 0 ) ? "Ja" : "Nein";
    }





});


