<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Transferor\Mappings;

class FixedFloat implements MappingTypeInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $field
     * @param string $value
     */
    public function __construct(
        string $field,
        string $value
    ) {
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @param array $data
     * @return float
     */
    public function getValue(array $data): float
    {
        return (float)$this->value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
