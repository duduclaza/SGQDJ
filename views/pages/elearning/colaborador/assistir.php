<div class="flex-1 flex flex-col bg-black">
    <div class="flex-1 relative">
        <?php if ($material['tipo'] === 'video'): ?>
            <div class="w-full h-full flex items-center justify-center bg-black">
                <?php if ($material['storage_type'] === 'google_drive'): ?>
                    <iframe src="https://drive.google.com/file/d/<?= $material['drive_id'] ?>/preview" class="w-full h-full" allow="autoplay"></iframe>
                <?php else: ?>
                    <video id="videoPlayer" class="max-h-full w-full" controls controlsList="nodownload" oncontextmenu="return false;">
                        <source src="<?= $material['arquivo_path'] ?>" type="video/mp4">
                        Seu navegador não suporta vídeos.
                    </video>
                <?php endif; ?>
            </div>
        <?php elseif ($material['tipo'] === 'pdf'): ?>
            <div class="w-full h-full bg-gray-800">
                <?php if ($material['storage_type'] === 'google_drive'): ?>
                    <iframe src="https://drive.google.com/file/d/<?= $material['drive_id'] ?>/preview" class="w-full h-full" frameborder="0"></iframe>
                <?php else: ?>
                    <iframe src="<?= $material['arquivo_path'] ?>#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
                <?php endif; ?>
            </div>
        <?php elseif ($material['tipo'] === 'imagem'): ?>
            <div class="w-full h-full flex items-center justify-center bg-gray-900 p-4">
                <?php if ($material['storage_type'] === 'google_drive'): ?>
                    <img src="https://lh3.googleusercontent.com/d/<?= $material['drive_id'] ?>" class="max-h-full max-w-full object-contain shadow-2xl" alt="<?= $material['titulo'] ?>">
                <?php else: ?>
                    <img src="<?= $material['arquivo_path'] ?>" class="max-h-full max-w-full object-contain shadow-2xl" alt="<?= $material['titulo'] ?>">
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="w-full h-full bg-white overflow-y-auto p-8 sm:p-12">
                <article class="prose prose-blue max-w-4xl mx-auto">
                    <h1 class="text-3xl font-bold text-gray-900 mb-8 border-b pb-4"><?= $material['titulo'] ?></h1>
                    <div class="text-gray-700 leading-relaxed text-lg">
                        <?= $material['conteudo_texto'] ?>
                    </div>
                </article>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Action Bar -->
    <div class="bg-white border-t border-gray-200 p-4 flex justify-between items-center z-20">
        <div class="flex items-center space-x-4">
            <button onclick="markComplete(<?= $material['id'] ?>)" 
                    id="btnComplete"
                    class="flex items-center space-x-2 px-6 py-2 rounded-lg <?= ($material['progresso']['visualizado'] ?? 0) == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-emerald-600 text-white hover:bg-emerald-700' ?> transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><?= ($material['progresso']['visualizado'] ?? 0) == 1 ? 'Concluído' : 'Marcar como Concluído' ?></span>
            </button>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Navigation could be added here -->
        </div>
    </div>
</div>

<script>
    function markComplete(materialId) {
        const btn = document.getElementById('btnComplete');
        if (btn.classList.contains('bg-emerald-100')) return; // Already complete

        const formData = new FormData();
        formData.append('id_material', materialId);
        formData.append('pct', 100);

        fetch('/elearning/registrar-progresso', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                btn.classList.remove('bg-emerald-600', 'text-white', 'hover:bg-emerald-700');
                btn.classList.add('bg-emerald-100', 'text-emerald-700');
                btn.querySelector('span').textContent = 'Concluído';
                // Optional: refresh page or update progress bar in parent
                window.location.reload();
            }
        });
    }

    <?php if ($material['tipo'] === 'video'): ?>
    const video = document.getElementById('videoPlayer');
    let lastUpdate = 0;

    video.ontimeupdate = function() {
        const pct = (video.currentTime / video.duration) * 100;
        // Update progress every 5 seconds
        if (video.currentTime - lastUpdate > 5) {
            lastUpdate = video.currentTime;
            updateProgress(<?= $material['id'] ?>, pct);
        }
    };

    video.onended = function() {
        updateProgress(<?= $material['id'] ?>, 100);
    };

    function updateProgress(materialId, pct) {
        const formData = new FormData();
        formData.append('id_material', materialId);
        formData.append('pct', pct);

        fetch('/elearning/registrar-progresso', {
            method: 'POST',
            body: formData
        });
    }
    <?php endif; ?>
</script>
