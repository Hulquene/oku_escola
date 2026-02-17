<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="<?= site_url('admin/students/guardians') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/guardians') ?>">Encarregados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user"></i> Informações Pessoais
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Nome:</th>
                        <td><?= $guardian->full_name ?></td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td><span class="badge bg-info"><?= $guardian->guardian_type ?></span></td>
                    </tr>
                    <tr>
                        <th>Telefone:</th>
                        <td><?= $guardian->phone ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= $guardian->email ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Profissão:</th>
                        <td><?= $guardian->profession ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Local Trabalho:</th>
                        <td><?= $guardian->workplace ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Documentos
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Tipo Doc.:</th>
                        <td><?= $guardian->identity_type ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>Nº Documento:</th>
                        <td><?= $guardian->identity_document ?: '-' ?></td>
                    </tr>
                    <tr>
                        <th>NIF:</th>
                        <td><?= $guardian->nif ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-map-marker-alt"></i> Endereço
            </div>
            <div class="card-body">
                <p>
                    <?= $guardian->address ?: '-' ?><br>
                    <?= $guardian->city ? $guardian->city . ', ' : '' ?>
                    <?= $guardian->municipality ? $guardian->municipality . ', ' : '' ?>
                    <?= $guardian->province ?: '' ?>
                </p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-graduate"></i> Alunos Associados
            </div>
            <div class="card-body">
                <?php if (!empty($students)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nº Matrícula</th>
                                    <th>Nome do Aluno</th>
                                    <th>Parentesco</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?= $student->student_number ?></td>
                                        <td><?= $student->first_name ?> <?= $student->last_name ?></td>
                                        <td><?= $student->relationship ?: $guardian->guardian_type ?></td>
                                        <td>
                                            <a href="<?= site_url('admin/students/view/' . $student->id) ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Ver Aluno
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum aluno associado a este encarregado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>