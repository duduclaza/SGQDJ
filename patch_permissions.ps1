# ============================================
# PATCH 1: PermissionMiddleware - add toners_defeitos routes
# ============================================
$file1 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\src\Middleware\PermissionMiddleware.php"
$content1 = [System.IO.File]::ReadAllText($file1)
$content1 = $content1 -replace "`r`n", "`n"

if ($content1.Contains("toners_defeitos")) {
    Write-Host "PATCH1 (Middleware): ALREADY_APPLIED"
}
else {
    $oldToners = "'/toners/retornados/delete' => 'toners_retornados',"
    $newToners = @"
'/toners/retornados/delete' => 'toners_retornados',
        
        // Toners com Defeito
        '/toners/defeitos' => 'toners_defeitos',
        '/toners/defeitos/store' => 'toners_defeitos',
        '/toners/defeitos/delete' => 'toners_defeitos',
        '/toners/defeitos/{id}/foto/{n}' => 'toners_defeitos',
"@
    $oldToners = $oldToners -replace "`r`n", "`n"
    $newToners = $newToners -replace "`r`n", "`n"

    if ($content1.Contains($oldToners)) {
        $content1 = $content1.Replace($oldToners, $newToners)
        $content1 = $content1 -replace "`n", "`r`n"
        [System.IO.File]::WriteAllText($file1, $content1)
        Write-Host "PATCH1 (Middleware): APPLIED OK"
    }
    else {
        Write-Host "PATCH1 (Middleware): PATTERN NOT FOUND"
    }
}

# ============================================
# PATCH 2: profiles.php - add module to JS array
# ============================================
$file2 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\views\admin\profiles.php"
$content2 = [System.IO.File]::ReadAllText($file2)
$content2 = $content2 -replace "`r`n", "`n"

if ($content2.Contains("toners_defeitos")) {
    Write-Host "PATCH2 (profiles.php): ALREADY_APPLIED"
}
else {
    $oldModules = "{ key: 'toners_retornados', name: 'Registro de Retornados' },"
    $newModules = @"
{ key: 'toners_retornados', name: 'Registro de Retornados' },
  { key: 'toners_defeitos', name: 'Toners com Defeito \u{1F534}' },
"@
    $oldModules = $oldModules -replace "`r`n", "`n"
    $newModules = $newModules -replace "`r`n", "`n"

    if ($content2.Contains($oldModules)) {
        $content2 = $content2.Replace($oldModules, $newModules)
        $content2 = $content2 -replace "`n", "`r`n"
        [System.IO.File]::WriteAllText($file2, $content2)
        Write-Host "PATCH2 (profiles.php): APPLIED OK"
    }
    else {
        Write-Host "PATCH2 (profiles.php): PATTERN NOT FOUND"
    }
}

# ============================================
# PATCH 3: TonersController.php - enforce permissions
# ============================================
$file3 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\src\Controllers\TonersController.php"
$content3 = [System.IO.File]::ReadAllText($file3)
$content3 = $content3 -replace "`r`n", "`n"

if ($content3.Contains("toners_defeitos")) {
    Write-Host "PATCH3 (Controller): ALREADY_APPLIED"
}
else {
    $lines = $content3.Split("`n")
    $newLines = [System.Collections.ArrayList]::new()
    $patchedDefeitos = $false
    $patchedStore = $false
    $patchedDelete = $false

    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        $trimmed = $line.Trim()

        # Patch defeitos() method - add view permission + pass canEdit/canDelete
        if ($trimmed -eq "public function defeitos(): void" -and -not $patchedDefeitos) {
            $newLines.Add($line) | Out-Null
            $newLines.Add("    {") | Out-Null
            $newLines.Add("        // Verificar permissao de visualizacao") | Out-Null
            $newLines.Add("        `$userId = (int)(`$_SESSION['user_id'] ?? 0);") | Out-Null
            $newLines.Add("        `$canEdit = \App\Services\PermissionService::hasPermission(`$userId, 'toners_defeitos', 'edit');") | Out-Null
            $newLines.Add("        `$canDelete = \App\Services\PermissionService::hasPermission(`$userId, 'toners_defeitos', 'delete');") | Out-Null
            # Skip the next line which is the opening brace
            $i++
            $patchedDefeitos = $true
            continue
        }

        # Find the render call in defeitos() to add canEdit/canDelete
        if ($patchedDefeitos -and -not $patchedStore -and $trimmed -eq "'departamentos_lista' => `$departamentos_lista,") {
            $newLines.Add($line) | Out-Null
            $newLines.Add("            'canEdit' => `$canEdit,") | Out-Null
            $newLines.Add("            'canDelete' => `$canDelete,") | Out-Null
            continue
        }

        # Patch storeDefeito() - add edit permission check
        if ($trimmed -eq "public function storeDefeito(): void" -and -not $patchedStore) {
            $newLines.Add($line) | Out-Null
            $newLines.Add("    {") | Out-Null
            $newLines.Add("        // Verificar permissao de edicao") | Out-Null
            $newLines.Add("        \App\Services\PermissionService::requirePermission((int)(`$_SESSION['user_id'] ?? 0), 'toners_defeitos', 'edit');") | Out-Null
            # Skip the next line which is the opening brace
            $i++
            $patchedStore = $true
            continue
        }

        # Patch deleteDefeito() - add delete permission check
        if ($trimmed -eq "public function deleteDefeito(): void" -and -not $patchedDelete) {
            $newLines.Add($line) | Out-Null
            $newLines.Add("    {") | Out-Null
            $newLines.Add("        // Verificar permissao de exclusao") | Out-Null
            $newLines.Add("        \App\Services\PermissionService::requirePermission((int)(`$_SESSION['user_id'] ?? 0), 'toners_defeitos', 'delete');") | Out-Null
            # Skip the next line which is the opening brace
            $i++
            $patchedDelete = $true
            continue
        }

        $newLines.Add($line) | Out-Null
    }

    $content3 = $newLines -join "`n"
    $content3 = $content3 -replace "`n", "`r`n"
    [System.IO.File]::WriteAllText($file3, $content3)

    if ($patchedDefeitos) { Write-Host "PATCH3a (defeitos view perm): APPLIED OK" } else { Write-Host "PATCH3a: NOT FOUND" }
    if ($patchedStore) { Write-Host "PATCH3b (storeDefeito perm): APPLIED OK" } else { Write-Host "PATCH3b: NOT FOUND" }
    if ($patchedDelete) { Write-Host "PATCH3c (deleteDefeito perm): APPLIED OK" } else { Write-Host "PATCH3c: NOT FOUND" }
}

# ============================================
# PATCH 4: defeitos.php view - conditional UI
# ============================================
$file4 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\views\pages\toners\defeitos.php"
$content4 = [System.IO.File]::ReadAllText($file4)
$content4 = $content4 -replace "`r`n", "`n"

if ($content4.Contains("canEdit")) {
    Write-Host "PATCH4 (View): ALREADY_APPLIED"
}
else {
    # Wrap the form section with canEdit check
    $oldFormStart = '<form id="formDefeito"'
    $newFormStart = "<?php if (!empty(`$canEdit)): ?>`n    <form id=""formDefeito"""

    # Find the closing </form> and add endif
    $oldFormEnd = "</form>`n  </div>"
    $newFormEnd = "</form>`n  <?php else: ?>`n    <div class=""bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center text-yellow-700 text-sm"">`n      <svg class=""w-5 h-5 inline mr-1"" fill=""none"" stroke=""currentColor"" viewBox=""0 0 24 24""><path stroke-linecap=""round"" stroke-linejoin=""round"" stroke-width=""2"" d=""M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z""/></svg>`n      Voce nao tem permissao para registrar defeitos.`n    </div>`n  <?php endif; ?>`n  </div>"

    $patchedForm = $false

    if ($content4.Contains($oldFormStart)) {
        $content4 = $content4.Replace($oldFormStart, $newFormStart)
        $patchedForm = $true
    }

    if ($content4.Contains($oldFormEnd)) {
        $content4 = $content4.Replace($oldFormEnd, $newFormEnd)
    }

    # Wrap delete buttons with canDelete check
    # Find the delete button pattern in history table
    $oldDeleteBtn = "onclick=""excluirDefeito("
    if ($content4.Contains($oldDeleteBtn)) {
        # We need to find all excluirDefeito buttons and wrap them
        # Since this is in a PHP loop, let's add a PHP conditional around them
        # Actually, let's just use JS to hide them based on a PHP variable
        
        # Add a JS variable at the top of the script section
        $oldScriptMarker = "// =====================================================`n// Envio do formulário"
        $newScriptMarker = "// Flag de permissoes (injetado pelo PHP)`nconst canEdit = <?php echo json_encode(!empty(`$canEdit)); ?>;`nconst canDelete = <?php echo json_encode(!empty(`$canDelete)); ?>;`n`n// Esconder botoes de exclusao se sem permissao`nif (!canDelete) {`n  document.addEventListener('DOMContentLoaded', () => {`n    document.querySelectorAll('[onclick^=""excluirDefeito""]').forEach(btn => {`n      btn.style.display = 'none';`n    });`n  });`n}`n`n// =====================================================`n// Envio do formulário"

        if ($content4.Contains($oldScriptMarker)) {
            $content4 = $content4.Replace($oldScriptMarker, $newScriptMarker)
            Write-Host "PATCH4b (delete buttons): APPLIED OK"
        }
        else {
            Write-Host "PATCH4b (delete buttons): SCRIPT MARKER NOT FOUND"
        }
    }

    $content4 = $content4 -replace "`n", "`r`n"
    [System.IO.File]::WriteAllText($file4, $content4)

    if ($patchedForm) { Write-Host "PATCH4a (form wrap): APPLIED OK" } else { Write-Host "PATCH4a: FORM NOT FOUND" }
}

Write-Host "`nALL PATCHES COMPLETE"
