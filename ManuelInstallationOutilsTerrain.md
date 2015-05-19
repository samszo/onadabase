# Manuel d'installation des outils de terrain #
## Installation des utilitaires ##

  * WAMP (2.0) : pour le serveur web et la base de donnée local
cf. http://www.wampserver.com/presentation.php

  * GoogleEarth : pour la manipulation des KML (facultatif)

  * Firefox : pour lancer l'application

## Installation de l'application Onadabase ##

  * Installation des fichiers de l’application
    1. Copier le répertoire onadabase dans le dossier c:/wamp/www/

  * Installation de la base de données :
    1. dans phpMyadmin, créer une nouvelle base de données (onadabase)
    1. comme la base de données est volumineuse, on préférera utiliser bigdump pour importer la base (c:/wamp/www/bigdump/bigdump.php). Pour cela, placer la base au format sql dans c:/wamp/www/bigdump, puis lancer la page http://localhost/bigdump/bigdump.php et cliquer le fichier à importer.

  * Installation de spip
    1. Aller avec votre navigateur sur http://localhost/spip/ecrire/ et suivre les différentes étapes. Login admin : root sans mot de passe. Base à importer : onadabase.

  * Vérifier l'activation des privilèges
    1. suivre ce tutorial : http://xulfr.org/wiki/ApplisWeb/ActiverLesPrivileges

## Application Onadabase ##

  * Tester l'application
    1. Pour lancer l'application aller sur http://localhost/onadabase/
    1. le login est le mot de passe sont ceux que vous avez défini lors de l'installation SPIP