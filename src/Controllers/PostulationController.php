<?php

namespace App\Controllers;

use App\Services\ExternalApiService;
use App\Config\Database;

class PostulationController {

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $vacante_id = $_POST['vacante_id'];
        $dni        = $_POST['dni'];
        $nombre     = $_POST['nombre_completo'];
        $celular    = $_POST['celular'];
        $email      = $_POST['correo_estudiante'];

        // 1. Validaciones de Duplicados, Celular y Correo Institucional
        $db = Database::getConnection();

        // Celular: exactamente 9 dígitos
        if (strlen($celular) !== 9 || !ctype_digit($celular)) {
            header("Location: /vacante/$vacante_id?postulado=error&msg=" . urlencode("El celular debe tener exactamente 9 dígitos numéricos."));
            exit;
        }

        // Correo Estudiante: Solo dominios .edu.pe
        if (!preg_match('/\.edu\.pe$/i', $email)) {
            header("Location: /vacante/$vacante_id?postulado=error&msg=" . urlencode("Solo se permite postular con un correo institucional (.edu.pe)"));
            exit;
        }

        // Duplicado DNI para esta vacante
        $stmt_check = $db->prepare("SELECT id FROM postulaciones WHERE vacante_id = ? AND (dni = ? OR correo_estudiante = ?)");
        $stmt_check->execute([$vacante_id, $dni, $email]);
        if ($stmt_check->fetch()) {
            header("Location: /vacante/$vacante_id?postulado=error&msg=" . urlencode("Usted ya cuenta con una postulación registrada para esta vacante."));
            exit;
        }

        // 1. Manejo de Archivo (CV)
        $cvPath = '';
        if (!isset($_FILES['url_cv_pdf']) || $_FILES['url_cv_pdf']['error'] !== 0) {
            $errCode = $_FILES['url_cv_pdf']['error'] ?? 'missing';
            header("Location: /vacante/$vacante_id?postulado=error&msg=" . urlencode("No se pudo subir el CV. Código de error: $errCode (Quizás el archivo pesa más de 2MB permitidos por el servidor)"));
            exit;
        }

        $ext = pathinfo($_FILES['url_cv_pdf']['name'], PATHINFO_EXTENSION);
        $fileName = 'cv_' . $dni . '_' . time() . '.' . $ext;
        
        try {
            $cloudinaryUrl = $_ENV['CLOUDINARY_URL'] ?? '';
            $uploadPreset  = $_ENV['CLOUDINARY_UPLOAD_PRESET'] ?? ''; // Opcional, para Unsigned uploads

            if (!empty($cloudinaryUrl)) {
                // Modo Producción: Usar Cloudinary sin SDK (vía cURL REST)
                // CLOUDINARY_URL format: cloudinary://api_key:api_secret@cloud_name
                preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches);
                if (count($matches) === 4) {
                    $apiKey     = $matches[1];
                    $apiSecret  = $matches[2];
                    $cloudName  = trim($matches[3]);
                    $timestamp  = time();
                    $folder     = 'startraining/cvs';
                    $public_id  = 'cv_' . $dni . '_' . $timestamp;
                    
                    $postData = [
                        'file' => new \CURLFile($_FILES['url_cv_pdf']['tmp_name'], 'application/pdf', $fileName),
                    ];

                    if (!empty($uploadPreset)) {
                        // MODO UNSIGNED (Más flexible si las API Keys tienen permisos restringidos)
                        $postData['upload_preset'] = $uploadPreset;
                        $postData['folder']        = $folder;
                        $postData['public_id']     = $public_id;
                        // 🔹 Nota: En modo UNSIGNED NO se permite enviar 'access_mode'.
                        // Se debe configurar el Preset en Cloudinary como 'Public'.
                    } else {
                        // MODO SIGNED (Estándar, requiere API Key con permiso 'Upload')
                        $params = [
                            'access_mode' => 'public', // 🔹 Incluir en la firma
                            'folder'    => $folder,
                            'public_id' => $public_id,
                            'timestamp' => $timestamp
                        ];
                        ksort($params);
                        $strToSign = "";
                        foreach ($params as $k => $v) { $strToSign .= "$k=$v&"; }
                        $strToSign = rtrim($strToSign, "&") . $apiSecret;
                        $signature = sha1($strToSign);

                        $postData['api_key']     = $apiKey;
                        $postData['timestamp']   = $timestamp;
                        $postData['signature']   = $signature;
                        $postData['folder']      = $folder;
                        $postData['public_id']   = $public_id;
                        $postData['access_mode'] = 'public'; // 🔹 Asegurar que sea público
                    }
                    
                    $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/auto/upload");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    $json = json_decode($response, true);
                    if (isset($json['secure_url'])) {
                        $cvPath = $json['secure_url'];
                    } else {
                        $errorMsg = $json['error']['message'] ?? 'Error desconocido';
                        if (strpos($errorMsg, 'actions=["create"]') !== false) {
                            $errorMsg .= " (Tu Clave API no tiene permisos de Upload. Sugerencia: Crea un 'Unsigned Upload Preset' en el panel de Cloudinary y configúralo en .env como CLOUDINARY_UPLOAD_PRESET=tu_preset)";
                        }
                        throw new \Exception("Error Cloudinary: $errorMsg");
                    }
                } else {
                    throw new \Exception("Formato de CLOUDINARY_URL inválido o incompleto.");
                }
            } else {
                // Modo Local / Desarrollo
                $uploadDir = __DIR__ . '/../../public/uploads/cvs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                if (move_uploaded_file($_FILES['url_cv_pdf']['tmp_name'], $uploadDir . $fileName)) {
                    $cvPath = 'uploads/cvs/' . $fileName;
                } else {
                    throw new \Exception("No se pudo guardar el archivo físicamente en el servidor.");
                }
            }

            // 2. Guardar en DB con match=0, estado en_espera (la IA se activa manualmente)
            $db = Database::getConnection();
            $sql = "INSERT INTO postulaciones (vacante_id, dni, nombre_completo, correo_estudiante, celular, url_cv_pdf, match_porcentaje, estado_postulacion)
                    VALUES (?, ?, ?, ?, ?, ?, 0, 'en_espera')";

            $stmt = $db->prepare($sql);
            $stmt->execute([$vacante_id, $dni, $nombre, $email, $celular, $cvPath]);
            $postulacion_id = $db->lastInsertId();

            header("Location: /vacante/$vacante_id?postulado=success");
            exit;
        } catch (\Exception $e) {
            $msg = substr($e->getMessage(), 0, 500); // 🔹 Evitar headers gigantes que confundan a Cloudflare
            header("Location: /vacante/$vacante_id?postulado=error&msg=" . urlencode("Fallo en la nube: " . $msg));
            exit;
        }
    }

    /**
     * Analiza UN candidato con la IA de n8n y actualiza la BD.
     * POST /api/analizar-cv  { postulacion_id: int }
     */
    public function analizarCv() {
        set_time_limit(180); // Increase time for slow AI
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $postulacionId = intval($input['postulacion_id'] ?? 0);

        if (!$postulacionId) {
            echo json_encode(['success' => false, 'error' => 'ID inválido']);
            return;
        }

        $db = Database::getConnection();

        // Obtener postulación + requisitos de la vacante
        $stmt = $db->prepare("SELECT p.*, v.requisitos_raw, v.titulo_puesto
                               FROM postulaciones p
                               JOIN vacantes v ON p.vacante_id = v.id
                               WHERE p.id = ?");
        $stmt->execute([$postulacionId]);
        $post = $stmt->fetch();

        if (!$post) {
            echo json_encode(['success' => false, 'error' => 'Postulación no encontrada']);
            return;
        }

        // Ruta del CV (Cloudinary URL o Disco local)
        $urlCv = trim($post['url_cv_pdf'] ?? '');
        if (empty($urlCv)) {
            echo json_encode(['success' => false, 'error' => 'Vacío o sin CV adjunto en la base de datos.']);
            return;
        }

        $cvFilePath = filter_var($urlCv, FILTER_VALIDATE_URL) ? $urlCv : __DIR__ . '/../../public/' . $urlCv;
        if (!filter_var($urlCv, FILTER_VALIDATE_URL) && !is_file($cvFilePath)) {
            echo json_encode(['success' => false, 'error' => 'Archivo CV local no encontrado o inválido.']);
            return;
        }

        // Llamar a n8n
        $resultado = ExternalApiService::analizarCvConN8n($cvFilePath, $post['requisitos_raw']);

        if (!$resultado['success']) {
            echo json_encode(['success' => false, 'error' => $resultado['error']]);
            return;
        }

        $puntaje     = $resultado['puntaje'];
        $descripcion = $resultado['descripcion'];

        // Actualizar BD
        $upd = $db->prepare("UPDATE postulaciones 
                              SET match_porcentaje = ?, ia_analisis_descripcion = ?, estado_postulacion = 'IA Realizado'
                              WHERE id = ?");
        $upd->execute([$puntaje, $descripcion, $postulacionId]);

        echo json_encode([
            'success'     => true,
            'puntaje'     => $puntaje,
            'descripcion' => $descripcion,
        ]);
    }

    /**
     * Analiza TODOS los candidatos en_espera de una empresa con la IA.
     * POST /api/analizar-todos  { empresa_id: int }
     */
    public function analizarTodos() {
        set_time_limit(300); // Bulk analysis takes longer
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $empresaId = intval($_SESSION['user_id'] ?? 0);
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT p.*, v.requisitos_raw
                               FROM postulaciones p
                               JOIN vacantes v ON p.vacante_id = v.id
                               WHERE v.empresa_id = ? AND p.estado_postulacion = 'en_espera'");
        $stmt->execute([$empresaId]);
        $pendientes = $stmt->fetchAll();

        $resultados = [];
        $ok = 0;
        $fail = 0;

        foreach ($pendientes as $post) {
            $urlCv = trim($post['url_cv_pdf'] ?? '');
            if (empty($urlCv)) {
                $fail++;
                continue;
            }

            $cvFilePath = filter_var($urlCv, FILTER_VALIDATE_URL) ? $urlCv : __DIR__ . '/../../public/' . $urlCv;

            if (!filter_var($urlCv, FILTER_VALIDATE_URL) && !is_file($cvFilePath)) {
                $fail++;
                continue;
            }

            $resultado = ExternalApiService::analizarCvConN8n($cvFilePath, $post['requisitos_raw']);

            if ($resultado['success']) {
                $upd = $db->prepare("UPDATE postulaciones 
                                     SET match_porcentaje = ?, ia_analisis_descripcion = ?, estado_postulacion = 'IA Realizado'
                                     WHERE id = ?");
                $upd->execute([$resultado['puntaje'], $resultado['descripcion'], $post['id']]);
                $resultados[] = ['id' => $post['id'], 'puntaje' => $resultado['puntaje']];
                $ok++;
            } else {
                $fail++;
            }
        }

        echo json_encode([
            'success'    => true,
            'analizados' => $ok,
            'fallidos'   => $fail,
            'total'      => count($pendientes),
            'resultados' => $resultados,
        ]);
    }

    public function consultDni() {
        $dni  = $_GET['dni'] ?? '';
        $data = ExternalApiService::consultDni($dni);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Consulta el resultado de una postulación por DNI y vacante_id
     * GET /api/postulacion/resultado?dni=...&vacante_id=...
     */
    public function getResultadoDni() {
        header('Content-Type: application/json');
        
        $dni       = $_GET['dni'] ?? '';
        $vacanteId = intval($_GET['vacante_id'] ?? 0);

        if (!$dni || !$vacanteId) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT nombre_completo, match_porcentaje, estado_postulacion, ia_analisis_descripcion 
                               FROM postulaciones 
                               WHERE dni = ? AND vacante_id = ? 
                               ORDER BY fecha_postulacion DESC LIMIT 1");
        $stmt->execute([$dni, $vacanteId]);
        $row = $stmt->fetch();

        if (!$row) {
            echo json_encode(['success' => false, 'error' => 'No se encontró ninguna postulación con ese DNI para esta vacante.']);
            return;
        }

        echo json_encode([
            'success'    => true,
            'nombre'     => $row['nombre_completo'],
            'match'      => floatval($row['match_porcentaje']),
            'estado'     => $row['estado_postulacion'],
            'analisis'   => $row['ia_analisis_descripcion']
        ]);
    }

    /**
     * Actualiza el estado de una postulación (Apto/No Apto).
     * POST /api/postulacion/update-status
     */
    public function updateStatus() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['postulacion_id'] ?? 0);
        $estado = $input['estado'] ?? '';

        if (!$id || !$estado) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE postulaciones SET estado_postulacion = ? WHERE id = ?");
        $success = $stmt->execute([$estado, $id]);

        echo json_encode(['success' => $success]);
    }

    /**
     * Envía un correo al postulante usando un Webhook de n8n (conectado a Gmail).
     * POST /api/postulacion/send-email
     */
    public function sendEmail() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['postulacion_id'] ?? 0);
        $subject = $input['subject'] ?? 'Actualización de tu postulación | StarTraining';
        $message = $input['message'] ?? '';

        if (!$id || !$message) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.correo_estudiante, p.nombre_completo, p.estado_postulacion, v.titulo_puesto 
                               FROM postulaciones p
                               JOIN vacantes v ON p.vacante_id = v.id
                               WHERE p.id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if (!$post) {
            echo json_encode(['success' => false, 'error' => 'Postulación no encontrada']);
            return;
        }

        // Solo permitir enviar si es APTO (como pidió el usuario)
        if ($post['estado_postulacion'] !== 'Apto') {
            echo json_encode(['success' => false, 'error' => 'Solo se puede enviar correos a postulantes marcados como "Apto".']);
            return;
        }

        // Reemplazamos la llamada al webhook por el servicio local de PHP (PHPMailer)
        $result = \App\Services\MailService::sendGmail(
            $post['correo_estudiante'],
            $subject,
            $message,
            $post['nombre_completo']
        );

        if ($result['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
    }
    public function marcarNotificacionLeida() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);
        if ($id) {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE postulaciones SET notificacion_leida = TRUE WHERE id = ?");
            $stmt->execute([$id]);
        }
        echo json_encode(['success' => true]);
    }

    public function marcarTodasNotificacionesLeidas() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $empresaId = intval($_SESSION['user_id'] ?? 0);
        if ($empresaId) {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE postulaciones p
                                   SET notificacion_leida = TRUE 
                                   FROM vacantes v 
                                   WHERE p.vacante_id = v.id AND v.empresa_id = ?");
            $stmt->execute([$empresaId]);
        }
        echo json_encode(['success' => true]);
    }
}
