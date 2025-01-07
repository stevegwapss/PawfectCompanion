<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Notifications\ApplicationApproved;
use App\Models\Appointment;
use App\Notifications\AppointmentApproved;

class AdminController extends Controller
{
    

  public function users()
{
    $users = User::with('listings')
        ->filter(request(['search', 'user_role']))
        ->paginate(10)
        ->appends(request()->query());

    return Inertia::render('Admin/AdminUsers', [
        'users' => $users,
        'status' => session('status')
    ]);
}

public function adoptionApplications()
{
    $adoptionApplications = AdoptionApplication::with('listing')
        ->when(request('search'), function ($query) {
            $query->where('fullName', 'like', '%' . request('search') . '%')
                ->orWhere('email', 'like', '%' . request('search') . '%')
                ->orWhere('phone', 'like', '%' . request('search') . '%');
        })
        ->when(request('status') && request('status') !== 'all', function ($query) {
            $query->where('status', request('status'));
        })
        ->paginate(10)
        ->appends(request()->query());

    return Inertia::render('Admin/AdminAdoptionApplications', [
        'adoptionApplications' => $adoptionApplications,
        'status' => session('status')
    ]);
}

    public function reviewApplication($applicationId)
    {
        $application = AdoptionApplication::with('listing', 'appointment')->findOrFail($applicationId);

        return Inertia::render('Admin/AdoptionApplicationReview', [
            'application' => $application,
        ]);
    }

    public function show(User $user)
    {
        $user_listings = $user
            ->listings()
            ->filter(request(['search', 'disapproved']))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/UserPage', [
            'user' => $user,
            'listings' => $user_listings,
            'status' => session('status')
        ]);
    }

    public function appointments()
    {
        $appointments = \DB::table('appointments')
            ->join('adoption_applications', 'appointments.adoption_application_id', '=', 'adoption_applications.id')
            ->join('listings', 'adoption_applications.listing_id', '=', 'listings.id')
            ->select(
                'appointments.*',
                'adoption_applications.fullName',
                'adoption_applications.email',
                'adoption_applications.phone',
                'listings.name as listing_name'
            )
            ->get();

        return Inertia::render('Admin/Appointments', [
            'appointments' => $appointments,
        ]);
    }

    public function approveAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['appointment_status' => 'approved']);

             $appointment->adoptionApplication->user->notify(new AppointmentApproved($appointment));
            
            return redirect()->back()->with('success', 'Appointment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve appointment.');
        }
    }

    public function disapproveAppointment($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['appointment_status' => 'disapproved']);

            $appointment->user->notify(new AppointmentApproved($appointment, 'disapproved'));
            
            return redirect()->back()->with('success', 'Appointment disapproved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to disapprove appointment.');
        }
    }

    public function approveApplication(AdoptionApplication $application)
    {
        $application->load('user');

        if (!$application->user) {
            return redirect()->back()->withErrors(['error' => 'User not found for this application.']);
        }

        $application->update(['status' => 'approved']);
        $application->listing->update(['status' => 'unavailable']);

        $application->user->notify(new ApplicationApproved($application));

        return redirect()->route('admin.adoption.review', $application->id)->with('status', 'Application approved.');
    }

    public function disapproveApplication(AdoptionApplication $application)
    {
        $application->load('user');

        if (!$application->user) {
            return redirect()->back()->withErrors(['error' => 'User not found for this application.']);
        }

        $application->update(['status' => 'disapproved']);
        $application->listing->update(['status' => 'available']);

        $application->user->notify(new ApplicationApproved($application, 'disapproved'));

        return redirect()->route('admin.adoption.review', $application->id)->with('status', 'Application disapproved.');
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();
        return redirect()->route('admin.users')->with('status', 'User deleted successfully.');
    }
    
    public function role(Request $request, User $user)
    {
        Gate::authorize('modifyRole', $user);
        $request->validate(['role' => 'string|required']);
        $user->update(['role' => $request->role]);
        return redirect()->route('admin.users')->with('status', "User role changed to {$request->role} successfully.");
    }

    public function approve(Listing $listing)
    {
        Gate::authorize('approve', $listing);
        $listing->update(['approved' => !$listing->approved]);
        $msg = $listing->approved ? 'approved' : 'disapproved';
        return back()->with('status', "Listing {$msg} successfully!");
    }
}