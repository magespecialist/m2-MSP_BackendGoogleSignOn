<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Magento\Store\Model\StoreManagerInterface;

class GetApplicationName
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Return the application name for Google Auth
     *
     * @return string
     */
    public function execute(): string
    {
        return $this->storeManager->getDefaultStoreView()->getName();
    }
}
