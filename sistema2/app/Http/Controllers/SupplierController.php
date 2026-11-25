<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    // Listado de proveedores (los más recientes primero)
    public function index()
    {
        $suppliers = Supplier::latest()->get();

        return view('management.suppliers.index', compact('suppliers'));
    }

    // Formulario para crear
    public function create()
    {
        return view('management.suppliers.create');
    }

    // Guardar nuevo proveedor
    public function store(Request $request)
    {
        // validate() devuelve solo los campos validados; si algo falla, regresa al formulario con los errores
        $validated = $request->validate([
            'name' => 'required|max:100',
            'ruc' => 'required|digits:11|unique:suppliers,ruc',
            'phone' => 'nullable|digits:9',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|max:150',
        ]);

        Supplier::create($validated); // INSERT (solo los campos de #[Fillable])

        // Patrón PRG: se redirige al listado con un mensaje flash
        return to_route('suppliers.index')->with('ok', 'Proveedor registrado');
    }

    // Formulario para editar
    public function edit(Supplier $supplier)
    {
        return view('management.suppliers.edit', compact('supplier'));
    }

    // Actualizar proveedor (el RUC sigue siendo único, ignorando el propio)
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'ruc' => ['required', 'digits:11', Rule::unique('suppliers', 'ruc')->ignore($supplier->id)],
            'phone' => 'nullable|digits:9',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|max:150',
        ]);

        $supplier->update($validated);

        return to_route('suppliers.index')->with('ok', 'Proveedor actualizado');
    }

    // Eliminar proveedor
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return to_route('suppliers.index')->with('ok', 'Proveedor eliminado');
    }
}
