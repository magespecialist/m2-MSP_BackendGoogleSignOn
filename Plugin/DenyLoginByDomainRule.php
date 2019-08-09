<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\User\Model\User;
use MSP\BackendGoogleSignOn\Model\GetDomainRule;
use MSP\BackendGoogleSignOn\Model\IsEnabled;

class DenyLoginByDomainRule
{
    /**
     * @var GetDomainRule
     */
    private $getDomainRule;

    /**
     * @var IsEnabled
     */
    private $isEnabled;

    /**
     * @param IsEnabled $isEnabled
     * @param GetDomainRule $getDomainRule
     */
    public function __construct(
        IsEnabled $isEnabled,
        GetDomainRule $getDomainRule
    ) {
        $this->getDomainRule = $getDomainRule;
        $this->isEnabled = $isEnabled;
    }

    /**
     * Return true if should log-in via Google
     *
     * @param User $subject
     * @return bool
     */
    private function shouldLoginViaGoogle(User $subject): bool
    {
        return $this->getDomainRule->execute($subject->getEmail()) !== null;
    }

    /**
     * @param User $subject
     * @param bool $result
     * @param string $username
     * @param string $password
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAuthenticate(User $subject, bool $result, string $username, string $password): bool
    {
        if ($result && $this->isEnabled->execute() && $this->shouldLoginViaGoogle($subject)) {
            $subject->unsetData();

            throw new LocalizedException(__('Please use the Google Authentication mechanism'));
        }

        return $result;
    }
}
