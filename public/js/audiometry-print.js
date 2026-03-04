(function () {
    function normalizeData(values) {
        return (values || []).map(function (value) {
            if (value === null || value === undefined || value === '' || value === '-') {
                return null;
            }
            var number = Number(value);
            return Number.isNaN(number) ? null : number;
        });
    }

    function createChart(el, airData, boneData, color, airPointStyle, bonePointStyle, bonePointRotation) {
        if (!el || typeof Chart === 'undefined') {
            return;
        }

        var options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.parsed.y + ' dB';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#777', lineWidth: 1 },
                    ticks: { font: { size: 10 } }
                },
                y: {
                    reverse: true,
                    min: -10,
                    max: 120,
                    title: {
                        display: true,
                        text: 'Hearing level in Decibels (dB)',
                        font: { size: 10, weight: 'bold' },
                        color: '#222'
                    },
                    ticks: { stepSize: 10, font: { size: 10 } },
                    grid: { color: '#777', lineWidth: 1 }
                }
            }
        };

        new Chart(el, {
            type: 'line',
            data: {
                labels: ['250', '500', '1000', '2000', '3000', '4000', '6000', '8000'],
                datasets: [
                    {
                        data: normalizeData(airData),
                        borderColor: color,
                        backgroundColor: color,
                        pointStyle: airPointStyle,
                        borderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 6,
                        tension: 0,
                        spanGaps: true
                    },
                    {
                        data: normalizeData(boneData),
                        borderColor: color,
                        backgroundColor: color,
                        pointStyle: bonePointStyle,
                        pointRotation: bonePointRotation,
                        borderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 6,
                        tension: 0,
                        spanGaps: true
                    }
                ]
            },
            options: options
        });
    }

    window.addEventListener('DOMContentLoaded', function () {
        var payload = document.getElementById('audiometry-payload');
        if (!payload) {
            return;
        }

        var rightAirData = [];
        var leftAirData = [];
        var rightBoneData = [];
        var leftBoneData = [];

        try {
            rightAirData = JSON.parse(payload.dataset.rightAir || '[]');
            leftAirData = JSON.parse(payload.dataset.leftAir || '[]');
            rightBoneData = JSON.parse(payload.dataset.rightBone || '[]');
            leftBoneData = JSON.parse(payload.dataset.leftBone || '[]');
        } catch (e) {
            rightAirData = [];
            leftAirData = [];
            rightBoneData = [];
            leftBoneData = [];
        }

        createChart(document.getElementById('rightEarChart'), rightAirData, rightBoneData, 'red', 'circle', 'triangle', 270);
        createChart(document.getElementById('leftEarChart'), leftAirData, leftBoneData, 'blue', 'crossRot', 'triangle', 90);

        setTimeout(function () {
            window.print();
        }, 500);
    });
})();
