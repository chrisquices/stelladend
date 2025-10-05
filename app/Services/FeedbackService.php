<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function store(array $feedbackData)
    {
        $user = auth()->user();

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'message' => $feedbackData['message'],
            'source'  => $feedbackData['source'],
            'status'  => 'pending',
        ]);

        return $feedback;
    }
}
