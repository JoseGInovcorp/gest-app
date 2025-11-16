<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Show the form for editing company settings.
     */
    public function edit(Request $request)
    {
        $company = Company::getInstance();

        return Inertia::render('Company/Edit', [
            'company' => $company,
            'can' => [
                'update' => $request->user()->can('company.update'),
            ],
        ]);
    }

    /**
     * Update the company settings in storage.
     */
    public function update(Request $request)
    {
        // Verificar permissão
        if (!$request->user()->can('company.update')) {
            abort(403, 'Não tem permissão para atualizar dados da empresa.');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'nif' => 'nullable|string|size:9',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // 2MB max
        ]);

        $company = Company::getInstance();

        // Upload do logo se fornecido
        if ($request->hasFile('logo')) {
            // Apagar logo antigo se existir
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            // Upload novo logo
            $path = $request->file('logo')->store('company/logos', 'public');
            $validated['logo'] = $path;
        }

        $company->update($validated);

        activity()
            ->performedOn($company)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'logo_updated' => $request->hasFile('logo')
            ])
            ->log('updated');

        return redirect()
            ->route('company.edit')
            ->with('success', 'Dados da empresa atualizados com sucesso.');
    }
}
