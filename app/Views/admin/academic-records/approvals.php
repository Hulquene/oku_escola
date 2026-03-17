<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-check-double me-2"></i>Processar Aprovações - <?= $class['class_name'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records') ?>">Pautas</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic-records/class/' . $class['id']) ?>"><?= $class['class_name'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aprovações</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn success" onclick="saveApprovals()">
                <i class="fas fa-save me-1"></i> Salvar Aprovações
            </button>
            <a href="<?= site_url('admin/academic-records/class/' . $class['id']) ?>" class="hdr-btn info">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma -->
<div class="ci-card mb-4">
    <div class="ci-card-header" style="background: var(--primary);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-info-circle me-2"></i>
            <span>Informações da Turma</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="info-label">Ano Letivo</div>
                <div class="info-value fw-semibold"><?= $class['year_name'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Turma</div>
                <div class="info-value fw-semibold"><?= $class['class_name'] ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Curso</div>
                <div class="info-value fw-semibold"><?= $class['course_name'] ?? 'Ensino Geral' ?></div>
            </div>
            <div class="col-md-3">
                <div class="info-label">Nível</div>
                <div class="info-value fw-semibold"><?= $class['level_name'] ?></div>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="info-label">Professor</div>
                <div class="info-value fw-semibold">
                    <?= ($class['teacher_first_name'] ?? 'Não') . ' ' . ($class['teacher_last_name'] ?? 'atribuído') ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-label">Turno</div>
                <div class="info-value fw-semibold"><?= $class['class_shift'] ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="stat-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Total de Alunos</div>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Transitam</div>
            <div class="stat-value"><?= $stats['transitam'] ?></div>
            <div class="stat-sub"><?= $stats['taxa_aprovacao'] ?>%</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Não Transitam</div>
            <div class="stat-value"><?= $stats['nao_transitam'] ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-question-circle"></i>
        </div>
        <div>
            <div class="stat-label">Pendentes</div>
            <div class="stat-value"><?= $stats['pendentes'] ?></div>
        </div>
    </div>
</div>

<!-- Tabela de Alunos para Aprovação -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-table me-2"></i>
            <span>Resultados Finais por Aluno</span>
        </div>
        <span class="badge-ci info"><?= count($students) ?> alunos</span>
    </div>
    
    <div class="ci-card-body p0">
        <div class="table-responsive">
            <table class="ci-table" id="approvalsTable" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Aluno</th>
                        <th class="center">Nº Processo</th>
                        <?php foreach ($disciplines as $disc): ?>
                            <th class="center" title="<?= $disc['discipline_name'] ?>"><?= $disc['discipline_code'] ?></th>
                        <?php endforeach; ?>
                        <th class="center">Média Final</th>
                        <th class="center">Resultado</th>
                        <th class="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="center"><?= $counter++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem; background: var(--accent);">
                                            <?= strtoupper(substr($student['full_name'] ?? '', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= $student['full_name'] ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="center"><span class="code-badge"><?= $student['student_number'] ?></span></td>
                                
                                <?php foreach ($disciplines as $disc): ?>
                                    <?php 
                                    $nota = $student['disciplinas'][$disc['id']]['mfd'] ?? '—';
                                    $notaClass = $nota !== '—' ? ($nota >= 10 ? 'success' : ($nota >= 7 ? 'warning' : 'danger')) : '';
                                    ?>
                                    <td class="center">
                                        <?php if ($nota !== '—'): ?>
                                            <span class="num-chip <?= $notaClass ?>"><?= $nota ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                
                                <td class="center">
                                    <span class="num-chip <?= $student['media_final_geral'] >= 10 ? 'success' : ($student['media_final_geral'] >= 7 ? 'warning' : 'danger') ?>">
                                        <?= number_format($student['media_final_geral'], 1) ?>
                                    </span>
                                </td>
                                
                                <td class="center">
                                    <select class="filter-select resultado-select" 
                                            data-enrollment="<?= $student['enrollment_id'] ?>"
                                            data-media="<?= $student['media_final_geral'] ?>">
                                        <option value="Transita" <?= $student['resultado_final'] == 'Transita' ? 'selected' : '' ?>>Transita</option>
                                        <option value="Não Transita" <?= $student['resultado_final'] == 'Não Transita' ? 'selected' : '' ?>>Não Transita</option>
                                        <option value="Recurso" <?= $student['resultado_final'] == 'Recurso' ? 'selected' : '' ?>>Recurso</option>
                                        <option value="Pendente" <?= $student['resultado_final'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                    </select>
                                </td>
                                
                                <td class="center">
                                    <?php if ($student['ja_aprovado']): ?>
                                        <span class="badge-ci success">Aprovado</span>
                                    <?php else: ?>
                                        <span class="badge-ci warning">Pendente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= 4 + count($disciplines) ?>" class="center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="text-muted">Nenhum aluno encontrado.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="ci-card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge-ci info">
                    <i class="fas fa-info-circle me-1"></i>
                    Alunos com <strong>Transita</strong> serão promovidos para o próximo ano letivo.
                </span>
            </div>
            <div>
                <button type="button" class="btn-ci primary" onclick="saveApprovals()">
                    <i class="fas fa-save me-2"></i> Salvar Aprovações
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Função para salvar aprovações
function saveApprovals() {
    // Coletar dados
    let approvals = {};
    let hasChanges = false;
    
    document.querySelectorAll('.resultado-select').forEach(select => {
        let enrollmentId = select.dataset.enrollment;
        let resultado = select.value;
        let mediaFinal = select.dataset.media;
        
        approvals[enrollmentId] = {
            resultado: resultado,
            media_final: mediaFinal
        };
        
        // Verificar se houve mudança (opcional)
        if (select.value !== select.defaultValue) {
            hasChanges = true;
        }
    });
    
    if (!hasChanges) {
        Swal.fire({
            icon: 'info',
            title: 'Sem alterações',
            text: 'Nenhuma alteração foi feita nas aprovações.',
            confirmButtonColor: 'var(--accent)'
        });
        return;
    }
    
    // Confirmar salvamento
    Swal.fire({
        title: 'Confirmar Aprovações',
        text: 'Deseja salvar as aprovações dos alunos? Esta ação pode afetar a progressão para o próximo ano letivo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--success)',
        cancelButtonColor: 'var(--border)',
        confirmButtonText: '<i class="fas fa-save me-2"></i>Sim, salvar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            processApprovals(approvals);
        }
    });
}

// Processar aprovações via AJAX
function processApprovals(approvals) {
    const formData = new FormData();
    formData.append('class_id', '<?= $class['id'] ?>');
    formData.append('approvals', JSON.stringify(approvals));
    
    fetch('<?= site_url('admin/academic-records/save-approvals') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message,
                confirmButtonColor: 'var(--success)'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message,
                confirmButtonColor: 'var(--danger)'
            });
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao processar requisição.',
            confirmButtonColor: 'var(--danger)'
        });
    });
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    margin-bottom: 0.15rem;
}

.info-value {
    font-size: 0.9rem;
    color: var(--text-primary);
}

.teacher-initials {
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    flex-shrink: 0;
}

.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(59,127,232,0.1);
    color: var(--accent);
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    display: inline-block;
}

.num-chip {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    display: inline-block;
}

.num-chip.success {
    background: rgba(22,168,125,0.15);
    color: var(--success);
}

.num-chip.warning {
    background: rgba(232,160,32,0.15);
    color: var(--warning);
}

.num-chip.danger {
    background: rgba(232,70,70,0.15);
    color: var(--danger);
}

/* Select personalizado */
.resultado-select {
    min-width: 120px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-primary);
}

.resultado-select:focus {
    border-color: var(--accent);
    outline: none;
}

/* Animações */
.ci-card {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsividade */
@media (max-width: 768px) {
    .info-label, .info-value {
        margin-bottom: 0.5rem;
    }
    
    .resultado-select {
        min-width: 100px;
    }
}
</style>
<?= $this->endSection() ?>