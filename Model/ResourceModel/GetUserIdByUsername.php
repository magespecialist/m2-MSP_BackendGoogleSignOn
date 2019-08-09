<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\BackendGoogleSignOn\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class GetUserIdByUsername
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
     * @param string $username
     * @return int
     * @throws NoSuchEntityException
     */
    public function execute(string $username): int
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('admin_user');

        $qry = $connection->select()->from($tableName, 'user_id')->where('username=?', $username);

        $res = $connection->fetchOne($qry);
        if (empty($res)) {
            throw new NoSuchEntityException(__('Unknown user with username %1', $username));
        }

        return (int) $res;
    }
}
