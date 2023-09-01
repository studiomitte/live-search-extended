<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use StudioMitte\LiveSearchExtended\Configuration\Table;
use StudioMitte\LiveSearchExtended\Event\ModifyRowEvent;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Search\Event\ModifyResultItemInLiveSearchEvent;
use TYPO3\CMS\Backend\Search\LiveSearch\DatabaseRecordProvider;
use TYPO3\CMS\Backend\Search\LiveSearch\ResultItem;
use TYPO3\CMS\Backend\Search\LiveSearch\ResultItemAction;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

final class ModifyResultEventListener
{
    protected LanguageService $languageService;

    public function __construct(
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly IconFactory $iconFactory,
        protected readonly LanguageServiceFactory $languageServiceFactory,
        protected readonly UriBuilder $uriBuilder
    )
    {
        $this->languageService = $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER']);
    }

    public function __invoke(ModifyResultItemInLiveSearchEvent $event): void
    {
        $resultItem = $event->getResultItem();
        if (!in_array($resultItem->getProviderClassName(), [DatabaseRecordProvider::class], true)) {
            return;
        }

        $table = $resultItem->getExtraData()['table'] ?? '';
        if (!$table) {
            return;
        }
        $searchConfiguration = Table::createFromTCA($table);

        $row = $resultItem->getInternalData()['row'] ?? null;
        if (!$row) {
            return;
        }

        $rowEvent = $this->eventDispatcher->dispatch(new ModifyRowEvent($table, $row));
        $row = $rowEvent->getRow();

        if ($searchConfiguration && $searchConfiguration->isValid()) {
            $this->addFieldsToResult($resultItem, $searchConfiguration, $table, $row);
            $this->addNotesToResult($resultItem, $searchConfiguration, $table, $row);
            $this->appendIdToTitle($resultItem, $row['uid']);
        }
    }

    protected function addFieldsToResult(ResultItem $resultItem, Table $searchConfiguration, string $table, array $row): void
    {
        foreach ($searchConfiguration->getFields() as $field) {
            $fieldName = $field->field;
            $fieldValue = $row[$fieldName];
            if (!isset($fieldValue)) {
                continue;
            }

            if (isset($GLOBALS['TCA'][$table]['columns'][$fieldName])) {
                $label = $this->languageService->sL(BackendUtility::getItemLabel($table, $fieldName));
                $content = BackendUtility::getProcessedValue($table, $fieldName, $fieldValue, 0, false, false, $row['uid']);
            } else {
                $label = $this->languageService->sL($field->getLabel());
                $content = $fieldValue;
            }
            if (!$content && $field->isSkipIfEmpty()) {
                continue;
            }
            $text = $field->isPrefixLabel() ? sprintf('%s: %s', $label, $content) : $content;
            $action = (new ResultItemAction($table . '_' . $fieldName))
                ->setLabel($text)
                ->setIcon($field->icon ? $this->iconFactory->getIcon($field->icon, Icon::SIZE_SMALL) : null);
            $resultItem->addAction($action);
        }
    }

    protected function addNotesToResult(ResultItem $resultItem, Table $searchConfiguration, string $table, array $row): void
    {
        $notesField = $GLOBALS['TCA'][$table]['ctrl']['descriptionColumn'] ?? null;
        if ($notesField && ($row[$notesField] ?? false) && $searchConfiguration->getUseNotesField()) {
            $content = BackendUtility::getProcessedValue($table, $notesField, $row[$notesField]);

            $action = (new ResultItemAction($table . '_descriptionColumn'))
                ->setLabel($content)
                ->setIcon($this->iconFactory->getIcon('actions-notebook', Icon::SIZE_SMALL));
            $resultItem->addAction($action);
        }
    }

    protected function appendIdToTitle(ResultItem $resultItem, int $id): void
    {
        $currentTitle = $resultItem->jsonSerialize()['itemTitle'] ?? false;
        if ($currentTitle) {
            $resultItem->setItemTitle(sprintf('%s [%s]', $currentTitle, $id));
        }
    }

}
