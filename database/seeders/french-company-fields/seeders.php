<?php

// Seeders of french-company-fields, found by the seed runner of suitecrm-core
// when the extension is active (make db@seed), after those of the core.

declare(strict_types=1);

use SuiteCrmExt\FrenchCompanyFields\Seed\AccountsSeeder;

require_once __DIR__ . '/../../../legacy/include/french-company-fields/CompanyProfile.php';
require_once __DIR__ . '/AccountsSeeder.php';

return [
    new AccountsSeeder(),
];
