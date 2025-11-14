<?php

namespace App\Livewire\DatabaseServer;

use App\Livewire\Forms\DatabaseServerForm;
use App\Services\DatabaseConnectionTester;
use Livewire\Component;

class Create extends Component
{
    public DatabaseServerForm $form;

    public function save()
    {
        $this->form->store();

        session()->flash('status', 'Database server created successfully!');

        return $this->redirect(route('database-servers.index'), navigate: true);
    }

    public function testConnection(DatabaseConnectionTester $tester)
    {
        $this->form->testConnection($tester);
    }

    public function render()
    {
        return view('livewire.database-server.create')
            ->layout('components.layouts.app', ['title' => __('Create Database Server')]);
    }
}
