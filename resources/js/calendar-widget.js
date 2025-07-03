// Gunakan 'alpine:init' untuk mendaftarkan komponen Alpine
document.addEventListener("alpine:init", () => {
    Alpine.data("projectCalendar", () => ({
        // events: [], // Properti ini tidak lagi diperlukan di sini

        initCalendar(calendarEl, events) {
            // Cek apakah FullCalendar sudah dimuat
            if (typeof FullCalendar === "undefined") {
                console.error("FullCalendar is not loaded");
                return;
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,listWeek",
                },
                initialView: "dayGridMonth",
                events: events,
                // locale: 'id', // Opsional: untuk bahasa Indonesia
            });

            calendar.render();
        },
    }));
});
