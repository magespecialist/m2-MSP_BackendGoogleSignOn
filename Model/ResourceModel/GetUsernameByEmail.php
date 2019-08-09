<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class GetUsernameByEmail
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get admin username by email
     *
     * @param string $email
     * @return string
     * @throws NoSuchEntityException
     */
    public function execute(string $email): string
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('admin_user');

        $qry = $connection->select()->from($tableName, 'username')->where('email=?', $email);

        $res = $connection->fetchOne($qry);
        if (empty($res)) {
            throw new NoSuchEntityException(__('Unknown user with email %1', $email));
        }

        return $res;
    }
}
