import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.publicQuickCalculator = function (config = {}) {
    return {
        form: {
            product_name: config.product_name ?? '',
            recipe_total_cost: config.recipe_total_cost ?? '',
            yield_quantity: config.yield_quantity ?? '',
            packaging_unit_cost: config.packaging_unit_cost ?? '',
            other_costs: config.other_costs ?? '',
            profit_margin_percentage: config.profit_margin_percentage ?? 100,
            sales_channel_name: config.sales_channel_name ?? '',
            channel_percentage_rate: config.channel_percentage_rate ?? '',
        },
        result: null,
        loading: false,
        error: '',
        timer: null,
        simulateUrl: config.simulate_url ?? '',

        init() {
            this.refresh();
        },

        queueRefresh() {
            window.clearTimeout(this.timer);
            this.timer = window.setTimeout(() => this.refresh(), 220);
        },

        async refresh() {
            this.error = '';

            if (!this.canSimulate()) {
                this.result = null;
                return;
            }

            this.loading = true;

            try {
                const params = new URLSearchParams();

                Object.entries(this.form).forEach(([key, value]) => {
                    if (value !== '' && value !== null && value !== undefined) {
                        params.append(key, value);
                    }
                });

                const response = await fetch(`${this.simulateUrl}?${params.toString()}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                const payload = await response.json();

                if (!response.ok) {
                    this.result = null;
                    this.error = payload.message ?? 'Nao foi possivel calcular agora.';
                    return;
                }

                this.result = payload.result ?? null;
            } catch (error) {
                this.result = null;
                this.error = 'Nao foi possivel calcular agora.';
            } finally {
                this.loading = false;
            }
        },

        canSimulate() {
            return this.form.recipe_total_cost !== '' &&
                this.form.yield_quantity !== '' &&
                Number(this.form.yield_quantity) > 0 &&
                this.form.profit_margin_percentage !== '';
        },

        money(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL',
            }).format(Number(value || 0));
        },

        percent(value) {
            return new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(Number(value || 0)) + '%';
        },

        quantity(value) {
            return new Intl.NumberFormat('pt-BR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2,
            }).format(Number(value || 0));
        },
    };
};

Alpine.start();

