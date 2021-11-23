<?php

declare (strict_types=1);


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class BookReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this['bookReview']->id,
                'review' => $this['bookReview']->review,
                'comment' => $this['bookReview']->comment,
                'user' => [
                    'id' => $this['user_id'],
                    'name' => $this['name']
                ]
            ]
        ];
    }
}
