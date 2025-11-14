<?php

namespace App\Livewire\DatabaseServer;

use App\Livewire\Forms\DatabaseServerForm;
use App\Models\DatabaseServer;
use App\Services\DatabaseConnectionTester;
use Livewire\Component;

class Edit extends Component
{
    public DatabaseServerForm $form;

    public function mount(DatabaseServer $server)
    {
        $this->form->setServer($server);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('status', 'Database server updated successfully!');

        return $this->redirect(route('database-servers.index'), navigate: true);
    }

    public function testConnection(DatabaseConnectionTester $tester)
    {
        $this->form->testConnection($tester);
    }

    public function render()
    {
        return view('livewire.database-server.edit')
            ->layout('components.layouts.app', ['title' => __('Edit Database Server')]);
    }
}
