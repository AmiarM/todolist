ToDoList
========

Projet 8 du parcours DA PHP/Symfony de OpenClassrooms
- lien fichier Git : https://github.com/webyves/todolist

### Avant propos
Le projet fonctionne sur PHP 5.5.9 et superieur 
Le projet est basé sur le framework symfony 3.4 (Doctrine, Twig, Swiftmailer, et PhpUnit)
Coté CSS le projet comprend Bootstrap v3.3.7
Coté JS le projet comprend JQuery 3.3.1

### Comment Contribuer au projet :
1) Cloner et Installer le repository sur votre serveur (voir le README.md)
2) Créez une branche A PARTIR DU MASTER a votre nom avec la fonction sur laquelle vous intervenez
3) Ecrivez un Issue sur les modifications que vous allez apporter
4) Ecrivez votre code **EN RESPECTANT LES BONNES PRATIQUES**
5) Ecrivez des Commit Clairs et precis avant de Push votre code
6) Mettez a jour vos issues
7) Faites une PullRequest et attendez sa validation

### Les bonnes pratiques :
# 1) le code
- votre code doit respecter le PSR 2 au minimum
- Votre code doit respecter les standards de code de Symfony ( https://symfony.com/doc/current/contributing/code/standards.html )
- Votre code doit respecter les conventions de code de Symfony ( https://symfony.com/doc/3.4/contributing/code/conventions.html )

# 2) les bundles
- toute installation de bundle PHP doit se faire avec **Composer OBLIGATOIREMENT**

# 3) Git
Merci de respecter un code de bonne conduite et de faire les choses dans l'ordre
- **Nouvelle branche a partir du master** duement nomée
- Commit Correctement commentés
- Issue Correctement commentées et documentées
- **pull Request OBLIGATOIRE**
- **seul le createur du projet _(Moi)_ peu merge** sur le master aprés revision de votre code

# 4) Tests unitaires et fonctionels
- PhpUnit est a votre disposition pour créer vos tests
- Toute nouvelle fonctionalité se doit d'avoir des tests associés
- Merci de respecter un taux de couverture au delà de 70%

# 5) Schemas UML
- dans l'idéal vous ferez les UML (UseCase, Class, Sequence) de vos nouvelles fonctionalités

# 6) Architecture de fichier
- Vous respecterez l'architecture de symfony 3.4 pour vos fichiers PHP ( src\AppBundle\ ... )
- Vos vues devront etre dans un dossier correspondant a la route associé
- Vos CSS devront etre dans des fichiers separés par page 
	- appelés dans vos vues dans le bloc  prevu par exemple
		- `{% block css_files %} <link href="{{ asset('css/main.css') }}" rel="stylesheet"> {% endblock %}`
- Vos JS devront etre dans des fichiers separés par page 
	- appelés dans vos vues dans le bloc prevu par exemple 
		- `{% block js_files %} <script src="{{ asset('js/bootstrap.min.js') }}"></script> {% endblock %}`
