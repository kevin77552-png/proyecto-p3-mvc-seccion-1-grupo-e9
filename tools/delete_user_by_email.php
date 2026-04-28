<?php
// script para eliminar un usuario por email desde la app Laravel
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'admin@example.com';

$user = User::where('email', $email)->first();
if (!$user) {
    echo "Usuario con correo {$email} no encontrado.\n";
    exit(0);
}

// mostrar info antes
echo "Encontrado usuario: ID={$user->id}, name={$user->name}, email={$user->email}\n";
$user->delete();

echo "Usuario eliminado correctamente.\n";
