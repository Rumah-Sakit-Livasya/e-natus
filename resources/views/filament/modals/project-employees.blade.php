@php
    $toWhatsAppNumber = function (?string $phone): ?string {
        if (! filled($phone)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    };
@endphp

<div class="space-y-4">
    @if ($employees->isEmpty())
        <div class="rounded-xl border border-gray-700/80 bg-gray-900/40 px-4 py-8 text-center">
            <p class="text-sm text-gray-300">Belum ada pegawai yang ditugaskan pada project ini.</p>
        </div>
    @else
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-gray-400">
                Total pegawai:
                <span class="font-semibold text-gray-200">{{ $employees->count() }}</span>
            </p>
            <p class="text-xs text-gray-500">Tip: klik nomor HP untuk chat WhatsApp cepat.</p>
        </div>

        <div class="space-y-3 md:hidden">
            @foreach ($employees as $employee)
                <article class="rounded-xl border border-gray-700/80 bg-gray-900/30 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-100">{{ $employee->user?->name ?? '-' }}</h4>
                            <p class="text-xs text-gray-500">ID: #{{ $employee->id }}</p>
                        </div>
                        @if (filled($employee->position))
                            <span class="inline-flex items-center rounded-md border border-primary-500/20 bg-primary-500/10 px-2 py-0.5 text-[11px] font-medium text-primary-300">
                                {{ $employee->position }}
                            </span>
                        @endif
                    </div>

                    <dl class="mt-3 grid grid-cols-[88px_1fr] gap-y-2 text-xs">
                        <dt class="text-gray-500">Nomor HP</dt>
                        <dd class="text-gray-300">
                            @php($waNumber = $toWhatsAppNumber($employee->phone))
                            @if (filled($waNumber))
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                                    class="hover:text-primary-400 transition-colors">
                                    {{ $employee->phone }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </dd>

                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-300 break-all">
                            @if (filled($employee->user?->email))
                                <a href="mailto:{{ $employee->user?->email }}" class="hover:text-primary-400 transition-colors">
                                    {{ $employee->user?->email }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </dd>

                    </dl>
                </article>
            @endforeach
        </div>

        <div class="hidden overflow-hidden rounded-xl border border-gray-700/80 bg-gray-900/30 md:block">
            <div class="overflow-x-auto">
                <table class="min-w-[760px] w-full divide-y divide-gray-700/80">
                    <caption class="sr-only">Daftar pegawai yang ditugaskan per project</caption>
                    <thead class="bg-gray-800/60">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-300">Nama</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-300">Nomor HP</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-300">Email</th>
                            <th scope="col" class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-300">Jabatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/80">
                        @foreach ($employees as $employee)
                            <tr class="transition-colors hover:bg-gray-800/40 focus-within:bg-gray-800/40">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-gray-100">
                                        {{ $employee->user?->name ?? '-' }}
                                    </div>
                                    <div class="mt-0.5 text-[11px] text-gray-500">ID: #{{ $employee->id }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">
                                    @php($waNumber = $toWhatsAppNumber($employee->phone))
                                    @if (filled($waNumber))
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
                                            class="hover:text-primary-400 transition-colors">
                                            {{ $employee->phone }}
                                        </a>
                                    @else
                                        <span class="inline-flex rounded-md border border-gray-700 px-2 py-0.5 text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">
                                    @if (filled($employee->user?->email))
                                        <a href="mailto:{{ $employee->user?->email }}"
                                            class="block max-w-[240px] truncate hover:text-primary-400 transition-colors"
                                            title="{{ $employee->user?->email }}">
                                            {{ $employee->user?->email }}
                                        </a>
                                    @else
                                        <span class="inline-flex rounded-md border border-gray-700 px-2 py-0.5 text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-300">
                                    @if (filled($employee->position))
                                        {{ $employee->position }}
                                    @else
                                        <span class="inline-flex rounded-md border border-gray-700 px-2 py-0.5 text-xs text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
