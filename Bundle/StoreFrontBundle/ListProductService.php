<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Bundle\StoreFrontBundle;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class ListProductService implements \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface
{

    /**
     * The previously existing core service.
     *
     * @var \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface
     */

    private $coreService;



    /**
     * The previously existing core service.
     *
     * @var \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService
     */

    private $configuratorService;



    /**
     * The main component.
     *
     * @var \Shopware\AtsdConfigurator\Components\AtsdConfigurator
     */

    private $component;



    /**
     * DI container.
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */

    protected $container;





    /**
     * Object constructor.
     *
     * @param \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface    $coreService
     * @param \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService   $configuratorService
     * @param \Shopware\AtsdConfigurator\Components\AtsdConfigurator                   $component
     * @param \Shopware\Components\DependencyInjection\Container                       $container
     *
     * @return \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ListProductService
     */

    public function __construct(
        \Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface $coreService,
        \Shopware\AtsdConfigurator\Bundle\StoreFrontBundle\ConfiguratorService $configuratorService,
        \Shopware\AtsdConfigurator\Components\AtsdConfigurator $component,
        \Shopware\Components\DependencyInjection\Container $container )
    {
        // set parameters
        $this->coreService         = $coreService;
        $this->configuratorService = $configuratorService;
        $this->component           = $component;
        $this->container           = $container;
    }





    /**
     * ...
     *
     * @param array                                                              $numbers
     * @param \Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface   $context
     *
     * @return \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct[]
     */

    public function getList( array $numbers, \Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface $context )
    {
        // call core service
        $products      = $this->coreService->getList( $numbers, $context );

        // get all configurators
        $configurators = $this->configuratorService->getList( $products );



        // loop all products
        foreach ( $products as $product )
        {
            // add attribute
            $product->addAttribute(
                "atsd_configurator",
                new \Shopware\Bundle\StoreFrontBundle\Struct\Attribute( array(
                    'hasConfigurator'     => ( isset( $configurators[$product->getId()] ) ),
                    'defaultConfigurator' => ( isset( $configurators[$product->getId()] ) ) ? $this->getDefaultConfigurator( $configurators[$product->getId()]['id'], $product ) : null
                ) )
            );
        }



        // return the products
        return $products;
    }






    /**
     * ...
     *
     * @param string                                                             $number
     * @param \Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface   $context
     *
     * @return \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct
     */

    public function get( $number, \Shopware\Bundle\StoreFrontBundle\Struct\ProductContextInterface $context )
    {
        // call our list
        $products = $this->getList( array( $number ), $context );

        // return first product
        return array_shift( $products );
    }







    /**
     * ...
     *
     * @param integer                                                $configuratorId
     * @param \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct   $product
     *
     * @return array
     */

    private function getDefaultConfigurator( $configuratorId, \Shopware\Bundle\StoreFrontBundle\Struct\ListProduct $product )
    {
        // call component
        $defaults = $this->component->getConfiguratorDefaults( $configuratorId );

        // add from our own article
        $defaults['price']       += $product->getCheapestPrice()->getCalculatedPrice();
        $defaults['pseudoPrice'] += $product->getCheapestPrice()->getCalculatedPrice();
        $defaults['stock']        = min( $defaults['stock'], $product->getStock() );

        // return it
        return $defaults;
    }





}


