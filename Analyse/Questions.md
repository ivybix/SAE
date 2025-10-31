# Lecture du cahier des charges

| Objet | Acteur |Actions |
|:--------: |:--------:| :--------:|
| Site Web     | Visiteur  | Consulter une partie de l'inventaire |
| Site Web     |  Technicien  | Consulter le parc informatique, Modifier une information, Ajouter une machine ,Supprimer une machine,Exporter une liste CSV , Consulter la liste du revue, Changer le statut du matériel, Ajouter une série de machines à partir d'un CSV|
| Site Web     | Administrateur Web | Créer un technicien, Supprimer un technicien, Créer une information, Bloquer la liste du rebut|
| Site Web     | Administrateur Système | Consulter les différents journaux d'activités de la plateforme |


# Questions de clarification

1. La plateforme doit-elle être accessible uniquement sur le réseau local (intranet via le RPi) ou aussi depuis l’extérieur ?

2. Les mots de passe des comptes (adminweb, sysadmin, tech1) doivent-ils être hachés dans la base de données ou laissés en clair pour simplifier l’évaluation ?

3. Le visiteur accède-t-il à la plateforme sans authentification ou via un compte invité ?

4. Les rôles utilisateurs doivent-ils être gérés dynamiquement en base ou codés directement dans le code PHP ?

5. Le technicien par défaut (tech1) doit-il être créé automatiquement dans la base au lancement de l’application ?

6. Lors de l’import d’un fichier CSV, que faire en cas de doublon de numéro de série (remplacer, ignorer, ou signaler l’erreur) ?

7. Comment distinguer les unités centrales et les moniteurs dans la base de données ?

8. Quand un matériel est marqué « remis en service », doit-il être automatiquement réintégré dans l’inventaire principal ?

9. Quand l’administrateur web bloque la liste du rebut, cela interdit-il toute modification ultérieure par les techniciens ?

10. Que doit contenir chaque entrée du fichier de log (date, action, utilisateur, IP, etc.) ?

11. Les logs d’activité doivent-ils être stockés en base de données ou dans un fichier texte sur le RPi ?

12. Le technicien peut-il modifier définitivement les informations dans l’inventaire, ou ses modifications sont-elles soumises à validation ?

13. L’administrateur système dispose-t-il d’une interface spécifique pour consulter les journaux d’activité ?

14. Des tests unitaires et/ou d’intégration sont-ils attendus dans le cadre du projet ? 

15. Quelle est la durée ciblée pour la vidéo explicative ?


# Réponses

1. fdede

2. defefe

3. fefeeded

4. fefededef

5. fededede

6. deded

7. dede

8. dedede

9. dede

10. dede

11. dee

12. dede

13. de

14. dede

15. ddedede












