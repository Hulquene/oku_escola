<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-check me-2" style="color: var(--success);"></i>Presenças do Aluno</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/students') ?>">Meus Alunos</a></li>
                    <li class="breadcrumb-item active">Presenças</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('guardians/students/view/' . $student['id']) ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <div class="mt-2">
        <span class="badge-ci info">
            <i class="fas fa-user-graduate me-1"></i> <?= $student['first_name'] ?> <?= $student['last_name'] ?> (<?= $student['student_number'] ?>)
        </span>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid mb-4">
    <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <div class="stat-icon <?= $stat['percentage'] >= 75 ? 'green' : ($stat['percentage'] >= 50 ? 'orange' : 'danger') ?>">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <div class="stat-label"><?= $stat['discipline_name'] ?></div>
                <div class="stat-value"><?= $stat['percentage'] ?>%</div>
                <div class="stat-sub"><?= $stat['present'] ?>/<?= $stat['total'] ?> presenças</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Tabela de Presenças -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Registo de Presenças</span>
        </div>
        <span class="badge-ci primary">
            <i class="fas fa-list me-1"></i> <?= count($attendance) ?> registos
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($attendance)): ?>
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Disciplina</th>
                            <th class="center">Status</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance as $item): ?>
                            <tr>
                                <td><?= $item['formatted_date'] ?></td>
                                <td><?= $item['discipline_name'] ?></td>
                                <td class="center">
                                    <?php
                                    $statusClass = [
                                        'Presente' => 'success',
                                        'Atrasado' => 'warning',
                                        'Falta' => 'danger',
                                        'Falta Justificada' => 'info'
                                    ][$item['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge-ci <?= $statusClass ?>">
                                        <?= $item['status'] ?>
                                    </span>
                                </td>
                                <td><?= $item['observations'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <h5>Nenhuma presença encontrada</h5>
                <p class="text-muted">Este aluno ainda não possui registo de presenças.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>