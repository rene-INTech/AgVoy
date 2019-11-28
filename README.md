# AgVoy

##Commandes utiles

###Liste des chambres
La liste des chambres au format `description prix (propriétaire)` est obtenue avec la commande:
```bash
bin/console list:rooms
```

###Liste des administrateurs
La liste des administrateurs est donnée par la commande 
```bash
bin/console list:admins
```

###Liste des utilisateurs
La liste des utilisateurs, avec leurs rôles au format `username: roles` est obtenue avec la commande
```bash
bin/console list:users
```

###Ajouter un administrateur
Un utilisateur ayant déjà un compte peut être promu administrateur grâce à la commande
```
bin/console add:admin <username> 
```
>*Une gestion des administrateurs plus complète est disponible [en ligne](http://localhost:8000/backoffice/admins)*
***
##Auteurs du projet
 - VATRE Flore
 - LAMIAUX Rémi