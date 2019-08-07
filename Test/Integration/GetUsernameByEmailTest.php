<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Test\Integration;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\GoogleSignOn\Model\ResourceModel\GetUsernameByEmail;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

class GetUsernameByEmailTest extends TestCase
{
    /**
     * @var GetUsernameByEmail
     */
    private $getUsernameByEmail;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->getUsernameByEmail = Bootstrap::getObjectManager()->get(GetUsernameByEmail::class);
    }

    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testGetUsernameByEmail(): void
    {
        $this->assertSame('adminUser', $this->getUsernameByEmail->execute('adminUser@example.com'));
    }

    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testShouldTriggerExceptionIfUserDoesNotExist(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Unknown user with email non-existing@mail.com');
        $this->getUsernameByEmail->execute('non-existing@mail.com');
    }
}
