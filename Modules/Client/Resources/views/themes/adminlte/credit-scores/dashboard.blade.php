@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.credit_score',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"> 
                    <h1>
                        {{ trans_choice('client::general.credit_score',1) }}
                        <a href="{{ url('client/'.$client->id.'/show') }}"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a></li>
                        <li class="breadcrumb-item"><a href="{{url('client')}}">{{ trans_choice('client::general.client',2) }}</a></li>
                        <li class="breadcrumb-item active">{{ trans_choice('client::general.credit_score',1) }}</li>
                    </ol>
                </div>
            </div>
        </div> 
    </section>
    <section class="content" id="app">
        <div class="row gy-4">
            <div class="col-md-8">
                <div class="card card-bordered card-preview">
                    <div class="card-header">
                        <h3 class="card-title">{{$client->full_name}} — {{$client->client_number}}</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div style="position: relative; width: 320px; height: 180px; margin: 0 auto;">
                                <svg viewBox="0 0 320 180" style="width: 100%; height: 100%;">
                                    @php
                                        $minScore = 0;
                                        $maxScore = 500;
                                        $totalRange = $maxScore - $minScore;
                                        $arcLength = 251;
                                        $segments = [];
                                        foreach($ranges as $range) {
                                            $start = max($range->min_score, $minScore);
                                            $end = min($range->max_score, $maxScore);
                                            if ($start < $end) {
                                                $segments[] = ['start' => $start, 'end' => $end, 'color' => $range->color_code];
                                            }
                                        }
                                        $score = $creditScore?->score ?? 0;
                                        $activeLength = (($score - $minScore) / $totalRange) * $arcLength;
                                        $activeColor = $creditScore?->range?->color_code ?? '#ef4444';
                                    @endphp
                                    @foreach($segments as $segment)
                                        @php
                                            $startOffset = (($segment['start'] - $minScore) / $totalRange) * $arcLength;
                                            $segmentLength = (($segment['end'] - $segment['start']) / $totalRange) * $arcLength;
                                        @endphp
                                        <circle cx="160" cy="160" r="80" 
                                            stroke="{{ $segment['color'] }}" 
                                            stroke-dasharray="{{ $segmentLength }} {{ $arcLength - $segmentLength }}"
                                            stroke-dashoffset="{{ -$startOffset }}"
                                            fill="none" stroke-width="20" stroke-linecap="round"
                                            transform="rotate(180 160 160)" />
                                    @endforeach
                                    <circle cx="160" cy="160" r="80" 
                                        stroke="{{ $activeColor }}" 
                                        stroke-dasharray="{{ $activeLength }} {{ $arcLength - $activeLength }}"
                                        stroke-dashoffset="0"
                                        fill="none" stroke-width="20" stroke-linecap="round"
                                        transform="rotate(180 160 160)" />
                                </svg>
                                <div style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); text-align: center;">
                                    <div class="text-6xl font-bold text-gray-800" id="scoreDisplay">{{ $score }}</div>
                                    <div class="text-lg text-gray-500 font-medium">{{ $creditScore?->rating_label ?? 'Poor' }}</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between text-muted px-5 mt-2">
                                <span>0</span>
                                <span>500</span>
                            </div>
                            <p class="text-muted mt-3">
                                Last updated on {{ $creditScore?->assessed_at?->format('d F Y h:iA') ?? now()->format('d F Y h:iA') }}
                            </p>
                        </div>

                        <h3 class="mt-5">{{ trans_choice('core::general.history',2) }}</h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.date',1) }}</th>
                                    <th>{{ trans_choice('core::general.status',1) }}</th>
                                    <th>{{ trans_choice('core::general.previous_score',1) }}</th>
                                    <th>{{ trans_choice('core::general.new_score',1) }}</th>
                                    <th>{{ trans_choice('core::general.reason',1) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($history as $item)
                                <tr>
                                    <td>{{ $item->change_date->format('d M Y') }}</td>
                                    <td>
                                        @if($item->status === 'Pending')
                                            <span class="badge badge-warning">{{ $item->status }}</span>
                                        @elseif($item->status === 'Confirmed')
                                            <span class="badge badge-success">{{ $item->status }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->previous_score }}</td>
                                    <td>{{ $item->new_score }}</td>
                                    <td>{{ $item->reason ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-bordered card-preview">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans_choice('core::general.update',1) }} {{ trans_choice('client::general.credit_score',1) }}</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ url('client/'.$client->id.'/credit_score/update') }}">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="new_score" class="control-label">{{ trans_choice('core::general.new_score',1) }} (0-500)</label>
                                <input type="number" name="new_score" id="new_score" min="0" max="500" class="form-control numeric" required>
                            </div>
                            <div class="form-group">
                                <label for="status" class="control-label">{{ trans_choice('core::general.status',1) }}</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="Pending">{{ trans_choice('core::general.pending',1) }}</option>
                                    <option value="Confirmed">{{ trans_choice('core::general.confirmed',1) }}</option>
                                    <option value="Rejected">{{ trans_choice('core::general.rejected',1) }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reason" class="control-label">{{ trans_choice('core::general.reason',1) }}</label>
                                <textarea name="reason" id="reason" rows="3" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ trans_choice('core::general.save',1) }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        var scoreDisplay = document.getElementById('scoreDisplay');
        var targetScore = {{ $score }};
        var currentScore = 0;
        var duration = 1500;
        var increment = (targetScore - 0) / (duration / 16);
        
        function animateScore() {
            currentScore += increment;
            if (currentScore < targetScore) {
                scoreDisplay.textContent = Math.round(currentScore);
                requestAnimationFrame(animateScore);
            } else {
                scoreDisplay.textContent = targetScore;
            }
        }
        animateScore();
    </script>
@endsection