<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Queue;
use App\Models\SubQueue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function checkPasswordPage(Request $request)
    {
        $clinicId = $request->query('clinic_id');
        return view('password_model', ['clinicId' => $clinicId]);
    }

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

        $queue = Queue::where('clinic_id', $clinicId)->first();  // Get the first queue for the clinic
        $clinic = Clinic::findOrFail($clinicId);

        // If no queue exists, create a default one
        if (!$queue) {
            $queue = Queue::create([
                'clinic_id' => $clinicId,
                'current_number' => 1,
                'next_number' => 2,
                'display' => 1 // default display value
            ]);
        }

        // Fallback to default view if custom display view is not found
        return view('index', compact('queue', 'clinic'));
    }

    // Move to the next number in the queue
    public function next($clinicId, $queueNumber)
    {
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinicId, 'queue_number' => $queueNumber],
            ['current_number' => 1, 'next_number' => 2]
        );

        $subQueue->current_number = $subQueue->next_number;
        $subQueue->next_number += 1;
        $subQueue->save();

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function previous($clinicId, $queueNumber)
    {
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinicId, 'queue_number' => $queueNumber],
            ['current_number' => 1, 'next_number' => 2]
        );

        if ($subQueue->current_number > 1) {
            $subQueue->next_number = $subQueue->current_number;
            $subQueue->current_number -= 1;
            $subQueue->save();
        }

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function reset($clinicId, $queueNumber)
    {
        $subQueue = SubQueue::firstOrCreate(
            ['clinic_id' => $clinicId, 'queue_number' => $queueNumber],
            ['current_number' => 1, 'next_number' => 2]
        );

        $subQueue->current_number = 1;
        $subQueue->next_number = 2;
        $subQueue->save();

        return redirect()->route('queues.index', ['clinicId' => $clinicId]);
    }

    public function getLiveQueue($clinicId)
    {
        $queue = Queue::where('clinic_id', $clinicId)->first();
        $subQueues = \App\Models\SubQueue::where('clinic_id', $clinicId)->get();

        $subQueueData = $subQueues->map(function ($subQueue) {
            return [
                'clinic_id' => $subQueue->clinic_id,
                'queue_number' => $subQueue->queue_number,
                'current_number' => $subQueue->current_number,
                'next_number' => $subQueue->next_number
            ];
        });

        return response()->json([
            'subQueues' => $subQueueData
        ]);
    }
}
