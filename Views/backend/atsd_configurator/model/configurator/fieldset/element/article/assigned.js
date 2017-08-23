
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.model.configurator.fieldset.element.article.Assigned",
{
    // parent
    extend: 'Ext.data.Model',

    // model fields
    fields:
        [

            // data
            { name: "id",               type: "int" },
            { name: "articleNumber",    type: "string" },
            { name: "articleName",      type: "string" },
            { name: "elementId",        type: "int" },
            { name: "quantity",         type: "int" },
            { name: "quantitySelect",   type: "int" },
            { name: "quantityMultiply", type: "int" },
            { name: "surcharge",        type: "int" }

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
                    update: '{url controller="AtsdConfigurator" action="updateArticle"}'
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



