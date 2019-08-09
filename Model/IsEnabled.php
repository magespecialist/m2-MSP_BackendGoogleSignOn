<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class IsEnabled
{
    /**
     * XML path to enabled flag
     */
    private const XML_PATH_ENABLED = 'msp_backend_google_sign_on/general/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return true if the module is enabled
     *
     * @return bool
     */
    public function execute(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_ENABLED);
    }
}
