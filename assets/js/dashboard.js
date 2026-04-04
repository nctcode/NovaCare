/**
 * Dashboard JS - NovaCare Smart Hospital
 * Chart.js charts + AOS init + Counter animation
 */

// ---- AOS-like scroll animation ----
function initAOS() {
    const elements = document.querySelectorAll('[data-aos]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = entry.target.getAttribute('data-aos-delay') || 0;
                setTimeout(() => {
                    entry.target.classList.add('aos-animate');
                }, parseInt(delay));
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => observer.observe(el));
}

// ---- Counter Animation ----
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    });
}

// ---- Counter on scroll ----
function initCounterOnScroll() {
    const counters = document.querySelectorAll('.counter');
    if (counters.length === 0) return;

    let animated = false;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !animated) {
                animated = true;
                animateCounters();
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(c => observer.observe(c));
}

// ---- Navbar scroll effect ----
function initNavbarScroll() {
    const nav = document.querySelector('.nav-public');
    if (!nav) return;
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
}

// ---- Chart.js: Appointment Status (Doughnut) ----
function initAppointmentChart(pending, confirmed, completed, cancelled) {
    const ctx = document.getElementById('appointmentChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xác nhận', 'Đã xác nhận', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [pending, confirmed, completed, cancelled],
                backgroundColor: ['#f59e0b', '#0ea5e9', '#22c55e', '#ef4444'],
                borderWidth: 0,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Poppins', size: 12 }
                    }
                }
            }
        }
    });
}

// ---- Chart.js: Monthly Patients (Bar) ----
function initPatientChart(labels, data) {
    const ctx = document.getElementById('patientChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Bệnh nhân mới',
                data: data,
                backgroundColor: 'rgba(14, 165, 233, 0.7)',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { family: 'Poppins', size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Poppins', size: 11 } }
                }
            }
        }
    });
}

// ---- Init everything ----
document.addEventListener('DOMContentLoaded', function() {
    initAOS();
    initCounterOnScroll();
    initNavbarScroll();
});
