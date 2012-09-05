<?php
/*
 *
 * @copyright C2B S.A. 2010
 */

/**
 * Représente un ensemble d'objets.
 *
 * Les objets de l'ensemble ne sont pas forcément du même type, tout dépend de l'usage qui est fait de la Collection.
 *
 * Les utilisations de la Collection sont :
 *
 * $collection->getXXXX();        où soit XXXX soit getXXX sont des méthodes présentes sur chaque objet
 * $collection->collectXXXX();    où soit XXXX soit getXXX sont des méthodes présentes sur chaque objet. Le mot clef collect permet de modifier le retour (@see __call)
 * $collection->setXXXX($val);    où setXXXX est une méthode présente sur chaque objet. @see __call
 * $collection['XXX'];            récupère la liste des valeurs de getXXX sur les objets de la collection (XXX est mis en CamelCase)
 * $collection['XXX'] = $val;     appelle setXXX($val) sur chaque objets de la collection (XXX est mis en CamelCase)
 * foreach($collection as $obj);  parcours la collection en récupérant l'objet la comprenant
 * $collection->testeET('XXX')    où XXX est une méthode présente sur chaque objet. Appelle la méthode de chaque objet en ET ensemble
 * $collection->testeOU('XXX')    où XXX est une méthode présente sur chaque objet. Appelle la méthode de chaque objet en OU ensemble
 *
 *
 * @method  array         getXXX()              Retourne la liste des valeurs retournées par getXXX() sur chaque objet
 * @method  Collection    collectXXX()          Retourne la Collection des valeurs retournées par getXXX() sur chaque objet
 * @method  Collection    setXXX()              Appelle setXXX() sur chaque objet de la Collection
 *
 * @author KEvin
 */
class Collection implements Iterator, ArrayAccess, Countable {

  const locale_defaut = 'fr_FR.utf8';
  protected static $locale_tri = array();

  protected $_ensemble = array();

    /**
   * Construit la Collection
   *
   * @param mixed $PV_ens l'ensemble de départ :
   *                          array un tableau PHP contenant des objets
   *                          Collection crée une copie (clone) de chaque objet de la collection
   *                          Traversable son contenu sera mis dans la collection (sans copie)
   */
  public function __construct ($PV_ens=null) {
    if (is_array($PV_ens)) {
      $this->_ensemble = $PV_ens;
    }
    elseif (is_object($PV_ens)) {
      if (is_a($PV_ens,__CLASS__)) {
        foreach ($PV_ens as $o) $this->_ensemble[] = clone $o;
      }
      elseif (is_a($PV_ens,'Traversable')) {
        foreach ($PV_ens as $o) $this->_ensemble[] = $o;
      }
    }
  }

  public function offsetExists ($offset) {
    if (count($this->_ensemble) == 0) return false;
    return $this->aMethode($this->_ensemble[0],$offset);
  }

  public function offsetGet ($offset) {
    return $this->itere('get'.sfInflector::camelize($offset));
  }

  public function offsetSet ($offset, $value) {
    $this->itere('set'.sfInflector::camelize($offset),array($value));
  }

  public function offsetUnset ($offset) {
    /* RAF */
  }

  public function current () {
    return current($this->_ensemble);
  }

  public function key () {
    return key($this->_ensemble);
  }

  public function next () {
    return next($this->_ensemble);
  }

  public function rewind () {
    return reset($this->_ensemble);
  }

  public function valid () {
    return current($this->_ensemble) !== false;
  }

  public function count () {
    return count($this->_ensemble);
  }

  public function premier () {
    return count($this->_ensemble) == 0 ? null : reset($this->_ensemble);
  }

  /**
   * Supprime un élément de la collection par son index
   *
   * L'index est récupérable en itérant dans la collection.
   *
   * @param   mixed       $PV_index   l'index à supprimer
   * @return  Collection              l'instance Collection résultante
   */
  public function retireIndex ($PV_index) {
    unset($this->_ensemble[$PV_index]);
    return $this;
  }

  /**
   * Ajoute un élément à la collection
   *
   * Attention : si l'élément est un array, une Collection ou un objet Traversable tous ses éléments seront mis individuellement (merge récursif). @see insere()
   *
   * @param mixed $PV_o
   * @return  Collection              l'instance Collection résultante
   */
  public function add ($PV_o) {
    if (is_array($PV_o) || is_a($PV_o,__CLASS__) || is_a($PV_o, 'Traversable')) {
      foreach ($PV_o as $o) $this->add($o);
    }
    else $this->insere($PV_o);

    return $this;
  }

  /**
   * Ajoute le contenu d'une autre Collection
   *
   * Chaque élément de la nouvelle collection est ajouté à la fin de l'actuelle.
   *
   * @param   Collection    $PV_collection
   * @return  Collection                      la collection modifiée
   */
  public function fusionne (Collection $PV_collection) {
    foreach ($PV_collection as $e) $this->insere($e);
    return $this;
  }

  /**
   * Ajoute un élément à la collection
   *
   * Attention : ne fait pas de récursivité @see add()
   *
   * @param mixed $PV_o
   * @return  Collection              l'instance Collection résultante
   */
  public function insere ($PV_o) {
    $this->_ensemble[] = $PV_o;
    return $this;
  }

  /**
   * Retourne un array simple avec les éléments de la Collection
   *
   * @return array
   */
  public function toArray () {
    return $this->_ensemble;
  }

  protected function preCall ($PV_func, $PV_param=array()) {
    // méthode vide à surcharger pour faire un traitement avant une série d'appel à $PV_func sur les objets de l'ensemble
  }

  /**
   * Permet d'appeler certaines méthodes présentent sur TOUS les objets de la Collection.
   *
   * Les modes d'appel possibles sont :
   *
   *  - getXXX => la méthode getXXX doit exister sur chaque objet. XXX doit être en CamelCase. Retourne un tableau PHP contenant les valeurs de retour de getXXX pour chaque objet
   *  - collectXXX => la méthode getXXX doit exister sur chaque objet. XXX doit être en CamelCase. Retourne un Collection contenant les valeurs de retour de getXXX pour chaque objet
   *  - setXXX => la méthode setXXX doit exister sur chaque objet. XXX doit être en CamelCase. Retourne l'objet courant pour le chainage.
   *
   * @param   string    $name
   * @param   array     $arguments
   * @return  mixed                   Voir la description ci-dessus.
   */
  public function __call ($name, $arguments) {

    switch (substr($name,0,3)) {
      case 'get' :
        $t = substr($name,3,1);
        $name = $this->trouveMethode($name,substr($name,3));
        if ($t != '' && strtoupper($t) == $t) return $this->itere($name,$arguments);
        break;
      case 'col' :
        $t = substr($name,7,1);
        if (substr($name,0,7) == 'collect' && $t != '' && strtoupper($t) == $t) {
          $name = $this->trouveMethode($name,substr($name,7));
          return $this->itere($name, $arguments, true);
        }
        break;
      case 'set' :
        $t = substr($name,3,1);
        if (strtoupper($t) == $t) $this->itere($name,$arguments);
        break;
      case '_tr' : // méthode de tri
        if (strlen($name) >= 11 && substr($name,0,10) == '_trieAlpha') {
          $asc = substr($name,10,1) == 'A';
          $f = substr($name,11);
          // on suppose que le local a bien été set préalablement
          if ($f == '') { return strcoll($arguments[$asc ? 0 : 1],$arguments[$asc ? 1 : 0]); }
          else {
            return strcoll(
              call_user_func(array($arguments[$asc ? 0 : 1],$f)),
              call_user_func(array($arguments[$asc ? 1 : 0],$f))
              );
          }
        }
        elseif (strlen($name) >= 13 && substr($name,0,12) == '_trieNoCasse') {
          $asc = substr($name,12,1) == 'A';
          $f = substr($name,13);
          // on suppose que le local a bien été set préalablement
          // il n'y a pas de méthode de trie non sensible à la casse en natif, on triche en mettant en minuscules
          if ($f == '') { return strcoll(mb_strtolower($arguments[$asc ? 0 : 1]),mb_strtolower($arguments[$asc ? 1 : 0])); }
          else {
            return strcoll(
              mb_strtolower(call_user_func(array($arguments[$asc ? 0 : 1],$f))),
              mb_strtolower(call_user_func(array($arguments[$asc ? 1 : 0],$f)))
              );
          }
        }
        elseif (strlen($name) >= 9 && substr($name,0,8) == '_trieNum') {
          $asc = substr($name,8,1) == 'A';
          $f = substr($name,9);
          if ($f == '') { return $arguments[$asc ? 0 : 1] - $arguments[$asc ? 1 : 0]; }
          else {
            return call_user_func(array($arguments[$asc ? 0 : 1],$f)) - call_user_func(array($arguments[$asc ? 1 : 0],$f));
          }
        }
        break;
    }

    return null;
  }

  /**
   * Détermine quelle nom de méthode utiliser dans le cadre des getXXX collectXXX magiques.
   *
   * dans un appel getXXX ou collectXXX, si XXX est une méthode existante à part entière elle est utilisée, sinon on essayera le getXXX de l'objet.
   *
   * @param   string    $PV_noml    le nom long de la méthode appelée (avec get et collect)
   * @param   string    $PV_nomc    le nom court de la méthode appelée (celle présente dans l'objet normalement
   * @return  string                le nom de la méthode des objets de la collection
   */
  protected function trouveMethode ($PV_noml, $PV_nomc) {
    if (count($this->_ensemble) == 0) return $PV_nomc;
    return method_exists($this->_ensemble, $PV_nomc) ? $PV_nomc : 'get'.$PV_nomc;
  }

  /**
   * Itère la méthode passée censée retourner un booléen, s'arrête au premier true
   *
   * Si la collection est vide retourne false.
   *
   * @param   string  $PV_methode   un nom de méthode présente sur tous les objets de la collection
   * @param   array   $PV_param     optionnels, les paramètres à passer à la méthode
   * @return  bool                  true si au moins un objet a retourné true ou false si tous ont retourné false
   */
  public function testeOU ($PV_methode, $PV_param=array()) {
    if (!is_array($PV_param) && $PV_param !== null) $PV_param = array($PV_param);
    foreach ($this->_ensemble as $obj) if (call_user_func_array(array($obj,$PV_methode), $PV_param)) return true;
    return false;
  }

  /**
   * Itère la méthode passée censée retourner un booléen, s'arrête au premier false
   *
   * Si la collection est vide retourne false.
   *
   * @param   string  $PV_methode   un nom de méthode présente sur tous les objets de la collection
   * @param   array   $PV_param     optionnels, les paramètres à passer à la méthode
   * @return  bool                  true si tous les objets ont retournée true ou false au moins un à retourné false
   */
  public function testeET ($PV_methode, $PV_param=array()) {
    if (!is_array($PV_param) && $PV_param !== null) $PV_param = array($PV_param);
    foreach ($this->_ensemble as $obj) if (!call_user_func_array(array($obj,$PV_methode), $PV_param)) return false;
    return count($this->_ensemble) == 0 ? false : true;
  }

  /**
   * Appelle une fonction sur chaque objet de la collection et remplace l'objet par le retour de la fonction
   *
   * @param   callable        $PV_fnc     le nom de la fonction ou le couple (objet,nom de méthode).
   *                                      Le premier paramètre de la fonction doit être l'objet, les autres paramètres sont ceux passés à map()
   * @param   array           $PV_param   optionnels, les paramètres à passer à la méthode
   * @return  Collection                  une référence vers l'objet collection.
   */
  public function map ($PV_fnc, $PV_param=array()) {
    foreach ($this->_ensemble as $k => $obj) $this->_ensemble[$k] = call_user_func_array($PV_fnc,array_merge(array($obj),$PV_param));
    return $this;
  }

  /**
   * Itère la méthode passée sur chaque objet de la collection
   *
   * @param   callable        $PV_methode un nom de méthode présente sur tous les objets de la collection
   * @param   array           $PV_param   optionnels, les paramètres à passer à la méthode
   * @return  Collection                  une référence vers l'objet collection.
   */
  public function mapObjets ($PV_methode, $PV_param=array()) {
    foreach ($this->_ensemble as $k => $obj) call_user_func_array(array($obj,$PV_methode),$PV_param);
    return $this;
  }

  /**
   * Retourne une Collection contenant uniquement les éléments :
   *  - soit pour lesquels la fonction passée retournent true (quand $PV_valeur == null)
   *  - soit pour lesquels la fonction passée retourne $PV_valeur (quand $PV_valeur != null)
   *
   * La comparaison n'est pas stricte (opérateur ==)
   *
   * @param   callable        $PV_func      le nom de la fonction retournant une valeur scalaire (pas un tableau) et qui prend en premier paramètre l'élement de la collection
   * @param   array           $PV_param     optionnels, les paramètres à passer à la méthode (après l'élément de la collection)
   * @param   mixed           $PV_valeur    optionnel, la valeur à comparer par rapport au retour de la méthode $PV_func ou un ensemble de valeurs parmi lesquelles le retour de la méthode $PV_func doit figurer
   * @return  Collection
   */
  public function filtre ($PV_func, $PV_param=array(), $PV_valeur=null) {
    $classe = get_class($this);
    $ret = new $classe;
    if (is_null($PV_valeur)) $PV_valeur = array(true);
    elseif (!is_array($PV_valeur)) $PV_valeur = array($PV_valeur);

    foreach ($this->_ensemble as $obj) {
      if (in_array(call_user_func_array($PV_func, array_merge(array($obj),$PV_param)),$PV_valeur)) $ret->insere($obj);
    }
    return $ret;
  }

  public function debug ($PV_mess) {
    echo __METHOD__.' objet "'.$PV_mess.'" :<br />';
    foreach ($this->_ensemble as $o) {
      echo '- '.get_class($o).'<br />';
    }
  }

  /**
   * Retourne une Collection contenant UNIQUEMENT les objets filtrés par rapport à la méthode passée en paramètre.
   *
   * Les éléments gardés sont soit :
   *  - ceux qui retournent true à la méthode passée (quand $PV_valeur == null)
   *  - ceux dont la valeur retournée par la méthode passée correspond à $PV_valeur (quand $PV_valeur != null)
   *
   * La comparaison n'est pas stricte (opérateur ==)
   *
   * @param   string          $PV_func      le nom de la méthode des objets retournant une valeur scalaire (pas un tableau)
   * @param   array           $PV_param     optionnels, les paramètres à passer à la méthode
   * @param   mixed           $PV_valeur    optionnel, valeur à comparer par rapport au retour de la méthode $PV_func ou un ensemble de valeurs parmi lesquelles le retour de la méthode $PV_func doit figurer
   * @param   boolean         $PV_exclure   Permet de definir si les valeurs seront à inclure (==) ou non (!=)
   * @return  Collection
   */
  public function filtreObjets ($PV_func, $PV_param=array(), $PV_valeur=null, $PV_exclure=false) {
    $classe = get_class($this);
    $ret = new $classe;
    if (is_null($PV_valeur)) $PV_valeur = array(true);
    elseif (!is_array($PV_valeur)) $PV_valeur = array($PV_valeur);

    $this->preCall($PV_func,$PV_param);
    foreach ($this->_ensemble as $obj) {
      if ($PV_exclure) {
        if (!in_array(call_user_func_array(array($obj,$PV_func), $PV_param),$PV_valeur)) $ret->insere($obj);
      } else {
        if (in_array(call_user_func_array(array($obj,$PV_func), $PV_param),$PV_valeur)) $ret->insere($obj);
      }
    }
    return $ret;
  }

  /**
   * Retourne une Collection contenant UNIQUEMENT les objets filtrés par rapport à TOUTES les méthodes passées en paramètre.
   *
   * Les éléments gardés sont soit :
   *  - ceux qui retournent true aux méthodes passées (quand $PV_valeur == array())
   *  - ceux dont la valeur retournée par les méthodes passées correspondent à la valeur (quand $PV_valeur != array())
   *
   * La comparaison n'est pas stricte (opérateur ==)
   *
   * @param array             $PV_func        la liste des noms de méthodes présentes sur chaque objet retournant une valeur scalaire (pas un tableau)
   * @param array             $PV_param       optionnels, liste de paramètres à passer à passer à chaque méthode.
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @param array             $PV_valeur      optionnel, liste de valeurs dont chaque est à comparer par rapport au retour de la méthode $PV_func dans l'index équivalent OU liste d'ensemble de valeurs parmi lesquelles le retour de la méthode $PV_func dans l'index équivalent doit figurer
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @return Collection
   */
  public function filtreObjetsMultET ($PV_func, $PV_param=array(), $PV_valeur=array()) {
    return $this->filtreObjetsMult($PV_func, $PV_param, $PV_valeur);
  }

  /**
   * Retourne une Collection contenant UNIQUEMENT les objets filtrés par rapport à au moins UNE des méthodes passées en paramètre.
   *
   * Les éléments gardés sont soit :
   *  - ceux qui retournent true aux méthodes passées (quand $PV_valeur == array())
   *  - ceux dont la valeur retournée par les méthodes passées correspondent à la valeur (quand $PV_valeur != array())
   *
   * La comparaison n'est pas stricte (opérateur ==)
   *
   * @param array             $PV_func        la liste des noms de méthodes présentes sur chaque objet retournant une valeur scalaire (pas un tableau)
   * @param array             $PV_param       optionnels, liste de paramètres à passer à passer à chaque méthode.
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @param array             $PV_valeur      optionnel, liste de valeurs dont chaque est à comparer par rapport au retour de la méthode $PV_func dans l'index équivalent OU liste d'ensemble de valeurs parmi lesquelles le retour de la méthode $PV_func dans l'index équivalent doit figurer
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @return Collection
   */
  public function filtreObjetsMultOU ($PV_func, $PV_param=array(), $PV_valeur=array()) {
    return $this->filtreObjetsMult($PV_func, $PV_param, $PV_valeur, false);
  }

  /**
   * Retourne une Collection contenant UNIQUEMENT les objets filtrés par rapport aux méthodes passées en paramètre.
   *
   * Les éléments gardés sont soit :
   *  - ceux qui retournent true aux méthodes passées (quand $PV_valeur == array())
   *  - ceux dont la valeur retournée par les méthodes passées correspondent à la valeur (quand $PV_valeur != array())
   *
   * La comparaison n'est pas stricte (opérateur ==)
   *
   * @param array             $PV_func        la liste des noms de méthodes présentes sur chaque objet retournant une valeur scalaire (pas un tableau)
   * @param array             $PV_param       optionnels, liste de paramètres à passer à passer à chaque méthode.
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @param array             $PV_valeur      optionnel, liste de valeurs dont chaque est à comparer par rapport au retour de la méthode $PV_func dans l'index équivalent OU liste d'ensemble de valeurs parmi lesquelles le retour de la méthode $PV_func dans l'index équivalent doit figurer
   *                                          ATTENTION : L'ordre doit être le même que dans $PV_func
   * @param bool              $PV_exclusif    true pour des tests eclusifs : toutes les fonctions passées doivent retourner true ou correspondre à la valeur indiquée pour garder l'objet dans la collection
   *                                          false pour des tests non exclusifs : si une seule des fonctions passées retourne true ou correspond au niveau de la valeur attendue, on garde l'objet dans la collection
   * @return Collection
   */
  protected function filtreObjetsMult ($PV_func, $PV_param=array(), $PV_valeur=array(), $PV_exclusif=true) {
    $classe = get_class($this);
    $ret = new $classe;
    if (is_array($PV_func)) {
      foreach ($PV_func as $k => $func) {
        $params = isset($PV_param[$k]) ? $PV_param[$k] : array();
        $this->preCall($func,$params);
      }
    }
    else { $this->preCall($PV_func,$PV_param); }

    foreach ($this->_ensemble as $obj) {
      $estOK = false;
      foreach ($PV_func as $k => $func) {
        $PV_valeur[$k] = !array_key_exists($k, $PV_valeur) ? array(true) : (!is_array($PV_valeur[$k]) ? array($PV_valeur[$k]) : $PV_valeur[$k]);
        $params = array_key_exists($k, $PV_param) ? $PV_param[$k] : array();
        if (!in_array(call_user_func_array(array($obj,$func), $params),$PV_valeur[$k])) {
          if ($PV_exclusif) continue 2;
        }
        else $estOK = true;
      }
      if ($PV_exclusif || (!$PV_exclusif && $estOK)) $ret->insere($obj);
    }
    return $ret;
  }

  /**
   * Range les OBJETS de la collection par ordre alphabetique (sensible à la casse).
   *
   * @param   string        $PV_func    le nom de la méthode qui retourne la chaine sur laquelle trier
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieObjetsAlphaCasse ($PV_func, $PV_asc=true) {
    $this->preCall($PV_func);
    self::swapLocale(true);
    // __call va rattraper le nom de la fonction de tri
    // on masque les erreurs suite à un bug PHP qui met un warning pensant que le tableau a été modifié
    // par la fonction de sort. Arrive avec des objets doctrine quand $PV_func est une relation I18n
    @usort($this->_ensemble,array($this,'_trieAlpha'.($PV_asc ? 'A' : 'D').$PV_func));
    self::swapLocale(false);
    return $this;
  }

  /**
   * Range les OBJETS de la collection par ordre alphabetique (non sensible à la casse).
   *
   * @param   string        $PV_func    le nom de la méthode qui retourne la chaine sur laquelle trier
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieObjetsAlpha ($PV_func, $PV_asc=true) {
    $this->preCall($PV_func);
    self::swapLocale(true);
    // __call va rattraper le nom de la fonction de tri
    // on masque les erreurs suite à un bug PHP qui met un warning pensant que le tableau a été modifié
    // par la fonction de sort. Arrive avec des objets doctrine quand $PV_func est une relation I18n
    @usort($this->_ensemble,array($this,'_trieNoCasse'.($PV_asc ? 'A' : 'D').$PV_func));
    self::swapLocale(false);
    return $this;
  }

  /**
   * Range les OBJETS de la collection par ordre numérique pur.
   *
   * @param   string        $PV_func    le nom de la méthode qui retourne la chaine sur laquelle trier
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieObjetsNum ($PV_func, $PV_asc=true) {
    $this->preCall($PV_func);
    // __call va rattraper le nom de la fonction de tri
    // on masque les erreurs suite à un bug PHP qui met un warning pensant que le tableau a été modifié
    // par la fonction de sort. Arrive avec des objets doctrine quand $PV_func est une relation I18n
    @usort($this->_ensemble,array($this,'_trieNum'.($PV_asc ? 'A' : 'D').$PV_func));
    return $this;
  }

  /**
   * Range les SCALAIRES de la collection par ordre alphabetique (sensible à la casse).
   *
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieAlphaCasse ($PV_asc=true) {
    self::swapLocale(true);
    // __call va rattraper le nom de la fonction de tri
    usort($this->_ensemble,array($this,'_trieAlpha'.($PV_asc ? 'A' : 'D')));
    self::swapLocale(false);
    return $this;
  }

  /**
   * Range les SCALAIRES de la collection par ordre alphabetique (non sensible à la casse).
   *
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieAlpha ($PV_asc=true) {
    self::swapLocale(true);
    // __call va rattraper le nom de la fonction de tri
    usort($this->_ensemble,array($this,'_trieNoCasse'.($PV_asc ? 'A' : 'D')));
    self::swapLocale(false);
    return $this;
  }

  /**
   * Range les SCALAIRES de la collection par ordre numérique pur.
   *
   * @param   bool          $PV_asc     true pour trier par ordre ascendant (défaut)
   *                                    false pour l'ordre descendant
   * @return  Collection                Retourne la même collection avec ses éléments triés
   */
  public function trieNum ($PV_asc=true) {
    // __call va rattraper le nom de la fonction de tri
    usort($this->_ensemble,array($this,'_trieNum'.($PV_asc ? 'A' : 'D')));
    return $this;
  }

  /**
   * Explose chaque élément de la Collection pour ajouter ses sous-éléments à la Collection.
   *
   * Ne marche que sur les éléments Traversable ou array(), les autres sont inchangés.
   *
   * @param   bool        $PV_deep    true pour aplatir aussi les sous éléments des sous éléments
   *                                  false (défaut) pour n'aplatir que les sous éléments de premier niveau
   * @return Collection   la Collection modifiée
   */
  public function aplatit ($PV_deep=false) {

    $this->_ensemble = $this->doAplatit($this->_ensemble, true, $PV_deep);
    return $this;
  }

  protected function doAplatit ($PV_val, $PV_deep, $PV_deepdeep) {
    $ret = array();
    foreach ($PV_val as $v) {
      if (($PV_deep || $PV_deepdeep) && (is_array($v) || is_a($v,'Traversable'))) {
        $ret = array_merge($ret,$this->doAplatit($v,false,$PV_deepdeep));
      }
      else $ret[] = $v;
    }
    return $ret;
  }

  protected function aMethode ($PV_obj, $PV_methode) {
    return is_callable(array($PV_obj, $PV_methode));
  }

  /**
   * Parcours les éléments de la Collection en appelant des méthodes dessus
   *
   * @param   string    $PV_func        le nom de la fonction des objets à appeler pour déterminer la valeur de retour
   *                                    peut être vide si $PV_collection = false pour retourner l'objet complet
   * @param   array     $PV_param       le tableau des paramètres à passer à $PV_func
   * @param   bool      $PV_collection  true pour avoir une collection en sortie
   *                                    false pour avoir une liste simple (défaut)
   * @param   string    $PV_clef        le nom de la fonction permettant de déterminer l'index de la liste de retour
   *                                    utile uniquement si $PV_collection = false
   * @return  mixed     soit un tableau soit une collection avec les valeurs de retour de $PV_func à la place des objets initiaux
   */
  public function itere ($PV_func, $PV_param=array(), $PV_collection=false, $PV_clef=null) {
    if (!is_array($PV_param) && $PV_param !== null) $PV_param = array($PV_param);
    $this->preCall($PV_func,$PV_param);

    if ($PV_collection) {
      $classe = get_class($this);
      $res = new $classe();
      foreach ($this->_ensemble as $obj) $res->insere(call_user_func_array(array($obj,$PV_func), $PV_param));
    }
    else {
      $res = array();
      $i = 0;
      $clef_inc = is_null($PV_clef);
      foreach ($this->_ensemble as $obj) {
        $clef = $clef_inc ? $i++ : call_user_func(array($obj,$PV_clef));
        $res[$clef] = $PV_func == '' ? $obj : call_user_func_array(array($obj,$PV_func), $PV_param);
      }
    }
    return $res;
  }

  /**
   * Méthode interne qui sert à s'assurer que les tris auront une locale cohérente pour la majorité des chaines
   *
   * L'échange (swap) se passe en deux temps :
   *  1) au début on note la locale courante et on la change pour celle par défaut
   *  2) à la fin on remet la locale précédente
   *
   * @param   bool  $PV_debut true pour indiquer qu'on débute l'échange et false pour indiquer qu'on termine l'échange
   */
  protected static function swapLocale ($PV_debut) {
    static $localeprec = null;

    if ($PV_debut) {
      $localeprec = setlocale(LC_COLLATE,null);
      // on ajoute la locale par défaut pour s'assurer qu'on en aura au moins une
      call_user_func_array('setlocale',array_merge(array(LC_COLLATE),self::$locale_tri,array(self::locale_defaut)));
    }
    else {
      setlocale(LC_COLLATE,$localeprec);
    }
  }

  /**
   *
   * @param   mixed   $PV_locale  la locale par défaut ou une liste de locale par défaut. Si vide on remet uniquement la locale par défaut
   */
  public static function setLocaleTri ($PV_locale) {
    if (empty($PV_locale)) $PV_locale = array(self::locale_defaut);

    self::$locale_tri = is_array($PV_locale) ? $PV_locale : array($PV_locale);
  }

  public function donneListe ($PV_key, $PV_value) {
    return $this->itere($PV_value, array(), false, $PV_key);
  }

  /**
   * Retourne une nouvelle collection sans les éléments en doublons.
   *
   * Ne modifie pas la collection existante (contrairement à @see unique()).
   *
   * @param   string      $PV_methode   nom de la fonction retournant la chaine permettant de comparer deux éléments
   * @param   array       $PV_params    les paramètres à passer à PV_fonc sous forme d'un tableau
   * @return  Collection                la nouvelle collection
   */
  public function dedoublonneObjet ($PV_methode, $PV_params=array()) {
    $classe = get_class($this);
    $new = new $classe();
    $listeDedoublonnee = array();
    $this->preCall($PV_methode,$PV_params);

    foreach ($this->_ensemble as $obj) {
      $val = call_user_func_array(array($obj, $PV_methode),$PV_params);
      if (array_key_exists($val, $listeDedoublonnee)) { continue; }
      $new->insere($obj);
      $listeDedoublonnee[$val] = true;
    }
    return $new;
  }

  /**
   * Filtre la collection courante pour retirer les éléments en doublons
   *
   * Modifie la collection courante (contrairement à @see dedoublonneObjet()).
   *
   * @param   string    $PV_fonc    si les éléments sont des objets, nom de la fonction retournant la chaine permettant de comparer deux éléments
   *                                si vide compare les éléments tels quels
   * @param   array     $PV_params  les paramètres à passer à PV_fonc sous forme d'un tableau
   * @return  Collection            un pointeur sur l'instance courante de la collection qui a été modifiée
   */
  public function unique ($PV_fonc, $PV_params=array()) {
    if ($PV_fonc != '') {
      $this->preCall ($PV_fonc,$PV_params);
      $temp = array();
      foreach ($this->_ensemble as $obj) {
        $clef = call_user_func_array(array($obj,$PV_fonc),$PV_params);
        if (!isset($temp[$clef])) $temp[$clef] = $obj;
      }
      $this->_ensemble = array_values($temp);
    }
    else {
      $this->_ensemble = array_unique($this->_ensemble);
    }

    return $this;
  }

}

