<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    // Display the queue information
    public function index()
    {
        // Fetch the current queue record or create it if it doesn't exist
        $queue = Queue::first();
        if (!$queue) {
            $queue = Queue::create([
                'current_number' => 1,
                'next_number' => 2
            ]);
        }

        // Return the view with the queue data
        return view('queues.index', compact('queue'));
    }

    // Move to the next number in the queue
    public function next()
    {
        // Fetch the current queue record
        $queue = Queue::first();
        if (!$queue) {
            $queue = Queue::create([
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

        return redirect()->route('queues.index');
    }

    // Go to the previous number in the queue
    public function previous()
    {
        // Fetch the current queue record
        $queue = Queue::first();
        if (!$queue) {
            $queue = Queue::create([
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

        return redirect()->route('queues.index');
    }

    // Reset the queue numbers to their initial values
    public function reset()
    {
        // Fetch the current queue record
        $queue = Queue::first();
        if (!$queue) {
            $queue = Queue::create([
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

        return redirect()->route('queues.index');
    }
}
