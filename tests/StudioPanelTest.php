<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields\Tests;

use PHPUnit\Framework\TestCase;
use SuiteCrmExt\FrenchCompanyFields\StudioPanel;

/**
 * The panel in a Studio layout, in the format Studio holds it while editing:
 * panels → rows → field names, '(empty)' for a blank cell.
 */
final class StudioPanelTest extends TestCase
{
    /** The record view of the accounts, shortened. */
    private const array PANELS = [
        'lbl_account_information' => [['name', 'assigned_user_name'], ['website', 'phone_office']],
        'LBL_PANEL_ADVANCED' => [['account_type', 'industry']],
    ];

    private const array TAB_DEFS = [
        'LBL_ACCOUNT_INFORMATION' => StudioPanel::TAB,
        'LBL_PANEL_ADVANCED' => StudioPanel::TAB,
    ];

    public function testThePanelIsAddedAtTheEndWithItsTab(): void
    {
        $layout = StudioPanel::add(self::PANELS, self::TAB_DEFS);

        self::assertNotNull($layout);
        self::assertSame(
            self::PANELS + [StudioPanel::KEY => [['fr_siret', 'fr_naf_code'], ['fr_legal_form', '(empty)']]],
            $layout['panels'],
            'les autres panneaux ne bougent pas',
        );
        self::assertSame(StudioPanel::KEY, array_key_last($layout['tabDefs']));
        self::assertSame(StudioPanel::TAB, $layout['tabDefs'][StudioPanel::KEY]);
    }

    public function testNoTabIsAddedToALayoutWithoutTabs(): void
    {
        $layout = StudioPanel::add(self::PANELS, []);

        self::assertNotNull($layout);
        self::assertSame([], $layout['tabDefs'], 'sans onglets, tous les panneaux s\'affichent');
    }

    public function testNothingIsAddedWhenThePanelIsThere(): void
    {
        $layout = StudioPanel::add(self::PANELS, self::TAB_DEFS);
        self::assertNotNull($layout);

        self::assertNull(StudioPanel::add($layout['panels'], $layout['tabDefs']));
    }

    public function testNothingIsAddedWhenAFieldWasPlacedInStudio(): void
    {
        $panels = self::PANELS;
        $panels['LBL_PANEL_ADVANCED'][] = ['fr_siret', '(empty)'];

        self::assertNull(StudioPanel::add($panels, self::TAB_DEFS), 'le Studio a déjà la main');
    }

    public function testRemovingTakesThePanelAndItsTabAway(): void
    {
        $layout = StudioPanel::add(self::PANELS, self::TAB_DEFS);
        self::assertNotNull($layout);

        self::assertSame(
            ['panels' => self::PANELS, 'tabDefs' => self::TAB_DEFS],
            StudioPanel::remove($layout['panels'], $layout['tabDefs']),
        );
    }

    public function testRemovingTakesTheFieldsOutWhereverStudioPutThem(): void
    {
        $panels = self::PANELS;
        $panels['LBL_PANEL_ADVANCED'] = [['account_type', 'fr_naf_code'], ['fr_siret', '(empty)']];
        // Renamed in Studio, with another field added: the panel stays for it.
        $panels['lbl_editview_panel1'] = [['fr_legal_form', 'description']];

        $layout = StudioPanel::remove($panels, self::TAB_DEFS);

        self::assertNotNull($layout);
        self::assertSame(
            self::PANELS['lbl_account_information'],
            $layout['panels']['lbl_account_information'],
            'un panneau sans les champs ne bouge pas',
        );
        self::assertSame([['account_type', '(empty)']], $layout['panels']['LBL_PANEL_ADVANCED']);
        self::assertSame([['(empty)', 'description']], $layout['panels']['lbl_editview_panel1']);
        self::assertSame([], StudioPanel::fieldsIn($layout['panels']));
    }

    public function testNothingIsRemovedWhenNoFieldIsInTheLayout(): void
    {
        self::assertNull(StudioPanel::remove(self::PANELS, self::TAB_DEFS));
    }
}
