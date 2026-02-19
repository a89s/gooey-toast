<?php

declare(strict_types=1);

namespace A89s\GooeyToast;

use Illuminate\Support\Facades\Request;

class Toast
{
    protected array $toasts = [];

    public function __construct()
    {
        $this->loadFromSession();
    }

    /**
     * Load toasts from session
     */
    protected function loadFromSession(): void
    {
        if (session()->has('gooey-toasts')) {
            $this->toasts = session('gooey-toasts', []);
            session()->forget('gooey-toasts');
        }
    }

    /**
     * Create a new toast message
     */
    public function make(?string $title = null, ?string $type = null): ToastBuilder
    {
        return new ToastBuilder($this, $title, $type);
    }

    /**
     * Quick success toast
     */
    public function success(string $title, ?string $message = null): self
    {
        return $this->make($title, 'success')->message($message)->send();
    }

    /**
     * Quick error toast
     */
    public function error(string $title, ?string $message = null): self
    {
        return $this->make($title, 'error')->message($message)->send();
    }

    /**
     * Quick warning toast
     */
    public function warning(string $title, ?string $message = null): self
    {
        return $this->make($title, 'warning')->message($message)->send();
    }

    /**
     * Quick info toast
     */
    public function info(string $title, ?string $message = null): self
    {
        return $this->make($title, 'info')->message($message)->send();
    }

    /**
     * Add a toast to the collection
     */
    public function add(array $data): self
    {
        $this->toasts[] = $data;
        return $this;
    }

    /**
     * Send toasts to the session (for next page load)
     */
    public function send(): self
    {
        if (!empty($this->toasts)) {
            session()->flash('gooey-toasts', $this->toasts);
            $this->toasts = [];
        }
        return $this;
    }

    /**
     * Get toasts for rendering (AJAX response)
     */
    public function get(): array
    {
        return $this->toasts;
    }

    /**
     * Check if there are toasts
     */
    public function hasToasts(): bool
    {
        return !empty($this->toasts);
    }

    /**
     * Render toasts as JSON for AJAX responses
     */
    public function render(): ?string
    {
        if (empty($this->toasts)) {
            return null;
        }

        return json_encode($this->toasts);
    }
}
