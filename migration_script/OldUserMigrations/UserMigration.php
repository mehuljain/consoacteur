<?php

/**
 *  This file migrates the user data from the old conso-acteur database to the new one.
 * 
 */
require_once dirname(__FILE__) . '/lib/caDialogueBD.class.php';

/**
 *  Standard canonicalize function used by FOS bundle.
 *
 * @param type $string
 * @return type 
 */
function canonicalize($string)
{
        if($string == NULL) return NULL;
        return mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
}

/**
 * 
 * @param string $val
 * @return int 
 */
function get_salutation($val)
{
  if($val == 'm')
    return 1;
  else if ($val == 'f')
  {
    return 2;
  }
  else
    return 3;
}

/**
 * The migration code starts here. 
 */
$bd = new caDialogueBD();
//The temp table to store the records
$tempTable = 'users_temp';
// The database name under which temporary tables will be stored.
$dbname = 'consoacteur_fr';
//old database name 
$olddbname = 'consoact_old';
// The database to transfer the data 
$create = $bd->requete('CREATE TABLE '.$dbname.'.'.$tempTable . ' LIKE '. $dbname.'.users', $dbname.'.'.$tempTable);

if(!$create)
{
  
  echo "The temp table was not created sucessfully"."\n";
  $bd->ferme();
  EXIT;
}
$start = 0;
$end = 1000;
$inserts = array();

//if deleted accounts are required to be copied they need to be handled seperately, as number of constraints fail.
while($tableCount = $bd->select($olddbname.'.comptes','*','supprime_le IS NULL',$start.','.$end,'','',false,true) > 0)
{
  $rowsUsersTemp = $bd->toutesLignes();
  foreach ($rowsUsersTemp as $userRow){
   
    if($userRow['email'] == '' || $userRow['id'] == 26658 || $userRow['id'] == 20687) {
      continue;
    }
    
    if($userRow['id'] == '118245'){
      $userRow['login'] = 'mar2';
    }
    else if($userRow['id'] == '234002'){
      $userRow['login'] = 'bjorn1';
    }
    else if($userRow['id'] == '347464'){
      $userRow['login'] = 'flow1';
    }
    else if($userRow['id'] == '177227'){
      $userRow['login'] = 'joker2';
    }
    else if($userRow['id'] == '409362'){
      $userRow['login'] = 'laetus1';
    }
    else if($userRow['id'] == '179965'){
      $userRow['login'] = 'lora1';
    }
    else if($userRow['id'] == '441162'){
      $userRow['login'] = 'maud1';
    }
    
    $temp['id'] = $userRow['id'];
    $temp['username'] = utf8_encode($userRow['login']);
    $temp['username_canonical'] = canonicalize(utf8_encode($userRow['login']));
    $temp['email'] = utf8_encode($userRow['email']);
    $temp['email_canonical'] = canonicalize(utf8_encode($userRow['email']));
    $temp['enabled'] = 1;
    $temp['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    $temp['password'] = $userRow['mdp'];   
    $temp['last_login'] = NULL;
    $temp['locked'] = 0;
    $temp['expired'] = 0;
    $temp['expires_at'] = NULL;
    $temp['confirmation_token'] = NULL;
    $temp['password_requested_at'] = NULL;
    $temp['roles'] = 'a:0:{}';
    $temp['credentials_expired'] = 0;
    $temp['credentials_expire_at'] = NULL;
    $temp['created_at'] = $userRow['cree_le'];
    $temp['updated_at'] = $userRow['modification'];
    $temp['two_step_code'] = NULL;
    $temp['salutation'] = get_salutation($userRow['condition']);
    $temp['first_name'] = utf8_encode($userRow['prenom']);
    $temp['last_name'] = utf8_encode($userRow['nom']);
    $temp['dob'] = $userRow['date_de_naissance'];
    $temp['profession'] = $userRow['profession'];
    $temp['apartment_number'] = utf8_encode($userRow['ad1_numero']);
    $temp['address_location'] =  $userRow['ad1_voie'];
    $temp['location_name'] = utf8_encode($userRow['ad1_rue']);
    $temp['complementary_address_details'] = $userRow['ad1_complementaire'];
    $temp['zipcode'] = $userRow['ad1_code_postal'];
    $temp['city'] = $userRow['ad1_ville'];
    $temp['country'] = $userRow['ad1_pays'] == '65' ? 'FR' : NULL;
    $temp['phone_home'] = utf8_encode($userRow['tel_dom']);
    $temp['phone_mobile'] = utf8_encode($userRow['tel_port']);
    $temp['phone_office'] = utf8_encode($userRow['tel_bur']);
    $temp['blacklist_reason'] = NULL;
    $temp['referral_type'] = NULL;
    $temp['newsletter_subscription'] = 0;
    $temp['share_contact'] = NULL;
    $temp['account_closure_reason'] = NULL;
    $temp['account_closure_comment'] = NULL;
    $temp['is_admin_user'] = 0;
    $temp['archived_at'] = NULL;
    $temp['is_archived'] = 0;
    $temp['withdrawal_code'] = NULL;
    $temp['withdrawal_code_expires_at'] = NULL;
    $temp['is_blacklisted'] = 0;
    $temp['blacklisted_at'] = NULL;
    $temp['sponsorship_code'] = $userRow['parrain'] === NULL ? NULL :  $userRow['parrain'];
    $temp['advertisement_by_email'] = NULL;
    $temp['advertisement_by_post'] = NULL;
    $temp['advertisement_by_telephone'] = NULL;
    $temp['advertisement_by_sms'] = NULL;
    $temp['is_olduser'] = 1;
    
    $inserts[] = $temp;
    if (count($inserts) > 200) {
      if ($bd->insert($dbname .'.'.$tempTable, $inserts) === FALSE) {
        echo "Could not insert in GREATDEALS database";
        $bd->ferme();
        exit;
      }
      unset($inserts);
      $inserts = array();
    }
    
    }
    
    if (count($inserts) > 0) {

      if ($bd->insert($dbname .'.'.$tempTable, $inserts) === FALSE) {
      echo "Error inserting records into the temporary database for source";
      }
      unset($inserts);
    }    
   
    $start += $end;  
}

$bd->ferme();
    