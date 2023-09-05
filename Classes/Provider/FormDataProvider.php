<?php

declare(strict_types=1);


namespace StudioMitte\LiveSearchExtended\Provider;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Search\LiveSearch\ResultItem;
use TYPO3\CMS\Backend\Search\LiveSearch\ResultItemAction;
use TYPO3\CMS\Backend\Search\LiveSearch\SearchDemand\SearchDemand;
use TYPO3\CMS\Backend\Search\LiveSearch\SearchProviderInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;
use TYPO3\CMS\Form\Service\DatabaseService;

final class FormDataProvider implements SearchProviderInterface
{

    protected LanguageService $languageService;
    protected string $userPermissions;
    protected bool $formsAllowed = false;

//    protected FormPersistenceManagerInterface $formPersistenceManager;

    public function __construct(
        protected readonly LanguageServiceFactory $languageServiceFactory,
        protected readonly IconFactory $iconFactory,
        protected readonly UriBuilder $uriBuilder,
        protected readonly FormPersistenceManager $formPersistenceManager,
        protected DatabaseService $databaseService,
    )
    {
        $this->languageService = $this->languageServiceFactory->createFromUserPreferences($this->getBackendUser());
        $this->formsAllowed = ExtensionManagementUtility::isLoaded('form') && $this->getBackendUser()->check('modules', 'web_FormFormbuilder');
    }

    public function getFilterLabel(): string
    {
        return $this->languageService->sL('LLL:EXT:form/Resources/Private/Language/locallang_module.xlf:mlang_tabs_tab');
    }

    public function count(SearchDemand $searchDemand): int
    {
        if (!$this->formsAllowed) {
            return 0;
        }
        return count($this->get($searchDemand));
    }

    /**
     * @return ResultItem[]
     */
    public function find(SearchDemand $searchDemand): array
    {
        if (!$this->formsAllowed) {
            return [];
        }
        $result = [];
        $remainingItems = $searchDemand->getLimit();
        $offset = $searchDemand->getOffset();
        if ($remainingItems < 1) {
            return [];
        }


        $result = $this->get($searchDemand);

        return $result;
    }

    protected function get(SearchDemand $searchDemand)
    {
        $forms = [];
        $allForms = $this->formPersistenceManager->listForms();
        foreach ($allForms as $form) {
            if (stripos($form['name'], $searchDemand->getQuery()) !== false) {
                $forms[] = $form;
            }
        }

        $items = [];
        foreach ($forms as $form) {
            $actions = [];
            $editLink = $this->getEditLink($form['persistenceIdentifier']);
            if ($editLink !== '') {
                $actions[] = (new ResultItemAction('edit_record'))
                    ->setLabel($this->languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_common.xlf:edit'))
                    ->setIcon($this->iconFactory->getIcon('actions-open', Icon::SIZE_SMALL))
                    ->setUrl($editLink);

            }

            if ($count = $this->getReferenceCount($form)) {
                $text = sprintf('%s: %s', $this->languageService->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:formManager.references'), $count);
                $actions[] = (new ResultItemAction('form-usage'))
                    ->setLabel($text)
                    ->setIcon($this->iconFactory->getIcon('form-number', Icon::SIZE_SMALL))//                    ->setUrl($editLink);
                ;
            }

            $extraData = [
                'table' => 'form',
                'uid' => $form['identifier'],
                'breadcrumb' => $form['persistenceIdentifier'],
            ];

            $items[] = (new ResultItem(self::class))
                ->setItemTitle($form['name'])
                ->setTypeLabel('Form')
                ->setIcon($this->iconFactory->getIcon('content-form', Icon::SIZE_SMALL))
                ->setActions(...$actions)
                ->setExtraData($extraData)
                ->setInternalData([
                    'row' => $form,
                ]);
        }

        return $items;
    }

    protected function canAccessTable(string $tableName): bool
    {
        return true;
    }


    /**
     * Build a backend edit link based on given record.
     */
    protected function getEditLink(string $formIdentifier): string
    {
        $backendUser = $this->getBackendUser();
        if (true) {
            $editLink = (string)$this->uriBuilder->buildUriFromRoute('web_FormFormbuilder.FormEditor_index', [
                'formPersistenceIdentifier' => $formIdentifier,
            ]);
        }
        return $editLink;
    }

    protected function getReferenceCount(array $formDefinition): int
    {
        $allReferencesForFileUid = $this->databaseService->getAllReferencesForFileUid();
        $allReferencesForPersistenceIdentifier = $this->databaseService->getAllReferencesForPersistenceIdentifier();

        $referenceCount = 0;
        if (
            isset($formDefinition['fileUid'])
            && array_key_exists($formDefinition['fileUid'], $allReferencesForFileUid)
        ) {
            $referenceCount = $allReferencesForFileUid[$formDefinition['fileUid']];
        } elseif (array_key_exists($formDefinition['persistenceIdentifier'], $allReferencesForPersistenceIdentifier)) {
            $referenceCount = $allReferencesForPersistenceIdentifier[$formDefinition['persistenceIdentifier']];
        }

        return $referenceCount;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
