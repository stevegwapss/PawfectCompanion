<?php
namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AdoptionApplication;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $query = Listing::query()
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->when($request->species, function ($query, $species) {
            $query->where('species', 'like', "%{$species}%");
        });

    // Clone the query before pagination to get the same filtered results
    $allListings = (clone $query)->get();
    $listings = $query->paginate(10)->withQueryString();

    $notifications = Auth::user()->unreadNotifications;
    $adoptionApplications = AdoptionApplication::all();

    return Inertia::render('Dashboard', [
        'listings' => $listings,
        'allListings' => $allListings,
        'notifications' => $notifications,
        'adoptionApplications' => $adoptionApplications,
        'status' => session('status'),
    ]);
}

}