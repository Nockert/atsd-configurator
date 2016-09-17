
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.model.configurator.fieldset.Element",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [

            // data
            { name: "id",            type: "int",    useNull: true },
            { name: "fieldsetId",    type: "int" },
            { name: "name",          type: "string" },
            { name: "description",   type: "string" },
            { name: "mediaFile",     type: "string", defaultValue: "" },
            { name: "position",      type: "int" },
            { name: "mandatory",     type: "int",    defaultValue: 0 },
            { name: "multiple",      type: "int",    defaultValue: 0 },
            { name: "comment",       type: "string", defaultValue: "" },
            { name: "countArticles", type: "int" },
            { name: "templateId",    type: "int" },
            { name: "templateName",  type: "string" }

        ],

    // associations
    associations:
        [
        ],        
        
    // communication proxy
    proxy:
        {
            // type
            type: "ajax",

            // api functions
            api:
                {
                    // update
                    update:  '{url controller="AtsdConfigurator" action="updateElement"}',
                    create:  '{url controller="AtsdConfigurator" action="createElement"}',
                    destroy: '{url controller="AtsdConfigurator" action="deleteElement"}'
                },

            // reader
            reader:
                {
                    type: "json",
                    root: "data",
                    totalProperty: "total"
                }
        }
        
});



