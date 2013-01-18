<?php

require_once dirname(__FILE__) . '/lib/caDialogueBD.class.php';
require_once dirname(__FILE__) . '/SearchIndexScript.php';
require_once dirname(__FILE__) . '/constants.php';
require_once dirname(__FILE__) . '/swiftmailer/lib/swift_required.php';

if (empty($argv[1]) || empty($argv[2])) {

    echo "Input Error : Please enter the following argument for a successful run as mentioned below..\n";
    echo "USAGE:\n";
    echo "The first argument must be the country database name. Eg:consoacteur_uk, consoacteur_fr...\n";
    echo "The second argument must be the locale for which the index must be created. Eg:fr,en...\n";
    echo "Example run : php ProcessTransactionConfirm.php consoacteur_fr fr\n";
    echo "Exiting without processing...\n";
    exit;
}

$database = $argv[1];
$locale   = $argv[2];

$object = new TransactionRecordsConfirm();

if (!is_a($object, 'SearchIndexScript')) {
    echo 'Fatal Error : Not an object of TransactionRecordsConfirm' . "\n";
    exit;
}

$object->process($database,$locale);

exit;