
// Sidebar toggle (mobile)
const hexToRgba = (hex, alpha = 1) => {
  const sanitized = hex.replace('#', '');
  const bigint = parseInt(sanitized, 16);
  const r = (bigint >> 16) & 255;
  const g = (bigint >> 8) & 255;
  const b = bigint & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
    sidebar.addEventListener('click', (e) => {
      if (e.target === sidebar) sidebar.classList.remove('show');
    });
  }

  // Theme toggle with localStorage
  const themeToggle = document.getElementById('themeToggle');
  const root = document.documentElement;
  const stored = localStorage.getItem('theme');
  if (stored) root.setAttribute('data-bs-theme', stored);
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const current = root.getAttribute('data-bs-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme', next);
    });
  }

  // Chart.js setup
  if (typeof window.Chart !== 'undefined') {
    const styles = getComputedStyle(document.documentElement);
    const textColor = styles.getPropertyValue('--text-secondary').trim() || '#adb5d3';
    const gridColor = 'rgba(255, 255, 255, 0.08)';
    const accentPrimary = styles.getPropertyValue('--accent-primary').trim() || '#e14eca';
    const accentSecondary = styles.getPropertyValue('--accent-secondary').trim() || '#1d8cf8';
    const accentSuccess = styles.getPropertyValue('--accent-success').trim() || '#00f2c3';
    const accentWarning = styles.getPropertyValue('--accent-warning').trim() || '#ff8d72';

    const gaugeCanvases = document.querySelectorAll('.chart-gauge');
    gaugeCanvases.forEach((canvas, index) => {
      const totalRaw = Number(canvas.dataset.total || 0);
      const capacity = Number(canvas.dataset.capacity || 100) || 100;
      const caption = canvas.dataset.caption || 'Capacity';
      const summary = canvas.dataset.summary || `${totalRaw} / ${capacity}`;
      const preferLow = (canvas.dataset.direction || '').toLowerCase() === 'down';
      const labelMode = (canvas.dataset.label || 'percent').toLowerCase();
      const labelSuffix = canvas.dataset.labelSuffix || (labelMode === 'percent' ? '%' : '');

      const percentageRaw = capacity > 0 ? (totalRaw / capacity) * 100 : 0;
      const clampedPercentage = Math.max(0, Math.min(percentageRaw, 150));
      const displayPercentage = Math.round(Math.max(0, Math.min(percentageRaw, 999)));

      const COLORS = {
        good: 'rgb(140, 214, 16)',
        warning: 'rgb(239, 198, 0)',
        danger: 'rgb(231, 24, 49)',
      };
      const backgroundGrey = 'rgba(255, 255, 255, 0.08)';
      const gaugeColorFor = (perc) => {
        if (perc >= 80) return COLORS.good;
        if (perc >= 60) return COLORS.warning;
        return COLORS.danger;
      };
      const colorBasis = preferLow
        ? Math.max(0, Math.min(100, 100 - Math.min(clampedPercentage, 100)))
        : Math.max(0, Math.min(100, clampedPercentage));
      const activeColor = gaugeColorFor(colorBasis);

      const gaugeLabelPlugin = {
        id: `gaugeLabel${index}`,
        afterDraw(chart) {
          const { ctx, chartArea } = chart;
          const arc = chart.getDatasetMeta(0).data[0];
          if (!arc || !chartArea) return;

          const radius = arc.outerRadius || 0;
          const centerX = (chartArea.left + chartArea.right) / 2;
          const baseY = chartArea.bottom - radius * 0.45;

          ctx.save();
          ctx.textAlign = 'center';
          ctx.fillStyle = activeColor;
          ctx.font = '700 22px Roboto, sans-serif';
          const centralText = labelMode === 'count'
            ? `${Math.round(totalRaw)}${labelSuffix}`
            : `${displayPercentage}${labelSuffix}`;
          ctx.fillText(centralText, centerX, baseY - 14);

          ctx.fillStyle = textColor || '#adb5d3';
          ctx.font = '500 11px Roboto, sans-serif';
          ctx.fillText(caption, centerX, baseY + 4);

          ctx.font = '400 10px Roboto, sans-serif';
          ctx.fillText(summary, centerX, baseY + 18);
          ctx.restore();
        },
      };

      const filled = Math.max(0, Math.min(percentageRaw, 100));
      const remaining = Math.max(0, 100 - filled);

      new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels: ['Value', 'Remaining'],
          datasets: [
            {
              data: [filled, remaining],
              backgroundColor: [
                activeColor,
                backgroundGrey,
              ],
              borderWidth: 0,
              circumference: 180,
              rotation: -90,
              cutout: '75%',
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          aspectRatio: 2,
          plugins: {
            legend: { display: false },
            tooltip: {
              enabled: false,
            },
          },
        },
        plugins: [gaugeLabelPlugin],
      });
    });

    const overviewCanvas = document.getElementById('overviewChart');
    if (overviewCanvas) {
      new Chart(overviewCanvas, {
        type: 'line',
        data: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [
            {
              label: 'Sessions',
              data: [320, 380, 330, 410, 460, 520, 480],
              tension: 0.4,
              borderWidth: 3,
              borderColor: accentPrimary,
              pointBackgroundColor: accentPrimary,
              fill: {
                target: 'origin',
                above: hexToRgba(accentPrimary, 0.12),
              },
            },
            {
              label: 'Signups',
              data: [45, 52, 48, 60, 70, 85, 72],
              tension: 0.4,
              borderWidth: 3,
              borderColor: accentSecondary,
              pointBackgroundColor: accentSecondary,
              fill: {
                target: 'origin',
                above: hexToRgba(accentSecondary, 0.12),
              },
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: {
                color: textColor,
                font: { size: 12 },
              },
              grid: {
                display: false,
              },
            },
            y: {
              ticks: {
                color: textColor,
                font: { size: 12 },
              },
              grid: {
                color: gridColor,
              },
            },
          },
          plugins: {
            legend: {
              labels: {
                color: textColor,
                usePointStyle: true,
                padding: 18,
              },
            },
            tooltip: {
              backgroundColor: 'rgba(23, 25, 45, 0.85)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: 'rgba(255, 255, 255, 0.1)',
              borderWidth: 1,
            },
          },
        },
      });
    }

    const channelsCanvas = document.getElementById('channelsChart');
    if (channelsCanvas) {
      new Chart(channelsCanvas, {
        type: 'doughnut',
        data: {
          labels: ['Organic', 'Paid', 'Referral', 'Email'],
          datasets: [
            {
              data: [42, 28, 18, 12],
              backgroundColor: [
                accentPrimary,
                accentSecondary,
                accentSuccess,
                accentWarning,
              ],
              borderWidth: 0,
              hoverOffset: 8,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: '65%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                color: textColor,
                usePointStyle: true,
              },
            },
            tooltip: {
              backgroundColor: 'rgba(23, 25, 45, 0.85)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: 'rgba(255, 255, 255, 0.1)',
              borderWidth: 1,
            },
          },
        },
      });
    }
  }
});
