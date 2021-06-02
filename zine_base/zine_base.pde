/*
  Squelette de code pour créer une illustration au format A5, paysage à 150 dpi
  Cours code créatif / EDNA, novembre 2019 / pierre@lesporteslogiques.net
  'espace' pour redessiner une image
  's' pour enregistrer l'image de la fenêtre au format png
 */

String emplacement = ""; // à conserver tel quel, utilisé pour la publication
boolean TESTMODE = true; // à conserver tel quel, utilisé pour la publication

String fichier =  "zine_nom_prenom.png"; // <- à remplacer par vos noms et prénoms (sans accents, espaces, maj)

void setup() {
  size(1240, 874); // A5 paysage 150 dpi
  init();          // à conserver, utilisé pour la publication
}

void draw() {
  
  // début de la partie graphique ***********************
  
  // Votre code commence ici!
  
  background(240);
  stroke(0);
  fill(0);
  strokeWeight(3);
  for (int i=0; i < 25; i++) {
    float x = random(width/3*2);
    float y = random(height/3*2);
    line(x, y, x + width, y + height);
  }
  textSize(48);
  text(fichier, 40, height-40);
   
  // Votre code s'arrête ici!
  
  marges(); // ajout des marges, 4mm de blanc tout autour
  // fin de la partie graphique *************************  
  
  if (!TESTMODE) {
    saveFrame(emplacement + fichier);
    exit();
  }
  noLoop();
}

void keyPressed() {
  if (key == ' ') redraw();
  if (key == 's') saveFrame();
}



// Fonction utilisée pour la publication à conserver telle quelle

void init() {
  if (args != null) {
    println(args.length);
    for (int i = 0; i < args.length; i++) {
      println(args[i]);
    }
    fichier = args[0];
    emplacement = args[1];
    if (args[2].equals("0")) TESTMODE = false;
    else TESTMODE = true;
    println("fichier : " + fichier);
    println("emplacement : " + emplacement);
    println("testmode : " + TESTMODE);
    println(fichier + " en cours");
  } else {
    println("args == null");
  }
}

// Fonction pour ajouter des marges ******************************
void marges() {
  colorMode(RGB);
  fill(255);
  stroke(255);
  rect(0, 0, width, 24);         // haut
  rect(0, height-24, width, 24); // bas
  rect(0, 0, 24, height);        // gauche
  rect(width-24, 0, 24, height); // droite
}
