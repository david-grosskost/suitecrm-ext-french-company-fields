# Champs des entreprises françaises (`french-company-fields`)

Extension SuiteCRM qui ajoute aux **comptes** les informations d'une entreprise française,
dans un panneau « Entreprise française » de la fiche :

| Champ | Colonne | Contenu |
|---|---|---|
| SIRET | `fr_siret` | numéro d'établissement, 14 chiffres (historisé) |
| Code NAF | `fr_naf_code` | activité principale, ex. `62.01Z` |
| Forme juridique | `fr_legal_form` | liste : EI, EURL, SARL, SASU, SAS, SA, SNC, SCI, SCOP, Association, Autre |

Libellés en français et en anglais. Le contrôle du SIRET (clé de Luhn) n'est pas ici : c'est
l'extension `siret-validation`, qui dépend de celle-ci.

## Comment c'est fait

- **Les champs** (`legacy/Extension/modules/Accounts/Ext/Vardefs/`) : trois colonnes de la
  table `accounts`, préfixées `fr_` pour ne gêner ni SuiteCRM ni un champ fait dans le Studio
  (`_c`), créées par la réparation rapide qui suit l'activation. Un index sur le SIRET.
- **La liste des formes juridiques** (`legacy/Extension/application/Ext/Language/`) : les clés
  sont enregistrées en base, ne jamais en renommer une, en ajouter une autre.
- **Le panneau de la fiche** (`backend/ViewDefinitions/`) : ajouté quand SuiteCRM 8 construit la
  vue, pas écrit dans ses fichiers de mise en page. La mise en page faite dans le Studio reste
  intacte, et le panneau disparaît avec l'extension.

Désactiver l'extension retire les champs et le panneau de l'interface ; les colonnes et leurs
données restent dans la base (la réparation rapide n'en supprime jamais).

## Développer

```bash
make extension@validate   # format : manifeste, composer.json, place des fichiers
make quality@lint         # style du code, scripts
make quality@stan         # analyse statique (PHPStan, niveau max)
make test@unit            # tests unitaires (PHPUnit)
```

Les commandes, la CI et les règles viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit).
