<?php

declare(strict_types=1);

namespace App\Extension\FrenchCompanyFields\backend\ViewDefinitions;

use App\FieldDefinitions\Entity\FieldDefinition;
use App\ViewDefinitions\Entity\ViewDefinition;
use App\ViewDefinitions\LegacyHandler\ViewDefinitionMapperInterface;

/**
 * Adds the panel « Entreprise française » (SIRET, NAF code, legal form) to the
 * record view of the accounts, at the end. Added when SuiteCRM builds the view,
 * not written in its layout files: the layout made in Studio stays untouched,
 * and the panel goes away with the extension.
 */
final class FrenchCompanyPanel implements ViewDefinitionMapperInterface
{
    public function getKey(): string
    {
        return 'french-company-fields-panel';
    }

    // Every module: the view of the accounts is recognised in map().
    public function getModule(): string
    {
        return 'default';
    }

    public function map(ViewDefinition $definition, FieldDefinition $fieldDefinition): void
    {
        if (strtolower($definition->getId()) !== 'accounts') {
            return;
        }
        $recordView = PanelBuilder::addTo($definition->getRecordView() ?? [], $fieldDefinition->getVardef() ?? []);
        if ($recordView !== null) {
            $definition->setRecordView($recordView);
        }
    }
}
