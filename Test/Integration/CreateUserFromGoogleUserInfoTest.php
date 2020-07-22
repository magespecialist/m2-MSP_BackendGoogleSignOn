<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Test\Integration;

use Google_Service_Oauth2_Userinfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\User\Model\User;
use MSP\BackendGoogleSignOn\Model\CreateUserFromGoogleUserInfo;
use MSP\BackendGoogleSignOn\Model\ResourceModel\GetUsernameByEmail;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

class CreateUserFromGoogleUserInfoTest extends TestCase
{
    /**
     * @var CreateUserFromGoogleUserInfo
     */
    private $createUserFromGoogleUserInfo;

    /**
     * @var GetUsernameByEmail
     */
    private $getUsernameByEmail;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->createUserFromGoogleUserInfo = Bootstrap::getObjectManager()->get(CreateUserFromGoogleUserInfo::class);
        $this->getUsernameByEmail = Bootstrap::getObjectManager()->get(GetUsernameByEmail::class);
    }

    /**
     * @param string $email
     * @return User
     */
    private function getUserByEmail(string $email): ?User
    {
        try {
            $username = $this->getUsernameByEmail->execute($email);
        } catch (NoSuchEntityException $e) {
            return null;
        }

        /** @var User $user */
        $user = Bootstrap::getObjectManager()->create(User::class);
        $user->loadByUsername($username);

        if (!$user->getId()) {
            return null;
        }

        return $user;
    }

    /**
     * @magentoAdminConfigFixture msp_backend_google_sign_on/advanced/domain_rules {"1":{"domain":"mydomain.com","rule":"force"}, "2":{"domain":"mydomain2.com","rule":"force"}, "3":{"domain":"mydomain3.com","rule":"1"}}
     */
    public function testShouldCreateNewUser(): void
    {
        $googleInfo = new Google_Service_Oauth2_Userinfo();
        $googleInfo->setGivenName('John');
        $googleInfo->setFamilyName('Doe');
        $googleInfo->setEmail('some@mydomain3.com');
        $googleInfo->setHd('mydomain3.com');

        $this->createUserFromGoogleUserInfo->execute($googleInfo);

        $user = $this->getUserByEmail('some@mydomain3.com');
        $this->assertNotNull($user);

        $this->assertSame('John', $user->getFirstName());
        $this->assertSame('Doe', $user->getLastName());
        $this->assertSame(1, (int) $user->getRole()->getId());
    }

    /**
     * @magentoAdminConfigFixture msp_backend_google_sign_on/advanced/domain_rules {"1":{"domain":"mydomain.com","rule":"force"}, "2":{"domain":"mydomain2.com","rule":"force"}, "3":{"domain":"mydomain3.com","rule":"1"}}
     */
    public function testShouldNotCreateNewUserWithADomainRule(): void
    {
        $googleInfo = new Google_Service_Oauth2_Userinfo();
        $googleInfo->setGivenName('John');
        $googleInfo->setFamilyName('Doe');
        $googleInfo->setEmail('some@mydomain.com');
        $googleInfo->setHd('mydomain.com');

        $this->createUserFromGoogleUserInfo->execute($googleInfo);

        $user = $this->getUserByEmail('some@mydomain.com');
        $this->assertNull($user);
    }

    /**
     * @magentoAdminConfigFixture msp_backend_google_sign_on/advanced/domain_rules {"1":{"domain":"mydomain.com","rule":"force"}, "2":{"domain":"mydomain2.com","rule":"force"}, "3":{"domain":"mydomain3.com","rule":"1"}}
     */
    public function testShouldNotCreateNewUserWithoutADomainRule(): void
    {
        $googleInfo = new Google_Service_Oauth2_Userinfo();
        $googleInfo->setGivenName('John');
        $googleInfo->setFamilyName('Doe');
        $googleInfo->setEmail('some@gmail.com');
        $googleInfo->setHd('gmail.com');

        $this->createUserFromGoogleUserInfo->execute($googleInfo);

        $user = $this->getUserByEmail('some@gmail.com');
        $this->assertNull($user);
    }
}
