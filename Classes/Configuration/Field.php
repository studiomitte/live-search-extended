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

    /**
     * @return bool
     */
    public function isSkipIfEmpty(): bool
    {
        return $this->skipIfEmpty;
    }

    public function setSkipIfEmpty(bool $skipIfEmpty): Field
    {
        $this->skipIfEmpty = $skipIfEmpty;
        return $this;
    }

    public function isPrefixLabel(): bool
    {
        return $this->prefixLabel;
    }

    public function setPrefixLabel(bool $prefixLabel): Field
    {
        $this->prefixLabel = $prefixLabel;
        return $this;
    }



}