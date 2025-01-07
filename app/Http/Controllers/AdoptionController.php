<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Listing;
use App\Models\Appointment;
use App\Notifications\ApplicationApproved;
use Illuminate\Support\Facades\Log;

class AdoptionController extends Controller
{
    public function create($listingId)
    {
        $listing = Listing::findOrFail($listingId);
        return Inertia::render('AdoptionForm', [
            'listingId' => $listing->id,
        ]);
    }

    public function status(Request $request)
    {
        $adoptionRequests = AdoptionApplication::with(['listing', 'appointment'])
            ->where('user_id', $request->user()->id)
            ->paginate(10)
            ->appends(request()->query());

        return Inertia::render('Status', [
            'adoptionRequests' => $adoptionRequests,
        ]);
    }

    public function show(AdoptionApplication $application)
    {
        $application->load(['listing', 'appointment']);
        
        return Inertia::render('AdoptionApplicationOverview', [
            'application' => $application
        ]);
    }

    public function overview(AdoptionApplication $application)
    {
        try {
            $application->load('listing', 'appointment');
            return Inertia::render('AdoptionApplicationOverview', [
                'application' => $application,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching application details', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to fetch application details'], 500);
        }
    }

    public function scheduleAppointment(Request $request, AdoptionApplication $application)
    {
        $request->validate([
            'appointment_date' => 'required|date|after:today',
        ]);

        try {
            // Create a new appointment
            $appointment = Appointment::create([
                'adoption_application_id' => $application->id,
                'appointment_date' => $request->appointment_date,
                'appointment_status' => 'pending',
            ]);

            // Update the adoption application with the appointment date
            $application->update(['appointment_date' => $request->appointment_date]);

            // Load the appointment relationship
            $application->load('appointment');

            return response()->json([
                'success' => true,
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            Log::error('Error scheduling appointment', [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
                'appointment_date' => $request->appointment_date,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule appointment'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'dob' => 'required|date',
            'reason' => 'required|string|max:1000',
            'listing_id' => 'required|integer|exists:listings,id',
        ]);

        $adoptionApplication = AdoptionApplication::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'dob' => $request->dob,
            'reason' => $request->reason,
            'listing_id' => $request->listing_id,
            'user_id' => $request->user()->id,
        ]);

        $listing = Listing::findOrFail($request->listing_id);
        $listing->application_id = $adoptionApplication->id;
        $listing->save();

        return redirect()->route('home')->with('status', 'Adoption application submitted successfully.');
    }
}