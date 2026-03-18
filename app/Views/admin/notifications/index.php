<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Estilos específicos para a página de notificações */
.notification-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.notification-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.25rem;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.notification-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--accent);
    transform: translateY(-2px);
}

.notification-card.unread {
    background: rgba(59,127,232,0.02);
    border-left: 3px solid var(--accent);
}

.notification-card.unread::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 30px 30px 0;
    border-color: transparent var(--accent) transparent transparent;
}

.notification-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.notification-icon.primary {
    background: rgba(59,127,232,0.1);
    color: var(--accent);
}

.notification-icon.success {
    background: rgba(22,168,125,0.1);
    color: var(--success);
}

.notification-icon.warning {
    background: rgba(232,160,32,0.1);
    color: var(--warning);
}

.notification-icon.danger {
    background: rgba(232,70,70,0.1);
    color: var(--danger);
}

.notification-icon.info {
    background: rgba(8,145,178,0.1);
    color: #0891B2;
}

.notification-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.85rem;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

.notification-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--border);
    font-size: 0.75rem;
    color: var(--text-muted);
}

.notification-time {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.notification-time i {
    font-size: 0.7rem;
    color: var(--accent);
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
}

.notification-actions .btn-action {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.18s;
}

.notification-actions .btn-action:hover {
    color: #fff;
    border-color: transparent;
    transform: translateY(-1px);
}

.notification-actions .btn-action.read:hover {
    background: var(--accent);
}

.notification-actions .btn-action.delete:hover {
    background: var(--danger);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--surface-card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
}

.empty-state i {
    font-size: 3rem;
    color: var(--text-muted);
    opacity: 0.3;
    margin-bottom: 1rem;
}

.empty-state h5 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 0;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

/* Responsividade */
@media (max-width: 768px) {
    .notification-grid {
        grid-template-columns: 1fr;
    }
    
    .notification-meta {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-bell me-2"></i><?= $title ?? 'Notificações' ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notificações</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/notifications/mark-all-read') ?>" 
               class="hdr-btn success" 
               onclick="return confirm('Marcar todas as notificações como lidas?')">
                <i class="fas fa-check-double"></i> Marcar todas como lidas
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Lista de Notificações -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Minhas Notificações</span>
        </div>
        <span class="badge bg-primary"><?= $pager->getTotal() ?? 0 ?> notificações</span>
    </div>
    
    <div class="ci-card-body">
        <?php if (!empty($notifications)): ?>
            <div class="notification-grid">
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    // Determinar ícone e cor baseado no tipo
                    $icon = $notification['icon'] ?? 'fa-info-circle';
                    $color = $notification['color'] ?? 'primary';
                    
                    // Mapear cores
                    $colorMap = [
                        'primary' => 'primary',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        'info' => 'info'
                    ];
                    
                    $colorClass = $colorMap[$color] ?? 'primary';
                    
                    // Formatar data
                    $createdAt = new DateTime($notification['created_at']);
                    $now = new DateTime();
                    $diff = $now->diff($createdAt);
                    
                    if ($diff->days > 7) {
                        $timeStr = $createdAt->format('d/m/Y');
                    } elseif ($diff->days > 0) {
                        $timeStr = $diff->days . ' dia' . ($diff->days > 1 ? 's' : '') . ' atrás';
                    } elseif ($diff->h > 0) {
                        $timeStr = $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
                    } elseif ($diff->i > 0) {
                        $timeStr = $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
                    } else {
                        $timeStr = 'agora mesmo';
                    }
                    ?>
                    
                    <div class="notification-card <?= $notification['is_read'] ? '' : 'unread' ?>">
                        <?php if (!$notification['is_read']): ?>
                            <div class="notification-badge"></div>
                        <?php endif; ?>
                        
                        <div class="notification-header">
                            <div class="notification-icon <?= $colorClass ?>">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <div style="flex: 1;">
                                <div class="notification-title"><?= esc($notification['title']) ?></div>
                                <div class="notification-message">
                                    <?= esc($notification['message']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-meta">
                            <div class="notification-time">
                                <i class="fas fa-clock"></i>
                                <span><?= $timeStr ?></span>
                            </div>
                            
                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                    <a href="<?= site_url('admin/notifications/read/' . $notification['id']) ?>" 
                                       class="btn-action read" 
                                       title="Marcar como lida">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($notification['link'])): ?>
                                    <a href="<?= $notification['link'] ?>" 
                                       class="btn-action read" 
                                       title="Ver detalhes">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?= site_url('admin/notifications/delete/' . $notification['id']) ?>" 
                                   class="btn-action delete" 
                                   title="Remover"
                                   onclick="return confirm('Remover esta notificação?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($notification['link_text'])): ?>
                            <div class="mt-2">
                                <a href="<?= $notification['link'] ?>" class="text-accent" style="font-size: 0.8rem;">
                                    <?= $notification['link_text'] ?> <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginação -->
            <?php if ($pager && $pager->getPageCount() > 1): ?>
                <div class="pagination-wrapper mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell"></i>
                <h5>Nenhuma notificação</h5>
                <p>Você não possui notificações no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Atualizar contador de notificações não lidas a cada minuto
function updateUnreadCount() {
    fetch('<?= site_url('admin/notifications/unread-count') ?>', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badge = document.querySelector('.notif-dot');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 9 ? '9+' : data.count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    })
    .catch(error => console.error('Erro ao atualizar contador:', error));
}

// Atualizar a cada 60 segundos
setInterval(updateUnreadCount, 60000);

// Marcar notificação como lida ao clicar no link
document.querySelectorAll('.notification-card a').forEach(link => {
    link.addEventListener('click', function(e) {
        const parent = this.closest('.notification-card');
        if (parent && parent.classList.contains('unread')) {
            // Se for um link que não é de marcar como lida, marcar como lida também
            if (!this.href.includes('read/') && !this.href.includes('delete/')) {
                const readLink = parent.querySelector('.btn-action.read');
                if (readLink) {
                    fetch(readLink.href, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>