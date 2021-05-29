Ouvrez le fichier ".env", vous trouverez cette ligne de code :

DATABASE_URL="mysql://bd_user:bd_pwd@localhost/bd_Contacts"

Vous changez "bd_user" , "bd_pwd" par votre nom d'utilisateur et votre mot de passe de la base de données.

bd_Contacts est le nom de la base de données.

quand vous avez mis vos propres informations d'identification, ouvrez la ligne de commande dans le dossier du projet et exécutez cette commande : :

"php bin/console doctrine:database:create"

vérifiez si la base de données est créée sur phpmyadmin

si elle est créée, lancez cette commande : :

"php bin/console doctrine:schema:create"

cette commande devrait créer la table des contacts


Ouvrez src/controller/api....controller.php , qui contient toutes les fonctions de l'api. 

****************

ENSUITE VOUS POUVEZ TESTER L'API AVEC POSTMAN PAR EXEMPLE
(bien sûr, vous devez lancer le serveur en utilisant " symfony server:start ")


URL :

pour tous les contacts : api/contact/
pour un contact : api/contact/id
pour supprimer un contact : api/contact/delete/id
pour ajouter un contact : api/contact/add 
    ET ceci le format json d'entrée : 
    {
    "email": "",
    "nom": "",
    "prenom": "",
    "adresse": "",
    "telephone": "",
    "age" : ""
    }
pour mettre à jour un contact : api/contact/update/id 
    ET ceci est le format d'entrée json : (vous pouvez mettre un seul champ à mettre à jour) 
    {
    "email": "",
    "nom": "",
    "prenom": "",
    "adresse": "",
    "telephone": "",
    "age" : ""
    }

