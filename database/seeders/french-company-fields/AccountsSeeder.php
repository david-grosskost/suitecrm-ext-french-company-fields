<?php

declare(strict_types=1);

namespace SuiteCrmExt\FrenchCompanyFields\Seed;

use SuiteCrmCore\Seed\Core\AccountsSeeder as CoreAccounts;
use SuiteCrmCore\Seed\Core\VolumeSeeder;
use SuiteCrmCore\Seed\Fr;
use SuiteCrmCore\Seed\SeedContext;
use SuiteCrmCore\Seed\Seeder;
use SuiteCrmExt\FrenchCompanyFields\CompanyProfile;

/**
 * SIRET, NAF code and legal form of the demo accounts: those of core/accounts
 * and of core/volume. Only the empty fields: a value typed in a demo stays.
 * SIRET numbers are fictional, with a valid Luhn key (Fr::siret, from the
 * account's key: the same on every run).
 */
final class AccountsSeeder implements Seeder
{
    public function name(): string
    {
        return 'french-company-fields/accounts';
    }

    public function dependsOn(): array
    {
        return ['core/accounts', 'core/volume'];
    }

    public function run(SeedContext $seed): void
    {
        foreach (self::keys() as $key) {
            $bean = \BeanFactory::getBean('Accounts', SeedContext::id('Accounts', $key));
            if (!$bean instanceof \SugarBean || empty($bean->id)) {
                // A volume account not generated (COUNT smaller): the next are not either.
                if (str_starts_with($key, 'volume-')) {
                    return;
                }

                continue;
            }

            $values = CompanyProfile::missing([
                'fr_siret' => self::text($bean, 'fr_siret'),
                'fr_naf_code' => self::text($bean, 'fr_naf_code'),
                'fr_legal_form' => self::text($bean, 'fr_legal_form'),
                'name' => self::text($bean, 'name'),
                'industry' => self::text($bean, 'industry'),
                'employees' => (int) self::text($bean, 'employees'),
            ], Fr::siret('french-company-fields/' . $key));
            if ($values !== []) {
                $seed->update($bean, $values);
            }
        }
    }

    /**
     * Keys of the seeded accounts, in the order they were written.
     *
     * @return \Generator<int, string>
     */
    private static function keys(): \Generator
    {
        yield from array_keys(CoreAccounts::ACCOUNTS);
        for ($index = 1; $index <= VolumeSeeder::MAX; ++$index) {
            yield 'volume-' . $index;
        }
    }

    private static function text(\SugarBean $bean, string $field): string
    {
        $value = $bean->{$field} ?? '';

        return is_scalar($value) ? (string) $value : '';
    }
}
