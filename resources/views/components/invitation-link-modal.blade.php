@props(['title', 'message', 'doneAction' => null, 'doneLabel' => __('Done')])

<x-modal wire:model="showCopyModal" class="backdrop-blur" title="{{ $title }}" separator persistent>
    <p class="mb-4">{{ $message }}</p>

    <div class="flex gap-2" x-data="{ copied: false }">
        <x-input
            wire:model="invitationUrl"
            readonly
            class="flex-1"
        />
        <x-button
            icon="o-clipboard-document"
            class="btn-primary"
            x-on:click="
                const text = $wire.invitationUrl;
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text);
                } else {
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    textArea.remove();
                }
                copied = true;
                setTimeout(() => copied = false, 2000);
                $wire.success('{{ __('Link copied to clipboard!') }}', null, 'toast-bottom');
            "
            tooltip="{{ __('Copy') }}"
        />
    </div>

    <x-slot:actions>
        @if($doneAction)
            <x-button label="{{ $doneLabel }}" wire:click="{{ $doneAction }}" class="btn-primary" />
        @else
            <x-button label="{{ $doneLabel }}" @click="$wire.showCopyModal = false" class="btn-primary" />
        @endif
    </x-slot:actions>
</x-modal>
