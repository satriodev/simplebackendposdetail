<?php

namespace App\Helpers;

class ReturnMessage{

    public static function successmessage($message)
    {
        if($message==null||!$message){
        return response()->json(['status'=>true, 'message'=>'sukses'], 200);
        }
        else{
            return response()->json(['status'=>true, 'message'=>$message], 200);
        }
    }

    public static function errormessage()
    {
        return response()->json(['status'=>false, 'message'=>'error'], 500);
    }

}