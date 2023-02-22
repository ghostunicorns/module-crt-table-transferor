<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Transferor\Mappings;

class FixedNull implements MappingTypeInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @param string $field
     */
    public function __construct(
        string $field
    ) {
        $this->field = $field;
    }

    /**
     * @param array $data
     * @return null
     */
    public function getValue(array $data)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
