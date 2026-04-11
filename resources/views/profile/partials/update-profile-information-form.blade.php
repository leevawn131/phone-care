<section
    x-data="{ editing: @js($errors->has('name') || $errors->has('email')) }"
    @keydown.escape.window="editing = false"
>
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Thông tin hồ sơ') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Cập nhật thông tin hồ sơ và địa chỉ email của tài khoản.') }}
            </p>
        </div>

        <button
            type="button"
            @click="editing = true"
            class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-indigo-300 hover:text-indigo-600"
        >
            {{ __('Chỉnh sửa') }}
        </button>
    </header>

    <div class="mt-6 rounded-2xl border border-gray-100 bg-gray-50/70 p-5 shadow-sm">
        <dl class="space-y-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Họ và tên') }}</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Email') }}</dt>
                <dd class="mt-1 text-sm font-medium text-gray-900">{{ $user->email }}</dd>
            </div>
        </dl>

        @if (session('status') === 'profile-updated')
            <p class="mt-4 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                {{ __('Đã lưu.') }}
            </p>
        @endif
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

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
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Chỉnh sửa thông tin hồ sơ') }}</h3>
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

            <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('patch')

                <div>
                    <x-input-label for="name" :value="__('Họ và tên')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                            <p class="text-sm text-amber-800">
                                {{ __('Địa chỉ email của bạn chưa được xác minh.') }}
                                <button form="send-verification" class="ml-1 font-semibold underline hover:text-amber-900">
                                    {{ __('Nhấn vào đây để gửi lại email xác minh.') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-sm font-medium text-emerald-700">
                                    {{ __('Một liên kết xác minh mới đã được gửi tới email của bạn.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="editing = false"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                    >
                        {{ __('Hủy') }}
                    </button>
                    <x-primary-button class="rounded-xl px-5">{{ __('Lưu') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</section>
