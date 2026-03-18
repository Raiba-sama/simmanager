<div class="space-y-4">
    @if($sims->isEmpty())
        <div class="text-center py-6 text-gray-400">
            <x-filament::icon icon="heroicon-o-device-phone-mobile" class="w-10 h-10 mx-auto mb-2 text-gray-300" />
            <p>Aucune carte SIM attribuée à cet utilisateur.</p>
        </div>
    @else
        @foreach($sims as $index => $sim)
            @php
                $label = 'SIM ' . ($index + 1);
                $statusLabel = match($sim->status) {
                    'attribue' => 'Attribuée',
                    'suspendu', 'suspendue' => 'Suspendue',
                    'libre' => 'Libre',
                    'defectueuse' => 'Défectueuse',
                    default => ucfirst($sim->status),
                };
                $statusColor = match($sim->status) {
                    'attribue' => '#0d6efd',
                    'suspendu', 'suspendue' => '#dc3545',
                    'libre' => '#198754',
                    'defectueuse' => '#fd7e14',
                    default => '#6c757d',
                };

                $latestRequest = \App\Models\SimRequest::where('sim_id', $sim->id)
                    ->whereNotNull('plan_id')
                    ->with('plan')
                    ->orderByDesc('created_at')
                    ->first();

                $plan = $latestRequest?->plan;
            @endphp

            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fff;">
                {{-- Header --}}
                <div style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding:12px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e5e7eb;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:36px; height:36px; border-radius:8px; background:{{ $statusColor }}15; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $statusColor }}" style="width:20px; height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <div>
                            <span style="font-weight:700; font-size:0.95rem; color:#0f172a;">{{ $label }}</span>
                            @if($sim->phone_number)
                                <span style="font-weight:600; color:#334155; margin-left:6px;">{{ $sim->phone_number }}</span>
                            @endif
                        </div>
                    </div>
                    <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; color:#fff; background:{{ $statusColor }};">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Body --}}
                <div style="padding:14px 16px;">
                    <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
                        <tr>
                            <td style="padding:5px 0; color:#64748b; width:40%;">ICCID</td>
                            <td style="padding:5px 0; font-weight:500; color:#1e293b; font-family:monospace; font-size:0.8rem;">{{ $sim->iccid ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0; color:#64748b;">Opérateur</td>
                            <td style="padding:5px 0; font-weight:500; color:#1e293b;">{{ $sim->operator ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0; color:#64748b;">Type de plan</td>
                            <td style="padding:5px 0; font-weight:500; color:#1e293b;">{{ $sim->plan_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0; color:#64748b;">Coût mensuel</td>
                            <td style="padding:5px 0; font-weight:500; color:#1e293b;">
                                @if($sim->monthly_cost)
                                    {{ number_format((float)$sim->monthly_cost, 0, ',', ' ') }} XOF
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0; color:#64748b;">Date d'attribution</td>
                            <td style="padding:5px 0; font-weight:500; color:#1e293b;">
                                {{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    </table>

                    {{-- Détails du forfait (depuis la dernière demande avec un plan) --}}
                    @if($plan)
                        <div style="margin-top:12px; padding:10px 14px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                            <div style="font-weight:700; font-size:0.8rem; color:#0369a1; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                                Forfait : {{ $plan->name }}
                            </div>
                            <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                                @if($plan->description)
                                <tr>
                                    <td style="padding:3px 0; color:#64748b; width:40%;">Description</td>
                                    <td style="padding:3px 0; color:#1e293b;">{{ $plan->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:3px 0; color:#64748b;">Limite crédit</td>
                                    <td style="padding:3px 0; font-weight:600; color:#1e293b;">
                                        {{ $plan->limite_credit ? number_format((float)$plan->limite_credit, 0, ',', ' ') . ' XOF' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0; color:#64748b;">Limite data</td>
                                    <td style="padding:3px 0; font-weight:600; color:#1e293b;">
                                        {{ $plan->limite_data ? $plan->limite_data . ' Go' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0; color:#64748b;">Coût forfait</td>
                                    <td style="padding:3px 0; font-weight:600; color:#1e293b;">
                                        {{ $plan->monthly_cost ? number_format((float)$plan->monthly_cost, 0, ',', ' ') . ' XOF/mois' : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0; color:#64748b;">Opérateur forfait</td>
                                    <td style="padding:3px 0; color:#1e293b;">{{ $plan->operator ?? '-' }}</td>
                                </tr>
                            </table>

                            @if($latestRequest)
                                @if($latestRequest->limite_credit || $latestRequest->limite_data)
                                <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #93c5fd;">
                                    <div style="font-weight:600; font-size:0.75rem; color:#0369a1; margin-bottom:4px;">Limites appliquées (dernière demande)</div>
                                    <table style="width:100%; border-collapse:collapse; font-size:0.82rem;">
                                        @if($latestRequest->limite_credit)
                                        <tr>
                                            <td style="padding:2px 0; color:#64748b; width:40%;">Crédit</td>
                                            <td style="padding:2px 0; font-weight:600; color:#1e293b;">{{ number_format((float)$latestRequest->limite_credit, 0, ',', ' ') }} XOF</td>
                                        </tr>
                                        @endif
                                        @if($latestRequest->limite_data)
                                        <tr>
                                            <td style="padding:2px 0; color:#64748b;">Data</td>
                                            <td style="padding:2px 0; font-weight:600; color:#1e293b;">{{ $latestRequest->limite_data }} Go</td>
                                        </tr>
                                        @endif
                                        @if($latestRequest->is_temporary)
                                        <tr>
                                            <td style="padding:2px 0; color:#64748b;">Temporaire</td>
                                            <td style="padding:2px 0; color:#b45309; font-weight:500;">
                                                {{ $latestRequest->temporary_start_date?->format('d/m/Y') }} → {{ $latestRequest->temporary_end_date?->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <div style="margin-top:10px; padding:8px 12px; background:#f9fafb; border-radius:6px; color:#94a3b8; font-size:0.8rem; text-align:center;">
                            Aucun forfait associé via les demandes
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if($sims->count() > 2)
            <div style="text-align:center; padding:6px; color:#64748b; font-size:0.8rem;">
                {{ $sims->count() }} cartes SIM attribuées au total
            </div>
        @endif
    @endif
</div>
