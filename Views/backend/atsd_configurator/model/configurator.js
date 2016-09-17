
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.model.Configurator",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [

            // data
            { name: "id",            type: "int", useNull: true },
            { name: "name",          type: "string" },
            { name: "rebate",        type: "int" },
            { name: "articleNumber", type: "string", useNull: true },
            { name: "articleName",   type: "string" }

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
                    update: '{url controller="AtsdConfigurator" action="updateConfigurator"}',
                    create: '{url controller="AtsdConfigurator" action="createConfigurator"}',
                    destroy: '{url controller="AtsdConfigurator" action="deleteConfigurator"}'
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



