<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Month Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Mês</label>
                <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Ano</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y <= date('Y') + 2; $y++): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Calendar -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-alt"></i> Calendário de Exames - <?= $months[$month] ?> <?= $year ?>
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Sala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentDate = '';
                        foreach ($exams as $exam): 
                            $examDate = date('Y-m-d', strtotime($exam->exam_date));
                            if ($currentDate != $examDate):
                                $currentDate = $examDate;
                        ?>
                            <tr class="table-secondary">
                                <td colspan="5" class="fw-bold">
                                    <i class="fas fa-calendar-day"></i> 
                                    <?= date('d/m/Y', strtotime($exam->exam_date)) ?> 
                                    (<?= getDayOfWeek($exam->exam_date) ?>)
                                </td>
                            </tr>
                        <?php endif; ?>
                            <tr>
                                <td></td>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '-' ?></td>
                                <td><?= $exam->exam_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum exame agendado para este mês.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('functions') ?>
<?php
// Helper function to get day of week in Portuguese
if (!function_exists('getDayOfWeek')) {
    function getDayOfWeek($date) {
        $days = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado'
        ];
        $dayName = date('l', strtotime($date));
        return $days[$dayName] ?? $dayName;
    }
}
?>
<?= $this->endSection() ?>