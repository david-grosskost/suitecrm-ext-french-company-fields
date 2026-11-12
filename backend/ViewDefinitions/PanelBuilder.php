<?php

declare(strict_types=1);

namespace App\Extension\FrenchCompanyFields\backend\ViewDefinitions;

/**
 * The panel of the French company fields, in the format of a SuiteCRM 8
 * record view (panels → rows → cols), without SuiteCRM: tested on its own.
 */
final class PanelBuilder
{
    /** Key of the panel, also the key of its label (Accounts language files). */
    public const string KEY = 'lbl_fr_company_panel';

    /** Two fields a row, as in the other panels; '' is an empty cell. */
    public const array ROWS = [
        ['fr_siret', 'fr_naf_code'],
        ['fr_legal_form', ''],
    ];

    /**
     * The record view with the panel at the end; null when nothing is to be
     * changed: no panel to add to, panel already there, or fields missing from
     * the vardefs (Quick Repair not run yet).
     *
     * @param array<mixed> $recordView
     * @param array<mixed> $vardefs
     *
     * @return array<mixed>|null
     */
    public static function addTo(array $recordView, array $vardefs): ?array
    {
        $panels = $recordView['panels'] ?? null;
        if (!is_array($panels) || $panels === []) {
            return null;
        }
        foreach ($panels as $panel) {
            if (is_array($panel) && ($panel['key'] ?? null) === self::KEY) {
                return null;
            }
        }

        $rows = [];
        foreach (self::ROWS as $names) {
            $cols = [];
            foreach ($names as $name) {
                if ($name === '') {
                    $cols[] = ['name' => '', 'fieldDefinition' => []];

                    continue;
                }
                $vardef = $vardefs[$name] ?? null;
                if (!is_array($vardef)) {
                    return null;
                }
                $cols[] = [
                    'name' => $name,
                    'label' => $vardef['vname'] ?? '',
                    'type' => $vardef['type'] ?? 'varchar',
                    'fieldDefinition' => $vardef,
                ];
            }
            $rows[] = ['cols' => $cols];
        }

        $panels[] = ['key' => self::KEY, 'rows' => $rows];
        $recordView['panels'] = $panels;

        return $recordView;
    }
}
