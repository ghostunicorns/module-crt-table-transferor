<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Transferor;

use GhostUnicorns\CrtBase\Api\CrtConfigInterface;
use GhostUnicorns\CrtBase\Api\TransferorInterface;
use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtEntity\Api\EntityRepositoryInterface;
use GhostUnicorns\CrtTableTransferor\Model\TableManager;
use GhostUnicorns\CrtTableTransferor\Transferor\Mappings\MappingTypeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Monolog\Logger;

class TableTransferor implements TransferorInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityRepositoryInterface
     */
    private $entityRepository;

    /**
     * @var CrtConfigInterface
     */
    private $config;

    /**
     * @var TableManager
     */
    private $tableManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var MappingTypeInterface[]
     */
    private $insertMappings;

    /**
     * @var MappingTypeInterface[]
     */
    private $deleteMappings;

    /**
     * @param Logger $logger
     * @param EntityRepositoryInterface $entityRepository
     * @param CrtConfigInterface $config
     * @param TableManager $tableManager
     * @param ResourceConnection $resourceConnection
     * @param SerializerInterface $serializer
     * @param string $tableName
     * @param array $insertMappings
     * @param array $deleteMappings
     * @throws CrtException
     */
    public function __construct(
        Logger $logger,
        EntityRepositoryInterface $entityRepository,
        CrtConfigInterface $config,
        TableManager $tableManager,
        ResourceConnection $resourceConnection,
        SerializerInterface $serializer,
        string $tableName,
        array $insertMappings,
        array $deleteMappings
    ) {
        $this->logger = $logger;
        $this->entityRepository = $entityRepository;
        $this->config = $config;
        $this->tableManager = $tableManager;
        $this->resourceConnection = $resourceConnection;
        $this->tableName = $tableName;
        $this->insertMappings = $insertMappings;
        foreach ($insertMappings as $insertMapping) {
            if (!$insertMapping instanceof MappingTypeInterface) {
                throw new CrtException(__("Invalid type for mappings"));
            }
        }
        $this->deleteMappings = $deleteMappings;
        foreach ($deleteMappings as $deleteMapping) {
            if (!$deleteMapping instanceof MappingTypeInterface) {
                throw new CrtException(__("Invalid type for mappings"));
            }
        }
        $this->serializer = $serializer;
    }

    /**
     * @param int $activityId
     * @param string $transferorType
     * @throws CrtException
     */
    public function execute(int $activityId, string $transferorType): void
    {
        $allActivityEntities = $this->entityRepository->getAllDataRefinedByActivityIdGroupedByIdentifier($activityId);
        foreach ($allActivityEntities as $entityIdentifier => $entities) {
            $details = $this->serializer->serialize($entities);
            $this->logger->info(__(
                'activityId:%1 ~ Transferor ~ transferorType:%2 ~ entityIdentifier:%3 ~ START',
                $activityId,
                $transferorType,
                $entityIdentifier
            ));

            $connection = $this->resourceConnection->getConnection();
            $connection->beginTransaction();
            try {
                $deleted = $this->delete($entities);
                if ($deleted) {
                    $this->logger->info(__(
                        'activityId:%1 ~ Transferor ~ transferorType:%2 ~ entityIdentifier:%3 ~ Deleted: %4',
                        $activityId,
                        $transferorType,
                        $entityIdentifier,
                        $details
                    ));
                }

                $created = $this->insert($entities);
                if ($created) {
                    $this->logger->info(__(
                        'activityId:%1 ~ Transferor ~ transferorType:%2 ~ entityIdentifier:%3 ~ Created: %4',
                        $activityId,
                        $transferorType,
                        $entityIdentifier,
                        $details
                    ));
                }
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
                $this->logger->error(__(
                    'activityId:%1 ~ Transferor ~ transferorType:%2 ~ entityIdentifier:%3 ~ ERROR ~ error:%4',
                    $activityId,
                    $transferorType,
                    $entityIdentifier,
                    $e->getMessage()
                ));

                if (!$this->config->continueInCaseOfErrors()) {
                    throw new CrtException(__(
                        'activityId:%1 ~ Transferor ~ transferorType:%2 ~ entityIdentifier:%3 ~ END ~'.
                        ' Because of continueInCaseOfErrors = false',
                        $activityId,
                        $transferorType,
                        $entityIdentifier
                    ));
                }
            }
        }
    }

    /**
     * @param array $entities
     * @return int
     */
    public function delete(array $entities): int
    {
        $deleteData = [];
        foreach ($this->deleteMappings as $deleteMapping) {
            $deleteData[$deleteMapping->getField()] = $deleteMapping->getValue($entities);
        }
        return $this->tableManager->deleteIfExists($this->tableName, $deleteData);
    }

    /**
     * @param array $entities
     * @return int
     */
    public function insert(array $entities): int
    {
        $insertData = [];
        foreach ($this->insertMappings as $insertMapping) {
            $insertData[$insertMapping->getField()] = $insertMapping->getValue($entities);
        }
        return $this->tableManager->insert($this->tableName, $insertData);
    }
}
