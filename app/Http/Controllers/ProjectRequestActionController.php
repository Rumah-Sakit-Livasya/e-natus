<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectRequestActionController extends Controller
{
    public function approve($id): RedirectResponse
    {
        $request = ProjectRequest::findOrFail($id);
        $request->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Project request telah disetujui.');
    }

    public function reject($id): RedirectResponse
    {
        $request = ProjectRequest::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Project request telah ditolak.');
    }
}
