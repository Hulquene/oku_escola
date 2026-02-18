<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/profile') ?>">Meu Perfil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Encarregados</li>
        </ol>
    </nav>
</div>

<div class="row">
    <?php if (!empty($guardians)): ?>
        <?php foreach ($guardians as $guardian): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <?= $guardian->full_name ?>
                            <?php if ($guardian->is_authorized): ?>
                                <span class="badge bg-success float-end">Autorizado</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge bg-info"><?= $guardian->guardian_type ?></span>
                            <?php if ($guardian->relationship): ?>
                                <span class="badge bg-secondary"><?= $guardian->relationship ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%"><i class="fas fa-phone"></i> Telefone:</th>
                                <td><?= $guardian->phone ?></td>
                            </tr>
                            <?php if ($guardian->email): ?>
                            <tr>
                                <th><i class="fas fa-envelope"></i> Email:</th>
                                <td><?= $guardian->email ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($guardian->profession): ?>
                            <tr>
                                <th><i class="fas fa-briefcase"></i> Profissão:</th>
                                <td><?= $guardian->profession ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($guardian->workplace): ?>
                            <tr>
                                <th><i class="fas fa-building"></i> Local Trabalho:</th>
                                <td><?= $guardian->workplace ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        
                        <?php if ($guardian->address): ?>
                            <hr>
                            <p class="mb-0">
                                <i class="fas fa-map-marker-alt"></i> <?= $guardian->address ?>
                                <?php if ($guardian->city): ?>, <?= $guardian->city ?><?php endif; ?>
                                <?php if ($guardian->province): ?> - <?= $guardian->province ?><?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>Nenhum encarregado registado</h5>
                <p>Não há encarregados de educação associados ao seu perfil.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="row mt-4">
    <div class="col-12">
        <a href="<?= site_url('students/profile') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>
</div>

<?= $this->endSection() ?>