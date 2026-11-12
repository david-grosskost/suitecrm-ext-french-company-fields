<?php

// Uninstall script of french-company-fields: takes its fields out of the
// Studio layout of the record view of the accounts, wherever they were put, so
// that the layout names no field SuiteCRM no longer knows; the panel goes with
// them once empty. Run when the extension is turned off, before its files are
// taken out (suitecrm-core, scripts/extension-script.php), in SuiteCRM, as the
// administrator. The values stay in the database (columns fr_*).

declare(strict_types=1);

use SuiteCrmExt\FrenchCompanyFields\StudioPanel;

require_once __DIR__ . '/../legacy/include/french-company-fields/StudioPanel.php';
// SuiteCRM's own Studio code: the current directory is public/legacy.
require_once getcwd() . '/modules/ModuleBuilder/parsers/ParserFactory.php';

$parser = ParserFactory::getParser(MB_DETAILVIEW, 'Accounts');
if (!$parser instanceof GridLayoutMetaDataParser) {
    throw new RuntimeException('mise en page de la fiche des comptes introuvable');
}

$viewdefs = $parser->_viewdefs;
$templateMeta = is_array($viewdefs['templateMeta'] ?? null) ? $viewdefs['templateMeta'] : [];
$layout = StudioPanel::remove(
    is_array($viewdefs['panels'] ?? null) ? $viewdefs['panels'] : [],
    is_array($templateMeta['tabDefs'] ?? null) ? $templateMeta['tabDefs'] : [],
);
if ($layout === null) {
    echo "  french-company-fields : aucun de ses champs dans la mise en page, rien à retirer.\n";

    return;
}

$viewdefs['panels'] = $layout['panels'];
$templateMeta['tabDefs'] = $layout['tabDefs'];
$viewdefs['templateMeta'] = $templateMeta;
$parser->_viewdefs = $viewdefs;
$parser->handleSave(false);
echo "  french-company-fields : champs retirés de la fiche des comptes (Studio).\n";
