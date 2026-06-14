<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $leads = Lead::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('service', 'like', "%{$search}%");
                });
            })
            ->latestFirst()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total'     => Lead::count(),
            'mailed'    => Lead::where('mail_send', true)->count(),
            'resubmits' => Lead::where('form_resubmit', true)->count(),
        ];

        return view('admin.leads.index', compact('leads', 'stats', 'search'));
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return back()->with('success', 'Lead deleted.');
    }
}
