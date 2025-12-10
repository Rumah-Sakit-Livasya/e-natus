<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Notification Stats
            </x-slot>

            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Total Notifications</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ $this->getAllNotificationsCount() }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">Unread</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                        {{ $this->getUnreadNotificationsCount() }}</dd>
                </div>
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500">User ID</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ auth()->id() }}</dd>
                </div>
            </dl>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Latest Notifications (Database)
            </x-slot>

            @if ($this->getNotifications()->count() > 0)
                <div class="space-y-2">
                    @foreach ($this->getNotifications() as $notification)
                        <div class="border rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                            <div class="flex justify-between">
                                <div class="flex-1">
                                    <p class="font-medium">{{ $notification->data['title'] ?? 'No title' }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $notification->data['message'] ?? 'No message' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="ml-4">
                                    @if ($notification->read_at)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Read</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Unread</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No notifications found</p>
            @endif
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Livewire Database Notifications Component Test
            </x-slot>

            <p class="text-sm text-gray-600 mb-4">Testing if Livewire component renders:</p>

            @livewire(\Filament\Notifications\Livewire\DatabaseNotifications::class, ['lazy' => false])
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Panel Configuration
            </x-slot>

            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="font-medium inline">Panel ID:</dt>
                    <dd class="inline">{{ \Filament\Facades\Filament::getDefaultPanel()->getId() }}</dd>
                </div>
                <div>
                    <dt class="font-medium inline">Has Database Notifications:</dt>
                    <dd class="inline">
                        {{ \Filament\Facades\Filament::getDefaultPanel()->hasDatabaseNotifications() ? 'Yes' : 'No' }}
                    </dd>
                </div>
                <div>
                    <dt class="font-medium inline">Polling Interval:</dt>
                    <dd class="inline">
                        {{ \Filament\Facades\Filament::getDefaultPanel()->getDatabaseNotificationsPollingInterval() }}
                    </dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>
