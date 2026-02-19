<?php

it('renders the toast container', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('x-data="gooeyToast(', escape: false);
    $view->assertSee('gooey-toast-filter', escape: false);
    $view->assertSee('window.toast', escape: false);
});

it('renders with default config values', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee("gooeyToast(5000, 5, 'dark', 'bottom-center')", escape: false);
    $view->assertSee('bottom:2rem', escape: false);
    $view->assertSee('left:50%', escape: false);
});

it('renders with custom position', function () {
    config(['gooey-toast.position' => 'top-right']);

    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('top:2rem', escape: false);
    $view->assertSee('right:2rem', escape: false);
});

it('renders with custom duration', function () {
    config(['gooey-toast.duration' => 3000]);

    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('gooeyToast(3000,', escape: false);
});

it('renders with custom max toasts', function () {
    config(['gooey-toast.max_toasts' => 3]);

    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee(', 3,', escape: false);
});

it('renders with light theme', function () {
    config(['gooey-toast.theme' => 'light']);

    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee("'light'", escape: false);
});

it('includes the gooey svg filter', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('<filter id="gooey-toast-filter">', escape: false);
    $view->assertSee('feGaussianBlur', escape: false);
    $view->assertSee('feColorMatrix', escape: false);
});

it('includes all toast type color classes', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('.gooey-toast-color-success', escape: false);
    $view->assertSee('.gooey-toast-color-error', escape: false);
    $view->assertSee('.gooey-toast-color-warning', escape: false);
    $view->assertSee('.gooey-toast-color-info', escape: false);
    $view->assertSee('.gooey-toast-color-loading', escape: false);
});

it('includes all entrance animation keyframes', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('@keyframes gooey-toast-enter ', escape: false);
    $view->assertSee('@keyframes gooey-toast-enter-error', escape: false);
    $view->assertSee('@keyframes gooey-toast-enter-warning', escape: false);
    $view->assertSee('@keyframes gooey-toast-spin', escape: false);
    $view->assertSee('@keyframes gooey-toast-exit', escape: false);
});

it('includes the promise api', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('window.toast.promise', escape: false);
    $view->assertSee('toast-update', escape: false);
});

it('includes the theme switcher api', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('window.toast.theme', escape: false);
    $view->assertSee('@toast-theme.window', escape: false);
});

it('includes action button template', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('gooey-toast-action-btn', escape: false);
    $view->assertSee('fireAction', escape: false);
});

it('includes css custom properties for theming', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('--gooey-toast-bg', escape: false);
    $view->assertSee('--gooey-toast-text', escape: false);
    $view->assertSee('.gooey-toast-light', escape: false);
});

it('includes input sanitization in js', function () {
    $view = $this->blade('<x-gooey-toast />');

    $view->assertSee('sanitize(str)', escape: false);
    $view->assertSee('validTypes', escape: false);
    $view->assertSee("String(data.title", escape: false);
});

it('uses x-text for user content and x-html only for internal svgs', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('class="gooey-toast-value" x-text="row.value"');

    $xHtmlCount = substr_count($html, 'x-html=');
    expect($xHtmlCount)->toBe(2); // toast.iconSvg + action.iconSvg (both internal SVGs)
});

it('renders only once per page', function () {
    $html = (string) $this->blade('<x-gooey-toast /><x-gooey-toast />');

    expect(substr_count($html, '<filter id="gooey-toast-filter">'))->toBe(1);
    expect(substr_count($html, 'window.toast = function'))->toBe(1);
    expect(substr_count($html, 'x-data="gooeyToast('))->toBe(1);
});

it('handles all four position options', function () {
    $positions = [
        'bottom-center' => 'bottom:2rem;left:50%',
        'bottom-right' => 'bottom:2rem;right:2rem',
        'top-center' => 'top:2rem;left:50%',
        'top-right' => 'top:2rem;right:2rem',
    ];

    foreach ($positions as $position => $expected) {
        config(['gooey-toast.position' => $position]);
        $view = $this->blade('<x-gooey-toast />');
        $view->assertSee($expected, escape: false);
    }
});

it('includes timer pause and resume methods', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('pauseTimer(toast)');
    expect($html)->toContain('resumeTimer(toast)');
    expect($html)->toContain('@mouseenter="pauseTimer(toast)"');
    expect($html)->toContain('@mouseleave="resumeTimer(toast)"');
});

it('includes destroy lifecycle method', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('destroy()');
});

it('includes custom icon registration api', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('window.toast.registerIcon');
    expect($html)->toContain('toast-register-icon');
    expect($html)->toContain('customIcons');
    expect($html)->toContain('resolveIcon');
});

it('includes promise thenable guard', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain("typeof promise.then !== 'function'");
});

it('pauses timer bar animation on hover via css', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('.gooey-toast-item:hover .gooey-toast-timer-bar');
    expect($html)->toContain('animation-play-state: paused');
});

// --- Progress toast tests ---

it('includes progress bar template', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('gooey-toast-progress');
    expect($html)->toContain('gooey-toast-progress-track');
    expect($html)->toContain('gooey-toast-progress-fill');
    expect($html)->toContain('toast.progress');
});

it('includes progress convenience api', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('window.toast.progress');
});

it('includes progress css styles', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('.gooey-toast-progress');
    expect($html)->toContain('.gooey-toast-progress-track');
    expect($html)->toContain('.gooey-toast-progress-fill');
});

it('activates gooey filter for progress toasts', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('.gooey-toast-has-progress .gooey-toast-wrap');
});

// --- Persistent toast tests ---

it('supports persistent toast option', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('persistent');
    expect($html)->toContain("data.persistent === true");
});

// --- Custom color tests ---

it('supports custom color option', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('toast.color');
    expect($html)->toContain('toastColor(toast)');
});

it('applies custom color to icon and text via inline style', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain("toast.color ? 'color:' + toast.color : ''");
});

it('applies custom color to timer bars', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain("toast.color ? '--timer-color:' + toast.color : ''");
});

// --- Undo toast tests ---

it('includes undo toast api', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('window.toast.undo');
    expect($html)->toContain('toast-undo');
    expect($html)->toContain('addUndo');
});

it('includes undo button in tab', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('gooey-toast-undo-btn');
    expect($html)->toContain('undoAction(toast)');
    expect($html)->toContain('toast.undoCountdown');
});

it('hides chevron for undo toasts', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('!toast.isUndo');
});

it('fires confirm event on undo toast timeout', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('toast.confirmEvent');
    expect($html)->toContain('toast.undoData');
});

it('includes undo interval cleanup in destroy', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('undoIntervals');
    expect($html)->toContain('clearUndoInterval');
});

// --- Update convenience API ---

it('includes update convenience api', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('window.toast.update');
});

// --- Undo event listener on container ---

it('listens for toast-undo events', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('@toast-undo.window="addUndo($event.detail)"');
});

// --- Action button layout ---

it('lays out multiple action buttons side by side', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('.gooey-toast-actions:has(.gooey-toast-action-btn:nth-child(2))');
    expect($html)->toContain('flex: 1 1 0');
});

// --- Action button color ---

it('supports action button color', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain("action.color ? 'background:' + action.color");
});

// --- Action button confirm ---

it('supports action button confirm step', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('action.confirming');
    expect($html)->toContain('gooey-toast-action-confirm');
});

it('includes confirm logic in fireAction', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain("action.confirm && !action.confirming");
    expect($html)->toContain("action.label = 'Sure?'");
});

// --- Vibrate ---

it('supports vibrate option', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('navigator.vibrate');
    expect($html)->toContain('data.vibrate');
});

// --- Message text block ---

it('includes message template', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('gooey-toast-message');
    expect($html)->toContain('toast.message');
});

it('renders message with x-text not x-html', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('class="gooey-toast-message" x-text="toast.message"');
});

it('includes message css styles', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('.gooey-toast-message');
    expect($html)->toContain('word-wrap: break-word');
});

it('includes message in add and updateToast', function () {
    $html = (string) $this->blade('<x-gooey-toast />');

    expect($html)->toContain('data.message ? String(data.message) : null');
    expect($html)->toContain('message: message');
});
