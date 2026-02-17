<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/attendance') ?>">Presenças</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Semester Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="semester" class="form-label">Semestre</label>
                <select class="form-select" id="semester" name="semester" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php if (!empty($semesters)): ?>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= $selectedSemester == $sem->id ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?> (<?= date('Y', strtotime($sem->start_date)) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Summary by Semester -->
<?php if (!empty($attendances)): ?>
    <?php
    // Group by semester
    $grouped = [];
    foreach ($attendances as $att) {
        $semesterId = $att->semester_id ?? 'outros';
        if (!isset($grouped[$semesterId])) {
            $grouped[$semesterId] = [
                'semester' => null,
                'attendances' => [],
                'stats' => ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'justified' => 0]
            ];
        }
        $grouped[$semesterId]['attendances'][] = $att;
        
        $stats = &$grouped[$semesterId]['stats'];
        $stats['total']++;
        switch ($att->status) {
            case 'Presente':
                $stats['present']++;
                break;
            case 'Ausente':
                $stats['absent']++;
                break;
            case 'Atrasado':
                $stats['late']++;
                break;
            case 'Falta Justificada':
                $stats['justified']++;
                break;
        }
    }
    ?>
    
    <?php foreach ($grouped as $groupId => $group): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> 
                    <?= $group['semester'] ? $group['semester']->semester_name : 'Outros Períodos' ?>
                </h5>
            </div>
            <div class="card-body">
                <!-- Mini Stats -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <small class="text-muted d-block">Total: <strong><?= $group['stats']['total'] ?></strong></small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-success d-block">Presentes: <strong><?= $group['stats']['present'] ?></strong></small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-danger d-block">Ausentes: <strong><?= $group['stats']['absent'] ?></strong></small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-warning d-block">Atrasados: <strong><?= $group['stats']['late'] ?></strong></small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-info d-block">Justificados: <strong><?= $group['stats']['justified'] ?></strong></small>
                    </div>
                    <div class="col-md-2">
                        <small class="text-primary d-block">
                            Taxa: <strong><?= round(($group['stats']['present'] / $group['stats']['total']) * 100, 1) ?>%</strong>
                        </small>
                    </div>
                </div>
                
                <!-- Attendance List -->
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Disciplina</th>
                                <th>Status</th>
                                <th>Justificação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group['attendances'] as $att): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($att->attendance_date)) ?></td>
                                    <td><?= $att->discipline_name ?? 'Geral' ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'Presente' => 'success',
                                            'Ausente' => 'danger',
                                            'Atrasado' => 'warning',
                                            'Falta Justificada' => 'info',
                                            'Dispensado' => 'secondary'
                                        ][$att->status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $att->status ?></span>
                                    </td>
                                    <td><?= $att->justification ?: '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Nenhum registro de presença encontrado.
    </div>
<?php endif; ?>

<?= $this->endSection() ?>