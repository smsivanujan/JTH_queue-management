<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function show($clinicId)
    {
        $queue = Queue::where('clinic_id', $clinicId)->first();
        if (!$queue) {
            return "No queue found for clinic ID: $clinicId";
        }
        return view('index', compact('queue'));
    }

    // public function checkPasswordPage(Request $request)
    // {
    //     $clinicId = $request->query('clinic_id');
    //     return view('password_model', ['clinicId' => $clinicId]);
    // }

    public function verifyPassword(Request $request)
    {
        $queue = Queue::where('clinic_id', $request->clinic_id)->first();
        if ($request->password ===  $queue->password) {
            // Add clinic ID to session
            $allowedClinics = session()->get('allowed_clinics', []);
            $allowedClinics[] = $request->clinic_id;
            session(['allowed_clinics' => $allowedClinics]);

            return redirect('/' . $request->clinic_id);
        }

        return back()->withErrors(['Invalid password']);
    }

    public function index($clinicId)
    {
        $allowedClinics = session('allowed_clinics', []);

        if (!in_array($clinicId, $allowedClinics)) {
            return redirect('/')->withErrors(['Access denied. Please enter the password.']);
        }

        $queue = Queue::where('clinic_id', $clinicId)->first();
        $clinic = Clinic::findOrFail($clinicId);

        if (!$queue) {
            $queue = Queue::create([
                'clinic_id' => $clinicId,
                'current_number' => 1,
                'next_number' => 2
            ]);
        }

        return view('index', compact('queue', 'clinic'));
    }

    // Move to the next number in the queue
    public function next($clinicId)
    {
        // Fetch the current queue record
        $queue = Queue::where('clinic_id', $clinicId)->first();

        if (!$queue) {
            $queue = Queue::create([
                'clinic_id' => $clinicId,
                'current_number' => 1,
                'next_number' => 2
            ]);
        }

        // Update the queue numbers
        $queue->current_number = $queue->next_number;
        $queue->next_number += 1; // Increment the next number
        $queue->save();

        // Set a session flag to indicate the queue has been updated
        session()->flash('queue-updated', true);

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function previous($clinicId)
    {
        // Fetch the current queue record
        $queue = Queue::where('clinic_id', $clinicId)->first();

        if (!$queue) {
            $queue = Queue::create([
                'clinic_id' => $clinicId,
                'current_number' => 1,
                'next_number' => 2
            ]);
        }

        // Prevent going below 1
        if ($queue->current_number > 1) {
            $queue->next_number = $queue->current_number;
            $queue->current_number -= 1; // Decrement the current number
            $queue->save();
        }

        // Set a session flag to indicate the queue has been updated
        session()->flash('queue-updated', true);

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function reset($clinicId)
    {
        // Fetch the current queue record
        $queue = Queue::where('clinic_id', $clinicId)->first();

        if (!$queue) {
            $queue = Queue::create([
                'clinic_id' => $clinicId,
                'current_number' => 1,
                'next_number' => 2
            ]);
        }

        // Reset the queue numbers
        $queue->current_number = 1;
        $queue->next_number = 2;
        $queue->save();

        // Set a session flag to indicate the queue has been updated
        session()->flash('queue-updated', true);

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function getLiveQueue($clinicId)
    {
        $queue = Queue::where('clinic_id', $clinicId)->first();

        return response()->json([
            'current_number' => $queue->current_number,
            'next_number' => $queue->next_number
        ]);
    }
}
