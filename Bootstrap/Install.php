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

class Install
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
	 * @return \Shopware\AtsdConfigurator\Bootstrap\Install
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

	public function install()
	{
        // install
        $this->createDatabase();
        $this->subscribeEvents();
        $this->createMainDatabaseRecords();
        $this->installCreateDatabaseAttributes();
        $this->createConfigForm();
        $this->installCreateMenu();

		// done
		return true;
	}




    /**
     * Creates the necessary Databasetables for the plugin.
     *
     * @return void
     */

    private function createDatabase()
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
        $tool->createSchema( $classes );

        // done
        return;
    }







    /**
     * Creates the database attributes for our plugin.
     *
     * @return void
     */

    private function installCreateDatabaseAttributes()
    {
        // order basket
        Shopware()->Models()->addAttribute( 's_order_basket_attributes', 'atsd', 'configurator_selection_id', 'int(11)', true, null );

        // save our attributes
        Shopware()->Models()->generateAttributeModels( array(
            's_order_basket_attributes'
        ));

        // order details
        Shopware()->Models()->addAttribute( 's_order_details_attributes', 'atsd', 'configurator_selection_id', 'int(11)', true, null );
        Shopware()->Models()->addAttribute( 's_order_details_attributes', 'atsd', 'configurator_selection_master', 'tinyint(1)', true, null );

        // and generate the model
        Shopware()->Models()->generateAttributeModels(array(
            's_order_details_attributes',
        ));

        // and done
        return;
    }








    /**
     * Register the controller
     *
     * @return void
     */

    private function subscribeEvents()
    {
        // main subscriber
        $this->bootstrap->subscribeEvent(
            "Enlight_Controller_Front_DispatchLoopStartup",
            "onStartDispatch"
        );
    }






    /**
     * Insert mandatory data into the database for the plugin.
     *
     * @return void
     */

    private function createMainDatabaseRecords()
    {
        // get the .sql file
        $sql = @file_get_contents( $this->bootstrap->Path() . "Install/install.sql" );

        // insert it
        Shopware()->Db()->exec( $sql );

        // and we re done
        return;
    }





    /**
     * Creates the configuration form for the plugin
     *
     * @return void
     */

    private function createConfigForm()
    {
        // returns this plugin form
        $form = $this->bootstrap->Form();

        // and set the element
        $form->setElement( "select", "saleType",
            array(
                'label'       => "Artikel überverkaufen",
                'description' => "Dürfen Artikel innerhalb eines Konfigurators überverkauft werden?",
                'required'    => true,
                'value'       => 1,
                'store'       => array(
                    array( 0, "Immer überverkaufen" ),
                    array( 1, "Nur Abverkauf-Artikel nicht überverkaufen" ),
                    array( 2, "Nie Artikel überverkaufen" )
                )
            )
        );

        // and set the element
        $form->setElement( "boolean", "splitConfigurator",
            array(
                'label'       => "Artikel bei Bestellung aufteilen",
                'description' => "Sollen der Konfigurator in seine einzelnen Artikel nach einer erfolgreichen Bestellung aufgeteilt werden?",
                'value'       => true
            )
        );

        // and set the element
        $form->setElement( "text", "saveInAttribute",
            array(
                'label'       => "Konfiguration in Attribut speichern",
                'description' => "Die gewählte Konfiguration wird als Fließtext in diesem Attribut gespeichert. Um die Konfiguration in Emails, Rechnungen etc zu verwenden, können Sie auf dieses Attribut zugreifen. Bitte achten Sie darauf, dass 255 Zeichen eventuell nicht ausreichen und Sie die Datenbank-Struktur des Attribut anpassen.",
                'value'       => "attr1"
            )
        );


    }




    /**
     * Helper method to create the menu.
     *
     * @return void
     */

    private function installCreateMenu()
    {
        // create our menu item
        $this->bootstrap->createMenuItem( array(
            'label'      => "Konfigurator",
            'controller' => "AtsdConfigurator",
            'class'      => "sprite-application-block",
            'action'     => "Index",
            'active'     => 1,
            'parent'     => $this->bootstrap->Menu()->findOneBy( array( "label" => "Artikel" ) )
        ));
    }











}