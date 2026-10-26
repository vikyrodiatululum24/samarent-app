@php
    $record = $getRecord();
@endphp

@if ($record)
    <div class="py-1" x-on:click.stop="">
        <x-filament::button
            :color="$record->is_submitted_customer ? 'success' : 'danger'"
            size="sm"
            :icon="$record->is_submitted_customer ? 'heroicon-m-check-circle' : 'heroicon-m-minus-circle'"
            x-on:click.stop=""
            wire:click.stop="mountTableAction('toggle_submit_customer', '{{ $record->getKey() }}')"
        >
            {{ $record->is_submitted_customer ? 'Submit' : 'Belum Submit' }}
        </x-filament::button>
    </div>
@endif
