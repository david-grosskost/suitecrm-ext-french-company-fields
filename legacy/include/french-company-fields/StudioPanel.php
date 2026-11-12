<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields;

/**
 * The panel « Entreprise française » in a Studio layout, as Studio holds it
 * while editing (GridLayoutMetaDataParser): panels → rows → field names, with
 * '(empty)' and '(filler)' for blank cells. Pure functions, tested without
 * SuiteCRM; database/install.php and uninstall.php apply them.
 *
 * Written once in the layout of the record view, the panel then belongs to
 * Studio: moved, renamed, emptied or deleted there, it is never put back.
 */
final class StudioPanel
{
    /** Key of the panel, also the key of its title (Accounts language files). */
    public const string KEY = 'LBL_FR_COMPANY_PANEL';

    /**
     * Two fields a row, as in the other panels of the accounts. '(filler)':
     * a blank cell kept as such ('(empty)' would let the field span the row).
     */
    public const array ROWS = [
        ['fr_siret', 'fr_naf_code'],
        ['fr_legal_form', self::FILLER],
    ];

    /** Its tab, when the layout shows its panels as tabs. */
    public const array TAB = ['newTab' => true, 'panelDefault' => 'expanded'];

    public const string EMPTY = '(empty)';
    public const string FILLER = '(filler)';

    /**
     * The layout with the panel at the end; null when nothing is to change:
     * the panel or one of its fields is already in the layout (put there by
     * Studio, or by an earlier install).
     *
     * @param array<array-key, mixed> $panels
     * @param array<array-key, mixed> $tabDefs
     *
     * @return array{panels: array<array-key, mixed>, tabDefs: array<array-key, mixed>}|null
     */
    public static function add(array $panels, array $tabDefs): ?array
    {
        if (isset($panels[self::KEY]) || self::fieldsIn($panels) !== []) {
            return null;
        }

        $panels[self::KEY] = self::ROWS;
        // With tabs, only the panels listed in tabDefs show; without (no
        // tabDefs), every panel shows: adding one would turn tabs on.
        if ($tabDefs !== []) {
            $tabDefs[self::KEY] = self::TAB;
        }

        return ['panels' => $panels, 'tabDefs' => $tabDefs];
    }

    /**
     * The layout without the fields of the extension, wherever they are
     * (fields that SuiteCRM will no longer know): each one becomes a blank
     * cell, a row left blank goes, and so does a panel left without rows,
     * with its tab. Null when none of them is in the layout.
     *
     * @param array<array-key, mixed> $panels
     * @param array<array-key, mixed> $tabDefs
     *
     * @return array{panels: array<array-key, mixed>, tabDefs: array<array-key, mixed>}|null
     */
    public static function remove(array $panels, array $tabDefs): ?array
    {
        if (self::fieldsIn($panels) === []) {
            return null;
        }

        $fields = self::fields();
        foreach ($panels as $key => $rows) {
            if (!is_array($rows)) {
                continue;
            }
            $kept = [];
            $touched = false;
            foreach ($rows as $row) {
                // Only the rows that showed a field of the extension change.
                if (!is_array($row) || self::fieldsIn([[$row]]) === []) {
                    $kept[] = $row;

                    continue;
                }
                $touched = true;
                $blank = true;
                foreach ($row as $column => $cell) {
                    if (in_array($cell, $fields, true)) {
                        $row[$column] = $cell = self::EMPTY;
                    }
                    $blank = $blank && in_array($cell, [self::EMPTY, self::FILLER], true);
                }
                if (!$blank) {
                    $kept[] = $row;
                }
            }
            if (!$touched) {
                continue;
            }
            if ($kept === []) {
                unset($panels[$key], $tabDefs[strtoupper((string) $key)]);
            } else {
                $panels[$key] = $kept;
            }
        }

        return ['panels' => $panels, 'tabDefs' => $tabDefs];
    }

    /** @return list<string> The fields of the panel. */
    public static function fields(): array
    {
        return array_values(array_diff(array_merge(...self::ROWS), [self::EMPTY, self::FILLER]));
    }

    /**
     * The fields of the extension that the layout already shows.
     *
     * @param array<array-key, mixed> $panels
     *
     * @return list<string>
     */
    public static function fieldsIn(array $panels): array
    {
        $found = [];
        foreach ($panels as $rows) {
            foreach (is_array($rows) ? $rows : [] as $row) {
                foreach (is_array($row) ? $row : [] as $cell) {
                    if (is_string($cell) && in_array($cell, self::fields(), true)) {
                        $found[] = $cell;
                    }
                }
            }
        }

        return array_values(array_unique($found));
    }
}
