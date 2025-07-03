<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Kita bungkus semuanya dalam satu komponen Alpine --}}
        <div x-data="{
            events: @js($this->getCalendarEvents()),
        
            init() {
                // Pastikan FullCalendar dimuat sebelum menggunakannya
                if (typeof FullCalendar === 'undefined') {
                    console.error('FullCalendar is not loaded. Trying to load now...');
                    this.loadScript('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js', () => {
                        this.renderCalendar();
                    });
                } else {
                    this.renderCalendar();
                }
            },
        
            renderCalendar() {
                const calendarEl = this.$refs.calendar;
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    initialView: 'dayGridMonth',
                    events: this.events,
                    locale: 'id', // Opsional: Bahasa Indonesia
                });
                calendar.render();
            },
        
            loadScript(url, callback) {
                let script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = url;
                script.onload = callback;
                document.head.appendChild(script);
            }
        }" wire:ignore>
            <div x-ref="calendar"></div>
        </div>
    </x-filament::card>

    {{-- Kita masih butuh CDN, tapi sekarang hanya sebagai fallback jika belum dimuat --}}
    @once
        @push('scripts')
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
        @endpush
    @endonce
</x-filament-widgets::widget>
