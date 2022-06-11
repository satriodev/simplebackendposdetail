<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MutasiBahanBakuModel;
use App\Models\SetengahJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use App\Helpers\ReturnMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SetengahJadiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Auth::user()->akses!='superadmin')
        {
            return ReturnMessage::errormessage();
        }
    }

    public function tambahmastersetengahjadi(Request $request)
    {
       
        $requests = json_decode($request->getContent());
        $validated= $this->validate($request,[
            'nama_barang_setengah_jadi'=>'required|max:255',
            'satuan_setengahjadi'=>'required'
        ]);
        $idsetengahjadi= DB::table('setengahjadi')
        ->max('id_setengahjadi');
        
        $query= DB::table('setengahjadi')
        ->insert(array(
            'id_setengahjadi' => $idsetengahjadi+1,
            'nama_barang_setengah_jadi' => $validated['nama_barang_setengah_jadi'],
            "satuan_setengahjadi" => $validated['satuan_setengahjadi'],
           
        ));

        if ($query){
       
         return ReturnMessage::successmessage();
        //  return $idbahanbaku; 
        }
        else {
            return $query;
        }
    

 
    }
      


    




    public function komposisi_setengahjadi(Request $request)
    {
       
        $requests = json_decode($request->getContent());
        $validated=  $this->validate($request, [
            'data.*.id_setengahjadi'=>'required|exists:setengahjadi,id_setengahjadi|max:20',
            'data.*.id_bahan_baku'=>'required|exists:bahan_baku,id_bahan_baku|max:20',
            'data.*.jumlah_kebutuhan'=> 'numeric|regex:/^[1-9]*$/'
            
            
        ]);
      
        $query= DB::table('komposisi_setengahjadi')
        ->insert($validated['data']);
         

                if ($query){        
                return ReturnMessage::successmessage();
                }
                else{
                    return ReturnMessage::errormessage();
                }
    }
  







    
}

