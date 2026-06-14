<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrPackage;
use Illuminate\Http\Request;

class PrPackageController extends Controller
{
    public function index()
    {
        $packages = PrPackage::ordered()->paginate(30);
        return view('admin.pr_packages.index', compact('packages'));
    }

    public function create()
    {
        $package = new PrPackage(['is_active' => true, 'sort_order' => (int) PrPackage::max('sort_order') + 1]);
        return view('admin.pr_packages.create', compact('package'));
    }

    public function store(Request $request)
    {
        PrPackage::create($this->validated($request));
        return redirect()->route('admin.pr_packages.index')->with('success', 'PR package created.');
    }

    public function edit(PrPackage $pr_package)
    {
        $package = $pr_package;
        return view('admin.pr_packages.edit', compact('package'));
    }

    public function update(Request $request, PrPackage $pr_package)
    {
        $pr_package->update($this->validated($request));
        return redirect()->route('admin.pr_packages.index')->with('success', 'PR package updated.');
    }

    public function destroy(PrPackage $pr_package)
    {
        $pr_package->delete();
        return back()->with('success', 'PR package deleted.');
    }

    public function toggle(PrPackage $pr_package)
    {
        $pr_package->update(['is_active' => !$pr_package->is_active]);
        return back()->with('success', 'Status updated.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label'          => 'nullable|string|max:80',
            'name'           => 'required|string|max:160',
            'original_price' => 'nullable|string|max:40',
            'price'          => 'required|string|max:40',
            'sub'            => 'nullable|string|max:255',
            'badge'          => 'nullable|string|max:80',
            'is_popular'     => 'nullable|boolean',
            'sort_order'     => 'nullable|integer',
            'is_active'      => 'nullable|boolean',
            'features'       => 'nullable|array',
            'features.*'     => 'nullable|string|max:255',
        ]);

        // Fold the repeated feature inputs into a clean JSON array.
        $features = [];
        foreach ($request->input('features', []) as $f) {
            $f = trim((string) $f);
            if ($f !== '') $features[] = $f;
        }
        $data['features']   = empty($features) ? null : json_encode($features, JSON_UNESCAPED_UNICODE);
        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int) $request->input('sort_order', 0);

        return $data;
    }
}
