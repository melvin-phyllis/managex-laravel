<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollCountry;
use App\Models\PayrollCountryField;
use App\Models\PayrollCountryRule;
use Illuminate\Http\Request;

class PayrollSettingsController extends Controller
{
    /**
     * Liste des pays configurés
     */
    public function countries()
    {
        $countries = PayrollCountry::withCount(['rules', 'fields', 'templates'])
            ->orderBy('name')
            ->get();

        return view('admin.payroll-settings.countries.index', compact('countries'));
    }

    /**
     * Formulaire création pays
     */
    public function createCountry()
    {
        return view('admin.payroll-settings.countries.create');
    }

    /**
     * Enregistrer nouveau pays
     */
    public function storeCountry(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:payroll_countries,code',
            'name' => 'required|string|max:100',
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        PayrollCountry::create($validated);

        return redirect()->route('admin.payroll-settings.countries')
            ->with('success', 'Pays ajouté avec succès.');
    }

    /**
     * Formulaire édition pays
     */
    public function editCountry(PayrollCountry $country)
    {
        return view('admin.payroll-settings.countries.edit', compact('country'));
    }

    /**
     * Mettre à jour pays
     */
    public function updateCountry(Request $request, PayrollCountry $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'currency' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country->update($validated);

        return redirect()->route('admin.payroll-settings.countries')
            ->with('success', 'Pays mis à jour.');
    }

    /**
     * Supprimer pays
     */
    public function destroyCountry(PayrollCountry $country)
    {
        $country->delete();

        return redirect()->route('admin.payroll-settings.countries')
            ->with('success', 'Pays supprimé.');
    }

    // ==================== RÈGLES ====================

    /**
     * Liste des règles d'un pays
     */
    public function rules(PayrollCountry $country)
    {
        $rules = $country->rules()->orderBy('display_order')->get();

        return view('admin.payroll-settings.rules.index', compact('country', 'rules'));
    }

    /**
     * Formulaire création règle
     */
    public function createRule(PayrollCountry $country)
    {
        return view('admin.payroll-settings.rules.create', compact('country'));
    }

    /**
     * Enregistrer règle
     */
    public function storeRule(Request $request, PayrollCountry $country)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rule_type' => 'required|in:tax,contribution,allowance,deduction,earning',
            'rule_category' => 'required|in:employee,employer,both',
            'calculation_type' => 'required|in:percentage,fixed,bracket,formula',
            'rate' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'brackets' => 'nullable|json',
            'base_field' => 'required|string',
            'ceiling' => 'nullable|numeric|min:0',
            'floor' => 'nullable|numeric|min:0',
            'is_deductible' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_visible_on_payslip' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'pdf_code' => 'nullable|string|max:10',
        ]);

        if (isset($validated['brackets'])) {
            $validated['brackets'] = json_decode($validated['brackets'], true);
        }

        $country->rules()->create($validated);

        return redirect()->route('admin.payroll-settings.rules', $country)
            ->with('success', 'Règle ajoutée.');
    }

    /**
     * Formulaire édition règle
     */
    public function editRule(PayrollCountry $country, PayrollCountryRule $rule)
    {
        return view('admin.payroll-settings.rules.edit', compact('country', 'rule'));
    }

    /**
     * Mettre à jour règle
     */
    public function updateRule(Request $request, PayrollCountry $country, PayrollCountryRule $rule)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rule_type' => 'required|in:tax,contribution,allowance,deduction,earning',
            'rule_category' => 'required|in:employee,employer,both',
            'calculation_type' => 'required|in:percentage,fixed,bracket,formula',
            'rate' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'brackets' => 'nullable|json',
            'base_field' => 'required|string',
            'ceiling' => 'nullable|numeric|min:0',
            'floor' => 'nullable|numeric|min:0',
            'is_deductible' => 'boolean',
            'is_mandatory' => 'boolean',
            'is_visible_on_payslip' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'pdf_code' => 'nullable|string|max:10',
        ]);

        if (isset($validated['brackets'])) {
            $validated['brackets'] = json_decode($validated['brackets'], true);
        }

        $rule->update($validated);

        return redirect()->route('admin.payroll-settings.rules', $country)
            ->with('success', 'Règle mise à jour.');
    }

    /**
     * Supprimer règle
     */
    public function destroyRule(PayrollCountry $country, PayrollCountryRule $rule)
    {
        $rule->delete();

        return redirect()->route('admin.payroll-settings.rules', $country)
            ->with('success', 'Règle supprimée.');
    }

    // ==================== CHAMPS DYNAMIQUES ====================

    /**
     * Liste des champs d'un pays
     */
    public function fields(PayrollCountry $country)
    {
        $fields = $country->fields()->orderBy('display_order')->get();

        return view('admin.payroll-settings.fields.index', compact('country', 'fields'));
    }

    /**
     * Créer champ
     */
    public function storeField(Request $request, PayrollCountry $country)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:100',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,number,select,date,boolean,textarea',
            'options' => 'nullable|json',
            'is_required' => 'boolean',
            'default_value' => 'nullable|string',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'section' => 'required|string',
            'is_taxable' => 'boolean',
            'affects_gross' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if (isset($validated['options'])) {
            $validated['options'] = json_decode($validated['options'], true);
        }

        $country->fields()->create($validated);

        return redirect()->route('admin.payroll-settings.fields', $country)
            ->with('success', 'Champ ajouté.');
    }

    /**
     * Supprimer champ
     */
    public function destroyField(PayrollCountry $country, PayrollCountryField $field)
    {
        $field->delete();

        return redirect()->route('admin.payroll-settings.fields', $country)
            ->with('success', 'Champ supprimé.');
    }
}
