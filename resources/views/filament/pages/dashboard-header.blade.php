<style>
    .dashboard-header-layout {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-header-shell {
        overflow: hidden;
        border-radius: 2rem;
        border: 1px solid rgba(162, 0, 255, 0.46);
        background: rgba(2, 6, 23, 0.78);
        padding: 2rem;
        box-shadow: 0 24px 65px rgba(2, 6, 23, 0.46);
    }

    .dashboard-header-grid {
        display: grid;
        gap: 1.5rem;
    }

    .dashboard-kicker {
        display: inline-flex;
        width: fit-content;
        border-radius: 999px;
        border: 1px solid rgba(217, 70, 239, 0.38);
        background: rgba(162, 0, 255, 0.2);
        padding: 0.5rem 1rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: #f5d0fe;
    }

    .dashboard-title {
        margin: 0;
        color: #fff;
        font-size: 2.2rem;
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    .dashboard-copy {
        max-width: 42rem;
        color: rgba(226, 232, 240, 0.82);
        font-size: 0.9rem;
        line-height: 1.75;
    }

    .dashboard-metric-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .dashboard-metric {
        border-radius: 1rem;
        border: 1px solid rgba(162, 0, 255, 0.4);
        background: rgba(255, 255, 255, 0.03);
        padding: 0.85rem 1rem;
        min-width: 11rem;
    }

    .dashboard-metric-label {
        color: rgba(148, 163, 184, 0.9);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
    }

    .dashboard-metric-value {
        margin-top: 0.45rem;
        color: #fff;
        font-size: 1.45rem;
        font-weight: 900;
    }

    .dashboard-side {
        border-radius: 1.75rem;
        border: 1px solid rgba(162, 0, 255, 0.46);
        background: rgba(2, 6, 23, 0.32);
        padding: 1.25rem;
    }

    .dashboard-side-title {
        color: rgba(148, 163, 184, 0.9);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
    }

    .dashboard-side-rate {
        margin-top: 0.35rem;
        color: #6ee7b7;
        font-size: 2.2rem;
        font-weight: 900;
    }

    .dashboard-progress {
        height: 0.7rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .dashboard-progress > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #a200ff 0%, #d946ef 45%, #6ee7b7 100%);
    }

    .dashboard-side-list {
        display: grid;
        gap: 0.75rem;
        margin-top: 0.25rem;
    }

    .dashboard-side-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 1rem;
        border: 1px solid rgba(162, 0, 255, 0.35);
        background: rgba(255, 255, 255, 0.03);
        padding: 0.75rem 1rem;
        color: rgba(226, 232, 240, 0.82);
        font-size: 0.88rem;
    }

    .dashboard-side-item strong {
        color: #fff;
    }

    @media (min-width: 1024px) {
        .dashboard-header-grid {
            grid-template-columns: minmax(0, 1.4fr) minmax(260px, 0.9fr);
            align-items: start;
        }
    }
</style>

<section class="dashboard-header-layout">
    <div class="dashboard-header-shell">
        <div class="dashboard-header-grid">
            <div class="grid gap-5">
                <span class="dashboard-kicker">Painel executivo</span>
                <div class="grid gap-3">
                    <h1 class="dashboard-title">Controle total da operação de locação</h1>
                    <p class="dashboard-copy">
                        Acompanhe os principais números do estoque, da carteira de clientes e da ocupação dos equipamentos em uma visão única e moderna.
                    </p>
                </div>
                <div class="dashboard-metric-list">
                    <div class="dashboard-metric">
                        <div class="dashboard-metric-label">Equipamentos</div>
                        <div class="dashboard-metric-value">{{ number_format($productCount, 0, ',', '.') }}</div>
                    </div>
                    <div class="dashboard-metric">
                        <div class="dashboard-metric-label">Clientes</div>
                        <div class="dashboard-metric-value">{{ number_format($clientCount, 0, ',', '.') }}</div>
                    </div>
                    <div class="dashboard-metric">
                        <div class="dashboard-metric-label">Locações ativas</div>
                        <div class="dashboard-metric-value">{{ number_format($activeRentalCount, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="dashboard-side">
                <div class="grid gap-4">
                    <div>
                        <div class="dashboard-side-title">Ocupação atual</div>
                        <div class="dashboard-side-rate">{{ $occupancyRate }}%</div>
                    </div>
                    <div class="dashboard-progress">
                        <span style="width: {{ $occupancyRate }}%"></span>
                    </div>
                    <div class="dashboard-side-list">
                        <div class="dashboard-side-item">
                            <span>Receita ativa</span>
                            <strong>R$ {{ number_format((float) $activeRevenue, 2, ',', '.') }}</strong>
                        </div>
                        <div class="dashboard-side-item">
                            <span>Contratos em andamento</span>
                            <strong>{{ number_format($activeRentalCount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
