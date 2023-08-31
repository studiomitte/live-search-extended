<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\Configuration;

class Field
{

    protected bool $skipIfEmpty = false;
    protected bool $prefixLabel = true;

    public function __construct(
        public readonly string $field,
        public readonly string $icon = ''
    )
    {

    }

    public function getConfiguration(): array
    {
        return [
            'icon' => $this->icon,
            'skipIfEmpty' => $this->skipIfEmpty,
            'prefixLabel' => $this->prefixLabel,
        ];
    }

    public function isSkipIfEmpty(): bool
    {
        return $this->skipIfEmpty;
    }

    public function skipIfEmpty(): Field
    {
        $this->skipIfEmpty = true;
        return $this;
    }

    public function usePrefixLabel(): bool
    {
        return $this->prefixLabel;
    }

    public function skipPrefixLabel(): Field
    {
        $this->prefixLabel = false;
        return $this;
    }



}