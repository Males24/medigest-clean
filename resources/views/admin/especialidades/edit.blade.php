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

  <form action="{{ route('admin.especialidades.update', $especialidade) }}" method="POST"
        class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200/60 p-6 space-y-6">
    @csrf @method('PUT')

    <div>
      <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">@lang('messages.specialties.name')</label>
      <input id="nome" type="text" name="nome" value="{{ old('nome', $especialidade->nome) }}"
             class="w-full rounded-xl border-gray-300 focus:border-home-medigest-button focus:ring-home-medigest-button" required>
      @error('nome') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
      <button class="px-5 py-2.5 rounded-xl bg-home-medigest text-white hover:bg-home-medigest-hover">@lang('messages.actions.save_changes')</button>
      <a href="{{ route('admin.especialidades.index') }}" class="text-gray-700 hover:text-gray-900 hover:underline">@lang('messages.actions.cancel')</a>
    </div>
  </form>
</div>
@endsection
