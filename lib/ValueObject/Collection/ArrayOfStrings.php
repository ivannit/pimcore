<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\ValueObject\Collection;

use ValueError;
use function gettype;
use function is_string;
use function sprintf;

final class ArrayOfStrings
{
    /**
     * @throws ValueError
     */
    public function __construct(private readonly array $value)
    {
        $this->validate();
    }

    /**
     * @throws ValueError
     */
    public function __wakeup(): void
    {
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->value as $value) {
            if (!is_string($value)) {
                throw new ValueError(
                    sprintf(
                        'Provided array must contain only string values. (%s given)',
                        gettype($value)
                    ),
                );
            }
        }
    }

    /**
     * @return string[]
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
