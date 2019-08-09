<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Computes the access rule for a domain
 */
class GetDomainRule
{
    /**
     * XML PATH to domain rules configuration
     */
    private const XML_PATH_DOMAIN_RULES = 'msp_backend_google_sign_on/advanced/domain_rules';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get rule for user
     *
     * null => No rule is defined
     * 'force' => Force login via Google Sign-On
     * '1' => Create one user in group 1 and force Google Sign-On
     *
     * @param string $emailOrDomain
     * @return string|null
     */
    public function execute(string $emailOrDomain): ?string
    {
        try {
            $rules = $this->serializer->unserialize($this->scopeConfig->getValue(self::XML_PATH_DOMAIN_RULES));
        } catch (\InvalidArgumentException $e) {
            $rules = [];
        }

        $atPos = strpos($emailOrDomain, '@');
        $domain = ($atPos !== false) ? substr($emailOrDomain, $atPos + 1) : $emailOrDomain;

        foreach ($rules as $rule) {
            if ($rule['domain'] === $domain) {
                return $rule['rule'];
            }
        }

        return null;
    }
}
