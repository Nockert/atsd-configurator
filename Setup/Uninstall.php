<?php

/**
 * Aquatuning Software Development - Configurator - Setup
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Setup;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Model\ModelManager;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Doctrine\ORM\Tools\SchemaTool;
use AtsdConfigurator\Models;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Uninstall
{

    /**
     * Main bootstrap object.
     *
     * @var Plugin
     */

    protected $plugin;



    /**
     * ...
     *
     * @var UninstallContext
     */

    protected $context;



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
     * @var array
     */

    protected $models = array(
        Models\Configurator::class,
        Models\Configurator\Fieldset::class,
        Models\Configurator\Fieldset\Element::class,
        Models\Configurator\Fieldset\Element\Article::class,
        Models\Template::class,
        Models\Selection::class,
        Models\Selection\Article::class
    );



    /**
     * ...
     *
     * @param Plugin               $plugin
     * @param UninstallContext     $context
     * @param ModelManager         $modelManager
     * @param CrudService          $crudService
     */

    public function __construct( Plugin $plugin, UninstallContext $context, ModelManager $modelManager, CrudService $crudService )
    {
        // set params
        $this->plugin       = $plugin;
        $this->context      = $context;
        $this->modelManager = $modelManager;
        $this->crudService  = $crudService;
    }



    /**
     * ...
     *
     * @return void
     */

    public function uninstall()
    {
        // ...
        $this->uninstallAttributes();
        $this->removeDatabaseTables();
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

        // ...
        $classes = array_map(
            function( $model ) use ( $em ) {
                return $em->getClassMetadata( $model );
            },
            $this->models
        );

        // remove them
        $tool->dropSchema( $classes );
    }

}
