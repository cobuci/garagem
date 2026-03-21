<div class="min-h-screen flex flex-col md:flex-row bg-slate-50 dark:bg-slate-950 transition-colors duration-500">
    <div class="hidden md:flex md:w-1/2 bg-slate-900 dark:bg-slate-900 relative overflow-hidden flex-col justify-center items-center p-12 text-white">
        <div class="absolute inset-0 opacity-20 dark:opacity-30">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 -right-24 w-80 h-80 bg-blue-600 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 left-1/4 w-64 h-64 bg-indigo-500 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 text-center">
            <div class="flex flex-col items-center gap-4">
                <h1 class="text-6xl font-black tracking-tighter italic">{{ config('app.name') }}</h1>
                <p class="text-slate-400 max-w-sm">{{ __('login.access_account') }}</p>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center p-8 sm:p-12 md:p-16 lg:p-24 relative">
        <div class="absolute top-4 right-4">
            <x-button
                x-on:click="darkMode = !darkMode"
                flat
                secondary
                icon="sun"
                class="dark:hidden"
            />
            <x-button
                x-on:click="darkMode = !darkMode"
                flat
                secondary
                icon="moon"
                class="hidden dark:inline-flex"
            />
        </div>

        <div class="w-full max-w-md space-y-8">
            <div class="md:hidden flex flex-col items-center mb-8">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white italic tracking-tighter">{{ config('app.name') }}</h2>
            </div>

            <div class="space-y-2 text-center md:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight leading-tight">
                    {{ $step == 1 ? __('login.welcome_back') : __('login.verify_email') }}
                </h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium">
                    {{ $step == 1 ? __('login.access_account') : __('login.otp_sent', ['email' => $email]) }}
                </p>
            </div>

            <div class="mt-8">
                @if($step == 1)
                    <form wire:submit.prevent="sendOtp" class="space-y-6">
                        <x-input
                            wire:model="email"
                            :label="__('login.email_label')"
                            :placeholder="__('login.email_placeholder')"
                            icon="envelope"
                            type="email"
                            autocomplete="email"
                            class="dark:bg-slate-900/50 dark:border-slate-800 dark:text-slate-200 dark:placeholder-slate-500"
                            required
                        />

                        <x-button
                            type="submit"
                            primary
                            xl
                            class="w-full font-bold shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 transition-all duration-300"
                            wire:loading.attr="disabled"
                            wire:target="sendOtp"
                        >
                            <span wire:loading.remove wire:target="sendOtp">{{ __('login.send_code') }}</span>
                            <span wire:loading wire:target="sendOtp" class="flex items-center gap-2">
                                 <x-icon name="arrow-path" class="w-5 h-5 animate-spin" />
                                 {{ __('login.sending') }}
                            </span>
                        </x-button>
                    </form>
                @else
                    <form wire:submit.prevent="verifyOtp" class="space-y-6">
                        <x-input
                            wire:model="otp"
                            :label="__('login.otp_label')"
                            :placeholder="__('login.otp_placeholder')"
                            maxlength="6"
                            class="text-center text-2xl tracking-[1em] font-mono dark:bg-slate-900/50 dark:border-slate-800 dark:text-slate-200 dark:placeholder-slate-500"
                            required
                            autofocus
                        />

                        <div class="grid grid-cols-2 gap-4">
                            <x-button
                                secondary
                                xl
                                flat
                                :label="__('login.back')"
                                wire:click="backToEmail"
                                wire:loading.attr="disabled"
                                class="dark:text-slate-400 dark:hover:bg-slate-800"
                            />
                            <x-button
                                type="submit"
                                primary
                                xl
                                class="font-bold shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 transition-all duration-300"
                                wire:loading.attr="disabled"
                                wire:target="verifyOtp"
                            >
                                <span wire:loading.remove wire:target="verifyOtp">{{ __('login.verify') }}</span>
                                <span wire:loading wire:target="verifyOtp" class="flex items-center gap-2">
                                    <x-icon name="arrow-path" class="w-5 h-5 animate-spin" />
                                    {{ __('login.validating') }}
                                </span>
                            </x-button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
