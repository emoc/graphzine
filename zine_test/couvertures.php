<?php

// Création des couvertures pour le graphzine **********************************

$data = "./couv_assets/noms_couverture.txt";
$lines = file($data, FILE_IGNORE_NEW_LINES);
$year = file_get_contents("./couv_assets/annee.txt");


function cmd($cmd) {
    echo $cmd . PHP_EOL;
    echo exec($cmd) . PHP_EOL;
}


// Préparer le titre : noms, date, etc.
$noms = "";
$cmpt = 0;
shuffle($lines);
foreach ($lines as $val) {
    if ($cmpt == 0)
        $noms .= ">>>>";
    else
        $noms .= "/";
    $noms .= mb_strtoupper($val, 'UTF-8');
    $cmpt ++;
}
$noms = trim($noms) . ">>>>>" .$year;

// Découpage de cette chaine en tronçons de taille fixe
$nomsl = strlen($noms);

$lng = 31;
$ch = "";
for ($i = 0; $i < $nomsl; $i += $lng) {
    $max = $lng;
    if ($i + $max > $nomsl) {
        $max = $nomsl - $i;
        $fill = $lng - $max + 5;
    }
    $ch .= mb_substr($noms, $i, $max, 'UTF-8'). "\n";
}
$ch = trim($ch);
$ch .= substr("<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<", 0, $fill);
echo $ch . PHP_EOL;



$titre = "GRAPH\nZINE>";

// Composer le texte en image
/* Usage des fontes dans imagemagick
    Fontes systèmes + fontes du fichier /etc/ImageMagick-6/type.xml
        convert -list font|grep glyphs      # liste des fichiers
        convert -list font|grep Font        # liste des noms de fontes à utiliser
*/
// Noms pour le verso
$police_arg = "-fill white -pointsize 74 -interline-spacing -14 -font Fantasque-Sans-Mono-Bold";
cmd("convert -extent 1240x874 -background none " . $police_arg . " label:\"" . $ch . "\" -geometry 1240x874+0+0 noms.png");

// Titre pour le recto
$police_arg = "-fill white -pointsize 460 -interline-spacing -90 -font Fantasque-Sans-Mono-Bold";
cmd("convert -extent 1240x874 -background none " . $police_arg . " label:\"" . $titre . "\" -geometry 1240x874+0+0 titre.png");
$angle = floor(rand(95, 175));
cmd("convert -size 2480x874 canvas:white \( ./couv_assets/texture2.png -resize 220% -rotate " . $angle . " -crop 2480x874+2500+2500 \) -composite rovo0.png");
cmd("composite -compose In rovo0.png titre.png titre_tex.png");


// Créer les deux images qui composent le fond
$angle = floor(rand(5, 85));
cmd("convert -size 2480x874 canvas:white \( ./couv_assets/texture.png -resize 220% -rotate " . $angle . " -crop 2480x874+2500+2500 \) -composite rovo1.png");
$angle = floor(rand(95, 175));
cmd("convert -size 2480x874 canvas:white \( ./couv_assets/texture.png -resize 220% -rotate " . $angle . " -crop 2480x874+2500+2500 \) -composite rovo2.png");

// Superposer les deux trames de fond
cmd("composite rovo1.png rovo2.png -compose Multiply rovo.png");

// Composer le titre et le fond
cmd("composite noms.png -geometry 1240x874+30+70 rovo.png -compose Over rovo_et_titre0.png");
cmd("composite titre_tex.png -geometry 1240x874+1280+0 rovo_et_titre0.png -compose Over rovo_et_titre.png");

// Découpage en 2 parties (recto et verso)
cmd("convert -size 1240x874 canvas:white rovo_et_titre.png -crop 1240x874+1240+0 -composite ./couv/c_ro.png");
cmd("convert -size 1240x874 canvas:white rovo_et_titre.png -crop 1240x874+0+0 -composite ./couv/c_vo.png");

// Couvertures intérieures
cmd("convert -size 1240x874 canvas:white ./couv/i_ro.png");
cmd("convert -size 1240x874 canvas:white ./couv/i_vo.png");

cmd("rm noms.png rovo.png rovo0.png rovo1.png rovo2.png rovo_et_titre.png rovo_et_titre0.png titre.png titre_tex.png")
?>
