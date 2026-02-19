# Fix controller permissions + view delete buttons
# ============================================
# PATCH A: Controller - add permission checks
# ============================================
$file3 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\src\Controllers\TonersController.php"
$content3 = [System.IO.File]::ReadAllText($file3)
$content3 = $content3 -replace "`r`n", "`n"

# Check if already patched (look for the specific permission string, not table name)
if ($content3.Contains("requirePermission") -and $content3.Contains("'toners_defeitos', 'edit'")) {
    Write-Host "PATCH_A (Controller perms): ALREADY_APPLIED"
}
else {
    $lines = $content3.Split("`n")
    $newLines = [System.Collections.ArrayList]::new()
    $patchedDefeitos = $false
    $patchedStore = $false
    $patchedDelete = $false
    $foundRenderDefeitos = $false

    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        $trimmed = $line.Trim()

        # Patch defeitos() method
        if ($trimmed -eq "public function defeitos(): void" -and -not $patchedDefeitos) {
            $newLines.Add($line) | Out-Null
            # Next line should be opening brace
            $i++
            $newLines.Add($lines[$i]) | Out-Null
            # Add permission variables
            $newLines.Add("        // Verificar permissoes do modulo") | Out-Null
            $newLines.Add("        `$userId = (int)(`$_SESSION['user_id'] ?? 0);") | Out-Null
            $newLines.Add("        `$canEdit = \App\Services\PermissionService::hasPermission(`$userId, 'toners_defeitos', 'edit');") | Out-Null
            $newLines.Add("        `$canDelete = \App\Services\PermissionService::hasPermission(`$userId, 'toners_defeitos', 'delete');") | Out-Null
            $newLines.Add("") | Out-Null
            $patchedDefeitos = $true
            continue
        }

        # Find the render call in defeitos() to add canEdit/canDelete to view data
        if ($patchedDefeitos -and -not $foundRenderDefeitos -and $trimmed -eq "'departamentos_lista' => `$departamentos_lista,") {
            $newLines.Add($line) | Out-Null
            $newLines.Add("            'canEdit' => `$canEdit,") | Out-Null
            $newLines.Add("            'canDelete' => `$canDelete,") | Out-Null
            $foundRenderDefeitos = $true
            continue
        }

        # Patch storeDefeito()
        if ($trimmed -eq "public function storeDefeito(): void" -and -not $patchedStore) {
            $newLines.Add($line) | Out-Null
            # Next line should be opening brace
            $i++
            $newLines.Add($lines[$i]) | Out-Null
            # Add permission check
            $newLines.Add("        // Verificar permissao de edicao") | Out-Null
            $newLines.Add("        \App\Services\PermissionService::requirePermission((int)(`$_SESSION['user_id'] ?? 0), 'toners_defeitos', 'edit');") | Out-Null
            $newLines.Add("") | Out-Null
            $patchedStore = $true
            continue
        }

        # Patch deleteDefeito()
        if ($trimmed -eq "public function deleteDefeito(): void" -and -not $patchedDelete) {
            $newLines.Add($line) | Out-Null
            # Next line should be opening brace
            $i++
            $newLines.Add($lines[$i]) | Out-Null
            # Add permission check
            $newLines.Add("        // Verificar permissao de exclusao") | Out-Null
            $newLines.Add("        \App\Services\PermissionService::requirePermission((int)(`$_SESSION['user_id'] ?? 0), 'toners_defeitos', 'delete');") | Out-Null
            $newLines.Add("") | Out-Null
            $patchedDelete = $true
            continue
        }

        $newLines.Add($line) | Out-Null
    }

    $content3 = $newLines -join "`n"
    $content3 = $content3 -replace "`n", "`r`n"
    [System.IO.File]::WriteAllText($file3, $content3)

    if ($patchedDefeitos) { Write-Host "PATCH_A1 (defeitos view check): APPLIED OK" } else { Write-Host "PATCH_A1: NOT FOUND" }
    if ($foundRenderDefeitos) { Write-Host "PATCH_A2 (render params): APPLIED OK" } else { Write-Host "PATCH_A2: NOT FOUND" }
    if ($patchedStore) { Write-Host "PATCH_A3 (storeDefeito check): APPLIED OK" } else { Write-Host "PATCH_A3: NOT FOUND" }
    if ($patchedDelete) { Write-Host "PATCH_A4 (deleteDefeito check): APPLIED OK" } else { Write-Host "PATCH_A4: NOT FOUND" }
}

# ============================================
# PATCH B: View - add canDelete JS variable for hiding buttons
# ============================================
$file4 = "c:\Users\djkyk\OneDrive\Desktop\SGQDJ\views\pages\toners\defeitos.php"
$content4 = [System.IO.File]::ReadAllText($file4)
$content4 = $content4 -replace "`r`n", "`n"

if ($content4.Contains("canDelete")) {
    Write-Host "PATCH_B (View delete): ALREADY_APPLIED"
}
else {
    # Find the script section with the form submit listener
    $oldScriptSection = "document.getElementById('formDefeito').addEventListener"
    if ($content4.Contains($oldScriptSection)) {
        $insertBefore = $oldScriptSection
        $jsPermVars = @"
// Permissoes injetadas pelo PHP
const canDelete = <?php echo json_encode(!empty(`$canDelete)); ?>;

// Ocultar botoes de exclusao se sem permissao
if (!canDelete) {
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[onclick*="excluirDefeito"]').forEach(btn => {
      btn.closest('.group, td, div')?.querySelector('[onclick*="excluirDefeito"]')?.remove() || btn.remove();
    });
  });
}

"@
        $jsPermVars = $jsPermVars -replace "`r`n", "`n"
        $content4 = $content4.Replace($insertBefore, $jsPermVars + $insertBefore)
        $content4 = $content4 -replace "`n", "`r`n"
        [System.IO.File]::WriteAllText($file4, $content4)
        Write-Host "PATCH_B (View delete JS): APPLIED OK"
    }
    else {
        Write-Host "PATCH_B: FORM LISTENER NOT FOUND"
    }
}

Write-Host "`nDONE"
