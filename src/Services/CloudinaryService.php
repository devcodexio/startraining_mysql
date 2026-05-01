<?php

namespace App\Services;

class CloudinaryService {
    
    /**
     * Sube un archivo a Cloudinary o de forma local si no hay configuracion.
     * 
     * @param string $tmpFilePath Ruta temporal del archivo ($_FILES['...']['tmp_name'])
     * @param string $fileName Nombre original o generado con extension (ej: logo_ruc.png)
     * @param string $folder Carpeta en Cloudinary (ej: 'startraining/logos')
     * @param string $publicId Identificador a usar (sin extension)
     * @param string $localFolder Carpeta local fallback si no hay cloudinary (ej: 'logos')
     * @return string URL segura de la imagen (o ruta local ej: '/uploads/logos/xyz.png')
     * @throws \Exception
     */
    public static function uploadFile($tmpFilePath, $fileName, $folder, $publicId, $localFolder) {
        $cloudinaryUrl = $_ENV['CLOUDINARY_URL'] ?? '';
        $uploadPreset  = $_ENV['CLOUDINARY_UPLOAD_PRESET'] ?? '';

        if (!empty($cloudinaryUrl)) {
            preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches);
            if (count($matches) === 4) {
                $apiKey     = $matches[1];
                $apiSecret  = $matches[2];
                $cloudName  = trim($matches[3]);
                $timestamp  = time();
                
                $mime = mime_content_type($tmpFilePath);
                $resourceType = strpos($mime, 'image/') === 0 ? 'image' : 'auto';

                $postData = [
                    'file' => new \CURLFile($tmpFilePath, $mime, $fileName),
                ];

                if (!empty($uploadPreset)) {
                    $postData['upload_preset'] = $uploadPreset;
                    $postData['folder']        = $folder;
                    $postData['public_id']     = $publicId;
                } else {
                    $params = [
                        'access_mode' => 'public',
                        'folder'    => $folder,
                        'public_id' => $publicId,
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
                    $postData['public_id']   = $publicId;
                    $postData['access_mode'] = 'public';
                }
                
                $ch = curl_init("https://api.cloudinary.com/v1_1/$cloudName/$resourceType/upload");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                
                $json = json_decode($response, true);
                if (isset($json['secure_url'])) {
                    return $json['secure_url'];
                } else {
                    $errorMsg = $json['error']['message'] ?? 'Error desconocido';
                    throw new \Exception("Error Cloudinary: $errorMsg");
                }
            } else {
                throw new \Exception("Formato de CLOUDINARY_URL inválido.");
            }
        } else {
            // Fallback a Local
            $uploadDir = __DIR__ . '/../../public/uploads/' . $localFolder . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($tmpFilePath, $uploadDir . $fileName)) {
                return '/uploads/' . $localFolder . '/' . $fileName;
            } else {
                throw new \Exception("No se pudo guardar el archivo localmente.");
            }
        }
    }
}
