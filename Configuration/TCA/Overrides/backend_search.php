<?php

$configuration = new \StudioMitte\LiveSearchExtended\Configuration\Table('be_users');
$configuration
    ->addField(
        (new \StudioMitte\LiveSearchExtended\Configuration\Field('realName', 'actions-user'))
            ->setSkipIfEmpty(true)
            ->setPrefixLabel(false)
    )
    ->addField(
        (new \StudioMitte\LiveSearchExtended\Configuration\Field('email', 'actions-envelope'))
            ->setSkipIfEmpty(true)
            ->setPrefixLabel(false)
    )
    ->addField(
        (new \StudioMitte\LiveSearchExtended\Configuration\Field('usergroup', 'status-user-group-backend'))
            ->setSkipIfEmpty(true)
    )
    ->persist();


$configuration = new \StudioMitte\LiveSearchExtended\Configuration\Table('tt_content');
$configuration
    ->addField(
        (new \StudioMitte\LiveSearchExtended\Configuration\Field('subheader'))
            ->setSkipIfEmpty(true)
    )
    ->persist();

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    $configuration = new \StudioMitte\LiveSearchExtended\Configuration\Table('tx_news_domain_model_news');
    $configuration
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('datetime', 'actions-clock'))
                ->setSkipIfEmpty(true)
                ->setPrefixLabel(false)
        )
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('teaser', 'actions-document'))
                ->setSkipIfEmpty(true)
                ->setPrefixLabel(false)
        )
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('author', 'actions-user'))
                ->setSkipIfEmpty(true)
                ->setPrefixLabel(false)
        )
        ->persist();
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('tt_address')) {
    $configuration = new \StudioMitte\LiveSearchExtended\Configuration\Table('tt_address');
    $configuration
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('first_name', 'actions-user'))
                ->setSkipIfEmpty(true)
        )
        ->addField(
            (new \StudioMitte\LiveSearchExtended\Configuration\Field('last_name', 'actions-user'))
                ->setSkipIfEmpty(true)
        )
        ->persist();
}