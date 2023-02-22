<?php
/*
 * Copyright © Ghost Unicorns snc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GhostUnicorns\CrtTableTransferor\Transferor\Mappings;

interface MappingTypeInterface
{
    /**
     * @return string
     */
    public function getField(): string;

    /**
     * @param array $data
     */
    public function getValue(array $data);
}
