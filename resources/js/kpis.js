import Chart from 'chart.js/auto';

async function loadKpis(){
  const res = await fetch('/api/kpis');
  const data = await res.json();

  // Actualiza tus KPIs si quieres por id/clase aquí

  const ctx = document.getElementById('tt-chart');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.labels,
      datasets: [{
        label: 'Travel Time Reduction',
        data: data.series,
        tension: 0.35,
        fill: true,
        backgroundColor: 'rgba(6, 182, 212, 0.1)',
        borderColor: '#06b6d4',
        pointBackgroundColor: '#06b6d4',
        pointBorderColor: '#0891b2',
        pointHoverBackgroundColor: '#22d3ee',
        pointHoverBorderColor: '#0891b2',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { 
        legend: { display: false } 
      },
      scales: {
        x: { 
          ticks: { color: '#a1a1aa' }, 
          grid: { color: '#27272a' } 
        },
        y: { 
          ticks: { color: '#a1a1aa' }, 
          grid: { color: '#27272a' } 
        },
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', loadKpis);