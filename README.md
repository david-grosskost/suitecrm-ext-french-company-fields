# Champs des entreprises françaises (`french-company-fields`)

Ajoute aux **comptes** les informations d'une entreprise française, dans un panneau
« Entreprise française » de la fiche : numéro **SIRET**, **code NAF** (activité principale)
et **forme juridique** (SARL, SAS, EI…), avec leurs libellés en français et en anglais.

| Fiche | |
|---|---|
| Version de SuiteCRM | 8.10 (`>=8.10 <9`) |
| Types | `fields`, `lang` |
| Dépend de | — |
| Installée d'office | non |

Le contrôle du SIRET et du code NAF à l'enregistrement est fait par l'extension
[french-company-validation](https://github.com/david-grosskost/suitecrm-ext-french-company-validation),
qui dépend de celle-ci.

## Installer

Sur une instance, par **Administration → Chargeur de module** : l'extension y figure dans
la liste des paquets disponibles, cliquer sur **Installer**. Depuis le terminal, dans
suitecrm-core :

```bash
make extensions@install SLUGS="french-company-fields"
```

La première installation ajoute le panneau à la fiche des comptes. La désinstallation se
fait au même endroit : le panneau est retiré, les données restent en base.

## Documentation

| Page | Contenu |
|---|---|
| [Utilisation](docs/utilisation.md) | les trois champs, le panneau, les données de démo |
| [Fonctionnement](docs/fonctionnement.md) | colonnes, liste des formes juridiques, panneau écrit dans Studio, installation et désinstallation |
| [Développement](docs/developpement.md) | ajouter une forme juridique, vérifier, tester en local, publier |

Les commandes, la CI et le format des extensions sont décrits dans la documentation de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation).
