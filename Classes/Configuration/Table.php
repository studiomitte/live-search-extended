<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\Configuration;

class Table
{

    /**
     * @var Field[]
     */
    protected array $fields = [];
    protected bool $useNotesField = true;

    public function __construct(
        public readonly string $table
    )
    {
    }

    public static function createFromTCA(string $tableName): ?Table
    {
        $config = $GLOBALS['TCA'][$tableName]['ctrl']['live_search_extended'] ?? [];

        $table = new self($tableName);
        $table->setUseNotesField($config['useNotesField'] ?? true);
        foreach ($config['fields'] ?? [] as $field => $fieldConfiguration) {
            $field = new Field($field, $fieldConfiguration['icon'] ?? '');
            $field->setConfiguration($fieldConfiguration);
            $table->addField($field);
        }
        return $table;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(Field $field): Table
    {
        $this->fields[$field->field] = $field;
        return $this;
    }

    /**
     * @return bool
     */
    public function getUseNotesField(): bool
    {
        return $this->useNotesField;
    }

    /**
     * @param bool $useNotesField
     * @return Table
     */
    public function setUseNotesField(bool $useNotesField): Table
    {
        $this->useNotesField = $useNotesField;
        return $this;
    }


    public function persist(): void
    {
        if (!isset($GLOBALS['TCA'][$this->table])) {
            throw new \UnexpectedValueException(sprintf('Table %s does not exist', $this->table), 1693469649);
        }
        $fieldData = [];
        foreach ($this->fields as $field) {
            $fieldData[$field->field] = $field->getConfiguration();
        }
        $GLOBALS['TCA'][$this->table]['ctrl']['live_search_extended'] = [
            'useNotesField' => $this->useNotesField,
            'fields' => $fieldData,
        ];
    }

    public function isValid(): bool
    {
        return isset($GLOBALS['TCA'][$this->table]);
    }

}