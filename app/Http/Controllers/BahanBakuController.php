<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use App\Helpers\ReturnMessage;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
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
        // $requests = json_decode($request->getContent());
        $validated= $this->validate($request,[
            'nama_bahan_baku'=>'required|max:255',
            'harga'=> 'required|max:255|regex:/^[1-9]+[0-9]*$/|not_in:0',
            'jumlah'=> 'required|regex:/^[0-9]*$/',
            'satuan'=>'required'
        ]);
        $idbahanbaku= DB::table('bahan_baku')
        ->max('id_bahan_baku');
        $total_nilai_persediaan=$validated['jumlah'] * $validated['harga'];
        $query= DB::table('bahan_baku')
        ->insert(array(
            'id_bahan_baku' => $idbahanbaku+1,
            'nama_bahan_baku' => $validated['nama_bahan_baku'],
            'harga' => $validated['harga'],
            'jumlah' => $validated['jumlah'],
            'total_nilai_persediaan' => $total_nilai_persediaan,
            "satuan" => $validated['satuan'],
           
        ));

        if ($query){
       
         return ReturnMessage::successmessage();
        //  return $idbahanbaku; 
        }
        else {
            return $query;
        }
    }

    else{
        return ReturnMessage::errormessage();
    }
    }



    public function UpdateBahanBaku(Request $request)
    {
        if (Auth::user()->akses=='superadmin')
        {
            
            $validated_data= $this->validate($request,[
                'id_bahan_baku'=>'numeric|required|exists:bahan_baku,id_bahan_baku',
                'nama_bahan_baku'=>'required|max:255',
                'harga'=> 'required|max:255|regex:/^[1-9]+[0-9]*$/|not_in:0',
                'jumlah'=> 'required|regex:/^[0-9]*$/',
                'satuan'=>'required'
            ]);
         
            $idbahanbaku=$validated_data['id_bahan_baku'];
            unset($validated_data['id_bahan_baku']);
            $update=$validated_data;
           
            try { 
            $m_bahanbaku= new BahanBaku();
            $updates=$m_bahanbaku->UpdateBahanBaku($update,$idbahanbaku);
            return ReturnMessage::successmessage();
                 
              } catch(\Illuminate\Database\QueryException $error){ 
             return array('status'=>false,'message'=>$error->getMessage());
              }
   
 
        }

    }

    public function GetBahanBaku(Request $request)
    {
        
            
            $validated= $this->validate($request,[
                'id_bahan_baku'=>'numeric|required|exists:bahan_baku,id_bahan_baku',
               
            ]);
         
       
            $bahanbaku= BahanBaku::where('id_bahan_baku',$validated['id_bahan_baku'])->first();
          
            return ReturnMessage::successmessage($bahanbaku);
              
         
   
 
        

    }
   
}
