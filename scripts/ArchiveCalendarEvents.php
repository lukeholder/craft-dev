<?php

function cli() {
    $options = getopt('', ['doArchive']);
    $doArchive = array_key_exists('doArchive', $options);

    define('CRAFT_BASE_PATH', dirname(__DIR__));
    define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH.'/vendor');
    define('CRAFT_COMPOSER_PATH', CRAFT_BASE_PATH.'/composer.json');

    require_once CRAFT_VENDOR_PATH.'/autoload.php';

    $env = getenv('ENVIRONMENT');
    l("environment:", $env);
    $knownEnvs = ['dev', 'staging', 'production'];
    if (!in_array($env, $knownEnvs)) {
        throw new Exception("unknown env '$env'. Expected one of: ".implode(",", $knownEnvs));
    }
    define('CRAFT_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');
    l("loading craft app...");
    $app = require CRAFT_VENDOR_PATH.'/craftcms/cms/bootstrap/console.php';

    $threshold = date("Y-m-d", strtotime("-6 months"));
    l("==> Threshold date: ".$threshold);
    $selectBackupThreshold = "select date_format(endDate, '%Y-%m-%d')
from calendar_events
order by endDate desc
limit 1
offset 500
;";
    $backupThreshold = $app->db->createCommand()->setSql($selectBackupThreshold)->queryScalar();
    l("--> Backup threshold date: ".$backupThreshold);

    $createCalendarExceptionsArchiveTableSql = 'create table if not exists calendar_exceptions_archive like calendar_exceptions;';
    $createCalendarEventsArchiveTableSql = 'create table if not exists calendar_events_archive like calendar_events;';
    $insertCalendarExceptionsSql = "insert into calendar_exceptions_archive
select x.*
from calendar_exceptions x
inner join calendar_events v on x.eventId = v.id
where v.endDate < :threshold
;";
    $insertCalendarEventsSql = "insert into calendar_events_archive
select *
from calendar_events
where endDate < :threshold
;";
    $deleteCalendarExceptionsSql = 'delete from calendar_exceptions
where id in (select id from calendar_exceptions_archive)
;';
    $deleteCalendarEventsSql = 'delete from calendar_events
where id in (select id from calendar_events_archive)
;';
    $selectEventCount = 'select count(*) from calendar_events;';
    $selectEventsToDelete = 'select count(*) from calendar_events where endDate < :threshold;';

    function runSql($sqlStmt, $app, $doArchive, $threshold='') {
        $cmd = $app->db->createCommand()->setSql($sqlStmt);
        if (!empty($threshold)) {
            $cmd = $cmd->bindParam(':threshold', $threshold, PDO::PARAM_STR);
        }
        if (!$doArchive) {
            l("DRYRUN SQL: ".$cmd->getRawSql());
        } else {
            l("Running SQL: ".$sqlStmt);
            $cmd->execute();
        }
    }

    runSql($createCalendarExceptionsArchiveTableSql, $app, $doArchive);
    runSql($createCalendarEventsArchiveTableSql, $app, $doArchive);
    $tx = $app->db->beginTransaction();
    try {
        $startEventCount = $app->db->createCommand()->setSql($selectEventCount)->queryScalar();
        $deleteEstimate = $app->db->createCommand()->setSql($selectEventsToDelete)->bindParam(':threshold', $threshold)->queryScalar();
        $remainingEstimate = $startEventCount - $deleteEstimate;
        l("Starting calendar event count: ".$startEventCount);
        l("Expecting to archive ".$deleteEstimate." events leaving ".$remainingEstimate." events remaining");
        if ($remainingEstimate < 500) {
            l("WARNING: Only ".$remainingEstimate." events would be left remaining with ".$threshold." date, so we'll use the backup threshold date: ".$backupThreshold);
            $threshold = $backupThreshold;
        }
        runSql($insertCalendarExceptionsSql, $app, $doArchive, $threshold);
        runSql($insertCalendarEventsSql, $app, $doArchive, $threshold);
        runSql($deleteCalendarExceptionsSql, $app, $doArchive);
        runSql($deleteCalendarEventsSql, $app, $doArchive);
        $finalEventCount = $app->db->createCommand()->setSql($selectEventCount)->queryScalar();
        $deletedEventCount = $startEventCount - $finalEventCount;
        l("Final calendar event count: ".$finalEventCount);
        l("Deleted ".$deletedEventCount." events");
        $tx->commit();
    } catch (\Exception $e) {
        l("Exception, rolling back tx and rethrowing");
        $tx->rollback();
        throw $e;
    } catch (\Throwable $e) {
        l("Throwable, rolling back tx and rethrowing");
        $tx->rollback();
        throw $e;
    } finally {
        $chownCmd = 'chown -R www-data:www-data /app/storage';
        l('Fixing up directory perms by running: '.$chownCmd);
        $chownResult = system($chownCmd, $chownExitCode);
        if ($chownResult === false || $chownExitCode !== 0) {
            l('ERROR: ran '.$chownCmd.' and got result: '.$chownResult.' and exit code: '.$chownExitCode);
            throw new Exception($chownCmd.' was not successful got result: '.$chownResult.' and exit code: '.$chownExitCode);
        }
    }
    l('done :-)');
}

function l(...$msg) {
    $now = new DateTime();
    echo $now->format(DateTimeInterface::ISO8601) ." ". implode(" ", $msg).PHP_EOL;
}

$start = new DateTime();
try {
    cli();
} finally {
    $end = new DateTime();
    $elapsed = $end->diff($start);
    echo 'Elapsed: '.$elapsed->format('%H:%I:%S.%f').PHP_EOL;
}
