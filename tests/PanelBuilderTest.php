<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields\Tests;

use App\Extension\FrenchCompanyFields\backend\ViewDefinitions\PanelBuilder;
use PHPUnit\Framework\TestCase;

final class PanelBuilderTest extends TestCase
{
    /** @var array<string, array<string, string>> */
    private const array VARDEFS = [
        'name' => ['name' => 'name', 'vname' => 'LBL_NAME', 'type' => 'name'],
        'fr_siret' => ['name' => 'fr_siret', 'vname' => 'LBL_FR_SIRET', 'type' => 'varchar'],
        'fr_naf_code' => ['name' => 'fr_naf_code', 'vname' => 'LBL_FR_NAF_CODE', 'type' => 'varchar'],
        'fr_legal_form' => ['name' => 'fr_legal_form', 'vname' => 'LBL_FR_LEGAL_FORM', 'type' => 'enum'],
    ];

    /** @var array<string, mixed> */
    private const array RECORD_VIEW = [
        'panels' => [['key' => 'lbl_account_information', 'rows' => [['cols' => [['name' => 'name']]]]]],
    ];

    public function testThePanelIsAddedAtTheEndWithItsFields(): void
    {
        $view = PanelBuilder::addTo(self::RECORD_VIEW, self::VARDEFS);

        self::assertIsArray($view);
        self::assertIsArray($view['panels']);
        self::assertCount(2, $view['panels']);
        self::assertSame(self::RECORD_VIEW['panels'][0], $view['panels'][0], 'les panneaux existants ne bougent pas');

        $panel = $view['panels'][1];
        self::assertIsArray($panel);
        self::assertSame(PanelBuilder::KEY, $panel['key']);
        self::assertSame(
            [['fr_siret', 'fr_naf_code'], ['fr_legal_form', '']],
            array_map(
                static fn(mixed $row): array => array_column(is_array($row) && is_array($row['cols'] ?? null) ? $row['cols'] : [], 'name'),
                is_array($panel['rows'] ?? null) ? $panel['rows'] : [],
            ),
        );
    }

    public function testTheCellsCarryTheFieldDefinitions(): void
    {
        $view = PanelBuilder::addTo(self::RECORD_VIEW, self::VARDEFS);

        self::assertIsArray($view);
        self::assertIsArray($view['panels']);
        $panel = $view['panels'][1];
        self::assertIsArray($panel);
        self::assertIsArray($panel['rows']);
        $row = $panel['rows'][0];
        self::assertIsArray($row);
        self::assertIsArray($row['cols']);
        self::assertSame(
            ['name' => 'fr_siret', 'label' => 'LBL_FR_SIRET', 'type' => 'varchar', 'fieldDefinition' => self::VARDEFS['fr_siret']],
            $row['cols'][0],
        );
    }

    public function testNothingChangesWhenThePanelIsAlreadyThere(): void
    {
        $view = PanelBuilder::addTo(self::RECORD_VIEW, self::VARDEFS);
        self::assertIsArray($view);

        self::assertNull(PanelBuilder::addTo($view, self::VARDEFS));
    }

    public function testNothingChangesWhileTheFieldsAreNotInstalled(): void
    {
        $vardefs = self::VARDEFS;
        unset($vardefs['fr_legal_form']);

        self::assertNull(PanelBuilder::addTo(self::RECORD_VIEW, $vardefs), 'champs absents : Quick Repair pas encore lancé');
    }

    public function testNothingChangesWithoutPanels(): void
    {
        self::assertNull(PanelBuilder::addTo([], self::VARDEFS));
        self::assertNull(PanelBuilder::addTo(['panels' => []], self::VARDEFS));
    }

    public function testThePanelGetsItsTabWhenTheViewUsesTabs(): void
    {
        $tabDefs = ['LBL_ACCOUNT_INFORMATION' => PanelBuilder::TAB];
        $view = PanelBuilder::addTo(self::RECORD_VIEW + ['templateMeta' => ['useTabs' => true, 'tabDefs' => $tabDefs]], self::VARDEFS);

        self::assertIsArray($view);
        self::assertIsArray($view['templateMeta']);
        self::assertSame(
            $tabDefs + ['LBL_FR_COMPANY_PANEL' => PanelBuilder::TAB],
            $view['templateMeta']['tabDefs'],
            'onglet ajouté à la fin, les autres ne bougent pas',
        );
    }

    public function testNoTabIsAddedWhenTheViewHasNoTabs(): void
    {
        $view = PanelBuilder::addTo(self::RECORD_VIEW + ['templateMeta' => ['maxColumns' => '2']], self::VARDEFS);

        self::assertIsArray($view);
        self::assertSame(['maxColumns' => '2'], $view['templateMeta'], 'sans onglets, tous les panneaux s\'affichent');
    }
}
