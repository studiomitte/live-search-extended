<?php

namespace StudioMitte\LiveSearchExtended\EventListener\RowModification;

use StudioMitte\LiveSearchExtended\Event\ModifyRowEvent;

final class SysTemplateRowModificationEventListener
{

    public function __invoke(ModifyRowEvent $event)
    {
        if ($event->tableName === 'sys_template') {
            $row = $event->getRow();
            $row['_count_constants'] = $this->count((string)$row['constants']);
            $row['_count_config'] = $this->count((string)$row['config']);
            $event->setRow($row);
        }
    }

    protected function count(string $value): int
    {
        if ($value === '') {
            return 0;
        }
        return count(explode(chr(10), trim($value)));
    }

}