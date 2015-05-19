# Manuel d'utilisation de la synchronisation #

## Procédure de synchronisation d'une base de terrain avec une base serveur ##


### Préparation de la base serveur ###

  1. importer la base de référence (id négatif) dans la base synchro (vide)
![http://www.onadabase.eu/doc/manuel/images/localhost_onadabase_bdd_bigdump_php_site=local1.png](http://www.onadabase.eu/doc/manuel/images/localhost_onadabase_bdd_bigdump_php_site=local1.png)

  1. vérifier dans spip que l'importation est correct
![http://www.onadabase.eu/doc/manuel/images/localhost_onadabase_spipsync_ecrire__exec=naviguer&id_rubrique=0.png](http://www.onadabase.eu/doc/manuel/images/localhost_onadabase_spipsync_ecrire__exec=naviguer&id_rubrique=0.png)

  1. sauvegarder la base de terrain
  1. nettoyer la base de terrain
    1. supprimer les territoires inutiles
      * soit via l'interface de l'outil
      * soit via le service de suppression : DelRubriqueFrere
    1. supprimer les rubriques de références via le service de suppression : DelRubriqueFrere
    1. nettoyer les erreurs éventuelles via le service de nettoyage : CleanForm

  1. intégrer la base de terrain dans la base de synchro
