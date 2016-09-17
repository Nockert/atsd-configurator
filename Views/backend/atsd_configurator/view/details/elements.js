
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.Elements",
{
    // parent
    extend: "Ext.grid.Panel",

    // css
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-elements",

    // alias
    alias: "widget.atsdconfigurator-details-elements",
    itemId: "atsdconfigurator-details-elements",
    stateId: "atsdconfigurator-details-elements",

    // no border
    border: false,

	// columns
	columns: null,



    // the configurator record
    record: null,

    // the selected fieldset record
    fieldsetRecord: null,

    // store
    store: null,

    // input field for toolbar
    addName: undefined,
    addDescription: undefined,



    // component init
    initComponent: function()
    {
        // get this
        var me = this;

        // register all events
        me.registerEvents();

        // get the selection model
        me.selModel = me.getSelectionModel();

        // get the columns
        me.columns = me.getColumns();

        // get bottom toolbar
        me.bbar = me.getBottomToolbar();

        // get top toolbar
        me.tbar = me.getTopToolbar();

        // create an editor and save it as plugin
        me.plugins = [ me.getRowEditorPlugin() ];

        // call the parent
        me.callParent( arguments );
    },






    // register all events
    registerEvents: function()
    {
		// add events
        this.addEvents( "addElement", "deleteElement", "editElement", "updateElement" );
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
                        me.fireEvent( "updateElement", data.record );
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

        //
        me.addName = Ext.create( "Ext.form.TextField",
            {
                name: 'addName',
                width: 150,
                margin: "0 0 0 4",
                emptyText: "Name angeben..."
            }
        );

        // add
        me.addDescription = Ext.create( "Ext.form.TextField",
            {
                name: 'addDescription',
                width: 150,
                margin: "0 0 0 4",
                emptyText: "Beschreibung angeben..."
            }
        );

        // add element
        var button = Ext.create( "Ext.button.Button",
            {
                iconCls:'sprite-plus-circle',
                text: "Element hinzufügen",
                margin: "0 4 4 4",
                handler: function()
                {
                    me.fireEvent( "addElement", me );
                }
            }
        );

        // create the toolbar
        var toolbar = Ext.create( "Ext.toolbar.Toolbar",
            {
                ui: 'shopware-ui',
                padding: '2 0',
                items:
                    [
                        me.addName,
                        me.addDescription,
                        button
                    ]
            }
        );

        // return it
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
                    sortable: false,
                    width: 60
                },
                {
                    header: "Interner Name",
                    dataIndex: "name",
                    sortable: false,
                    flex: 1,
                    editor:
                    {
                        xtype: "textfield"
                    }
                },
                {
                    header: "Beschreibung",
                    dataIndex: "description",
                    sortable: false,
                    flex: 1,
                    editor:
                    {
                        xtype: "textfield"
                    }
                },
                {
                    header: "Artikel",
                    dataIndex: "countArticles",
                    sortable: false,
                    width: 60
                },
                {
                    header: "",
                    xtype: "actioncolumn",
                    width: 55,
                    items:
                        [
                            {
                                iconCls : 'sprite-minus-circle-frame',
                                action  : 'delete',
                                cls     : 'deleteButton',
                                tooltip : 'Element löschen',
                                handler: function(grid, rowIndex, colIndex, button)
                                {
                                    me.fireEvent( "deleteElement", me, grid, rowIndex, colIndex, button );
                                }

                            },
                            {
                                iconCls : 'sprite-pencil',
                                tooltip : 'Element editieren',
                                handler: function( grid, rowIndex, colIndex, button )
                                {
                                    me.fireEvent( "editElement", me, grid, rowIndex, colIndex, button );
                                }
                            }
                        ]
                }
            ];

        //
        return columns;
    }





});


