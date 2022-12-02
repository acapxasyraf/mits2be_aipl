<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PatientShharpRegistrationHospitalManagement;
use App\Models\PatientRiskProtectiveAnswer;
use App\Models\SharpRegistrationSelfHarmResult;
use App\Models\SharpRegistrationFinalStep;
use App\Models\PatientRegistration;
use App\Models\Postcode;
use App\Models\State;
use App\Models\PatientShharpRegistrationRiskProtective;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RequestAppointmentReportExport;
use App\Models\PatientAppointmentDetails;
use App\Models\AppointmentRequest; 
use App\Models\StaffManagement; 


class RequestAppointmentReportController extends Controller
{
    //        $list = StaffManagement::select('id', 'team_id', 'branch_id')->where('email','=', $request->email)->get();
    // $list2 = StaffManagement::select('id', 'team_id', 'name')->where('branch_id','=', $list[0]['branch_id'])->where('team_id','=', $request->appointment_type)->get();
    // return response()->json(["message" => "Staff Name", 'list' => $list2, "code" => 200]);

    public function getRequestAppointmentReport(Request $request)
    {
 
        $response = AppointmentRequest::select('*')
        ->whereBetween('created_at', [$request->fromDate, $request->toDate])
            ->where('branch_id','=', $request->branch_id)
        ->where('status', '1');
        
        $ssh= $response->get()->toArray();
        $result = [];
        $index=0;
        foreach ($ssh as $k => $v) {
        $result[$index]['No']=$index+1;
        $result[$index]['name']=$v['name'];

        if($v['nric_or_passportno']==NULL){
            $result[$index]['nric_or_passportno']='NA';
        }else{
            $result[$index]['nric_or_passportno']=$v['nric_or_passportno'];
        }

     
        
        if($v['address']==NULL && $v['address1']==NULL){
            $result[$index]['address']='NA';
        }else if($v['address']==NULL||$v['address1']==NULL){  
            $result[$index]['address']=$v['address'].$v['address1'];
        }else{
            $result[$index]['address']=$v['address'].', '.$v['address1'];
        }

        $result[$index]['contact_no']=$v['contact_no'];
        $result[$index]['email']=$v['email'];
        $result[$index]['created_at']=$v['created_at'];
        $index++;
        }
        if ($response) {

            if (isset($request->report_type) && $request->report_type == 'excel') {
                $filename = 'RequestAppointmentReport-'.time().'.xls';
                return response([
                    'message' => 'Data successfully retrieved.',
                    'result' => $result,
                    'header' => 'Request Appointment Report from '.$request->fromDate.' To '.$request->toDate,
                    'filename' => $filename,
                    'code' => 200]);

                } else {
                    $filename = 'RequestAppointmentReport-'.time().'.pdf';
                return response()->json(["message" => "Request Report",'result' => $result,'filename' => $filename, "code" => 200]);

                }

        } else {

            return response()->json(["message" => "Request Report", 'result' => [], 'filepath' => null, "code" => 200]);
        }
    }

    
}
