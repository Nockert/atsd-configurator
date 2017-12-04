<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Components\Helper;

use Shopware\Models\Customer\Customer;
use Enlight_Components_Session_Namespace as Session;
use Shopware\Components\Model\ModelManager;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class CustomerService
{

    /**
     * ...
     *
     * @var Session
     */

    protected $session;



    /**
     * ...
     *
     * @var ModelManager
     */

    protected $modelManager;



    /**
     * ...
     *
     * @param Session        $session
     * @param ModelManager   $modelManager
     */

    public function __construct( Session $session, ModelManager $modelManager )
    {
        // set params
        $this->session      = $session;
        $this->modelManager = $modelManager;
    }



    /**
     * ...
     *
     * @return Customer
     */

    public function getCustomer()
    {
        // get the customer id (if logged in)
        $customerId = (integer) $this->session->offsetGet( "sUserId" );

        // get the customer
        /* @var $customer Customer */
        $customer = $this->modelManager
            ->getRepository( Customer::class )
            ->find( $customerId );

        // return it
        return $customer;
    }

}
