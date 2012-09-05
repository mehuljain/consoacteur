<?php
/**
 * Librairie d'accès à la base de données compatible UTF-8.
 *
 * REMARQUE : "compatible UTF-8" signifie que les données qui transitent entre PHP et le serveur de base ne sont pas modifiées.
 *
 *  @copyright C2B S.A. 2010
 */

/**
 * DialogueBD est faite pour pouvoir gérer l'accès à des serveurs différents dans un système de maitre-esclave (réplication) et ayant des rôles différents
 * (ex serveur de stats, serveur de catalogues, ...).
 * Pour un rôle donné, les requêtes peuvent être faites soit sur le maitre (lecture-ecriture) soit sur un esclave (lecture seule).
 *
 * Pour utiliser cette classe il faut la surcharger et setter les attributs "abstract" (dans le premier constructeur appelé) :
 *
 * 1) $_default_db    qui donne le nom de la base de donnée par défaut
 *
 * 2) self::$_rep_conf      qui donne le chemin complet vers les fichiers de configuration des grappes
 *
 * 3) self::$_rep_log      qui donne le chemin complet vers le répertoire où mettre les fichiers de log
 *
 * 4) doChoixGrappe         qui fait la correspondance entre un nom de table et de grappe
 *
 * 5) il faut appeler le constructeur de cette classe dans le constructeur de la classe fille
 *
 * 6) si souhaité, il est possible de setter self::$_cls_collection qui donne le nom de la classe de Collection
 *
 *
 * Les fichiers de log sont activés avec les setters des attributs statiques :
 *  - ADialogueBD::$log_req     pour activer le log des requêtes dans le fichier requetesYYYYMMDDHH.log
 *  - ADialogueBD::$log_bench   pour activer le log de benchmark de l'activité dans le fichier benchYYYYMMDDHH.log
 *
 * ATTENTION : lors de la surcharge il ne faut pas redeclarer les attributs statiques sinon self:: ne pointera plus sur la même valeur dans la classe fille et dans la classe mère.
 *
 *
 ***********************************************************************************************************************************
 ***********************************************************************************************************************************
 ***********************************************************************************************************************************
 ******************* Informations noyau (pour maintenir la classe)*******************
 *
 * La classe gére des grappes de serveurs, une grappe est composée d'un serveur maitre (qui reçoit les modifications) et d'un ensemble de serveurs esclaves (qui reçoivent les select).
 * Remarque : dans la version netav2 (2.5) une grappe était appelée un "rôle".
 *
 * Chaque grappe est identifiée par un nom simple, pour une grappe les ips des serveurs sont conservées dans des fichiers.
 * Le chemin de base des fichiers de configuration répond à l'arborescence suivante :
 *    "bdd" / nom_grappe / "master"
 *                       / "slaves"
 *          / my.cnf (qui contient les identifiants de connexion)
 *
 * Le nom "bdd" est conservé dans self::$_base_rep_conf
 * Ce chemin est conservé dans l'attribut $this->_rep_conf, initialisé par le constructeur
 *
 *************
 *
 * Les fichiers "master" et "slaves" contiennent la localisation des serveurs de la grappe choisie.
 *    Le fichier "master" contient une seule ligne de serveur (= un seul serveur) tandis que le fichier "slaves" peut contenir plusieurs lignes de serveurs auquel cas un serveur aléatoire est choisi.
 *    DANS LES FICHIERS "slaves" UNIQUEMENT : les lignes vides ou les lignes commençant par # (dièse) sont ignorées.
 *
 *    Une ligne peut contenir :
 *      - soit une adresse IP de la forme : IP:PORT ou IP (auquel cas le port :3306 est utilisé)
 *      - soit un nom de domaine de la forme : NDD:PORT ou NDD (auquel cas le port :3306 est utilisé)
 *        Dans ce cas le nom est résolu en ip et une des adresses est prises aléatoirement.
 *        Le nom de domaine DOIT contenir au moins un point
 *      - le mot clef "localhost" (suivi d'un port ou non)
 *
 *    L'adresse prise sera choisie au hasard à moins qu'une connexion ne soit déjà ouverte sur une des ips possibles.
 *    Une ip apparaissant plusieurs fois dans les adresses des noms de domaines aura plus de chances d'être choisie.
 *
 *************
 *
 * La classe gère un pool de connexions pour le script courant dans self::$_liste_conn.
 *
 *
 *
 * @author kevin
 */
abstract class ADialogueBD {

  static protected $_base_rep_conf    = 'bdd/';

  // le nom de la classe de Collection pour les retours sous cette forme
  protected $_cls_collection   = 'Collection';

  // ABSTRACT la base de donnée par défaut à ajouter aux noms de table trouvés par le biais d'un modèle Doctrine
  protected $_default_db;

  // ABSTRACT le chemin complet vers les fichiers de configuration
  static protected $_rep_conf;

  // le nombre d'essai de connexion à une même ip avant de retourner un échec. Minimum forcé 1, défaut 4.
  static protected $nbr_essais_conn   = 4;

  // true pour loguer les requêtes dans self::$log_rep
  static protected $log_req      = false;

  // true pour loguer les requêtes dans self::$log_rep
  // sa valeur DOIT être setté par self::setLogBenchmark()
  static protected $log_bench;

  // ABSTRACT le répertoire où écrire les logs (cf commentaire de classe)
  static protected $_rep_log;


  /**
   * Liste des connexions ouvertes.
   *
   * Syntaxe:
   * 	ip:port => array(
   * 			0 => ressource,
   * 			1 => nombre de connexion ouverte (> 0)
   *      2 => 1 si l'ip correspond à un maitre, 2 si l'ip correspond à un esclave et 3 si elle correspond au deux
   *      3 => liste des grappes gérées par l'ip
   * 			4 => liste des verrous SQL acquis (à fermer à la fermeture de la connexion)
   * 			5 => données de benchmark de la connexion, sous la forme :
   * 					0 => timestamp de la première d'ouverture
   * 					1 => timestamp de la dernière fermeture ou 0
   * 					2 => nombre de requêtes sur l'ip
   * 					3 => nombre de fermetures
   * 				Les données de benchmark sont écrites dans le log à la fermeture d'une connexion.
   * 		)
   *
   * @var array $_liste_conn
   */
  static private   $_liste_conn       = array();


  protected $classe_courante  = '';
  protected $db_courante      = '';
  protected $grappe_courante  = '';
  protected $ferme_auto       = false;
  protected $req_maitre       = false;
  protected $_ret_insert_id   = false;
  protected $erreur_possible  = false;

  protected   $_resultat        = false;
  protected   $_ligne_actu      = false;
  protected   $_backup_res      = array();
  protected   $_code_resultat   = '';
  protected   $_num_touche      = 0;
  protected   $_insert_id       = 0;
  protected   $der_req          = '';
  protected   $conn_ouvertes    = array();

  /**
   * Retourne le nom de la grappe à partir d'un nom de table complet (base.table)
   *
   * @param   string    $PV_table   le nom de la base de donnée ou de la table au format base.table
   * @return  string                le nom de la grappe à choisir
   */
  abstract protected function doChoixGrappe ($PV_table);

  public function __construct () {
    $this->db_courante = $this->_default_db;
  }

  public function __destruct() {
    $this->ferme();
  }

  /**
   * Indique si l'on veut remplir le log de requête
   *
   * @param   bool    $PV_val     true pour activer le log, false sinon
   */
  static public function setLogRequete ($PV_val) {
    self::$log_req = $PV_val ? true : false;
  }

  /**
   * Indique si l'on veut remplir le log de benchmark
   *
   * @param   bool    $PV_val     true pour activer le log, false sinon
   */
  static public function setLogBenchmark ($PV_val) {
    static $cpt = 1;

    self::$log_bench = $PV_val ? true : false;

    // ne passe qu'une fois dans le if et seulement si on veut le log
    if ($cpt == 1 && self::$log_bench) {
      register_shutdown_function(array(__CLASS__,'doLogBench'));
      ++$cpt;
    }
  }

  /**
   * Prend un verrou applicatif dans la base de données.
   *
   * La fonction MySQL utilisée est GET_LOCK.
   * Le verrou est forcément pris sur la base de donnée par défaut.
   *
   * @param   string    $PV_nom       le nom du verrou
   * @param   int       $PV_timeout   le délai pendant lequel on essaye de prendre le verrou
   * @return  bool                    true si le verrou est pris
   *                                  false sinon
   */
  public function prendVerrou ($PV_nom, $PV_timeout=10) {
    $r = 'SELECT GET_LOCK('.$this->protegeValeur($PV_nom).','.$PV_timeout.')';

    $oldmaitre = $this->choisitMaitre(true);
    $oldgrappe = $this->choisitGrappe($this->_default_db);
    $this->backupResultat();

    $c = $this->doRequete($r);

    $ok = $c && intval(mysql_result($this->_resultat,0,0)) == 1;

    $this->choisitMaitre($oldmaitre);
    $this->choisitGrappe($oldgrappe);
    $this->restoreResultat();

    return $ok;
  }

  public function rendVerrou ($PV_nom) {
    $r = 'DO RELEASE_LOCK('.$this->protegeValeur($PV_nom).')';

    $oldmaitre = $this->choisitMaitre(true);
    $oldgrappe = $this->choisitGrappe($this->_default_db);

    $this->doRequete($r);

    $this->choisitMaitre($oldmaitre);
    $this->choisitGrappe($oldgrappe);
  }

  /**
   * Sélectionne des lignes dans UNE table.
   *
   * Pour faire un select multiple (une jointure) il faut utiliser @see ADialogueBD::union()
   *
   * @param string $PV_table    le nom de la table sous la forme base.table
   * @param mixed  $PV_champs   les champs à récupérer,
   *                              soit une chaine avec la liste des champs séparés par des virgules (syntaxe SQL)
   *                              soit '*' pour tous les champs
   *                              soit array() pour tous les champs
   *                              soit une liste de noms de champs. Si un index est une chaine c'est l'alias du champ.
   * @param string $PV_cond     la condition SQL
   * @param string $PV_limit    la clause LIMIT
   * @param string $PV_group    la clause GROUP BY
   * @param string $PV_order    la clause ORDER BY
   * @param bool   $PV_explain  true pour faire une requête EXPLAIN au lieu de SELECT
   * @param bool   $PV_prtegNom false pour prendre le PV_champs telque il est sans protection
   * @return int                le nombre de lignes sélectionnées ou false en cas d'erreur
   */
  public function select ($PV_table, $PV_champs='*', $PV_cond='', $PV_limit='', $PV_group='', $PV_order='', $PV_explain=false, $PV_protegNom= true) {
    return $this->_select($PV_table, $PV_champs, $PV_cond, $PV_limit, $PV_group, $PV_order, $PV_explain, $PV_protegNom);
  }

  /**
   * Sélectionne des lignes dans UNE table à partir d'une classe Doctrine.
   *
   * Tous les champs sont obligatoirement récupérés dans ce cas.
   *
   * @param string $PV_classe   le nom de la classe de modèle Doctrine de la table
   * @param string $PV_cond     la condition SQL
   * @param string $PV_limit    la clause LIMIT
   * @param string $PV_group    la clause GROUP BY
   * @param string $PV_order    la clause ORDER BY
   * @param bool   $PV_explain  true pour faire une requête EXPLAIN au lieu de SELECT
   * @return int                le nombre de lignes sélectionnées ou false en cas d'erreur
   */
  public function selectO ($PV_classe, $PV_cond='', $PV_limit='', $PV_group='', $PV_order='', $PV_explain=false) {
    $table = $this->extraitTabledeClasse($PV_classe);
    if ($table === false) return false;

    $this->setClasse($PV_classe);

    return $this->_select($table, '*', $PV_cond, $PV_limit, $PV_group, $PV_order, $PV_explain);
  }

  /**
   * Sélectionne des lignes dans PLUSIEURS tables.
   *
   * On peut utiliser des noms de tables ou des classes Doctrine pour identifier les tables, la différence se fait sur la majuscule en premier caractère.
   *
   * @param array $PV_selects     la liste des select individuels sous la forme :
   *                  alias => array( 0 => nom de la table sous la forme base.table ou nom de la classe Doctrine,
   *                                  1 => champs à sélectionner de cette table : alias => nom_champ, array() pour aucun, '*' pour tous
   *                                       si un nom de classe Doctrine est donné ce champ est ignoré (on prend *)
   *                                  2 => condition SQL concernant cette table
   *                                  3 => true pour combiner la condition SQL de la table en ET (défaut) avec les autres conditions
   *                                       false pour combiner en OU
   *                                )
   *                où, alias est un nom d'alias pour cette table dans la requête
   * @param string $PV_jointure   la condition SQL de jointure entre les tables. Utiliser l'alias des tables
   * @param string $PV_limit      la clause LIMIT
   * @param string $PV_group      la clause GROUP BY
   * @param string $PV_order      la clause ORDER BY
   * @param bool   $PV_explain    true pour faire une requête EXPLAIN au lieu de SELECT
   * @return int                  le nombre de lignes sélectionnées ou false en cas d'erreur
   */
  public function union ($PV_selects, $PV_jointure='', $PV_limit='', $PV_group='', $PV_order='', $PV_explain=false) {

    $tous_champs = $toutes_tables = $toutes_cond = array();
    $grappe = $bdd = '';

    foreach ($PV_selects as $alias => $table) {

      // détermine le nom de la table

      $p = substr($table[0],0,1);
      if ($p == strtoupper($p)) {
        $nomtable = $this->extraitTabledeClasse($table[0]);
        if ($nomtable === false) return false;
        $est_classe = true;
      }
      else {
        $nomtable = $table[0];
        $est_classe = false;
      }

      // vérifie que toutes les tables de l'union sont sur la même grappe et la même base
      $db = $this->extraitBdddeTable($nomtable);
      if ($bdd != '' && $db != $bdd) { return false; }
      $bdd = $db;

      $g = $this->doChoixGrappe($nomtable);
      if ($grappe != '' && $g != $grappe) { return false; }
      $grappe = $g;

      $toutes_tables[] = $this->protegeNomTable($nomtable).(is_string($alias) ? ' AS '.$alias : '');
      $ref_table = $this->protegeNomTable(is_string($alias) ? $alias : $nomtable);

      // détermine les champs à sélectionner

      if ($est_classe) {
        $tous_champs[] = $ref_table.'.*';
      }
      else {
        if (is_array($table[1])) {
          foreach ($table[1] as $ac => $c) {
            $tous_champs[] = $ref_table.'.'.$this->protegeNomTable($c).(is_string($ac) ? ' AS '.$ac : '');
          }
        }
        else $tous_champs[] = $ref_table.'.*';
      }

      if ($table[2] != '') {
        $combi = empty($table[3]) || $table[3] ? true : false;
        // initialise la partie gauche des conditions
        $toutes_cond[] = (count($toutes_cond) == 0 ? ($combi ? '1' : '0') : '').($combi ? ' AND' : ' OR').' ( '.$table[2].' )';
      }

    }

    $r = sprintf(
      ($PV_explain ? 'EXPLAIN ' : '').'SELECT %s FROM %s WHERE %s',
      implode(',',$tous_champs),
      implode(',',$toutes_tables),
      ($PV_jointure == '' ? '1' : $PV_jointure).(count($toutes_cond) == 0 ? '' : ' AND ('.implode(' ',$toutes_cond).')')
    );

    if ($PV_group != '') $r .= ' GROUP BY '.$PV_group;
    if ($PV_order != '') $r .= ' ORDER BY '.$PV_order;
    if ($PV_limit != '') $r .= ' LIMIT '.$PV_limit;

    $this->videResultat(); // vide le résultat d'un précédent select au cas où celui-ci n'aurait pas de résultat
    $oldgrappe = $this->forceGrappeServeurs($grappe); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($bdd);

    $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);

    return $this->_resultat === false ? false : mysql_num_rows($this->_resultat);
  }

  /**
   * Met à jour des lignes d'une table
   *
   * @param string  $PV_table    le nom de la table sous la forme base.table
   * @param array   $PV_champs   les champs à mettre à jour, sous la forme : nom de champs => nouvelle valeur
   * @param string  $PV_cond     la condition SQL
   * @param string  $PV_limit    la clause LIMIT
   * @param bool    $PV_protege  true pour que les valeurs des champs soient échappés (défaut), false pour les laisser tels quels
   * @return int                 le nombre de lignes modifiées (0 n'est pas une erreur), false en cas d'erreur
   */
  public function update ($PV_table, $PV_champs, $PV_cond, $PV_limit='', $PV_protege=true) {
    return $this->_update($PV_table, $PV_champs, $PV_cond, $PV_limit, $PV_protege);
  }

  /**
   * Met à jour la ligne correspondante à un objet. FONCTION NON OPERATIONNELLE
   *
   * L'objet doit implémentaire toArray() pour récupérer ses champs.
   *
   * @param mixed $PV_objet     une instance d'une classe de modèle Doctrine, ces champs ayant été modifiés
   * @return int                1 si la ligne est modifiée, 0 si la ligne n'est pas modifiée (pas une erreur), false en cas d'erreur
   */
  private function updateO ($PV_objet) {
    $table = $this->extraitTabledeClasse($PV_objet);
    if ($table === false) return false;

    /* @var $PV_objet Doctrine_Record */
    $champs = $PV_objet->toArray();
    if (!is_array($champs) || count($champs) == 0) return false;

    $cond = ' = '; // AFAIRE : trouver la cond SQL sur le champ d'identification (autoinc ou unique)

    // AFAIRE + tard : filtrer les champs autoinc ? approfondir dans Doctrine pour trouvé les champs modifiés

    return $this->_update($table, $champs, $cond, '1', true);
  }

  /**
   * Supprime des lignes d'une table
   *
   * @param string $PV_table      le nom de la table sous la forme base.table sur laquelle supprimer des lignes
   * @param string $PV_cond       la condition SQL de sélection des lignes à supprimer
   * @param string $PV_limit      la clause LIMIT
   * @return int                  le nombre de lignes supprimées (0 n'est pas une erreur) ou false en cas d'erreur
   */
  public function delete ($PV_table, $PV_cond, $PV_limit='') {
    return $this->_delete($PV_table, $PV_cond, $PV_limit);
  }

  /**
   * Supprime une ligne d'une table correspondante à un objet.
   *
   * @param mixed $PV_objet   une instance d'une classe de modèle Doctrine qu'il faut supprimer de la table
   * @return int              1 si la ligne est supprimée, 0 si la ligne n'a pas été trouvée (pas une erreur), false en cas d'erreur
   */
  private function deleteO ($PV_objet) {
    $table = $this->extraitTabledeClasse($PV_objet);
    if ($table === false) return false;

    $cond = ' = '; // AFAIRE : trouver la cond SQL sur le champ d'identification (autoinc ou unique)

    return $this->_delete($table, $cond, '1');
  }

  /**
   * Insère des lignes dans une table
   *
   * @param string  $PV_table     le nom de la table sous la forme base.table où ajouter des lignes
   * @param array   $PV_ligne     la ou les lignes a ajouter sous la forme :
   *                                pour un insert simple   : array( nom champ => valeur )
   *                                pour un insert multiple : liste : array( n => array( nom champ => valeur ), )
   *                              Dans le cas d'un insert multiple chaque ligne doit avoir les mêmes champs
   * @param string  $PV_options   les options SQL à placer après le mot clef INSERT : IGNORE, DELAYED
   * @param bool    $PV_protege   true pour protéger chaque champ (encadré par ") (défaut)
   *                              false si l'appelant le fait lui même
   * @return int                  si $PV_ligne est un insert simple : l'autoincrement ou 0 si aucun (normal)
   *                              si $PV_ligne est un insert multiple (même avec une seule ligne) : le nombre de lignes insérées
   *                              false en cas d'erreur
   */
  public function insert ($PV_table, $PV_ligne, $PV_options='', $PV_protege=true) {
    return $this->_insert($PV_table, $PV_ligne, $PV_options,$PV_protege);
  }

  /**
   * Insère un objet dans sa table
   *
   * AFAIRE : non finie du coup reste en private
   *
   * L'objet doit implémenter toArray() pour récupérer ses champs.
   *
   * @param   mixed   $PV_objet     une instance de classe de modèle Doctrine qu'il faut insérer dans la table
   * @param   string  $PV_options   les options SQL à placer après le mot clef INSERT : IGNORE, DELAYED
   * @return  int                   l'autoincrement de la ligne insérée
   *                                0 si aucun autoincrement n'est disponible (pas une erreur)
   *                                false en cas d'erreur
   */
  private function insertO ($PV_objet, $PV_options='') {
    $table = $this->extraitTabledeClasse($PV_objet);
    if ($table === false) return false;

    /* @var $PV_objet Doctrine_Record */
    $champs = $PV_objet->toArray();
    if (!is_array($champs) || count($champs) == 0) return false;

    // AFAIRE : filtrer les champs pour trouver le champ d'identification (autoinc ou unique)

    $r = $this->_insert($table, $champs, $PV_options, true);

    return $r === false ? false : $this->_insert_id;
  }

  /**
   * Remplace une ligne dans la table.
   *
   * Le remplacement agit sur une colonne unique en supprimant l'éventuelle ligne existante et en insérant la nouvelle (aucun champ de l'ancienne n'est conservé)
   *
   * @param   string    $PV_table       le nom de la table au format base.table
   * @param   array     $PV_ligne       la ligne a remplacer : nomchamp => valeur
   * @param   bool      $PV_protege     true pour protéger chaque champ (encadré par ") (défaut)
   *                                    false si l'appelant le fait lui même
   * @return  int                       le nombre de lignes touchées ou false en cas d'erreur
   */
  public function replace ($PV_table, $PV_ligne, $PV_protege=true) {
    if (count($PV_ligne) == 0) return 0;

    $cols = array();
    $champs = '';

    foreach ($PV_ligne as $n => $v) {
      $cols[] = $this->protegeNomTable($n);
      $champs .= $this->protegeValeur($v,$PV_protege).',';
    }

    $r = sprintf('REPLACE INTO %s (%s) VALUES (%s)',
      $this->protegeNomTable($PV_table),
      implode(',',$cols),
      substr($champs,0,strlen($champs)-1)
    );

    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));

    $c = $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);

    return $c ? $this->_num_touche : false;
  }

  /**
   * Avance le pointeur interne de résultat sur la ligne suivante
   *
   * @return  bool    true si une ligne suivante est disponible et false si on a déjà eu la dernière
   */
  public function suivante () {
    $this->_ligne_actu = is_resource($this->_resultat) ? mysql_fetch_assoc($this->_resultat) : false;
    return $this->_ligne_actu !== false;
  }

  /**
   * Retourne la valeur du champ dans la ligne de résultat courante
   *
   * suivante() sera appelée si elle n'a jamais été appelée, sinon elle n'est pas appelée.
   *
   * @param   string  $PV_champ   le nom du champ
   * @param   mixed   $PV_defaut  la valeur a retourner si le champ n'existe pas dans le résultat
   * @return  string              la valeur du champ sous forme de chaine
   *                              $PV_defaut (null par défaut) si le champ n'existe pas
   *                              null s'il vaut SQL NULL
   */
  public function valeur ($PV_champ, $PV_defaut=null) {
    if ($this->_ligne_actu === false) $this->suivante();
    return $this->_ligne_actu !== false && array_key_exists($PV_champ,$this->_ligne_actu) ? $this->_ligne_actu[$PV_champ] : $PV_defaut;
  }

  /**
   * Retourne la ligne de résultat courante sous forme de tableau
   *
   * suivante() sera appelée si elle n'a jamais été appelée, sinon elle n'est pas appelée.
   *
   * @return  array     le tableau nom champ => valeur ou array() si aucune ligne n'est disponible
   */
  public function ligneCourante () {
    if ($this->_ligne_actu === false) $this->suivante();
    return $this->_ligne_actu === false ? array() : $this->_ligne_actu;
  }

  /**
   * Retourne la ligne de résultat courante sous forme d'Objet
   *
   * Aucune vérification n'est faite sur la classe passée, le seul pré-requis est qu'elle définisse la méthode fromArray() qui initialise l'objet avec un tableau associatif issue de la base.
   *
   * La classe est trouvée de deux façons :
   * 1) soit on a obtenu ce résultat avec @see ADialogueBD::selectO()
   * 2) soit on a précisé préalablement la classe avec @see ADialogueBD::setClasse()
   *
   * @return  object      une instance de la classe de modèle Doctrine ou null en cas d'erreur (classe inexistante ou incompatible avec le résultat)
   */
  public function objetCourant () {
    if ($this->classe_courante == '') return null;

    $ligne = $this->ligneCourante();
    if ($ligne === array()) return null;

    try {
      /* @var $o Doctrine_Record */
      $o = new $this->classe_courante;
      $o->fromArray($ligne);
    }
    catch (Exception $e) { return null; }

    return $o;
  }

  /**
   * Retourne toutes les lignes du résultat courant, une ligne étant en forme de tableau PHP
   *
   * Vide les résultats de l'instance DialogueBD.
   *
   * @param   bool    $PV_collect       true pour recevoir une Collection, false pour avoir une liste de tableau (défaut)
   * @return  mixed                     selon $PV_collect
   */
  public function toutesLignes ($PV_collect=false) {
    $res = array();
    while ($this->suivante()) $res[] = $this->ligneCourante();
    $this->videResultat();
    $cls = $this->_cls_collection;
    return $PV_collect ? new $cls($res) : $res;
  }

  /**
   * Retourne toutes les lignes du résultat courant, une ligne étant en forme d'objet
   *
   * Vide les résultats de l'instance DialogueBD.
   *
   * @param   bool    $PV_collect       true pour recevoir une Collection (défaut), false pour avoir une liste d'objets
   * @return  mixed                     selon $PV_collect. L'objet de collection est de la classe self::$_cls_collection
   */
  public function tousObjets ($PV_collect=true) {
    $classe = $this->_cls_collection;
    $res = new $classe;
    while ($this->suivante()) $res->insere($this->objetCourant());
    $this->videResultat();

    return $PV_collect ? $res : $res->toArray();
  }

  /**
   * Supprime le résultat courant (ne touche pas à la base)
   */
  public function videResultat () {
    if (is_resource($this->_resultat)) @mysql_free_result($this->_resultat);
    $this->_resultat = false;
    $this->_ligne_actu = false;
    $this->_code_resultat = '';
  }

  /**
   * Indique si on veut faire une requête sur le maitre ou sur l'esclave
   *
   * @param   bool  $PV_bool    true pour faire les prochaines requêtes sur le maitre et false pour les faire sur l'esclave
   * @return  bool              l'ancienne valeur
   */
  public function choisitMaitre ($PV_bool) {
    $old = $this->req_maitre;
    $this->req_maitre = $PV_bool ? true : false;
    return $old;
  }

  /**
   * Positionne la classe d'objet pour les méthodes de retour d'objet
   *
   * La classe peut ne pas être dérivée de Doctrine si elle a les caractéristique nécessaires.
   * @see objetCourant()
   *
   * @param string $PV_classe     un nom de classe modèle Doctrine
   */
  public function setClasse ($PV_classe) {
    $this->classe_courante = $PV_classe;
  }

  /**
   * Indique si l'on veut fermer la connexion après chaque requête
   *
   * @param   bool  $PV_bool    true pour fermer la connexion après chaque requête et false pour la laisser ouverte
   */
  public function FermeAuto ($PV_bool) {
    $this->ferme_auto = $PV_bool;
  }

  /**
   * Ferme toutes les connexions ouvertes par cet objet.
   *
   * Le pool de connexion est partagé par tous les objets de la classe, cette méthode ne ferme que celles ouverte par cette instance.
   *
   * @param   mixed   $PV_valret    la valeur que la fonction doit retourner
   * @return  mixed                 la même valeur que $PV_valret
   */
  public function ferme ($PV_valret=null) {
    $this->videResultat();
    foreach ($this->conn_ouvertes as $conn) self::doFerme($conn);
    $this->conn_ouvertes = array();
    return $PV_valret;
  }

  /**
   * Positionne la base de donnée des tables issues des classes de modèle Doctrine
   *
   * Si cette méthode n'est pas appelée, la base qui sera utilisée est $_default_db
   *
   * Si la classe de modèle Doctrine définit une méthode getDatabaseName() la valeur de celle-ci sera prise à la place.
   *
   * @param   string  $PV_bdd     un nom de base de donnée
   * @return  string              l'ancienne base qui avait été choisie
   */
  public function setBdd ($PV_bdd) {
    $old = $this->db_courante;
    $this->db_courante = $PV_bdd;
    return $old;
  }

  /**
   * Indique si les prochaines requêtes sont susceptibles d'échouer.
   *
   * Si une requête échoue après appel à true de cette fonction on ne levera pas d'erreur.
   *
   * @param   bool    $PV_bool    true pour indiquer que les prochaines requêtes peuvent échouer
   *                              false si les requêtes ne doivent pas échouer (défaut)
   * @return  bool                la valeur précédente
   */
  public function peutEchouer ($PV_bool) {
    $old = $this->erreur_possible;
    $this->erreur_possible = $PV_bool ? true : false;
    return $old;
  }

  /**
   * Retourne la classe d'objet positionnée précédemment
   *
   * @return string       un nom de classe modèle Doctrine
   */
  public function getClasse () {
    return $this->classe_courante;
  }

  /**
   * Change le nombre de tentatives de connexions sur une ip
   *
   * A chaque nouvelle tentative (après la 2ème) le délai d'attente est augmenté
   *
   * @param   int   $PV_nbr     le nombre d'essais souhaités, minimum 1
   */
  public static function setNombreEssaisConn ($PV_nbr) {
    self::$nbr_essais_conn = max(1,$PV_nbr);
  }

  /**
   * Retourne la dernière requête exécutée
   *
   * @return string   le code SQL de la dernière requête exécutée
   */
  public function getRequete () {
    return $this->der_req;
  }

  /**
   * Permet d'indiquer la grappe de serveurs pour les prochaines requêtes
   *
   * Cette méthode n'est utile que si on utilise pas les méthodes de requêtage toutes faites (select, update, insert, ...)
   *
   * @param   string    $PV_grappe    le nom de la grappe (cf le commentaire de classe, partie noyau)
   * @return  string                  le nom de l'ancienne grappe choisie
   */
  public function forceGrappeServeurs ($PV_grappe) {
    $old = $this->grappe_courante;
    $this->grappe_courante = $PV_grappe;
    return $old;
  }

  /**
   * @see ADialogueBD::select()
   */
  protected function _select ($PV_table, $PV_champs='*', $PV_cond='', $PV_limit='', $PV_group='', $PV_order='', $PV_explain=false, $PV_protegNom= true) {
    $r = ($PV_explain ? 'EXPLAIN ' : '').'SELECT ';

    if (is_string($PV_champs)) {
      if ($PV_champs == '*') $r .= '*';
      elseif($PV_protegNom) {
        $r .= implode(',',array_map(array($this,'protegeNomTable'),explode(',',$PV_champs)));
      }else{
        $r .= $PV_champs;
      }
    }
    elseif (is_array($PV_champs)) {
      $nbr = count($PV_champs);
      foreach ($PV_champs as $alias => $champ) {
        $champ = $this->protegeNomTable($champ);
        $r .= (is_string($alias) ? $champ.' AS '.$this->protegeNomTable($alias) : $champ).(--$nbr > 0 ? ',' : '');
      }
    }
    else return false;

    $r .= ' FROM '.$this->protegeNomTable($PV_table);

    if ($PV_cond != '') $r .= ' WHERE '.$PV_cond;
    if ($PV_group != '') $r .= ' GROUP BY '.$PV_group;
    if ($PV_order != '') $r .= ' ORDER BY '.$PV_order;
    if ($PV_limit != '') $r .= ' LIMIT '.$PV_limit;

    $this->videResultat(); // vide le résultat d'un précédent select au cas où celui-ci n'aurait pas de résultat
    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));
    $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);

    return $this->_resultat === false ? false : mysql_num_rows($this->_resultat);
  }

  /**
   * Exécute un show table status
   *
   * Cette fonction n'écrase pas la ressource de résultat d'un select précédent.
   *
   * @param   string    $PV_table     le nom de la table au format base.table
   * @return  array                   false en cas d'erreur
   *                                  array() si la table n'existe pas
   *                                  les champs du show table status
   *                                    'Name'            => nom
   *                                    'Type'            => type
   *                                    'Row_format'      => Fixed, Dynamic ou Compressed
   *                                    'Rows'            => le nom de lignes
   *                                    'Avg_row_length'  => la taille moyenne d'une ligne en octets
   *                                    'Data_length'     => taille du fichier de donnée en octets
   *                                    'Max_data_length' => taille maximum des données possible
   *                                    'Index_length'    => taille du fichier d'index en octets
   *                                    'Data_free'       => nombre d'octets alloués mais non utilisés
   *                                    'Auto_increment'  => prochaine valeur d'auto increment
   *                                    'Create_time'     => DATETIME de création
   *                                    'Update_time'     => DATETIME de mise à jour
   *                                    'Check_time'      => DATETIME de dernier check
   *                                    'Collation'       => jeu de caractère et collation
   *                                    'Checksum'        => somme de contrôle si disponible
   *                                    'Create_options'  => options du create table
   *                                    'Comment'         => commentaires de la table.
   *                                                         pour innodb contiendra l'espace disque libre
   */
  public function showTable ($PV_table) {
    list($base,$table) = $this->extraitBdddeTable($PV_table, true);
    $r = 'SHOW TABLE STATUS FROM '.$this->protegeNomTable($base).' LIKE "'.$table.'"';

    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($base);
    $this->backupResultat();

    $c = $this->doRequete($r);

    if ($c) {
      $res = $this->toutesLignes();
      if (count($res) > 0) $res = $res[0];
    }
    else $res = false;

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);
    $this->restoreResultat();

    return $res;
  }

  /**
   * @see ADialogueBD::insert()
   */
  protected function _insert ($PV_table, $PV_ligne, $PV_options='', $PV_protege=true) {
    if (count($PV_ligne) == 0) return 0;
    if (is_int(key($PV_ligne))) $simple = false;
    else {
      $simple = true;
      $PV_ligne = array($PV_ligne); // met en multiligne
    }

    // lit la première ligne pour trouver le nom des colonnes et le premier enregistrement à mettre
    reset($PV_ligne);
    $cols = array();
    $clefs = array();
    $champs = '';

    foreach (current($PV_ligne) as $n => $v) {
      $cols[] = $this->protegeNomTable($n);
      $clefs[] = $n;
      $champs .= $this->protegeValeur($v,$PV_protege).',';
    }

    $r = sprintf('INSERT %s INTO %s (%s) VALUES (%s)',
      $PV_options,
      $this->protegeNomTable($PV_table),
      implode(',',$cols),
      substr($champs,0,strlen($champs)-1)
    );

    // met le reste des valeurs directement dans la requête (optimisation mémoire)
    $max = count($clefs);
  	next($PV_ligne);
  	while (list(,$ligne) = each($PV_ligne)) { // pour chaque ligne
      $cpt = $max; $r .= ',(';
  		foreach ($clefs as $key) { // on parcours par clef pour faire respecter le premier ordre
  			$r .= $this->protegeValeur(isset($ligne[$key]) ? $ligne[$key] : null,$PV_protege);
  			if (--$cpt > 0) $r .= ',';
  		}
  		$r .= ')';
    }

    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));
    if ($simple) $oldins = $this->veutInsertId (true);

    $c = $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);
    if ($simple) $this->veutInsertId($oldins);

    return $c ? ($simple ? $this->_insert_id : $this->_num_touche) : false;
  }

  /**
   *
   * @see ADialogueBD::update()
   */
  protected function _update ($PV_table, $PV_champs, $PV_cond, $PV_limit='', $PV_protege=true) {

    $vals = array();
    foreach ($PV_champs as $n => $v) { $vals[] = $this->protegeNomTable($n).' = '.$this->protegeValeur($v,$PV_protege); }
    if (count($vals) == 0) return 0; // aucune modification à faire => rien à modifier

    $r = sprintf('UPDATE %s SET %s',$this->protegeNomTable($PV_table),implode(',',$vals));

    if (strlen($PV_cond) > 0)   $r .= ' WHERE '.$PV_cond;
    if (strlen($PV_limit) > 0)  $r .= ' LIMIT '.$PV_limit;

    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));

    $c = $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);

    return $c ? $this->_num_touche : false;
  }

  /**
   * @see ADialogueBD::delete()
   */
  protected function _delete ($PV_table, $PV_cond, $PV_limit='') {
    $r = 'DELETE FROM '.$this->protegeNomTable($PV_table);

    if (strlen($PV_cond) > 0)   $r .= ' WHERE '.$PV_cond;
    if (strlen($PV_limit) > 0)  $r .= ' LIMIT '.$PV_limit;

    $oldgrappe = $this->choisitGrappe($PV_table); // écrasera une éventuelle valeur mise par forceGrappeServeurs()
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));

    $c = $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);

    return $c ? $this->_num_touche : false;
  }

  /**
   * Choisit la grappe de serveur hébergeant la table passée
   *
   * Cette méthode est interne, pour faire un choix depuis l'extérieur il faut utiliser @see ADialogueBD::forceGrappeServeurs()
   *
   * @param   string  $PV_table   le nom de la table sous la forme base.table
   * @return  string              le nom de l'ancienne grappe (qui a été remplacée)
   */
  protected function choisitGrappe ($PV_table) {
    $choix = $this->doChoixGrappe($PV_table);
    $old = $this->grappe_courante;
    $this->grappe_courante = $choix;

    return $old;
  }

  /**
   * FONCTION SPECIALE A MANIER AVEC PRECAUTIONS.
   *
   * Permet d'exécuter une requête SQL quelconque.
   *
   * @param   string    $PV_req     la requête SQL a exécuter
   * @param   string    $PV_table   le nom de la table sur laquelle la requête va être lancée
   *                                ou au moins une des tables concernées
   * @param   bool      $PV_maitre  true si la requête doit se faire sur le maitre
   *                                false sur un esclave
   *                                null si on garde l'état courant de cette valeur
   * @return  bool                  true si la requête a bien été exécutée
   *                                false sinon
   */
  public function requete ($PV_req, $PV_table, $PV_maitre=null) {

    $r = str_replace($PV_table,$this->protegeNomTable($PV_table),$PV_req);

    if (is_bool($PV_maitre)) {
      $oldmaitre = $this->req_maitre;
      $this->choisitMaitre($PV_maitre);
    }

    $oldgrappe = $this->choisitGrappe($PV_table);
    $oldbdd = $this->setBdd($this->extraitBdddeTable($PV_table));

    $c = $this->doRequete($r);

    $this->forceGrappeServeurs($oldgrappe); // remet la valeur précédente
    $this->setBdd($oldbdd);
    if (is_bool($PV_maitre)) { $this->choisitMaitre($oldmaitre); }

    return $c;
  }

  /**
   * Méthode interne réalisant une requête
   *
   * @param   string $PV_str    la requête au format SQL
   * @return  bool              true si la requête a été exécutée et false sinon
   */
  protected function doRequete ($PV_str) {
    $this->der_req = $PV_str;
    $maitre = $this->req_maitre; // utilise une variable pour ne pas changer la valeur originale souhaitée

    if (!$maitre) { // vérifie qu'on a pas une requête de modification
      switch (strtoupper(substr($PV_str,0,strpos($PV_str,' ')))) {
        case 'SELECT': break;
        case 'EXPLAIN': break;
        case 'SHOW': break;
        case 'DESCRIBE': break;
        default: $maitre = true; // c'est une requete qui DOIT se faire sur le maitre (dans le doute on le fait aussi dessus)
      }
    }

    if ($this->grappe_courante == '') {
      $this->choisitGrappe($this->_default_db);
      error_log(__METHOD__.' : grappe vide, choisit "'.$this->grappe_courante.'"',0);
    }

    $conn = self::connecte($maitre,$this->grappe_courante);
    if ($conn === false) {
      error_log(__METHOD__.' : ECHEC de la requete (connexion) : '.$this->der_req,0);
      return false;
    }

    mysql_select_db($this->db_courante, $conn);

    // essaie d'executer la requete 2 fois, on etale la boucle pour plus de perf
    $r = @mysql_query($PV_str,$conn);
    if ($r === false) { // ressaie
      // un code d'erreur >= 2000 indique une erreur du client, en general probleme de connexion
      // une erreur de code 1000-1999 est une erreur du serveur, en general un probleme dans la requete
      if (mysql_errno($conn) >= 2000) {
        $conn = self::connecte($maitre,$this->grappe_courante,$conn);
        if ($conn === false) {
          error_log(__METHOD__.' : ECHEC de la requete (connexion 2eme) : '.$this->der_req,0);
          return false;
        }
        mysql_select_db($this->db_courante, $conn);
        $r = @mysql_query($PV_str,$conn);
      }
    }
    // fin de l'execution

    if (!in_array($conn,$this->conn_ouvertes)) $this->conn_ouvertes[] = $conn;

    if ($r === false) { // la requête a échouée au bout de une ou deux tentatives selon l'erreur
      if (!$this->erreur_possible) error_log(__METHOD__.' : ECHEC de la requete (exec) : '.mysql_error($conn)."\n".$this->der_req,0);
      return false;
    }

    // on ne modifie ressource de résultat que si on en a une nouvelle pour conserver le resultat d'un select precedent apres un update
    if (is_resource($r)) $this->_resultat = $r;
    else {
      $this->_code_resultat = $r;
      $this->_num_touche = mysql_affected_rows($conn);
      if ($this->_ret_insert_id) $this->_insert_id = mysql_insert_id($conn);
    }

    if (self::$log_req)   self::doLogReq($this->der_req,$this->grappe_courante,$maitre,$conn);
    if (self::$log_bench) self::$_liste_conn[self::donneIP($conn)][5][2]++;

    if ($this->ferme_auto) $this->ferme();

    return true;
  }

  /**
   * Ouvre une connexion
   *
   * ATTENTION : self::$_rep_conf doit être initialisé avant l'appel à la méthode.
   *
   * Le login/mdp est pris dans le databases.yml de Sf. ILS DOIVENT ÊTRE IDENTIQUES POUR TOUS LES SERVEURS.
   *
   * Le process d'attente entre les tentatives de connexion est le suivant :
   *
   * 1) mysql_connect                   (tentative = 1)
   * 2) mysql_connect                   (tentative = 2)
   * 3) sleep 2 + ln(2) ~ 2,69 sec      (tentative = 2)
   * 4) mysql_connect                   (tentative = 3)
   * 5) sleep 2 + ln(3) ~ 2,69 sec      (tentative = 3)
   * x) mysql_connect                   (tentative = self::$nbr_essais_conn)
   * y) abandonne
   *
   *
   *
   * @param   bool        $PV_maitre        true pour se connecter sur le maitre de la grappe, false pour choisir un esclave
   * @param   string      $PV_grappe        nom de la grappe sur laquelle se connecter
   * @param   ressource   $PV_conn_erreur   la ressource de connexion en erreur, null si pas d'erreur à signaler
   * @return  ressource                     la ressource de connexion choisie ou false si une connexion est impossible
   */
  protected static function connecte ($PV_maitre, $PV_grappe, $PV_conn_erreur=null) {
    static $user, $mdp;

    if ($PV_grappe == '') {
      error_log(__METHOD__.' : ANORMAL connexion sur grappe vide. ECHEC.',0);
      return false;
    }

    if ($user == '') list($user,$mdp) = self::chargeUserMdp();
    if ($user == '') {
      error_log(__METHOD__.' : ANORMAL user/mdp vides. ECHEC.',0);
      return false;
    }

    $code_maitre = $PV_maitre ? 1 : 2;

    // recherche si un connexion ouverte ne correspond pas déjà à la demande d'ouverture (ne prend pas la connexion marquée en erreur)
    $trouve = '';
    foreach (self::$_liste_conn as $clef => $tab) {
      if (($tab[2] & $code_maitre) == 0) continue;
      if (($PV_conn_erreur === null || $PV_conn_erreur !== $tab[0]) && in_array($PV_grappe,$tab[3])) {
        $trouve = $clef;
        break;
      }
    }

    if ($trouve == '') {
      $trouve = self::choisitServeur($PV_maitre,$PV_grappe,$PV_conn_erreur);
      // choisitServeur() essayera sur le maitre si aucun esclave n'est possible => nous ne pouvons plus rien tenter
      if ($trouve == '') {
        error_log(__METHOD__.' : ANORMAL aucune ip trouvee. ECHEC.',0);
        return false;
      }
    }

    // ici on a trouve une ip sur laquelle se connecter

    if (!isset(self::$_liste_conn[$trouve])) {
      self::$_liste_conn[$trouve] = array(
          0 => null,
          1 => 0,
          2 => 0,
          3 => array(),
          4 => array(),
          5 => array(
            0 => time(),
            1 => 0,
            2 => 0,
            3 => 0,
          ),
      );
    }

    if (self::$_liste_conn[$trouve][0] === null) {
      $i = 1; $r = false;
      do {
        $r = mysql_connect($trouve,$user,$mdp,true);

        if ($r === false && $i > 1) {
          error_log(__METHOD__.' : essai '.$i.'/'.self::$nbr_essais_conn.', connexion impossible a '.$trouve,0);
          if ($i != self::$nbr_essais_conn) {
            usleep(intval((2 + log($i)) * 1000000));
            // on cherche un nouveau serveur, si on était sur le maitre cela permettra de relire la conf au cas où l'ip aurait changée
            $t = self::choisitServeur($PV_maitre,$PV_grappe,$trouve);
            if ($t == '') return false;
            if ($t != $trouve) $i = 0;
            $trouve = $t;
          }
        }
      } while ($r === false && ++$i <= self::$nbr_essais_conn);

      if ($r === false) {
        error_log(__METHOD__.' : echec de connexion sur '.$trouve,0);
        return false;
      }

      self::$_liste_conn[$trouve][0] = $r;
      mysql_query('SET NAMES "binary"',$r); // compatibilité UTF-8 : on ne transforme pas les données entre PHP et la base
    }


    self::$_liste_conn[$trouve][1]++;
    self::$_liste_conn[$trouve][2] |= $code_maitre;
    if (!in_array($PV_grappe,self::$_liste_conn[$trouve][3])) self::$_liste_conn[$trouve][3][] = $PV_grappe;

    return self::$_liste_conn[$trouve][0];
  }

  /**
   * Choisit un serveur correspondant aux critères
   *
   * La condition minimum pour faire un choix est qu'une ip soit présente dans le fichier maitre.
   * Si tout échoue c'est cette ip qui sera choisie.
   *
   * ATTENTION : self::$_rep_conf doit être initialisé avant l'appel à la méthode
   *
   * @param   bool        $PV_maitre        true pour choisir le serveur maitre de la grappe, false pour choisir un esclave
   * @param   string      $PV_grappe        nom de la grappe concernée
   * @param   mixed       $PV_conn_erreur   la ressource de connexion en erreur
   *                                        l'ip du serveur en erreur (si on a même pas pu faire de connexion dessus)
   *                                        null si pas d'erreur à signaler
   * @return  string                        l'adresse du serveur choisit sous la forme ip:port ou '' si le choix est impossible
   */
  protected static function choisitServeur ($PV_maitre, $PV_grappe, $PV_conn_erreur=null) {
    $fichier = self::$_rep_conf.$PV_grappe.'/'.($PV_maitre ? 'master' : 'slaves');

    $liste = @file($fichier);
    // aucun candidats en esclave, on prend le maitre, sinon on échoue
    if (!is_array($liste) || count($liste) == 0) { return $PV_maitre ? '' : self::choisitServeur(true,$PV_grappe,$PV_conn_erreur); }

    $candidats = array();
    $non = array();
    if (!$PV_maitre && is_string($PV_conn_erreur)) $non[] = $PV_conn_erreur;

    foreach ($liste as $ip) {
      $ip = trim($ip);
      if (strlen($ip) == 0 || substr($ip,0,1) == '#') continue; // ignore une ligne commentée ou une ligne vide

      $p = strpos($ip,':');
      if ($p === false) { $p = strlen($ip); $ip .= ':3306'; }
      elseif ($p == strlen($ip) - 1) $ip .= '3306'; // : en fin sans le port !

      $est_localhost = false;
      $p2 = strrpos($ip,'.');
      if ($p2 === false) {
        if (substr($ip,0,$p) == 'localhost') $est_localhost = true;
        else continue;
      }

      if (!$est_localhost && !is_numeric(substr($ip,$p2+1,$p - $p2 - 1))) { // un nom de domaine, on doit le résoudre en ip
        $ips = gethostbynamel(substr($ip,0,$p));
        if (!is_array($ips)) continue;

        $port = substr($ip,$p);
        foreach ($ips as $a) {
          $a .= $port;
          if (!in_array($a,$non)) { $candidats[] = $a; }
        }
      }
      elseif (!in_array($ip,$non)) { $candidats[] = $ip; }
    }

    // aucun candidats en esclave, on prend le maitre, sinon on échoue
    if (count($candidats) == 0) return $PV_maitre ? '' : self::choisitServeur(true,$PV_grappe,$PV_conn_erreur);

    // de préférence, re-choisi une connexion déjà ouverte parmi les candidats
    foreach ($candidats as $ip) {
      if (isset(self::$_liste_conn[$ip]) && self::$_liste_conn[$ip][0] !== $PV_conn_erreur) return $ip;
    }

    return $candidats[mt_rand(0,count($candidats) - 1)];
  }

  /**
   * Ferme une connexion
   *
   * Décrémente le compteur et ferme réellement la connexion si plus aucun objet ne s'en sert.
   *
   * @param   ressource   $PV_connexion     la ressource de connexion à fermer
   */
  protected static function doFerme ($PV_connexion) {

    // trouve l'ip à fermer
    foreach (self::$_liste_conn as $ip => $tab) {
      if ($tab[0] !== $PV_connexion) continue;

      self::$_liste_conn[$ip][1]--;
      if (self::$_liste_conn[$ip][1] > 0) return;

      if (count($tab[4]) > 0 && mysql_ping($tab[0])) { // ferme les verrous
        foreach ($tab[4] as $verrou) { @mysql_query('DO RELEASE_LOCK("'.$verrou.'")',$tab[0]); }
      }

      @mysql_close($tab[0]);

      self::$_liste_conn[$ip][0] = null;
      self::$_liste_conn[$ip][1] = 0;
      // on ré-initialise aussi les données maitre et grappe au cas où cela changerait avant la prochaine utilisation de l'ip
      self::$_liste_conn[$ip][2] = 0;
      self::$_liste_conn[$ip][3] = array();
      self::$_liste_conn[$ip][4] = array();
      self::$_liste_conn[$ip][5][1] = time();
      self::$_liste_conn[$ip][5][3]++;

      break;
    }
  }

  static private function donneIP ($PV_conn) {
    foreach (self::$_liste_conn as $ip => $tab) {
      if ($tab[0] === $PV_conn) return $ip;
    }
    return '0.0.0.0';
  }

  /**
   * Récupère les identifiants de connexion depuis le fichier my.cnf de l'arborescence de configuration.
   *
   * ATTENTION : self::$_rep_conf doit être initialisé avant l'appel à la méthode
   *
   * @return  array       Le couple (login,mdp) ou array('','') en cas d'erreur
   */
  protected static function chargeUserMdp () {
    $params = self::parseINIFile(self::$_rep_conf.'my.cnf', true);
    if (!isset($params['client']['user'])) return array('','');
    return array($params['client']['user'],isset($params['client']['password']) ? $params['client']['password'] : '');
  }

  /**
   * Protège le nom de la table pour éviter un conflit avec un mot clef.
   *
   * Le caractère de protection est le backtique `
   *
   * @param   string $PV_table  le nom de la table
   * @return  string            le nom de la table protégé ou identique s'il était déjà protégé
   */
  private function protegeNomTable ( $PV_table ) {
    if (substr(ltrim($PV_table),0,1) == '`') return $PV_table; // déjà protégé
    // pour un nom composé, il faut protéger chaque partie indépendamment
    return '`'.str_replace('.','`.`',trim($PV_table)).'`';
  }

  /**
   * S'assure qu'une valeur de champs est compatible pour être mise dans une requête
   *
   * Les valeurs non scalaire sont sérializées.
   *
   * Il n'est pas tenu compte de magic_quotes_gpc car cette méthode ne peut pas savoir si les valeurs passées viennent de GPC ou pas.
   * C'est de toute évidence à celui qui manipule ces valeurs de les échapper proprement.
   *
   * @param   string    $PV_valeur     la valeur du champ à protéger
   * @param   bool      $PV_protege   true pour ajouter des quotes autour de la valeur (défaut) et false pour ne pas le faire
   * @return  string                  la valeur modifiée
   */
  public function protegeValeur ($PV_valeur, $PV_protege=true) {
    if (is_null($PV_valeur)) {
      $valeur = 'NULL';
      $PV_protege = false;
    }
    elseif (!is_scalar($PV_valeur)) {
      $valeur = serialize($PV_valeur);
      $PV_protege = true; // il faut forcément ajouter des quotes autour
    }
    else $valeur = $PV_valeur;

    // on ne peut pas utiliser mysql_real_escape_string() car nous n'avons pas de connexion ouverte à ce stade
    if ($PV_protege) {
      $valeur = addslashes($valeur);
      $valeur = "'".$valeur."'";
    }

    return $valeur;
  }

  /**
   * Retourne le nom de table à partir d'une classe de modèle Doctrine
   *
   * Le nom de la base de donnée associée à la table est choisie de la façon suivante :
   *
   *  1) si la méthode getDatabaseName() existe sur la classe $PV_classe on utilise son retour
   *  2) sinon utilise $this->db_courante qui vaut soit $_default_db soit la valeur passée à setBdd()
   *
   * @param   mixed   $PV_classe    le nom de la classe de modèle Doctrine de la table ou une instance
   * @return  string                le nom de la table sous la forme base.table ou false en cas d'erreur (classe de modèle inexistante)
   */
  private function extraitTabledeClasse ($PV_classe) {
    try {
      /* @var $o Doctrine_Record */
      $o = is_object($PV_classe) ? $PV_classe : new $PV_classe();
      $table = $o->getTable()->getTableName();
    }
    catch(Exception $e) {
      return false;
    }

    if (is_callable(array($o,'getDatabaseName'))) $db = $o->getDatabaseName();
    else $db = $this->db_courante;

    return $db.'.'.$table;
  }

  /**
   * Retourne le nom de la base de données à partir d'un nom de table complet
   *
   * @param   string    $PV_table     le nom de la table au format base.table ou `base`.`table`
   * @param   bool      $PV_explose   true pour avoir un couple (bdd,table)
   *                                  false pour avoir que bdd (défaut)
   * @return  string                  le nom de la base de données sans caractère de protection
   */
  protected function extraitBdddeTable ($PV_table,$PV_explose=false) {
    $PV_table = str_replace(array('`'),array(''),$PV_table);

    $p = strpos($PV_table,'.');
    if ($p === false) $bdd = $PV_table;
    else $bdd = substr($PV_table,0,$p);

    return $PV_explose ? array($bdd,substr($PV_table,$p+1)) : $bdd;
  }

  protected function veutInsertId ($PV_val) {
    $old = $this->_ret_insert_id;
    $this->_ret_insert_id = $PV_val;
    return $old;
  }

  /**
   * Sauve les attributs de résultat pour ne pas les écraser.
   * Plusieurs appels peuvent être fait, les résultats seront restorés dans l'ordre.
   */
  protected function backupResultat () {
    array_push($this->_backup_res, array($this->_resultat,  $this->_ligne_actu));
  }

  /**
   * Restore les attributs de résultat sauvés en dernier
   */
  protected function restoreResultat () {
    $couple = array_pop($this->_backup_res);
    if (is_array($couple)) {
      $this->_resultat    = $couple[0];
      $this->_ligne_actu  = $couple[1];
    }
  }

  static protected function initRepConf ($PV_val) {
    if (self::$_rep_conf == '') self::$_rep_conf = $PV_val;
  }

  static protected function initRepLog ($PV_val) {
    if (self::$_rep_log == '') self::$_rep_log = $PV_val;
  }

  /**
   * Lit la configuration stockée dans un fichier de configuration de type .ini.
   *
   * Le format reconnu est :
   * - les espaces en début et fin de ligne sont ignorés
   * - une ligne commençant par # est une ligne de commentaire
   * - une ligne contenant le texte : [texte] ou texte est un texte sans crochet fermant définit un groupe auquel seront rattachées toutes les lignes qui suivent.
   * - une ligne de d'affectation d'attributs la forme :
   * 			texte = reste ou texte est un texte ne contenant pas de = et reste contient n'importe quel texte et ou il peut y avoir des espaces ou non entre le signe égal (ils seront ignorés).
   * - une ligne autre est ignorée (notamment une ligne vide)
   *
   * Si un groupe apparait plusieurs fois dans le fichier les attributs sont rassemblés.
   *
   * @param   string    $PV_fichier     le nom complet du fichier à lire
   * @param   bool      $PV_groupes     true si on veut groupés les attributs par "groupe" (défaut)
   *                                    false si on veut tous les attributs au même niveau
   * @return  array                     si $PV_groupes = true   : nom_groupe => nom_attribut => valeur
   * 																		si $PV_groupes = false  : nom_attribut => valeur
   *                                    un attribut présent plusieurs fois a la valeur de la dernière occurence
   *                                    array() si le fichier est vide ou non trouvé
   */
  public static function parseINIFile ( $PV_fichier, $PV_groupes=true ) {
    $ret = array();
    $lignes = @file($PV_fichier);
    if ($lignes === false) return array();
    $groupe = '';
    foreach ($lignes as $ligne) {
      $l = trim($ligne);
      $c = substr($l,0,1);
      if ($c == '#') continue; // ligne de commentaire
      elseif ($c == '[') {
        if (!$PV_groupes) continue; // on n'est pas intéressé par les lignes de groupes
        $p = strpos($l,']');
        if ($p === false) continue; // c'est une ligne de groupe incorrecte, on l'ignore
        $groupe = substr($l,1,$p - 1);
        if (!isset($ret[$groupe])) $ret[$groupe] = array();
      }
      else {
        $p = strpos($l,'=');
        if ($p === false) continue; // ligne inconnue
        $attrib = rtrim(substr($l,0,$p));
        $val = ltrim(substr($l,$p+1));
        if ($groupe == '') $ret[$attrib] = $val;
        else $ret[$groupe][$attrib] = $val;
      }
    }
    return $ret;
  }

  static protected function doLogReq ($PV_req, $PV_grappe, $PV_maitre, $PV_conn) {
    $str = date('H:i:s')."\t".getmypid()."\t".
            $PV_grappe."\t".
            ($PV_maitre ? 'M' : 'E')."\t".
            self::donneIP($PV_conn)."\t".
            $PV_req."\n"
    ;

    $format = "heure\tpid\tgrappe\tMaitre/Esclave\tIP\trequete\n";
    self::ecritLog($str, self::$_rep_log.'requetes'.date('YmdH').'.log',$format);
  }

  /**
   * NE PAS UTILISER. Cette méthode est public pour être appelée en callback par register_shutdown_function.
   */
  static public function doLogBench () {
    $now = time();
    $entete = date('H:i:s',$now)."\t".getmypid()."\t";
    $str = '';

    foreach (self::$_liste_conn as $ip => $tab) {
      $str .= $entete.
              reset($tab[3]).(count($tab[3]) > 1 ? 'S' : '')."\t".
              ($tab[2] == 3 ? 'D' : ($tab[2] == 1 ? 'M' : 'E'))."\t".
              $ip."\t".
              $tab[5][2]."\t".
              ($tab[5][1] == 0 ? ($now - $tab[5][0]).'!' : $tab[5][1] - $tab[5][0])."\t".
              $tab[5][3]."\n"
      ;
    }

    $format = "heure\tpid\tgrappe(S)\tMaitre/Esclave/Dual\tIP\tnb req\tcumul tps ouvert\tnb fermeture\n";
    self::ecritLog($str, self::$_rep_log.'bench'.date('YmdH').'.log',$format);
  }

  /**
   * Ecrit un message dans un fichier
   *
   * @param   string    $PV_str       le message complet (\n final inclu)
   * @param   string    $PV_fichier   le fichier à écrire. Si lui où un répertoire n'existe pas ils seront crées.
   * @param   string    $PV_entete    le chaine à mettre avant si le fichier n'existe pas
   */
  static private function ecritLog ($PV_str, $PV_fichier, $PV_entete='') {
    $dir = dirname($PV_fichier);
    if (!is_dir($dir)) @mkdir($dir,0777,true);
    if ($PV_entete != '' && !file_exists($PV_fichier)) $PV_str = $PV_entete.$PV_str;
    @error_log($PV_str,3,$PV_fichier);
  }

}

