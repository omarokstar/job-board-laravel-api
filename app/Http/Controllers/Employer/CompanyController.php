<?php

namespace App\Http\Controllers\Employer;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
 
    public function index()
    {
        return  CompanyResource::collection(Company::all());
    }

    public function store(StoreCompanyRequest $request)
{


    $data = $request->only([
        'company_name', 'email', 'company_address', 'city', 'country',
        'phone', 'about', 'organization_type', 'establishment_year',
        'company_vision', 'industry_type', 'team_size', 'company_website',
        'linkedIn', 'facebook', 'twitter', 'github',
    ]);

    if ($request->hasFile('banner')) {
        $imageName = time() . '_banner.' . $request->banner->extension(); 
        $request->file('banner')->storeAs('images', $imageName, 'public');
        $data['banner'] = $imageName;
    }

    if ($request->hasFile('logo')) {
        $imageName = time() . '_logo.' . $request->logo->extension(); 
        $request->file('logo')->storeAs('images', $imageName, 'public');
        $data['logo'] = $imageName;
    }
    $data['user_id'] = Auth::id();
    $company = Company::create($data);

    return new CompanyResource($company);
}

    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        return new CompanyResource($company);
    }

    public function update(UpdateCompanyRequest $request, string $id)
    {

        $company = Company::findOrFail($id);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        $user = Auth::user();
        if ($user->id !== $company->user_id && !($user->role=='admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
     

        $data = $request->only([
            'company_name',
            'email',
            'company_address',
            'city',
            'country',
            'phone',
            'about',
            'organization_type',
            'establishment_year',
            'company_vision',
            'industry_type',
            'team_size',
            'company_website',
            'linkedIn',
            'facebook',
            'twitter',
        ]);

        if ($request->hasFile('banner')) {
            $imageName = time() . '.' . $request->banner->extension(); 
            $request->file('banner')->storeAs('images', $imageName, 'public');
            $data['banner'] = $imageName;
        } else {
            $data['banner'] = null;
        }
        if ($request->hasFile('logo')) {
            $imageName = time() . '.' . $request->logo->extension(); 
            $request->file('logo')->storeAs('images', $imageName, 'public');
            $data['logo'] = $imageName;
        } else {
            $data['logo'] = null;
        }
        $company->update($request->all());
        return CompanyResource::make($company);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();
        $company = Company::findOrFail($id);
        if ($user->id !== $company->user_id && !($user->role=='admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
     
        $company->delete();
        return response()->json(['message' => 'Company deleted successfully']);

    }
}
