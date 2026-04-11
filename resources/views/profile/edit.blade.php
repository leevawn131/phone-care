<x-app-layout>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 shadow-sm sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">Điểm tích lũy</p>
                <p class="mt-2 text-3xl font-bold text-blue-900">{{ number_format((int) ($user->loyalty_points ?? 0)) }} điểm</p>
                <p class="mt-2 text-sm text-blue-800">Điểm được cộng khi đơn hàng hoàn tất và có thể dùng tại bước checkout để giảm giá.</p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="max-w-3xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="max-w-3xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="max-w-3xl">
                    @include('profile.partials.update-delivery-address-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
