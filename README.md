# todolist
une application permettant de gérer ses tâches quotidiennes d'une entreprise
# installer le rgestionnaire de dépendances Composer
https://getcomposer.org/download/
# installer la CLI de symfony
```https://symfony.com/download```
# installer Git
https://getcomposer.org/download/
# cloner le projet
  - git clone  https://github.com/AmiarM/bilemo_api.git  ou  
  - télécharger l'archive
# installer les différentes dépendances du projet
```
CD bilemo_api
composer install
```
  # modifier le ficher .env pour ajuster les valeurs:
  - **DATABASE_URL** pour l'accès à la base de données 
  
# Création de la base de données 
```symfony console doctrine:database:create```

# création des tables dans la base de données 
```symfony console doctrine:migrations:migrate```

# charger les fixtures
```symfony console doctrine:fixtures:load```

# lancer le serveur symfony
```symfony serve```

# acceder à  la page d'acceuil
http://localhost:8000
