<?php

// Champs des entreprises françaises (extension french-company-fields): three
// columns of the accounts table, created by Quick Repair (repairDatabase).
// Prefixed fr_: no clash with a field of SuiteCRM or one made in Studio (_c).

/** @var array<string, array<string, array<mixed>>> $dictionary Set by SuiteCRM */
$dictionary['Account']['fields']['fr_siret'] = [
    'name' => 'fr_siret',
    'vname' => 'LBL_FR_SIRET',
    'type' => 'varchar',
    'len' => 14,
    'comment' => 'SIRET number of the establishment (14 digits)',
    'audited' => true,
    'importable' => 'true',
    'massupdate' => false,
];

$dictionary['Account']['fields']['fr_naf_code'] = [
    'name' => 'fr_naf_code',
    'vname' => 'LBL_FR_NAF_CODE',
    'type' => 'varchar',
    'len' => 6,
    'comment' => 'NAF code of the main activity (e.g. 62.01Z)',
    'importable' => 'true',
    'massupdate' => false,
];

$dictionary['Account']['fields']['fr_legal_form'] = [
    'name' => 'fr_legal_form',
    'vname' => 'LBL_FR_LEGAL_FORM',
    'type' => 'enum',
    'len' => 20,
    'options' => 'fr_legal_form_list',
    'default' => '',
    'comment' => 'Legal form of the company',
    'importable' => 'true',
    'massupdate' => true,
];

// Search and duplicate check by SIRET.
$dictionary['Account']['indices'][] = [
    'name' => 'idx_accnt_fr_siret',
    'type' => 'index',
    'fields' => ['fr_siret'],
];
