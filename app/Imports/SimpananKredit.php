<?php

namespace App\Imports;

use App\TransaksiHarian;
use Maatwebsite\Excel\Concerns\ToModel;
use Tanggal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\TransaksiHarianAnggota;
use App\Anggota;
use App\TransaksiHarianBiaya;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SimpananKredit implements ToCollection, WithStartRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    protected $periode;
	protected $anggota;

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $transaksiHarian = TransaksiHarian::create([
                'divisi_id' => '1',
                'tgl' => Tanggal::transformDate($row[1]),
                'jenis_pembayaran' => $row[2],
                'keterangan' => $row[3],
                'jenis_transaksi' => '2',
                'periode_id' => $this->periode->id,
            ]);
			$this->anggota = Anggota::select()->where('nik', '=', $row[4])->value('id');
            TransaksiHarianAnggota::create([
                'transaksi_harian_id' => $transaksiHarian->id,
                'anggota_id' => $this->anggota,
            ]);

            TransaksiHarianBiaya::create([
                'transaksi_harian_id' => $transaksiHarian->id,
                'biaya_id' => '4',
                'nominal' => $row[5]
            ]);
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required',

            // Above is alias for as it always validates in batches
            '*.0' => 'required',

            '1' => 'required',

            // Above is alias for as it always validates in batches
            '*.1' => 'required',

            '2' => 'required',

            // Above is alias for as it always validates in batches
            '*.2' => 'required',
            '3' => 'required',

            // Above is alias for as it always validates in batches
            '*.3' => 'required',

            '4' => 'required',

            // Above is alias for as it always validates in batches
            '*.4' => 'required',
            '5' => 'required',

            // Above is alias for as it always validates in batches
            '*.5' => 'required'
        ];
    }
}
