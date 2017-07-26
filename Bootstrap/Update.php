<?php

/**
 * Aquatuning Software Development - Configurator - Setup
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bootstrap;

use Shopware_Components_Plugin_Bootstrap as Bootstrap;
use Exception;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;



/**
 * Aquatuning Software Development - Configurator - Setup
 */

class Update
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

	public function install()
	{
        // install all updates
        return $this->update( "0.0.0" );
	}



	/**
	 * ...
	 *
	 * @param string   $version
	 *
	 * @return boolean
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
                $this->updateVersion115();
            case "1.1.5":
            case "1.1.6":
            case "1.1.7":
            case "1.1.8":
            case "1.1.9":
            case "1.1.10":
            case "1.1.11":
            case "1.1.12":
                $this->updateVersion1113();
            case "1.1.13":
            case "1.1.14":
                $this->updateVersion1115();
            case "1.1.15":
                $this->updateVersion1116();
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
                $this->updateVersion140();
            case "1.4.0":
            case "1.4.1":
            case "1.4.2":
            case "1.4.3":
            case "1.4.4":
            case "1.4.5":
            case "1.4.6":
                $this->updateVersion147();
		}

		// done
		return true;
	}






    /**
     * Creates the configuration form for the plugin
     *
     * @return void
     */

    private function updateVersion115()
    {
        // returns this plugin form
        $form = $this->bootstrap->Form();

        // and set the element
        $form->setElement( "boolean", "allowArticlesWithoutCategory",
            array(
                'label'       => "Artikel ohne Kategorie erlauben",
                'description' => "Sollen Artikel, die keiner Kategorie zugeordnet sind, im Konfigurator angezeigt werden?",
                'value'       => true
            )
        );
    }



    /**
     * ...
     *
     * @return void
     */

    private function updateVersion1113()
    {
        // create the form
        $form = $this->bootstrap->Form();

        // ...
        $form->setElement( "boolean", "shopStatus",
            array(
                'label'       => "Shop aktivieren",
                'description' => "Soll das Plugin für dieses Shop freigeschaltet werden? Betrifft z.B. Änderungen am Menü im Kundenkonto.",
                'value'       => true,
                'scope'       => \Shopware\Models\Config\Element::SCOPE_SHOP
            )
        );
    }






    /**
     * Updates the plugin to 1.1.5
     *
     * @return void
     */

    private function updateVersion1115()
    {
        // create the form
        $form = $this->bootstrap->Form();

        // and set the element
        $form->setElement( "text", "articleInfoAttribute",
            array(
                'label'       => "Artikel Attribut",
                'description' => "Sie können weitere Artikel Informationen in den Attributen speichern, so dass diese in den Details jeder Komponente in der Listen-Ansicht ausgegeben werden. Beispiel: attr1, attr2, attr20 etc",
                'value'       => "attr20",
                'required'    => false
            )
        );
    }





    /**
     * Updates the plugin to 1.1.6
     *
     * @return void
     */

    private function updateVersion1116()
    {
        // create the form
        $form = $this->bootstrap->Form();

        // ...
        $form->setElement( "boolean", "articleLinkStatus",
            array(
                'label'       => "Artikel verlinken",
                'description' => "Sollen die angezeigten Komponenten mit einem Link auf den jeweiligen Artikel versehen werden?",
                'value'       => true,
                'required'    => false
            )
        );

        // ...
        $form->setElement( "select", "noChoicePosition",
            array(
                'label'       => "Position von \"Keine Auswahl\"",
                'description' => "Wo soll die Auswahl \"Keine Auswahl\" positioniert werden? Vor/hinter für den Slider, über/unter für die Listen Ansicht.",
                'required'    => false,
                'value'       => 1,
                'store'       => array(
                    array( 0, "Über/vor den Komponenten" ),
                    array( 1, "Unter/hinter den Komponenten" )
                )
            )
        );
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

    public function updateVersion147()
    {
        // create the form
        $form = $this->bootstrap->Form();

        // info
        $form->setElement( "button", "cacheButton",
            array(
                'label' => "<b>Cache</b>"
            )
        );

        // default locale
        $form->setElement( "boolean", "cacheStatus",
            array(
                'label'       => "Cache aktivieren",
                'description' => "Sollen Konfiguratoren und Konfigurationen gecached werden? Eine Deaktivierung des caches wirkt sich massiv auf die Performance aus.",
                'value'       => true
            )
        );

        //
        $form->setElement( "integer", "cacheTime",
            array(
                'label'       => "Cache Zeit",
                'description' => "Wie lange soll ein Konfigurator / eine Konfiguration (in Sekunden) im cache behalten werden?",
                'value'       => 3600
            )
        );
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
        $sql = @file_get_contents( $this->bootstrap->Path() . "Update/update-" . $version . ".sql" );

        // execute the query
        try { Shopware()->Db()->exec( $sql ); }
        // catch any db exception
        catch ( Exception $exception ) {}
    }



}