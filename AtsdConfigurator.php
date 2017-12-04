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
 * 1.4.1
 * - always drop articles without categories due to shopware restrictions
 *
 * 1.4.2
 * - added asynchronous loading of jquery plugin
 *
 * 1.4.3
 * - added check if a session exists before decorating product list service
 *
 * 1.4.4
 * - fixed removing articles from a configurator
 * - fixed product slider
 *
 * 1.4.5
 * - optimized performance for price update in sBasket
 *
 * 1.4.6
 * - added help notice for article columns in backend view
 *
 * 1.4.7
 * - moved basket weight calculation to basket service
 * - added plugin configuration for cache status and time
 * - added subscribe to ups xml plugin to add selection weights to the basket
 *
 * 1.4.8
 * - fixed plugin configuration 1.4.7 update
 *
 * 1.4.9
 * - fixed sGetDispatchBasket hook for empty basket
 *
 * 1.4.10
 * - fixed non-escaped buy button
 *
 * 1.4.11
 * - fixed not allowed php functions for activated smarty security
 *
 * 1.5.0
 * - added simple dependency for master and slave components within one element
 * - added percental surcharge for components
 *
 * 1.5.1
 * - added controller actions to csrf whitelist
 *
 * 1.5.2
 * - fixed updating from version 1.3.2
 *
 * 1.5.3
 * - added possibility to translate fieldset and element names
 *
 * 1.5.4
 * - fixed account subscriber for smarty security settings
 *
 * 1.5.5
 * - removed multiplication of surcharges
 * - changed behaviour of element surcharge option
 *
 * 1.5.6
 * - removed deprecated allow-articles-without-category plugin configuration
 *
 * 1.5.7
 * - fixed translations when copying a configurator
 *
 * 2.0.0
 * - migrated to shopware 5.2 plugin system
 *
 * 2.0.1
 * - fixed plugin update
 *
 *
 *
 * @todo add selection garbage collector
 * @todo save default selection prices in 1:n table with customer group
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Exception;
use Enlight_Controller_EventArgs as EventArgs;
use Enlight_Controller_Action as Controller;



class AtsdConfigurator extends Plugin
{

    /**
     * ...
     *
     * @param ContainerBuilder   $container
     *
     * @return void
     */

    public function build( ContainerBuilder $container )
    {
        // set plugin parameters
        $container->setParameter( "atsd_configurator.plugin_dir", $this->getPath() . "/" );
        $container->setParameter( "atsd_configurator.view_dir", $this->getPath() . "/Resources/views/" );

        // call parent builder
        parent::build( $container );
    }



    /**
     * ...
     *
     * @return array
     */

    public static function getSubscribedEvents()
    {
        // ...
        return array(
            'Enlight_Controller_Front_DispatchLoopStartup' => 'onStartDispatch'
        );
    }



    /**
     * ...
     *
     * @param EventArgs   $args
     *
     * @return void
     */

    public function onStartDispatch( EventArgs $args )
    {
        /* @var $controller Controller */
        $controller = $args->getSubject();

        // get the module
        $module = $controller->Request()->getModuleName();

        // not in frontend?!
        if ( $module != "frontend" )
            // dont check license
            return;

        // check license and throw exceptions
        $this->checkLicense();
    }



    /**
     * Checks for a valid license key.
     *
     * @param boolean   $throwException
     *
     * @throws Exception
     *
     * @return bool
     */

    private function checkLicense( $throwException = true )
    {
        // license check
        $check = null;

        // throw an event for the check
        $check = $this->container->get( "events" )->filter(
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



        try {
            /** @var $l Shopware_Components_License */
            $l = Shopware()->License();
        } catch (\Exception $e) {
            if ($throwException) {
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
                $i = $l->getLicense($module, $r);
                $t = $l->getCoreLicense();
                $u = strlen($t) === 20 ? sha1($t . $s . $t, true) : 0;
                $r = $i === sha1($c. $u . $r, true);
            }
            if (!$r && $throwException) {
                throw new Exception('License check for module "' . $module . '" has failed.');
            }
            return $r;
        } catch (Exception $e) {
            if ($throwException) {
                throw new Exception('License check for module "' . $module . '" has failed.');
            } else {
                return false;
            }
        }
    }



    /**
     * Install the plugin.
     *
     * @param Context\InstallContext   $context
     *
     * @return void
     */

    public function install( Context\InstallContext $context )
    {
        // install the plugin
        $installer = new Setup\Install(
            $this,
            $context,
            $this->container->get( "models" ),
            $this->container->get( "db" )
        );
        $installer->install();

        // update it to current version
        $updater = new Setup\Update(
            $this,
            $context,
            $this->container->get( "models" ),
            $this->container->get( "db" ),
            $this->container->get( "shopware_attribute.crud_service" )
        );
        $updater->install();

        // call default installer
        parent::install( $context );
    }



    /**
     * Update the plugin.
     *
     * @param Context\UpdateContext   $context
     *
     * @return void
     */

    public function update( Context\UpdateContext $context )
    {
        // update the plugin
        $updater = new Setup\Update(
            $this,
            $context,
            $this->container->get( "models" ),
            $this->container->get( "db" ),
            $this->container->get( "shopware_attribute.crud_service" )
        );
        $updater->update( $context->getUpdateVersion());

        // call default updater
        parent::update( $context );
    }



    /**
     * Uninstall the plugin.
     *
     * @param Context\UninstallContext   $context
     *
     * @return void
     */

    public function uninstall( Context\UninstallContext $context )
    {
        // uninstall the plugin
        $uninstaller = new Setup\Uninstall(
            $this,
            $context,
            $this->container->get( "models" ),
            $this->container->get( "shopware_attribute.crud_service" )
        );
        $uninstaller->uninstall();

        // call default uninstaller
        parent::uninstall( $context );
    }

}
