/* ============================================================
   Qadamchi docs.js (3.2.0) — vanilla JS, module'siz, defer.
   - Theme toggle (light/dark) + localStorage
   - Reading progress bar
   - TOC scrollspy (IntersectionObserver)
   - Code copy button
   - Docs search filter (index page)
   ============================================================ */
(function () {
    'use strict';

    var THEME_KEY = 'qadamchi-theme';

    // ---- Theme ----
    function currentTheme() {
        var saved = null;
        try { saved = localStorage.getItem(THEME_KEY); } catch (e) {}
        if (saved === 'light' || saved === 'dark') return saved;
        return (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    function applyTheme(t) {
        document.documentElement.setAttribute('data-theme', t);
        var btn = document.querySelector('.theme-toggle');
        if (btn) btn.setAttribute('aria-label', t === 'dark' ? 'Yorug rejimga o\'tish' : 'Qorong\'i rejimga o\'tish');
    }
    function initTheme() {
        applyTheme(currentTheme());
        var btn = document.querySelector('.theme-toggle');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var next = (currentTheme() === 'dark') ? 'light' : 'dark';
            try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
            applyTheme(next);
        });
    }

    // ---- Reading progress ----
    function initProgress() {
        var bar = document.querySelector('.progress-bar');
        if (!bar) return;
        function update() {
            var h = document.documentElement;
            var max = h.scrollHeight - h.clientHeight;
            var p = max > 0 ? (h.scrollTop / max) * 100 : 0;
            bar.style.width = p + '%';
        }
        window.addEventListener('scroll', update, { passive: true });
        update();
    }

    // ---- TOC scrollspy ----
    function initScrollspy() {
        var toc = document.querySelector('.docs-toc');
        if (!toc) return;
        var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
        if (!links.length) return;
        var byId = {};
        links.forEach(function (l) { byId[l.getAttribute('href').slice(1)] = l; });

        var headings = links.map(function (l) {
            return document.getElementById(l.getAttribute('href').slice(1));
        }).filter(Boolean);

        function setActive(id) {
            links.forEach(function (l) { l.classList.remove('active'); });
            if (byId[id]) byId[id].classList.add('active');
        }

        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) setActive(e.target.id);
                });
            }, { rootMargin: '-80px 0px -75% 0px', threshold: 0 });
            headings.forEach(function (h) { io.observe(h); });
        } else {
            window.addEventListener('scroll', function () {
                var top = document.documentElement.scrollTop + 100;
                var cur = headings[0] ? headings[0].id : null;
                headings.forEach(function (h) { if (h.offsetTop <= top) cur = h.id; });
                if (cur) setActive(cur);
            }, { passive: true });
        }
    }

    // ---- Code copy ----
    function initCopy() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('.code-copy') : null;
            if (!btn) return;
            var block = btn.closest('.code');
            var codeEl = block ? block.querySelector('pre code') : null;
            if (!codeEl) return;
            var text = codeEl.textContent;
            var done = function () {
                var label = btn.querySelector('span');
                var prev = label ? label.textContent : '';
                btn.classList.add('copied');
                if (label) label.textContent = 'Nusxalandi';
                setTimeout(function () {
                    btn.classList.remove('copied');
                    if (label) label.textContent = prev || 'Nusxa';
                }, 1300);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done).catch(fallbackCopy(text, done));
            } else {
                fallbackCopy(text, done)();
            }
        });
    }
    function fallbackCopy(text, done) {
        return function () {
            try {
                var ta = document.createElement('textarea');
                ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select();
                document.execCommand('copy'); document.body.removeChild(ta);
            } catch (e) {}
            done();
        };
    }

    // ---- Search filter (index) ----
    function initSearch() {
        var input = document.getElementById('docs-search');
        if (!input) return;
        var cards = Array.prototype.slice.call(document.querySelectorAll('.docs-card'));
        var cats = Array.prototype.slice.call(document.querySelectorAll('.docs-cat'));
        var noRes = document.querySelector('.docs-no-results');
        function filter() {
            var q = input.value.trim().toLowerCase();
            var visible = 0;
            cards.forEach(function (c) {
                var title = (c.getAttribute('data-title') || '').toLowerCase();
                var desc = (c.getAttribute('data-desc') || '').toLowerCase();
                var show = q === '' || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
                c.classList.toggle('is-hidden', !show);
                if (show) visible++;
            });
            cats.forEach(function (cat) {
                var hasVisible = cat.querySelectorAll('.docs-card:not(.is-hidden)').length > 0;
                cat.classList.toggle('is-hidden', !hasVisible);
            });
            if (noRes) noRes.classList.toggle('show', q !== '' && visible === 0);
        }
        input.addEventListener('input', filter);
    }

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        initTheme();
        initProgress();
        initScrollspy();
        initCopy();
        initSearch();
    });
})();