<?php
/**
 * Email Bulk Sending with QR Code for Event
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
                            <h5 class="mb-0">Enviar Email Masivo con Código QR</h5>
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
                    
                    <form method="POST" action="/eventos/<?= $event['id'] ?>/send-email">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Variables disponibles:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><code>{nombre}</code> - Nombre del asistente</li>
                                        <li><code>{codigo}</code> - Código de registro (boleto QR)</li>
                                        <li><code>{evento}</code> - Nombre del evento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="subject" class="form-label">Asunto <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="subject" 
                                        name="subject" 
                                        value="<?= htmlspecialchars($defaultSubject) ?>"
                                        required
                                    >
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="email_body" class="form-label">Mensaje <span class="text-danger">*</span></label>
                                    <textarea 
                                        class="form-control" 
                                        id="email_body" 
                                        name="email_body" 
                                        rows="15"
                                        required
                                    ><?= htmlspecialchars($defaultBody) ?></textarea>
                                    <small class="form-text text-muted">
                                        Este mensaje se personalizará para cada destinatario.
                                        El código QR se incluirá automáticamente.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>
                                    Enviar Correos
                                </button>
                                <a href="/eventos/<?= $event['id'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Cancelar
                                </a>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Nota:</strong> El envío de correos puede tomar varios minutos dependiendo del número de registrados.
                                    Por favor, no cierres esta ventana hasta que se complete el proceso.
                                </div>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
