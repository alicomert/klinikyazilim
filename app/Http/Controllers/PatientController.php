<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientNote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $patients = Patient::accessibleBy($user)
            ->with(['doctor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'tc_identity' => 'required|string|size:11|unique:patients,tc_identity',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'address' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'previous_operations' => 'nullable|string',
            'complaints' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'planned_operation' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
        ]);
        
        // Doktor ID'sini ata
        $validatedData['doctor_id'] = $user->getDoctorIdForFiltering();
        $validatedData['is_active'] = true;
        $validatedData['last_visit'] = now();
        
        $patient = Patient::create($validatedData);
        
        return redirect()->route('patients.show', $patient)
            ->with('success', 'Hasta başarıyla kaydedildi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $patient = Patient::accessibleBy($user)
            ->with(['doctor', 'notes.user'])
            ->findOrFail($id);
            
        $notes = PatientNote::where('patient_id', $patient->id)
            ->accessibleBy($user)
            ->visibleTo($user)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('patients.show', compact('patient', 'notes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $patient = Patient::accessibleBy($user)->findOrFail($id);
        
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $patient = Patient::accessibleBy($user)->findOrFail($id);
        
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'tc_identity' => 'required|string|size:11|unique:patients,tc_identity,' . $patient->id,
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'address' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'previous_operations' => 'nullable|string',
            'complaints' => 'nullable|string',
            'anamnesis' => 'nullable|string',
            'physical_examination' => 'nullable|string',
            'planned_operation' => 'nullable|string',
            'chronic_conditions' => 'nullable|string',
        ]);
        
        $patient->update($validatedData);
        
        return redirect()->route('patients.show', $patient)
            ->with('success', 'Hasta bilgileri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $patient = Patient::accessibleBy($user)->findOrFail($id);
        
        // Sadece admin veya hasta sahibi doktor silebilir
        if (!$user->isAdmin() && $patient->doctor_id !== $user->getDoctorIdForFiltering()) {
            return redirect()->back()
                ->with('error', 'Bu hastayı silme yetkiniz yok.');
        }
        
        $patient->delete();
        
        return redirect()->route('patients.index')
            ->with('success', 'Hasta kaydı başarıyla silindi.');
    }
    
    /**
     * Get patients data for API/AJAX requests
     */
    public function getPatients(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Patient::accessibleBy($user);
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        
        $patients = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));
            
        return response()->json($patients);
    }
}
