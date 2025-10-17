# Cahier des charges – SAE3 2025
## Plateforme Web de gestion de parc informatique

### Introduction  
Dans le cadre des semestres 3 et 4 du BUT Informatique, les étudiants doivent concevoir et déployer une application web de gestion de parc informatique.  
Ce projet, intitulé **SAE3 – Plateforme Web**, a pour objectif de mettre en œuvre une solution complète de gestion de parc informatique tout en mobilisant les différentes compétences acquises au cours du semestre.  

Les étudiants travaillent en groupe de quatre personnes (exceptionnellement cinq) et doivent livrer, à différentes étapes du projet, des versions fonctionnelles et documentées de leur application.  
L’ensemble du projet sera évalué à la fois sur les aspects techniques, organisationnels et communicationnels.  

---

### Énoncé du projet  

#### Objectif général  
Le bout du projet est de développer une **plateforme web en PHP & MySQL** (ou tout autre système SQL compatible) permettant la **gestion d’un parc informatique**.  
Cette plateforme devra offrir des fonctionnalités adaptées aux différents types d’utilisateurs et garantir la traçabilité des actions par le biais d’un système de **logs d’activité**.  

#### Description fonctionnelle  
La plateforme devra proposer une page d’accueil avec un texte explicatif et une vidéo présentant les fonctionnalités principales.  
Elle permettra ensuite, selon le type d’utilisateur connecté, d’accéder à différentes fonctionnalités :  

##### Utilisateurs de la plateforme  
**1. Administrateur système**  
L’administrateur système a pour rôle de consulter les journaux d’activité de la plateforme. Il ne participe pas à la gestion du parc informatique.  
Ses identifiants sont imposés : `sysadmin` / `sysadmin`.  

**2. Administrateur web**  
L’administrateur web est responsable de la gestion des techniciens et des informations globales. Il peut :  
- créer et supprimer des techniciens,  
- définir les systèmes d’exploitation et les constructeurs disponibles,  
- consulter et verrouiller la liste du matériel mis au rebut.  
Ses identifiants sont obligatoires : `adminweb` / `adminweb`.  

**3. Technicien**  
Les techniciens sont créés par l’administrateur web. Ils peuvent :  
- consulter et modifier le parc informatique,  
- ajouter des machines via un formulaire ou un fichier CSV,  
- supprimer ou réactiver des machines,  
- exporter les données au format CSV.  
Un premier technicien doit être présent dès le départ : `tech1` / `tech1`.  

**4. Visiteur**  
Le visiteur n’a pas besoin de s’authentifier. Il peut simplement consulter une partie de l’inventaire sans modification possible.  

#### Structure des données  
Deux fichiers de données au format CSV seront fournis :  

- **Fichier UC** : contient les informations sur les unités centrales, avec les colonnes suivantes :  
  `NAME, SERIAL, MANUFACTURER, MODEL, TYPE, CPU, RAM_MB, DISK_GB, OS, DOMAIN, LOCATION, BUILDING, ROOM, MACADDR, PURCHASE_DATE, WARRANTY_END`  

- **Fichier Moniteurs** : pour les écrans, avec les colonnes suivantes :  
  `SERIAL, MANUFACTURER, MODEL, SIZE_INCH, RESOLUTION, CONNECTOR, ATTACHED_TO`  

---

### Priorités des livrables

#### Développement Web (R301)  
Création de l’interface web statique, navigation fictive, et intégration progressive du back-end.  

#### Analyse (R303)  
Rédaction du présent cahier des charges et modélisation des données (MCD/MLD).  

#### Qualité de développement (R304)  
Mise en place d’une documentation claire et respect des conventions de code.  

#### Programmation Système & Réseau (R305/R306)  
Installation du serveur web sur le Raspberry Pi et configuration réseau/SSH.  

#### SQL dans un langage de programmation (R307)  
Implémentation des requêtes SQL pour la gestion du parc informatique.  

#### Cryptographie et Sécurité (R309)  
Sécurisation des sessions, des mots de passe et des accès.  

#### Communication professionnelle et Anglais (R312/R313)  
Création d’un logo et justification du choix.  
Préparation d’un exposé oral en anglais sur le projet.  

#### Management et Droit (R310/R311)  
Gestion de projet, respect du cadre légal et des droits numériques.  








