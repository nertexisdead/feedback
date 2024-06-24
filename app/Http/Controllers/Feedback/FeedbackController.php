<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\UseCases\AcceptFeedbackData;
use App\UseCases\FeedbackUseCases;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class FeedbackController extends Controller
{
    public function store(FeedbackStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $feedback = app(FeedbackUseCases::class)->acceptFeedback(new AcceptFeedbackData(
            title: $data['title'],
            description: $data['description'],
            datetime: $this->convertTimestampToDT($data['datetime']),
            service: $data['service'],
            rating: $data['rating'],
        ));

        return response()->json([
            'id' => $feedback->id,
            'data' => $data
        ], 201);
    }

    public function show(Feedback $feedback): JsonResponse
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $feedback->datetime);
        $formattedDateTime = $datetime->format('d.m.Y, H:i:s');

        return response()->json([
            'title' => $feedback->title,
            'description' => $feedback->description,
            'service' => $feedback->service,
            'rating' => $feedback->rating,
            // 'datetime' => DateTime::createFromFormat('Y-m-d H:i:s', $feedback->datetime)->getTimestamp()
            'datetime' => $formattedDateTime
        ]);
    }

    private function convertTimestampToDT($microtime): DateTime
    {
        $dt = DateTime::createFromFormat('U', floor($microtime / 1000));
        $dt->setTimeZone(new DateTimeZone('Europe/Moscow'));
        return $dt;
    }
}
