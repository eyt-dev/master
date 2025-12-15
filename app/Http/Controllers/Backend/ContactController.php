<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\CountryRegion;
use Illuminate\Support\Facades\Session;

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
                ->addColumn('vat', function ($row) {
                    return ($row->vat_country_code || $row->vat_number) ? trim($row->vat_country_code . ' ' . $row->vat_number) : '-';
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-contact btn btn-sm btn-success" data-path="' . route('contact.edit', ['username' => request()->segment(1), 'contact' => $row->id]) . '" title="Edit"><i class="fa fa-edit"></i></a>'
                         . '<a class="delete-contact btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
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
        ]);

        Contact::create([
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
        ]);

        Session::flash('successMsg', 'Contact created successfully.');
        return redirect()->route('contact.index', ['username' => request()->segment(1)]);
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
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update([
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
        ]);

        Session::flash('successMsg', 'Contact updated successfully.');
        return redirect()->route('contact.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Contact::findOrFail($id)->delete();
        return response()->json(['msg' => 'Contact deleted successfully.']);
    }
}
