@extends('layouts.master')
@section('css')
		<link href="{{URL::asset('assets/plugins/accordion/accordion.css')}}" rel="stylesheet" />
        <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!--Page header-->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Settings</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/e')}}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Settings</a></li>
            </ol>
        </div>
    </div>
    <!--End Page header-->
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form 
                    action="{{ isset($setting) && $setting->id ? route('setting.update', ['username' => $siteSlug, 'setting' => $setting->id]) : route('setting.store', ['username' => $siteSlug]) }}"
                    method="POST" 
                    id="setting_form"
                    novalidate=""
                    class="needs-validation" 
                    enctype="multipart/form-data"
                >
                    <div class="card-header">
                        <h3 class="card-title">Settings</h3>
                    </div>
                    <div class="card-body">
                        @csrf
                        @if(auth()->user()->role === 'SuperAdmin')
                            <div class="form-group">
                                <label class="form-label"> Admin User: 
                                {{-- @if($setting->created_by)
                                    @php
                                        $admin = $admins->find($setting->created_by);
                                    @endphp
                                    {{ $admin->name.' ('.$admin->email.')' }}</label>
                                @else  --}}
                                    </label>
                                    <select 
                                        class="form-control select2-show-search" 
                                        data-placeholder="Choose one (with searchbox)" 
                                        required=""
                                        name="created_by"
                                    >
                                        <option value="">Select</option>
                                        @foreach($admins as $admin)
                                            <option 
                                                value="{{$admin->id}}"
                                                {{ (isset($setting) && $setting->created_by == $admin->id ? "selected" : "") }}
                                            >
                                                {{ $admin->name.' ('.$admin->email.')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                {{-- @endif --}}
                                
                                @error('domain')
                                    <label id="domain-error" class="error" for="domain">{{ $message }}</label>
                                @enderror
                            </div>
                        @endif
                        <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
                            <div class="acc-card">
                                <div class="acc-header" id="headingOne" role="tab">
                                    <h5 class="mb-0">
                                        <a aria-controls="collapseOne" aria-expanded="true" data-toggle="collapse" href="#collapseOne">Domain</a>
                                    </h5>
                                </div>
                                <div aria-labelledby="headingOne" class="collapse show" data-parent="#accordion" id="collapseOne" role="tabpanel">
                                    <div class="acc-body">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="domain" class="form-label">Domain <span class="text-red">*</span></label>
                                                    <input type="text" class="form-control" name="domain" id="domain" placeholder="Website Domain"
                                                        value="{{ old('domain', $setting->domain ?? '') }}" required="" />
                                                    @error('domain')
                                                        <label id="domain-error" class="error" for="domain">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="admin_domain" class="form-label">Admin Domain <span class="text-red">*</span></label>
                                                    <input type="text" class="form-control" name="admin_domain" id="admin_domain" placeholder="Website Admin Domain"
                                                        value="{{ old('admin_domain', $setting->admin_domain ?? '') }}" required="" />
                                                    @error('admin_domain')
                                                        <label id="admin_domain-error" class="error" for="admin_domain">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="acc-card">
                                <div class="acc-header" id="headingTwo" role="tab">
                                    <h5 class="mb-0">
                                        <a aria-controls="collapseTwo" aria-expanded="false" class="collapsed" data-toggle="collapse" href="#collapseTwo">Images</a>
                                    </h5>
                                </div>
                                <div aria-labelledby="headingTwo" class="collapse" data-parent="#accordion" id="collapseTwo" role="tabpanel">
                                    <div class="acc-body">
                                        <div class="row">
                                            @foreach (['dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $logoType)
                                                <div class="col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="{{ $logoType }}" class="form-label">{{ ucwords(str_replace('_', ' ', $logoType)) }}</label>
                                                        <input type="file" class="form-control" name="{{ $logoType }}" id="{{ $logoType }}" accept="image/*" />
                                                        @error($logoType)
                                                            <label id="{{ $logoType }}-error" class="error" for="{{ $logoType }}">{{ $message }}</label>
                                                        @enderror
                                                        @if(isset($setting) && $setting->$logoType)
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/'.$setting->$logoType) }}" alt="{{ ucwords(str_replace('_', ' ', $logoType)) }}" class="img-thumbnail" width="150" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="acc-card">
                                <div class="acc-header" id="headingThree" role="tab">
                                    <h5 class="mb-0">
                                        <a aria-controls="collapseThree" aria-expanded="false" class="collapsed" data-toggle="collapse" href="#collapseThree">Colors</a>
                                    </h5>
                                </div>
                                <div aria-labelledby="headingThree" class="collapse" data-parent="#accordion" id="collapseThree" role="tabpanel">
                                    <div class="acc-body">
                                        <div class="row">
                                            <!-- Primary Text Color -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="primary_text_color" class="form-label">Primary Text Color</label>
                                                    <input type="color" class="form-control" name="primary_text_color" id="primary_text_color" placeholder="#000000"
                                                        value="{{ old('primary_text_color', $setting->primary_text_color ?? '') }}" />
                                                    @error('primary_text_color')
                                                        <label id="primary_text_color-error" class="error" for="primary_text_color">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                    
                                            <!-- Secondary Text Color -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="secondary_text_color" class="form-label">Secondary Text Color</label>
                                                    <input type="color" class="form-control" name="secondary_text_color" id="secondary_text_color" placeholder="#ffffff"
                                                        value="{{ old('secondary_text_color', $setting->secondary_text_color ?? '') }}" />
                                                    @error('secondary_text_color')
                                                        <label id="secondary_text_color-error" class="error" for="secondary_text_color">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Primary Button Background -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="primary_button_background" class="form-label">Primary Button Background</label>
                                                    <input type="color" class="form-control" name="primary_button_background" id="primary_button_background" placeholder="#007bff"
                                                        value="{{ old('primary_button_background', $setting->primary_button_background ?? '') }}" />
                                                    @error('primary_button_background')
                                                        <label id="primary_button_background-error" class="error" for="primary_button_background">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                        
                                            <!-- Secondary Button Background -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="secondary_button_background" class="form-label">Secondary Button Background</label>
                                                    <input type="color" class="form-control" name="secondary_button_background" id="secondary_button_background" placeholder="#6c757d"
                                                        value="{{ old('secondary_button_background', $setting->secondary_button_background ?? '') }}" />
                                                    @error('secondary_button_background')
                                                        <label id="secondary_button_background-error" class="error" for="secondary_button_background">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>

                                            <!-- Primary Button Text Color -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="primary_button_text_color" class="form-label">Primary Button Text Color</label>
                                                    <input type="color" class="form-control" name="primary_button_text_color" id="primary_button_text_color" placeholder="#ffffff"
                                                        value="{{ old('primary_button_text_color', $setting->primary_button_text_color ?? '') }}" />
                                                    @error('primary_button_text_color')
                                                        <label id="primary_button_text_color-error" class="error" for="primary_button_text_color">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                        
                                            <!-- Secondary Button Text Color -->
                                            <div class="col-sm-4 col-md-4">
                                                <div class="form-group">
                                                    <label for="secondary_button_text_color" class="form-label">Secondary Button Text Color</label>
                                                    <input type="color" class="form-control" name="secondary_button_text_color" id="secondary_button_text_color" placeholder="#000000"
                                                        value="{{ old('secondary_button_text_color', $setting->secondary_button_text_color ?? '') }}" />
                                                    @error('secondary_button_text_color')
                                                        <label id="secondary_button_text_color-error" class="error" for="secondary_button_text_color">{{ $message }}</label>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- collapse -->
                            </div>
                            <div class="acc-card">
                                <div class="acc-header" id="headingThree" role="tab">
                                    <h5 class="mb-0">
                                        <a aria-controls="collapseFour" aria-expanded="false" class="collapsed" data-toggle="collapse" href="#collapseFour">Themes</a>
                                    </h5>
                                </div>
                                <div aria-labelledby="headingThree" class="collapse" data-parent="#accordion" id="collapseFour" role="tabpanel">
                                    <div class="acc-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-xl-4">
                                                        <div class="card item-card">
                                                            <div class="card-body pb-0">
                                                                <div class="text-center">
                                                                    <img src="{{URL::asset('assets/images/products/1.jpg')}}" alt="img" class="img-fluid w-100">
                                                                </div>
                                                                <div class="card-body px-0 ">
                                                                    <div class="cardtitle">
                                                                        <a class="shop-title">Theme #3</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center pb-4 pl-4 pr-4">
                                                                <a href="{{url('/' . $page='shop-des')}}" class="btn btn-primary btn-block mb-2"><i class="fe fe-eye mr-1"></i>View More</a>
                                                                <a href="{{url('/' . $page='cart')}}" class="btn btn-secondary btn-block"><i class="fe fe-monitor mr-1"></i>Apply</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="card item-card">
                                                            <div class="card-body pb-0">
                                                                <div class="text-center">
                                                                    <img src="{{URL::asset('assets/images/products/2.jpg')}}" alt="img" class="img-fluid w-100">
                                                                </div>
                                                                <div class="card-body px-0 ">
                                                                    <div class="cardtitle">
                                                                        <a class="shop-title">Theme #2</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center pb-4 pl-4 pr-4">
                                                                <a href="{{url('/' . $page='shop-des')}}" class="btn btn-primary btn-block mb-2"><i class="fe fe-eye mr-1"></i>View More</a>
                                                                <a href="{{url('/' . $page='cart')}}" class="btn btn-secondary btn-block"><i class="fe fe-monitor mr-1"></i>Apply</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-4">
                                                        <div class="card item-card">
                                                            <div class="card-body pb-0">
                                                                <div class="text-center">
                                                                    <img src="{{URL::asset('assets/images/products/3.jpg')}}" alt="img" class="img-fluid w-100">
                                                                </div>
                                                                <div class="card-body px-0 ">
                                                                    <div class="cardtitle">
                                                                        <a class="shop-title">Theme #3</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="text-center pb-4 pl-4 pr-4">
                                                                <a href="{{url('/' . $page='shop-des')}}" class="btn btn-primary btn-block mb-2"><i class="fe fe-eye mr-1"></i>View More</a>
                                                                <a href="{{url('/' . $page='cart')}}" class="btn btn-secondary btn-block"><i class="fe fe-monitor mr-1"></i>Apply</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- collapse -->
                            </div>
                        </div><!-- accordion -->
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{ route('setting.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
@section('js')
		<script src="{{URL::asset('assets/plugins/accordion/accordion.min.js')}}"></script>
		<script src="{{URL::asset('assets/js/accordion.js')}}"></script>
        <script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
        <script>
            $(document).ready(function(){
                checkValidation();
                var siteSlug = "{{ request()->route('username') }}";
                $('select[name="created_by"]').on('change', function () {
                    var selectedId = $(this).val();
                    if (selectedId) {
                        $.ajax({
                            url: "{{ url('e/setting/check-setting/') }}" + "/" + selectedId, 
                            type: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                if (data.exists) {
                                    var editUrl = "{{ route('setting.edit', ['username' => 'SITE_SLUG', 'setting' => ':id']) }}";

                                    editUrl = editUrl.replace('SITE_SLUG', siteSlug).replace(':id', data.setting_id);

                                    window.location.href = editUrl;
                                } else {
                                    if(data.admin) {
                                        var createUrl = "{{ route('setting.edit', ['username' => 'SITE_SLUG', 'setting' => ':id']) }}";
                                        createUrl = createUrl.replace('SITE_SLUG', siteSlug).replace(':id', data.admin);
                                        window.location.href = createUrl;
                                    }
                                }
                                // else do nothing
                            },
                            error: function (xhr, status, error) {
                                console.error('AJAX Error:', error);
                            }
                        });
                    }
                });
            })
            function checkValidation() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.forEach.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();

                            // Find the first invalid input
                            var firstInvalid = form.querySelector(':invalid');
                            if (firstInvalid) {
                                firstInvalid.focus();

                                // Find the closest collapse panel and open it
                                var collapseDiv = firstInvalid.closest('.collapse');
                                if (collapseDiv && !collapseDiv.classList.contains('show')) {
                                    // First close all open accordions
                                    var allCollapses = document.querySelectorAll('#accordion .collapse.show');
                                    allCollapses.forEach(function(openCollapse) {
                                        $(openCollapse).collapse('hide');
                                    });

                                    // Then open the accordion which has the invalid field
                                    $(collapseDiv).collapse('show');
                                }

                                // Scroll into view smoothly
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }


        </script>
@endsection