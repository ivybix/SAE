# SAÉ Cryptographie : Implémentation de ChaCha20

Ce projet a été réalisé dans le cadre de la **SAÉ Cryptographie** (IUT de Vélizy). Il vise à comprendre et implémenter l'algorithme de chiffrement par flux **ChaCha20** selon la spécification **RFC 8439**.

## L'Équipe

* **ABOUALOU Jihan**
* **ALILECHE Samy**
* **BENJAMIN BENEDICT**
* **LE BRETON Nicolas**
* **PERONI Dylan**

## Contenu du Dépôt

Le projet contient deux approches distinctes de l'algorithme :

### 1. `Chacha20.py` (Implémentation Manuelle)

C'est le cœur pédagogique du projet. Il s'agit d'une implémentation "from scratch" sans bibliothèque cryptographique externe.

* **Fonctionnalités :** Gestion de la matrice d'état 4x4, fonctions de rotation (ROTL), Quarter Rounds, et génération du Keystream via XOR.
* **Objectif :** Démontrer la compréhension mathématique et algorithmique de ChaCha20.

### 2. `chacha20_lib.py` (Implémentation PyCryptodome)

Il s'agit de la version "production", utilisant la bibliothèque standardisée **PyCryptodome**.

* **Fonctionnalités :** Génération d'aléatoire cryptographique sécurisé (`Crypto.Random`) et utilisation optimisée de l'algorithme.
* **Objectif :** Comparer notre version manuelle avec une implémentation professionnelle et sécurisée contre les attaques par canaux auxiliaires.

## Installation

### Prérequis

* Python 3.x

### Dépendances

Pour faire fonctionner le script `chacha20_lib.py`, vous devez installer la bibliothèque `pycryptodome` :

```bash
pip install pycryptodome

```

## Utilisation

### Tester la version manuelle

```bash
python Chacha20.py

```

### Tester la version librairie

```bash
python chacha20_lib.py

```

## Détails Techniques

* **Type de chiffrement :** Chiffrement par flux (Stream Cipher)
* **Taille de clé :** 256 bits (32 octets)
* **Taille de Nonce :** 96 bits (12 octets) - Standard RFC 8439
* **Compteur :** 32 bits (commence à 1)
