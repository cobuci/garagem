@props(['label' => null, 'placeholder' => '0,00', 'icon' => null, 'prefix' => null])

<div x-data="{
    value: @entangle($attributes->wire('model')),
    displayValue: '',
    init() {
        this.formatDisplay();
        this.$watch('value', () => this.formatDisplay());
    },
    formatDisplay() {
        if (this.value === null || this.value === undefined || this.value === '') {
            this.displayValue = '';
            return;
        }

        let val = this.value;
        if (typeof val === 'string') {
            val = val.replace(',', '.');
        }

        let numericValue = parseFloat(val);
        if (isNaN(numericValue)) {
            this.displayValue = '';
            return;
        }

        this.displayValue = new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(numericValue);
    },
    updateValue(e) {
        let input = e.target.value.replace(/\D/g, '');

        if (input === '') {
            this.value = null;
            this.displayValue = '';
            return;
        }

        let numericValue = parseInt(input) / 100;
        this.value = numericValue.toFixed(2);
        this.formatDisplay();
    }
}" class="w-full">
    <x-input
        :label="$label"
        :placeholder="$placeholder"
        :icon="$icon"
        :prefix="$prefix"
        x-model="displayValue"
        x-on:input="updateValue"
        {{ $attributes->whereDoesntStartWith('wire:model') }}
    />
</div>
