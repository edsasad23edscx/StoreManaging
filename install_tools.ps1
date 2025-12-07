$ErrorActionPreference = "Stop"

$binDir = "$PSScriptRoot\.bin"
if (!(Test-Path $binDir)) {
    New-Item -ItemType Directory -Force -Path $binDir | Out-Null
    Write-Host "Created .bin directory"
}

$phpZipUrl = "https://windows.php.net/downloads/releases/php-8.3.28-Win32-vs16-x64.zip"
$phpZipPath = "$binDir\php.zip"
$phpDir = "$binDir\php"

if (!(Test-Path $phpDir)) {
    Write-Host "Downloading PHP from $phpZipUrl..."
    Invoke-WebRequest -Uri $phpZipUrl -OutFile $phpZipPath
    
    Write-Host "Extracting PHP..."
    Expand-Archive -Path $phpZipPath -DestinationPath $phpDir -Force
    Remove-Item $phpZipPath
} else {
    Write-Host "PHP already present."
}

# Setup php.ini
$phpIniPath = "$phpDir\php.ini"
if (!(Test-Path $phpIniPath)) {
    Copy-Item "$phpDir\php.ini-development" $phpIniPath
    
    $iniContent = Get-Content $phpIniPath
    $iniContent = $iniContent -replace ";extension_dir = ""ext""", "extension_dir = ""ext"""
    $iniContent = $iniContent -replace ";extension=curl", "extension=curl"
    $iniContent = $iniContent -replace ";extension=fileinfo", "extension=fileinfo"
    $iniContent = $iniContent -replace ";extension=mbstring", "extension=mbstring"
    $iniContent = $iniContent -replace ";extension=openssl", "extension=openssl"
    $iniContent = $iniContent -replace ";extension=pdo_sqlite", "extension=pdo_sqlite"
    $iniContent = $iniContent -replace ";extension=sqlite3", "extension=sqlite3"
    
    $iniContent | Set-Content $phpIniPath
    Write-Host "Configured php.ini"
}

# Download Composer
$composerPath = "$binDir\composer.phar"
if (!(Test-Path $composerPath)) {
    Write-Host "Downloading Composer..."
    Invoke-WebRequest -Uri "https://getcomposer.org/composer.phar" -OutFile $composerPath
}

# Create wrapper scripts
$batContent = "@echo off`r`n""$phpDir\php.exe"" ""$binDir\composer.phar"" %*"
Set-Content "$binDir\composer.bat" $batContent

$phpBatContent = "@echo off`r`n""$phpDir\php.exe"" %*"
Set-Content "$binDir\php.bat" $phpBatContent

Write-Host "Installation Complete."
Write-Host "You can now use '$binDir\composer.bat install'"
