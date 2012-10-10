<?php

/**
 * This file migrates the user transactions from transactions_temp to transaction table due to the failure in the
 * first run.
 * 
 */
require_once dirname(__FILE__) . '/lib/caDialogueBD.class.php';

//The temp table which stores the transactions
$tempTable_transactions = 'transactions_temp';
// The database name.
$dbname = 'consoacteur_fr';

$bd = new caDialogueBD();

//insert all columns except for id..
if(!$bd->requete('INSERT INTO '.$dbname .'.transactions
    (user_id,offer_id,merchant_id,withdrawal_id,user_gain_amount,
    commission_amount, transaction_amount,type,status,validation_date, transaction_date, confirmation_date, merchant_confirmation_date,
    merchant_cancellation_date,rejection_date ,lost_date,order_number,reason,description,program_id,offer_type,
    is_action_required,created_at,updated_at,username,version,referral_email,offerUsage_id) 
    SELECT user_id,offer_id,merchant_id,withdrawal_id,user_gain_amount,
    commission_amount, transaction_amount,type,status,validation_date, transaction_date, confirmation_date, merchant_confirmation_date,
    merchant_cancellation_date,rejection_date ,lost_date,order_number,reason,description,program_id,offer_type,
    is_action_required,created_at,updated_at,username,version,referral_email,offerUsage_id
    FROM '.$dbname . '.' . $tempTable_transactions,$dbname . '.' . $tempTable_transactions))
{
  echo 'not success';
}
$bd->ferme();