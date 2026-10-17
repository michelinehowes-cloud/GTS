$baseDir = "c:\Users\Sohib\graduate_training_system"
$deployDir = "$baseDir\deploy_artifacts"
$staging = "$deployDir\public_html_staging"

if (Test-Path $staging) {
    Remove-Item -Recurse -Force $staging
}
New-Item -ItemType Directory -Force -Path $staging | Out-Null

Copy-Item -Recurse "$baseDir\public\*" "$staging\"
Copy-Item "$deployDir\index.php" "$staging\index.php" -Force
Copy-Item "$deployDir\setup_helper.php" "$staging\setup_helper.php" -Force

# Create gts_public_html.zip with tar
$zipPath = "$deployDir\gts_public_html.zip"
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

& tar -a -cf $zipPath -C $staging .

Remove-Item -Recurse -Force $staging
Write-Host "gts_public_html.zip created successfully!"
