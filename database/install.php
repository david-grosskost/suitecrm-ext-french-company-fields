<?php

// Install script of french-company-fields: puts the panel « Entreprise
// française » (SIRET, NAF code, legal form) at the end of the record view of
// the accounts, in its Studio layout. Run once, at the first activation, after
// Quick Repair has created the fields (suitecrm-core,
// scripts/extension-script.php), in SuiteCRM, as the administrator.
// From then on the panel belongs to Studio: moved, renamed or deleted there,
// it is never put back.

// Its message is returned, not printed (echo is refused by the scanner of
// the Module Loader): the caller shows it.

declare(strict_types=1);

use SuiteCrmExt\FrenchCompanyFields\StudioPanel;

require_once __DIR__ . '/../legacy/include/french-company-fields/StudioPanel.php';
// SuiteCRM's own Studio code: the current directory is public/legacy.
require_once getcwd() . '/modules/ModuleBuilder/parsers/ParserFactory.php';

// The record view of SuiteCRM 8 is the detail view of Studio.
$parser = ParserFactory::getParser(MB_DETAILVIEW, 'Accounts');
if (!$parser instanceof GridLayoutMetaDataParser) {
    throw new RuntimeException('mise en page de la fiche des comptes introuvable');
}

$viewdefs = $parser->_viewdefs;
$templateMeta = is_array($viewdefs['templateMeta'] ?? null) ? $viewdefs['templateMeta'] : [];
$layout = StudioPanel::add(
    is_array($viewdefs['panels'] ?? null) ? $viewdefs['panels'] : [],
    is_array($templateMeta['tabDefs'] ?? null) ? $templateMeta['tabDefs'] : [],
);
if ($layout === null) {
    return 'french-company-fields : panneau déjà dans la mise en page, rien à faire.';
}

$viewdefs['panels'] = $layout['panels'];
$templateMeta['tabDefs'] = $layout['tabDefs'];
$viewdefs['templateMeta'] = $templateMeta;
$parser->_viewdefs = $viewdefs;
// false: the layout as set here, not read from a Studio form.
$parser->handleSave(false);
return 'french-company-fields : panneau « Entreprise française » ajouté à la fiche des comptes (Studio).';
