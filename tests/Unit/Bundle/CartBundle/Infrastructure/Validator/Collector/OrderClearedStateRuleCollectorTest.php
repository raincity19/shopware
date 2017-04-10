<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Bundle\CartBundle\Infrastructure\Validator\Collector;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\CartBundle\Domain\Cart\CalculatedCart;
use Shopware\Bundle\CartBundle\Domain\Validator\Data\RuleDataCollection;
use Shopware\Bundle\CartBundle\Domain\Validator\Rule\RuleCollection;
use Shopware\Bundle\CartBundle\Infrastructure\Validator\Collector\OrderClearedStateRuleCollector;
use Shopware\Bundle\CartBundle\Infrastructure\Validator\Data\OrderClearedStateRuleData;
use Shopware\Bundle\CartBundle\Infrastructure\Validator\Rule\OrderClearedStateRule;
use Shopware\Bundle\StoreFrontBundle\Struct\Customer;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;

class OrderClearedStateRuleCollectorTest extends TestCase
{
    public function testWithoutRule()
    {
        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $connection = $this->createMock(Connection::class);

        $collector = new OrderClearedStateRuleCollector($connection);

        $dataCollection = new RuleDataCollection();

        $ruleCollection = new RuleCollection();

        $collector->collect($ruleCollection, $cart, $context, $dataCollection);

        $this->assertSame(0, $dataCollection->count());
    }

    public function testWithoutCustomer()
    {
        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);

        $connection = $this->createMock(Connection::class);

        $collector = new OrderClearedStateRuleCollector($connection);

        $dataCollection = new RuleDataCollection();

        $ruleCollection = new RuleCollection([new OrderClearedStateRule([10])]);

        $collector->collect($ruleCollection, $cart, $context, $dataCollection);

        $this->assertSame(0, $dataCollection->count());
    }

    public function testWithStates()
    {
        $cart = $this->createMock(CalculatedCart::class);

        $context = $this->createMock(ShopContext::class);
        $customer = new Customer();
        $customer->setId(1);
        $context->method('getCustomer')
            ->will($this->returnValue($customer));

        $connection = $this->createConnection([1]);

        $collector = new OrderClearedStateRuleCollector($connection);

        $dataCollection = new RuleDataCollection();

        $ruleCollection = new RuleCollection([new OrderClearedStateRule([10])]);

        $collector->collect($ruleCollection, $cart, $context, $dataCollection);

        $this->assertSame(1, $dataCollection->count());

        $rule = $dataCollection->get(OrderClearedStateRuleData::class);

        $this->assertInstanceOf(OrderClearedStateRuleData::class, $rule);

        /* @var OrderClearedStateRuleData $rule */
        $this->assertSame([1], $rule->getStates());
    }

    private function createConnection(?array $result)
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects(static::any())
            ->method('fetchAll')
            ->will(static::returnValue($result));

        $query = $this->createMock(QueryBuilder::class);
        $query->expects(static::any())
            ->method('execute')
            ->will(static::returnValue($statement));

        $connection = $this->createMock(Connection::class);
        $connection->expects(static::any())
            ->method('createQueryBuilder')
            ->will(static::returnValue($query));

        return $connection;
    }
}