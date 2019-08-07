<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\GoogleSignOn\Test\Integration;

use MSP\GoogleSignOn\Model\GetDomainRule;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;

class GetDomainRuleTest extends TestCase
{
    /**
     * @var GetDomainRule
     */
    private $getDomainRule;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->getDomainRule = Bootstrap::getObjectManager()->get(GetDomainRule::class);
    }

    /**
     * @return array
     */
    public function getDomainRuleDataProvider(): array
    {
        return [
            ['my.name@gmail.com', null],
            ['my.name@mydomain.com', 'force'],
            ['my.name@mydomain2.com', 'force'],
            ['my.name@mydomain3.com', '1'],
            ['mydomain3.com', '1'],
            ['mydomain2.com', 'force'],
            ['gmail.com', null],
        ];
    }

    /**
     * @param string $emailOrDomain
     * @param string|null $rule
     * @dataProvider getDomainRuleDataProvider
     * @magentoAdminConfigFixture msp_google_sign_on/advanced/domain_rules {"1":{"domain":"mydomain.com","rule":"force"}, "2":{"domain":"mydomain2.com","rule":"force"}, "3":{"domain":"mydomain3.com","rule":"1"}}
     */
    public function testShouldGetDomainRule(string $emailOrDomain, ?string $rule): void
    {
        $this->assertSame($rule, $this->getDomainRule->execute($emailOrDomain));
    }
}
