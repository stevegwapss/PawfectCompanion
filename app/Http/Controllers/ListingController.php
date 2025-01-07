<?php

namespace App\Http\Controllers;

use App\Http\Middleware\NotSuspended;
use App\Models\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ListingController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(
                ['auth', 'verified', NotSuspended::class],
                except: ['index', 'show']
            )
        ];
    }
    public function index(Request $request)
    {
        $listings = Listing::whereHas('user', function (Builder $query) {
            $query->where('role', '!=', 'suspended');
        })
            ->with('user')
            ->filter(request(['search', 'user_id', 'tag']))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Home', [
            'listings' => $listings,
            'searchTerm' => $request->search,
        ]);
    }

public function explore(Request $request)
{
    $query = Listing::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('desc', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $listings = $query->paginate(20);

    return Inertia::render('Explore', [
        'listings' => $listings,
    ]);
}

   public function create()
{
    try {
        Gate::authorize('create', Listing::class);
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return redirect('/')->with('error', 'You do not have permission to create a listing.');
    }

    return Inertia::render('Listing/Create');
}

    public function store(Request $request)
    {
        Gate::authorize('create', Listing::class);

        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'desc' => ['required'],
            'species' => ['nullable', 'string'],
            'breed' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp']
        ]);

        if ($request->hasFile('image')) {
            $fields['image'] = Storage::disk('public')->put('images/listing', $request->image);
        }

        $request->user()->listings()->create($fields);

        return redirect()->route('dashboard')->with('status', 'Listing created successfully.');
    }

    public function show(Listing $listing)
    {
        Gate::authorize('view', $listing);

        return Inertia::render('Listing/Show', [
            'listing' => $listing,
            'user' => $listing->user->only(['name', 'id']),
            'canModify' => Auth::user() ? Auth::user()->can('modify', $listing) : false
        ]);
    }

    public function edit(Listing $listing)
    {
        Gate::authorize('modify', $listing);

        return Inertia::render('Listing/Edit', [
            'listing' => $listing
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        Gate::authorize('modify', $listing);

        $fields = $request->validate([
            'name' => ['required', 'max:255'],
            'desc' => ['required'],
            'species' => ['nullable', 'string'],
            'breed' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:3072', 'mimes:jpeg,jpg,png,webp']
        ]);

        if ($request->hasFile('image')) {
            if ($listing->image) {
                Storage::disk('public')->delete($listing->image);
            }
            $fields['image'] = Storage::disk('public')->put('images/listing', $request->image);
        } else {
            $fields['image'] = $listing->image;
        }

        $listing->update($fields);

        return redirect()->route('dashboard')->with('status', 'Listing updated successfully.');
    }

    public function destroy(Listing $listing)
    {
        Gate::authorize('modify', $listing);

        if ($listing->image) {
            Storage::disk('public')->delete($listing->image);
        }

        $listing->delete();

        return redirect()->route('dashboard')->with('status', 'Listing deleted successfully.');
    }
}