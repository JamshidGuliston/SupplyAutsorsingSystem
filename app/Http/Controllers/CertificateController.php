<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index()
    {
        // Barcha sertifikatlarni olish
        $certificates = Certificate::with('products')->get();

        // Muddati yaqinlashgan sertifikatlarni olish
        $expiringCertificates = Certificate::expiringSoon()->get();
        
        return view('technolog.certificates.index', compact('certificates', 'expiringCertificates'));
    }

    public function create()
    {
        $products = Product::where('hide', 1)->whereNull('certificate_id')->get();
        return view('technolog.certificates.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'certificate_number' => 'required|unique:certificates',
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pdf_file' => 'required|mimes:pdf|max:10240', // max 10MB
            'image_file' => 'nullable|image|max:5120', // max 5MB
            'products' => 'required|array',
            'products.*' => 'exists:products,id'
        ]);

        // PDF faylni saqlash
        $pdfPath = $request->file('pdf_file')->store('certificates/pdf', 'public');
        
        // Rasm faylni saqlash (agar yuklangan bo'lsa)
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('certificates/images', 'public');
        }

        // Sertifikat yaratish
        $certificate = Certificate::create([
            'certificate_number' => $request->certificate_number,
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'pdf_file' => $pdfPath,
            'image_file' => $imagePath,
            'is_active' => true
        ]);

        // Maxsulotlarni bog'lash
        Product::whereIn('id', $request->products)->update(['certificate_id' => $certificate->id]);

        return redirect()->route('technolog.certificates.index')
            ->with('success', 'Sertifikat muvaffaqiyatli yaratildi');
    }

    public function edit($id)
    {
        $certificate = Certificate::with('products')->findOrFail($id);
        // Hozirgi sertifikatga bog'langan va bog'lanmagan maxsulotlarni olish
        $products = Product::where('hide', 1)
            ->where(function($query) use ($id) {
                $query->whereNull('certificate_id')
                      ->orWhere('certificate_id', $id);
            })
            ->get();
        return view('technolog.certificates.edit', compact('certificate', 'products'));
    }

    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);

        $request->validate([
            'certificate_number' => 'required|unique:certificates,certificate_number,'.$id,
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
            'image_file' => 'nullable|image|max:5120',
            'products' => 'required|array',
            'products.*' => 'exists:products,id'
        ]);

        // PDF faylni yangilash
        if ($request->hasFile('pdf_file')) {
            // Eski faylni o'chirish
            Storage::disk('public')->delete($certificate->pdf_file);
            // Yangi faylni saqlash
            $pdfPath = $request->file('pdf_file')->store('certificates/pdf', 'public');
            $certificate->pdf_file = $pdfPath;
        }

        // Rasm faylni yangilash
        if ($request->hasFile('image_file')) {
            // Eski faylni o'chirish
            if ($certificate->image_file) {
                Storage::disk('public')->delete($certificate->image_file);
            }
            // Yangi faylni saqlash
            $imagePath = $request->file('image_file')->store('certificates/images', 'public');
            $certificate->image_file = $imagePath;
        }

        // Sertifikatni yangilash
        $certificate->update([
            'certificate_number' => $request->certificate_number,
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active')
        ]);

        // Avvalgi bog'langan maxsulotlarni bo'shatish
        Product::where('certificate_id', $id)->update(['certificate_id' => null]);
        
        // Yangi maxsulotlarni bog'lash
        Product::whereIn('id', $request->products)->update(['certificate_id' => $id]);

        return redirect()->route('technolog.certificates.index')
            ->with('success', 'Sertifikat muvaffaqiyatli yangilandi');
    }

    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Fayllarni o'chirish
        Storage::disk('public')->delete($certificate->pdf_file);
        if ($certificate->image_file) {
            Storage::disk('public')->delete($certificate->image_file);
        }

        // Bog'langan maxsulotlarni bo'shatish
        Product::where('certificate_id', $id)->update(['certificate_id' => null]);

        // Sertifikatni o'chirish (soft delete)
        $certificate->delete();

        return redirect()->route('technolog.certificates.index')
            ->with('success', 'Sertifikat muvaffaqiyatli o\'chirildi');
    }

    public function show($id)
    {
        $certificate = Certificate::with('products')->findOrFail($id);
        return view('technolog.certificates.show', compact('certificate'));
    }
} 