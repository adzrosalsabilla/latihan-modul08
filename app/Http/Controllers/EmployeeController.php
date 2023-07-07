<?php

namespace App\Http\Controllers;

use PDF;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmployeesExport;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $pageTitle = 'Employee List';

        confirmDelete();

        return view('employee.index', compact('pageTitle'));

        // $pageTitle = 'Employee List';

        // // ELOQUENT
        // $employees = Employee::all();

        // return view('employee.index', [
        //     'pageTitle' => $pageTitle,
        //     'employees' => $employees
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $pageTitle = 'Create Employee';

        // ELOQUENT
        $positions = Position::all();

        return view('employee.create', compact('pageTitle', 'positions'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Meletakkan SweetAlert
        Alert::success('Added Successfully', 'Employee Data Added Successfully.');

        return redirect()->route('employees.index');

        // Mendefinisikan pesan kesalahan untuk validasi input
        $messages = [
            'required' => ':attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar.',
            'numeric' => 'Isi :attribute dengan angka.'
        ];

        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        // Jika terdapat kesalahan validasi, kembalikan kembali ke halaman sebelumnya dengan pesan kesalahan dan input yang diisi sebelumnya
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get File
        $file = $request->file('cv');

        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store File
            $file->store('public/files');
        }

            // ELOQUENT
        $employee = New Employee;
        $employee->firstname = $request->firstName;
        $employee->lastname = $request->lastName;
        $employee->email = $request->email;
        $employee->age = $request->age;
        $employee->position_id = $request->position;

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();


        return redirect()->route('employees.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $pageTitle = 'Employee Detail';

        // ELOQUENT
        $employee = Employee::find($id);

        return view('employee.show', compact('pageTitle', 'employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Edit Employee';

        // ELOQUENT
        $positions = Position::all();
        $employee = Employee::find($id);

        return view('employee.edit', compact('pageTitle', 'employee', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Meletakkan SweetAlert
        Alert::success('Changed Successfully', 'Employee Data Changed Successfully.');

        return redirect()->route('employees.index');

        $messages = [
            'required' => ':Attribute harus diisi.',
            'email' => 'Isi :attribute dengan format yang benar',
            'numeric' => 'Isi :attribute dengan angka'
        ];

        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'age' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Get File
        $file = $request->file('cv');

        // kondisi 1 save lanjut hapus
        if ($file != null) {
            $originalFilename = $file->getClientOriginalName();
            $encryptedFilename = $file->hashName();

            // Store File
            $file->store('public/files');

            // Hapus file lama jika ada
            $employee = Employee::find($id);
            if ($employee->encrypted_filename) {
                Storage::delete('public/files/'.$employee->encrypted_filename);
            }
        }

        // // logic 2 hapus lanjut save
        // if ($file != null) {
        //     $employee = Employee::find($id);
        //     $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
        //     Storage::delete($encryptedFilename);
        // }
        // if ($file != null) {
        //     $originalFilename = $file->getClientOriginalName();
        //     $encryptedFilename = $file->hashName();

        //     // Store File
        //     $file->store('public/files');
        // }

        // ELOQUENT
        $employee = Employee::find($id);
        $employee->firstname = $request->input('firstName');
        $employee->lastname = $request->input('lastName');
        $employee->email = $request->input('email');
        $employee->age = $request->input('age');
        $employee->position_id = $request->input('position');

        if ($file != null) {
            $employee->original_filename = $originalFilename;
            $employee->encrypted_filename = $encryptedFilename;
        }

        $employee->save();

        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Meletakkan SweetAlert
        Alert::success('Deleted Successfully', 'Employee Data Deleted Successfully.');

        return redirect()->route('employees.index');

        // ELOQUENT
        $employee = Employee::find($id);

        // kondisi 1 Hapus file terkait tidak menggunakan path jika ada
        if ($employee->encrypted_filename) {
            Storage::delete('public/files/'.$employee->encrypted_filename);
        }

        // // kondisi 2 Hapus file terkait menggunakan path jika ada
        // if ($employee->encrypted_filename) {
        //     $path = 'files/'.$employee->encrypted_filename;
        //     Storage::disk('public')->delete($path);
        // }

        $employee->delete();

        return redirect()->route('employees.index');
    }

    public function downloadFile($employeeId)
    {
        $employee = Employee::find($employeeId);
        $encryptedFilename = 'public/files/'.$employee->encrypted_filename;
        $downloadFilename = Str::lower($employee->firstname.'_'.$employee->lastname.'_cv.pdf');

        if(Storage::exists($encryptedFilename)) {
            return Storage::download($encryptedFilename, $downloadFilename);
        }
    }

    // method getData()
    public function getData(Request $request)
    {
    $employees = Employee::with('position');

    if ($request->ajax()) {
        return datatables()->of($employees)
            ->addIndexColumn()
            ->addColumn('actions', function($employee) {
                return view('employee.actions', compact('employee'));
            })
            ->toJson();
        }
    }

    // EXCEL
    public function exportExcel()
    {
    return Excel::download(new EmployeesExport, 'employees.xlsx');
    }

    // PDF
    public function exportPdf()
    {
    $employees = Employee::all();

    $pdf = PDF::loadView('employee.export_pdf', compact('employees'));

    return $pdf->download('employees.pdf');
    }


}
