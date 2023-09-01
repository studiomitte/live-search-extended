<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\Configuration;

class Field
{

    protected bool $skipIfEmpty = false;
    protected bool $prefixLabel = true;
    protected string $label = '';

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
            'label' => $this->label,
        ];
    }

    public function setConfiguration(array $configuration): void
    {
        if (isset($configuration['skipIfEmpty'])) {
            $this->setSkipIfEmpty($configuration['skipIfEmpty']);
        }
        if (isset($configuration['prefixLabel'])) {
            $this->setPrefixLabel($configuration['prefixLabel']);
        }
        $this->setLabel($configuration['label'] ?? '');
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): Field
    {
        $this->label = $label;
        return $this;
    }


}