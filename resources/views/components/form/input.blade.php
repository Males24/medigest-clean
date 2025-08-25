@props([
  'id',
  'name' => null,
  'type' => 'text',
  'label' => null,
  'icon' => null,            // include path (ex: 'components.icons.admin.user')
  'placeholder' => '',
  'value' => '',
  'toggle' => false,         // true => mostra botão "ver/ocultar"
  'toggleLabel' => null,     // opcional; senão tenta i18n e cai em PT
])

@php
  $hasIcon   = (bool) $icon;
  $hasToggle = filter_var($toggle, FILTER_VALIDATE_BOOLEAN);

  // padding dinâmico
  $padL = $hasIcon   ? 'ps-10' : '';
  $padR = $hasToggle ? 'pe-12' : '';

  // label do toggle (acessibilidade)
  $tLabel = $toggleLabel ?? __('messages.profile.show_hide_password');
  if ($tLabel === 'messages.profile.show_hide_password') {
      $tLabel = 'Mostrar/ocultar password';
  }

  $baseClasses = trim("
    bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full
    {$padL} {$padR} p-2.5 focus:ring-emerald-500 focus:border-emerald-500
  ");
@endphp

@if($label)
  <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900">{{ $label }}</label>
@endif

<div class="relative">
  @if($hasIcon)
    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-gray-500">
      @include($icon)
    </div>
  @endif

  <input
    id="{{ $id }}"
    name="{{ $name ?? $id }}"
    type="{{ $type }}"
    value="{{ old($name ?? $id, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => $baseClasses]) }}
  />

  {{-- Botão ver/ocultar password (opcional) --}}
  @if($hasToggle)
    <button
      type="button"
      class="absolute inset-y-0 end-1.5 my-auto h-9 w-9 grid place-items-center rounded-md
             bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 shadow-sm
             focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40"
      aria-label="{{ $tLabel }}"
      aria-pressed="false"
      data-pwd-toggle
      data-target="{{ $id }}"
    >
      {{-- olho aberto --}}
      <svg data-eye="on" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/><circle cx="12" cy="12" r="3"/>
      </svg>
      {{-- olho riscado --}}
      <svg data-eye="off" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12a21.77 21.77 0 0 1 5.06-6.94"/>
        <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.83 21.83 0 0 1-3.87 5.49"/>
        <path d="M1 1l22 22"/>
      </svg>
    </button>
  @endif
</div>
