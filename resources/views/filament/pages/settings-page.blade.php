<div class="mt-6">
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    <x-filament-actions::modals />
</div>
