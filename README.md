# Gooey Toast

A gooey expandable toast notification component for Laravel 10 / 11 / 12.

Features a unique SVG gooey blob animation, expandable detail rows, action buttons with icons, promise toasts, progress toasts, undo countdown, persistent toasts, custom colors, animated timer bars, dark/light theming, and per-type entrance animations. Zero external CSS dependencies — works with any stack.

## Installation

```bash
composer require gooey/toast
```

The package auto-discovers its service provider. No manual registration needed.

Publish the config (optional):

```bash
php artisan vendor:publish --tag=gooey-toast-config
```

## Setup

Add the component to your layout, just before `</body>`:

```blade
<x-gooey-toast />
```

The component requires [Alpine.js](https://alpinejs.dev) (already included if you use Livewire). For non-Livewire projects, include Alpine yourself:

```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
```

### Advanced Placement

If you need styles in `<head>` and scripts before `</body>` separately, use the Blade directives instead of the component:

```blade
<head>
    @gooeyToastStyles
</head>
<body>
    ...
    @gooeyToastScripts
</body>
```

## Usage

### JavaScript

```js
// Simple
toast({ type: 'success', title: 'Saved!' });

// With details and footer
toast({
    type: 'error',
    title: 'Upload failed',
    details: [
        { label: 'File', value: 'report.pdf' },
        { label: 'Error', value: 'File too large' },
    ],
    footer: 'Max file size: 10 MB',
});
```

### Livewire

```php
$this->dispatch('toast', [
    'type'    => 'success',
    'title'   => 'Deployment complete',
    'details' => [
        ['label' => 'Environment', 'value' => 'Production'],
        ['label' => 'Branch',      'value' => 'main'],
    ],
    'footer' => 'https://deploy.example.com/logs/3f8a2c1',
]);
```

## Toast Types

| Type | Color | Entrance |
|------|-------|----------|
| `success` | Green | Smooth slide-up |
| `error` | Red | Slide-up + shake |
| `warning` | Amber | Slide-up + bounce |
| `info` | Blue | Smooth slide-up |
| `loading` | Gray | Smooth slide-up (spinner icon) |

## Progress Toasts

Show a progress bar that updates as work completes:

```js
// Create a progress toast — returns an ID
const id = toast.progress('Uploading photos...');

// Update progress (0 to 1)
toast.progress(id, 0.25);
toast.progress(id, 0.5);
toast.progress(id, 0.75);

// Complete — auto-switches to success type
toast.progress(id, 1, 'Upload complete!');
```

With options:

```js
const id = toast.progress('Processing...', { type: 'warning', color: '#8b5cf6' });
```

### Livewire

```php
// Start
$this->dispatch('toast', [
    'id'       => 'upload-1',
    'type'     => 'info',
    'title'    => 'Uploading...',
    'progress' => 0,
]);

// Update
$this->dispatch('toast-update', [
    'id'       => 'upload-1',
    'progress' => 0.5,
]);

// Complete
$this->dispatch('toast-update', [
    'id'       => 'upload-1',
    'progress' => 1,
    'type'     => 'success',
    'title'    => 'Uploaded!',
]);
```

## Undo Toasts

Show a toast with an inline undo button and countdown. If the user doesn't click undo, a confirm event fires when the countdown expires.

```js
toast.undo('Message deleted', 'confirm-delete', { id: 123 });

// Listen — only fires if NOT undone
window.addEventListener('confirm-delete', (e) => {
    fetch(`/api/messages/${e.detail.id}`, { method: 'DELETE' });
});
```

With more options:

```js
toast.undo({
    title: 'Item archived',
    event: 'confirm-archive',      // fires on countdown expiry
    undoEvent: 'undo-archive',     // fires if user clicks undo (optional)
    data: { id: 456 },
    duration: 8000,                // countdown length in ms
    type: 'info',                  // toast type (default: warning)
});
```

### Livewire

```php
$this->dispatch('toast-undo', [
    'title'    => 'Item deleted',
    'event'    => 'confirm-delete',
    'data'     => ['id' => $id],
    'duration' => 5000,
]);
```

## Persistent Toasts

Toasts that never auto-dismiss. Only removed by the close button, an action button, or swipe.

```js
toast({ type: 'error', title: 'Connection lost', persistent: true });
```

### Livewire

```php
$this->dispatch('toast', [
    'type'       => 'error',
    'title'      => 'Payment failed',
    'persistent' => true,
    'details'    => [
        ['label' => 'Reason', 'value' => 'Card declined'],
    ],
]);
```

## Custom Colors

Override the type color for any toast. Applies to the icon, title, timer bars, and progress bar.

```js
toast({ type: 'success', title: 'VIP Access Granted', color: '#8b5cf6' });
```

Works with all toast types:

```js
toast.undo('Archived', 'confirm-archive', { id: 1 });
toast.progress('Syncing...', { color: '#ec4899' });
toast({ type: 'info', title: 'Custom', color: '#06b6d4', persistent: true });
```

### Livewire

```php
$this->dispatch('toast', [
    'type'  => 'success',
    'title' => 'Branded notification',
    'color' => '#8b5cf6',
]);
```

## Action Buttons

Add clickable buttons to the expanded toast body. Each button dispatches a custom window event and dismisses the toast.

```js
toast({
    type: 'success',
    title: 'Message sent',
    actions: [
        { label: 'Undo', icon: 'undo', event: 'undo-send' },
        { label: 'View', icon: 'external-link', event: 'view-message', data: { id: 123 } },
    ],
});

// Listen for the event
window.addEventListener('view-message', (e) => {
    console.log(e.detail); // { id: 123 }
});
```

### Available Icons

`external-link`, `eye`, `undo`, `retry`, `map-pin`, `download`, `copy`, `trash`, `check`, `x`, `image`

You can also register custom icons:

```js
toast.registerIcon('star', '<svg viewBox="0 0 24 24">...</svg>');

// Use in toasts
toast({ type: 'success', title: 'Favorited!', icon: 'star' });
```

## Promise Toasts

Show a loading spinner that resolves to success or error automatically:

```js
toast.promise(fetch('/api/save'), {
    loading: 'Saving...',
    success: 'Saved!',
    error: 'Failed to save',
});
```

Returns the original promise so you can chain `.then()`.

## Updating Toasts

Update any toast by ID:

```js
toast.update('my-toast', { title: 'New title', type: 'success' });
```

### Livewire

```php
$this->dispatch('toast-update', [
    'id'    => 'my-toast',
    'title' => 'Updated!',
    'type'  => 'success',
]);
```

## Theme Switching

The component supports `dark` and `light` themes. Set the default in config, or switch at runtime:

```js
toast.theme('light');
toast.theme('dark');
```

## Configuration

```php
// config/gooey-toast.php
return [
    'position'   => 'bottom-center', // bottom-center, bottom-right, top-center, top-right
    'duration'   => 5000,            // auto-dismiss ms (0 = never)
    'max_toasts' => 5,
    'theme'      => 'dark',          // dark, light
];
```

## Full Options Reference

```js
toast({
    type: 'success',             // success, error, warning, info, loading
    title: 'Notification',       // required
    id: 'my-id',                 // optional — use for updates
    details: [                   // optional — expandable rows
        { label: 'Key', value: 'Value' },
    ],
    footer: 'Footer text',      // optional
    actions: [                   // optional — buttons in expanded body
        { label: 'Click me', icon: 'check', event: 'my-event', data: {} },
    ],
    duration: 5000,              // optional — override config duration
    persistent: false,           // optional — never auto-dismiss
    color: '#8b5cf6',            // optional — override type color
    progress: 0.5,               // optional — show progress bar (0 to 1)
    icon: 'star',                // optional — override type icon (registered name)
});
```

## Publishing Views

To customize the component markup:

```bash
php artisan vendor:publish --tag=gooey-toast-views
```

Views will be published to `resources/views/vendor/gooey-toast/`.

## License

MIT — see [LICENSE](LICENSE).
