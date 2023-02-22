<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Model;

use Magento\Framework\App\ResourceConnection;

class TableManager
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return int
     */
    public function insert(string $tableName, array $data): int
    {
        $tableName = $this->resourceConnection->getTableName($tableName);
        $connection = $this->resourceConnection->getConnection();
        return $connection->insert($tableName, $data);
    }

    /**
     * @param string $tableBaseName
     * @param array $data
     * @return int
     */
    public function deleteIfExists(string $tableBaseName, array $data): int
    {
        $tableName = $this->resourceConnection->getTableName($tableBaseName);
        $connection = $this->resourceConnection->getConnection();
        $where = $this->getWhereFromFieldArray($data);
        return $connection->delete($tableName, $where);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getWhereFromFieldArray(array $data): string
    {
        $result = '';
        $connection = $this->resourceConnection->getConnection();
        $glue = ' and ';

        foreach ($data as $field => $value) {
            if (is_array($value)) {
                $result .= $connection->quoteInto("$field in ?" . $glue, $value);
            } else {
                $result .= $connection->quoteInto("$field = ?" . $glue, $value);
            }
        }

        return rtrim($result, $glue);
    }
}
