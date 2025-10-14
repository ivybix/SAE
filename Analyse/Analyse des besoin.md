# Analyse des Besoins – Plateforme de Gestion de Parc Informatique (SAÉ S3)

## 1. Objectif de l'Application

La plateforme web, développée en **PHP & MySQL**, a pour objectif la **gestion complète et centralisée d'un parc informatique** (moniteurs et unités centrales). Elle doit offrir des modules basés sur le rôle de l'utilisateur et assurer la traçabilité via des **journaux d'activités (logs)**.

---

## 2. Acteurs et Rôles

Le système doit gérer quatre profils d'utilisateurs distincts:

| Acteur | Rôle Principal | Accès Principal |
| :--- | :--- | :--- |
| **Visiteur** | Consultation partielle de l'inventaire. | Consultation |
| **Technicien** | Gestion et manipulation des données d'inventaire. | Modification, Ajout, Suppression |
| **Admin Web** | Gestion des techniciens et des données de référence (OS, Constructeurs). | Gestion des utilisateurs |
| **Admin Système** | Surveillance de l'activité du système. | Consultation des journaux d'activités (logs) |

---

## 3. Exigences Fonctionnelles (EF)

### 3.1. Gestion de l'Inventaire (Technicien)

| Exigence |
| :--- |
|Le technicien doit pouvoir **consulter et modifier** les informations du parc informatique. |
| Le système doit permettre l'**ajout d'une seule machine** via un formulaire. |
| Le système doit permettre l'**ajout d'une série de machines** via un fichier CSV. |
|  Le technicien doit pouvoir **supprimer une machine** pour la placer dans la **liste du rebut**. |
| Le système doit permettre de **consulter la liste du rebut** et **changer le statut** du matériel remis en service. |
| Le technicien doit pouvoir **exporter une liste** au format CSV. |

### 3.2. Administration (Admin Web)

| Exigence |
| :--- |
|  L'Admin Web doit pouvoir **créer** et **supprimer** des techniciens. |
|  Le système doit permettre de créer des **informations réutilisables** (noms des OS, constructeurs). |
|  L'Admin Web doit pouvoir **consulter la liste du rebut**. |
|  L'Admin Web doit pouvoir **bloquer la liste du rebut** pour une future exportation. |

### 3.3. Journalisation (Admin Système)

| Exigence |
|  :--- |
|  Un **fichier de log** inhérent à toutes les actions réalisées doit être créé. |
|  L'Admin Système doit pouvoir **consulter les différents journaux d'activités** de la plateforme. |
|  L'Admin Système **n'accède pas** aux fonctionnalités web. |

---

## 4. Exigences Non Fonctionnelles (ENF)

### 4.1. Techniques et Performances

* L'application doit être développée en **PHP & MySQL**.
* L'installation doit être réalisée sur un serveur porté par un **Raspberry Pi 4 (RPi4)**.
* L'installation sur le RPi4 doit inclure le système, un **serveur web** (Apache), et un **serveur SGBD** (MySQL).
* La page d'accueil doit proposer un texte explicatif et une **vidéo explicative** des fonctionnalités.

### 4.2. Sécurité et Contraintes

* Le système doit gérer les accès SSH du RPi4, notamment l'identifiant obligatoire `sae2025` et le mot de passe `!sae2025!`.
* Les identifiants obligatoires de l'Admin Web (`adminweb/adminweb`) et du Technicien de base (`techi/tech1`) ne doivent pas être modifiés.
* Les applications qui permettent de **sécuriser les accès ssh** doivent être installées.
* Le *hacker* le matériel d'un autre groupe est formellement interdit et sera lourdement sanctionné.

### 4.3. Documentation et Livrables

* Le code, la documentation et toute autre information concernant le projet doivent être mis à disposition sur un compte **Gitlab ou Github**.
* Le **cahier des charges** doit être réécrit après étude du sujet.
* Un document expliquant le **choix du logo** doit être rédigé (travail de Communication Professionnelle).
* Le projet fera l'objet d'un **exposé en anglais**.
