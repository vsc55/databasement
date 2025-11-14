<div>
    <div class="mx-auto max-w-4xl">
        <flux:heading size="xl" class="mb-2">{{ __('Edit Database Server') }}</flux:heading>
        <flux:subheading class="mb-6">{{ __('Update your database server configuration') }}</flux:subheading>

        <x-card class="space-y-6">
            @include('livewire.database-server._form', [
                'form' => $form,
                'submitLabel' => 'Update Database Server',
                'isEdit' => true,
            ])
        </x-card>
    </div>
</div>
