<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields;

/**
 * Credible French company details for the demo data: the legal form read in
 * the name of the account (« Duval Paysages EURL » → EURL), the NAF code of
 * its industry. Pure functions, tested without SuiteCRM; the seeder
 * french-company-fields/accounts applies them.
 */
final class CompanyProfile
{
    /** Legal forms written at the end of a company name, longest first. */
    private const array SUFFIXES = ['SASU', 'EURL', 'SARL', 'SCOP', 'SAS', 'SCI', 'SNC', 'SA'];

    /**
     * NAF code of a main activity, by SuiteCRM industry (industry_dom key).
     *
     * @var array<string, string>
     */
    public const array NAF = [
        'Apparel' => '14.13Z',
        'Banking' => '64.19Z',
        'Biotechnology' => '72.11Z',
        'Chemicals' => '20.59Z',
        'Communications' => '73.11Z',
        'Construction' => '41.20B',
        'Consulting' => '70.22Z',
        'Education' => '85.59A',
        'Electronics' => '26.11Z',
        'Energy' => '35.11Z',
        'Engineering' => '71.12B',
        'Entertainment' => '59.11A',
        'Environmental' => '38.11Z',
        'Finance' => '69.20Z',
        'Government' => '84.11Z',
        'Healthcare' => '75.00Z',
        'Hospitality' => '55.10Z',
        'Insurance' => '65.12Z',
        'Machinery' => '28.29B',
        'Manufacturing' => '31.09B',
        'Media' => '18.12Z',
        'Not For Profit' => '94.99Z',
        'Recreation' => '93.29Z',
        'Retail' => '47.19B',
        'Shipping' => '52.29A',
        'Technology' => '62.01Z',
        'Telecommunications' => '61.10Z',
        'Transportation' => '49.41A',
        'Utilities' => '36.00Z',
    ];

    /** Activity of an account without industry, or with "Other". */
    public const string NAF_DEFAULT = '82.99Z';

    /**
     * Legal form (fr_legal_form_list key): the one at the end of the name,
     * ASSOCIATION for an association, otherwise SAS from 50 employees and
     * SARL below.
     */
    public static function legalForm(string $name, int $employees): string
    {
        $name = trim($name);
        if (preg_match('/^association\b/iu', $name) === 1) {
            return 'ASSOCIATION';
        }
        foreach (self::SUFFIXES as $suffix) {
            if (preg_match('/\s' . $suffix . '$/u', $name) === 1) {
                return $suffix;
            }
        }

        return $employees >= 50 ? 'SAS' : 'SARL';
    }

    public static function nafCode(string $industry): string
    {
        return self::NAF[$industry] ?? self::NAF_DEFAULT;
    }

    /**
     * The fields of an account that are still empty, filled: never overwrites
     * a value already there (typed in by a user, or an earlier seed).
     *
     * @param array{fr_siret: string, fr_naf_code: string, fr_legal_form: string, name: string, industry: string, employees: int} $account
     *
     * @return array<string, string> Field => value, only those to write
     */
    public static function missing(array $account, string $siret): array
    {
        $values = [
            'fr_siret' => $siret,
            'fr_naf_code' => self::nafCode($account['industry']),
            'fr_legal_form' => self::legalForm($account['name'], $account['employees']),
        ];

        return array_filter(
            $values,
            static fn(string $field): bool => trim($account[$field]) === '',
            ARRAY_FILTER_USE_KEY,
        );
    }
}
