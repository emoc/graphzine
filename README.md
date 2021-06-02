# Graphzine

## Procédé d'édition

Un script programmé orchestre le processus depuis la création des images jusqu’à la réalisation des fichiers pdf prêts à imprimer, selon plusieurs étapes :

### Étape 1 : génération des images

Chaque programme est « appelé » pour créer une image au format PNG

![Du code vers l'image](./assets/code_vers_image.png?00)

### Étape 2 : constitution du chemin de fer

Une table des matières spécifique est créée pour l'exemplaire en cours. Elle permet de créer le chemin de fer propre à cet exemplaire.

![Table des matières](./assets/chemin_de_fer.png?00)

### étape 3 : montage du fichier imprimable

Le fichier pdf est assemblé à partir du chemin de fer. Pour cela les images sont placées les unes à côté des autres et orientées dans le sens adéquat...

![Préparation du fichier](./assets/montage.png?00)

## Préparation des pages

Chaque image est conçue à partir d'un squelette de code fourni (*zine_base*). Le sketch processing produit une image dans une définition de 1240 x 874 (format A5 paysage 150 dpi). Une marge blanche de 4 mm est appliquée sur toutes les images dans le script *zine_base*.

## Préparation d'un exemplaire

### Étapes
  * création des images par script
  * création des couvertures par un script adapté
  * assemblage des images par script, jusqu'au fichier pdf
  * impression au format A3 recto-verson (TODO : bord court ou bord long ?)

### Mise en oeuvre

Donner des droits d'exécution aux scripts
```bash
  chmod +x ./creation_exemplaire.php
  chmod +x ./commandes_conversion.sh  
```
Lancer le script php
```bash
  php ./creation_exemplaire.php
```
## Ressources

[Traitement par lot avec processing en ligne de commande](http://lesporteslogiques.net/wiki/ressource/code/processing/traitement_par_lot)  
