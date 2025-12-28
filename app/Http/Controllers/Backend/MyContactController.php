<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MyContact;
use App\Models\Contact;
use App\Models\CountryRegion;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class MyContactController extends Controller
{
    public function index(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $data = MyContact::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($data)
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('storage/my_contacts/' . $row->image) . '" style="max-height:50px;" />';
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
                    return '<a class="edit-mycontact btn btn-sm btn-success"'
                        . ' data-path="' . route('contacts.edit', ['username' => request()->segment(1), 'mycontact' => $row->id]) . '" title="Edit"><i class="fa fa-edit"></i></a>'
                        . '<a class="delete-mycontact btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action','image'])
                ->make(true);
        }

        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.mycontact.index', compact('countries'));
    }

    public function create($siteUrl)
    {
        $contact = new MyContact();
        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.mycontact.create', compact('contact', 'countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'formal_name' => 'nullable|string|max:191',
            'vat_country_code' => 'nullable|string|max:4',
            'vat_number' => 'nullable|string|max:64',
            'email' => 'nullable|email|unique:my_contacts,email',
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

        $contact = Contact::where('email', $request->email)->first();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('my_contacts', 'public');
            $data['image'] = basename($path);
        }

        MyContact::create($data);

        //If record not exist in global contact then add this record to contacts table
        if (!$contact && $request->email) {
            Contact::create($data);
        }

        Session::flash('successMsg', 'My Contact created successfully.');
        return redirect()->route('contacts.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $contact = MyContact::findOrFail($id);
        $countries = CountryRegion::orderBy('name')->get();
        return view('backend.mycontact.create', compact('contact', 'countries'));
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

        $contact = MyContact::findOrFail($id);

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
                Storage::disk('public')->delete('my_contacts/' . $contact->image);
            }
            $path = $request->file('image')->store('my_contacts', 'public');
            $data['image'] = basename($path);
        }

        $contact->update($data);

        Session::flash('successMsg', 'My Contact updated successfully.');
        return redirect()->route('contacts.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $contact = MyContact::findOrFail($id);
        if ($contact->image) {
            Storage::disk('public')->delete('my_contacts/' . $contact->image);
        }
        $contact->delete();
        return response()->json(['msg' => 'My Contact deleted successfully.']);
    }
}
