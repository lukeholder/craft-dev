<?php


function cli() {
    $localVolsToMigrate = [
        [
            'name' => 'SEA Page Images',
            'handle' => 'pageImages',
            'baseUrl' => '@assetsUrl/images/pages',
            'fileSystemPath' => '@webroot/images/pages',
        ],
        [
            'name' => 'SEA Partner Images',
            'handle' => 'partnerImages',
            'baseUrl' => '@assetsUrl/images/partner',
            'fileSystemPath' => '@webroot/images/partner',
        ],
        [
            'name' => 'SEA Ad Images',
            'handle' => 'adImages',
            'baseUrl' => '@assetsUrl/images/ads',
            'fileSystemPath' => '@webroot/images/ads',
        ],
        [
            'name' => 'SEA General Images',
            'handle' => 'generalImages',
            'baseUrl' => '@assetsUrl/images/general',
            'fileSystemPath' => '@webroot/images/general',
        ],
        [
            'name' => 'SEA Sponsor Logos',
            'handle' => 'sponsorLogos',
            'baseUrl' => '@assetsUrl/images/logos',
            'fileSystemPath' => '@webroot/images/logos',
        ],
        [
            'name' => 'User Photos',
            'handle' => 'userPhotos',
            'baseUrl' => '@assetsUrl/images/members',
            'fileSystemPath' => '@webroot/images/members',
        ],
        [
            'name' => 'LA Page Images',
            'handle' => 'la_pageImages',
            'baseUrl' => '@assetsUrl/images/la/pages',
            'fileSystemPath' => '@webroot/images/la/pages',
        ],
        [
            'name' => 'LA Partner Images',
            'handle' => 'la_partnerImages',
            'baseUrl' => '@assetsUrl/images/la/partner',
            'fileSystemPath' => '@webroot/images/la/partner',
        ],
        [
            'name' => 'LA Ad Images',
            'handle' => 'la_adImages',
            'baseUrl' => '@assetsUrl/images/la/ads',
            'fileSystemPath' => '@webroot/images/la/ads',
        ],
        [
            'name' => 'LA General Images',
            'handle' => 'la_generalImages',
            'baseUrl' => '@assetsUrl/images/la/general',
            'fileSystemPath' => '@webroot/images/la/general',
        ],
        [
            'name' => 'LA Sponsor Logos',
            'handle' => 'la_sponsorLogos',
            'baseUrl' => '@assetsUrl/images/la/logos',
            'fileSystemPath' => '@webroot/images/la/logos',
        ],
        [
            'name' => 'Site folder (not an asset)',
            'handle' => 'N/A',
            'baseUrl' => '@assetsUrl/images/site',
            'fileSystemPath' => '@webroot/images/site',
        ],
    ];

    $azureBucketConfig = [
        'local-dev' => [
            'azurePrefix' => 'https://stagingteentixassets.blob.core.windows.net/local-developer-assets',
        ],
        'dev' => [
            'azurePrefix' => 'https://stagingteentixassets.blob.core.windows.net/dev-assets',
        ],
        'staging' => [
            'azurePrefix' => 'https://stagingteentixassets.blob.core.windows.net/staging-assets',
        ],
        'prod' => [
            'azurePrefix' => 'https://productionteentixassets.blob.core.windows.net/production-assets',
        ],
    ];

    $options = getopt('', ['env:', 'copyAssets']);
    $copyAssets = array_key_exists('copyAssets', $options);
    $env = $options['env'];
    $knownEnvs = array_keys($azureBucketConfig);

    if (!in_array($env, $knownEnvs)) {
        throw new Exception("Unknown --env '$env'. env must be one of: ".json_encode($knownEnvs));
    }

    $azureConfig = $azureBucketConfig[$env];
    $azurePrefix = $azureConfig['azurePrefix'];
    $azureAccountName = $azureConfig['azureAccountName'];
    $azureContainer = $azureConfig['azureContainer'];
    $useUploadBatchInsteadOfAzcopySync = $azureConfig['useUploadBatchInsteadOfAzcopySync'] ?? false;

    if (array_key_exists('azureCreds', $azureConfig)) {
        
        $azureCredVarName = "AZURE_STORAGE_CONNECTION_STRING";
        echo "Setting $azureCredVarName environment variable".PHP_EOL;
        putenv($azureCredVarName."=".$azureConfig['azureCreds']);
    }

    $currentDir = _joinPosixPaths(getcwd(), 'html');
    if (!is_dir($currentDir)) {
        throw new Exception("Expected directory at: '$currentDir'");
    }

    if ($useUploadBatchInsteadOfAzcopySync) {
        _checkAzCliUtil();
    } else {
        _checkAzcopyUtil();
    }

    $errors = [];
    
    foreach ($localVolsToMigrate as $localVolumeToMigrate) {
        echo "Processing volume named ".$localVolumeToMigrate['name'].PHP_EOL;
        $baseUrl = explode('/', $localVolumeToMigrate['baseUrl'], 3);
        $fileSystemPath = explode('/', $localVolumeToMigrate['fileSystemPath'], 3);

        if ($baseUrl[0] !== '@assetsUrl') {
            throw new Exception('Expected baseUrl to start with @assetsUrl for this volume: '.json_encode($localVolumeToMigrate, JSON_PRETTY_PRINT));
        }
        if ($fileSystemPath[0] !== '@webroot') {
            throw new Exception('Expected fileSystemPath to start with @webroot for this volume: '.json_encode($localVolumeToMigrate, JSON_PRETTY_PRINT));
    
        }

        $baseUrlSuffix = _joinPosixPaths($baseUrl[1], $baseUrl[2]);
        $fileSystemPathSuffix = _joinPosixPaths($fileSystemPath[1], $fileSystemPath[2]);
        if ($baseUrlSuffix !== $fileSystemPathSuffix) {
            throw new Exception("Expected baseUrl and fileSystemPath to have identical paths after the top level alias. baseUrl suffix '$baseUrlSuffix' does not match fileSystemPath suffix '$fileSystemPathSuffix'. ".json_encode($localVolumeToMigrate, JSON_PRETTY_PRINT));
        }

        $localDirectoryToCopy = _joinPosixPaths($currentDir, $fileSystemPathSuffix);
        if (!is_dir($localDirectoryToCopy)) {
            throw new Exception("Expected local directory '$localDirectoryToCopy' to exist for volume. ".json_encode($localVolumeToMigrate, JSON_PRETTY_PRINT));
        }

        $azDestUrl = $azurePrefix."/".$fileSystemPathSuffix;

        if ($useUploadBatchInsteadOfAzcopySync) {
            $azCommand = 'az storage blob upload-batch --source "'.$localDirectoryToCopy.'" --destination "'.$azureContainer.'" --destination-path "'.$fileSystemPathSuffix.'" --overwrite ';
            if (!$copyAssets) {
                $azCommand .= '--dryrun ';
            }    
        } else {
            $azCommand = 'azcopy sync "'.$localDirectoryToCopy.'" "'.$azDestUrl.'" --recursive=true --from-to=LocalBlob ';
            if (!$copyAssets) {
                $azCommand .= '--dry-run ';
            }    
        }

        $cmdToRun = explode(' ', $azCommand, 2)[0];
        echo 'About to run command: '.$azCommand.PHP_EOL;

        $result = system($azCommand, $azResultCode);
        echo $azCommand.' result code: '.$azResultCode.PHP_EOL.PHP_EOL;
        if ($result === false || $azResultCode !== 0) {
            $errors[] = $azCommand." call failed (returned code $azResultCode) for , see error output :-(";
        }
    }

    if (count($errors) > 0) {
        throw new Exception("Errors:\n".implode('\n', $errors));
    }
}

function _joinPosixPaths() {
    $paths = array();

    foreach (func_get_args() as $arg) {
        if ($arg !== '') { $paths[] = $arg; }
    }

    return preg_replace('#/+#','/',join('/', $paths));
}

function _checkAzCliUtil() {
    $azVersion = shell_exec('az version');
    if ($azVersion == null || $azVersion === false) {
        throw new Exception('Install az. On macOS: brew install az');
    }
    try {
        $azVersionArray = json_decode($azVersion, $assoc = true, $depth = 512, $options = JSON_THROW_ON_ERROR);
    } catch (JsonException $je) {
        throw new Exception('Ran `az version` and expected output to be json. Output was: '.$azVersion);
    }
    $azVersionOutput = $azVersionArray['azure-cli'] ?? '';
    if ($azVersionOutput === '') {
        throw new Exception('Ran `az version` and expected output to be json and have "azure-cli" key in the json. Output was: '.$azVersion);
    }
}

function _checkAzcopyUtil() {
    $azVersion = shell_exec('azcopy --version');
    if ($azVersion == null || $azVersion === false) {
        throw new Exception('Install azcopy. On macOS: brew install azcopy');
    }
    $expectedOutputPrefix = 'azcopy version ';
    if (substr($azVersion, 0, strlen($expectedOutputPrefix)) !== $expectedOutputPrefix) {
        throw new Exception('Ran `azcopy --version` and expected output to start with: '.$expectedOutputPrefix);
    }
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
