<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MutasiBahanBakuModel;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use App\Helpers\ReturnMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MutasiBahanBakuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function tambah(Request $request)
    {
        if (Auth::user()->akses=='superadmin')
        {
        $requests = json_decode($request->getContent());
        $validated=  $this->validate($request, [
            'data.*.id_bahan_baku'=>'numeric|required|exists:bahan_baku,id_bahan_baku|max:20',
            'data.*.harga'=> 'numeric|required|regex:/^[1-9]+[0-9]*$/|not_in:0',
            'data.*.jumlah_masuk'=> 'numeric|regex:/^[1-9]+[0-9]*$/|not_in:0'
            
        ]);
        $datamutasi;
        $databahanbakuupdate;
        $i=0;
        foreach ($validated['data'] as $val) {
           $datamutasi[$i]=array(
               'id_bahan_baku'=>$val['id_bahan_baku'],
               'harga' => $val['harga'],
               'jumlah_masuk'=>$val['jumlah_masuk'],
               'created_at'=>date('Y-m-d H:i:s'),
               'created_off'=>Auth::user()->id,
           );
           $databahanbaku=BahanBaku::where('id_bahan_baku', $val['id_bahan_baku'])->first();//ambil data bahan baku

           //persiapan susun data

         $total_nilai_sekarang=$databahanbaku->total_nilai_persediaan+($val['jumlah_masuk']*$val['harga']);
         
           $hargaupdate= round($total_nilai_sekarang/($databahanbaku->jumlah+$val['jumlah_masuk'])) ;
           $databahanbakuupdate[$i]=array(
            'id'=>$val['id_bahan_baku'],
            'harga_update'=>$hargaupdate,
            'jumlah_update'=> ($val['jumlah_masuk']+$databahanbaku->jumlah),
            'total_nilai_update'=>$total_nilai_sekarang
           );
           $i++;
        }
        
        
       
       $query= DB::beginTransaction();

        try {
           
            foreach ($databahanbakuupdate as $key) {
            DB::table('bahan_baku')->where('id_bahan_baku', $key['id'])->update(
                ['harga' => $key['harga_update'], 'jumlah'=>$key['jumlah_update'],
                'total_nilai_persediaan'=>$key['total_nilai_update']]);
            }
            MutasiBahanBakuModel::insert($datamutasi); 
            DB::commit();
            $query=true;
            // all good
        } catch (Exception $e) {
            DB::rollback();
            $query = false;
        }
         

                if ($query){        
                return ReturnMessage::successmessage();
                }
                else{
                    return ReturnMessage::errormessage();
                }
     
         }
        else{
            return ReturnMessage::errormessage();
        }
      


    }
  
}

