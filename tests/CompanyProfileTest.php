<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SuiteCrmExt\FrenchCompanyFields\CompanyProfile;

final class CompanyProfileTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int, string}>
     */
    public static function names(): iterable
    {
        yield 'EURL en fin de nom' => ['Duval Paysages EURL', 3, 'EURL'];
        yield 'SARL en fin de nom' => ['Vallee & Gimenez SARL', 120, 'SARL'];
        yield 'SAS en fin de nom' => ['Guerin & Monnier SAS', 4, 'SAS'];
        yield 'SASU, pas SAS' => ['Martin Conseil SASU', 1, 'SASU'];
        yield 'SA en fin de nom' => ['Tanguy & Lesage SA', 30, 'SA'];
        yield 'association' => ['Association Les Jardins Partagés', 6, 'ASSOCIATION'];
        yield 'sans forme, 50 salariés ou plus' => ['Transports Garnier Logistique', 140, 'SAS'];
        yield 'sans forme, moins de 50 salariés' => ['Menuiserie Perrin', 11, 'SARL'];
        yield 'SA au milieu du nom : rien' => ['SA Conseil Numérique', 5, 'SARL'];
    }

    #[DataProvider('names')]
    public function testTheLegalFormIsReadInTheName(string $name, int $employees, string $form): void
    {
        self::assertSame($form, CompanyProfile::legalForm($name, $employees));
    }

    public function testEveryLegalFormIsInTheList(): void
    {
        $app_list_strings = [];
        require __DIR__ . '/../legacy/Extension/application/Ext/Language/fr_FR.french-company-fields.php';
        $list = $app_list_strings['fr_legal_form_list'] ?? [];
        self::assertIsArray($list);

        foreach (['EURL', 'SARL', 'SAS', 'SASU', 'SA', 'SNC', 'SCI', 'SCOP', 'ASSOCIATION'] as $form) {
            self::assertArrayHasKey($form, $list);
        }
    }

    public function testTheNafCodeFollowsTheIndustry(): void
    {
        self::assertSame('62.01Z', CompanyProfile::nafCode('Technology'));
        self::assertSame(CompanyProfile::NAF_DEFAULT, CompanyProfile::nafCode('Other'));
        self::assertSame(CompanyProfile::NAF_DEFAULT, CompanyProfile::nafCode(''));
        foreach ([...CompanyProfile::NAF, CompanyProfile::NAF_DEFAULT] as $code) {
            self::assertMatchesRegularExpression('/^\d{2}\.\d{2}[A-Z]$/', $code, 'format NAF : 62.01Z');
        }
    }

    public function testOnlyTheEmptyFieldsAreFilled(): void
    {
        $account = [
            'fr_siret' => '',
            'fr_naf_code' => '47.11F',
            'fr_legal_form' => ' ',
            'name' => 'Duval Paysages EURL',
            'industry' => 'Other',
            'employees' => 3,
        ];

        self::assertSame(
            ['fr_siret' => '90000000000006', 'fr_legal_form' => 'EURL'],
            CompanyProfile::missing($account, '90000000000006'),
            'le code NAF saisi reste',
        );
    }

    public function testNothingIsFilledWhenEverythingIsThere(): void
    {
        $account = [
            'fr_siret' => '12345678900011',
            'fr_naf_code' => '62.01Z',
            'fr_legal_form' => 'SAS',
            'name' => 'Studio Pixel Production',
            'industry' => 'Entertainment',
            'employees' => 14,
        ];

        self::assertSame([], CompanyProfile::missing($account, '90000000000006'));
    }
}
