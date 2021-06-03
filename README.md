# Graphzine

Réalisation et montage d'un zine de 20 pages de graphisme génératif.

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
Dans cette étape, la variable booléenne TESTMODE est définie comme *true* dans le sketch, quand il est prêt, on la passe à *false*.  
Quand les sketchs des différentes pages sont prêts, on peut préparer un exemplaire

## Préparation d'un exemplaire

### Structure du dossier

```
.
├── couv
│   ├── c_ro.png
│   ├── c_vo.png
│   ├── i_ro.png
│   └── i_vo.png
│
├── pages
│   ├── zine_page_1
│   │   └── zine_page_1.pde
│   ├── zine_page_2
│   │   └── zine_page_2.pde
│   ├── zine_page_3
│   │   ├── data
│   │   │   └── image.png
│   │   └── zine_page_3.pde
│   ├── zine_page_4
│   │   └── zine_page_4.pde
│   └── ... etc.
│       └── ...
│
├── exemplaires
│   ├── genzine_20210602_175714_print_212dpi_A4.pdf
│   └── genzine_20210602_175714_web.pdf
│
├── couvertures.php
└── creation_exemplaire.php
```

Le dossier zine_test contient une structure complète, et le script creation_exemplaire.php

### Étapes

  * création des images par le script "creation_exemplaire.php"
  * création des couvertures (par le moyen désiré), à placer dans *couv* en respectant la nomenclature
  * assemblage des images par script, jusqu'au fichier pdf
  * impression au format A3 recto-verso **bord court** (très important!)
  * plier, d'abord sur la longueur, en pli en crête (pli montagne)
  * agrafer
  * massicoter le haut des pages (ou cutter)

### Mise en oeuvre

Ranger les dossiers de sketch dans le dossier pages en respectant la structure (le nom du dossier n'est pas important)  
Donner des droits d'exécution aux scripts
```bash
  chmod +x ./creation_exemplaire.php
```
Lancer le script php
```bash
  php ./creation_exemplaire.php
```
Options du script **creation_exemplaire.php**
```
exemple : php ./creation_exemplaire.php --random=0 --exemplaires=12 --format=A4 --chemin="/pages"
--exemplaires=n        : nombre d'exemplaires à fabriquer
--random=0             : ordre des pages au hasard (1, par défaut) ou dans l'ordre alpha (0)
--format=format        : format du papier (A4, A3 par défaut)
--chemin="chemin"      : chemin vers le dossier des sketchs
```

## Améliorations possibles

* Choisir le nombre de pages ...
* Permettre d'activer x fois le même script pour tout le zine (dossier "page_unique"), les variations sont alors prises en compte dans le code génératif
* prise en charge du format A5 / définition variable ?

## Ressources

[Traitement par lot avec processing en ligne de commande](http://lesporteslogiques.net/wiki/ressource/code/processing/traitement_par_lot)  
[Graphzine (wikipedia)](https://fr.wikipedia.org/wiki/Graphzine)  
[Written Images](http://writtenimages.net/)  
[Vocabulaire du pliage](https://www.chine-culture.com/origami/pli-de-base.php)  
