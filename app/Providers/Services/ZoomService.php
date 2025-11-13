<?php

// namespace App\Services;
namespace App\Providers\Services;  

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    private $clientId;
    private $clientSecret;
    private $accountId;
    private $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
        $this->accountId = config('services.zoom.account_id');
    }

    /**
     * Get Access Token using OAuth
     */
    private function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
            ])->asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $this->accountId,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('Zoom OAuth Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Zoom OAuth Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a Zoom meeting
     */
    public function createMeeting($appointment, $doctor, $patient)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get Zoom access token');
            }

            $startTime = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);
            
            $meetingData = [
                'topic' => 'Medical Consultation - Dr. ' . $doctor->name . ' with ' . $patient->name,
                'type' => 2, // Scheduled meeting
                'start_time' => $startTime->format('Y-m-d\TH:i:s\Z'),
                'duration' => \Carbon\Carbon::parse($appointment->start_time)
                    ->diffInMinutes(\Carbon\Carbon::parse($appointment->end_time)),
                'timezone' => config('app.timezone', 'UTC'),
                'password' => substr(str_shuffle('0123456789'), 0, 6), // 6-digit password
                'agenda' => 'Medical appointment consultation',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => false,
                    'waiting_room' => true,
                    'audio' => 'both', // Both telephony and VoIP
                    'auto_recording' => 'none',
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/users/me/meetings', $meetingData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom Meeting Creation Failed: ' . $response->body());
            throw new \Exception('Failed to create Zoom meeting: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Meeting Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get meeting details
     */
    public function getMeeting($meetingId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get Zoom access token');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get($this->baseUrl . '/meetings/' . $meetingId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom Get Meeting Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Zoom Get Meeting Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a Zoom meeting
     */
    public function deleteMeeting($meetingId)
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                throw new \Exception('Failed to get Zoom access token');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->delete($this->baseUrl . '/meetings/' . $meetingId);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Zoom Delete Meeting Error: ' . $e->getMessage());
            return false;
        }
    }
}