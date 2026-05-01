<?php
use App\Models\ConfigModel;
$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$msg = $config['mantenimiento_msg'] ?? 'Estamos realizando mejoras técnicas en la plataforma. Volveremos pronto.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento | <?= htmlspecialchars($siteName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #06070a;
        }
        .maint-card {
            max-width: 600px;
            text-align: center;
            padding: 4rem;
            position: relative;
        }
        .gear-icon {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 2rem;
            animation: rotate 4s linear infinite;
        }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .glow-orb {
            position: absolute;
            width: 300px; height: 300px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.15;
            z-index: -1;
            top: 50%; left: 50%; transform: translate(-50%, -50%);
        }
    </style>
</head>
<body class="animate">
    <div class="glow-orb"></div>
    <div class="glass-card maint-card animate">
        <div class="gear-icon"><i class="fas fa-cog"></i></div>
        <h1 class="text-gradient fw-800 mb-3" style="font-size: 2.5rem;"><?= htmlspecialchars($siteName) ?></h1>
        <h3 class="mb-4 fw-600">Sistema en Mantenimiento</h3>
        <p class="text-secondary fs-5 mb-5" style="opacity: 0.8; line-height: 1.6;">
            <?= htmlspecialchars($msg) ?>
        </p>
        <div class="pt-4 border-top border-secondary border-opacity-10">
            <span class="badge" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary); padding: 10px 20px; border-radius: 12px;">
                <i class="fas fa-clock me-2"></i> Estimamos volver en unos minutos
            </span>
        </div>
    </div>
</body>
</html>
