<div class="space-y-4">
    @if($sims->isEmpty())
        <div class="text-center py-8">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#94a3b8" style="width:48px; height:48px; margin:0 auto 12px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>
            <p style="color:#94a3b8; font-size:0.9rem;">Aucune carte SIM associée à cet utilisateur.</p>
            <p style="color:#cbd5e1; font-size:0.78rem; margin-top:4px;">Ni par assignation directe, ni via les demandes SIM.</p>
        </div>
    @else
        <div style="padding:8px 12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd; margin-bottom:4px;">
            <span style="font-size:0.82rem; color:#0369a1; font-weight:500;">
                {{ $sims->count() }} carte(s) SIM trouvée(s) pour <strong>{{ $user->full_name }}</strong>
                @if($user->matricule)
                    <span style="color:#64748b;">({{ $user->matricule }})</span>
                @endif
            </span>
        </div>

        @foreach($sims as $index => $sim)
            @php
                $isDirectlyAssigned = $sim->assigned_to == $user->id;
                $label = $sim->phone_number ?: ('SIM ' . ($index + 1));

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
                    ->where(function($q) use ($user) {
                        $q->where('user_id', $user->id);
                        if ($user->matricule) {
                            $q->orWhere('collaborator_matricule', $user->matricule)
                              ->orWhere('beneficiary_matricule', $user->matricule);
                        }
                    })
                    ->whereNotNull('plan_id')
                    ->with('plan')
                    ->orderByDesc('created_at')
                    ->first();

                if (!$latestRequest) {
                    $latestRequest = \App\Models\SimRequest::where('sim_id', $sim->id)
                        ->whereNotNull('plan_id')
                        ->with('plan')
                        ->orderByDesc('created_at')
                        ->first();
                }

                $plan = $latestRequest?->plan;
            @endphp

            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; background:#fff;">
                {{-- Header --}}
                <div style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding:10px 14px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e5e7eb;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="width:32px; height:32px; border-radius:7px; background:{{ $statusColor }}15; display:flex; align-items:center; justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $statusColor }}" style="width:18px; height:18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <div>
                            <span style="font-weight:700; font-size:0.92rem; color:#0f172a;">{{ $label }}</span>
                            @if($isDirectlyAssigned)
                                <span style="font-size:0.7rem; color:#059669; margin-left:6px; background:#d1fae5; padding:1px 6px; border-radius:4px;">assignée</span>
                            @endif
                        </div>
                    </div>
                    <span style="display:inline-block; padding:2px 9px; border-radius:20px; font-size:0.72rem; font-weight:600; color:#fff; background:{{ $statusColor }};">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Body --}}
                <div style="padding:12px 14px;">
                    <table style="width:100%; border-collapse:collapse; font-size:0.83rem;">
                        <tr>
                            <td style="padding:4px 0; color:#64748b; width:38%;">ICCID</td>
                            <td style="padding:4px 0; font-weight:500; color:#1e293b; font-family:monospace; font-size:0.78rem;">{{ $sim->iccid ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0; color:#64748b;">Opérateur</td>
                            <td style="padding:4px 0; font-weight:500; color:#1e293b;">{{ $sim->operator ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0; color:#64748b;">Type de plan</td>
                            <td style="padding:4px 0; font-weight:500; color:#1e293b;">{{ $sim->plan_type ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0; color:#64748b;">Coût mensuel</td>
                            <td style="padding:4px 0; font-weight:500; color:#1e293b;">
                                @if($sim->monthly_cost)
                                    {{ number_format((float)$sim->monthly_cost, 0, ',', ' ') }} XOF
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:4px 0; color:#64748b;">Date d'attribution</td>
                            <td style="padding:4px 0; font-weight:500; color:#1e293b;">
                                {{ $sim->assigned_at ? $sim->assigned_at->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    </table>

                    @if($plan)
                        <div style="margin-top:10px; padding:10px 12px; background:#f0f9ff; border-radius:8px; border:1px solid #bae6fd;">
                            <div style="font-weight:700; font-size:0.78rem; color:#0369a1; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.5px;">
                                Forfait : {{ $plan->name }}
                            </div>
                            <table style="width:100%; border-collapse:collapse; font-size:0.8rem;">
                                @if($plan->description)
                                <tr>
                                    <td style="padding:3px 0; color:#64748b; width:38%;">Description</td>
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

                            @if($latestRequest && ($latestRequest->limite_credit || $latestRequest->limite_data))
                                <div style="margin-top:8px; padding-top:8px; border-top:1px dashed #93c5fd;">
                                    <div style="font-weight:600; font-size:0.73rem; color:#0369a1; margin-bottom:3px;">Limites appliquées (dernière demande)</div>
                                    <table style="width:100%; border-collapse:collapse; font-size:0.8rem;">
                                        @if($latestRequest->limite_credit)
                                        <tr>
                                            <td style="padding:2px 0; color:#64748b; width:38%;">Crédit</td>
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
                        </div>
                    @else
                        <div style="margin-top:8px; padding:6px 10px; background:#f9fafb; border-radius:6px; color:#94a3b8; font-size:0.78rem; text-align:center;">
                            Aucun forfait associé
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
