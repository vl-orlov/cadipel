<?php
require __DIR__ . '/guard.php';

$customPromptPath = __DIR__ . '/../../api/custom_prompt.txt';
$customPromptText = is_file($customPromptPath) ? (string) file_get_contents($customPromptPath) : '';
?>
<div class="container-fluid abm_page">
    <div class="abm_card card shadow mb-4">
        <div class="card-header py-3">
            <h1 class="h5 mb-0">Instrucciones adicionales para el asistente IA</h1>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Este texto se agrega al final del prompt del sistema del asistente (junto con la
                información base del sitio) en cada conversación. Usalo para ajustar el tono,
                sumar avisos temporales o corregir algún comportamiento — sin tocar código.
            </p>
            <form id="promptForm">
                <div class="form-group">
                    <textarea id="promptTextarea" class="form-control" rows="14"
                        maxlength="20000"
                        placeholder="Ej: Durante esta semana mencioná que Cadipel participa de la feria X..."
                    ><?= htmlspecialchars($customPromptText, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div id="promptStatus" class="login_message" role="status"></div>
                <button type="submit" class="login_btn" style="width:auto; padding:10px 28px;">Guardar</button>
            </form>
        </div>
    </div>
</div>
<script src="js/prompt.js"></script>
