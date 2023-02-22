<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Transferor\Mappings;

use GhostUnicorns\CrtBase\Exception\CrtException;
use GhostUnicorns\CrtUtils\Model\DotConvention;

class Simple implements MappingTypeInterface
{
    /**
     * @var DotConvention
     */
    private $dotConvention;

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $source;

    /**
     * @param DotConvention $dotConvention
     * @param string $field
     * @param string $source
     */
    public function __construct(
        DotConvention $dotConvention,
        string $field,
        string $source
    ) {
        $this->dotConvention = $dotConvention;
        $this->field = $field;
        $this->source = $source;
    }

    /**
     * @param array $data
     * @return null|string|int|float
     * @throws CrtException
     */
    public function getValue(array $data): float|int|string|null
    {
        return $this->dotConvention->getValue($data, $this->source);
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
