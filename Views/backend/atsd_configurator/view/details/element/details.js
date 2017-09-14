
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.view.details.element.Details",
{
    // parent
    extend: 'Ext.form.Panel',

    // css
    cls: Ext.baseCSSPrefix + "atsdconfigurator-details-element-details",

    // alias
    alias: "widget.atsdconfigurator-details-element-details",
    itemId: "atsdconfigurator-details-element-details",
    stateId: "atsdconfigurator-details-element-details",

    // load translation component
    plugins: [
        {
            pluginId: 'translation',
            ptype: 'translation',
            translationType: "atsd-configurator.element"
        }
    ],

    // css
    border: false,
    bodyPadding: 10,

    // ...
    autoScroll: true,

    // default label width
    labelWidth: 150,


	
	// the element record
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

        // load the element
        me.loadRecord( me.record );
    },
    
    
    
    
    
    // register all events
    registerEvents: function()
    {
        // ...
        this.addEvents( "saveElement", "closeElement" );
    },






    //
    getBottomBar: function()
    {
        // get this
        var me = this;

        // create the bar
        var bar =
        {
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
                            me.fireEvent( "closeElement", me );
                        }
                    },
                    {
                        text:'Speichern',
                        action:'saveItem',
                        cls:'primary',
                        handler: function()
                        {
                            me.fireEvent( "saveElement", me, me.record );
                        }
                    }
                ]
        };

        // return it
        return bar;
    },





    // get alle form items
    getItems: function()
    {
        // get this
        var me = this;
        
        // create items
        var items =
            [
                me.getFormFieldsetDetails(),
                me.getFormFieldsetArticleNotice()
            ];

        // return the items
        return items;
    },






    // ...
    getFormFieldsetArticleNotice: function()
    {
        // create the form fieldset
        return Ext.create( "Ext.form.FieldSet",
            {
                collapsible: false,
                title: 'Hinweise zu Artikeln',
                layout: "column",
                defaults:
                    {
                        columnWidth: 1,
                        flex: 1,
                        anchor: '100%'
                    },
                items:
                    [
                        Ext.create( "Ext.container.Container",
                            {
                                html:
                                '<span style="font-weight: bold;">Aufschlag:</span> ein Aufschlag ersetzt den Preis der Komponente durch einen prozentualen Wert, der sämtliche Komponenten des Konfigurators, die die Option "Aufschlag berechnen" aktiviert haben, um den angegebenen Prozentwert teurer macht. Der Preis des Haupt-Artikels ist von prozentualen Aufschlägen stets unberührt.<br />' +
                                '<span style="font-weight: bold;">Auswahl:</span> darf der Kunde die Stückzahl des Artikels selber wählen?<br />' +
                                '<span style="font-weight: bold;">Multiplikator:</span> hat der Kunde eine freie Auswahl der Stückzahl oder muss es ein Vielfaches der definierten Anzahl sein?<br />' +
                                '<span style="font-weight: bold;">Anzahl:</span> die Stückzahl des Artikels.',
                                style: ''
                            }
                        )
                    ]
            }
        );
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
                        me.createFormField( "description", "Beschreibung", false, "R", "Eine Beschreibung, die im Konfigurator ausgegeben wird." ),
                        me.createFormCombobox( "mandatory", "Pflichtfeld", "L", undefined, "Muss mindestens eine Komponente dieses Elements gewählt werden?" ),
                        me.createFormCombobox( "multiple", "Mehrfach Auswahl", "R", undefined, "Darf der Kunde eine Komponente oder mehrere Komponenten wählen?" ),
                        me.createFormCombobox( "dependency", "Abhängigkeit aktivieren", "L", undefined, "Wenn diese Option aktiviert ist, dann gilt die oberste / erste Komponente als Vater und alle anderen Komponenten als Kinder. Die Kinder können nur ausgewählt werden, wenn der Vater ausgewählt ist. Diese Option ist nur möglich bei deaktivertem Pflichtfeld und aktivierter Mehrfach Auswahl. Sollte der Vater nicht verfügbar sein oder sollte es keine gültigen Kinder geben, dann wird das komplette Element deaktiviert." ),
                        me.createFormCombobox( "surcharge", "Aufschlag berechnen", "R", undefined, "Sollen Aufschläge für alle Komponenten dieses Elements berechnet werden? Nur wenn diese Option aktiviert ist, werden alle Komponenten dieses Element um ausgewählte Aufschläge teurer." ),
                        me.createFormCombobox( "templateId", "Template", "L", Ext.create( "Shopware.apps.AtsdConfigurator.store.Templates").load(), "Soll die Auswahl der Komponenten als Liste oder als Slider dargestellt werden?" ),
                        Ext.create( 'Shopware.MediaManager.MediaSelection',
                            {
                                name: 'mediaFile',
                                fieldLabel: "Grafik",
                                multiSelect: false,
                                anchor: "100%",
                                validTypes: [ 'gif', 'png', 'jpeg', 'jpg' ],
                                labelWidth: me.labelWidth,
                                margin: "0px 0px 0px 10px"
                            }
                        )
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
    },











    // ...
    createFormCombobox: function( name, label, position, store, helpText )
    {
        // get this
        var me = this;

        // margin
        var margin = ( typeof position !== 'undefined' )
            ? ( ( position == "R" ) ? "0px 0px 0px 10px" : "0px 10px 0px 0px" )
            : "0px";

        // store set?
        if ( typeof store === 'undefined' )
            // set yes/no store
            store = Ext.create( "Ext.data.Store",
                {
                    fields: [ "id", "name" ],
                    data:   [ { id: 1, name: "Ja" }, { id: 0, name: "Nein" } ]
                }
            );

        // create the form field
        return Ext.create( "Ext.form.field.ComboBox",
            {
                labelWidth:    me.labelWidth,
                name:          name,
                fieldLabel:    label,
                margin:        margin,
                valueField:    "id",
                displayField:  "name",
                mode:          "local",
                triggerAction: "all",
                editable:      false,
                store:         store,
                helpText:      helpText
            }
        );
    }








});





