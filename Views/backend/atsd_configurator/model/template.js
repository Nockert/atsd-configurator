
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.model.Template",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [

            // data
            { name: "id",   type: "int" },
            { name: "name", type: "string" },
            { name: "key",  type: "string" }

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



