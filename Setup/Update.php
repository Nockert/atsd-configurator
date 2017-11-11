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
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Enlight_Components_Db_Adapter_Pdo_Mysql as Db;
use Exception;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Update
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
     * @var CrudService
     */

    protected $crudService;



    /**
     * ...
     *
     * @param Plugin           $plugin
     * @param InstallContext   $context
     * @param ModelManager     $modelManager
     * @param Db               $db
     * @param CrudService      $crudService
     */

    public function __construct( Plugin $plugin, InstallContext $context, ModelManager $modelManager, Db $db, CrudService $crudService )
    {
        // set params
        $this->plugin       = $plugin;
        $this->context      = $context;
        $this->modelManager = $modelManager;
        $this->db           = $db;
        $this->crudService  = $crudService;
    }



    /**
	 * ...
	 *
	 * @return void
	 */

	public function install()
	{
		// install updates
		$this->update( "0.0.0" );
	}



    /**
     * ...
     *
     * @param string   $version
     *
     * @return void
     */

    public function update( $version )
    {
        // check current installed version
        switch ( $version )
        {
            case "0.0.0":
            case "1.0.0":
            case "1.0.1":
            case "1.0.2":
            case "1.0.3":
            case "1.0.4":
            case "1.1.0":
            case "1.1.1":
            case "1.1.2":
            case "1.1.3":
            case "1.1.4":
            case "1.1.5":
            case "1.1.6":
            case "1.1.7":
            case "1.1.8":
            case "1.1.9":
            case "1.1.10":
            case "1.1.11":
            case "1.1.12":
            case "1.1.13":
            case "1.1.14":
            case "1.1.15":
            case "1.1.16":
            case "1.2.0":
            case "1.2.1":
            case "1.2.2":
            case "1.2.3":
                $this->updateSql( "1.2.4-a" );
            case "1.2.4":
            case "1.2.5":
                $this->updateSql( "1.3.0-a" );
                $this->updateSql( "1.3.0-b" );
                $this->updateSql( "1.3.0-c" );
            case "1.3.0":
            case "1.3.1":
            case "1.3.2":
                $this->updateVersion140();
            case "1.4.0":
            case "1.4.1":
            case "1.4.2":
            case "1.4.3":
            case "1.4.4":
            case "1.4.5":
            case "1.4.6":
            case "1.4.7":
            case "1.4.8":
            case "1.4.9":
            case "1.4.10":
            case "1.4.11":
                $this->updateSql( "1.5.0-a" );
                $this->updateSql( "1.5.0-b" );
            case "1.5.0":
            case "1.5.1":
            case "1.5.2":
            case "1.5.3":
            case "1.5.4":
            case "1.5.5":
                $this->updateSql( "1.5.6-a" );
            case "1.5.6":
            case "1.5.7":
                $this->updateVersion200();
        }
    }



    /**
     * ...
     *
     * @return void
     */

    private function updateVersion140()
    {
        // ...
        $this->crudService->update(
            "s_order_basket_attributes",
            "atsd_configurator_selection_id",
            "integer",
            array(
                'displayInBackend' => false,
                'custom'           => false
            )
        );

        // ...
        $this->crudService->update(
            "s_order_details_attributes",
            "atsd_configurator_selection_id",
            "integer",
            array(
                'displayInBackend' => false,
                'custom'           => false
            )
        );

        // ...
        $this->crudService->update(
            "s_order_details_attributes",
            "atsd_configurator_selection_master",
            "boolean",
            array(
                'displayInBackend' => false,
                'custom'           => false
            )
        );

        // save our attributes
        $this->modelManager->generateAttributeModels( array( "s_order_basket_attributes", "s_order_details_attributes" ) );
    }



    /**
     * ...
     *
     * @return void
     */

    private function updateVersion200()
    {
        // ...
        $query = "
            DELETE FROM s_core_subscribes
            WHERE listener LIKE 'Shopware_Plugins_Frontend_AtsdConfigurator_Bootstrap::%'
        ";
        $this->db->query( $query );
    }



    /**
     * ...
     *
     * @param string   $version
     *
     * @return void
     */

    private function updateSql( $version )
    {
        // get the sql query for this update
        $sql = @file_get_contents( $this->plugin->getPath() . "/Update/update-" . $version . ".sql" );

        // execute the query
        try { $this->db->exec( $sql ); }
        // catch any db exception and ignore it
        catch ( Exception $exception ) {}
    }

}
