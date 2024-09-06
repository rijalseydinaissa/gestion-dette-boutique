<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use Log;

class ClientCreated
{
    use Dispatchable, SerializesModels;

    public $client;
    public $photo;

    /**
     * Create a new event instance.
     *
     * @param Client $client
     * @param \Illuminate\Http\UploadedFile $photo
     * @return void
     */
    public function __construct(Client $client, $photo)
    {
        // Log::info($photo);
        // dd($photo);
        $this->client = $client;
        $this->photo = $photo;
    }
}
