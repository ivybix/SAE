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

## Catalogue Complet des Fiches Détaillées de Cas d'Utilisation (SAE Parc Informatique) 🛡️

Ce catalogue intègre le Scénario Nominal, les Scénarios Alternatifs (chemin différent menant au succès) et les Scénarios d'Exception (échec ou annulation).

---

### 1. S'authentifier

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Ouvrir une Session |
| **Contexte d'utilisation :** | L'utilisateur fournit ses identifiants pour se connecter aux zones sécurisées de la plateforme. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Sous-fonction** |
| **Acteur principal :** | Tous (sauf Visiteur) |
| **Pré-condition :** | L'utilisateur est sur la page de connexion et possède des identifiants valides. |
| **Garanties minimales :** | L'événement de connexion est consigné dans le log. L'utilisateur non autorisé est refusé. |
| **Garanties en cas de succès :** | L'utilisateur est connecté et redirigé vers son tableau de bord. |

#### Scénario Nominal

1. L'Acteur accède à la page de connexion.
2. L'Acteur saisit son identifiant et son mot de passe, puis soumet le formulaire.
3. Le Système vérifie la validité des identifiants et le rôle de l'Acteur.
4. Le Système accorde l'accès, enregistre l'événement, et affiche la page d'accueil de l'acteur.

#### Scénarios Alternatifs

Aucun scénario alternatif significatif à ce niveau.

#### Scénarios d'Exception

* **E1 : Identifiants Invalides** : Si la vérification des identifiants (Étape 3) échoue, le Système affiche un message d'erreur et invite l'Acteur à recommencer (retour à l'Étape 1).

---

### 2. Consulter l'Inventaire

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Consulter et Rechercher l'Inventaire |
| **Contexte d'utilisation :** | L'Acteur (Technicien ou Visiteur) visualise la liste du matériel actif (Unités Centrales et Moniteurs). |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien, Visiteur |
| **Pré-condition :** | L'Acteur a un accès autorisé. |
| **Garanties minimales :** | Les données affichées correspondent toujours au statut actuel dans la base. |
| **Garanties en cas de succès :** | L'Acteur dispose de la liste d'équipements recherchée, conforme à son niveau d'accès. |

#### Scénario Nominal

1. L'Acteur accède à la page de consultation de l'Inventaire.
2. Le Système affiche la liste des équipements (UC et Moniteurs) visibles pour l'Acteur.
3. L'Acteur saisit des critères de recherche/filtrage (ex : `LOCATION`, `SERIAL`).
4. Le Système filtre les résultats de l'inventaire en temps réel et affiche la liste réduite.

#### Scénarios Alternatifs

* **A1 : Affichage Initial Vide** : Si l'inventaire ne contient aucun enregistrement, le Système affiche un message l'indiquant au lieu de la liste (Étape 2).

#### Scénarios d'Exception

* **E1 : Critères Non Pertinents** : Si les critères de recherche ne correspondent à aucune machine, le Système affiche un message "Aucun résultat trouvé" (Étape 4).

---

### 3. Ajouter une Machine (Formulaire)

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Enregistrer une Machine dans l'Inventaire (via Formulaire) |
| **Contexte d'utilisation :** | Le Technicien ajoute un seul nouvel équipement (UC ou Moniteur) à l'inventaire actif. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. Les listes de référence (OS, Constructeurs) sont configurées. |
| **Garanties minimales :** | Les machines ajoutées reçoivent un statut "Actif" par défaut. |
| **Garanties en cas de succès :** | Le nouvel équipement est enregistré dans l'inventaire principal et l'action est loguée. |

#### Scénario Nominal

1. Le Technicien accède à la fonction "Ajouter une Machine".
2. Le Technicien remplit les champs obligatoires et utilise les listes de référence.
3. Le Technicien soumet le formulaire.
4. Le Système valide les données (unicité du `SERIAL`, complétude).
5. Le Système insère la nouvelle machine dans l'Inventaire.
6. Le Système confirme le succès de l'enregistrement.

#### Scénarios Alternatifs

* **A1 : Utilisation des Listes de Référence** : Au lieu de saisir manuellement les champs (ex: Constructeur), le Technicien sélectionne une valeur prédéfinie dans une liste déroulante fournie par le Système (Étape 2).

#### Scénarios d'Exception

* **E1 : Numéro de Série Dupliqué** : Si la vérification (Étape 4) révèle que le `SERIAL` existe déjà, le Système rejette l'entrée et affiche un message d'erreur. Le CU échoue.
* **E2 : Champs Obligatoires Manquants** : Si des champs obligatoires sont vides (Étape 4), le Système marque les erreurs et invite le Technicien à corriger l'entrée (retour à l'Étape 2).

---

### 4. Importer du Matériel (CSV)

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Importer une Série de Machines (via Fichier CSV) |
| **Contexte d'utilisation :** | Le Technicien ajoute un lot de matériel à l'inventaire en téléchargeant un fichier CSV formaté. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. Le fichier CSV est formaté avec les en-têtes requis. |
| **Garanties minimales :** | Les enregistrements avec un `SERIAL` existant ou un format invalide sont rejetés sans interrompre l'importation. |
| **Garanties en cas de succès :** | Toutes les machines valides du fichier sont ajoutées à l'inventaire. Un rapport d'importation est généré. |

#### Scénario Nominal

1. Le Technicien accède à la fonction "Importation CSV" et télécharge le fichier.
2. Le Système lit et valide le fichier (structure, en-têtes, unicité).
3. Le Système insère les enregistrements valides dans la base de données.
4. Le Système affiche un rapport de l'importation (succès/échecs) et enregistre l'action.

#### Scénarios Alternatifs

Aucun scénario alternatif significatif à ce niveau.

#### Scénarios d'Exception

* **E1 : Fichier Non Conforme** : Si le fichier n'est pas au format CSV ou si les en-têtes sont incorrects (Étape 2), le Système rejette l'ensemble du fichier et affiche un message d'erreur.
* **E2 : Erreurs Partielles d'Enregistrement** : Si certaines lignes contiennent des erreurs (`SERIAL` dupliqué, données incorrectes), le Système ignore ces lignes, les liste dans le rapport d'échecs, mais continue l'importation des lignes valides (Étape 3).

---

### 5. Modifier les Infos Machine

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Modifier les Détails d'une Machine |
| **Contexte d'utilisation :** | Le Technicien met à jour les informations d'une machine existante (localisation, OS, RAM, etc.). |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. La machine est localisée dans l'inventaire actif. |
| **Garanties minimales :** | Seul le Technicien peut modifier les informations techniques. |
| **Garanties en cas de succès :** | Les données de la machine sont mises à jour dans la base et la modification est tracée. |

#### Scénario Nominal

1. Le Technicien sélectionne la machine à modifier depuis l'inventaire.
2. Le Système affiche le formulaire d'édition pré-rempli.
3. Le Technicien modifie les champs requis et soumet le formulaire.
4. Le Système valide les données et met à jour l'enregistrement dans la base de données.
5. Le Système confirme le succès.

#### Scénarios Alternatifs

* **A1 : Annulation de la Modification** : Le Technicien quitte le formulaire ou clique sur "Annuler" avant l'Étape 3. Le Système annule la modification et revient à la liste d'inventaire.

#### Scénarios d'Exception

* **E1 : Donnée Invalide** : Si les données soumises (Étape 3) ne respectent pas le format attendu (ex: texte dans un champ numérique), le Système rejette l'enregistrement et affiche un message d'erreur.
* **E2 : Modification du Numéro de Série** : Si le Technicien tente de modifier le `SERIAL` de la machine, le Système l'empêche de soumettre cette modification (nécessite une suppression/recréation) ou affiche un avertissement critique.

---

### 6. Déclarer un Matériel "Rebut"

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Déclarer un Matériel "Rebut" |
| **Contexte d'utilisation :** | Le Technicien met une machine hors service et la transfère de l'inventaire principal à la liste du rebut. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. La machine existe dans l'inventaire principal. |
| **Garanties minimales :** | La machine est retirée de la liste active des machines. |
| **Garanties en cas de succès :** | La machine est transférée dans la liste du rebut et l'action est loguée. |

#### Scénario Nominal

1. Le Technicien sélectionne la machine à réformer.
2. Le Technicien choisit l'action "Mettre au Rebut".
3. Le Système demande confirmation.
4. Le Technicien confirme l'action.
5. Le Système déplace (ou change le statut de) la machine vers la liste du rebut.
6. Le Système confirme le succès.

#### Scénarios Alternatifs

* **A1 : Annulation par l'Acteur** : Le Technicien annule l'opération lorsque le Système demande confirmation (Étape 3). Le CU se termine sans modification.

#### Scénarios d'Exception

* **E1 : Machine Déjà Rebutée** : Si le Technicien tente de mettre au rebut une machine déjà dans la liste du rebut, le Système affiche un message d'erreur et bloque l'opération.

---

### 7. Gérer le Statut du Rebut

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Gérer le Statut du Rebut (Remise en Service) |
| **Contexte d'utilisation :** | Le Technicien consulte la liste du rebut et peut changer le statut d'une machine pour la remettre en service. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. La machine est dans la liste du rebut. La liste du rebut n'est pas "finalisée" par l'Admin Web. |
| **Garanties minimales :** | Le statut est modifié de manière sécurisée. |
| **Garanties en cas de succès :** | La machine est retirée du rebut et réintégrée dans l'inventaire actif. |

#### Scénario Nominal

1. Le Technicien accède à la "Liste du Rebut".
2. Le Technicien sélectionne une machine du rebut et choisit l'action "Remettre en Service".
3. Le Système demande confirmation.
4. Le Technicien confirme.
5. Le Système met à jour le statut de la machine (de Rebut à Actif) et elle réapparaît dans l'Inventaire Actif.

#### Scénarios Alternatifs

Aucun scénario alternatif significatif à ce niveau.

#### Scénarios d'Exception

* **E1 : Liste du Rebut Finalisée** : Si la liste du rebut est marquée comme "finalisée" (UC 2.3), le Système bloque la fonction "Remettre en Service" et affiche un message indiquant que la liste est gelée pour audit.

---

### 8. Exporter les Données

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Exporter l'Inventaire ou le Rebut au format CSV |
| **Contexte d'utilisation :** | Le Technicien télécharge les données complètes de l'inventaire ou du rebut dans un fichier CSV. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Technicien |
| **Pré-condition :** | Le Technicien est authentifié. |
| **Garanties minimales :** | L'opération est en lecture seule et est loguée. |
| **Garanties en cas de succès :** | Le Technicien reçoit un fichier CSV complet des données demandées. |

#### Scénario Nominal

1. Le Technicien accède à la fonction d'Exportation.
2. Le Technicien choisit le jeu de données à exporter (Inventaire Actif ou Liste du Rebut).
3. Le Technicien lance l'exportation.
4. Le Système génère le fichier CSV et le transmet pour téléchargement.

#### Scénarios Alternatifs

* **A1 : Jeu de Données Vide** : Le Technicien choisit d'exporter une liste qui ne contient aucune donnée. Le Système génère un fichier CSV ne contenant que les en-têtes de colonne.

#### Scénarios d'Exception

* **E1 : Erreur de Génération de Fichier** : Si le Système rencontre une erreur technique lors de la génération du fichier (problème de base de données ou de ressource), l'opération échoue et un message d'erreur s'affiche.

---

### 9. Gérer les Comptes Techniciens

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Gérer les Comptes Techniciens (Création/Suppression) |
| **Contexte d'utilisation :** | L'Admin Web crée ou supprime des comptes pour les Techniciens. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Administrateur Web |
| **Pré-condition :** | L'Admin Web est authentifié (via `adminweb`). |
| **Garanties minimales :** | L'Admin Web ne peut pas supprimer son propre compte. |
| **Garanties en cas de succès :** | Un compte Technicien est créé ou supprimé. L'action est loguée. |

#### Scénario Nominal (Création)

1. L'Admin Web accède à la fonction "Gestion des Techniciens" et choisit "Créer".
2. L'Admin Web saisit les informations requises (login, mot de passe initial).
3. Le Système valide les données et crée un nouvel utilisateur avec le rôle 'Technicien'.
4. Le Système confirme le succès.

#### Scénarios Alternatifs

* **A1 : Modification du Compte Technicien** : L'Admin Web modifie les informations d'un compte (ex: changement de mot de passe) au lieu d'en créer un. Le Système applique la modification.

#### Scénarios d'Exception

* **E1 : Login Déjà Existant** : Si le login saisi pour la création (Étape 2) est déjà pris, le Système rejette l'entrée et affiche un message d'erreur.
* **E2 : Tentative de Suppression du Propre Compte** : Si l'Admin Web tente de se supprimer, le Système bloque l'action et affiche un message d'erreur.

---

### 10. Consulter les Journaux d'Activités (Logs)

| Rubrique | Description |
| :--- | :--- |
| **Nom :** | Consulter les Journaux d'Activités (Logs) |
| **Contexte d'utilisation :** | L'Admin Système accède aux logs générés par la plateforme pour assurer l'audit et le diagnostic. |
| **Portée :** | **Système Boîte Noire** |
| **Niveau :** | **Objectif Utilisateur** |
| **Acteur principal :** | Administrateur Système |
| **Pré-condition :** | L'Admin Système est authentifié (via `sysadmin`). Les logs existent. |
| **Garanties minimales :** | La consultation des logs n'affecte pas les données du parc informatique. |
| **Garanties en cas de succès :** | L'Admin Système dispose d'une vue complète et filtrable des activités récentes du système. |

#### Scénario Nominal

1. L'Admin Système se connecte à l'interface de consultation des logs.
2. Le Système affiche la liste des journaux d'activités (heure, acteur, action, résultat).
3. L'Admin Système utilise les outils de filtrage (par date, par acteur, par type d'action).
4. Le Système affiche les résultats filtrés.

#### Scénarios Alternatifs

* **A1 : Exportation des Logs** : L'Admin Système choisit d'exporter les logs (filtrés ou non) pour une analyse externe. Le Système génère le fichier.

#### Scénarios d'Exception

* **E1 : Aucun Log Trouvé** : Si les critères de filtrage de l'Admin Système ne renvoient aucun résultat, le Système affiche un message l'indiquant.
