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
$tempTable_users = 'users_temp';
$tempTable_transactions = 'transactions_temp';
$tempTable_withdrawal = 'withdrawal_temp';
// The database name under which temporary tables will be stored.
$dbname = 'consoacteur_fr';

$bd = new caDialogueBD();
if(!$bd->requete('INSERT INTO '.$dbname .'.users SELECT * FROM '.$dbname . '.' . $tempTable_users,$dbname . '.' . $tempTable_users))
{
  echo 'not success';
}
if(!$bd->requete('INSERT INTO '.$dbname .'.transactions SELECT * FROM '.$dbname . '.' . $tempTable_transactions,$dbname . '.' . $tempTable_transactions))
{
  echo 'not success';
}
if(!$bd->requete('INSERT INTO '.$dbname .'.Withdrawal SELECT * FROM '.$dbname . '.' . $tempTable_withdrawal,$dbname . '.' . $tempTable_withdrawal))
{
  echo 'not success';
}
$bd->ferme();