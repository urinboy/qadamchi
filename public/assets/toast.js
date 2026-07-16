/**
 * Qadamchi toast xabarnomalari — yengil, boshqa kutubxonaga bog'lanmagan.
 * Server tomonidan render qilingan .toast'larni avtomatik yopadi (data-auto ms),
 * qo'lda yopish tugmasi ishlaydi, va window.Qadamchi.toast(type, msg, ms) API beradi.
 */
(function () {
    var ICONS = {
        success: 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
        error:   'M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z',
        info:    'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z'
    };

    function container() {
        var c = document.getElementById('toastContainer');
        if (!c) {
            c = document.createElement('div');
            c.className = 'toast-container';
            c.id = 'toastContainer';
            document.body.appendChild(c);
        }
        return c;
    }

    function dismiss(toast) {
        if (!toast || toast.classList.contains('is-leaving')) return;
        toast.classList.add('is-leaving');
        setTimeout(function () { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 220);
    }

    function schedule(toast, ms) {
        setTimeout(function () { dismiss(toast); }, ms || 4000);
    }

    // Server tomonidan render qilingan toastlarni avtomatik yopamiz.
    function boot() {
        var c = document.getElementById('toastContainer');
        if (c) {
            c.querySelectorAll('.toast').forEach(function (t) {
                schedule(t, parseInt(t.getAttribute('data-auto') || '4000', 10));
            });
        }
    }

    document.addEventListener('click', function (e) {
        var cl = e.target.closest('.toast-close');
        if (cl) { e.preventDefault(); dismiss(cl.closest('.toast')); }
    });

    window.Qadamchi = window.Qadamchi || {};
    window.Qadamchi.toast = function (type, message, ms) {
        var t = document.createElement('div');
        t.className = 'toast toast-' + (type || 'info');
        t.setAttribute('role', type === 'error' ? 'alert' : 'status');
        t.innerHTML =
            '<svg viewBox="0 0 24 24"><path d="' + (ICONS[type] || ICONS.info) + '"></path></svg>' +
            '<span class="toast-body">' + message + '</span>' +
            '<button type="button" class="toast-close" aria-label="Yopish">&times;</button>' +
            '<span class="toast-progress"></span>';
        container().appendChild(t);
        schedule(t, ms);
        return t;
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();