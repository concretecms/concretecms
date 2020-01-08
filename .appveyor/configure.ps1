# Define some variables
$GettextVersion = '0.19.8.1'
$IconvVersion = '1.15'

# Setup a sane environment
$ProgressPreference = 'SilentlyContinue'
$ErrorActionPreference = 'Stop'
$ConfirmPreference = 'None'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12 + [Net.SecurityProtocolType]::Tls11 + [Net.SecurityProtocolType]::Tls

# Setup the directory structure
if (-Not(Test-Path -PathType Container -Path 'C:\tools')) {
    Write-Host 'Creating tools directory'
    New-Item -ItemType Directory -Path 'C:\tools' | Out-Null
}
if (-Not(Test-Path -PathType Container -Path 'C:\tools\downloads')) {
    Write-Host 'Creating download directory'
    New-Item -ItemType Directory -Path 'C:\tools\downloads' | Out-Null
}

# Setup gettext & iconv
$Env:Path = "C:\tools\gettext\bin;$Env:Path"
if (-Not(Test-Path -PathType Leaf -Path 'C:\tools\gettext\bin\msgen.exe')) {
    if (-Not(Test-Path -PathType Container -Path 'C:\tools\gettext')) {
        Write-Host 'Creating gettext directory'
        New-Item -ItemType Directory -Path 'C:\tools\gettext' | Out-Null
    }
    if (-Not(Test-Path -PathType Leaf -Path "C:\tools\downloads\gettext$GettextVersion-iconv$IconvVersion.zip")) {
        Write-Host 'Downloading gettext & iconv'
        Invoke-WebRequest -Uri "https://github.com/mlocati/gettext-iconv-windows/releases/download/v$GettextVersion-v$IconvVersion/gettext$GettextVersion-iconv$IconvVersion-shared-32.zip" -OutFile "C:\tools\downloads\gettext$GettextVersion-iconv$IconvVersion.zip"
    }
    Write-Host 'Extracting gettext & iconv'
    Expand-Archive -Path "C:\tools\downloads\gettext$GettextVersion-iconv$IconvVersion.zip" -DestinationPath 'C:\tools\gettext' -Force
}

# Update the module used to install PowerShell modules
Write-Host 'Updating PowerShellGet PowerShell module'
Update-Module -Name PowerShellGet -Force -ErrorAction SilentlyContinue

# Setup VcRedist PowerShell module
try {
    Import-Module VcRedist -ErrorAction Stop
} catch {
    Write-Host 'Installing VcRedist PowerShell module'
    Install-Module -Name VcRedist -Repository PSGallery -Scope AllUsers -Force
}

# Setup PhpManager PowerShell module
try {
    Import-Module PhpManager -ErrorAction Stop
} catch {
    Write-Host 'Installing PhpManager PowerShell module'
    Install-Module -Name PhpManager -Repository PSGallery -Scope AllUsers -Force
}

Set-PhpDownloadCache -Path 'C:\tools\downloads'

# Setup PHP
$phpInstallPath = "C:\tools\php-$Env:PHP_VERSION-$Env:PHP_ARCHITECTURE"
$Env:Path = "$phpInstallPath;$Env:Path"
if (Test-Path -PathType Leaf -Path "$phpInstallPath\php-installed.txt") {
    Write-Host 'Updating PHP'
    Update-Php -Path $phpInstallPath -Verbose | Out-Null
} else {
    Write-Host 'Installing PHP'
    if (Test-Path -Path $phpInstallPath) {
        Remove-Item -Recurse -Force $phpInstallPath
    }
    Install-Php -Version $Env:PHP_VERSION -Architecture $Env:PHP_ARCHITECTURE -ThreadSafe $false -Path $phpInstallPath -TimeZone UTC -InitialPhpIni Production -InstallVC -Force -Verbose
    Set-PhpIniKey -Path $phpInstallPath -Key zend.assertions -Value 1
    Set-PhpIniKey -Path $phpInstallPath -Key assert.exception -Value On
    Set-PhpIniKey -Path $phpInstallPath -Key memory_limit -Value 256M
    Enable-PhpExtension -Path $phpInstallPath -Extension mbstring,bz2,mysqli,curl,gd,intl,pdo_mysql,xsl,fileinfo,openssl,opcache,exif
    New-Item -ItemType File -Path "$phpInstallPath\php-installed.txt" | Out-Null
}
Write-Host 'Refreshing CA Certificates for PHP'
Update-PhpCAInfo -Path $phpInstallPath -Source LocalMachine -Verbose

# Setup composer
$Env:Path = 'C:\tools\bin;' + $Env:Path
if (-Not(Test-Path -PathType Leaf -Path C:\tools\bin\composer.bat)) {
    Write-Host 'Installing Composer'
    Install-Composer -Path C:\tools\bin -PhpPath $phpInstallPath -NoAddToPath -Verbose
} else {
    Write-Host 'Updating Composer'
    & composer self-update
}

# Install hirak/prestissimo composer global package
try {
    & composer global show hirak/prestissimo -q 2>&1
} catch {
    Write-Host 'Installing hirak/prestissimo composer global package'
    try {
        & composer global require --no-interaction --no-progress --optimize-autoloader hirak/prestissimo
    } catch {
        Write-Host 'Failed to install hirak/prestissimo'
    }
}
