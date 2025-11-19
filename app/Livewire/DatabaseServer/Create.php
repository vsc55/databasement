<?php

namespace App\Livewire\DatabaseServer;

use App\Livewire\Forms\DatabaseServerForm;
use Livewire\Component;

class Create extends Component
{
    public DatabaseServerForm $form;

    public function save()
    {
        if ($this->form->store()) {
            session()->flash('status', 'Database server created successfully!');

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
        return view('livewire.database-server.create')
            ->layout('components.layouts.app', ['title' => __('Create Database Server')]);
    }
}
