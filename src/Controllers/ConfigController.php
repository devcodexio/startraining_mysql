<?php

namespace App\Controllers;

use App\Models\ConfigModel;

class ConfigController {
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $model = new ConfigModel();
        
        foreach ($_POST as $key => $value) {
            $model->set($key, $value);
        }
        
        // Handle logo upload
        if (isset($_FILES['logo_sitio']) && $_FILES['logo_sitio']['error'] === 0) {
            try {
                $timestamp = time();
                $ext = pathinfo($_FILES['logo_sitio']['name'], PATHINFO_EXTENSION);
                $logoUrl = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['logo_sitio']['tmp_name'],
                    'main_logo_' . $timestamp . '.' . $ext,
                    'startraining/branding',
                    'main_logo_' . $timestamp,
                    'branding'
                );
                $model->set('logo_sitio', $logoUrl);
            } catch (\Exception $e) {
                // Ignore upload errors to avoid breaking config save
            }
        }
        
        header('Location: /admin/config?success=1');
        exit;
    }
}
