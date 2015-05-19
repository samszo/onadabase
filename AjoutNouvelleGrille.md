#Procédure pour ajouter une nouvelle grille

Cette page décrit la procédure a effectuer pour ajouter une nouvelle grille dans ONADABASE.
Les éléments **texte** doivent être remplacés par les valeurs adéquates

  1. SPIP : créer une nouvelle grille
  1. onadabase/xul/chrome/content/param/onadabase.xml : ajouter un bloc de description de la grille
```
	<Param nom="AddObj-Type de la grille-" id="*identifiant spip de la grille*" >
		<NoVerif>*message d'alerte si aucune rubrique n'est sélectionnée*</NoVerif>
		<TitreFormSaisi>*titre du formulaire affiché*</TitreFormSaisi>
	</Param>
```
  1. onadabase/param/SolAcc.xml : ajouter un bloc de description de la requête pour construire l'arbre des éléments de la grille
```
	<Query fonction="GetTreeChildren_*type de la grille*">
		<col tag="id" parse="Integer"/>
		<col tag="titre"/>				
		<select>SELECT r.id_rubrique id, r.titre, r.id_parent parent </select>
		<from> FROM spip_rubriques r
INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
INNER JOIN spip_forms_donnees d ON d.id_donnee = da.id_donnee AND d.id_form = *identifiant de la grille*
		</from>
		<where> WHERE id_parent = -parent- </where>				
		<js evt="onselect" function="GetTreeSelect('tree-param0-',['idRub','libRub'],[0,1]);ChargeTabboxFromAjax('idRub','FormSaisi','Ligne');"/>
	</Query>
```
  1. onadabase/param/SolAcc.xml : ajouter un bloc de description de la requête pour construire le formulaire de saisie
```
	<Query fonction="Grille_GetXulTabForm*type de la grille*">
		<select>SELECT a.id_article id, a.titre</select>
		<from> 
			FROM spip_rubriques r
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
			INNER JOIN spip_forms_donnees_articles fda ON fda.id_article = a.id_article
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fda.id_donnee AND fd.id_form = *identifiant de la grille*
		</from>
		<where> WHERE r.id_rubrique = -id- </where>
		<dst>Art</dst>
	</Query>
```
