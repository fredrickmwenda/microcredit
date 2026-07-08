<?php

namespace Modules\Client\Services;

use Modules\Client\Entities\Client;
use Modules\Client\Entities\CreditScore;
use Modules\Client\Entities\CreditScoreHistory;
use Modules\Client\Entities\CreditScoreRange;

class CreditScoreService
{
    public function updateScore(Client $client, int $newScore, string $status, ?string $reason = null): CreditScore
    {
        $previousScore = $client->creditScore?->score ?? 300;
        $range = CreditScoreRange::getRatingForScore($newScore);

        $creditScore = CreditScore::create([
            'client_id' => $client->id,
            'score' => $newScore,
            'range_id' => $range?->id,
            'rating_label' => $range?->rating_label ?? 'Unknown',
            'assessed_at' => now(),
            'notes' => $reason,
        ]);

        CreditScoreHistory::create([
            'client_id' => $client->id,
            'previous_score' => $previousScore,
            'new_score' => $newScore,
            'status' => $status,
            'change_date' => now(),
            'reason' => $reason,
        ]);

        return $creditScore;
    }
}