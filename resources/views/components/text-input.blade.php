@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-white border border-slate-200 text-gray-900 focus:border-pindoor-accent focus:ring-pindoor-accent rounded-lg shadow-none transition']) !!}>
