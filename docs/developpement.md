# Développement

Les commandes `make`, la CI et le format des extensions viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation), à la
version de `composer.json`. Cette page ne décrit que ce qui est propre à l'extension.

## Ajouter une forme juridique

L'ajouter à `fr_legal_form_list` dans les deux fichiers de langue
(`legacy/Extension/application/Ext/Language/`), avec une **nouvelle clé** : les clés existantes
sont enregistrées en base. Si les données de démo doivent la reconnaître dans un nom,
compléter `CompanyProfile` et son test.

## Vérifier

```bash
make extension@validate && make quality@lint && make quality@stan && make test@unit
make package@build && make package@scan
```

## Tester sur une instance locale

1. `make package@build`, puis sur l'instance locale de suitecrm-core, **Administration →
   Chargeur de module** : **Choisir un fichier**
   (`build/french-company-fields-<version>.zip`), **Télécharger**, **Installer**.
2. Ouvrir un compte : le panneau « Entreprise française » est en bas de la fiche.
3. Dans suitecrm-core, `make db@seed` : les comptes de démo ont un SIRET, un code NAF et une
   forme juridique.
4. Désinstaller puis réinstaller : le panneau part puis revient, les valeurs restent.
5. Dans suitecrm-core : `make test@regression`.

## Publier une version

Pull request relue, puis merge (squash) : release-please ouvre la pull request
« chore(main): release x.y.z ». La merger crée le tag, la release et son paquet.
suitecrm-core la récupère avec
`make composer@update PACKAGES="david-grosskost/suitecrm-ext-french-company-fields"`.

Mettre à jour `README.md` et `docs/` dans la même pull request que le changement qu'ils
décrivent.
