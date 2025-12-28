<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\CountryRegion;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $data = Contact::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($data)
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('storage/contacts/' . $row->image) . '" style="max-height:50px;" />';
                    }
                    return '';
                })
                ->addColumn('vat', function ($row) {
                    return ($row->vat_country_code || $row->vat_number) ? trim($row->vat_country_code . ' ' . $row->vat_number) : '-';
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-contact btn btn-sm btn-success"
                                data-path="' . route('global_contacts.edit', ['username' => request()->segment(1), 'contact' => $row->id]) . '"
                                title="Edit"><i class="fa fa-edit"></i></a>'
                        . '<a class="delete-contact btn btn-sm btn-danger"
                                data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action','image'])
                ->make(true);
        }

        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.contact.index', compact('countries'));
    }

    public function create($siteUrl)
    {
        $contact = new Contact();
        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.contact.create', compact('contact', 'countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'formal_name' => 'nullable|string|max:191',
            'vat_country_code' => 'nullable|string|max:4',
            'vat_number' => 'nullable|string|max:64',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:64',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:32',
            'city' => 'nullable|string|max:191',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'formal_name' => $request->formal_name,
            'vat_country_code' => $request->vat_country_code,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'created_by' => auth()->id(),
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('contacts', 'public');
            $data['image'] = basename($path);
        }

        Contact::create($data);

        Session::flash('successMsg', 'Contact created successfully.');
        return redirect()->route('global_contacts.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $contact = Contact::findOrFail($id);
        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.contact.create', compact('contact', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'formal_name' => 'nullable|string|max:191',
            'vat_country_code' => 'nullable|string|max:4',
            'vat_number' => 'nullable|string|max:64',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:64',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:32',
            'city' => 'nullable|string|max:191',
            'image' => 'nullable|image|max:2048',
        ]);

        $contact = Contact::findOrFail($id);

        $data = [
            'name' => $request->name,
            'formal_name' => $request->formal_name,
            'vat_country_code' => $request->vat_country_code,
            'vat_number' => $request->vat_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
        ];

        if ($request->hasFile('image')) {
            // delete old image
            if ($contact->image) {
                Storage::disk('public')->delete('contacts/' . $contact->image);
            }
            $path = $request->file('image')->store('contacts', 'public');
            $data['image'] = basename($path);
        }

        $contact->update($data);

        Session::flash('successMsg', 'Contact updated successfully.');
        return redirect()->route('global_contacts.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact->image) {
            Storage::disk('public')->delete('contacts/' . $contact->image);
        }
        $contact->delete();
        return response()->json(['msg' => 'Contact deleted successfully.']);
    }

    public function search(Request $request, $siteUrl)
    {
        $q = $request->get('q');
        $results = Contact::select('id', 'name', 'formal_name', 'vat_country_code', 'vat_number', 
                'email', 'phone', 'address1', 'address2', 'postal_code', 'city', 'image')
            ->whereNotIn('email', function($query) {
                $query->select('email')
                    ->from('my_contacts')
                    ->whereNotNull('email');
            })
            ->when($q, function($query) use ($q) {
                $query->where(function($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();
        return response()->json($results);
    }
}
