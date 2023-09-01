<?php

declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\Event;

final class ModifyRowEvent
{
    public function __construct(
        public readonly string $tableName,
        protected array $row
    )
    {
    }

    public function getRow(): array
    {
        return $this->row;
    }

    public function setRow(array $row): void
    {
        $this->row = $row;
    }
}
