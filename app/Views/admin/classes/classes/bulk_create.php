<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
.bulk-form-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.bulk-form-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    padding: 1.2rem 1.5rem;
}

.bulk-form-header h3 {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.bulk-form-body {
    padding: 1.8rem;
}

.info-box {
    background: rgba(59,127,232,0.05);
    border: 1px solid rgba(59,127,232,0.15);
    border-left: 3px solid var(--accent);
    border-radius: var(--radius-sm);
    padding: 1rem 1.2rem;
    margin-bottom: 1.8rem;
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
}

.info-box i {
    color: var(--accent);
    font-size: 1.2rem;
    margin-top: 0.1rem;
}

.info-box-content {
    flex: 1;
}

.info-box-title {
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.3rem;
}

.info-box-text {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0;
}

.form-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1.2rem;
    margin-bottom: 1.5rem;
}

.form-section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.form-section-title i {
    color: var(--accent);
    font-size: 0.8rem;
}

.form-label-custom {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.form-label-custom i {
    color: var(--accent);
    font-size: 0.7rem;
}

.form-control-custom, .form-select-custom {
    width: 100%;
    padding: 0.7rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-family: 'Sora', sans-serif;
    color: var(--text-primary);
    background: #fff;
    transition: all 0.2s;
}

.form-control-custom:focus, .form-select-custom:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.1);
    outline: none;
}

.levels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0.8rem;
    margin-top: 0.8rem;
}

.level-item {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.8rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    transition: all 0.2s;
    cursor: pointer;
}

.level-item:hover {
    border-color: var(--accent);
    background: rgba(59,127,232,0.02);
}

.level-item.selected {
    border-color: var(--accent);
    background: rgba(59,127,232,0.05);
    box-shadow: 0 2px 8px rgba(59,127,232,0.1);
}

.level-checkbox {
    width: 20px;
    height: 20px;
    accent-color: var(--accent);
    cursor: pointer;
}

.level-info {
    flex: 1;
}

.level-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
}

.level-code {
    font-size: 0.7rem;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
}

.level-badge {
    font-size: 0.6rem;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    background: var(--surface);
    color: var(--text-secondary);
}

.course-select {
    width: 140px;
    padding: 0.3rem 0.5rem;
    font-size: 0.8rem;
    border: 1px solid var(--border);
    border-radius: 4px;
}

.preview-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}

.preview-table th {
    background: var(--surface);
    color: var(--text-secondary);
    font-weight: 600;
    padding: 0.6rem 0.8rem;
    text-align: left;
    border-bottom: 1.5px solid var(--border);
}

.preview-table td {
    padding: 0.5rem 0.8rem;
    border-bottom: 1px solid var(--border);
}

.preview-table tr:hover td {
    background: rgba(59,127,232,0.02);
}

.preview-badge {
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
}

.summary-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.summary-item {
    text-align: center;
    flex: 1;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1.2;
}

.summary-label {
    font-size: 0.7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.btn-preview {
    background: var(--surface);
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    padding: 0.7rem 1.5rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-preview:hover {
    background: #fff;
    border-color: var(--accent);
    color: var(--accent);
}

.btn-create {
    background: var(--accent);
    border: none;
    color: #fff;
    padding: 0.7rem 2rem;
    border-radius: var(--radius-sm);
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-create:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,127,232,0.3);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-box {
    background: #fff;
    padding: 2rem;
    border-radius: var(--radius);
    text-align: center;
    box-shadow: var(--shadow-lg);
}

.loading-spinner {
    border: 3px solid var(--border);
    border-top: 3px solid var(--accent);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Novos estilos para os ciclos */
.cycle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.8rem;
    margin-bottom: 1.5rem;
}

.cycle-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem 0.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.cycle-card:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.cycle-card.selected {
    border-color: var(--accent);
    background: rgba(59,127,232,0.05);
    box-shadow: 0 4px 12px rgba(59,127,232,0.15);
}

.cycle-icon {
    font-size: 1.5rem;
    color: var(--accent);
    margin-bottom: 0.5rem;
}

.cycle-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.85rem;
}

.cycle-count {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-top: 0.2rem;
}

/* Prefix selector */
.prefix-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.prefix-selector select {
    flex: 1;
}

.prefix-selector input {
    width: 80px;
    text-align: center;
}

.prefix-options {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.prefix-tag {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 50px;
    padding: 0.3rem 1rem;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
}

.prefix-tag:hover {
    border-color: var(--accent);
    background: rgba(59,127,232,0.05);
}

.prefix-tag.selected {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}

/* Níveis container */
.levels-container {
    display: none;
    margin-top: 1rem;
}

.levels-container.active {
    display: block;
}

/* Responsive */
@media (max-width: 768px) {
    .cycle-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-layer-group me-2"></i>
            <?= $title ?>
        </h1>
        <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-outline-light">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Criação em Lote</li>
        </ol>
    </nav>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-box">
        <div class="loading-spinner"></div>
        <h6 style="color: var(--text-primary); margin-bottom: 0.5rem;">A criar turmas...</h6>
        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0;">Por favor, aguarde</p>
    </div>
</div>

<!-- Main Form Card -->
<div class="bulk-form-card">
    <div class="bulk-form-header">
        <h3>
            <i class="fas fa-magic"></i>
            Assistente de Criação em Lote
        </h3>
    </div>
    
    <div class="bulk-form-body">
        
        <!-- Info Box -->
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div class="info-box-content">
                <div class="info-box-title">Crie múltiplas turmas de uma só vez!</div>
                <p class="info-box-text">
                    Escolha o ciclo de ensino, selecione os níveis desejados e defina a numeração.
                    Para o 2º Ciclo e Ensino Médio, escolha também o curso.
                </p>
            </div>
        </div>
        
        <form action="<?= site_url('admin/classes/bulk-create/process') ?>" 
              method="post" 
              id="bulkForm"
              onsubmit="showLoading()">
            <?= csrf_field() ?>
            
            <!-- Step 1: Ano Letivo e Turno -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Passo 1: Ano Letivo e Turno
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-custom" for="academic_year_id">
                            <i class="fas fa-calendar"></i> Ano Letivo <span class="text-danger">*</span>
                        </label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select-custom" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>" <?= ($year->is_current ?? false) ? 'selected' : '' ?>>
                                    <?= $year->year_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label-custom" for="class_shift">
                            <i class="fas fa-clock"></i> Turno <span class="text-danger">*</span>
                        </label>
                        <select name="class_shift" id="class_shift" class="form-select-custom" required>
                            <option value="">Selecione...</option>
                            <option value="Manhã">Manhã</option>
                            <option value="Tarde">Tarde</option>
                            <option value="Noite">Noite</option>
                            <option value="Integral">Integral</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Ciclo de Ensino -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-school"></i>
                    Passo 2: Ciclo de Ensino <span class="text-danger">*</span>
                </div>
                
                <div class="cycle-grid" id="cycleGrid">
                    <?php 
                    $cycles = [
                        'Iniciação' => ['icon' => 'fa-baby', 'count' => 0],
                        'Primário' => ['icon' => 'fa-book', 'count' => 0],
                        '1º Ciclo' => ['icon' => 'fa-layer-group', 'count' => 0],
                        '2º Ciclo' => ['icon' => 'fa-graduation-cap', 'count' => 0],
                        'Ensino Médio' => ['icon' => 'fa-university', 'count' => 0]
                    ];
                    
                    // Contar níveis por ciclo
                    foreach ($gradeLevels as $level) {
                        if (isset($cycles[$level->education_level])) {
                            $cycles[$level->education_level]['count']++;
                        }
                    }
                    ?>
                    
                    <?php foreach ($cycles as $cycleName => $cycleData): ?>
                    <div class="cycle-card" data-cycle="<?= $cycleName ?>" onclick="selectCycle('<?= $cycleName ?>')">
                        <div class="cycle-icon">
                            <i class="fas <?= $cycleData['icon'] ?>"></i>
                        </div>
                        <div class="cycle-name"><?= $cycleName ?></div>
                        <div class="cycle-count"><?= $cycleData['count'] ?> níveis</div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <input type="hidden" name="selected_cycle" id="selected_cycle" value="">
            </div>
            
            <!-- Step 3: Níveis de Ensino (dinâmico por ciclo) -->
            <?php foreach ($cycles as $cycleName => $cycleData): ?>
            <div class="form-section levels-container" id="levels_<?= str_replace(' ', '_', $cycleName) ?>" data-cycle="<?= $cycleName ?>">
                <div class="form-section-title">
                    <i class="fas fa-layer-group"></i>
                    Passo 3: Níveis de <?= $cycleName ?>
                </div>
                
                <div class="levels-grid">
                    <?php 
                    $cycleLevels = array_filter($gradeLevels, function($level) use ($cycleName) {
                        return $level->education_level == $cycleName;
                    });
                    
                    foreach ($cycleLevels as $level): 
                    ?>
                    <label class="level-item" for="level_<?= $level->id ?>_<?= str_replace(' ', '_', $cycleName) ?>">
                        <input type="checkbox" 
                               class="level-checkbox cycle-<?= str_replace(' ', '_', $cycleName) ?>" 
                               name="grade_level_ids[]" 
                               value="<?= $level->id ?>"
                               id="level_<?= $level->id ?>_<?= str_replace(' ', '_', $cycleName) ?>"
                               data-education="<?= $level->education_level ?>"
                               data-level-name="<?= $level->level_name ?>"
                               data-grade-number="<?= $level->grade_number ?>"
                               onchange="updatePreview()">
                        <div class="level-info">
                            <div class="level-name"><?= $level->level_name ?></div>
                            <div>
                                <span class="level-code"><?= $level->level_code ?></span>
                            </div>
                        </div>
                        
                        <!-- Course selector for 2º Ciclo and Ensino Médio -->
                        <?php if (in_array($level->education_level, ['2º Ciclo', 'Ensino Médio'])): ?>
                        <div class="course-select-container" style="display: none;" id="course_container_<?= $level->id ?>">
                            <select name="course_ids[<?= $level->id ?>]" class="course-select" onchange="updatePreview()">
                                <option value="">Escolher curso...</option>
                                <?php 
                                // Filtrar cursos que incluem este nível
                                foreach ($courses as $course): 
                                    if ($course->start_grade_id <= $level->id && $course->end_grade_id >= $course->id):
                                ?>
                                    <option value="<?= $course->id ?>"><?= $course->course_name ?></option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Step 4: Numeração e Prefixos -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-tag"></i>
                    Passo 4: Numeração e Prefixos
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label-custom" for="start_number">
                            <i class="fas fa-play"></i> Número Inicial <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control-custom" 
                               id="start_number" 
                               name="start_number" 
                               value="1" 
                               min="1" 
                               max="50"
                               required
                               onchange="updatePreview()">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label-custom" for="end_number">
                            <i class="fas fa-stop"></i> Número Final <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control-custom" 
                               id="end_number" 
                               name="end_number" 
                               value="5" 
                               min="1" 
                               max="50"
                               required
                               onchange="updatePreview()">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label-custom" for="class_prefix">
                            <i class="fas fa-heading"></i> Prefixo da Turma
                        </label>
                        <div class="prefix-selector">
                            <select class="form-select-custom" id="class_prefix_select" onchange="syncPrefix()">
                                <option value="Turma">Turma</option>
                                <option value="Classe">Classe</option>
                                <option value="Sala">Sala</option>
                                <option value="Grupo">Grupo</option>
                                <option value="T">T</option>
                                <option value="custom">Personalizado...</option>
                            </select>
                            <input type="text" 
                                   class="form-control-custom" 
                                   id="class_prefix_custom" 
                                   name="class_prefix" 
                                   value="Turma"
                                   placeholder="Personalizado"
                                   style="display: none;"
                                   oninput="updatePreview()">
                        </div>
                        
                        <div class="prefix-options mt-2">
                            <span class="prefix-tag selected" onclick="setPrefix('Turma')">Turma A</span>
                            <span class="prefix-tag" onclick="setPrefix('Classe')">Classe 1</span>
                            <span class="prefix-tag" onclick="setPrefix('Sala')">Sala 101</span>
                            <span class="prefix-tag" onclick="setPrefix('T')">T1</span>
                        </div>
                        <small class="text-muted">Exemplo: <span id="prefixExample">Turma 1, Turma 2...</span></small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label-custom" for="room_prefix">
                            <i class="fas fa-door-open"></i> Prefixo da Sala
                        </label>
                        <select class="form-select-custom" id="room_prefix" name="room_prefix" onchange="updatePreview()">
                            <option value="Sala">Sala</option>
                            <option value="Laboratório">Laboratório</option>
                            <option value="Anfiteatro">Anfiteatro</option>
                            <option value="Bloco">Bloco</option>
                            <option value="Pavilhão">Pavilhão</option>
                            <option value="custom-room">Personalizado...</option>
                        </select>
                        <input type="text" 
                               class="form-control-custom mt-2" 
                               id="room_prefix_custom" 
                               name="room_prefix_custom" 
                               placeholder="Digite o prefixo personalizado"
                               style="display: none;"
                               oninput="updatePreview()">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label-custom" for="capacity">
                            <i class="fas fa-users"></i> Capacidade
                        </label>
                        <input type="number" 
                               class="form-control-custom" 
                               id="capacity" 
                               name="capacity" 
                               value="30" 
                               min="1" 
                               max="100"
                               onchange="updatePreview()">
                    </div>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="form-section" id="previewSection" style="display: none;">
                <div class="form-section-title">
                    <i class="fas fa-eye"></i>
                    Pré-visualização
                </div>
                
                <div class="summary-box mb-3">
                    <div class="summary-item">
                        <div class="summary-value" id="previewCount">0</div>
                        <div class="summary-label">Total de Turmas</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="previewLevels">0</div>
                        <div class="summary-label">Níveis</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-value" id="previewShift">-</div>
                        <div class="summary-label">Turno</div>
                    </div>
                </div>
                
                <div style="max-height: 300px; overflow-y: auto;">
                    <table class="preview-table">
                        <thead>
                            <tr>
                                <th>Nível</th>
                                <th>Curso</th>
                                <th>Nome</th>
                                <th>Código</th>
                                <th>Sala</th>
                            </tr>
                        </thead>
                        <tbody id="previewBody"></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn-preview" onclick="updatePreview()">
                    <i class="fas fa-eye"></i> Pré-visualizar
                </button>
                
                <div>
                    <span class="text-muted me-3" id="totalInfo"></span>
                    <button type="submit" class="btn-create" id="submitBtn">
                        <i class="fas fa-magic"></i> Criar Turmas em Lote
                    </button>
                </div>
            </div>
            
        </form>
    </div>
</div>

<script>
// Variáveis globais
let previewData = [];
let selectedCycle = '';

// Selecionar ciclo
function selectCycle(cycle) {
    // Atualizar visual dos cards
    document.querySelectorAll('.cycle-card').forEach(card => {
        card.classList.remove('selected');
        if (card.dataset.cycle === cycle) {
            card.classList.add('selected');
        }
    });
    
    // Esconder todos os containers de níveis
    document.querySelectorAll('.levels-container').forEach(container => {
        container.classList.remove('active');
    });
    
    // Mostrar container do ciclo selecionado
    const containerId = 'levels_' + cycle.replace(/ /g, '_');
    const container = document.getElementById(containerId);
    if (container) {
        container.classList.add('active');
    }
    
    selectedCycle = cycle;
    document.getElementById('selected_cycle').value = cycle;
    
    // Limpar seleções anteriores de outros ciclos
    document.querySelectorAll('.level-checkbox').forEach(cb => {
        if (!cb.closest('.levels-container.active')) {
            cb.checked = false;
        }
    });
    
    updatePreview();
}

// Mostrar/esconder selects de curso quando checkbox é marcado
document.querySelectorAll('.level-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const levelItem = this.closest('.level-item');
        const courseContainer = levelItem.querySelector('.course-select-container');
        if (courseContainer) {
            courseContainer.style.display = this.checked ? 'inline-block' : 'none';
        }
        updatePreview();
    });
});

// Sincronizar prefixo personalizado
function syncPrefix() {
    const select = document.getElementById('class_prefix_select');
    const customInput = document.getElementById('class_prefix_custom');
    
    if (select.value === 'custom') {
        customInput.style.display = 'block';
        customInput.value = '';
        customInput.focus();
    } else {
        customInput.style.display = 'none';
        customInput.value = select.value;
        updatePreview();
    }
}

// Definir prefixo rápido
function setPrefix(prefix) {
    document.getElementById('class_prefix_select').value = prefix;
    document.getElementById('class_prefix_custom').value = prefix;
    document.getElementById('class_prefix_custom').style.display = 'none';
    
    // Atualizar tags visuais
    document.querySelectorAll('.prefix-tag').forEach(tag => {
        tag.classList.remove('selected');
        if (tag.textContent.includes(prefix)) {
            tag.classList.add('selected');
        }
    });
    
    updatePreview();
}

// Gerenciar sala personalizada
document.getElementById('room_prefix').addEventListener('change', function() {
    const customInput = document.getElementById('room_prefix_custom');
    if (this.value === 'custom-room') {
        customInput.style.display = 'block';
        customInput.value = '';
    } else {
        customInput.style.display = 'none';
        updatePreview();
    }
});

// Update preview
function updatePreview() {
    const start = parseInt(document.getElementById('start_number').value) || 1;
    const end = parseInt(document.getElementById('end_number').value) || 1;
    const shift = document.getElementById('class_shift').value;
    
    // Obter prefixo (personalizado ou selecionado)
    let prefix = document.getElementById('class_prefix_custom').value;
    if (!prefix) {
        prefix = document.getElementById('class_prefix_select').value;
        if (prefix === 'custom') prefix = 'Turma';
    }
    
    // Obter prefixo da sala
    let roomPrefix = document.getElementById('room_prefix').value;
    if (roomPrefix === 'custom-room') {
        roomPrefix = document.getElementById('room_prefix_custom').value || 'Sala';
    }
    
    // Obter níveis selecionados
    const selectedLevels = [];
    document.querySelectorAll('.level-checkbox:checked').forEach(cb => {
        const levelItem = cb.closest('.level-item');
        const levelName = cb.dataset.levelName || levelItem.querySelector('.level-name').textContent;
        const education = cb.dataset.education;
        const gradeNumber = cb.dataset.gradeNumber;
        
        let courseName = 'Ensino Geral';
        let courseId = null;
        
        // Para 2º Ciclo e Ensino Médio, verificar curso selecionado
        if (education === '2º Ciclo' || education === 'Ensino Médio') {
            const courseSelect = document.querySelector(`select[name="course_ids[${cb.value}]"]`);
            if (courseSelect && courseSelect.value) {
                courseName = courseSelect.options[courseSelect.selectedIndex].text;
                courseId = courseSelect.value;
            } else {
                courseName = '⚠️ Curso não selecionado';
            }
        }
        
        selectedLevels.push({
            id: cb.value,
            name: levelName,
            education: education,
            gradeNumber: gradeNumber,
            course: courseName,
            courseId: courseId
        });
    });
    
    // Gerar preview
    previewData = [];
    const tbody = document.getElementById('previewBody');
    tbody.innerHTML = '';
    
    selectedLevels.forEach(level => {
        for (let i = start; i <= end; i++) {
            // Nome da turma com formatação inteligente
            let className;
            if (prefix.toLowerCase() === 'turma' || prefix.toLowerCase() === 'classe') {
                // Turma A, Turma B... para numeração pequena
                if (i <= 26) {
                    const letra = String.fromCharCode(64 + i); // A=1, B=2...
                    className = `${prefix} ${letra}`;
                } else {
                    className = `${prefix} ${i}`;
                }
            } else {
                className = `${prefix} ${i}`;
            }
            
            // Gerar código
            const shiftCode = shift ? shift.substring(0,1) : 'X';
            let classCode;
            if (level.courseId) {
                // Para Ensino Médio, incluir código do curso
                classCode = `EM-${level.id}-${shiftCode}-${i}`;
            } else {
                classCode = `EG-${level.id}-${shiftCode}-${i}`;
            }
            
            const room = `${roomPrefix} ${i}`;
            
            previewData.push({
                level: level.name,
                course: level.course,
                name: className,
                code: classCode,
                room: room,
                hasError: level.course.includes('⚠️')
            });
            
            const row = tbody.insertRow();
            row.style.backgroundColor = level.course.includes('⚠️') ? 'rgba(232,70,70,0.05)' : '';
            row.innerHTML = `
                <td>${level.name}</td>
                <td><span class="preview-badge" style="${level.course.includes('⚠️') ? 'background: rgba(232,70,70,0.1); color: var(--danger);' : ''}">${level.course}</span></td>
                <td>${className}</td>
                <td><code>${classCode}</code></td>
                <td>${room}</td>
            `;
        }
    });
    
    // Atualizar summary
    document.getElementById('previewCount').textContent = previewData.length;
    document.getElementById('previewLevels').textContent = selectedLevels.length;
    document.getElementById('previewShift').textContent = shift || 'Não definido';
    
    const hasErrors = previewData.some(item => item.hasError);
    const totalInfo = document.getElementById('totalInfo');
    totalInfo.textContent = `${previewData.length} turma(s) a criar`;
    totalInfo.style.color = hasErrors ? 'var(--danger)' : '';
    
    document.getElementById('previewSection').style.display = previewData.length > 0 ? 'block' : 'none';
    document.getElementById('submitBtn').disabled = previewData.length === 0 || hasErrors;
    
    // Atualizar exemplo de prefixo
    document.getElementById('prefixExample').textContent = `${prefix} 1, ${prefix} 2...`;
}

// Show loading overlay
function showLoading() {
    if (previewData.length === 0) {
        alert('Nenhuma turma para criar. Faça a pré-visualização primeiro.');
        return false;
    }
    
    if (previewData.some(item => item.hasError)) {
        alert('Existem níveis sem curso selecionado. Por favor, selecione os cursos antes de continuar.');
        return false;
    }
    
    document.getElementById('loadingOverlay').style.display = 'flex';
    return true;
}

// Add event listeners
document.getElementById('class_shift').addEventListener('change', updatePreview);
document.getElementById('start_number').addEventListener('change', updatePreview);
document.getElementById('end_number').addEventListener('change', updatePreview);
document.getElementById('capacity').addEventListener('change', updatePreview);
document.getElementById('class_prefix_custom').addEventListener('input', updatePreview);
document.getElementById('room_prefix_custom').addEventListener('input', updatePreview);

// Initialize
updatePreview();
</script>

<?= $this->endSection() ?>