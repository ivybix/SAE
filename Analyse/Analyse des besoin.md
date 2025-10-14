# Document d'Analyse des Besoins – Plateforme de Gestion de Parc Informatique (SAÉ S3)

## 1. Objectif, Portée et Contexte

### 1.1. Objectif de l'Application

La plateforme web, développée en **PHP & MySQL**, a pour but la **gestion complète et centralisée d'un parc informatique** (moniteurs et unités centrales) pour la société. Elle doit proposer différents modules basés sur le rôle de l'utilisateur et assurer la traçabilité complète via des **Journaux d'Activités (Logs)**.

### 1.2. Portée et Exclusions du Système

| Périmètre | Inclus | Exclu |
| :--- | :--- | :--- |
| **Périmètre Fonctionnel** | Gestion de l'inventaire, gestion des comptes techniciens, journalisation des actions. | Gestion des licences logicielles, système de ticketing pour la maintenance. |
| **Périmètre Technique** | Installation et configuration du serveur web (Apache) et SGBD (MySQL) sur RPi4. | Configuration du routeur réseau global (uniquement l'accès local au RPi4 est concerné). |

### 1.3. Glossaire 

| Terme | Définition |
| :--- | :--- |
| **UC** | Unité Centrale : Ordinateur de bureau (Tour). |
| **Moniteur** | Écran (dispositif d'affichage) associé ou non à une UC. |
| **Liste du Rebut** | Base de données des matériels déclarés hors service ou mis au rebut. |
| **Admin Web** | Administrateur de l'application web (gestion des techniciens). |
| **Admin Système** | Administrateur du serveur (RPi4) ayant accès aux logs de l'application. |
| **Logs** | Journaux d'activités : Fichiers enregistrant toutes les actions significatives dans la plateforme (création, suppression, modification). |

---

## 2. Acteurs et Rôles

Le système doit gérer quatre profils d'utilisateurs distincts pour définir la sécurité et les droits d'accès.

| Acteur | Rôle Principal | Accès Principal | Pré-Conditions (État avant l'action) |
| :--- | :--- | :--- | :--- |
| **Visiteur** | Consultation partielle de l'inventaire. | Accès libre ou par compte invité. | Aucun |
| **Technicien** | Gestion et manipulation des données d'inventaire. | Authentification requise (login/mot de passe). | Compte créé par l'Admin Web et actif. |
| **Admin Web** | Gestion des techniciens et des données de référence (OS, Constructeurs). | Authentification requise (login/mot de passe). | Rôle Admin Web assigné. |
| **Admin Système** | Surveillance de l'activité du système. | Authentification requise (login/mot de passe). | Rôle Admin Système assigné. |

---

## 3. Exigences Fonctionnelles (EF)

Les EF décrivent ce que le système doit faire. Elles sont identifiées pour garantir la traçabilité.

### 3.1. Gestion de l'Inventaire (Technicien)

| ID | Exigence | Pré-Condition (Clé) | Critère de Validation |
| :--- | :--- | :--- | :--- |
| **EF-1.1** | Le Technicien doit pouvoir **consulter et modifier** les informations d'un matériel dans le parc. | L'utilisateur est authentifié. La machine existe dans l'inventaire actif. | La modification est enregistrée en BDD et visible immédiatement. Un log est généré. |
| **EF-1.2** | Le système doit permettre l'**ajout d'une seule machine** via un formulaire structuré. | L'utilisateur est authentifié. | Le formulaire doit contrôler le format des données entrées (séries, dates). |
| **EF-1.3** | Le système doit permettre l'**ajout d'une série de machines** via un fichier CSV. | L'utilisateur est authentifié. Le fichier CSV doit respecter les en-têtes et le format requis. | Le système doit gérer les erreurs de formatage ou les doublons lors de l'import CSV. |
| **EF-1.4** | Le Technicien doit pouvoir **supprimer une machine** pour la placer dans la **liste du rebut**. | L'utilisateur est authentifié. La machine existe dans l'inventaire actif. | La machine est transférée dans la liste Rebut, elle n'est plus visible dans l'inventaire actif. |
| **EF-1.5** | Le système doit permettre de **consulter la liste du rebut** et **changer le statut** du matériel remis en service. | L'utilisateur est authentifié. | Le statut de la machine doit être réversible (Rebut vers Actif). |
| **EF-1.6** | Le Technicien doit pouvoir **exporter une liste** (Inventaire ou Rebut) au format CSV. | L'utilisateur est authentifié. | L'export CSV doit inclure les champs clés et respecter la mise en forme. |

### 3.2. Administration (Admin Web)

| ID | Exigence | Pré-Condition (Clé) | Critère de Validation |
| :--- | :--- | :--- | :--- |
| **EF-2.1** | L'Admin Web doit pouvoir **créer** et **supprimer** des comptes Techniciens. | L'utilisateur est authentifié en tant qu'Admin Web. | La création de compte doit nécessiter un mot de passe haché (voir ENF 4.3.2). |
| **EF-2.2** | Le système doit permettre de créer des **informations réutilisables** (noms des OS, constructeurs) via une interface dédiée. | L'utilisateur est authentifié en tant qu'Admin Web. | Les nouvelles références doivent apparaître dans les listes déroulantes des formulaires d'ajout/modification. |
| **EF-2.3** | L'Admin Web doit pouvoir **consulter la liste du rebut**. | L'utilisateur est authentifié en tant qu'Admin Web. | L'accès à cette liste doit être restreint aux Admin Web et Techniciens. |
| **EF-2.4** | L'Admin Web doit pouvoir **bloquer la liste du rebut** pour une future exportation (état définitif). | L'utilisateur est authentifié en tant qu'Admin Web. | Un bouton ou une action spécifique doit être disponible pour changer l'état du rebut en "Bloqué". |

### 3.3. Journalisation (Admin Système)

| ID | Exigence | Pré-Condition (Clé) | Critère de Validation |
| :--- | :--- | :--- | :--- |
| **EF-3.1** | Un **fichier de log** inhérent à toutes les actions de modification et d'administration doit être créé. | Une action modifiant les données (EF-1.x ou EF-2.x) doit avoir été exécutée. | Le fichier de log doit être consultable et inclure l'horodatage et l'ID de l'acteur pour chaque action. |
| **EF-3.2** | L'Admin Système doit pouvoir **consulter les différents journaux d'activités** de la plateforme via une interface dédiée. | L'utilisateur est authentifié en tant qu'Admin Système. | L'interface doit permettre le tri ou le filtrage des logs (par date, par utilisateur). |
| **EF-3.3** | L'Admin Système **n'accède pas** aux fonctionnalités de gestion (ajout, modification) du parc informatique. | L'utilisateur est authentifié en tant qu'Admin Système. | Toute tentative d'accès aux modules EF-1.x ou EF-2.x par l'Admin Système doit être refusée. |

---

## 4. Exigences Non Fonctionnelles (ENF)

### 4.1. Exigences Techniques et de Déploiement

| ID | Exigence | Catégorie |
| :--- | :--- | :--- |
| **ENF-1.1** | L'application doit être développée en **PHP** et utiliser une base de données **MySQL** (ou autre serveur SQL). | Technologie |
| **ENF-1.2** | Le déploiement doit être réalisé sur un serveur porté par un **Raspberry Pi 4 (RPi4)**. | Contrainte Matérielle |
| **ENF-1.3** | Le temps de chargement pour la consultation de l'inventaire complet ne doit pas dépasser **5 secondes** sur le RPi4. | Performance |
| **ENF-1.4** | L'installation sur le RPi4 doit inclure le système, un **serveur web** (Apache) et un **serveur SGBD** (MySQL) que le groupe devra configurer. | Configuration |

### 4.2. Exigences d'Utilisabilité et Ergonomie

| ID | Exigence | Catégorie |
| :--- | :--- | :--- |
| **ENF-2.1** | L'interface doit être conçue en respectant la **Charte Graphique** du projet (logo, couleurs, police). | Esthétique/Charte |
| **ENF-2.2** | L'interface doit garantir la **lisibilité** des données (alignement des données, contraste suffisant). | Accessibilité/Clarté |
| **ENF-2.3** | La page d'accueil doit proposer un texte et une **vidéo explicative** des fonctionnalités. | Documentation utilisateur |

### 4.3. Exigences de Sécurité et de Contraintes

| ID | Exigence | Catégorie |
| :--- | :--- | :--- |
| **ENF-3.1** | Les accès SSH au RPi4 doivent être sécurisés, incluant l'identifiant obligatoire `sae2025` et le mot de passe `!sae2025!`. | Sécurité Système |
| **ENF-3.2** | Le système doit gérer les mots de passe des utilisateurs par **hachage fort**. | Sécurité Application |
| **ENF-3.3** | Les identifiants obligatoires de l'Admin Web (`adminweb/adminweb`) et du Technicien de base (`techi/tech1`) doivent être fonctionnels et **ne pas pouvoir être modifiés** par l'utilisateur. | Contrainte de Login |
| **ENF-3.4** | Le *hacker* le matériel d'un autre groupe est formellement interdit et sera lourdement sanctionné. | Contrainte Légale/Éthique |

### 4.4. Exigences de Livraison 

* **ENF-4.1** : Le code source et la documentation doivent être mis à disposition sur un compte **Github**.
* **ENF-4.2** : Le **cahier des charges** doit être rédigé à partir de cette Analyse des Besoins.
* **ENF-4.3** : Le projet fera l'objet d'un **exposé en anglais**.

---

## 5. Cas d'Utilisation (Scénarios d'Interaction)

Ces scénarios décrivent les actions clés réalisées par les différents utilisateurs sur la plateforme, organisées par rôle.

### Niveaux des Cas d'Utilisation

Le cas d'utilisation cité ci-dessous sont au niveau **Utilisateur**, ce qui représente une tâche complète et mesurable pour l'acteur. (temps de prendre un café)

| ID | Tâche d'Utilisation Simplifiée | Objectif | Acteurs Primaires | Niveau |
| :--- | :--- | :--- | :--- | :--- |
| **0.1** | **S'authentifier** | Ouvrir une session pour accéder aux fonctionnalités sécurisées. | Tous (sauf Visiteur) | Sous-fonction |
| **1.1** | Consulter l'Inventaire | Visualiser et rechercher la liste des Unités Centrales et des Moniteurs. | Technicien, Visiteur | Utilisateur |
| **1.2** | Ajouter une Machine (Formulaire) | Enregistrer une seule nouvelle machine dans l'inventaire. | Technicien |  Utilisateur |
| **1.3** | Importer de Matériel (CSV) | Ajouter rapidement une série de machines à l'inventaire via un fichier CSV. | Technicien | Utilisateur |
| **1.4** | Modifier les Infos Machine | Mettre à jour les détails techniques ou la localisation d'une machine. | Technicien | Utilisateur |
| **1.5** | Déclarer un Matériel "Rebut" | Mettre une machine hors service et la transférer dans la liste du rebut. | Technicien |  Utilisateur |
| **1.6** | Gérer le Statut du Rebut | Consulter le rebut et changer le statut d'une machine remise en service. | Technicien |  Utilisateur |
| **1.7** | Exporter les Données | Télécharger l'inventaire ou la liste du rebut au format CSV. | Technicien |  Utilisateur |
| **2.1** | Gérer les Comptes Techniciens | Créer et supprimer des comptes Technicien dans la base de données. | Admin Web |  Utilisateur |
| **2.2** | Configurer les Listes de Référence | Ajouter des options réutilisables (noms d'OS, Constructeurs) pour les formulaires. | Admin Web |  Utilisateur |
| **2.3** | Finaliser la Liste du Rebut | Consulter le rebut et **bloquer la liste** en vue d'une exportation administrative future. | Admin Web |  Utilisateur |
| **3.1** | Consulter les Journaux d'Activités (Logs) | Accéder et parcourir les différents journaux d'activités de la plateforme. | Admin Système |  Utilisateur |
| **3.2** | Authentification du Sysadmin | Se connecter pour accéder à l'interface de consultation des logs. | Admin Système | Sous-fonction |


