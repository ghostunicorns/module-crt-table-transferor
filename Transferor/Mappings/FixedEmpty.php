<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace GhostUnicorns\CrtTableTransferor\Transferor\Mappings;

class FixedEmpty implements MappingTypeInterface
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
     * @return string
     */
    public function getValue(array $data): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }
}
