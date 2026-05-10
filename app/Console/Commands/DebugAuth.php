<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

final class DebugAuth extends Command
{
    protected $signature = 'debug:auth {email?} {password?}';
    protected $description = 'Debug auth setup';

    public function handle(): int
    {
        // Show users
        $users = User::select('id', 'email', 'password')->get();
        $this->info('=== Users ===');
        foreach ($users as $u) {
            $this->line("ID: {$u->id} | Email: {$u->email} | PW prefix: " . substr($u->password, 0, 15));
        }

        // Show OAuth clients
        $clients = Client::all(['id', 'name', 'grant_types', 'revoked', 'secret']);
        $this->info('=== OAuth Clients ===');
        foreach ($clients as $c) {
            $grants = is_array($c->grant_types) ? implode(',', $c->grant_types) : $c->grant_types;
            $this->line("ID: {$c->id} | Name: {$c->name} | Grants: {$grants} | Revoked: {$c->revoked}");
        }

        // Test password if provided
        $email    = $this->argument('email');
        $password = $this->argument('password');

        if ($email && $password) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User not found: {$email}");
            } else {
                $check = Hash::check($password, $user->password);
                $this->info("Hash::check result: " . ($check ? 'TRUE' : 'FALSE'));
                $this->line("Password hash: " . $user->password);
            }
        }

        return self::SUCCESS;
    }
}
