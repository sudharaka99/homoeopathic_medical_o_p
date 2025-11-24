<?php

namespace App\Providers\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;

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
        
        // Log credentials (remove in production)
        Log::info('Zoom Service Initialized', [
            'client_id' => $this->clientId ? substr($this->clientId, 0, 5) . '...' : 'missing',
            'account_id' => $this->accountId ? 'set' : 'missing',
            'client_secret' => $this->clientSecret ? 'set' : 'missing'
        ]);
    }

    /**
     * Try OAuth first, fallback to JWT
     */
    public function createMeeting($appointment, $doctor, $patient)
    {
        // First try OAuth
        try {
            Log::info('Attempting OAuth method');
            return $this->createMeetingWithOAuth($appointment, $doctor, $patient);
        } catch (\Exception $e) {
            Log::warning('OAuth failed, trying JWT: ' . $e->getMessage());
            
            // Fallback to JWT
            try {
                return $this->createMeetingWithJWT($appointment, $doctor, $patient);
            } catch (\Exception $jwtError) {
                throw new \Exception('Both OAuth and JWT failed. OAuth: ' . $e->getMessage() . ' JWT: ' . $jwtError->getMessage());
            }
        }
    }

    /**
     * OAuth Method
     */
    private function createMeetingWithOAuth($appointment, $doctor, $patient)
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            throw new \Exception('Failed to get OAuth access token');
        }

        $meetingData = $this->prepareMeetingData($appointment, $doctor, $patient);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->baseUrl . '/users/me/meetings', $meetingData);

        if ($response->successful()) {
            $meeting = $response->json();
            Log::info('Zoom meeting created with OAuth', ['meeting_id' => $meeting['id']]);
            
            return $this->formatMeetingResponse($meeting);
        }

        throw new \Exception('OAuth API error: ' . $response->body());
    }

    /**
     * JWT Method
     */
    private function createMeetingWithJWT($appointment, $doctor, $patient)
    {
        $jwtToken = $this->generateJWTToken();

        $meetingData = $this->prepareMeetingData($appointment, $doctor, $patient);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwtToken,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->baseUrl . '/users/me/meetings', $meetingData);

        if ($response->successful()) {
            $meeting = $response->json();
            Log::info('Zoom meeting created with JWT', ['meeting_id' => $meeting['id']]);
            
            return $this->formatMeetingResponse($meeting);
        }

        $errorBody = $response->body();
        Log::error('JWT Meeting Creation Failed: ' . $errorBody);
        
        // If JWT also fails, provide specific guidance
        if (str_contains($errorBody, 'Invalid access token') || str_contains($errorBody, '124')) {
            throw new \Exception('JWT authentication failed. This could be due to: 1) Incorrect credentials 2) JWT deprecation 3) App not properly configured in Zoom Marketplace');
        }
        
        throw new \Exception('JWT API error: ' . $errorBody);
    }

    /**
     * Get OAuth Access Token
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

            Log::error('OAuth Token Failed: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('OAuth Token Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate JWT Token
     */
    private function generateJWTToken()
    {
        try {
            $payload = [
                'iss' => $this->clientId,
                'exp' => time() + 3600,
            ];
            
            $token = JWT::encode($payload, $this->clientSecret, 'HS256');
            Log::info('JWT Token generated', ['token_prefix' => substr($token, 0, 20) . '...']);
            
            return $token;
            
        } catch (\Exception $e) {
            Log::error('JWT Generation Error: ' . $e->getMessage());
            throw new \Exception('JWT token generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Prepare meeting data
     */
    private function prepareMeetingData($appointment, $doctor, $patient)
    {
        // Calculate duration safely
        $duration = 30;
        if (!empty($appointment->start_time) && !empty($appointment->end_time)) {
            try {
                $start = \Carbon\Carbon::parse($appointment->start_time);
                $end = \Carbon\Carbon::parse($appointment->end_time);
                $duration = $start->diffInMinutes($end);
                if ($duration <= 0) $duration = 30;
            } catch (\Exception $e) {
                Log::warning('Duration calculation failed, using default');
            }
        }

        // Format start time
        $startTime = \Carbon\Carbon::parse($appointment->appointment_date . ' ' . $appointment->start_time);

        return [
            'topic' => 'Medical Consultation - Dr. ' . $doctor->name . ' with ' . $patient->name,
            'type' => 2,
            'start_time' => $startTime->format('Y-m-d\TH:i:s'),
            'duration' => $duration,
            'timezone' => config('app.timezone', 'Asia/Kolkata'),
            'password' => $this->generateMeetingPassword(),
            'agenda' => 'Medical appointment consultation',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => false,
                'waiting_room' => true,
                'audio' => 'both',
                'auto_recording' => 'none',
            ]
        ];
    }

    /**
     * Format meeting response for controller
     */
    private function formatMeetingResponse($meetingData)
    {
        return [
            'id' => $meetingData['id'],
            'join_url' => $meetingData['join_url'],
            'start_url' => $meetingData['start_url'] ?? $meetingData['join_url'],
            'password' => $meetingData['password']
        ];
    }

    /**
     * Generate meeting password
     */
    private function generateMeetingPassword()
    {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
    }
}