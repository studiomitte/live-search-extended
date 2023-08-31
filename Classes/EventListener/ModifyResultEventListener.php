<?php
declare(strict_types=1);

namespace StudioMitte\LiveSearchExtended\EventListener;

use StudioMitte\LiveSearchExtended\Configuration\Table;
use StudioMitte\LiveSearchExtended\LiveSearch\NewsDatabaseRecordProvider;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Search\Event\ModifyResultItemInLiveSearchEvent;
use TYPO3\CMS\Backend\Search\LiveSearch\DatabaseRecordProvider;
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

        $table = $resultItem->getExtraData()['table'] ?? null;
        $searchConfiguration = Table::createFromTCA($table);
        if ($searchConfiguration && $searchConfiguration->isValid()) {
            $row = $resultItem->getInternalData()['row'] ?? null;
            if (!$row) {
                return;
            }

            foreach ($searchConfiguration->getFields() as $field) {
                if (!isset($row[$field->field])) {
                    continue;
                }
                $content = BackendUtility::getProcessedValue($table, $field->field, $row[$field->field], 0, false, false, $row['uid']);
                if (!$content && $field->isSkipIfEmpty()) {
                    continue;
                }
                $label = $this->languageService->sL(BackendUtility::getItemLabel($table, $field->field));
                $text = $field->isPrefixLabel() ? sprintf('%s: %s', $label, $content) : $content;
                $action = (new ResultItemAction($table . '_' . $field->field))
                    ->setLabel($text)
                    ->setIcon($field->icon ? $this->iconFactory->getIcon($field->icon, Icon::SIZE_SMALL) : null);
                $resultItem->addAction($action);
            }

            $notesField = $GLOBALS['TCA'][$table]['ctrl']['descriptionColumn'] ?? null;
            if ($notesField && ($row[$notesField] ?? false) && $searchConfiguration->getUseNotesField()) {
                $content = BackendUtility::getProcessedValue($table, $notesField, $row[$notesField]);

                $action = (new ResultItemAction($table . '_descriptionColumn'))
                    ->setLabel($content)
                    ->setIcon($this->iconFactory->getIcon('actions-notebook', Icon::SIZE_SMALL));
                $resultItem->addAction($action);
            }
        }
    }
}
