# Fonctionnement

## Fichiers

| Fichier | Rôle |
|---|---|
| `legacy/Extension/modules/Accounts/Ext/Vardefs/french-company-fields.php` | les trois champs des comptes, et un index sur le SIRET |
| `legacy/Extension/modules/Accounts/Ext/Language/` | leurs libellés (français, anglais) |
| `legacy/Extension/application/Ext/Language/` | la liste des formes juridiques (`fr_legal_form_list`) |
| `legacy/include/french-company-fields/StudioPanel.php` | le panneau dans une mise en page de Studio : l'ajouter, en retirer les champs |
| `legacy/include/french-company-fields/CompanyProfile.php` | des informations crédibles pour la démo : forme juridique lue dans le nom, code NAF par secteur |
| `database/install.php`, `uninstall.php` | pose et retrait du panneau |
| `database/seeders/french-company-fields/` | les données de démo |
| `tests/` | tests des champs, du panneau et des informations de démo |

## Données en base

Trois colonnes de la table `accounts`, créées par la réparation rapide qui suit
l'installation :

| Colonne | Type |
|---|---|
| `fr_siret` | texte, 14 caractères, historisé, indexé (`idx_accnt_fr_siret`) |
| `fr_naf_code` | texte, 6 caractères |
| `fr_legal_form` | liste (`fr_legal_form_list`), 20 caractères |

Le préfixe `fr_` évite tout conflit avec SuiteCRM et avec un champ créé dans Studio (suffixe
`_c`). Les clés de la liste des formes juridiques sont enregistrées en base : **ne jamais en
renommer une**, en ajouter une autre.

## Installation et désinstallation

- **Première installation** (`database/install.php`) : écrit une fois, avec le code de Studio,
  le panneau « Entreprise française » à la fin de la vue détaillée des comptes, deux champs
  par ligne. Si un des trois champs est déjà dans la mise en page, rien n'est ajouté. Le
  panneau appartient ensuite à Studio : une mise à jour de l'extension ne repasse pas ce
  script.
- **Désinstallation** (`database/uninstall.php`) : retire ses champs de la mise en page, où
  qu'ils soient, et le panneau s'il est vide, pour que la mise en page ne nomme plus de champ
  inconnu. Les colonnes et leurs données restent en base (la réparation rapide n'en supprime
  jamais). Réinstallée, l'extension remet le panneau.

## Points d'attention

- Les données de démo chargent `CompanyProfile` là où le Chargeur de module l'a installée
  (`custom/include/french-company-fields/`) : dans l'image, seuls les seeders sont copiés.
- Les scripts d'installation renvoient leur message au lieu de l'afficher : le scanner du
  Chargeur de module refuse `echo`.
- La vue détaillée de Studio est aussi la fiche de SuiteCRM 8 : le panneau y apparaît sans
  autre réglage.
