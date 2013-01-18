<?php

/**
 *  This class will be used to create the Search Index Script.
 * 
 */
class SearchIndexScript {

    //country database to make changes to the transaction table
    protected $countryDatabase;
    //locale for which the search index must be created
    protected $locale;
    //the timezone in which all the records will be converted to and processed
    static protected $localTimeZone  = 'Europe/Paris';
    //The log file for the source
    static protected $logFile;   
    //temporary database under which the temporary search index tables will be created
    static protected $tempDatabase  = 'search_import';
    //temporary table where the search records to be indexed will be inserted
    static protected $tempTable     = 'searchindex_temp';

    /**
     * The main function for processing the records. It executes the script and sends the result to the
     * specified support email addresses. The database parameter is the input from the cron script.
     *
     * @param string $database The database name of the country website(greatdeals_fr, greatdeals_uk...)   * 
     */
    public function process($database,$locale) {

        $this->setCountryDatabase($database);
        $this->setLocale($locale);                
        $this->createLogFile();
        $this->createTempTable();
        $this->createCommonSchema();
        $this->transactionExecute();
        $this->sendResult();
    }

    /**
     *  Sets the log file name for the source. The naming convention is similar to the retrieved files from
     *  external source.
     */
    protected function createLogFile() {
        self::$logFile = dirname(__FILE__) . '/' . 'log/' . 'searchindex' . '_' . date('YmdHis') . '.log';
    }

    /**
     *  Sets the database for a country. This parameter is obtained
     *  as an argument from the cron script run.
     *
     *  @param string $database The database for the country
     */
    protected function setCountryDatabase($database) {

        $this->countryDatabase = $database;
    }

    /**
     *  Gets the database for a country.
     *
     *  @return string $countryDatabase The database for the country
     */
    protected function getCountryDatabase() {

        return $this->countryDatabase;
    }
    
    /**
     *  Sets the locale. This parameter is obtained
     *  as an argument from the cron script run.
     *
     *  @param string $database The database for the country
     */
    protected function setLocale($locale) {

        $this->locale = $locale;
    }

    /**
     *  Gets the locale.
     *
     *  @return string $countryDatabase The database for the country
     */
    protected function getLocale() {

        return $this->locale;
    }

    /**
     * This function writes info log while running the script.
     *
     * @param string $PV_str : The message to log
     * @param string $PV_fichier : The log file along with the path
     */
    protected function writeInfoLog($PV_str, $PV_fichier) {
        $dir = dirname($PV_fichier);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $PV_str .= "\n";
        @error_log($PV_str, 3, $PV_fichier);
    }
    
    /**
     *  Returns the complete temporary table name along with the database.
     *
     *  @return string tableName The temporary table name for a specific source.
     */
    protected function getTempTableName() {

        return self::$tempDatabase . '.' . self::$tempTable;
    }
    
    
    /**
     *  Creates the temporary table for the script run. The fields for the
     *  temporary table are described below :
     *
     *  id : The unique id of the record
     *  merchant_id : The merchant_id for the offer,
     *  name : The name of the merchant,  
     *  merchantSlug : The merchant slug
     *  user_gain_value : The code promo for the offer,
     *  keywords : The keywords for the merchant
     *  process_status : The processing status in temp table. (0-invalid, 1 - read from file , 2 - matched with conso-acteur database , 3 - done)
     *  process_operation : The operation to be performed in the conso-acteur transactions table,(0 - unknown, 1 - insert , 2 - update, 3 - delete)
     *  errors : The validation errors for the record
     */
    protected function createTempTable() {

        $bd        = new caDialogueBD();       
        $tempTable = $this->getTempTableName();
     
        $checkTableExist = 'table_schema =' . $bd->protegeValeur(self::$tempDatabase) . ' AND table_name =' . $bd->protegeValeur(self::$tempTable);

        $tableCount = $bd->select('information_schema.tables', '*', $checkTableExist);

        if ($tableCount === FALSE) {
            $bd->ferme();
            $this->exitlog('Error selecting table information from information schema', self::$logFile);
        }
        if ($tableCount == 1) {

            if (!$bd->requete('DROP table ' . $tempTable, $tempTable)) {
                $bd->ferme();
                $this->exitlog('Could not drop the old temporary table ' .$tempTable, self::$logFile);
            }
        }

        $create = $bd->requete('CREATE TABLE ' . $tempTable .
                '(`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `merchant_id` int(11) DEFAULT NULL,
            `name` varchar(255) DEFAULT NULL,
            `user_gain_value` varchar(255) DEFAULT NULL,
            `merchantSlug` varchar(255) DEFAULT NULL,
            `keywords` text DEFAULT NULL,
            `process_status` tinyint(1)  DEFAULT NULL,
            `process_operation` tinyint(1) DEFAULT NULL,
            `errors` text,
             PRIMARY KEY (`id`)) ENGINE = MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1', $tempTable);

        if (!$create) {
            $bd->ferme();
            $this->exitlog('The temporary table was not created sucessfully for the search script' , self::$logFile);
        }
        $bd->ferme();
        $this->writeInfoLog('Successfully created the temporary table for the search script', self::$logFile);
    }
    
    

    /**
     * This function writes to the log and exits by sending an email with the error message.
     *
     * @param string $PV_str : The message to log
     * @param string $PV_fichier : The log file along with the path
     */
    protected function exitlog($PV_str, $PV_fichier) {

        $PV_str .= "\nExiting...";
        $this->writeInfoLog($PV_str, $PV_fichier);
        $this->sendResult($PV_str);
        exit;
    }
    
    
    /**
     * Step 1 : Connect to the Conso-Acteur Database for the respective country.
     * Step 2 : Extract the records required by implementing a business logic
     * Step 3: Insert these records to the temporary database to index them.
     */
    public function createCommonSchema() {

        $bd = new caDialogueBD();

        $tempTable = $this->getTempTableName();
        $countrydb = $this->getCountryDatabase();

        $merchants = $bd->select($countrydb . '.merchants', array('id'), 'affiliatePartner_id=' . $this->getAffiliatePartnerId());

        if ($merchants === FALSE) {
            $this->cleanAndExit('SQL error selecting the merchant from the country database');
        }
        if (!$merchants) {
            $this->cleanAndExit('There are no merchants associated with the source ' . $source);
        }

        $rowMerchants    = $bd->toutesLignes();
        $sourceMerchants = array();
        foreach ($rowMerchants as $merchant) {
            $sourceMerchants[] = $merchant['id'];
        }

        $bd->select($tempTable, '*', 'process_status = 1'); // retrieves all (or part of) the lines to process at this stage
        $rowsTransTemp = $bd->toutesLignes();

        foreach ($rowsTransTemp AS $tempRow) {

            $id              = $tempRow['id'];
            $program_id      = '= ' . $bd->protegeValeur($tempRow['program_id']);
            $name            = '= ' . $bd->protegeValeur($tempRow['name']);
            $user_gain_value = '= ' . $bd->protegeValeur($tempRow['user_gain_value']);

            $fields = array(); //The fields array will be used to update the temporary table
            //Step 1  - Through program_id determine the merchant_id
            $condition_merchantaffiliate = 'program_id' . $program_id . ' AND affiliatePartner_id = ' . $this->getAffiliatePartnerId();
            $offer                       = $bd->select($countrydb . '.merchant_affiliateprogram', 'merchants_id', $condition_merchantaffiliate);

            if ($offer === FALSE) {
                $this->cleanAndExit('SQL error selecting the offer from the country database');
            }

            if ($offer) {
                $fields['merchant_id'] = $bd->valeur('merchants_id');
            } else {
                $errors                   = $tempRow['errors'];
                $errors .= 'The program id was not found in the conso-acteur database';
                $fields['errors']         = $errors;
                $fields['process_status'] = 2; //status matched with conso-acteur database
                if ($bd->update($tempTable, $fields, 'id =' . $id) === FALSE) {
                    $this->cleanAndExit('SQL error updating the record in tempTable for id ' . $id . 'and name : ' . $name);
                }
                continue;
            }

            //Step 2 --- Now determine the process operation insert,update or delete
            //a) -- Check if the offer is unique or not
            $condition_unique = 'merchant_id=' . $fields['merchant_id'] . ' AND user_gain_value' . $user_gain_value;
            $offers           = $bd->select($countrydb . '.offers', array('id'), $condition_unique);

            if ($offers === FALSE) {
                $this->cleanAndExit('SQL error selecting unique offers from the country database');
            }

            if (!$offers) {
                $fields['process_operation'] = 1; //1 for insert
            } else if ($offers == 1) {
                $fields['process_operation'] = 2; //2 for update
                $fields['offer_id']          = $bd->valeur('id');
            } else {
                $errors                   = $tempRow['errors'];
                $errors .= 'Could not find a unique offer to update';
                $fields['errors']         = $errors;
                $fields['process_status'] = 2; //status matched with conso-acteur database
                if ($bd->update($tempTable, $fields, 'id =' . $id) === FALSE) {
                    $this->cleanAndExit('SQL error updating the record in tempTable for id ' . $id . 'and name : ' . $name);
                }
                continue;
            }

            $fields['process_status'] = 2; //status matched with conso-acteur database
            //Get a random slug -- Temporary solution--Need to identify the slug function of doctrine
            $slug                     = strtolower($tempRow['user_gain_value'] . rand(1, 1000));
            $fields['slug']           = str_replace(" ", "", $slug);
            $condition_update         = 'id =' . $id . ' AND type =' . OFFER_TYPE_CODE_PROMO;
            if ($bd->update($tempTable, $fields, $condition_update) === FALSE) {
                $this->cleanAndExit('SQL Error updating temporary table');
            }
        }
        $bd->ferme();
        $this->writeInfoLog('Records processed from the file and in the temporary table to be inserted/updated in the greatdeals database ', self::$logFile);
    }

    /**
     * Execute the business logic to update the transaction statuses to confirmed from pending confirmation.
     * This script does not update those records for which the validation_date or the offer_maturity_period
     * is NULL. There will be a seperate script to handle such cases and update 
     */
    private function transactionExecute() {

        $bd = new caDialogueBD();

        $countrydb = $this->getCountryDatabase();

        //type = 2 refers to Offers only.(see Transaction entity for more details). Check only Cashback and Subscription gain offer types
        $condition_transaction = 'validation_date IS NOT NULL AND status ='.TRANSACTION_STATUS_PENDING_CONFIRMATION .' and type ='.TRANSACTION_TYPE_OFFER .' and offer_type in ('.OFFER_TYPE_CASHBACK.','.OFFER_TYPE_SUBSCRIPTION_GAIN.')';
        $transactions          = $bd->select($countrydb . '.transactions', array('id', 'merchant_id', 'validation_date'), $condition_transaction);

        if ($transactions === FALSE) {
            $this->exitlog('SQL error selecting the transactions from the country database', self::$logFile);
        }

        if (!$transactions) {
            $message = 'There are no records to update the status to confirmed in the transactions table';
            $this->exitlog($message, self::$logFile);
        }

        $rows_transactions = $bd->toutesLignes();

        $fields = array();
        $i = 0;
        foreach ($rows_transactions as $trans) {

            $merchants[]                  = $trans['merchant_id'];
            echo $trans['merchant_id'];
            echo $trans['validation_date'];
            $fields[$i]['id']             = $trans['merchant_id'];
            $fields[$i]['days']           = $this->getDaysDifference($trans['validation_date']);
            $fields[$i]['transaction_id'] = $trans['id'];
            echo $fields[$i]['days'];            
            $i++;
        }

        $merchants           = array_unique($merchants);
        $condition_merchants = 'id in(' . implode(",", $merchants) . ')';
        $merchants           = $bd->select($countrydb . '.merchants', array('id', 'offer_maturity_period'), $condition_merchants);

        if ($merchants === FALSE) {
            $this->exitlog('SQL error selecting the merchants from the country database');
        }

        $merchants_period = $bd->toutesLignes();
        $updateRecord     = array();

        foreach ($fields as $final) {
            
            $period = intval($this->searchForOfferMaturity($final['id'], $merchants_period));
            if (intval($final['days']) > $period && intval($final['days']) > 15 ) {                                                  
                $updateRecord[] = $final['transaction_id'];
            }
        }

        if (count($updateRecord) > 0) {
            $currentTime      = new DateTime("now", new DateTimeZone(self::$localTimeZone));
            $currentTime      = $currentTime->format('Y-m-d H:i:s');
            $condition_update = 'id in(' . implode(",", $updateRecord) . ') and status ='.TRANSACTION_STATUS_PENDING_CONFIRMATION;
            $this->updated    = $bd->update($countrydb . '.transactions', array('status' => TRANSACTION_STATUS_CONFIRMED, 'confirmation_date' => $currentTime, 'updated_at' => $currentTime, 'validation_date' => $currentTime), $condition_update);
            if ($this->updated === FALSE) {
                $this->exitlog('SQL Error updating the status to confirmed ', self::$logFile);
            }
        }
        $bd->ferme();
    }

    /**
     * Sends the results of the script run to the specified email address.
     *  Along with details like number of records updated.
     * 
     * @param string $errorMessage
     */
    public function sendResult($errorMessage = NULL) {

        $message = 'Database Name : ' . $this->getCountryDatabase() . "\n";
        $message .= 'Processing Date : ' . date("Y-m-d") . "\n";
        $message .= 'Records updated : ' . $this->updated . "\n";

        $transport = Swift_SmtpTransport::newInstance(MAILER_HOST, MAILER_PORT)
                ->setUsername(MAILER_USER)
                ->setPassword(MAILER_PASSWORD);

        $mailer = Swift_Mailer::newInstance($transport);

        if (empty($errorMessage)) {
            $message.= 'Status : ' . 'Processed Successfully' . "\n";
            $subject = 'Transaction Confirmation script processing successful';
        } else {
            $message .= 'Status : ' . 'Processing UnSuccessful' . "\n";
            $message .= 'Message : ' . $errorMessage . "\n";
            $subject = 'Transaction Confirmation script processing unsuccessful';
        }
        $email   = Swift_Message::newInstance($subject)
                ->setFrom(array(MAILER_FROM => MAILER_FROM_ALIAS))
                ->setTo(array(MAILER_TO => MAILER_TO_ALIAS))
                ->setBody($message);

        $mailer->send($email);
        exit;
    }

}