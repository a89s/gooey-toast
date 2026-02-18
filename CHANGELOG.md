# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-01

### Added
- Toast notifications with gooey blob expand/collapse animation
- Five toast types: `success`, `error`, `warning`, `info`, `loading`
- Expandable detail rows with label/value pairs
- Footer text support
- Action buttons with predefined icon set
- Promise toast API (`toast.promise()`) for async operations
- Dark and light theme support with runtime switching (`toast.theme()`)
- Animated timer bars with gooey filter effect
- Type-specific entrance animations (shake for error, bounce for warning)
- Configurable position, duration, max toasts, and theme
- XSS protection (type validation, string coercion, event name sanitization)
- Livewire event dispatch support
- Vanilla JS API (`window.toast()`)
- `@once` deduplication for multiple component includes
- Laravel auto-discovery support
- Publishable config and views
