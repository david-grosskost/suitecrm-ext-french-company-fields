<?php

// Seeders of french-company-fields, found by the seed runner of suitecrm-core
// when the extension is active (make db@seed), after those of the core.

declare(strict_types=1);

use SuiteCrmExt\FrenchCompanyFields\Seed\AccountsSeeder;

// The classes of the extension, where the Module Loader installed them (the
// seed runner works in public/legacy). In the image, only the seeders are
// copied next to this file: the extension's legacy/ is not there.
$profile = 'custom/include/french-company-fields/CompanyProfile.php';
require_once is_file($profile)
    ? $profile
    : __DIR__ . '/../../../legacy/include/french-company-fields/CompanyProfile.php';
require_once __DIR__ . '/AccountsSeeder.php';

return [
    new AccountsSeeder(),
];
