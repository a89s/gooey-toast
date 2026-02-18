@once
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gooeyToast', (duration, maxToasts, initialTheme, position) => ({
            toasts: [],
            timers: {},
            undoIntervals: {},
            theme: initialTheme,
            position: position || 'bottom-center',
            prefersReducedMotion: false,
            swipePrevented: false,
            customIcons: {},

            init() {
                this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            },

            destroy() {
                Object.keys(this.timers).forEach(id => {
                    clearTimeout(this.timers[id]);
                });
                Object.keys(this.undoIntervals).forEach(id => {
                    clearInterval(this.undoIntervals[id]);
                });
                this.timers = {};
                this.undoIntervals = {};
            },

            get isStacked() {
                return this.toasts.filter(t => !t.removing).length >= 3;
            },

            get stackDirection() {
                return this.position.startsWith('top') ? 1 : -1;
            },

            get hiddenCount() {
                const active = this.toasts.filter(t => !t.removing).length;
                return active > 3 ? active - 3 : 0;
            },

            typeColors: {
                success: '#22c55e',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6',
                loading: '#888',
            },

            toastColor(toast) {
                return toast.color || this.typeColors[toast.type] || '#888';
            },

            toastStyle(toast) {
                let parts = ['user-select: none'];
                const idx = this.toasts.indexOf(toast);
                const activeToasts = this.toasts.filter(t => !t.removing);
                const frontExpanded = activeToasts.length > 0 && activeToasts[0].expanded;

                if (this.prefersReducedMotion) {
                    if (toast.removing) {
                        return 'display: none';
                    }
                } else {
                    if (toast.removing) {
                        parts.push('animation: gooey-toast-exit 0.25s ease forwards');
                    } else if (!toast.entered && !toast.swiping) {
                        if (!this.isStacked || idx === 0) {
                            parts.push('animation: ' + toast.enterAnimation + ' ' + toast.enterDuration + ' forwards');
                        }
                    }
                }

                if (toast.entered && !toast.swiping && !toast.removing) {
                    parts.push('transition: opacity 0.3s, transform 0.3s');
                }

                if (toast.swiping) {
                    const deltaX = toast.swipeCurrentX - toast.swipeStartX;
                    const opacity = Math.max(0, 1 - Math.abs(deltaX) / 200);
                    parts.push('transform: translateX(' + deltaX + 'px)');
                    parts.push('opacity: ' + opacity);
                    parts.push('transition: none');
                    return parts.join('; ');
                }

                if (this.isStacked && !toast.removing) {
                    if (idx === 0) {
                        parts.push('position: relative');
                        parts.push('z-index: 3');
                    } else if (idx === 1) {
                        const dir = this.stackDirection;
                        parts.push('position: absolute');
                        parts.push('transform: scale(0.94) translateY(' + (8 * dir) + 'px)');
                        parts.push('opacity: ' + (frontExpanded ? 0 : 0.7));
                        parts.push('pointer-events: none');
                        parts.push('z-index: 2');
                    } else if (idx === 2) {
                        const dir = this.stackDirection;
                        parts.push('position: absolute');
                        parts.push('transform: scale(0.88) translateY(' + (16 * dir) + 'px)');
                        parts.push('opacity: ' + (frontExpanded ? 0 : 0.4));
                        parts.push('pointer-events: none');
                        parts.push('z-index: 1');
                    } else {
                        parts.push('display: none');
                    }
                }

                return parts.join('; ');
            },

            enterAnimations: {
                success: 'gooey-toast-enter',
                error: 'gooey-toast-enter-error',
                warning: 'gooey-toast-enter-warning',
                info: 'gooey-toast-enter',
                loading: 'gooey-toast-enter',
            },

            enterDurations: {
                success: '0.4s cubic-bezier(.2,.8,.3,1)',
                error: '0.6s cubic-bezier(.2,.8,.3,1)',
                warning: '0.6s cubic-bezier(.2,.8,.3,1)',
                info: '0.4s cubic-bezier(.2,.8,.3,1)',
                loading: '0.4s cubic-bezier(.2,.8,.3,1)',
            },

            actionIcons: {
                'external-link': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
                'eye': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
                'undo': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>',
                'retry': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>',
                'map-pin': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
                'download': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
                'copy': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
                'trash': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
                'check': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
                'x': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                'image': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
            },

            icons: {
                success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11.5 14.5 16 10"/></svg>',
                error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="16 12 12 8 8 12"/><line x1="12" y1="16" x2="12" y2="8"/></svg>',
                loading: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 2a10 10 0 0 1 10 10" style="animation: gooey-toast-spin 0.8s linear infinite; transform-origin: center;"/></svg>',
            },

            resolveIcon(name) {
                return this.icons[name] || this.customIcons[name] || this.actionIcons[name] || null;
            },

            sanitize(str) {
                const el = document.createElement('div');
                el.textContent = str;
                return el.innerHTML;
            },

            add(data) {
                const validTypes = ['success', 'error', 'warning', 'info', 'loading'];
                const type = validTypes.includes(data.type) ? data.type : 'success';
                const persistent = data.persistent === true;
                const toastDuration = persistent ? 0 : (data.duration !== undefined ? data.duration : duration);

                const details = Array.isArray(data.details)
                    ? data.details.map(d => ({ label: String(d.label || ''), value: String(d.value || '') }))
                    : [];

                const actions = Array.isArray(data.actions)
                    ? data.actions
                        .filter(a => a.label && a.event)
                        .map(a => ({
                            label: String(a.label),
                            event: String(a.event).replace(/[^a-zA-Z0-9\-_:.]/g, ''),
                            data: a.data || {},
                            iconSvg: a.icon && this.actionIcons[a.icon] ? this.actionIcons[a.icon] : null,
                        }))
                    : [];

                let iconSvg = this.icons[type];
                if (data.icon) {
                    const resolved = this.resolveIcon(data.icon);
                    if (resolved) {
                        iconSvg = resolved;
                    }
                }

                const color = data.color ? String(data.color) : null;
                const progress = data.progress !== undefined ? Math.max(0, Math.min(1, Number(data.progress))) : null;

                const toast = {
                    id: data.id || (Date.now() + Math.random()),
                    title: String(data.title || 'Notification'),
                    type: type,
                    iconSvg: iconSvg,
                    details: details,
                    footer: data.footer ? String(data.footer) : null,
                    actions: actions,
                    color: color,
                    progress: progress,
                    persistent: persistent,
                    isUndo: false,
                    confirmEvent: null,
                    undoEvent: null,
                    undoData: null,
                    undoCountdown: 0,
                    expanded: false,
                    collapsing: false,
                    removing: false,
                    entered: this.prefersReducedMotion,
                    duration: toastDuration,
                    timerStartedAt: Date.now(),
                    timerRemaining: toastDuration,
                    timerPaused: false,
                    barCount: 12,
                    enterAnimation: this.enterAnimations[type] || 'gooey-toast-enter',
                    enterDuration: this.enterDurations[type] || '0.4s cubic-bezier(.2,.8,.3,1)',
                    swipeStartX: 0,
                    swipeCurrentX: 0,
                    swiping: false,
                };

                this.toasts.unshift(toast);

                setTimeout(() => { toast.entered = true; }, 700);

                if (this.toasts.length > maxToasts) {
                    this.dismiss(this.toasts[this.toasts.length - 1]);
                }

                if (toastDuration > 0) {
                    this.startTimer(toast, toastDuration);
                }

                return toast.id;
            },

            addUndo(data) {
                const undoDuration = data.duration !== undefined ? data.duration : duration;
                const confirmEvent = data.event ? String(data.event).replace(/[^a-zA-Z0-9\-_:.]/g, '') : null;
                const undoEvent = data.undoEvent ? String(data.undoEvent).replace(/[^a-zA-Z0-9\-_:.]/g, '') : null;

                const id = this.add({
                    id: data.id,
                    type: data.type || 'warning',
                    title: String(data.title || 'Action pending'),
                    icon: data.icon,
                    color: data.color,
                    duration: undoDuration,
                });

                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return id;

                toast.isUndo = true;
                toast.confirmEvent = confirmEvent;
                toast.undoEvent = undoEvent;
                toast.undoData = data.data || {};
                toast.undoCountdown = Math.ceil(undoDuration / 1000);

                this.undoIntervals[id] = setInterval(() => {
                    toast.undoCountdown = Math.max(0, toast.undoCountdown - 1);
                }, 1000);

                return id;
            },

            undoAction(toast) {
                if (toast.undoEvent) {
                    window.dispatchEvent(new CustomEvent(toast.undoEvent, { detail: toast.undoData || {} }));
                }
                this.clearUndoInterval(toast);
                this.dismiss(toast, true);
            },

            clearUndoInterval(toast) {
                if (this.undoIntervals[toast.id]) {
                    clearInterval(this.undoIntervals[toast.id]);
                    delete this.undoIntervals[toast.id];
                }
            },

            updateToast(data) {
                const toast = this.toasts.find(t => t.id === data.id);
                if (!toast) return;

                const validTypes = ['success', 'error', 'warning', 'info', 'loading'];

                if (data.type && validTypes.includes(data.type)) {
                    toast.type = data.type;
                    toast.iconSvg = this.icons[data.type];
                }
                if (data.title) toast.title = String(data.title);

                if (data.icon) {
                    const resolved = this.resolveIcon(data.icon);
                    if (resolved) {
                        toast.iconSvg = resolved;
                    }
                }

                if (data.color !== undefined) {
                    toast.color = data.color ? String(data.color) : null;
                }

                if (data.progress !== undefined) {
                    toast.progress = Math.max(0, Math.min(1, Number(data.progress)));
                }

                if (data.persistent !== undefined) {
                    toast.persistent = data.persistent === true;
                    if (toast.persistent) {
                        this.clearTimer(toast);
                        toast.duration = 0;
                    }
                }

                if (!toast.persistent && data.duration !== undefined) {
                    const newDuration = data.duration;
                    toast.duration = newDuration;
                    toast.timerRemaining = newDuration;

                    if (newDuration > 0) {
                        this.clearTimer(toast);
                        this.startTimer(toast, newDuration);
                    }
                }
            },

            startTimer(toast, ms) {
                toast.timerStartedAt = Date.now();
                toast.timerRemaining = ms;
                toast.timerPaused = false;
                this.timers[toast.id] = setTimeout(() => {
                    if (!toast.expanded) this.dismiss(toast);
                }, ms);
            },

            clearTimer(toast) {
                if (this.timers[toast.id]) {
                    clearTimeout(this.timers[toast.id]);
                    delete this.timers[toast.id];
                }
            },

            pauseTimer(toast) {
                if (!this.timers[toast.id] || toast.timerPaused) return;
                clearTimeout(this.timers[toast.id]);
                delete this.timers[toast.id];
                toast.timerRemaining = Math.max(0, toast.timerRemaining - (Date.now() - toast.timerStartedAt));
                toast.timerPaused = true;
            },

            resumeTimer(toast) {
                if (!toast.timerPaused || toast.expanded || toast.duration <= 0) return;
                if (toast.timerRemaining <= 0) {
                    this.dismiss(toast);
                    return;
                }
                this.startTimer(toast, toast.timerRemaining);
            },

            toggle(toast) {
                if (this.swipePrevented || toast.isUndo) return;

                if (toast.expanded) {
                    toast.expanded = false;
                    toast.collapsing = true;
                    setTimeout(() => { toast.collapsing = false; }, 450);

                    if (toast.duration > 0 && toast.timerRemaining > 0) {
                        this.startTimer(toast, toast.timerRemaining);
                    }
                } else {
                    toast.expanded = true;
                    this.clearTimer(toast);
                    toast.timerPaused = true;
                }
            },

            dismiss(toast, isUserAction) {
                if (toast.isUndo && !isUserAction && toast.confirmEvent) {
                    window.dispatchEvent(new CustomEvent(toast.confirmEvent, { detail: toast.undoData || {} }));
                }
                this.clearTimer(toast);
                this.clearUndoInterval(toast);
                toast.removing = true;
                if (this.prefersReducedMotion) {
                    this.remove(toast.id);
                }
            },

            dismissMostRecent() {
                const toast = this.toasts.find(t => !t.removing);
                if (toast) this.dismiss(toast, true);
            },

            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },

            fireAction(toast, action) {
                window.dispatchEvent(new CustomEvent(action.event, { detail: action.data || {} }));
                this.dismiss(toast, true);
            },

            registerIcon(name, svg) {
                this.customIcons[name] = svg;
            },

            swipeStart(toast, event) {
                toast.swipeStartX = event.touches[0].clientX;
                toast.swipeCurrentX = event.touches[0].clientX;
                toast.swiping = true;
            },

            swipeMove(toast, event) {
                if (!toast.swiping) return;
                toast.swipeCurrentX = event.touches[0].clientX;
                if (Math.abs(toast.swipeCurrentX - toast.swipeStartX) > 10) {
                    this.swipePrevented = true;
                }
            },

            swipeEnd(toast, event) {
                if (!toast.swiping) return;
                const deltaX = toast.swipeCurrentX - toast.swipeStartX;
                toast.swiping = false;

                if (Math.abs(deltaX) > 100) {
                    this.dismiss(toast, true);
                } else {
                    toast.swipeStartX = 0;
                    toast.swipeCurrentX = 0;
                }

                if (this.swipePrevented) {
                    setTimeout(() => { this.swipePrevented = false; }, 300);
                }
            },
        }));
    });

    window.toast = function(options) {
        window.dispatchEvent(new CustomEvent('toast', { detail: options }));
    };

    window.toast.theme = function(theme) {
        window.dispatchEvent(new CustomEvent('toast-theme', { detail: { theme: theme } }));
    };

    window.toast.registerIcon = function(name, svg) {
        window.dispatchEvent(new CustomEvent('toast-register-icon', { detail: { name: name, svg: svg } }));
    };

    window.toast.update = function(id, data) {
        data.id = id;
        window.dispatchEvent(new CustomEvent('toast-update', { detail: data }));
    };

    window.toast.progress = function(titleOrId, value, extra) {
        if (typeof titleOrId === 'string' && value === undefined) {
            var id = Date.now() + Math.random();
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { id: id, type: 'info', title: titleOrId, progress: 0, duration: 0 }
            }));
            return id;
        }
        if (typeof titleOrId === 'string' && typeof value === 'object') {
            var id = Date.now() + Math.random();
            value.id = id;
            value.title = value.title || titleOrId;
            value.progress = value.progress !== undefined ? value.progress : 0;
            value.duration = value.duration !== undefined ? value.duration : 0;
            value.type = value.type || 'info';
            window.dispatchEvent(new CustomEvent('toast', { detail: value }));
            return id;
        }
        var detail = { id: titleOrId, progress: value };
        if (typeof extra === 'string') {
            detail.title = extra;
        } else if (typeof extra === 'object' && extra) {
            Object.assign(detail, extra);
        }
        if (value >= 1 && !detail.type) {
            detail.type = 'success';
        }
        window.dispatchEvent(new CustomEvent('toast-update', { detail: detail }));
        return titleOrId;
    };

    window.toast.undo = function(title, event, data, undoDuration) {
        if (typeof title === 'object') {
            window.dispatchEvent(new CustomEvent('toast-undo', { detail: title }));
            return;
        }
        window.dispatchEvent(new CustomEvent('toast-undo', {
            detail: {
                title: title,
                event: event,
                data: data || {},
                duration: undoDuration,
            }
        }));
    };

    window.toast.promise = function(promise, messages) {
        if (!promise || typeof promise.then !== 'function') {
            console.warn('toast.promise() requires a thenable (Promise)');
            return promise;
        }

        const id = Date.now() + Math.random();

        window.dispatchEvent(new CustomEvent('toast', {
            detail: { id: id, type: 'loading', title: messages.loading, duration: 0 }
        }));

        promise.then(
            (result) => {
                window.dispatchEvent(new CustomEvent('toast-update', {
                    detail: { id: id, type: 'success', title: messages.success }
                }));
                return result;
            },
            (err) => {
                window.dispatchEvent(new CustomEvent('toast-update', {
                    detail: { id: id, type: 'error', title: messages.error }
                }));
                throw err;
            }
        );

        return promise;
    };
</script>
@endonce
