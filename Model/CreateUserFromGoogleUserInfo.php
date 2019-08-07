<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Model;

use Google_Service_Oauth2_Userinfoplus;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\UserFactory;
use MSP\GoogleSignOn\Model\ResourceModel\GetUsernameByEmail;

class CreateUserFromGoogleUserInfo
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserResource
     */
    private $userResource;

    /**
     * @var GetDomainRule
     */
    private $getDomainRule;

    /**
     * @var GetUsernameByEmail
     */
    private $getUsernameByEmail;

    /**
     * @param GetDomainRule $getDomainRule
     * @param GetUsernameByEmail $getUsernameByEmail
     * @param UserFactory $userFactory
     * @param UserResource $userResource
     */
    public function __construct(
        GetDomainRule $getDomainRule,
        GetUsernameByEmail $getUsernameByEmail,
        UserFactory $userFactory,
        UserResource $userResource
    ) {
        $this->userFactory = $userFactory;
        $this->userResource = $userResource;
        $this->getDomainRule = $getDomainRule;
        $this->getUsernameByEmail = $getUsernameByEmail;
    }

    /**
     * Return true if user already exists
     *
     * @param string $email
     * @return bool
     */
    private function userExistsByEmail(string $email): bool
    {
        try {
            $this->getUsernameByEmail->execute($email);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Create a new admin user if not exists based on google info
     *
     * @param Google_Service_Oauth2_Userinfoplus $userInfo
     * @throws AlreadyExistsException
     */
    public function execute(Google_Service_Oauth2_Userinfoplus $userInfo): void
    {
        $rule = $this->getDomainRule->execute($userInfo->getHd());

        if (!is_numeric($rule) || $this->userExistsByEmail($userInfo->getEmail())) {
            return;
        }

        $user = $this->userFactory->create();
        $user->setFirstname($userInfo->getGivenName());
        $user->setLastname($userInfo->getFamilyName());
        $user->setUsername($userInfo->getEmail());
        $user->setPassword(md5(uniqid((string) time(), true)));
        $user->setEmail($userInfo->getEmail());
        $user->setInterfaceLocale($userInfo->getLocale());
        $user->setRoleId($rule);
        $user->setIsActive(1);
        $this->userResource->save($user);
    }
}
