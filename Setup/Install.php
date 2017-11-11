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
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Model\ModelManager;
use Doctrine\ORM\Tools\SchemaTool;
use AtsdConfigurator\Models;
use Enlight_Components_Db_Adapter_Pdo_Mysql as Db;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Install
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
     * @var InstallContext
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
     * @var Db
     */

    protected $db;



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
        Models\Selection::class
    );



    /**
     * ...
     *
     * @param Plugin           $plugin
     * @param InstallContext   $context
     * @param ModelManager     $modelManager
     * @param Db               $db
     */

    public function __construct( Plugin $plugin, InstallContext $context, ModelManager $modelManager, Db $db )
    {
        // set params
        $this->plugin       = $plugin;
        $this->context      = $context;
        $this->modelManager = $modelManager;
        $this->db           = $db;
    }



    /**
     * ...
     *
     * @return void
     */

    public function install()
    {
        // ...
        $this->createDatabase();
        $this->createMainDatabaseRecords();
    }



    /**
     * Creates the necessary database tables for the plugin.
     *
     * @return void
     */

    private function createDatabase()
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
        $tool->createSchema( $classes );
    }



    /**
     * Insert mandatory data into the database for the plugin.
     *
     * @return void
     */

    private function createMainDatabaseRecords()
    {
        // get the .sql file
        $sql = (string) @file_get_contents( $this->plugin->getPath() . "/Setup/install.sql" );

        // insert it
        $this->db->exec( $sql );
    }

}
