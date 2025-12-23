<div>
    <label for="{{ $name }}" class="block mb-2 text-sm font-semibold text-gray-800">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <div class="relative">
        <select id="{{ $name }}" name="{{ $name }}" @if ($required) required @endif
            class="peer h-12 w-full rounded-lg bg-white text-gray-900 text-sm
                   border border-gray-200 shadow-sm pl-3 pr-10
                   focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30
                   placeholder-gray-400 transition
                   @error($name) border-red-400 focus:ring-red-300 focus:border-red-400 @enderror">
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $key => $text)
                <option value="{{ $key }}" {{ $key == $value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>

        @if ($icon)
            <i class="{{ $icon }} absolute right-3 top-3.5 text-gray-400 peer-focus:text-blue-600"></i>
        @endif
    </div>

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
