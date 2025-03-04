<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    /**
    * Mengambil data dari tabel users untuk diekspor
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select("username", "name", "role")->get();
    }

    /**
     * Menentukan header dalam file Excel yang diekspor
     */
    public function headings(): array
    {
        return ["Username", "Name", "Role"];
    }
}
