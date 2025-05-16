<x-filament-panels::page>
    {{--  <p id="enableNotificationButton"></p>
    <div class="d-flex">
        <x-filament::button wire:click="enableFireBaseNotification">
            @lang('forms.fields.enable_safari_notification')
        </x-filament::button>
    </div>  --}}
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-2">
            <x-filament::button wire:click="submit" size="xl">
                @lang('forms.actions.send')
            </x-filament::button>
        </div>

    </form>
</x-filament-panels::page>
