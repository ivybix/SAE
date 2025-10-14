# Cahier des charges



### Recueuil des exigences 
 Les exigences fonctionnelles sont la gestion de l'inventaire, l'administration et la journalisation.  

 
 Au niveaux de la gestion d'inventaire :
 - Le technicien doit pouvoir **consulter et modifier** les informations du parc informatique. 
 - Le système doit permettre l'**ajout d'une seule machine** via un formulaire. 
 - Le système doit permettre l'**ajout d'une série de machines** via un fichier CSV. 
 - Le technicien doit pouvoir **supprimer une machine** pour la placer dans la **liste du rebut**. 
 - Le système doit permettre de **consulter la liste du rebut** et **changer le statut** du matériel remis en service. 
 - Le technicien doit pouvoir **exporter une liste** au format CSV.

Au niveaux de l'administration :

- L'Admin Web doit pouvoir **créer** et **supprimer** des techniciens. 
- Le système doit permettre de créer des **informations réutilisables** (noms des OS, constructeurs). 
- L'Admin Web doit pouvoir **consulter la liste du rebut**. 
- L'Admin Web doit pouvoir **bloquer la liste du rebut** pour une future exportation. 

Au niveaux de la journalisation : 

- Un **fichier de log** inhérent à toutes les actions réalisées doit être créé. 
- L'Admin Système doit pouvoir **consulter les différents journaux d'activités** de la plateforme. 
- L'Admin Système **n'accède pas** aux fonctionnalités web.

