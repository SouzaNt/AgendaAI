<div class="mb-4">
    <h3 class="fw-bold mb-1"><i class="fa-solid fa-map-location-dot text-primary me-2"></i> Base Geográfica (Dicionário de Cadastro)</h3>
    <p class="text-muted mb-0">Gestão de Países, Estados, Municípios, Bairros e Tipos de Logradouro.</p>
</div>

<ul class="nav nav-pills mb-4 gap-2" id="geo-tabs">
    <li class="nav-item">
        <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-paises">Países</button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-estados">Estados (UF)</button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-municipios">Municípios</button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-bairros">Bairros</button>
    </li>
    <li class="nav-item">
        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-logradouros">Tipos & Logradouros</button>
    </li>
</ul>

<div class="tab-content">
    <!-- Paises -->
    <div class="tab-pane fade show active" id="tab-paises">
        <div class="card card-glass border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Países</h5>
                <button class="btn btn-sm btn-primary-custom rounded-pill" onclick="promptNovoPais()">+ Novo País</button>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($paises as $p): ?>
                <li class="list-group-item bg-transparent"><strong>#<?= $p['id'] ?></strong> - <?= htmlspecialchars($p['nome']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Estados -->
    <div class="tab-pane fade" id="tab-estados">
        <div class="card card-glass border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Estados</h5>
                <button class="btn btn-sm btn-primary-custom rounded-pill" onclick="promptNovoEstado()">+ Novo Estado</button>
            </div>
            <table class="table table-hover align-middle">
                <thead><tr><th>ID</th><th>Nome</th><th>UF</th></tr></thead>
                <tbody>
                    <?php foreach ($estados as $e): ?>
                    <tr><td>#<?= $e['id'] ?></td><td><?= htmlspecialchars($e['nome']) ?></td><td><span class="badge bg-secondary"><?= htmlspecialchars($e['uf']) ?></span></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Municipios -->
    <div class="tab-pane fade" id="tab-municipios">
        <div class="card card-glass border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Municípios</h5>
                <button class="btn btn-sm btn-primary-custom rounded-pill" onclick="promptNovoMunicipio()">+ Novo Município</button>
            </div>
            <table class="table table-hover align-middle">
                <thead><tr><th>ID</th><th>Nome</th><th>Estado (ID)</th></tr></thead>
                <tbody>
                    <?php foreach ($municipios as $m): ?>
                    <tr><td>#<?= $m['id'] ?></td><td><?= htmlspecialchars($m['nome']) ?></td><td>Estado #<?= $m['id_estado'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bairros -->
    <div class="tab-pane fade" id="tab-bairros">
        <div class="card card-glass border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Bairros</h5>
                <button class="btn btn-sm btn-primary-custom rounded-pill" onclick="promptNovoBairro()">+ Novo Bairro</button>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($bairros as $b): ?>
                <li class="list-group-item bg-transparent"><strong>#<?= $b['id'] ?></strong> - <?= htmlspecialchars($b['nome']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Logradouros -->
    <div class="tab-pane fade" id="tab-logradouros">
        <div class="card card-glass border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Tipos de Logradouro e Logradouros</h5>
                <button class="btn btn-sm btn-primary-custom rounded-pill" onclick="promptNovoLogradouro()">+ Novo Logradouro</button>
            </div>
            <table class="table table-hover align-middle">
                <thead><tr><th>ID</th><th>Nome Logradouro</th><th>Tipo</th></tr></thead>
                <tbody>
                    <?php foreach ($logradouros as $l): ?>
                    <tr>
                        <td>#<?= $l['id'] ?></td>
                        <td><?= htmlspecialchars($l['nome']) ?></td>
                        <td>
                            <?php 
                                $tp = JsonDatabase::findById('tipos_logradouro', $l['id_tipo_logradouro']);
                                echo htmlspecialchars($tp['nome'] ?? 'Rua') . ' (' . htmlspecialchars($tp['abreviacao'] ?? 'R.') . ')';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function promptNovoPais() {
    bootbox.prompt("Nome do País:", function(r){
        if (r) sendAjaxRequest(BASE_URL + '/api/localizacao/pais', { nome: r });
    });
}
function promptNovoEstado() {
    bootbox.prompt("Nome do Estado:", function(nome){
        if (nome) {
            bootbox.prompt("UF (Sigla 2 Letras):", function(uf){
                if (uf) sendAjaxRequest(BASE_URL + '/api/localizacao/estado', { nome: nome, uf: uf });
            });
        }
    });
}
function promptNovoMunicipio() {
    bootbox.prompt("Nome do Município:", function(nome){
        if (nome) sendAjaxRequest(BASE_URL + '/api/localizacao/municipio', { nome: nome, id_estado: 1 });
    });
}
function promptNovoBairro() {
    bootbox.prompt("Nome do Bairro:", function(r){
        if (r) sendAjaxRequest(BASE_URL + '/api/localizacao/bairro', { nome: r });
    });
}
function promptNovoLogradouro() {
    bootbox.prompt("Nome do Logradouro (ex: Paulista):", function(r){
        if (r) sendAjaxRequest(BASE_URL + '/api/localizacao/logradouro', { nome: r, id_tipo_logradouro: 2 });
    });
}
</script>
