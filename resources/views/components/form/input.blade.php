@props([
  'id',
  'name' => null,
  'type' => 'text',
  'label' => null,
  'icon' => null,       // caminho do include do ícone (ex: 'components.icons.admin.user')
  'placeholder' => '',
  'value' => '',
])

@if($label)
  <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900">{{ $label }}</label>
@endif

<div class="relative">
  @if($icon)
    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
      @include($icon)
    </div>
  @endif

  <input
    id="{{ $id }}"
    name="{{ $name ?? $id }}"
    type="{{ $type }}"
    value="{{ old($name ?? $id, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->merge([
      'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full ' . ($icon ? 'ps-10 p-2.5' : 'p-2.5') . ' focus:ring-blue-500 focus:border-blue-500'
    ]) }}
  />
</div>
