<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields\Tests;

use SuiteCrmExt\FrenchCompanyFields\StudioPanel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The definitions the extension gives SuiteCRM (fields, labels, list), read as
 * SuiteCRM reads them: each file included with the array it fills.
 */
final class DefinitionsTest extends TestCase
{
    private const string LEGACY = __DIR__ . '/../legacy/Extension';
    private const string SLUG = 'french-company-fields';
    private const array FIELDS = ['fr_siret', 'fr_naf_code', 'fr_legal_form'];

    /**
     * The array a definition file fills.
     *
     * @return array<mixed>
     */
    private static function load(string $file, string $variable): array
    {
        $dictionary = $mod_strings = $app_list_strings = [];
        require self::LEGACY . '/' . $file;
        $values = compact('dictionary', 'mod_strings', 'app_list_strings');

        return is_array($values[$variable] ?? null) ? $values[$variable] : [];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function fields(): array
    {
        $dictionary = self::load('modules/Accounts/Ext/Vardefs/' . self::SLUG . '.php', 'dictionary');
        $account = is_array($dictionary['Account'] ?? null) ? $dictionary['Account'] : [];
        $fields = is_array($account['fields'] ?? null) ? $account['fields'] : [];

        /** @var array<string, array<string, mixed>> $fields */
        return $fields;
    }

    public function testTheThreeFieldsAreDefinedWithTheirOwnName(): void
    {
        $fields = self::fields();

        self::assertSame(self::FIELDS, array_keys($fields));
        foreach ($fields as $name => $field) {
            self::assertSame($name, $field['name'] ?? null);
            self::assertStringStartsWith('fr_', $name, 'préfixe fr_ : pas de conflit avec SuiteCRM ni le Studio');
        }
    }

    public function testSiretAndNafCodeFitTheirFormat(): void
    {
        $fields = self::fields();

        self::assertSame(14, $fields['fr_siret']['len'] ?? null, 'un SIRET a 14 chiffres');
        self::assertSame(6, $fields['fr_naf_code']['len'] ?? null, 'un code NAF a 6 caractères (62.01Z)');
        self::assertSame('fr_legal_form_list', $fields['fr_legal_form']['options'] ?? null);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function languages(): iterable
    {
        yield 'français' => ['fr_FR'];
        yield 'anglais' => ['en_us'];
    }

    #[DataProvider('languages')]
    public function testEveryFieldHasALabel(string $language): void
    {
        $labels = self::load('modules/Accounts/Ext/Language/' . $language . '.' . self::SLUG . '.php', 'mod_strings');

        foreach (self::fields() as $name => $field) {
            $vname = $field['vname'] ?? null;
            self::assertIsString($vname, "$name : vname manquant");
            self::assertNotEmpty($labels[$vname] ?? null, "$name : libellé $vname manquant en $language");
        }
        self::assertNotEmpty(
            $labels[StudioPanel::KEY] ?? null,
            "titre du panneau manquant en $language",
        );
    }

    #[DataProvider('languages')]
    public function testTheLegalFormsAreTheSameInEveryLanguage(string $language): void
    {
        $lists = self::load('application/Ext/Language/' . $language . '.' . self::SLUG . '.php', 'app_list_strings');
        $french = self::load('application/Ext/Language/fr_FR.' . self::SLUG . '.php', 'app_list_strings');

        self::assertIsArray($lists['fr_legal_form_list'] ?? null);
        self::assertIsArray($french['fr_legal_form_list'] ?? null);
        self::assertSame(
            array_keys($french['fr_legal_form_list']),
            array_keys($lists['fr_legal_form_list']),
            'mêmes formes juridiques (clés) dans toutes les langues',
        );
        self::assertArrayHasKey('', $lists['fr_legal_form_list'], 'une valeur vide : forme juridique pas encore connue');
        foreach (array_keys($lists['fr_legal_form_list']) as $key) {
            self::assertLessThanOrEqual(20, strlen((string) $key), "clé $key : la colonne fait 20 caractères");
        }
    }
}
