<?php

/**
 * Aquatuning Software Development - Configurator - Setup
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bootstrap;

use Doctrine\ORM\Tools\SchemaTool;
use Shopware_Components_Plugin_Bootstrap as Bootstrap;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Uninstall
{

    /**
     * Main bootstrap object.
     *
     * @var Bootstrap
     */

    protected $bootstrap;


    /**
     * ...
     *
     * @var ModelManager
     */

    protected $modelManager;


    /**
     * ...
     *
     * @var CrudService
     */

    protected $crudService;



    /**
     * ...
     *
     * @param Bootstrap      $bootstrap
     * @param ModelManager   $modelManager
     * @param CrudService    $crudService
     */

    public function __construct( Bootstrap $bootstrap, ModelManager $modelManager, CrudService $crudService )
    {
        // set params
        $this->bootstrap    = $bootstrap;
        $this->modelManager = $modelManager;
        $this->crudService  = $crudService;
    }





	/**
	 * ...
	 *
	 * @return boolean
	 */

	public function uninstall()
	{
	    // remove updates
        $this->removeUpdate130();

	    // ...
        $this->removeDatabaseTables();
        $this->uninstallAttributes();

		// done
		return true;
	}



    /**
     * ...
     *
     * @return void
     */

    private function removeUpdate130()
    {
        try {
            // get entity manager
            $em = $this->modelManager;

            // get our schema tool
            $tool = new SchemaTool( $em );

            // list of our custom models
            $classes = array(
                $em->getClassMetadata( 'Shopware\CustomModels\AtsdConfigurator\Selection\Article' )
            );

            // remove them
            $tool->dropSchema( $classes );
        }
        catch ( \Exception $exception ) {}
    }




    /**
     * Removes the plugin database tables.
     *
     * @return void
     */

    private function removeDatabaseTables()
    {
        // get entity manager
        $em = $this->modelManager;

        // get our schema tool
        $tool = new SchemaTool( $em );

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
    }








    /**
     * ...
     *
     * @return void
     */

    private function uninstallAttributes()
    {
        // ...
        $this->crudService->delete( "s_order_basket_attributes", "atsd_configurator_selection_id" );
        $this->crudService->delete( "s_order_details_attributes", "atsd_configurator_selection_id" );
        $this->crudService->delete( "s_order_details_attributes", "atsd_configurator_selection_master" );

        // save our attributes
        $this->modelManager->generateAttributeModels( array( "s_order_basket_attributes", "s_order_details_attributes" ) );
    }





}