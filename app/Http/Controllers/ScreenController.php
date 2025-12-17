<?php

namespace App\Http\Controllers;

use App\Models\ActiveScreen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ScreenController extends Controller
{
    /**
     * Register a new active screen
     * Called when second screen is opened
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'screen_type' => ['required', 'string', 'in:queue,opd_lab'],
            'clinic_id' => ['nullable', 'integer', 'exists:clinics,id'],
        ]);

        // Tenant is guaranteed to be set by IdentifyTenant middleware
        $tenant = app('tenant');

        // Verify clinic belongs to tenant if provided
        if ($validated['clinic_id']) {
            $clinic = \App\Models\Clinic::find($validated['clinic_id']);
            if (!$clinic || $clinic->tenant_id !== $tenant->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to clinic'
                ], 403);
            }
        }

        // Register screen in database
        $screen = ActiveScreen::register(
            $tenant->id,
            $validated['clinic_id'] ?? null,
            $validated['screen_type']
        );

        // Generate signed public URL for the screen (24 hour expiration)
        $signedUrl = $this->generateSignedUrl($screen->screen_token, $validated['screen_type'], 1440);

        // Generate pairing URL for QR code (15 minute expiration)
        $pairingUrl = $this->generatePairingUrl($screen->screen_token, $validated['screen_type']);

        return response()->json([
            'success' => true,
            'screen_token' => $screen->screen_token,
            'signed_url' => $signedUrl,
            'pairing_url' => $pairingUrl,
        ]);
    }

    /**
     * Generate signed URL for public screen access
     * 
     * @param string $screenToken
     * @param string $screenType 'queue' or 'opd_lab'
     * @param int $expirationMinutes Expiration time in minutes (default: 24 hours for screens, 15 minutes for pairing)
     * @return string
     */
    protected function generateSignedUrl(string $screenToken, string $screenType, int $expirationMinutes = 1440): string
    {
        $routeName = $screenType === 'queue' 
            ? 'public.screen.queue' 
            : 'public.screen.opd-lab';

        // Generate signed URL with configurable expiration
        return URL::signedRoute($routeName, ['screen_token' => $screenToken], now()->addMinutes($expirationMinutes));
    }

    /**
     * Generate pairing URL for QR code (short expiration)
     * 
     * @param string $screenToken
     * @param string $screenType 'queue' or 'opd_lab'
     * @return string
     */
    protected function generatePairingUrl(string $screenToken, string $screenType): string
    {
        // Pairing URLs expire in 15 minutes
        return URL::signedRoute('public.screen.pair', [
            'screen_token' => $screenToken,
            'type' => $screenType
        ], now()->addMinutes(15));
    }

    /**
     * Send heartbeat to keep screen active
     * Called periodically from JavaScript (every 10-15 seconds)
     */
    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'screen_token' => ['required', 'string', 'max:64'],
        ]);

        $success = ActiveScreen::heartbeat($validated['screen_token']);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Screen token not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
