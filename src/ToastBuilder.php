<?php

declare(strict_types=1);

namespace A89s\GooeyToast;

class ToastBuilder
{
    protected Toast $toast;
    protected array $data = [];

    public function __construct(Toast $toast, ?string $title = null, ?string $type = null)
    {
        $this->toast = $toast;
        $this->data = [
            'title' => $title ?? '',
            'type' => $type ?? 'info',
        ];
    }

    /**
     * Set the toast title
     */
    public function title(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Set the toast type
     */
    public function type(string $type): self
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * Set the toast message text
     */
    public function message(?string $message): self
    {
        if ($message) {
            $this->data['message'] = $message;
        }
        return $this;
    }

    /**
     * Set details rows
     */
    public function details(array $details): self
    {
        $this->data['details'] = $details;
        return $this;
    }

    /**
     * Add a detail row
     */
    public function detail(string $label, string $value): self
    {
        $this->data['details'][] = ['label' => $label, 'value' => $value];
        return $this;
    }

    /**
     * Set footer text
     */
    public function footer(string $footer): self
    {
        $this->data['footer'] = $footer;
        return $this;
    }

    /**
     * Set action buttons
     */
    public function actions(array $actions): self
    {
        $this->data['actions'] = $actions;
        return $this;
    }

    /**
     * Add an action button
     */
    public function action(string $label, string $event, ?string $icon = null, ?string $color = null, bool $confirm = false): self
    {
        $action = [
            'label' => $label,
            'event' => $event,
            'icon' => $icon,
        ];
        if ($color) {
            $action['color'] = $color;
        }
        if ($confirm) {
            $action['confirm'] = true;
        }
        $this->data['actions'][] = $action;
        return $this;
    }

    /**
     * Enable vibration when toast appears
     */
    public function vibrate(array|bool $pattern = true): self
    {
        $this->data['vibrate'] = $pattern;
        return $this;
    }

    /**
     * Set duration (ms)
     */
    public function duration(int $duration): self
    {
        $this->data['duration'] = $duration;
        return $this;
    }

    /**
     * Make toast persistent (no auto-dismiss)
     */
    public function persistent(): self
    {
        $this->data['persistent'] = true;
        return $this;
    }

    /**
     * Set custom color
     */
    public function color(string $color): self
    {
        $this->data['color'] = $color;
        return $this;
    }

    /**
     * Set avatar image URL
     */
    public function avatar(string $avatar): self
    {
        $this->data['avatar'] = $avatar;
        return $this;
    }

    /**
     * Set avatar size
     */
    public function avatarSize(string $size): self
    {
        $this->data['avatarSize'] = $size;
        return $this;
    }

    /**
     * Set the toast ID for updates
     */
    public function id(string $id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Send the toast (to session for next page load)
     */
    public function send(): Toast
    {
        $this->toast->add($this->data);
        return $this->toast->send();
    }

    /**
     * Get toast data as array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Convert to array (magic method)
     */
    public function __toArray(): array
    {
        return $this->toArray();
    }
}
