$file = "app/views/gerer_echanges.php"
$content = Get-Content $file -Raw

# Remplacements de variables
$content = $content -replace '\$senders', '$expediteurs'
$content = $content -replace '\$receivers', '$destinataires'
$content = $content -replace '\$user_data', '$donnees_utilisateur'
$content = $content -replace '\$exchange_items', '$elements_echange'
$content = $content -replace '\$all_users', '$tous_utilisateurs'

$content | Set-Content $file

Write-Host "✓ Remplacement effectué dans $file"
