<?php

/**
 * Aquatuning Software Development - Configurator - Setup
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bootstrap;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Uninstall
{

	/**
	 * Main bootstrap object.
	 *
	 * @var \Shopware_Components_Plugin_Bootstrap
	 */

	protected $bootstrap;



	/**
	 * ...
	 *
	 * @param \Shopware_Components_Plugin_Bootstrap   $bootstrap
	 *
	 * @return \Shopware\AtsdConfigurator\Bootstrap\Uninstall
	 */

	public function __construct( \Shopware_Components_Plugin_Bootstrap $bootstrap )
	{
		// set params
		$this->bootstrap = $bootstrap;
	}





	/**
	 * ...
	 *
	 * @return boolean
	 */

	public function uninstall()
	{
        $this->removeDatabaseTables();
        $this->removeDatabaseAttributes();

		// done
		return true;
	}




    /**
     * Removes the plugin database tables.
     *
     * @return void
     */

    public function removeDatabaseTables()
    {
        // get entity manager
        $em = Shopware()->Models();

        // get our schema tool
        $tool = new \Doctrine\ORM\Tools\SchemaTool( $em );

        // list of our custom models
        $classes = array(
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Configurator' ),
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset' ),
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element' ),
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Template' ),
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Configurator\Fieldset\Element\Article' ),
            $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Selection' )
        );

        // remove them
        $tool->dropSchema( $classes );

        // done
        return;
    }



    /**
     * Removes the additionally set attributes for the selection.
     *
     * @return void
     *
     */

    private function removeDatabaseAttributes()
    {
        // order basket
        Shopware()->Models()->removeAttribute( 's_order_basket_attributes', 'atsd', 'configurator_selection_id' );

        // save our attributes
        Shopware()->Models()->generateAttributeModels( array(
            's_order_basket_attributes'
        ));

        // order details
        Shopware()->Models()->removeAttribute( 's_order_details_attributes', 'atsd', 'configurator_selection_id' );
        Shopware()->Models()->removeAttribute( 's_order_details_attributes', 'atsd', 'configurator_selection_master' );

        // and generate the model
        Shopware()->Models()->generateAttributeModels(array(
            's_order_details_attributes',
        ));

        // done
        return;
    }




}