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
        
        header('Location: /admin/config?success=1');
        exit;
    }
}
