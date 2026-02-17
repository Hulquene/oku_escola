<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" class="btn btn-info">
                <i class="fas fa-user-graduate"></i> Ver Aluno
            </a>
            <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/view/' . $student->id) ?>"><?= $student->first_name ?> <?= $student->last_name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações do Aluno -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4><?= $student->first_name ?> <?= $student->last_name ?></h4>
                <p class="mb-1"><strong>Matrícula:</strong> <?= $student->student_number ?></p>
                <p class="mb-0"><strong>Email:</strong> <?= $student->email ?></p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-<?= $student->is_active ? 'success' : 'danger' ?> p-2">
                    <?= $student->is_active ? 'Ativo' : 'Inativo' ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Timeline de Matrículas -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Histórico de Matrículas
    </div>
    <div class="card-body">
        <?php if (!empty($history)): ?>
            <div class="timeline">
                <?php foreach ($history as $index => $enrollment): ?>
                    <div class="timeline-item <?= $index == 0 ? 'current' : '' ?>">
                        <div class="timeline-badge">
                            <?php
                            $badgeIcon = [
                                'Ativo' => 'check-circle text-success',
                                'Concluído' => 'flag-checkered text-info',
                                'Transferido' => 'exchange-alt text-primary',
                                'Anulado' => 'times-circle text-danger',
                                'Pendente' => 'clock text-warning'
                            ][$enrollment->status] ?? 'circle';
                            ?>
                            <i class="fas fa-<?= $badgeIcon ?>"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h5 class="timeline-title">
                                    <?= $enrollment->year_name ?> - <?= $enrollment->class_name ?>
                                    <?php if ($enrollment->status == 'Ativo'): ?>
                                        <span class="badge bg-success ms-2">Atual</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="text-muted">
                                    <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($enrollment->enrollment_date)) ?>
                                </p>
                            </div>
                            <div class="timeline-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Nº Matrícula:</strong> <?= $enrollment->enrollment_number ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Tipo:</strong> <?= $enrollment->enrollment_type ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Status:</strong>
                                        <?php
                                        $statusClass = [
                                            'Ativo' => 'success',
                                            'Pendente' => 'warning',
                                            'Concluído' => 'info',
                                            'Transferido' => 'primary',
                                            'Anulado' => 'danger'
                                        ][$enrollment->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $enrollment->status ?></span>
                                    </div>
                                </div>
                                
                                <?php if ($enrollment->previous_class_id): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-arrow-left"></i> Transferido de: 
                                            <?php
                                            $classModel = new \App\Models\ClassModel();
                                            $prevClass = $classModel->find($enrollment->previous_class_id);
                                            echo $prevClass ? $prevClass->class_name : 'Classe anterior';
                                            ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($enrollment->observations): ?>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small><strong>Obs:</strong> <?= $enrollment->observations ?></small>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-3">
                                    <a href="<?= site_url('admin/students/enrollments/view/' . $enrollment->id) ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum histórico de matrícula encontrado para este aluno.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 20px;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 50px;
}

.timeline-item.current .timeline-panel {
    border-left: 4px solid #1cc88a;
}

.timeline-badge {
    position: absolute;
    left: 11px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e3e6f0;
    text-align: center;
    line-height: 20px;
    z-index: 1;
}

.timeline-badge i {
    font-size: 12px;
    line-height: 16px;
}

.timeline-panel {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    padding: 20px;
    position: relative;
}

.timeline-panel::before {
    content: '';
    position: absolute;
    left: -9px;
    top: 10px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid #e3e6f0;
}

.timeline-heading {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e3e6f0;
}

.timeline-title {
    margin: 0;
    color: #4e73df;
}
</style>

<?= $this->endSection() ?>