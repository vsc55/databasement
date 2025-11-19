<?php

namespace App\Livewire\DatabaseServer;

use App\Livewire\Forms\DatabaseServerForm;
use App\Models\DatabaseServer;
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
        if ($this->form->update()) {
            session()->flash('status', 'Database server updated successfully!');

            return $this->redirect(route('database-servers.index'), navigate: true);
        }

        return false;
    }

    public function testConnection()
    {
        $this->form->testConnection();
    }

    public function render()
    {
        return view('livewire.database-server.edit')
            ->layout('components.layouts.app', ['title' => __('Edit Database Server')]);
    }
}
