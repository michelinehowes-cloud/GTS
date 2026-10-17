# Build DirectAdmin Deployment Packages
$baseDir = "c:\Users\Sohib\graduate_training_system"
$deployDir = "$baseDir\deploy_artifacts"
$tempDir = "$deployDir\temp_build"

Write-Host ">>> Starting packaging for DirectAdmin (gto.com.ly)..." -ForegroundColor Cyan

if (Test-Path $tempDir) {
    Remove-Item -Recurse -Force $tempDir
}
New-Item -ItemType Directory -Force -Path $tempDir | Out-Null
New-Item -ItemType Directory -Force -Path "$tempDir\core" | Out-Null
New-Item -ItemType Directory -Force -Path "$tempDir\public_html" | Out-Null

# 1. Prepare public_html folder
Write-Host ">>> Assembling public_html files..." -ForegroundColor Yellow
Copy-Item -Recurse "$baseDir\public\*" "$tempDir\public_html\"
# Replace index.php with the DirectAdmin version
Copy-Item "$deployDir\index.php" "$tempDir\public_html\index.php" -Force
# Add setup_helper.php
Copy-Item "$deployDir\setup_helper.php" "$tempDir\public_html\setup_helper.php" -Force

# 2. Prepare core folder
Write-Host ">>> Assembling core Laravel files..." -ForegroundColor Yellow
$coreItems = @('app', 'bootstrap', 'config', 'database', 'lang', 'resources', 'routes', 'storage', 'vendor', 'artisan', 'composer.json', 'composer.lock', 'package.json', 'vite.config.js')
foreach ($item in $coreItems) {
    if (Test-Path "$baseDir\$item") {
        Copy-Item -Recurse "$baseDir\$item" "$tempDir\core\$item"
    }
}

# Copy .env.production as .env into core
Copy-Item "$baseDir\.env.production" "$tempDir\core\.env" -Force

# Clean logs in storage
if (Test-Path "$tempDir\core\storage\logs") {
    Get-ChildItem "$tempDir\core\storage\logs\*.log" | Remove-Item -Force -ErrorAction SilentlyContinue
}

# 3. Create ZIP Archives
Write-Host ">>> Compressing gts_public_html.zip..." -ForegroundColor Green
$publicZip = "$deployDir\gts_public_html.zip"
if (Test-Path $publicZip) { Remove-Item $publicZip -Force }
Compress-Archive -Path "$tempDir\public_html\*" -DestinationPath $publicZip -CompressionLevel Optimal

Write-Host ">>> Compressing gts_core.zip (this may take 1-2 minutes)..." -ForegroundColor Green
$coreZip = "$deployDir\gts_core.zip"
if (Test-Path $coreZip) { Remove-Item $coreZip -Force }
Compress-Archive -Path "$tempDir\core\*" -DestinationPath $coreZip -CompressionLevel Optimal

# Cleanup temp
Remove-Item -Recurse -Force $tempDir

Write-Host ">>> Deployment Packages Created Successfully!" -ForegroundColor Cyan
Get-ChildItem "$deployDir\*.zip", "$deployDir\*.sql" | Select-Object Name, @{Name="Size (MB)"; Expression={[math]::Round($_.Length / 1MB, 2)}}, LastWriteTime
