<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\Configuration;

class Table
{

    /**
     * @var Field[]
     */
    protected array $fields = [];

    public function __construct(
        public readonly string $table
    )
    {

    }

    public static function createFromTCA(string $tableName): ?Table
    {
        $config = $GLOBALS['TCA'][$tableName]['ctrl']['live_search_extended'] ?? [];
        if (empty($config)) {
            return null;
        }
        $table = new self($tableName);
        foreach ($config['fields'] ?? [] as $field => $fieldConfiguration) {
            $field = new Field($field, $fieldConfiguration['icon'] ?? '');
            if ($fieldConfiguration['skipIfEmpty'] ?? true) {
                $field->skipIfEmpty();
            }
            if ($fieldConfiguration['prefixLabel'] ?? true) {
                $field->skipPrefixLabel();
            }
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
        $this->fields[] = $field;
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
            'fields' => $fieldData,
        ];
    }

    public function isValid(): bool
    {
        return isset($GLOBALS['TCA'][$this->table]) && !empty($this->fields);
    }

}