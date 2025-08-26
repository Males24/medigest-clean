@extends('layouts.dashboard')
@section('title', __('messages.specialties.edit'))

@section('content')
<div class="max-w-[1430px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900">@lang('messages.specialties.edit')</h1>
      <p class="text-sm text-gray-500 mt-1">@lang('messages.specialties.edit_subtitle')</p>
    </div>
    <a href="{{ route('admin.especialidades.index') }}" class="text-gray-700 hover:text-gray-900 hover:underline">@lang('messages.nav.back')</a>
  </div>

  <form action="{{ route('admin.especialidades.update', $especialidade) }}" method="POST" enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 space-y-6">
    @csrf @method('PUT')

    {{-- Nome --}}
    <div>
      <x-form.input
        id="nome"
        name="nome"
        :label="__('messages.specialties.name')"
        :placeholder="__('messages.specialties.name_ph')"
        icon="components.icons.admin.book"
        value="{{ old('nome', $especialidade->nome) }}"
        class="block text-sm font-medium text-gray-700 mb-1"
      />
      @error('nome') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Capa (upload OU URL) --}}
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label for="cover" class="block text-sm font-medium text-gray-700 mb-1">Capa (upload)</label>
        <input type="file" id="cover" name="cover" accept="image/*"
               class="block w-full text-sm border border-gray-300 rounded-lg file:mr-3 file:px-3 file:py-2 file:border-0 file:bg-gray-100 file:rounded-md">
        <p class="text-xs text-gray-500 mt-1">Se carregar um ficheiro, substitui a capa atual (se existir).</p>
        @error('cover') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="cover_path" class="block text-sm font-medium text-gray-700 mb-1">Capa (URL ou caminho)</label>
        <input type="text" id="cover_path" name="cover_path" value="{{ old('cover_path', $especialidade->cover_path) }}"
               placeholder="/cardiologia.jpg ou https://…"
               class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm focus:border-emerald-600 focus:ring-emerald-600">
        <p class="text-xs text-gray-500 mt-1">Deixa vazio para limpar. Upload tem prioridade sobre este campo.</p>
        @error('cover_path') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Pré-visualização da capa atual --}}
    @php $cover = $especialidade->cover_url; @endphp
    @if($cover)
      <div class="pt-2">
        <div class="text-sm text-gray-700 mb-1">Capa atual</div>
        <img src="{{ $cover }}" alt="Capa de {{ $especialidade->nome }}" class="w-full max-w-sm rounded-lg border border-gray-200">
      </div>
    @endif

    <div class="flex items-center gap-3 pt-2">
      <button class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">@lang('messages.actions.save_changes')</button>
      <a href="{{ route('admin.especialidades.index') }}" class="text-gray-700 hover:text-gray-900 hover:underline">@lang('messages.actions.cancel')</a>
    </div>
  </form>
</div>
@endsection
