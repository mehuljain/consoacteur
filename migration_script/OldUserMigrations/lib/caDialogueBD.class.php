<?php
/**
 * Librairie d'accès à la base de données compatible UTF-8.
 *
 * UTILISATION RESTREINTE : il faut favoriser Doctrine
 *
 * REMARQUE : "compatible UTF-8" signifie que les données qui transitent entre PHP et le serveur de base ne sont pas modifiées.
 *
 * Pour que la librairie fonctionne il faut copier les fichiers de conf mysql : doc/conf/bdd/local/* vers config/neta/na/bdd/
 *
 *
 *
 *  @copyright C2B S.A. 2010
 */

/**
 *
 * @method  NetaCollection  tousObjets() tousObjets($PV_collect=true) Retourne toutes les lignes du résultat courant, une ligne étant en forme d'objet. Utilise NetaCollection.
 *
 * @author kevin
 */

require_once 'ADialogueBD.class.php';

class caDialogueBD extends ADialogueBD {

  public function __construct () {

    self::initRepConf(dirname(__FILE__).'/'.self::$_base_rep_conf);
    self::initRepLog(dirname(__FILE__).'/'.'log/');
    parent::__construct();

    self::setNombreEssaisConn(5);
    $this->_default_db      = 'netav3';
  }

  protected function doChoixGrappe ($PV_table) {
    return 'std';
  }

}

