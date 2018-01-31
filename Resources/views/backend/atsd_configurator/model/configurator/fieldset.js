
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.model.configurator.Fieldset",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [

            // data
            { name: "id",             type: "int", useNull: true },
            { name: "configuratorId", type: "int" },
            { name: "name",           type: "string" },
            { name: "description",    type: "string" },
            { name: "mediaFile",      type: "string" },
            { name: "position",       type: "int" }

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
                    update:  '{url controller="AtsdConfigurator" action="updateFieldset"}',
                    create:  '{url controller="AtsdConfigurator" action="createFieldset"}',
                    destroy: '{url controller="AtsdConfigurator" action="deleteFieldset"}'
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



