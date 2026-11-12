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
- **Le panneau de la fiche** (`database/install.php`) : écrit **une fois** dans la mise en
  page de la fiche des comptes, avec Studio, à la première activation. Il appartient ensuite au
  Studio : le déplacer, le renommer, le vider ou le supprimer, il n'est jamais remis. Si un des
  trois champs est déjà dans la mise en page, rien n'est ajouté.
- **Les données de démo** (`database/seeders/`) : `make db@seed` remplit les champs vides des
  comptes de démo (`core/accounts` et `core/volume`) : SIRET fictif mais avec une clé de Luhn
  valide, code NAF selon le secteur, forme juridique lue dans le nom (« Duval Paysages EURL »),
  sinon SAS dès 50 salariés, SARL en dessous. Une valeur déjà saisie n'est jamais remplacée.

Désactiver l'extension (`database/uninstall.php`) retire ses champs de la mise en page de la
fiche, où qu'ils soient, et le panneau s'il est vide ; les colonnes et leurs données restent
dans la base (la réparation rapide n'en supprime jamais). Réactivée, elle remet le panneau.

## Développer

```bash
make extension@validate   # format : manifeste, composer.json, place des fichiers
make quality@lint         # style du code, scripts
make quality@stan         # analyse statique (PHPStan, niveau max)
make test@unit            # tests unitaires (PHPUnit)
```

Les commandes, la CI et les règles viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit).
