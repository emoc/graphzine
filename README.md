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
