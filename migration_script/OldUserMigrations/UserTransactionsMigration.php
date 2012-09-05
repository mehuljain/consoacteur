<?php

/**
 *  This file migrates the user data from the old conso-acteur database to the new one.
 * 
 */
require_once dirname(__FILE__) . '/lib/caDialogueBD.class.php';

/**
 * The user money accounts migration code starts here. 
 */
//The temp table to store the records
$tempTable_trans = 'transactions_temp';
//The temp table to store the records
$tempTable_withdraw = 'withdrawal_temp';
// The database name under which temporary tables will be stored.
$dbname = 'consoacteur_fr';
// The old database name to transfer the data 
$olddbname = 'consoact_old';


//function getuserArray($id){
//     
//  $user = $bd->select($dbname. '.users_temp', array('username_cannonical'), 'id=' .$id);
//        if ($user === FALSE) {
//          echo 'SQL error selecting the user from the country database from the users table';
//        }
//        if ($user == 1) {
//          $username = $bd->valeur('username_cannonical');
//          return $username;
//        }
//        return NULL;
//}
  

function  get_status($transactionRow, &$temp){
  
  $today = date('Y-m-d', time());
  $lr = @unserialize($transactionRow['retraits']);
  
  $dbname = 'consoacteur_fr';
  $tempTable_withdraw = 'withdrawal_temp';
  
        //payment confirmed --just for transactions
        $temp['withdrawal_id'] = NULL;
        $temp['status'] = 3;

//        static $withdrawals = 1000;
  
  foreach ($lr as $k => $r) {
    
        if (is_numeric($r)) {
//          $withdrawals++;
          //for transaction
//          $temp['withdrawal_id'] = $withdrawals;          
          //for withdrawals
          $withdraw = array();
//          $withdraw['id'] = $withdrawals;
          $withdraw['user_id'] = $transactionRow['id'];
          $withdraw['bankName'] = NULL;
          $withdraw['address'] = NULL;
          $withdraw['country'] = NULL;
          $withdraw['accountNumber'] = NULL;
          $withdraw['iban'] = NULL;
          $withdraw['swiftCode'] = NULL;
          $withdraw['chequePayee'] = NULL;
          $withdraw['email'] = NULL;
          $withdraw['amount'] = $transactionRow['solde'];
          $withdraw['code'] = 123456;
          $withdraw['type'] = 3;
          $withdraw['status'] = 4;
          $withdraw['internal_comment'] = 'old request';
          $withdraw['user_comment'] = NULL;
          $withdraw['created_at'] = $today;
          $withdraw['updated_at'] = $today;
          $withdraw['validated_at'] = NULL;
          $withdraw['requested_at'] = NULL;
          $withdraw['processed_at'] = NULL;
          $withdraw['paid_at'] = NULL;
          $withdraw['username'] = NULL;
          $db = new caDialogueBD();
          if (($id = $db->insert($dbname . '.' .$tempTable_withdraw, $withdraw)) === FALSE) {
                echo "Could not insert in GREATDEALS database";
                break;
          }
          $temp['withdrawal_id'] = $id;
          $temp['status'] = 4;
          break;
        }
  }
          $db->ferme();

}

$bd = new caDialogueBD();
$create_transaction = $bd->requete('CREATE TABLE ' . $dbname . '.' . $tempTable_trans . ' LIKE '. $dbname . '.transactions', $dbname . '.' . $tempTable_trans);
$create_withdrawal = $bd->requete('CREATE TABLE ' . $dbname . '.' . $tempTable_withdraw . ' LIKE ' . $dbname . '.Withdrawal', $dbname . '.' . $tempTable_withdraw);


if (!$create_transaction || !$create_withdrawal) {
  $bd->ferme();
  echo "The transactions temp table was not created sucessfully" . "\n";
  echo "\n";
  exit;
}


$start = 0;
$end = 1000;
$inserts = array();
$today = date('Y-m-d', time());
while ($tableCount = $bd->select($olddbname. '.mouvements', '*', '', $start . ',' . $end, '', '', false, true) > 0) {
  $rowsTransactionsTemp = $bd->toutesLignes();
  foreach ($rowsTransactionsTemp as $transactionRow) {
    $count = $bd->select($dbname.'.users_temp', array('id','username_cannonical'));
    if($count != 1)
    {
      continue;
    }
    
    $temp['username'] = $bd->valeur('username_cannonical');
    
    $temp['user_id'] = $transactionRow['id'];
    $temp['offer_id'] = 1;
    $temp['merchant_id'] = 1;
    $temp['user_gain_amount'] = floatval($transactionRow['solde']);
    $temp['commission_amount'] = NULL;
    $temp['transaction_amount'] = NULL;
    
    //1 - Parrainage, 2 - Offer, 3- Joining
    $temp['type'] = 2;  
    $temp['validation_date'] = $today;
    $temp['transaction_date'] = NULL;
    $temp['confirmation_date'] = $today;
    $temp['merchant_confirmation_date'] = $today;
    $temp['merchant_cancellation_date'] = NULL;
    $temp['rejection_date'] = NULL;
    $temp['lost_date'] = NULL;
    $temp['order_number'] = NULL;
    $temp['reason'] = NULL;
    $temp['description'] = NULL;
    $temp['program_id'] = '01';//verify
    $temp['offer_type'] = NULL;
    $temp['is_action_required'] = 0;
    $temp['created_at'] = $today;
    $temp['updated_at'] = $today;
    
    $temp['version'] = 1;
    $temp['referral_email'] = NULL;
    $temp['offerUsage_id'] = NULL;
    //3 - confirmed (Donc, Montant retirable)
    get_status($transactionRow,$temp);  

    $inserts[] = $temp;
    if (count($inserts) > 200) {
      if ($bd->insert($dbname . '.' . $tempTable_trans, $inserts) === FALSE) {
        echo "Could not insert in GREATDEALS database";
      }
      unset($inserts);
      $inserts = array();
    }
  }
  if (count($inserts) > 0) {

    if ($bd->insert($dbname . '.' . $tempTable_trans, $inserts) === FALSE) {
      echo "Error inserting records into the temporary database for source ";
    }
    unset($inserts);
  }
  $start += $end; 
}

$bd->ferme();
