@props(['notifications', 'pollingInterval' => null])

<div @if ($pollingInterval = $getPollingInterval()) wire:poll.{{ $pollingInterval }} @endif class="fi-topbar-notifications flex">
    <x-filament::icon-button color="gray" icon="heroicon-o-bell" icon-alias="panels::topbar.notifications.icon"
        icon-size="lg" :label="__('filament-panels::layout.actions.notifications.label')" :indicator="$unreadNotificationsCount" indicator-color="primary"
        {{ $attributes->class(['fi-topbar-notifications-trigger']) }}
        x-on:click="
            $dispatch('open-modal', {
                id: 'database-notifications',
            })
        " />

    <x-filament::modal :actions="$this->getDatabaseNotificationActions()" :heading="__('filament-panels::layout.actions.notifications.label')" icon="heroicon-o-bell"
        icon-alias="panels::topbar.notifications.modal.icon" id="database-notifications"
        :wire:key="$this->getId() . '.notifications'" class="fi-notifications-modal">
        @if (count($notifications))
            <x-filament::notifications :notifications="$notifications" />

            @if ($hasUnreadNotifications)
                <x-slot name="footerActions">
                    {{ $this->markAllDatabaseNotificationsAsReadAction() }}
                </x-slot>
            @endif
        @else
            <x-filament::empty-state :actions="$this->getDatabaseNotificationActions()" :description="__('filament-panels::layout.notifications.empty.description')" :heading="__('filament-panels::layout.notifications.empty.heading')" icon="heroicon-o-bell"
                icon-alias="panels::topbar.notifications.empty-state.icon" />
        @endif
    </x-filament::modal>
</div>
php artisan vendor:publish --tag=filament-views
