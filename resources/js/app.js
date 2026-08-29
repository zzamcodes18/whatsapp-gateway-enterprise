import Alpine from 'alpinejs';
import axios from 'axios';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.axios = axios;

// Configure default axios headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// ==========================================================================
// SOFT PAGE TRANSITIONS & INSTANT TOP LOADING BAR CONTROLLER
// ==========================================================================

const progressBar = {
    el: null,
    timer: null,
    init() {
        if (!document.getElementById('top-progress-bar')) {
            const bar = document.createElement('div');
            bar.id = 'top-progress-bar';
            document.body.appendChild(bar);
            this.el = bar;
        } else {
            this.el = document.getElementById('top-progress-bar');
        }
    },
    start() {
        if (!this.el) this.init();
        if (this.timer) clearInterval(this.timer);
        this.el.style.opacity = '1';
        this.el.style.width = '15%';

        let progress = 15;
        this.timer = setInterval(() => {
            if (progress < 85) {
                progress += Math.random() * 12;
                this.el.style.width = `${progress}%`;
            }
        }, 120);
    },
    finish() {
        if (!this.el) return;
        if (this.timer) clearInterval(this.timer);
        this.el.style.width = '100%';
        setTimeout(() => {
            this.el.style.opacity = '0';
            setTimeout(() => {
                this.el.style.width = '0%';
            }, 250);
        }, 150);
    }
};

window.progressBar = progressBar;

// Soft Link Interception for Smooth Feedback
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link || !link.href) return;
    
    // Ignore external links, downloads, new tabs, anchors, and javascript calls
    if (
        link.target === '_blank' || 
        link.hasAttribute('download') || 
        link.href.startsWith('javascript:') ||
        link.href.startsWith('#') ||
        link.origin !== window.location.origin
    ) {
        return;
    }

    progressBar.start();
});

// Finish progress bar once page loaded or restored from cache
window.addEventListener('pageshow', () => {
    progressBar.finish();
});

// ==========================================================================
// GLOBAL ALPINE STORES
// ==========================================================================

Alpine.store('toast', {
    items: [],
    show(message, type = 'success', duration = 3500) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => {
            renderLucide();
        }, 50);
        setTimeout(() => this.remove(id), duration);
    },
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    }
});

Alpine.store('dialog', {
    isOpen: false,
    title: 'Konfirmasi Tindakan',
    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmText: 'Ya, Lanjutkan',
    cancelText: 'Batal',
    type: 'danger', // danger, warning, primary, success
    callback: null,

    open(options = {}) {
        this.title = options.title || 'Konfirmasi Tindakan';
        this.message = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        this.confirmText = options.confirmText || 'Ya, Lanjutkan';
        this.cancelText = options.cancelText || 'Batal';
        this.type = options.type || 'danger';
        this.callback = options.onConfirm || null;
        this.isOpen = true;
        setTimeout(() => {
            renderLucide();
        }, 50);
    },

    confirm() {
        this.isOpen = false;
        if (typeof this.callback === 'function') {
            this.callback();
        }
    },

    cancel() {
        this.isOpen = false;
        this.callback = null;
    }
});

// Helper shortcuts on window
window.$toast = {
    success: (msg) => Alpine.store('toast').show(msg, 'success'),
    error: (msg) => Alpine.store('toast').show(msg, 'error'),
    info: (msg) => Alpine.store('toast').show(msg, 'info'),
    warning: (msg) => Alpine.store('toast').show(msg, 'warning'),
};

window.$confirm = (options) => Alpine.store('dialog').open(options);

window.copyToClipboard = (text, successMsg = 'Tersalin ke clipboard!') => {
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        window.$toast.success(successMsg);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        window.$toast.error('Gagal menyalin text.');
    });
};

// Global Lucide Icon Render Helper
export const renderLucide = () => {
    try {
        createIcons({ icons });
    } catch (e) {
        console.warn('Lucide icon render error:', e);
    }
};

window.renderLucide = renderLucide;

document.addEventListener('DOMContentLoaded', () => {
    progressBar.init();
    renderLucide();
});
document.addEventListener('alpine:initialized', renderLucide);
document.addEventListener('alpine:navigated', renderLucide);

Alpine.start();
