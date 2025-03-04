<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new User([
            'username' => trim($row['username']),
            'name'     => trim($row['name']),
            'password' => Hash::make($row['password']),
            'role'     => strtolower(trim($row['role'])),
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => 'required|unique:users,username',
            'name'     => 'required',
            'password' => 'required|min:5',
            'role'     => 'required|in:mahasiswa,dosen,instruktur,admin_fakultas,pimpinan_fakultas,mitra',
        ];
    }
}
