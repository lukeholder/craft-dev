<?php

function cli() {
    $options = getopt('', ['envFile:', 'doTransfer']);
    $doMigration = array_key_exists('doTransfer', $options);

    define('CRAFT_BASE_PATH', dirname(__DIR__));
    define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH.'/vendor');
    define('CRAFT_COMPOSER_PATH', CRAFT_BASE_PATH.'/composer.json');

    require_once CRAFT_VENDOR_PATH.'/autoload.php';

    if (array_key_exists('envFile', $options) && !empty($options['envFile'])) {
        $envFile = $options['envFile'];
        if (!file_exists($envFile)) {
            throw new Exception("--envFile '$envFile' not found");
        }

        l("loading environment from $envFile...");
        (new Dotenv\Dotenv(CRAFT_BASE_PATH, $envFile))->load();    
    }

    $env = getenv('ENVIRONMENT');    
    l("environment:", $env);
    $knownEnvs = ['dev', 'staging', 'production'];
    if (!in_array($env, $knownEnvs)) {
        throw new Exception("unknown env '$env'. Expected one of: ".implode(",", $knownEnvs));
    }
    define('CRAFT_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');
    l("loading craft app...");
    $app = require CRAFT_VENDOR_PATH.'/craftcms/cms/bootstrap/console.php';

    $firstId = 28262;
    $lastId = 30761;

    $seaSiteId = 1;
    $laSiteId = 2;

    $seaSectionId = 11;  // passes
    $laSectionId = 16;   // la_passes

    $seaEntryTypeId = 12;
    $laEntryTypeId = 23;

    l("Finding IDs to migrate...");
    $idsToTransfer = craft\elements\Entry::find()
        ->siteId($seaSiteId)
        ->where(['between', 'entries.id', $firstId, $lastId])
        ->orderBy(['entries.id' => SORT_ASC])
        ->ids();

    l("Found", count($idsToTransfer), "IDs to migrate.");
    $expectedIdsToTransfer = $lastId - $firstId + 1;
    if (count($idsToTransfer) !== $expectedIdsToTransfer) {
        throw new Exception("Expected to find $expectedIdsToTransfer entries to transfer, but only found ".count($idsToTransfer));
    }

    $elementsSql = _formatLongSql($app->db->createCommand()
        ->update('{{%elements_sites}}', ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
        ->getRawSql());

    $contentSql = _formatLongSql($app->db->createCommand()
        ->update('{{%content}}',        ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
        ->getRawSql());

    $searchIndexSql = _formatLongSql($app->db->createCommand()
        ->update('{{%searchindex}}',    ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
        ->getRawSql());

    $entriesUpdateSql = _formatLongSql($app->db->createCommand()
        ->update('{{%entries}}', ['sectionId' => $laSectionId, 'typeId' => $laEntryTypeId], ['id' => $idsToTransfer], [], false)
        ->getRawSql());

    if ($doMigration) {
        l("About to execute this query to update elements:\n$elementsSql");
        _pauseUntilKeypress();
        $elementsUp = $app->db->createCommand()
            ->update('{{%elements_sites}}', ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
            ->execute();
        l("Updated", $elementsUp, "rows");

        l("About to execute this query to update content:\n$contentSql");
        _pauseUntilKeypress();
        $contentUp = $app->db->createCommand()
            ->update('{{%content}}',        ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
            ->execute();
        l("Updated", $contentUp, "rows");

        l("About to execute this query to update searchindex:\n$searchIndexSql");
        _pauseUntilKeypress();
        $searchindexUp = $app->db->createCommand()
            ->update('{{%searchindex}}',    ['siteId' => $laSiteId], ['elementId' => $idsToTransfer], [], false)
            ->execute();
        l("Updated", $searchindexUp, "rows");

        l("About to execute this query to update entries:\n$entriesUpdateSql");
        _pauseUntilKeypress();
        $entriesUp = $app->db->createCommand()
            ->update('{{%entries}}', ['sectionId' => $laSectionId, 'typeId' => $laEntryTypeId], ['id' => $idsToTransfer], [], false)
            ->execute();
        l("Updated", $entriesUp, "rows");
    } else {
        l("DRYRUN: would execute this SQL to update sites:\n".$elementsSql);
        l("DRYRUN: would execute this SQL to update content:\n".$contentSql);
        l("DRYRUN: would execute this SQL to update search index:\n".$searchIndexSql);
        l("DRYRUN: would execute this SQL to update entries:\n".$entriesUpdateSql);

        l("DRYRUN: Call this script again and includ the --doTransfer flag to execute the above changes");
    }
    l("Done!!!");
}

function l(...$msg) {
    $now = new DateTime();
    echo $now->format(DateTimeInterface::ISO8601) ." ". implode(" ", $msg).PHP_EOL;
}

function _formatLongSql($longSql) {
    $len = strlen($longSql);
    if ($len < 150) {
        return $longSql;
    }
    $prefix = substr($longSql, 0, 100);
    $suffix = substr($longSql, $len-48);
    return $prefix." ... ".$suffix;
}

function _pauseUntilKeypress() {
    echo "Press enter key to continue...";
    $handle = fopen('php://stdin', 'r');
    try {
        $line = fgets($handle);
    } finally {
        fclose($handle);
    }
    echo PHP_EOL;
}

$start = new DateTime();
try {
    cli();
} finally {
    $end = new DateTime();
    $elapsed = $end->diff($start);
    echo 'Elapsed: '.$elapsed->format('%H:%I:%S.%f').PHP_EOL;
}
