
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.fieldset.Details",
{
    // parent
    extend: 'Ext.form.Panel',

    // css
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-fieldset-details",

    // alias
    alias: "widget.atsdconfigurator-details-fieldset-details",
    itemId: "atsdconfigurator-details-fieldset-details",
    stateId: "atsdconfigurator-details-fieldset-details",

    // load translation component
    plugins: [
        {
            pluginId: 'translation',
            ptype: 'translation',
            translationType: "atsd-configurator.fieldset"
        }
    ],

    // css
    border: false,
    bodyPadding: 10,

    // ...
    autoScroll: true,

    // default label width
    labelWidth: 120,


	
	// the fieldset record
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

        // save button
        me.bbar = me.getBottomBar();
        
        // call the parent
        me.callParent( arguments );

        // load the fieldset
        me.loadRecord( me.record );
    },
    
    
    
    
    
    // register all events
    registerEvents: function()
    {
        // ...
        this.addEvents( "saveFieldset", "closeFieldset" );
    },






    //
    getBottomBar: function()
    {
        // get this
        var me = this;

        // create the bar
        return {
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'shopware-ui',
            items:
                [
                    "->",
                    {
                        text:'Abbrechen',
                        scope: me,
                        cls: 'secondary',
                        handler: function()
                        {
                            me.fireEvent( "closeFieldset", me );
                        }
                    },
                    {
                        text:'Speichern',
                        action:'saveItem',
                        cls:'primary',
                        handler: function()
                        {
                            me.fireEvent( "saveFieldset", me, me.record );
                        }
                    }
                ]
        };
    },





    // get alle form items
    getItems: function()
    {
        // get this
        var me = this;
        
        // create items
        return [
            me.getFormFieldsetDetails()
        ];
    },






    // ...
    getFormFieldsetDetails: function()
    {
		// get this
        var me = this;
        
        // create the form fieldset
        return Ext.create( "Ext.form.FieldSet",
            {
                collapsible: false,
                title: 'Details',
                layout: "column",
                defaults:
                {
                    columnWidth: .5,
                    flex: 1,
                    anchor: '100%'
                },
                items:
                    [
                        me.createFormField( "name", "Interner Name", false, "L", "Eine ausschließlich intern genutzte Beschreibung, die nicht vom Kunden sichtbar ist." ),
                        me.createFormField( "description", "Beschreibung", false, "R", "Eine Beschreibung, die im Konfigurator ausgegeben wird." )
                    ]
            }
        );
    },











    // ...
    createFormField: function( name, label, optional, position, helpText )
    {
        // get this
        var me = this;

        // ...
        var allowBlank = ( typeof optional !== 'undefined' )
            ? optional
            : true;

        // margin
        var margin = ( typeof position !== 'undefined' )
            ? ( ( position == "R" ) ? "0px 0px 0px 10px" : "0px 10px 0px 0px" )
            : "0px";

        // create the form field
        return Ext.create( "Ext.form.field.Text",
            {
                name:         name,
                fieldLabel:   label,
                margin:       margin,
                allowBlank:   allowBlank,
                labelWidth:   me.labelWidth,
                helpText:     helpText,
                translatable: ( name === "description" )
            }
        );
    }






});





