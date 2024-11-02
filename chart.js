const ctx = document.getElementById('barChart').getContext('2d');

// Membuat gradien warna
const gradient = ctx.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(75, 192, 192, 0.8)');
gradient.addColorStop(1, 'rgba(153, 102, 255, 0.8)');

const barChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep'],
        datasets: [{
            label: 'Data Sample',
            data: [97, 98, 99, 101, 102],
            backgroundColor: gradient, // Menggunakan gradien untuk efek warna
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        },
        // Menambahkan bayangan di level plugin
        animation: {
            onComplete: () => {
                ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
                ctx.shadowBlur = 15;
                ctx.shadowOffsetX = 10;
                ctx.shadowOffsetY = 10;
            }
        }
    }
});
