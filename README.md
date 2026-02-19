# Gooey Toast

> ⚠️ **Work in progress** — This package is under active development and not production-ready yet. APIs may change. Use at your own risk.

A gooey expandable toast notification component for Laravel 10 / 11 / 12.

Features a unique SVG gooey blob animation, expandable detail rows, action buttons with icons and colors, promise toasts, progress toasts, undo countdown, persistent toasts, custom colors, message text blocks, vibration, action confirmation, animated timer bars, dark/light theming, and per-type entrance animations. Zero external CSS dependencies — works with any stack.

## Screenshots

Screenshot 1 - [Click here](.github/screenshots/1.png)  
Screenshot 2 - [Click here](.github/screenshots/2.png)  
Screenshot 3 - [Click here](.github/screenshots/3.png)  
Screenshot 4 - [Click here](.github/screenshots/4.png)  
Screenshot 5 - [Click here](.github/screenshots/5.png)  
Screenshot 6 - [Click here](.github/screenshots/6.png)  

## Installation

```bash
composer require a89s/gooey-toast
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

## PHP API (No Livewire Required)

Use the `GooeyToast` facade to trigger toasts directly from PHP code. Works with any Laravel project, no Livewire needed:

```php
use A89s\GooeyToast\Facades\GooeyToast;

// Quick toasts
GooeyToast::success('Saved!');
GooeyToast::error('Something went wrong');
GooeyToast::warning('Warning message');
GooeyToast::info('Information');

// Fluent builder
GooeyToast::make('Title', 'success')
    ->message('Additional message')
    ->send();

// With avatar
GooeyToast::make('New message', 'info')
    ->avatar('/avatars/user.jpg')
    ->avatarSize('32px')
    ->send();

// Full configuration
GooeyToast::make('Deployment complete', 'success')
    ->details([
        ['label' => 'Environment', 'value' => 'Production'],
        ['label' => 'Branch', 'value' => 'main'],
    ])
    ->footer('https://deploy.example.com/logs/123')
    ->duration(5000)
    ->send();
```

### Available Methods

| Method | Description |
|--------|-------------|
| `make($title, $type)` | Create a new toast |
| `success($title, $message)` | Quick success toast |
| `error($title, $message)` | Quick error toast |
| `warning($title, $message)` | Quick warning toast |
| `info($title, $message)` | Quick info toast |

### Builder Methods

| Method | Description |
|--------|-------------|
| `title($title)` | Set toast title |
| `type($type)` | Set toast type |
| `message($message)` | Set message text block |
| `details($array)` | Set detail rows |
| `detail($label, $value)` | Add a detail row |
| `footer($text)` | Set footer text |
| `actions($array)` | Set action buttons |
| `action($label, $event, $icon, $color, $confirm)` | Add an action button |
| `vibrate($pattern)` | Enable vibration (mobile) |
| `duration($ms)` | Set duration |
| `persistent()` | Make persistent |
| `color($color)` | Set custom color |
| `avatar($url)` | Set avatar image |
| `avatarSize($size)` | Set avatar size |
| `id($id)` | Set toast ID |
| `send()` | Send the toast |

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

## Message Text

Display a plain text message block in the expanded body. Unlike `details` (key-value rows), `message` renders as a natural paragraph — ideal for chat notifications, alerts, or any freeform content.

```js
toast({
    type: 'info',
    title: 'Melissa',
    avatar: '/avatars/melissa.jpg',
    message: 'Please visit HR when you get a chance 👋',
    footer: '1h ago',
});
```

### Livewire

```php
$this->dispatch('toast', [
    'type'    => 'info',
    'title'   => 'Melissa',
    'avatar'  => '/avatars/melissa.jpg',
    'message' => 'Please visit HR when you get a chance 👋',
    'footer'  => '1h ago',
]);
```

### PHP API

```php
GooeyToast::make('Melissa', 'info')
    ->avatar('/avatars/melissa.jpg')
    ->message('Please visit HR when you get a chance 👋')
    ->footer('1h ago')
    ->send();
```

## Action Buttons

Add clickable buttons to the expanded toast body. Each button dispatches a custom window event and dismisses the toast. When there are 2+ buttons they display side by side; a single button stays full width.

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

### Action Button Colors

Give individual action buttons a custom color. The button gets a tinted background and colored text.

```js
toast({
    type: 'info',
    title: 'Incoming call',
    persistent: true,
    actions: [
        { label: 'Accept', icon: 'check', event: 'accept', color: '#22c55e' },
        { label: 'Decline', icon: 'x', event: 'decline', color: '#ef4444' },
    ],
});
```

### Action Confirmation

Add `confirm: true` to an action to require a two-step click. The first click changes the label to "Sure?" for 3 seconds. If clicked again, the event fires. If not, the label reverts.

```js
toast({
    type: 'warning',
    title: 'Delete account',
    actions: [
        { label: 'Delete', icon: 'trash', event: 'delete-account', color: '#ef4444', confirm: true },
    ],
});
```

### Available Icons

`external-link`, `eye`, `undo`, `retry`, `reply`, `map-pin`, `download`, `copy`, `trash`, `check`, `x`, `image`

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

## User Avatars

Display a user avatar image instead of the default type icon:

```js
toast({
    title: 'New message from John',
    avatar: '/avatars/john.jpg',
    type: 'info'
});
```

### Custom Avatar Size

Override the default size (18px) with any CSS unit:

```js
toast({
    title: 'Welcome back!',
    avatar: '/avatars/user.png',
    avatarSize: '32px',
    type: 'success'
});
```

## Vibration

Trigger a device vibration (mobile only) when a toast appears. Uses the [Vibration API](https://developer.mozilla.org/en-US/docs/Web/API/Vibration_API) — silently ignored on desktop browsers.

```js
// Simple vibration (200ms)
toast({ type: 'info', title: 'Alert!', vibrate: true });

// Custom pattern (vibrate, pause, vibrate)
toast({ type: 'info', title: 'Incoming call', vibrate: [200, 100, 200] });
```

### PHP API

```php
GooeyToast::make('Incoming call', 'info')
    ->vibrate([200, 100, 200])
    ->send();
```

## Full Options Reference

```js
toast({
    type: 'success',             // success, error, warning, info, loading
    title: 'Notification',       // required
    id: 'my-id',                 // optional — use for updates
    message: 'Text block',      // optional — plain text message
    avatar: '/path/to/image.jpg', // optional — avatar image URL
    avatarSize: '32px',         // optional — avatar size (default: 18px)
    details: [                   // optional — expandable rows
        { label: 'Key', value: 'Value' },
    ],
    footer: 'Footer text',      // optional
    actions: [                   // optional — buttons in expanded body
        { label: 'Click me', icon: 'check', event: 'my-event', data: {}, color: '#22c55e', confirm: false },
    ],
    duration: 5000,              // optional — override config duration
    persistent: false,           // optional — never auto-dismiss
    color: '#8b5cf6',            // optional — override type color
    progress: 0.5,              // optional — show progress bar (0 to 1)
    icon: 'star',                // optional — override type icon (registered name)
    vibrate: true,               // optional — vibrate on mobile (true or [ms] pattern)
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
