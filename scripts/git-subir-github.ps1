# Uso:  .\scripts\git-subir-github.ps1
#    o:  .\scripts\git-subir-github.ps1 -Mensaje "mi mensaje de commit"
param(
    [string]$Mensaje = "feat: recordatorios (alarmas, aplazar/reprogramar), config, controladores auth"
)
$ErrorActionPreference = "Stop"
Set-Location (Split-Path -Parent $PSScriptRoot)
if (-not (Test-Path .git)) { Write-Error "No hay repositorio git en $(Get-Location)" }

git add -A
git status
git commit -m $Mensaje
if ($LASTEXITCODE -ne 0) {
    Write-Host "Commit omitido o sin cambios. Revisa arriba." -ForegroundColor Yellow
    exit $LASTEXITCODE
}
git push origin main
Write-Host "Push completado a origin/main." -ForegroundColor Green
