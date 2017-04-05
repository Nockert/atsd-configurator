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
	 * @param Bootstrap   $bootstrap
	 *
	 * @return Update
	 */

	public function __construct( Bootstrap $bootstrap )
	{
		// set params
		$this->bootstrap = $bootstrap;
	}



	/**
	 * ...
	 *
	 * @return boolean
	 */

	public function install()
	{
        // install all updates
        $this->update( "0.0.0" );

		// done
		return true;
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

        // done
        return;
    }






    /**
     * Updates the plugin to 1.1.5
     *
     * @return boolean
     */

    public function updateVersion1115()
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

        // done
        return true;
    }





    /**
     * Updates the plugin to 1.1.6
     *
     * @return boolean
     */

    public function updateVersion1116()
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

        // done
        return true;
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
        try
        { Shopware()->Db()->exec( $sql ); }
        // catch any db exception
        catch ( Exception $exception ) {}
    }



}