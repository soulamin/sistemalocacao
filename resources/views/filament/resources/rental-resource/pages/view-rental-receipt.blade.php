<x-filament-panels::page>
    @php
        $rental = $this->getRecord();
    @endphp

    <div class="grid gap-6">
        <section class="rounded-[2rem] border border-white/10 bg-slate-950/70 p-6 shadow-2xl shadow-slate-950/30">
            <div class="flex flex-wrap items-start justify-between gap-6 border-b border-white/10 pb-6">
                <div class="grid gap-4">
                    <span class="inline-flex w-fit rounded-full border border-indigo-400/20 bg-indigo-400/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.22em] text-indigo-200">
                        {{ $rental->recibo_codigo }}
                    </span>
                    <div class="grid gap-2">
                        <h2 class="text-3xl font-black text-white">Recibo de locação</h2>
                        <p class="max-w-2xl text-sm leading-6 text-slate-300/80">
                            Documento operacional da locação para a empresa {{ $rental->tenant?->nome ?? 'atual' }}.
                        </p>
                    </div>
                </div>

                <div class="grid gap-3 text-sm text-slate-200">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Status</div>
                        <div class="mt-2 font-semibold text-white">{{ ucfirst($rental->status) }}</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                        <div class="text-xs uppercase tracking-[0.2em] text-slate-400">Valor total</div>
                        <div class="mt-2 font-semibold text-emerald-300">R$ {{ number_format((float) $rental->valor_total, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-[1.75rem] border border-white/10 bg-black/20 p-5">
                    <h3 class="text-lg font-semibold text-white">Dados da locação</h3>
                    <dl class="mt-4 grid gap-4 text-sm text-slate-300/80">
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Cliente</dt>
                            <dd class="mt-1 font-medium text-white">{{ $rental->client?->nome }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Empresa responsável</dt>
                            <dd class="mt-1 font-medium text-white">{{ $rental->empresa_responsavel }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Período</dt>
                            <dd class="mt-1 font-medium text-white">{{ $rental->data_inicio?->format('d/m/Y') }} até {{ $rental->data_fim?->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Emitido para</dt>
                            <dd class="mt-1 font-medium text-white">{{ $rental->tenant?->nome ?? 'Empresa atual' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-[1.75rem] border border-white/10 bg-black/20 p-5">
                    <h3 class="text-lg font-semibold text-white">Resumo financeiro</h3>
                    <dl class="mt-4 grid gap-4 text-sm text-slate-300/80">
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Itens locados</dt>
                            <dd class="mt-1 font-medium text-white">{{ $rental->products->count() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Diárias aplicadas</dt>
                            <dd class="mt-1 font-medium text-white">
                                {{ $rental->products->map(fn ($product) => 'R$ ' . number_format((float) $product->pivot->valor_diaria, 2, ',', '.'))->join(' • ') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.2em] text-slate-400">Total do contrato</dt>
                            <dd class="mt-1 text-2xl font-black text-emerald-300">R$ {{ number_format((float) $rental->valor_total, 2, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 rounded-[1.75rem] border border-white/10 bg-black/20 p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-lg font-semibold text-white">Produtos vinculados</h3>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-bold uppercase tracking-[0.2em] text-slate-300">
                        {{ $rental->products->count() }} itens
                    </span>
                </div>

                <div class="overflow-x-auto rounded-[1.5rem] border border-white/10">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/5 text-left text-xs font-bold uppercase tracking-[0.18em] text-slate-300">
                            <tr>
                                <th class="px-4 py-4">Código</th>
                                <th class="px-4 py-4">Produto</th>
                                <th class="px-4 py-4">Categoria</th>
                                <th class="px-4 py-4">Marca</th>
                                <th class="px-4 py-4">Diária</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($rental->products as $product)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-4 text-slate-300">{{ $product->cod_produto }}</td>
                                    <td class="px-4 py-4 font-medium text-white">{{ $product->nome }}</td>
                                    <td class="px-4 py-4 text-slate-300">{{ $product->category?->nome ?? 'Sem categoria' }}</td>
                                    <td class="px-4 py-4 text-slate-300">{{ $product->marca }}</td>
                                    <td class="px-4 py-4 text-indigo-200">R$ {{ number_format((float) $product->pivot->valor_diaria, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
