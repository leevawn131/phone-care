<section
    x-data="{ editing: @js($errors->has('phone') || $errors->has('province_code') || $errors->has('district_code') || $errors->has('ward_code') || $errors->has('address_line')) }"
    @keydown.escape.window="editing = false"
>
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Địa chỉ giao hàng mặc định') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Lưu trữ thông tin địa chỉ mặc định để tự động điền vào biểu mẫu đặt hàng.
            </p>
        </div>

        <button
            type="button"
            @click="editing = true"
            class="btn btn-sm btn-outline-dark rounded-3 px-3"
        >
            {{ __('Chỉnh sửa') }}
        </button>
    </header>

    @php
        $addressParts = array_filter([
            $user->address_line,
            $user->ward_code,
            $user->district_code,
            $user->province_code,
        ]);
    @endphp

    <div class="mt-6 rounded-2xl border border-gray-100 bg-gray-50/70 p-5 shadow-sm">
        <dl class="space-y-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Số điện thoại') }}</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->phone ?: 'Chưa cập nhật' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Địa chỉ mặc định') }}</dt>
                <dd class="mt-1 text-sm leading-6 text-gray-900">{{ count($addressParts) ? implode(', ', $addressParts) : 'Chưa cập nhật địa chỉ mặc định' }}</dd>
            </div>
        </dl>

        @if (session('status') === 'profile-updated')
            <p class="mt-4 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                {{ __('Đã lưu.') }}
            </p>
        @endif
    </div>

    <div
        x-show="editing"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 px-4 py-6"
    >
        <div
            @click.outside="editing = false"
            class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl sm:p-7"
        >
            <div class="mb-5 flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Chỉnh sửa địa chỉ giao hàng mặc định') }}</h3>
                <button
                    type="button"
                    @click="editing = false"
                    class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Close"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <form method="post" action="{{ route('profile.update-address') }}" class="space-y-5">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="phone" :value="__('Số điện thoại')" />
                    <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('phone', $user->phone)" placeholder="0901234567" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="province_code" :value="__('Tỉnh / Thành phố')" />
                        <x-text-input id="province_code" name="province_code" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('province_code', $user->province_code)" placeholder="VD: TP.HCM" />
                        <x-input-error class="mt-2" :messages="$errors->get('province_code')" />
                    </div>

                    <div>
                        <x-input-label for="district_code" :value="__('Quận / Huyện')" />
                        <x-text-input id="district_code" name="district_code" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('district_code', $user->district_code)" placeholder="VD: Quận 1" />
                        <x-input-error class="mt-2" :messages="$errors->get('district_code')" />
                    </div>

                    <div>
                        <x-input-label for="ward_code" :value="__('Phường / Xã')" />
                        <x-text-input id="ward_code" name="ward_code" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('ward_code', $user->ward_code)" placeholder="VD: Bến Nghé" />
                        <x-input-error class="mt-2" :messages="$errors->get('ward_code')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="address_line" :value="__('Địa chỉ chi tiết')" />
                    <textarea id="address_line" name="address_line" rows="4" class="form-control border-secondary-subtle rounded-3 shadow-none mt-1" placeholder="Số nhà, tên đường...">{{ old('address_line', $user->address_line) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address_line')" />
                </div>

                <p class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-500">
                    Tạm thời đang dùng text input cho Tỉnh/Quận/Phường. Bạn có thể thay bằng dropdown ở bước tiếp theo.
                </p>

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="editing = false"
                        class="btn btn-outline-secondary rounded-3 px-4"
                    >
                        {{ __('Hủy') }}
                    </button>
                    <x-primary-button>{{ __('Lưu địa chỉ') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</section>
