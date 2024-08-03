<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditDesainGrafisRequest;
use App\Http\Requests\TambahDesainGrafisRequest;
use App\Models\DesainGrafis;
use Illuminate\Support\Facades\Session;

class DesainGrafisController extends Controller
{
    function create()
    {
        return view('pendaftaran.desain_grafis');
    }


    function store(TambahDesainGrafisRequest $request)
    {
        $request->validated();
        $request['paket'] = json_encode($request->paket);
        $sql = DesainGrafis::create($request->all());

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Anda [' . $request->nama . '] Berhasil Mendaftar!!!');
        }

        return redirect('/daftar_desain_grafis');
    }

    function index()
    {
        $data = DesainGrafis::get();

        return view('admin.desain_grafis.desain_grafis', ['data' => $data]);
    }

    function edit($id)
    {
        $data = DesainGrafis::where('id', $id)->get();

        return view('admin.desain_grafis.edit-desain_grafis', ['data' => $data]);
    }

    function update(EditDesainGrafisRequest $request, $id)
    {
        $request->validated();
        $request['paket'] = json_encode($request->paket);
        $sql = DesainGrafis::findOrFail($id);
        $update = $sql->update($request->all());
        if ($update) {
            Session::flash('status', 'success');
            Session::flash('message', 'Edit Data Berhasil!!!');
        }

        return redirect('/data_desain_grafis');
    }

    function delete($id)
    {
        $data = DesainGrafis::findOrFail($id);

        return view('admin.desain_grafis.hapus-desain_grafis', ['data' => $data]);
    }

    function destroy($id)
    {
        $sql = DesainGrafis::findOrFail($id);
        $delete = $sql->delete();
        if ($delete) {
            Session::flash('status', 'success');
            Session::flash('message', 'Hapus Data Berhasil!!!');
        }

        return redirect('/data_desain_grafis');
    }

    function deletedDesainGrafis()
    {
        $data = DesainGrafis::onlyTrashed()->get();

        return view('admin.desain_grafis.data-terhapus', ['data' => $data]);
    }

    function restoreData($id)
    {
        $sql = DesainGrafis::withTrashed()
            ->where('id', $id)
            ->restore();

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Restore Data Berhasil!!!');
        }

        return redirect('/data_desain_grafis');
    }

    function deletePermanen($id)
    {
        $data = DesainGrafis::withTrashed()
            ->findOrFail($id);

        return view('admin.desain_grafis.hapus-permanen', ['data' => $data]);
    }

    function forceDelete($id)
    {
        $sql = DesainGrafis::withTrashed()
            ->findOrFail($id)
            ->forceDelete();

        if ($sql) {
            Session::flash('status', 'success');
            Session::flash('message', 'Berhasil Hapus Data Secara Permanen!!!');
        }

        return redirect('/data_desain_grafis/terhapus');
    }
}
