Sommaire :
<img src="1.png"
     alt="Markdown Monster icon"
     style="float: left; margin-right: 10px;" />
Les notions d'authentification et d'autorisation 1
L'authentification 1
L'autorisation 2
Processus général 2
Mettre en place le composant de sécurité de Symfony : 3
Création de l'authentification 3
Création du formulaire d'inscription 4
Le security.yaml 4
Firewalls : 4
Encoders : 4
Providers : 5
Access control : 6
L’inscription 7
Le UserType.php 7
Formulaire d’inscription : 7
Le RegistrationController.php 8
L’entité USER : 8
Créez un formulaire de connexion 8
Créez un contrôleur(securityController) 9
Le userFormAuthenticator : 9

L’authentification

Les notions d'authentification et d'autorisation
L'authentification
L'authentification est le processus qui va définir qui vous êtes, en tant que visiteur. L'enjeu est vraiment très simple : soit vous ne vous êtes pas identifié sur le site et vous êtes un anonyme, soit vous vous êtes identifié (via le formulaire d'identification ou via un cookie « Se souvenir de moi ») et vous êtes un membre du site. C'est ce que la procédure d'authentification va déterminer. Ce qui gère l'authentification dans Symfony s'appelle un firewall.
Ainsi vous pourrez sécuriser des parties de votre site internet juste en forçant le visiteur à être un membre authentifié. Si le visiteur l'est, le firewall va le laisser passer, sinon il le redirigera sur la page d'identification. Cela se fera donc dans les paramètres du firewall.
L'autorisation
L'autorisation est le processus qui va déterminer si vous avez le droit d'accéder à la ressource (la page) demandée. Il agit donc après le firewall. Ce qui gère l'autorisation dans Symfony s'appelle l’Access control.
Par exemple, un membre identifié aura accès à la liste des tasks, mais ne peut pas accéder à la liste des utilisateurs. Seuls les membres disposant des droits d'administrateur le peuvent, ce que l’Access control va vérifier.
Processus général
Lorsqu'un utilisateur tente d'accéder à une ressource protégée, le processus est finalement toujours le même, le voici :

1. Un utilisateur veut accéder à une ressource protégée ;
2. Le firewall redirige l'utilisateur au formulaire de connexion ;
3. L'utilisateur soumet ses informations d'identification (par exemple login et mot de passe) ;
4. Le firewall authentifie l'utilisateur ;
5. L'utilisateur authentifié renvoie la requête initiale ;
6. Le contrôle d'accès vérifie les droits de l'utilisateur, et autorise ou non l'accès à la ressource protégée
   Mettre en place le composant de sécurité de Symfony :

Création de l'authentification
Pour créer l'authentification, nous utiliserons la commande

Cette commande va lancer un assistant qui va vous demander de renseigner les informations suivantes :
• Le type d'authentification (avec ou sans formulaire de connexion)
• Le nom de la classe contenant l'authentification (UserFormAuthenticator dans mon exemple)
• Le nom du contrôleur qui contiendra les routes de connexion et déconnexion (SecurityController)
• La création ou non d'une route de déconnexion (/logout)
Après l'exécution de la commande, les fichiers suivants auront été créés ou modifiés
• config/packages/security.yaml
• src/Controller/SecurityController.php
• src/Security/UserFormAuthenticator.php
• templates/security/login.html.twig
Création du formulaire d'inscription
Pour créer le formulaire d'inscription sur le site, nous utiliserons la commande

Cette commande va lancer un assistant qui va vous demander de renseigner les informations suivantes :
• Veut-on ajouter une annotation “@UniqueEntity” dans notre classe Users pour les rendre uniques
• Veut-on envoyer un email aux utilisateurs pour activer leur compte
• Veut-on connecter automatiquement les utilisateurs après leur inscription
Après l'exécution de la commande, les fichiers suivants auront été créés ou modifiés
• config/packages/security.yaml
• src/Controller/RegistrationController.php
• src/Entity/User.php
• src/Form/UserType.php
Nous pourrons ensuite modifier les fichiers en fonction du contexte de notre site.
Le security.yaml
Firewalls :
Permet de définir comment protéger notre application, quelle sont les parties de l’application qu’on veut protéger et comment les protéger.
On peut utiliser plusieurs méthodes tel que le formulaire de login…

Encoders :
On peut gérer la sécurité des données notamment les mots de passe Utilisant un encoder pour les hacher.

Grâce à l’interface UserPasswordHasherInterface on peut encoder nos mot de passe

Providers :
Permet de définir ou se trouve les utilisateurs :
• Une base de données
• Un annuaire d’entreprise
• Un fichier texte….

Access control :
Le contrôle d'accès vérifie que l'utilisateur a le(s) rôle(s) requis pour accéder au contenu demandé. Les contrôles d'accès peuvent être utilisés :
• À partir du fichier de configuration, comme c'est le cas ici. Pour cela, il faut appliquer règles sur des URL. Par exemple, on peut sécuriser toutes les URL commençant par /users

• Dans les contrôleurs directement

L’inscription
Le UserType.php
Formulaire d’inscription :

Le RegistrationController.php

L’entité USER :
On va créer une entité utilisateur qui va stocker nos utilisateurs en base de données.
Une seule contrainte pour la création d'un utilisateur est d'implémenter l'interface UserInterface du composant Security.
L’implémentation requière l'ajout des méthode suivantes :
• getRoles : Retourne les rôles de l'utilisateur.
• GetPassword : Récupération du mot de passe encodé.
• GetSalt : Retourne le sel pour l'encodage du mot de passe.
• GetUsername : Récupère le nom de l'utilisateur.
• EraseCredentials : Efface les données sensibles comme le mot de passe.
Créez un formulaire de connexion
Pour pouvoir authentifier nos utilisateurs nous avons besoin de mettre en place un formulaire de connexion qui demande à l'utilisateur son adresse mail et un mot de passe.

Créez un contrôleur(securityController)
Contient une
Route : /login_check
Récupère les erreurs d’authentification
Récupère le dernier utilisateur connecté et l’injecter dans le formulaire
Au niveau du logout on a rien du tout .

Le userFormAuthenticator :
Toute la logique d’authentification se situe au niveau de l’authenticator
Qui aura comme rôle d’initialiser l’authentification c’est à dire il vérifie si L’authenticator vérifie si la requête est une requête d’authentification ensuite il va commencer le début du processus
Créez de la méthode d'authentification
Pour créer une méthode d'authentification, nous allons utiliser une extension du composant Security appelée Guard. Changement majeur par rapport aux anciennes versions de Symfony, il est maintenant très facile de créer sa méthode d'authentification ou encore "authenticator".
Pour cela, nous allons créer une classe qui étend AbstractFormLoginAuthenticator du composant Guard. Il faudra implémenter et compléter quelques fonctions :
• supports() : la fonction définit dans quelles conditions la classe sera appelée.
• getCredentials() : retourne les éléments d'information d'authentification.
• getUser() : retourne un utilisateur au sens Symfony (instance de UserInterface du composant Security).
• checkCredentials() : contrôle à la connexion que les informations d'authentification sont valides.
• onAuthenticationSuccess() : décide que faire dans le cas où l'utilisateur est bien authentifié, généralement une redirection vers une URL donnée.
• getLoginUrl() : définit l’URL du formulaire de connexion, dans notre cas security_login .

La méthode Supprots de l’abstractLoginFormAuthenticator : vérifier si la méthode est POST et l’url correspond à l’url de login.
En cas d’échec de connexion on redirige vers la page de connexion

En cas de succès on redirige vars la page d’accueil :

La méthode authenticate renvoie un objet de type passeport
L’authenticator récupère l’email depuis la requête
Il le sauvegarde dans la session s’il Ya une erreur d’authentification ça permet de remettre l’email dans le champ et ensuite il génère un passeport qui va être vérifié par différentes méthodes
Le badge va nous donner des informations sur l’utilisateur notamment son identifiant dans notre système on a choisi l’email, on aura également un autre badge qui contiendra le mot de passe et
Un badge supplémentaire pour vérifier le token CSRF
Les listeners vont venir écouter le processus d’authentification pour récupérer le passeport et vérifier les badges
