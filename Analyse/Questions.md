# Lecture du cahier des charges

| Objet | Acteur |Actions |
|:--------: |:--------:| :--------:|
| Site Web     | Utilisateur  | Consulter une partie de l'inventaire |
| Site Web     |  Technicien  | Consulter le parc informatique, Modifier une information, Ajouter une machine ,Supprimer une machine,Exporter une liste CSV , Consulter la liste du revue, Changer le statut du matériel, Ajouter une série de machines à partir d'un CSV|
| Site Web     | Administrateur Web | Créer un technicien, Supprimer un technicien, Créer une information, Bloquer la liste du rebut|
| Site Web     | Administrateur Système | Consulter les différents journaux d'activités de la plateforme |


# Questions pour clarification du projet Plateforme WEB

## 1. Détails sur la structure du projet
- **Est-ce que les rôles des utilisateurs (administrateur système, administrateur web, technicien, visiteur) sont définis dès le début, ou évolueront-ils au fil du projet ?**
  
- **Quels champs spécifique doit être utilisé comme critère de recherche principal.**

 - **Faut-il prévoir une fonctionnalité permettant à l'admin Web de consulter ou de réinitialiser le mot de passe des techniciens qu'il crée?**

## 2. Gestion des utilisateurs
- **L’administrateur web peut-il gérer uniquement les techniciens, ou aussi d’autres administrateurs web ?**  

- **Le technicien peut-il modifier définitivement les informations dans l’inventaire, ou ses modifications sont-elles soumises à validation ?**  

- **L’administrateur système dispose-t-il d’une interface spécifique pour consulter les journaux d’activité ?**  


## 3. Fonctionnalités
- **Faut-il prévoir une fonction de recherche et filtrage dans l’inventaire pour faciliter le travail des techniciens ?**  

- **Des tests unitaires et/ou d’intégration sont-ils attendus dans le cadre du projet ?**  

- **La configuration complète du serveur web (Apache) et du SGBD (MySQL) est-elle fournie, ou devons-nous tout installer et configurer nous-mêmes ?**

- **Quelle est la durée cible pour la vidéo explicative.**



## 4. Sécurité
- **Devons-nous gérer la sécurité des sessions (mots de passe hachés, tokens, etc.) et des accès sur la plateforme ?**  


## 5. Support technique
- **Si nous rencontrons des problèmes avec les Raspberry Pi ou le réseau, quel est le canal officiel pour demander de l’aide ou signaler un incident ?**










