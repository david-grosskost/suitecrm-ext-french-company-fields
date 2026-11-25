# Utilisation

## Ce qui change

La fiche d'un compte a un panneau **« Entreprise française »** avec trois champs :

| Champ | Contenu |
|---|---|
| SIRET | numéro d'établissement, 14 chiffres |
| Code NAF | activité principale, ex. `62.01Z` |
| Forme juridique | liste : EI, EURL, SARL, SASU, SAS, SA, SNC, SCI, SCOP, Association, Autre |

Les champs se saisissent comme les autres, en édition de la fiche. Comme tout champ de
SuiteCRM, ils peuvent être ajoutés aux listes, aux filtres et aux rapports. Le SIRET est
historisé : ses changements apparaissent dans l'historique des modifications du compte.

## Réglages

Le panneau appartient à **Studio** dès qu'il est posé : l'administrateur peut le déplacer, le
renommer, en retirer un champ ou le supprimer (**Administration → Studio → Comptes → Mises en
page → Vue détaillée**). Il n'est jamais remis de force.

## Données de démo

`make db@seed` (ou le workflow Seed d'une démo) remplit les champs **vides** des comptes de
démo (`core/accounts` et `core/volume`) :

- un SIRET fictif mais avec une clé de contrôle valide, toujours le même pour un compte donné ;
- un code NAF selon le secteur d'activité du compte ;
- la forme juridique lue dans le nom (« Duval Paysages EURL »), sinon SAS dès 50 salariés,
  SARL en dessous.

Une valeur déjà saisie n'est jamais remplacée.

## Limites

- Aucun contrôle de saisie ici : un SIRET faux s'enregistre. Pour le refuser, installer
  french-company-validation.
- Pas de recherche automatique des informations de l'entreprise (annuaire INSEE).
