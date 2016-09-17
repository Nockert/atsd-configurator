
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.Fieldsets",
{
    // parent
    extend: "Ext.grid.Panel",

    // css
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-fieldsets",

    // alias
    alias: "widget.atsdconfigurator-details-fieldsets",
    itemId: "atsdconfigurator-details-fieldsets",
    stateId: "atsdconfigurator-details-fieldsets",

    // no border
    border: false,
	
	// columns
	columns: null,

    // plugin
    rowEditorPlugin: undefined,
	
	

    // the configurator record
    record: null,

    // currently selected fieldset (if selected) - set via controller
    selectedFieldset: null,

    // store
    store: null,

    // add stuff
    addName: undefined,
    addDescription: undefined,
    addMediaFile: undefined,



    // component init
    initComponent: function()
    {
        // get this
        var me = this;
        
        // register all events
        me.registerEvents();
        
        // get the selection model
        me.selModel = me.getSelectionModel();

        // get bottom toolbar
        me.bbar = me.getBottomToolbar();

        // get top toolbar
        me.tbar = me.getTopToolbar();

        // get the columns
        me.columns = me.getColumns();

        // create an editor and save it as plugin
        me.plugins = [ me.getRowEditorPlugin() ];

        // call the parent
        me.callParent( arguments );
    },
    
    
	
	
	
    
    // register all events
    registerEvents: function()
    {
		// add events
        this.addEvents( "addFieldset", "selectFieldset", "deleteFieldset", "updateFieldset" );
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
                        me.fireEvent( "updateFieldset", data.record );
                    }
                }
            }
        );

        // return it
        return me.rowEditorPlugin;
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
    getTopToolbar: function()
    {
        // get this
        var me = this;

        //
        me.addName = Ext.create( "Ext.form.TextField",
            {
                name: 'addName',
                width: 100,
                margin: "0 0 0 4",
                emptyText: "Name..."
            }
        );

        // add
        me.addDescription = Ext.create( "Ext.form.TextField",
            {
                name: 'addDescription',
                width: 100,
                margin: "0 0 0 4",
                emptyText: "Beschreibung..."
            }
        );

        // image
        me.addMediaFile = Ext.create( 'Shopware.MediaManager.MediaSelection',
            {
                buttonText: 'Wählen',
                name: 'mediaFile',
                multiSelect: false,
                margin: "0 0 0 4",
                width: 180,
                validTypes: [ 'gif', 'png', 'jpeg', 'jpg' ],
                emptyText: 'Grafik...'
            }
        );

        // add element
        var button = Ext.create( "Ext.button.Button",
            {
                iconCls:'sprite-plus-circle',
                text: "Gruppe hinzufügen",
                margin: "0 4 4 4",
                handler: function()
                {
                    me.fireEvent( "addFieldset", me );
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
                        me.addMediaFile,
                        button
                    ]
            }
        );

        // return it
        return toolbar;
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
                                me.fireEvent( "selectFieldset", me, view, selected );
                            }
                    }
            }
        );
        
        // and return it
        return selModel;
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
                    header: "Grafik",
                    dataIndex: "mediaFile",
                    sortable: false,
                    flex: 1,
                    editor:
                    {
                        xtype: "textfield"
                    }
                },
                {
                    header: "",
                    xtype: "actioncolumn",
                    // width: 55,
                    width: 30,
                    items:
                        [
                            {
                                iconCls : 'sprite-minus-circle-frame',
                                action  : 'delete',
                                cls     : 'deleteButton',
                                tooltip : 'Gruppe löschen',
                                handler: function(grid, rowIndex, colIndex, button)
                                {
                                    me.fireEvent( "deleteFieldset", me, grid, rowIndex, colIndex, button );
                                }

                            }
                            /*
                            {
                                iconCls : 'sprite-pencil',
                                tooltip : 'Gruppe editieren',
                                handler: function( grid, rowIndex, colIndex, button )
                                {
                                    // me.rowEditorPlugin.startEdit( record,0 );
                                }
                            }
                            */
                        ]
                }
            ];

        //
		return columns;
    }






});


