<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugAuth extends Command
{
    protected $signature = 'auth:debug
                            {username : The username to inspect}
                            {--test= : A password to test against the stored hash}
                            {--reset= : Reset the password to this value}';

    protected $description = 'Debug or reset a user\'s legacy password hash';

    public function handle(): int
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();

        if (! $user) {
            $this->error("No user found with username: {$username}");
            return 1;
        }

        $this->info("User found:");
        $this->table(
            ['id', 'username', 'nameFirst', 'nameLast', 'isActive', 'isHidden'],
            [[
                $user->id,
                $user->username,
                $user->nameFirst,
                $user->nameLast,
                $user->isActive ? 'yes' : 'no',
                $user->isHidden ? 'yes' : 'no',
            ]]
        );

        // Raw DB values — bypasses all model casting so we see exactly what MySQL has
        $raw = DB::selectOne('SELECT isActive, isHidden, password FROM users WHERE username = ?', [$username]);
        $this->line('');
        $this->line('<fg=cyan>Raw DB values (no casting):</>');
        $this->line('  isActive = <fg=yellow>' . var_export($raw->isActive, true) . '</>  (login query requires exactly: 1)');
        $this->line('  isHidden = <fg=yellow>' . var_export($raw->isHidden, true) . '</>  (login query requires exactly: 0)');
        $this->line('  password = <fg=yellow>' . $raw->password . '</>');
        $this->line('');

        // Simulate the exact login query
        $loginUser = User::where('username', $username)->where('isActive', 1)->where('isHidden', 0)->first();
        if ($loginUser) {
            $this->info('✓ Login query (isActive=1, isHidden=0) FOUND the user.');
        } else {
            $this->error('✗ Login query (isActive=1, isHidden=0) returned NULL — this is why login fails.');
        }
        $this->line('');
        $this->line('<fg=yellow>Stored hash:</> ' . $user->password);
        $this->line('');

        // Test a password against the stored hash
        if ($test = $this->option('test')) {
            $generated = User::legacyPasswordHash($username, $test);
            $match = $generated === $user->password;

            $this->line('<fg=yellow>Generated hash for "' . $test . '":</> ' . $generated);
            $this->line('');

            if ($match) {
                $this->info('✓ Password MATCHES the stored hash.');
            } else {
                $this->error('✗ Password does NOT match the stored hash.');
                $this->line('');
                $this->line('<fg=gray>Tip: The hash is derived from strtolower(username), so username');
                $this->line('case matters. The username used for hashing was: <fg=yellow>' . strtolower($username) . '</>');
            }
        }

        // Reset the password to a new value
        if ($reset = $this->option('reset')) {
            if (! $this->confirm("Reset password for '{$username}' to '{$reset}'?")) {
                $this->line('Aborted.');
                return 0;
            }

            $newHash = User::legacyPasswordHash($username, $reset);
            $user->password = $newHash;
            $user->save();

            $this->info("Password reset successfully.");
            $this->line('<fg=yellow>New hash:</> ' . $newHash);
        }

        return 0;
    }
}
