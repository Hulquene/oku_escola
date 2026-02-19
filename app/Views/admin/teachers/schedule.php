<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Perfil
            </a>
            <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>"><?= $teacher->full_name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Horário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Professor -->
<div class="alert alert-info d-flex align-items-center mb-4">
    <div class="me-3">
        <?php if ($teacher->photo): ?>
            <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                 alt="Foto" 
                 class="rounded-circle"
                 style="width: 60px; height: 60px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
            </div>
        <?php endif; ?>
    </div>
    <div>
        <h4 class="mb-1"><?= $teacher->full_name ?></h4>
        <p class="mb-0">
            <i class="fas fa-envelope me-2"></i><?= $teacher->email ?> |
            <i class="fas fa-phone me-2"></i><?= $teacher->phone ?: '-' ?> |
            <span class="badge bg-<?= $teacher->is_active ? 'success' : 'danger' ?>">
                <?= $teacher->is_active ? 'Ativo' : 'Inativo' ?>
            </span>
        </p>
    </div>
</div>

<?php if (empty($assignments)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Nenhuma disciplina atribuída</h4>
            <p class="mb-3">Este professor ainda não tem disciplinas atribuídas para o ano letivo atual.</p>
            <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Atribuir Disciplinas
            </a>
        </div>
    </div>
<?php else: ?>

<!-- Resumo -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total de Turmas</h6>
                        <h3 class="mb-0"><?= count($assignments) ?></h3>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Carga Horária Total</h6>
                        <h3 class="mb-0">
                            <?php
                            $totalHours = array_sum(array_column($assignments, 'workload_hours'));
                            echo $totalHours . 'h';
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Turnos</h6>
                        <h3 class="mb-0">
                            <?php
                            $shifts = array_unique(array_column($assignments, 'class_shift'));
                            echo count($shifts);
                            ?>
                        </h3>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Horário por Turno -->
<?php foreach (['Manhã', 'Tarde', 'Noite', 'Integral'] as $shift): ?>
    <?php if (!empty($byShift[$shift])): ?>
        <div class="card mb-4">
            <div class="card-header bg-<?= 
                $shift == 'Manhã' ? 'warning' : 
                ($shift == 'Tarde' ? 'success' : 
                ($shift == 'Noite' ? 'dark' : 'info')) 
            ?> text-white">
                <h5 class="mb-0">
                    <i class="fas fa-sun me-2"></i>Turno da <?= $shift ?>
                    <span class="badge bg-light text-dark ms-2"><?= count($byShift[$shift]) ?> disciplinas</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Turma</th>
                                <th>Código</th>
                                <th>Disciplina</th>
                                <th>Carga Horária</th>
                                <th>Sala</th>
                                <th>Semestre</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($byShift[$shift] as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= $item->class_name ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $item->class_code ?></span>
                                    </td>
                                    <td>
                                        <?= $item->discipline_name ?>
                                        <br>
                                        <small class="text-muted"><?= $item->discipline_code ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $item->workload_hours ?: '-' ?> h</span>
                                    </td>
                                    <td>
                                        <i class="fas fa-door-open me-1"></i><?= $item->class_room ?: '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($item->semester_name): ?>
                                            <span class="badge bg-secondary"><?= $item->semester_name ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Anual</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item->is_active): ?>
                                            <span class="badge bg-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativa</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Legenda -->
<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle me-2 text-info"></i>Informações</h6>
                <p class="text-muted small mb-0">
                    Este horário mostra todas as disciplinas atribuídas ao professor no ano letivo atual.
                    Para fazer alterações, utilize a opção "Atribuir Turmas".
                </p>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir Horário
                </button>
                <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->id) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit me-1"></i>Editar Atribuições
                </a>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Função para imprimir
function printSchedule() {
    window.print();
}
</script>

<style>
@media print {
    .btn, .page-header, .breadcrumb, footer, .card-header button {
        display: none !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
<?= $this->endSection() ?>