<?php

/**
 * Aquatuning Software Development - Configurator - Listener
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Listener\Components\Theme;

use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\Theme\LessDefinition;
use Doctrine\Common\Collections\ArrayCollection;



/**
 * Aquatuning Software Development - Configurator - Listener
 */

class Compiler
{

	/**
	 * ...
	 *
	 * @var string
	 */

	protected $viewDir;



	/**
	 * ...
	 *
	 * @param string   $viewDir
	 */

	public function __construct( $viewDir )
	{
		// set params
		$this->viewDir = $viewDir;
	}



    /**
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return ArrayCollection
     */

    public function addLessFiles( EventArgs $arguments )
    {
        // create less array
        $less = new LessDefinition(
            array(),
            array(
                $this->viewDir . 'frontend/_public/src/less/all.less'
            ),
            $this->viewDir
        );

        // return it
        return new ArrayCollection( array( $less ) );
    }



    /**
     * ...
     *
     * @param EventArgs   $arguments
     *
     * @return ArrayCollection
     */

    public function addJavascriptFiles( EventArgs $arguments )
    {
        // all js files here
        $files = array(
            $this->viewDir . "frontend/_public/src/js/jquery.atsd-configurator-product-slider.js",
            $this->viewDir . "frontend/_public/src/js/jquery.atsd-configurator-ajax-modal.js",
            $this->viewDir . "frontend/_public/src/js/jquery.atsd-configurator.js"
        );

        // return them
        return new ArrayCollection( $files );
    }

}
