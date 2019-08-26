<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use MSP\BackendGoogleSignOn\Model\GetGoogleClient;

class GoogleSignOn implements ArgumentInterface
{
    /**
     * @var GetGoogleClient
     */
    private $getGoogleClient;

    /**
     * @param GetGoogleClient $getGoogleClient
     */
    public function __construct(
        GetGoogleClient $getGoogleClient
    ) {
        $this->getGoogleClient = $getGoogleClient;
    }

    /**
     * Get google auth URL
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        $client = $this->getGoogleClient->execute();
        return $client->createAuthUrl();
    }
}
