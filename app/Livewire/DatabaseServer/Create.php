<?php

namespace App\Livewire\DatabaseServer;

use App\Models\DatabaseServer;
use App\Services\DatabaseConnectionTester;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $host = '';

    #[Validate('required|integer|min:1|max:65535')]
    public int $port = 3306;

    #[Validate('required|string|in:mysql,postgresql,mariadb,sqlite')]
    public string $database_type = 'mysql';

    #[Validate('required|string|max:255')]
    public string $username = '';

    #[Validate('required|string|max:255')]
    public string $password = '';

    #[Validate('nullable|string|max:255')]
    public ?string $database_name = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    public ?string $connectionTestMessage = null;

    public bool $connectionTestSuccess = false;

    public bool $testingConnection = false;

    public function save()
    {
        $validated = $this->validate();

        DatabaseServer::create($validated);

        session()->flash('status', 'Database server created successfully!');

        return $this->redirect(route('database-servers.index'), navigate: true);
    }

    public function testConnection(DatabaseConnectionTester $tester)
    {
        $this->testingConnection = true;
        $this->connectionTestMessage = null;

        // Validate only the connection-related fields
        try {
            $this->validate([
                'host' => 'required|string|max:255',
                'port' => 'required|integer|min:1|max:65535',
                'database_type' => 'required|string|in:mysql,postgresql,mariadb,sqlite',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->testingConnection = false;
            $this->connectionTestSuccess = false;
            $this->connectionTestMessage = 'Please fill in all required connection fields.';

            return;
        }

        $result = $tester->test([
            'database_type' => $this->database_type,
            'host' => $this->host,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password,
            'database_name' => $this->database_name,
        ]);

        $this->connectionTestSuccess = $result['success'];
        $this->connectionTestMessage = $result['message'];
        $this->testingConnection = false;
    }

    public function render()
    {
        return view('livewire.database-server.create')
            ->layout('components.layouts.app', ['title' => __('Create Database Server')]);
    }
}
