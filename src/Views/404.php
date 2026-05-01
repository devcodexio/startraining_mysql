<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Página No Encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body { height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; }
        .error-code { font-size: clamp(6rem, 15vw, 10rem); font-weight: 900; line-height: 0.8; margin-bottom: 2rem; color: var(--primary); opacity: 0.9; letter-spacing: -5px; }
        .message-box { max-width: 500px; padding: 4rem 3rem; }
    </style>
</head>
<body class="animate">
    <div class="glass-card message-box">
        <div class="mb-4" style="font-size: 3rem; color: var(--text-muted); opacity: 0.4;">
            <i class="fas fa-satellite"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="mb-3 text-gradient">Parece que te has perdido en el espacio</h2>
        <p class="text-secondary mb-5 small">La página que buscas no existe o ha sido movida. Verifica la URL o intenta volver al panel principal.</p>
        <a href="/dashboard" class="btn-futuristic" style="padding: 1rem 2.5rem;">
            <i class="fas fa-arrow-left me-2"></i> Volver al Inicio
        </a>
    </div>
</body>
</html>
