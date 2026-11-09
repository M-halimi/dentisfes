@php($size = $size ?? 18)
<svg @class(['directional-icon' => $name === 'arrow']) width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('arrow') <path d="M5 12h14M13 6l6 6-6 6"/> @break
        @case('check') <path d="m5 12 4 4L19 6"/> @break
        @case('calendar') <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/> @break
        @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
        @case('phone') <path d="M21 16.5v2a1.7 1.7 0 0 1-1.85 1.7 16.8 16.8 0 0 1-7.34-2.61 16.5 16.5 0 0 1-5.1-5.1A16.8 16.8 0 0 1 4.1 5.14 1.7 1.7 0 0 1 5.8 3.3h2a1.7 1.7 0 0 1 1.7 1.46c.1.77.29 1.52.56 2.24a1.7 1.7 0 0 1-.38 1.8L8.83 9.63a13.6 13.6 0 0 0 5.1 5.1l.83-.83a1.7 1.7 0 0 1 1.8-.38c.72.27 1.47.46 2.24.56A1.7 1.7 0 0 1 21 16.5Z"/> @break
        @case('pin') <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/> @break
        @case('heart') <path d="M20.8 8.8c0 5.3-8.8 10.3-8.8 10.3S3.2 14.1 3.2 8.8A4.5 4.5 0 0 1 12 6.5a4.5 4.5 0 0 1 8.8 2.3Z"/> @break
        @case('shield') <path d="M12 21s8-3.7 8-10V5l-8-3-8 3v6c0 6.3 8 10 8 10Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/> @break
        @case('sparkle') <path d="m12 3 1.2 5.8L19 10l-5.8 1.2L12 17l-1.2-5.8L5 10l5.8-1.2L12 3ZM19 16l.5 2.5L22 19l-2.5.5L19 22l-.5-2.5L16 19l2.5-.5L19 16Z"/> @break
        @case('tooth') <path d="M8.5 4C6.6 2.7 4 3.7 4 6.6c0 3.8 1.8 5.2 2 9.1.1 2.3.9 4.1 2.3 4.1 1.2 0 1.5-2.2 3.7-2.2s2.5 2.2 3.7 2.2c1.4 0 2.2-1.8 2.3-4.1.2-3.9 2-5.3 2-9.1 0-2.9-2.6-3.9-4.5-2.6-1 .7-1.6 1.1-3.5 1.1S9.5 4.7 8.5 4Z"/> @break
        @case('message') <path d="M20 11.5a7.5 7.5 0 0 1-8 7.5 8.4 8.4 0 0 1-3.2-.6L4 20l1.6-3.7A7.2 7.2 0 0 1 4 11.5 7.5 7.5 0 0 1 12 4a7.5 7.5 0 0 1 8 7.5Z"/><path d="M8 12h.01M12 12h.01M16 12h.01"/> @break
        @case('whatsapp') <path d="M20.5 11.7a8.5 8.5 0 0 1-12.7 7.4L3 20.5l1.4-4.7a8.5 8.5 0 1 1 16.1-4.1Z"/><path d="m8.4 7.2.9-.1.9 2.1-.7.9a9 9 0 0 0 3.5 3.5l.9-.7 2.1.9-.1.9c-.1.8-.8 1.4-1.6 1.3-3.7-.5-6.7-3.5-7.2-7.2-.1-.8.5-1.5 1.3-1.6Z"/> @break
        @case('menu') <path d="M4 7h16M4 12h16M4 17h16"/> @break
        @case('close') <path d="m6 6 12 12M18 6 6 18"/> @break
        @case('mail') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/> @break
        @case('sun') <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/> @break
        @case('dashboard') <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/> @break
        @case('users') <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/> @break
        @case('chart') <path d="M4 19V5M4 19h17"/><path d="m7 15 3-4 3 2 5-7"/> @break
        @case('settings') <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.7 1.7-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.04 1.56V22h-2.4v-.2a1.7 1.7 0 0 0-1.04-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-1.7-1.7.06-.06A1.7 1.7 0 0 0 8.4 15a1.7 1.7 0 0 0-1.56-1.04H6v-2.4h.84A1.7 1.7 0 0 0 8.4 10a1.7 1.7 0 0 0-.34-1.88L8 8.06l1.7-1.7.06.06a1.7 1.7 0 0 0 1.88.34 1.7 1.7 0 0 0 1.04-1.56V4h2.4v1.2a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.7 1.7-.06.06A1.7 1.7 0 0 0 19.6 10c.23.6.8.96 1.44.96H22v2.4h-.96A1.7 1.7 0 0 0 19.4 15Z"/> @break
        @case('arrow-up-right') <path d="M7 17 17 7M7 7h10v10"/> @break
        @case('logout') <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/> @break
        @case('bell') <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/> @break
        @case('verified') <path d="m12 3 2 1.5 2.5-.2 1 2.3 2.1 1.3-.7 2.4.7 2.4-2.1 1.3-1 2.3-2.5-.2-2 1.5-2-1.5-2.5.2-1-2.3-2.1-1.3.7-2.4-.7-2.4 2.1-1.3 1-2.3 2.5.2Z"/><path d="m8.5 12 2.2 2.2 4.8-4.8"/> @break
        @case('currency') <path d="M6 4v16M18 4v16M3 8h14a4 4 0 0 1 0 8H7a4 4 0 0 1 0-8h10"/> @break
        @case('trend-up') <path d="m3 17 6-6 4 4 8-8M15 7h6v6"/> @break
        @case('check-circle') <circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/> @break
        @case('plus-circle') <circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/> @break
        @case('download') <path d="M12 3v12M7 10l5 5 5-5M4 21h16"/> @break
    @endswitch
</svg>
