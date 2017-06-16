<?php

/**
 * Aquatuning Software Development - Configurator - Bootstrap
 *
 * 1.0.0
 * - initial release
 *
 * 1.0.1
 * - updated template to drop property container if we have no properties in the info ajax call
 * - added option to switch the article for a configurator in the configurator list
 *
 * 1.0.2
 * - added check if a basket attribute even exists in sBasket subscriber
 *
 * 1.0.3
 * - added possibility to click on the article name to activate/deactivate the checkbox/radio
 *
 * 1.0.4
 * - always add our template directory in the detail controller
 * - added smarty parent block in the detail view
 *
 * 1.1.0
 * - moved controller subscriber into new directory
 * - added "saved selections" menu to the account
 *
 * 1.1.1
 * - updated phpdoc
 *
 * 1.1.2
 * - added checkLicense() call to start dispatch listener
 *
 * 1.1.3
 * - remove selections before removing a configurator
 * - added check so one article can not be assigned to multiple configurators
 *
 * 1.1.4
 * - fixed checkLicense() method
 *
 * 1.1.5
 * - added rudimentary php 7 support
 * - added notice to configuration administration that selections may be deleted as well
 * - added a "no article picked" output in the basket
 * - added "no choice" option for list template
 * - added summed up price for articles with more than 1 quantity in the slider template
 * - added configuration to remove articles without category assignment from the configurator
 *
 * 1.1.6
 * - added star (*) to "no choice" price
 * - added article description on top of the configurator
 * - removed default article tabs
 *
 * 1.1.7
 * - added delivery status icon to "no choice"
 *
 * 1.1.8
 * 1.1.9
 * - added rudimentary php7 support
 *
 * 1.1.10
 * - fixed css for action panel to hide it in low resolution
 *
 * 1.1.11
 * - split onDispatch() method to load subscribers depending on request module
 *
 * 1.1.12
 * - revoked 1.1.11
 *
 * 1.1.13
 * - added plugin configuration to active/deactivate shop (e.g. for account links)
 *
 * 1.1.14
 * - fixed bug from 1.1.13
 *
 * 1.1.15
 * - added plugin configuration for article attribute to output in the popup
 *
 * 1.1.16
 * - moved no-choice selection into an external template file
 * - added plugin configuration to show no-choice before or after available articles
 * - added plugin configuration to disable "to product" button
 *
 * 1.2.0
 * - added shopware 5.2 compability (sw5.2 only)
 * - removed a bugfix for 5.1.2 where we had to generate thumbnails for configurator articles
 *   in the component
 *
 * 1.2.1
 * - fixed an exception when ordering a selection. we have to give the new 5.2 attribute bundle
 *   a order_basket item to create the attributes from and overwrite them later
 *
 * 1.2.2
 * - fixed saving of attribute when splitting of articles is disabled
 *
 * 1.2.3
 * - added shopware 5.1 support
 *
 * 1.2.4
 * - added configuration to set the main article as free of charge
 * - added custom product slider to remove infinity loop
 *
 * 1.2.5
 * - added support of article scaled prices for configurator components
 * - added modal article details for slider template
 *
 * 1.3.0
 * - moved article details modal popup in slider template to article name click event
 * - added article quantity as selection option
 * - added option to copy configurator
 * - added check to disallow a configurator article within a configurator
 * - removed articles from selection when removing an element article
 * - restructured components and services
 * - added check for invalid selections within the cart
 *
 * 1.3.1
 * - renamed internal js components to use plugin namespace
 *
 * 1.4.0
 * - added shopware 5.3 compatibility
 * - dropped shopware 5.1 compatibility
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

use Shopware\AtsdConfigurator\Subscriber;
use Shopware\AtsdConfigurator\Bootstrap\Install;
use Shopware\AtsdConfigurator\Bootstrap\Update;
use Shopware\AtsdConfigurator\Bootstrap\Uninstall;
use Enlight_Event_EventArgs as EventArgs;



class Shopware_Plugins_Frontend_AtsdConfigurator_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    
    // info
    private $pluginInfo = array(
        'version'     => "1.4.0",
        'label'       => "ATSD - Konfigurator",
        'description' => "Konfigurator",
        'supplier'    => "Aquatuning GmbH",
        'autor'       => "Aquatuning GmbH",
        'support'     => "Aquatuning GmbH",
        'copyright'   => "Aquatuning GmbH",
        'link'        => 'http://www.aquatuning.de',
        'source'      => null,
        'changes'     => null,
        'license'     => null,
        'revision'    => null
    );
    
    // get capabilities
    private $pluginCapabilities = array(
        'install' => true,
        'update'  => true,
        'enable'  => true
    );

    // invalidate these caches
    private $invalidateCacheArray = array(
        "proxy",
        "frontend",
        "backend",
        "theme",
        "config"
    );



    /**
     * Returns the current version of the plugin.
     * 
     * @return string
     */
    
    public function getVersion()
    {
        return $this->pluginInfo['version'];
    }



    /**
     * Get (nice) name for the plugin manager list.
     * 
     * @return string
     */
    
    public function getLabel()
    {
        return $this->pluginInfo['label'];
    }


    
    /**
     * Get full information for the plugin manager list.
     *
     * @return array
     */
    
    public function getInfo()
    {
        return $this->pluginInfo;
    } 
     

     
    /**
     * Get capabilities for the plugin manager.
     * 
     * @return array
     */
    
    public function getCapabilities()
    {
        return $this->pluginCapabilities;
    }



    /**
     * Installation method.
     *
     * @return array
     */

    public function install()
    {
        try
        {
            // check license
            $this->checkLicense();

            // install the plugin
            $installer = new Install( $this );
            $installer->install();

            // update it to current version
            $updater = new Update( $this, $this->get( "shopware.model_manager" ), $this->get( "shopware_attribute.crud_service" ) );
            $updater->install();

            // fertig
            return array(
                'success'         => true,
                'invalidateCache' => $this->invalidateCacheArray
            );
        }
        catch ( Exception $e )
        {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }





    /**
     * Checks for a valid license key.
     *
     * @param boolean   $throwException
     *
     * @throws \Exception
     *
     * @return bool
     */

    public function checkLicense( $throwException = true )
    {
        // license check
        $check = null;

        // throw an event for the check
        $check = $this->get( "events" )->filter(
            'Shopware_AtsdConfigurator_CheckLicense',
            $check,
            array(
                'subject' => $this
            )
        );

        // license checked via event?
        if ( $check == true )
            // all done
            return true;



        if(!Shopware()->Container()->has('license')){
            if($throwException) {
                throw new Exception('The license manager has to be installed and active');
            } else {
                return false;
            }
        }

        try {
            static $r, $module = 'AtsdConfigurator';
            if(!isset($r)) {
                $s = base64_decode('FSsjZlLwMzRbb2ya4Rsa1c5xXo4=');
                $c = base64_decode('VHKU6nlglRCILxE7/y2G0BgpWEw=');
                $r = sha1(uniqid('', true), true);
                /** @var $l Shopware_Components_License */
                $l = $this->Application()->License();
                $i = $l->getLicense($module, $r);
                $t = $l->getCoreLicense();
                $u = strlen($t) === 20 ? sha1($t . $s . $t, true) : 0;
                $r = $i === sha1($c. $u . $r, true);
            }
            if(!$r && $throwException) {
                throw new Exception('License check for module "' . $module . '" has failed.');
            }
            return $r;
        } catch (Exception $e) {
            if($throwException) {
                throw new Exception('License check for module "' . $module . '" has failed.');
            } else {
                return false;
            }
        }
    }




    /**
     * Register our custom models after initialisation.
     *
     * @return void
     */

    public function afterInit()
    {
        $this->get( "loader" )->registerNamespace(
            'Shopware\AtsdConfigurator',
            $this->Path()
        );

        // register our models
        $this->registerCustomModels();
    }






    /**
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return void
     */

    public function onStartDispatch( EventArgs $arguments )
    {
        // licence ok?
        if ( $this->checkLicense( false ) == false )
            // stop here
            return;

        // we need our service subscriber first to get at least our component via container
        $this->get( "events" )->addSubscriber(
            new Subscriber\ServiceContainer( $this, $this->get( "service_container" ) )
        );

        // subscribers to add
        $subscribers = array(
            new Subscriber\Components\Theme\Compiler( $this ),
            new Subscriber\Controllers\Frontend\Account( $this, $this->get( "service_container" ) ),
            new Subscriber\Controllers\Frontend\Checkout( $this, $this->get( "service_container" ) ),
            new Subscriber\Controllers\Frontend\Detail( $this, $this->get( "service_container" ) ),
            new Subscriber\Controllers\Frontend\Listing( $this, $this->get( "service_container" ) ),
            new Subscriber\Core\sAdmin( $this, $this->get( "service_container" ), $this->get( "service_container" )->get( "atsd_configurator.component" ) ),
            new Subscriber\Core\sBasket( $this, $this->get( "service_container" ), $this->get( "service_container" )->get( "atsd_configurator.component" ) ),
            new Subscriber\Core\sOrder( $this->get( "service_container" ), $this->get( "service_container" )->get( "atsd_configurator.config" ), $this->get( "service_container" )->get( "atsd_configurator.version-service" ) ),
            new Subscriber\Controllers( $this )
        );

        // loop them
        foreach( $subscribers as $subscriber )
            // and add subscriber
            $this->get( "events" )->addSubscriber( $subscriber );
    }




    /**
     * Uninstall our plugin.
     *
     * @return boolean
     */

    public function uninstall()
    {
        // uninstall the plugin
        $uninstaller = new Uninstall( $this, $this->get( "shopware.model_manager" ), $this->get( "shopware_attribute.crud_service" ) );
        $uninstaller->uninstall();

        // done
        return true;
    }



    /**
     * Update our plugin if necessary.
     *
     * @param string   $version
     *
     * @return boolean
     */

    public function update( $version )
    {
        // update it to current version
        $updater = new Update( $this, $this->get( "shopware.model_manager" ), $this->get( "shopware_attribute.crud_service" ) );
        $updater->update( $version );

        // all done
        return true;
    }





}
