<?php


if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    $configuration = new \StudioMitte\LiveSearchExtended\Configuration\Table('tx_news_domain_model_news');
    $configuration
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('datetime', 'actions-clock'))
                ->skipIfEmpty()
                ->skipPrefixLabel()
        )
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('teaser', 'actions-document'))
                ->skipIfEmpty()
                ->skipPrefixLabel()
        )
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('author', 'actions-user'))
                ->skipIfEmpty()
                ->skipPrefixLabel()
        )
        ->persist();
}