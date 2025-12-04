<?php
/**
 * WhatsApp Bulk Messaging for Event
 */
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <a href="/eventos/<?= $event['id'] ?>" class="btn btn-sm btn-secondary me-3">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                        <div>
                            <h5 class="mb-0">Enviar WhatsApp Masivo</h5>
                            <p class="text-sm mb-0"><?= htmlspecialchars($event['title']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($whatsappLinks)): ?>
                        <!-- Form to generate WhatsApp links -->
                        <form method="POST" action="/eventos/<?= $event['id'] ?>/send-whatsapp">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Variables disponibles:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><code>{nombre}</code> - Nombre del asistente</li>
                                            <li><code>{codigo}</code> - Código de registro (boleto)</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="message" class="form-label">Mensaje <span class="text-danger">*</span></label>
                                        <textarea 
                                            class="form-control" 
                                            id="message" 
                                            name="message" 
                                            rows="10"
                                            required
                                        ><?= htmlspecialchars($defaultMessage) ?></textarea>
                                        <small class="form-text text-muted">
                                            Este mensaje se personalizará para cada destinatario.
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fab fa-whatsapp me-2"></i>
                                        Generar Enlaces de WhatsApp
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- Display WhatsApp links -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Se generaron <?= count($whatsappLinks) ?> enlaces de WhatsApp.
                                    Haz clic en cada enlace para enviar el mensaje personalizado.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Teléfono</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($whatsappLinks as $link): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($link['nombre']) ?></td>
                                                    <td><?= htmlspecialchars($link['phone']) ?></td>
                                                    <td>
                                                        <a href="<?= htmlspecialchars($link['link']) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-success">
                                                            <i class="fab fa-whatsapp me-1"></i>
                                                            Abrir WhatsApp
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <a href="/eventos/<?= $event['id'] ?>/send-whatsapp" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i>
                                    Generar Nuevos Enlaces
                                </a>
                                <a href="/eventos/<?= $event['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Finalizar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>
