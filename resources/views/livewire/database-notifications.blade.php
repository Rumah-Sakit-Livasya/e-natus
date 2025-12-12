@php
    $notifications = $this->getNotifications();
    $unreadNotificationsCount = $this->getUnreadNotificationsCount();
@endphp

<div @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }} @endif class="flex">
    <x-filament::icon-button :badge="$unreadNotificationsCount ?: null" color="gray" icon="heroicon-o-bell" icon-size="lg" :label="__('filament-panels::layout.actions.open_database_notifications.label')"
        x-data="{}" x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
        class="fi-topbar-database-notifications-btn" />

    <x-filament-notifications::database.modal :notifications="$notifications" :unread-notifications-count="$unreadNotificationsCount" />

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::database.echo :channel="$broadcastChannel" />
    @endif
</div>
