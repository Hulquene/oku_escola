<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/academic/semesters/form-edit/' . $semester->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/academic/semesters') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/semesters') ?>">Semestres/Trimestres</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Details Card -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Informações do Período
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td><?= $semester->id ?></td>
                    </tr>
                    <tr>
                        <th>Nome</th>
                        <td><?= $semester->semester_name ?></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td>
                            <span class="badge bg-info"><?= $semester->semester_type ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td><?= $semester->year_name ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Data Início</th>
                        <td><?= date('d/m/Y', strtotime($semester->start_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Data Fim</th>
                        <td><?= date('d/m/Y', strtotime($semester->end_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($semester->is_active): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inativo</span>
                            <?php endif; ?>
                            
                            <?php if ($semester->is_current): ?>
                                <span class="badge bg-primary ms-2">Período Atual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Criado em</th>
                        <td><?= date('d/m/Y H:i', strtotime($semester->created_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Exams in this semester -->
<div class="card mt-4">
    <div class="card-header">
        <i class="fas fa-pencil-alt"></i> Exames neste Período
    </div>
    <div class="card-body">
        <?php
        $examModel = new \App\Models\ExamModel();
        $exams = $examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.semester_id', $semester->id)
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->findAll();
        ?>
        
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Exame</th>
                            <th>Turma</th>
                            <th>Disciplina</th>
                            <th>Sala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exams as $exam): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                                <td><?= $exam->exam_name ?></td>
                                <td><?= $exam->class_name ?></td>
                                <td><?= $exam->discipline_name ?></td>
                                <td><?= $exam->exam_room ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum exame agendado para este período.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>