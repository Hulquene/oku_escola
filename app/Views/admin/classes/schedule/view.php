<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary:       #1B2B4B;
    --primary-light: #243761;
    --accent:        #3B7FE8;
    --accent-hover:  #2C6FD4;
    --success:       #16A87D;
    --danger:        #E84646;
    --warning:       #E8A020;
    --surface:       #F5F7FC;
    --surface-card:  #FFFFFF;
    --border:        #E2E8F4;
    --text-primary:  #1A2238;
    --text-secondary:#6B7A99;
    --text-muted:    #9AA5BE;
    --shadow-sm:     0 1px 4px rgba(27,43,75,.07);
    --shadow-md:     0 4px 16px rgba(27,43,75,.10);
    --shadow-lg:     0 8px 32px rgba(27,43,75,.14);
    --radius:        12px;
    --radius-sm:     8px;
}

* { font-family: 'Sora', sans-serif; }
body { background: var(--surface); }

/* Page Header */
.ci-page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
    border-radius: var(--radius);
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-lg);
}

.ci-page-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
}

.ci-page-header .breadcrumb-item a {
    color: rgba(255,255,255,.6);
    text-decoration: none;
}

/* Info Card */
.info-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
    box-shadow: var(--shadow-sm);
}

.class-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(59,127,232,.1);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
}

.class-details {
    flex: 1;
}

.class-details h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: .5rem;
}

.class-meta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    color: var(--text-secondary);
    font-size: .9rem;
}

.meta-item i {
    color: var(--accent);
    width: 20px;
}

/* Action Buttons */
.action-bar {
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.2rem;
    border-radius: var(--radius-sm);
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 3px 10px rgba(59,127,232,.28);
}

.btn-primary:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
}

.btn-success {
    background: var(--success);
    color: #fff;
}

.btn-success:hover {
    background: #12906B;
    transform: translateY(-1px);
}

.btn-warning {
    background: var(--warning);
    color: #fff;
}

.btn-warning:hover {
    background: #d18c1c;
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--surface);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
}

.btn-secondary:hover {
    border-color: var(--accent);
    color: var(--accent);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.stat-icon.blue { background: rgba(59,127,232,.1); color: var(--accent); }
.stat-icon.green { background: rgba(22,168,125,.1); color: var(--success); }
.stat-icon.orange { background: rgba(232,160,32,.1); color: var(--warning); }

.stat-info {
    flex: 1;
}

.stat-label {
    font-size: .7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: .25rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    line-height: 1.2;
}

.stat-sub {
    font-size: .7rem;
    color: var(--text-muted);
}

/* Schedule Table */
.schedule-container {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}

.schedule-header h5 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.schedule-header h5 i {
    color: var(--accent);
}

.schedule-actions {
    display: flex;
    gap: .5rem;
}

.btn-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: #fff;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all .2s;
    cursor: pointer;
}

.btn-icon:hover {
    color: #fff;
    border-color: transparent;
    transform: translateY(-1px);
}

.btn-icon.edit:hover { background: var(--accent); }
.btn-icon.print:hover { background: var(--success); }
.btn-icon.pdf:hover { background: var(--warning); }
.btn-icon.excel:hover { background: #217346; }
.btn-icon.add:hover { background: var(--success); }
.btn-icon.configure:hover { background: var(--primary); }

.schedule-table {
    width: 100%;
    border-collapse: collapse;
}

.schedule-table th {
    background: var(--surface);
    color: var(--text-secondary);
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 1rem;
    text-align: center;
    border-bottom: 2px solid var(--border);
}

.schedule-table td {
    border: 1px solid var(--border);
    padding: .75rem;
    vertical-align: top;
    min-height: 100px;
    transition: background .2s;
}

.schedule-table td:hover {
    background: rgba(59,127,232,.02);
}

.time-column {
    background: var(--surface);
    font-weight: 600;
    color: var(--text-primary);
    width: 120px;
    text-align: center;
}

.time-column strong {
    display: block;
    font-size: .9rem;
}

.time-column small {
    font-size: .7rem;
    color: var(--text-muted);
}

.schedule-cell {
    min-height: 80px;
    position: relative;
}

.schedule-item {
    background: rgba(59,127,232,.05);
    border-left: 3px solid var(--accent);
    padding: .5rem;
    border-radius: 4px;
    margin-bottom: .5rem;
    position: relative;
    transition: all .2s;
    cursor: pointer;
}

.schedule-item:hover {
    background: rgba(59,127,232,.1);
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.schedule-item:last-child {
    margin-bottom: 0;
}

.schedule-item .discipline-name {
    font-weight: 600;
    font-size: .85rem;
    color: var(--text-primary);
    display: block;
    margin-bottom: .2rem;
    padding-right: 20px;
}

.schedule-item .teacher-name {
    font-size: .7rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: .25rem;
}

.schedule-item .teacher-name i {
    font-size: .6rem;
    color: var(--accent);
}

.schedule-item .room-badge {
    font-size: .65rem;
    background: rgba(255,255,255,.5);
    padding: .1rem .3rem;
    border-radius: 3px;
    display: inline-block;
    margin-top: .25rem;
}

.schedule-item-actions {
    position: absolute;
    top: .25rem;
    right: .25rem;
    display: flex;
    gap: .2rem;
    opacity: 0;
    transition: opacity .2s;
    z-index: 100;
}

.schedule-item:hover .schedule-item-actions {
    opacity: 1;
}

.schedule-item-actions .btn-sm {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: none;
    background: #fff;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    cursor: pointer;
    transition: all .2s;
    pointer-events: auto;
    position: relative;
    z-index: 101;
}

.schedule-item-actions .btn-sm:hover {
    color: #fff;
    transform: scale(1.1);
}

.schedule-item-actions .btn-sm.edit-sm:hover { background: var(--accent); }
.schedule-item-actions .btn-sm.delete-sm:hover { background: var(--danger); }

.empty-cell {
    color: var(--text-muted);
    text-align: center;
    font-size: .7rem;
    padding: 1rem;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-schedule-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: .5rem;
    border: 1px dashed var(--border);
    border-radius: 4px;
    background: transparent;
    color: var(--text-muted);
    font-size: .7rem;
    cursor: pointer;
    transition: all .2s;
}

.add-schedule-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(59,127,232,.05);
}

/* Quick Add Form */
.quick-form-container {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
    display: none;
}

.quick-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.quick-form-header h6 {
    font-size: .95rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.quick-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.quick-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1rem;
}

/* Legend */
.legend {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.legend-items {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .8rem;
    color: var(--text-secondary);
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
}

.legend-color.assigned { background: rgba(22,168,125,.2); border-left: 3px solid var(--success); }
.legend-color.pending { background: rgba(232,160,32,.2); border-left: 3px solid var(--warning); }

/* Modal */
.modal-custom .modal-content {
    border-radius: var(--radius);
    border: none;
    overflow: hidden;
}

.modal-custom .modal-header {
    background: var(--primary);
    color: white;
    border: none;
    padding: 1rem 1.5rem;
}

.modal-custom .modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.modal-custom .modal-body {
    padding: 1.5rem;
}

.modal-custom .modal-footer {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
}

/* Responsive */
@media print {
    .action-bar,
    .schedule-actions,
    .btn-icon,
    .schedule-item-actions,
    .quick-form-container,
    .add-schedule-btn,
    .legend .btn {
        display: none !important;
    }
    
    .schedule-table {
        border: 1px solid #000;
    }
    
    .schedule-table th {
        background: #f0f0f0 !important;
        color: #000 !important;
    }
}

@media(max-width: 768px) {
    .schedule-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .schedule-table td {
        min-width: 150px;
    }
    
    .quick-form-grid {
        grid-template-columns: 1fr;
    }
    
    .legend {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 4rem;
    opacity: .2;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: var(--text-primary);
    margin-bottom: .5rem;
}

.empty-state p {
    margin-bottom: 1.5rem;
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-calendar-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/schedule') ?>">Horários</a></li>
                    <li class="breadcrumb-item active"><?= $class['class_name'] ?></li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/classes/schedule') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Voltar
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Class Info Card -->
<div class="info-card">
    <div class="class-icon">
        <i class="fas fa-school"></i>
    </div>
    <div class="class-details">
        <h2><?= $class['class_name'] ?> <small class="text-muted">(<?= $class['class_code'] ?>)</small></h2>
        <div class="class-meta">
            <span class="meta-item">
                <i class="fas fa-layer-group"></i> <?= $class->level_name ?>
            </span>
            <span class="meta-item">
                <i class="fas fa-calendar"></i> <?= $class['year_name'] ?>
            </span>
            <span class="meta-item">
                <i class="fas fa-clock"></i> <?= $class['class_shift'] ?>
            </span>
            <span class="meta-item">
                <i class="fas fa-door-open"></i> Sala: <?= $class->class_room ?: 'Não definida' ?>
            </span>
            <span class="meta-item">
                <i class="fas fa-user-tie"></i> Diretor: <?= $class->teacher_name ?: 'Não atribuído' ?>
            </span>
        </div>
    </div>
</div>

<!-- Action Bar -->
<div class="action-bar">
    <button class="btn-action btn-primary" onclick="toggleQuickForm()">
        <i class="fas fa-plus-circle"></i> Adicionar Horário
    </button>
    <button class="btn-action btn-success" onclick="configureSchedule()">
        <i class="fas fa-cog"></i> Configurar Tabela
    </button>
    <button class="btn-action btn-warning" onclick="duplicateFromOtherClass()">
        <i class="fas fa-copy"></i> Duplicar de Outra Turma
    </button>
    <button class="btn-action btn-secondary" onclick="exportToExcel()">
        <i class="fas fa-file-excel"></i> Exportar Excel
    </button>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Carga Horária</div>
            <div class="stat-value"><?= $weeklyHours ?>h</div>
            <div class="stat-sub">Por semana</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Disciplinas</div>
            <div class="stat-value"><?= $totalDisciplines ?></div>
            <div class="stat-sub"><?= $assignedTeachers ?> com professor</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Horários</div>
            <div class="stat-value"><?= $totalSchedules ?></div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(106,76,202,.1); color: #6A4CCA;">
            <i class="fas fa-percent"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Cobertura</div>
            <div class="stat-value"><?= $totalDisciplines > 0 ? round(($totalSchedules / $totalDisciplines) * 100) : 0 ?>%</div>
            <div class="stat-sub">Do total de disciplinas</div>
        </div>
    </div>
</div>

<!-- Quick Add Form -->
<div class="quick-form-container" id="quickForm">
    <div class="quick-form-header">
        <h6><i class="fas fa-plus-circle text-success me-2"></i>Adicionar Novo Horário</h6>
        <button class="btn-icon" onclick="toggleQuickForm()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <form action="<?= site_url('admin/classes/schedule/save') ?>" method="post" id="quickScheduleForm">
        <?= csrf_field() ?>
        <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
        
        <div class="quick-form-grid">
            <div>
                <label class="form-label">Disciplina <span class="text-danger">*</span></label>
                <select name="discipline_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($disciplines as $disc): ?>
                        <option value="<?= $disc->discipline_id ?>">
                            <?= $disc->discipline_name ?> (<?= $disc->discipline_code ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="form-label">Dia <span class="text-danger">*</span></label>
                <select name="day_of_week" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($weekDays as $day => $dayName): ?>
                        <option value="<?= $day ?>"><?= $dayName ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="form-label">Período <span class="text-danger">*</span></label>
                <select name="period" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($periods as $period => $periodInfo): ?>
                        <option value="<?= $period ?>"><?= $periodInfo['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="form-label">Hora Início</label>
                <input type="time" name="start_time" class="form-control" value="07:30">
            </div>
            
            <div>
                <label class="form-label">Hora Término</label>
                <input type="time" name="end_time" class="form-control" value="09:00">
            </div>
            
            <div>
                <label class="form-label">Professor</label>
                <select name="teacher_id" class="form-select">
                    <option value="">Opcional</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher->id ?>">
                            <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="form-label">Sala</label>
                <input type="text" name="room" class="form-control" placeholder="Ex: Sala 101">
            </div>
        </div>
        
        <div class="quick-form-actions">
            <button type="button" class="btn btn-secondary" onclick="toggleQuickForm()">Cancelar</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Salvar Horário
            </button>
        </div>
    </form>
</div>

<!-- Schedule Table -->
<div class="schedule-container">
    <div class="schedule-header">
        <h5>
            <i class="fas fa-table"></i>
            Horário Semanal - <?= $class['class_name'] ?>
            <span class="badge bg-primary ms-2"><?= $totalSchedules ?> horários</span>
        </h5>
        <div class="schedule-actions">
            <button class="btn-icon add" onclick="toggleQuickForm()" title="Adicionar Horário">
                <i class="fas fa-plus"></i>
            </button>
            <button class="btn-icon configure" onclick="configureSchedule()" title="Configurar Tabela">
                <i class="fas fa-cog"></i>
            </button>
            <button class="btn-icon print" onclick="window.print()" title="Imprimir">
                <i class="fas fa-print"></i>
            </button>
            <button class="btn-icon pdf" onclick="exportToPDF()" title="Exportar PDF">
                <i class="fas fa-file-pdf"></i>
            </button>
            <button class="btn-icon excel" onclick="exportToExcel()" title="Exportar Excel">
                <i class="fas fa-file-excel"></i>
            </button>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Horário</th>
                    <?php foreach ($weekDays as $day => $dayName): ?>
                        <th><?= $dayName ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($periods as $period => $periodInfo): ?>
                    <tr>
                        <td class="time-column">
                            <strong><?= $periodInfo['time'] ?></strong>
                            <small><?= $periodInfo['name'] ?></small>
                        </td>
                        
                        <?php foreach ($daysOfWeek as $day): ?>
                            <td>
                                <div class="schedule-cell">
                                    <?php if (isset($schedule[$day][$period]) && !empty($schedule[$day][$period])): ?>
                                        <?php foreach ($schedule[$day][$period] as $item): ?>
                                            <div class="schedule-item">
                                                <span class="discipline-name"><?= $item['discipline_name'] ?></span>
                                                <span class="teacher-name">
                                                    <i class="fas fa-user"></i>
                                                    <?= $item['teacher_name'] ?>
                                                </span>
                                                <?php if (!empty($item['room'])): ?>
                                                    <span class="room-badge">
                                                        <i class="fas fa-door-open"></i> <?= $item['room'] ?>
                                                    </span>
                                                <?php endif; ?>
                                                <div class="schedule-item-actions">
                                                    <button class="btn-sm edit-sm" onclick="editSchedule('<?= $item['id'] ?>')" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-sm delete-sm" onclick="confirmDelete('<?= $item['id'] ?>', '<?= $item['discipline_name'] ?>')" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <button class="add-schedule-btn" onclick="quickAdd('<?= $period ?>', '<?= $day ?>')">
                                            <i class="fas fa-plus me-1"></i> Adicionar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Legend -->
<div class="legend">
    <div class="legend-items">
        <div class="legend-item">
            <span class="legend-color assigned"></span>
            <span>Com professor atribuído</span>
        </div>
        <div class="legend-item">
            <span class="legend-color pending"></span>
            <span>Sem professor</span>
        </div>
        <div class="legend-item">
            <i class="fas fa-door-open me-2 text-secondary"></i>
            <span>Com sala definida</span>
        </div>
    </div>
    <div>
        <button class="btn btn-sm btn-outline-primary" onclick="configureSchedule()">
            <i class="fas fa-cog me-1"></i>Configurar Tabela
        </button>
    </div>
</div>

<!-- Configuration Modal -->
<div class="modal fade modal-custom" id="configModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog me-2"></i>
                    Configurar Tabela de Horários
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="configForm">
                    <h6 class="mb-3">Períodos do Dia</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Horário</th>
                                    <th>Descrição</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="periodsList">
                                <?php foreach ($periods as $period => $periodInfo): ?>
                                    <tr>
                                        <td><?= $period ?>º Período</td>
                                        <td><?= $periodInfo['time'] ?></td>
                                        <td><?= $periodInfo['name'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editPeriod('<?= $period ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <h6 class="mb-3">Dias da Semana</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Dia</th>
                                    <th>Nome</th>
                                    <th>Ativo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($weekDays as $day => $dayName): ?>
                                    <tr>
                                        <td><?= $day ?></td>
                                        <td><?= $dayName ?></td>
                                        <td>
                                            <span class="badge bg-success">Ativo</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editDay('<?= $day ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="saveConfiguration()">
                    <i class="fas fa-save me-1"></i>Salvar Configurações
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-custom" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--danger);">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o horário de <strong id="deleteDisciplineName"></strong>?</p>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Debug - mostrar erros no console
window.onerror = function(msg, url, line) {
    console.error('Erro JavaScript:', msg, 'em', url, 'linha:', line);
    return false;
};

// Variável global para armazenar o modal de delete
let deleteModal = null;

$(document).ready(function() {
    console.log('Document ready - inicializando...');
    
    // Inicializar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar modal de delete
    deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Verificar se há disciplinas
    <?php if (empty($disciplines)): ?>
    $('#quickForm').hide();
    <?php endif; ?>
    
    // Debug: verificar se os botões estão sendo clicados
    $(document).on('click', '.edit-sm', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Botão editar clicado');
    });
    
    $(document).on('click', '.delete-sm', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Botão deletar clicado');
    });
    
    console.log('Inicialização completa');
});

// ============ FUNÇÕES DE HORÁRIO ============

// Editar horário
window.editSchedule = function(id) {
    console.log('Editando horário ID:', id);
    
    if (!id) {
        alert('ID do horário não encontrado');
        return;
    }
    
    // Buscar dados do horário via AJAX
    $.ajax({
        url: '<?= site_url('admin/classes/schedule/get-schedule-item') ?>',
        method: 'POST',
        data: {
            id: id,
            class_id: <?= $class['id'] ?>,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            console.log('Resposta do servidor:', response);
            
            if (response.success) {
                // Preencher o formulário com os dados do item
                $('select[name="discipline_id"]').val(response.item.discipline_id);
                $('select[name="day_of_week"]').val(response.day);
                $('select[name="period"]').val(response.period);
                
                // Extrair apenas HH:MM do time
                if (response.item.start_time) {
                    $('input[name="start_time"]').val(response.item.start_time.substring(0, 5));
                }
                if (response.item.end_time) {
                    $('input[name="end_time"]').val(response.item.end_time.substring(0, 5));
                }
                
                $('select[name="teacher_id"]').val(response.item.teacher_id);
                $('input[name="room"]').val(response.item.room || '');
                
                // Adicionar campo hidden com o ID
                if ($('#editId').length === 0) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: 'editId',
                        name: 'id',
                        value: response.item.id
                    }).appendTo('#quickScheduleForm');
                } else {
                    $('#editId').val(response.item.id);
                }
                
                // Mudar texto do botão
                $('#quickScheduleForm button[type="submit"]').html('<i class="fas fa-save me-1"></i>Atualizar Horário');
                
                // Abrir o formulário se estiver fechado
                if ($('#quickForm').is(':hidden')) {
                    toggleQuickForm();
                } else {
                    $('#quickForm').show();
                }
                
                // Scroll para o formulário
                $('html, body').animate({
                    scrollTop: $('#quickForm').offset().top - 100
                }, 500);
            } else {
                alert('Erro ao carregar dados do horário');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', error);
            console.error('Resposta:', xhr.responseText);
            alert('Erro ao comunicar com o servidor');
        }
    });
};

// Confirmar eliminação de horário
window.confirmDelete = function(id, disciplineName) {
    console.log('confirmDelete chamado - ID:', id, 'Disciplina:', disciplineName);
    
    if (!id) {
        alert('ID do horário não encontrado');
        return;
    }
    
    // Verificar se o modal foi inicializado
    if (!deleteModal) {
        console.log('Inicializando modal de delete...');
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    }
    
    // Atualizar o texto do modal
    $('#deleteDisciplineName').text(disciplineName || 'esta disciplina');
    
    // Atualizar o link de confirmação
    var deleteUrl = '<?= site_url('admin/classes/schedule/delete/') ?>' + id;
    $('#confirmDeleteBtn').attr('href', deleteUrl);
    console.log('URL de delete:', deleteUrl);
    
    // Mostrar o modal
    try {
        deleteModal.show();
        console.log('Modal mostrado com sucesso');
    } catch (e) {
        console.error('Erro ao mostrar modal:', e);
        // Fallback: confirm nativo
        if (confirm('Tem certeza que deseja eliminar o horário de ' + disciplineName + '?')) {
            window.location.href = deleteUrl;
        }
    }
};

// ============ FUNÇÕES DO FORMULÁRIO RÁPIDO ============

// Toggle Quick Form
window.toggleQuickForm = function() {
    $('#quickForm').slideToggle(300, function() {
        if ($(this).is(':hidden')) {
            resetQuickForm();
        }
    });
};

// Resetar formulário para modo de criação
window.resetQuickForm = function() {
    $('#quickScheduleForm')[0].reset();
    $('#editId').remove();
    $('#quickScheduleForm button[type="submit"]').html('<i class="fas fa-save me-1"></i>Salvar Horário');
    $('input[name="start_time"]').val('07:30');
    $('input[name="end_time"]').val('09:00');
};

// Quick Add from cell
window.quickAdd = function(period, day) {
    console.log('quickAdd - Período:', period, 'Dia:', day);
    resetQuickForm();
    $('select[name="period"]').val(period.toString());
    $('select[name="day_of_week"]').val(day);
    toggleQuickForm();
};

// ============ FUNÇÕES DE CONFIGURAÇÃO ============

// Configurar horário
window.configureSchedule = function() {
    new bootstrap.Modal(document.getElementById('configModal')).show();
};

// Duplicar de outra turma
window.duplicateFromOtherClass = function() {
    let sourceClass = prompt('Digite o ID da turma para duplicar o horário:');
    if (sourceClass) {
        $.ajax({
            url: '<?= site_url('admin/classes/schedule/duplicate') ?>',
            method: 'POST',
            data: {
                source_class_id: sourceClass,
                target_class_id: <?= $class['id'] ?>,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Erro: ' + response.message);
                }
            }
        });
    }
};

// ============ FUNÇÕES DE EXPORTAÇÃO ============

window.exportToExcel = function() {
    window.location.href = '<?= site_url('admin/classes/schedule/export-excel/' . $class['id']) ?>';
};

window.exportToPDF = function() {
    window.location.href = '<?= site_url('admin/classes/schedule/export-pdf/' . $class['id']) ?>';
};

// ============ FUNÇÕES DE CONFIGURAÇÃO (placeholders) ============

window.saveConfiguration = function() {
    alert('Configurações salvas com sucesso!');
    bootstrap.Modal.getInstance(document.getElementById('configModal')).hide();
};

window.editPeriod = function(period) {
    alert('Editar período ' + period);
};

window.editDay = function(day) {
    alert('Editar dia ' + day);
};

// ============ VALIDAÇÃO DO FORMULÁRIO ============

// Check availability before submit
$('#quickScheduleForm').on('submit', function(e) {
    const day = $('select[name="day_of_week"]').val();
    const period = $('select[name="period"]').val();
    const isEditing = $('#editId').length > 0;
    
    if (day && period && !isEditing) {
        // Verificar disponibilidade via AJAX apenas para novos registros
        $.ajax({
            url: '<?= site_url('admin/classes/schedule/check-availability') ?>',
            method: 'POST',
            data: {
                class_id: <?= $class['id'] ?>,
                day_of_week: day,
                period: period,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            async: false, // Sincrono para aguardar resposta
            success: function(response) {
                if (!response.available) {
                    if (!confirm('Já existe um horário para este período. Deseja ignorar e salvar assim mesmo?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            }
        });
    }
});

// ============ ATALHOS DE TECLADO ============

$(document).keydown(function(e) {
    // Ctrl + N = Novo horário
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        resetQuickForm();
        toggleQuickForm();
    }
    // Ctrl + P = Imprimir
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
    // Ctrl + E = Configurar
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        configureSchedule();
    }
});

// ============ DRAG AND DROP (placeholder) ============

let draggedItem = null;
$(document).on('dragstart', '.schedule-item', function() {
    draggedItem = this;
}).on('dragover', 'td', function(e) {
    e.preventDefault();
}).on('drop', 'td', function(e) {
    e.preventDefault();
    alert('Funcionalidade em desenvolvimento');
});
</script>
<?= $this->endSection() ?>