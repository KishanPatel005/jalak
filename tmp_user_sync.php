<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::updateOrCreate(
    ['email' => 'kishan7112@gmail.com'],
    [
        'name' => 'Kishan',
        'password' => Hash::make('123')
    ]
);

if ($user) {
    echo "USER_SYNC_SUCCESS: User '{$user->email}' updated with password '123'\n";
} else {
    echo "USER_SYNC_FAILED\n";
}
