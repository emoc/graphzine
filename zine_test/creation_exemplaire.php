#!/usr/bin/php -q
<?php

$OK = true;                               // Permet de tester le script "à blanc"

$dossier_exemplaires = "./exemplaires/";  // dossier dans lequel sont placés les pdf créés
$ordre_random = true;                     // par défaut : ordre aléatoire des pages
$pages_max = 20;                          // nombre de pages = CONSTANTE
$nom_zine = "graphzine";                  // racine du nom des fichiers pdf / TODO : NOT SAFE!
$format = "A3";                           // format 13 à l'italienne par défaut
$exemplaires = 1;                         // combien d'exemplaires réaliser ?
$chemin_dossier_sketch = "./pages";       // dossier contenant les sketchs des pages

/*
   Montage d'un exemplaire du fanzine génératif
   Chaque page est créée en appelant les sketch processing, un par un
   Puis elles sont assemblées dans un second temps par un script bash
    en utilisant convert (imagemagick) sous forme de pages comprenant 4 images (format A4 ou A3)
    orientées dans le bon sens!
  Ces pages sont ensuite assemblées dans un unique fichier pdf
  Le pdf est à imprimer, plier, agrafer et massicoter

  juin 2021 / pierre@lesporteslogiques.net
  PHP 7.0.33 / Debian 9.5 @ kirin
  https://github.com/emoc/graphzine

  Chaque exemplaire est caractérisé par un timestamp unique AAAA-MM-JJ_HH:MM:SS

  DEMARRER

  php ./creation_exemplaire --exemplaires=2 --format=A4 --chemin="./pages"

  COMMENT IMPRIMER ?

  en paysage, recto-verso sur le bord court (très important pour que les pages soient dans l'ordre et dans le bon sens...)
  plier toutes les feuilles ensemble, en commençant par le pli dans la longueur, plier en pli en crête (pli montagne)
  agrafer
  massicoter le haut des pages
*/




/*
  Fonction pour récupérer des arguments au lancement du script
  sous la forme : php myscript.php --user=nobody --password=secret -p --access="host=127.0.0.1 port=456"
  d'après https://www.php.net/manual/fr/features.commandline.php#78093
*/

function arguments($argv) {
    $_ARG = array();
    foreach ($argv as $arg) {
        if (preg_match('#^-{1,2}([a-zA-Z0-9]*)=?(.*)$#', $arg, $matches)) {
            $key = $matches[1];
            switch ($matches[2]) {
                case '':
                case 'true':
                    $arg = true;
                    break;
                case 'false':
                    $arg = false;
                    break;
                default:
                    $arg = $matches[2];
            }
            $_ARG[$key] = $arg;
        } else {
            $_ARG['input'][] = $arg;
        }
    }
    return $_ARG;
}

function afficher_aide() {
    echo "exemple : php ./creation_exemplaire_001.php --exemplaires=12 --random=0 --pages=20 --format=A4 --nom=\"zinetest\"--chemin=\"/chemin/vers/dossier\"" . PHP_EOL;
    echo "--exemplaires=n        : nombre d'exemplaires à fabriquer" . PHP_EOL;
    echo "--pages=n              : nombre de pages sans les 4 couvertures (20 par défaut)" . PHP_EOL; // TODO
    echo "--random=0             : ordre des pages au hasard (1, par défaut) ou dans l'ordre alpha (0)" . PHP_EOL;
    echo "--format=format        : format du papier (A4, A3 par défaut)" . PHP_EOL;
    echo "--nom=\"nom\"            : racine du nom des fichiers (graphzine par défaut)" . PHP_EOL;
    echo "--chemin=\"chemin\"      : chemin vers le dossier des sketchs" . PHP_EOL;
}

function afficher_parametres() {
    global $groupe, $exemplaires, $timestamp, $format, $nom_zine, $ordre_random, $pages_max, $chemin, $titre_complet_print, $titre_complet_web;

    echo "exemplaires           : " . $exemplaires . PHP_EOL;
    echo "pages                 : " . $pages_max . PHP_EOL;
    echo "format                : " . $format . PHP_EOL;
    echo "random                : " . $ordre_random . PHP_EOL;
    echo "nom du zine           : " . $nom_zine . PHP_EOL;
    echo "timestamp             : " . $timestamp . PHP_EOL;
    echo "chemin du script      : " . $chemin . PHP_EOL;
    echo "titre complet (print) : " . $titre_complet_print . PHP_EOL;
    echo "titre complet (web)   : " . $titre_complet_web . PHP_EOL;
}

// ***** Etape 0 - Traitement des arguments, initialisation des paramètres *****

$arguments = arguments($argv);

foreach ($arguments as $action => $valeur) {
    if ($action == "aide") {
        afficher_aide();
        exit();
    }
    if ($action == "help") {
        afficher_aide();
        exit();
    }
    if ($action == "exemplaires") {
        $exemplaires = $valeur;
    }
    if ($action == "pages") {
        $pages_max = $valeur;
    }
    if ($action == "format") {
        $format = $valeur;
    }
    if ($action == "dossier") {
        $chemin_dossier_sketch = $valeur;
    }
    if ($action == "nom") {
        $nom_zine = $valeur;
    }
    if ($action == "random") {
        if ($valeur == 0)
            $ordre_random = false;
    }
}

$timestamp = date("Ymd_His");
$chemin = getcwd();
$densite = 150;                           // densité en DPI pour la version print
$titre = $nom_zine;
if ($format == "")
    $format = "A3";
if ($format == "A4")
    $densite = 212;
if ($format == "A3")
    $densite = 150;
$titre_complet_print = $titre . "_" . $timestamp . "_print_" . $densite . "dpi_" . $format . ".pdf" ;
$titre_complet_web = $titre . "_" . $timestamp . "_web.pdf";
$usleep = 1000000;

afficher_parametres();

// ***** Etape 1 - Verification que les scripts sont bien au complet ***********


$rep = $chemin;  // Le répertoire depuis lequel est lancé le script servira pour les fichiers temporaires

$pages_images = array();
$pages_contenu = array_diff(scandir("./pages"), array('..', '.'));
foreach ($pages_contenu as $p) {
    if (is_dir("./pages/".$p)) {
        $pages_images[] = $p;
    }
}

if ($ordre_random)          // Pages en ordre aléatoire
    shuffle($pages_images);
else                        // ou par ordre alphabétique
    sort($pages_images);

if (count($pages_images) != $pages_max) {
    echo "ATTENTION script arrêté car le nombre de pages à créer (" . count($pages_images) . ") est différent du nombre attendu ($pages_max)\n";
    exit();
}


// *****************************************************************************
// ********************** Création des différents exemplaires ******************

for ($ex = 0; $ex < $exemplaires; $ex++) {

    $page = 1;

    // ********************* Etape 2 - Création des images *********************

    foreach ($pages_images as $sketch) {
        if ($page < 10)
            $page_str = "0" . $page;
        else
            $page_str = $page;
        $page_fichier = "p_" . $page_str . ".png";

        $chemin = $chemin_dossier_sketch . "/" . $sketch;

        $cmd = "xvfb-run -s \"-ac -screen 0 1600x900x24\"  /home/emoc/processing-3.4/processing-java --sketch=\""
             . $chemin . "\" --run \"" . $page_fichier .  "\" \"" . $rep . "/\" \"0\"";

        echo $cmd . PHP_EOL;
        if ($OK) {
            echo exec($cmd) . PHP_EOL;
            usleep($usleep);
        }
        $page ++;
    }


    // *************** Etape 3 - Conversions, montage des pages ****************

    $cmd = "montage \( p_11.png -rotate 180 \) \( p_10.png -rotate 180 \) ./couv/c_vo.png ./couv/c_ro.png -geometry 1240x874+0+0 -tile 2x2 f_a_ro.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;
    $cmd = "montage \( p_09.png -rotate 180 \) \( p_12.png -rotate 180 \) ./couv/i_ro.png ./couv/i_vo.png -geometry 1240x874+0+0 -tile 2x2 f_a_vo.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;
    $cmd = "montage \( p_13.png -rotate 180 \) \( p_08.png -rotate 180 \) p_20.png p_01.png -geometry 1240x874+0+0 -tile 2x2 f_b_ro.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;
    $cmd = "montage \( p_07.png -rotate 180 \) \( p_14.png -rotate 180 \) p_02.png p_19.png -geometry 1240x874+0+0 -tile 2x2 f_b_vo.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;
    $cmd = "montage \( p_15.png -rotate 180 \) \( p_06.png -rotate 180 \) p_18.png p_03.png -geometry 1240x874+0+0 -tile 2x2 f_c_ro.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;
    $cmd = "montage \( p_05.png -rotate 180 \) \( p_16.png -rotate 180 \) p_04.png p_17.png -geometry 1240x874+0+0 -tile 2x2 f_c_vo.png";
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;



    // ****************** Etape 4 - Création du fichier pdf ********************

    // 4.1  version print
    $cmd = "convert f_a_ro.png f_a_vo.png f_b_ro.png f_b_vo.png f_c_ro.png f_c_vo.png -units PixelsPerInch -density "
         . $densite . " " . $dossier_exemplaires . $titre_complet_print;
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;

    // 4.2  version web
    $cmd = "convert ./couv/c_ro.png ./couv/i_ro.png "
         . "p_01.png p_02.png p_03.png p_04.png p_05.png p_06.png p_07.png p_08.png p_09.png p_10.png "
         . "p_11.png p_12.png p_13.png p_14.png p_15.png p_16.png p_17.png p_18.png p_19.png p_20.png "
         . "./couv/i_vo.png ./couv/c_vo.png"
         . " -units PixelsPerInch -density 150x150 " . $dossier_exemplaires . $titre_complet_web;
    echo $cmd . PHP_EOL;
    if ($OK)
        echo exec($cmd) . PHP_EOL;


    // *************** Etape 5 - Effacer les fichiers temporaires ****************

    usleep($usleep);

    $cmd = "rm p_01.png p_02.png p_03.png p_04.png p_05.png p_06.png p_07.png p_08.png p_09.png p_10.png "
         . "p_11.png p_12.png p_13.png p_14.png p_15.png p_16.png p_17.png p_18.png p_19.png p_20.png "
         . "f_a_ro.png f_a_vo.png f_b_ro.png f_b_vo.png f_c_ro.png f_c_vo.png";
    echo $cmd . PHP_EOL;

    if ($OK)
        echo exec($cmd) . PHP_EOL;
}
?>
