<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Model;

use Google_Client;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GetGoogleClient
{
    /**
     * Client ID XML path
     */
    private const XML_PATH_CLIENT_ID = 'msp_google_sign_on/general/client_id';

    /**
     * Client secret XML path
     */
    private const XML_PATH_CLIENT_SECRET = 'msp_google_sign_on/general/client_secret';

    /**
     * @var GoogleClientFactory
     */
    private $googleClientFactory;

    /**
     * @var Google_Client
     */
    private $googleClient;

    /**
     * @var GetApplicationName
     */
    private $getApplicationName;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param GoogleClientFactory $googleClientFactory
     * @param GetApplicationName $getApplicationName
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     */
    public function __construct(
        GoogleClientFactory $googleClientFactory,
        GetApplicationName $getApplicationName,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url
    ) {
        $this->googleClientFactory = $googleClientFactory;
        $this->getApplicationName = $getApplicationName;
        $this->scopeConfig = $scopeConfig;
        $this->url = $url;
    }

    /**
     * Get a configured Google client
     *
     * @return Google_Client
     */
    public function execute(): Google_Client
    {
        if ($this->googleClient === null) {
            $this->googleClient = $this->googleClientFactory->create();

            $clientId = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID) ?: '';
            $clientSecret = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET) ?: '';

            $redirectUrl = $this->url->getUrl('adminhtml');

            $this->googleClient->setApplicationName($this->getApplicationName->execute());
            $this->googleClient->setClientId($clientId);
            $this->googleClient->setClientSecret($clientSecret);
            $this->googleClient->setRedirectUri($redirectUrl);
            $this->googleClient->addScope('email');
        }

        return $this->googleClient;
    }
}
