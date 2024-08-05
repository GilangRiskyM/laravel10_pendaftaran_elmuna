<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditMengemudiRequest;
use App\Http\Requests\TambahMengemudiRequest;
use App\Models\Mengemudi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MengemudiController extends Controller
{
    function create()
    {
        return view('pendaftaran.mengemudi');
    }

    function store(TambahMengemudiRequest $request)
    {
        $request->validated();
        $request['tanggal'] = date('Y-m-d');
        $request['paket'] = json_encode($request->paket);
        $sql = Mengemudi::create($request->all());

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Anda [' . $request->nama . '] Berhasil Mendaftar!!!');
        }

        return redirect('/daftar_mengemudi');
    }

    function index(Request $request)
    {
        $cari = $request->cari;

        if (isset($request->cari)) {
            $data = Mengemudi::orderBy('id', 'desc')
                ->where('nik', 'LIKE', '%' . $cari . '%')
                ->orWhere('nama', 'LIKE', '%' . $cari . '%')
                ->orWhere('alamat', 'LIKE', '%' . $cari . '%')
                ->orWhere('tempat_lahir', 'LIKE', '%' . $cari . '%')
                ->orWhere('tanggal_lahir', 'LIKE', '%' . $cari . '%')
                ->orWhere('jk', 'LIKE', '%' . $cari . '%')
                ->orWhere('nama_ayah', 'LIKE', '%' . $cari . '%')
                ->orWhere('nama_ibu', 'LIKE', '%' . $cari . '%')
                ->orWhere('telepon', 'LIKE', '%' . $cari . '%')
                ->orWhere('email', 'LIKE', '%' . $cari . '%')
                ->orWhere('kecamatan', 'LIKE', '%' . $cari . '%')
                ->orWhere('paket', 'LIKE', '%' . $cari . '%')
                ->get();
        } else {
            $data = Mengemudi::orderBy('id', 'desc')->get();
        }

        return view('admin.mengemudi.mengemudi', ['data' => $data]);
    }

    function filterData(Request $request)
    {
        $start_date = $request->tgl_awal;
        $end_date = $request->tgl_akhir;

        $data = Mengemudi::orderBy('id', 'desc')
            ->whereDate('tanggal', '>=', $start_date)
            ->whereDate('tanggal', '<=', $end_date)
            ->get();

        return view('admin.mengemudi.mengemudi', ['data' => $data]);
    }

    function edit($id)
    {
        $data = Mengemudi::where('id', $id)->get();

        return view('admin.mengemudi.edit-mengemudi', ['data' => $data]);
    }

    function update(EditMengemudiRequest $request, $id)
    {
        $request->validated();
        $request['paket'] = json_encode($request->paket);
        $sql = Mengemudi::findOrFail($id);
        $update = $sql->update($request->all());
        if ($update) {
            Session::flash('status', 'success');
            Session::flash('message', 'Edit Data Berhasil!!!');
        }

        return redirect('/data_mengemudi');
    }

    function delete($id)
    {
        $data = Mengemudi::findOrFail($id);

        return view('admin.mengemudi.hapus-mengemudi', ['data' => $data]);
    }

    function destroy($id)
    {
        $sql = Mengemudi::findOrFail($id);
        $delete = $sql->delete();
        if ($delete) {
            Session::flash('status', 'success');
            Session::flash('message', 'Hapus Data Berhasil!!!');
        }

        return redirect('/data_mengemudi');
    }

    function deletedMengemudi()
    {
        $data = Mengemudi::onlyTrashed()->get();

        return view('admin.mengemudi.data-terhapus', ['data' => $data]);
    }

    function restoreData($id)
    {
        $sql = Mengemudi::withTrashed()
            ->where('id', $id)
            ->restore();

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Restore Data Berhasil!!!');
        }

        return redirect('/data_mengemudi');
    }

    function deletePermanen($id)
    {
        $data = Mengemudi::withTrashed()
            ->findOrFail($id);

        return view('admin.mengemudi.hapus-permanen', ['data' => $data]);
    }

    function forceDelete($id)
    {
        $sql = Mengemudi::withTrashed()
            ->findOrFail($id)
            ->forceDelete();

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Berhasil Hapus Data Secara Permanen!!!');
        }

        return redirect('/data_mengemudi/terhapus');
    }

    function export(Request $request)
    {
        $start_date = $request->tgl_awal;
        $end_date = $request->tgl_akhir;

        $sql = Mengemudi::orderBy('id', 'desc')
            ->whereDate('tanggal', '>=', $start_date)
            ->whereDate('tanggal', '<=', $end_date)
            ->get();

        $laki_laki = Mengemudi::where('jk', 'Laki-laki')->count();
        $perempuan = Mengemudi::where('jk', 'Perempuan')->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'Alamat');
        $sheet->setCellValue('E1', 'Tempat Lahir');
        $sheet->setCellValue('F1', 'Tanggal Lahir');
        $sheet->setCellValue('G1', 'Jenis Kelamin');
        $sheet->setCellValue('H1', 'Nama Ayah');
        $sheet->setCellValue('I1', 'Nama Ibu');
        $sheet->setCellValue('J1', 'No. WA');
        $sheet->setCellValue('K1', 'Email');
        $sheet->setCellValue('L1', 'Kecamatan');
        $sheet->setCellValue('M1', 'paket');
        $sheet->setCellValue('N1', 'Tanggal Mendaftar');
        $sheet->setCellValue('O1', 'Jumlah Peserta Laki-laki');
        $sheet->setCellValue('P1', 'Jumlah Peserta Perempuan');

        $no = 1;
        $rows = 2;


        $filename = "Laporan Daftar Peserta Mengemudi " . date($start_date) . " sampai " . date($end_date) . ".xlsx";

        foreach ($sql as $data) {
            $sheet->setCellValue('A' . $rows, $no++);
            $sheet->setCellValue('B' . $rows, $data->nik);
            $sheet->setCellValue('C' . $rows, $data->nama);
            $sheet->setCellValue('D' . $rows, $data->alamat);
            $sheet->setCellValue('E' . $rows, $data->tempat_lahir);
            $sheet->setCellValue('F' . $rows, $data->tanggal_lahir);
            $sheet->setCellValue('G' . $rows, $data->jk);
            $sheet->setCellValue('H' . $rows, $data->nama_ayah);
            $sheet->setCellValue('I' . $rows, $data->nama_ibu);
            $sheet->setCellValue('J' . $rows, $data->telepon);
            $sheet->setCellValue('K' . $rows, $data->email);
            $sheet->setCellValue('L' . $rows, $data->kecamatan);

            // Decode the JSON paket
            $paket = json_decode($data->paket, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Jika json_decode berhasil
                if (is_array($paket)) {
                    // Gabungkan semua nilai menjadi satu string
                    $paketString = implode(", ", $paket);
                } elseif (is_object($paket)) {
                    // Jika berbentuk objek, ubah menjadi array kemudian gabungkan
                    $paketString = implode(", ", (array) $paket);
                } else {
                    // Jika tipe data lain, langsung jadikan string
                    $paketString = (string) $paket;
                }
            } else {
                // Jika json_decode gagal, set nilai ke string kosong atau pesan error
                $paketString = 'Invalid JSON';
            }

            $sheet->setCellValue('M' . $rows, $paketString);

            $sheet->setCellValue('N' . $rows, $data->tanggal);
            $rows++;
        }

        $sheet->setCellValue('O2', $laki_laki);
        $sheet->setCellValue('P2', $perempuan);

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
