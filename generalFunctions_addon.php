<?php
include_once 'brainatwork.php';


	function logMe($msg){
		$fp = fopen("log-brainatwork.txt", "a");
		$escreve = fwrite($fp, $msg);
		fclose($fp);
	}



/*

	if ($_REQUEST['tDeslocKm'])

    	brainLog(' GF ADDON  ', $_REQUEST); 



    	$sql = "SELECT iTripId FROM cab_request_now WHERE iCabRequestId = '" . $_REQUEST['iCabRequestId'] . "' LIMIT 1 ";

		$dataSql = $obj->MySQLSelect($sql);



		$dataWhere = " iTripId='" . $dataSql[0]['iTripId'] . "' ";

		$dataId = $obj->MySQLQueryPerform('trips', $dataUpdate, 'update', $dataWhere);

*/



	if ($_REQUEST['type']=='GenerateTrip' && $_REQUEST['iCabRequestId']>0 && $_REQUEST['tDeslocKm']) {

		$dataUpdate['tDeslocKm'] = $_REQUEST['tDeslocKm'];

        $dataWhere = " iCabRequestId='" . $_REQUEST['iCabRequestId'] . "' ";

        $dataId = $obj->MySQLQueryPerform('cab_request_now', $dataUpdate, 'update', $dataWhere);

	}

    

	/* to clean function */

	

	function clean($str) {

    global $obj;  

		$str = trim($str);

		//$str = mysqli_real_escape_string($str);

    	$str = $obj->SqlEscapeString($str);

		$str = htmlspecialchars($str);

		$str = strip_tags($str);

		return($str);

	}

	

	/* get vLangCode as per member or if member not found check lcode and then defualt take lang code set at $lang_label */

	

	function getLanguageCode($memberId = '', $lcode = '') {

		global $lang_label, $lang_code, $obj;

		/* find vLanguageCode using member id */

		if ($memberId != '') {

			

			$sql = "SELECT  `vLanguageCode` FROM  `member` WHERE iMemberId = '" . $memberId . "' AND `eStatus` = 'Active' ";

			$get_vLanguageCode = $obj->MySQLSelect($sql);

			

			if (count($get_vLanguageCode) > 0)

            $lcode = (isset($get_vLanguageCode[0]['vLanguageCode']) && $get_vLanguageCode[0]['vLanguageCode'] != '') ? $get_vLanguageCode[0]['vLanguageCode'] : '';

		}

		

		/* find default language of website set by admin */

		if ($lcode == '') {

			$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";

			$default_label = $obj->MySQLSelect($sql);

			

			$lcode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'PT';

		}

		

		$lang_code = $lcode;

		$sql = "SELECT  `vLabel` ,  `vValue`  FROM  `language_label`  WHERE  `vCode` = '" . $lcode . "' ";

		$all_label = $obj->MySQLSelect($sql);

		

		for ($i = 0; $i < count($all_label); $i++) {

			$vLabel = $all_label[$i]['vLabel'];

			$vValue = $all_label[$i]['vValue'];

			$lang_label[$vLabel] = $vValue;

		}

		//echo "<pre>"; print_R($lang_label); echo "</pre>";

	}

	

	#function to get value from table can be use for any table - create to get value from configuration

	#$check_phone = get_value('configurations', 'vValue', 'vName', 'PHONE_VERIFICATION_REQUIRED');

	

	function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {

		global $obj;

		$returnValue = array();

		

		$where = ($condition_field != '') ? ' WHERE ' . clean($condition_field) : '';

		$where .= ($where != '' && $condition_value != '') ? ' = "' . clean($condition_value) . '"' : '';

		

		if ($table != '' && $field_name != '' && $where != '') {

			$sql = "SELECT $field_name FROM  $table $where";

			if ($setParams != '') {

				$sql .= $setParams;

			}  

			$returnValue = $obj->MySQLSelect($sql);

			} else if ($table != '' && $field_name != '') {

			$sql = "SELECT $field_name FROM  $table";

			if ($setParams != '') {

				$sql .= $setParams;

			}  

      $returnValue = $obj->MySQLSelect($sql);

		}

		if ($directValue == '') {

			return $returnValue;

			} else {

			$temp = $returnValue[0][$field_name];

			return $temp;

		}

	}

	

	function get_client_ip() {

		$ipaddress = '';

		if (isset($_SERVER['HTTP_CLIENT_IP']))

        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];

		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))

        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

		else if (isset($_SERVER['HTTP_X_FORWARDED']))

        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];

		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))

        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];

		else if (isset($_SERVER['HTTP_FORWARDED']))

        $ipaddress = $_SERVER['HTTP_FORWARDED'];

		else if (isset($_SERVER['REMOTE_ADDR']))

        $ipaddress = $_SERVER['REMOTE_ADDR'];

		else

        $ipaddress = 'UNKNOWN';

		return $ipaddress;

	}

	

	function createUserLog($userType, $eAutoLogin, $iMemberId, $deviceType) {

		global $generalobj, $obj;

		

		if (SITE_TYPE != "Demo") {

			return "";

		}  

		$data['iMemberId'] = $iMemberId;

		$data['eMemberType'] = $userType;

		$data['eMemberLoginType'] = "AppLogin";

		$data['eDeviceType'] = $deviceType;

		$data['eAutoLogin'] = $eAutoLogin;

		$data['vIP'] = get_client_ip();

		

		$id = $obj->MySQLQueryPerform("member_log", $data, 'insert');

	}

	

	function dateDifference($date_1, $date_2, $differenceFormat = '%a') {

		$datetime1 = date_create($date_1);

		$datetime2 = date_create($date_2);

		

		$interval = date_diff($datetime1, $datetime2);

		

		return $interval->format($differenceFormat);

	}

	

	function getVehicleTypes($cityName = "") {

		global $obj;

		$sql_vehicle_type = "SELECT * FROM vehicle_type";

		

		$row_result_vehivle_type = $obj->MySQLSelect($sql_vehicle_type);

		return $row_result_vehivle_type;

	}

	

	function paymentimg($paymentm) {

		global $tconfig;

		if ($paymentm == "Card") {

			// return "webimages/icons/payment_images/ic_payment_type_card.png";

			return $tconfig["tsite_url"] . "webimages/icons/payment_images/ic_payment_type_card.png";

			} else {

			// return "webimages/icons/payment_images/ic_payment_type_cash.png";

			return $tconfig["tsite_url"] . "webimages/icons/payment_images/ic_payment_type_cash.png";

		}

	}

	

	function ratingmark($ratingval) {

		global $tconfig;

		$a = $ratingval;

		$b = explode('.', $a);

		$c = $b[0];

		

		$str = "";

		$count = 0;

		for ($i = 0; $i < 5; $i++) {

			if ($c > $i) {

				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" width="20px;" align="left" >';

				} elseif ($a > $c && $count == 0) {

				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-Half-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" width="20px;" align="left" >';

				$count = 1;

				} else {

				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-blank.png" style="outline:none;text-decoration:none;width:20px;border:none" width="20px;" align="left" >';

			}

		}

		return $str;

	}

	

	function getTripFare($Fare_data, $surgePrice) {

		global $generalobj, $obj;       

		//$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");

    $iVehicleTypeId = get_value('trips', 'iVehicleTypeId', 'iTripId', $Fare_data[0]['iTripId'], '', 'true');

    $iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');

    $iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');

    if($iParentId == 0){

    $ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  

    }else{ 

    $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 

    }

    $eHailTrip=$Fare_data[0]['eHailTrip'];

    $eFlatTrip=$Fare_data[0]['eFlatTrip'];

    $fFlatTripPrice=$Fare_data[0]['fFlatTripPrice'];

    $waitingTime = $Fare_data[0]['waitingTime'];

    $iWaitingFeeTimeLimit = $Fare_data[0]['iWaitingFeeTimeLimit'];

    $fWaitingFees = $Fare_data[0]['fWaitingFees'] * $waitingTime;

    $fWaitingFees= round($fWaitingFees,2);

    if($waitingTime < $iWaitingFeeTimeLimit){

      $fWaitingFees = 0;

    }    

    if($eFlatTrip == "Yes"){

       $Fare_data[0]['iBaseFare'] = $fFlatTripPrice;

       $Fare_data[0]['fPricePerMin'] = 0;

       $Fare_data[0]['fPricePerKM'] = 0;

    }

    if($eHailTrip == "Yes"){

      $fWaitingFees = 0;

    }

      

    //$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  

    $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";

		

		$fAmount = 0;

		if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {

			$iDriverVehicleId = get_value('trips', 'iDriverVehicleId', 'iTripId', $Fare_data[0]['iTripId'], '', 'true');

			

			

			$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";

			$serviceProData = $obj->MySQLSelect($sqlServicePro);

			

			if (count($serviceProData) > 0) {

				$fAmount = $serviceProData[0]['fAmount'];

			}

		}     

		if ($surgePrice >= 1) {

			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;

			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;

			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;

			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;

		}

		

		if ($Fare_data[0]['eFareType'] == 'Fixed') {

			$Fare_data[0]['fPricePerMin'] = 0;

			$Fare_data[0]['fPricePerKM'] = 0;

			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes" && $fAmount != 0) {

				$Fare_data[0]['iBaseFare'] = $fAmount * $Fare_data[0]['iQty'];

				} else {

				$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $Fare_data[0]['iQty'];

			}

			} else if ($Fare_data[0]['eFareType'] == 'Hourly') {

			$Fare_data[0]['iBaseFare'] = 0;

			$Fare_data[0]['fPricePerKM'] = 0;

			

			$totalHour = $Fare_data[0]['TripTimeMinutes'] / 60;

			$Fare_data[0]['TripTimeMinutes'] = $totalHour;

			

			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes" && $fAmount != 0) {

				$Fare_data[0]['fPricePerMin'] = $fAmount;

				} else {

				$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerHour'];

			}

		}

		

		/*Add For Rental*/

		if($Fare_data[0]['iRentalPackageId'] > 0) {

			$query = "SELECT `iRentalPackageId`, `iVehicleTypeId`, `vPackageName_EN`, `fPrice`, `fKiloMeter`, `fHour`, `fPricePerKM`, `fPricePerHour` FROM `rental_package` WHERE iRentalPackageId = ".$Fare_data[0]['iRentalPackageId'];

			$data_trip_pkg = $obj->MySQLSelect($query);

			$iBaseFare = round($data_trip_pkg[0]['fPrice'], 2);

			$RentalTripTimeMinutes = $Fare_data[0]['TripTimeMinutes'];

			$RentalOrgMinutes = $data_trip_pkg[0]['fHour']* 60;

			if($RentalTripTimeMinutes > $RentalOrgMinutes) {

				$extra_min = $RentalTripTimeMinutes-$RentalOrgMinutes;

				$Minute_Fare = round($data_trip_pkg[0]['fPricePerHour'] * $extra_min, 2);

			} else {

				$Minute_Fare = 0;

			}

			$TripKilometer = getVehicleCountryUnit($iVehicleTypeId,$data_trip_pkg[0]['fKiloMeter']);

			$rPricePerKM = getVehicleCountryUnit_PricePerKm($iVehicleTypeId,$data_trip_pkg[0]['fPricePerKM']);

			if($Fare_data[0]['TripDistance'] > $TripKilometer) {

				$extradistance = $Fare_data[0]['TripDistance']-$TripKilometer;

				$Distance_Fare = round($rPricePerKM * $extradistance, 2);

			} else {

				$Distance_Fare = 0;

			}

		} else {

		$Minute_Fare = round($Fare_data[0]['fPricePerMin'] * $Fare_data[0]['TripTimeMinutes'], 2);

		$Distance_Fare = round($Fare_data[0]['fPricePerKM'] * $Fare_data[0]['TripDistance'], 2);

		$iBaseFare = round($Fare_data[0]['iBaseFare'], 2);

		}

		/*End Add For Rental*/

		$fMaterialFee = round($Fare_data[0]['fMaterialFee'], 2);

		$fMiscFee = round($Fare_data[0]['fMiscFee'],2);

		$fDriverDiscount = round($Fare_data[0]['fDriverDiscount'],2);

		$fVisitFee= round($Fare_data[0]['fVisitFee'],2);

		//  print_r($Fare_data); 

		$total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee ) - $fDriverDiscount;

    		

		$total_fare_for_commission_ufx = $iBaseFare + $Minute_Fare + $Distance_Fare ;

		$Commision_Fare = round((($total_fare_for_commission_ufx * $Fare_data[0]['fCommision']) / 100), 2);

		

		$result['FareOfMinutes'] = $Minute_Fare;

		$result['FareOfDistance'] = $Distance_Fare;

		$result['FareOfCommision'] = $Commision_Fare;

		// $result['iBaseFare'] = $iBaseFare;

		$result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];

		$result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];

		$result['fCommision'] = $Fare_data[0]['fCommision'];

		$result['FinalFare'] = $total_fare;

		$result['FinalFare_UFX_Commission'] = $total_fare_for_commission_ufx;

		$result['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;

		$result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];

		$result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];

		$result['iMinFare'] = $Fare_data[0]['iMinFare'];

    $result['fMaterialFee'] = $fMaterialFee;

    $result['fMiscFee'] = $fMiscFee;

    $result['fVisitFee'] = $fVisitFee;

    $result['fWaitingFees'] = $fWaitingFees;

    

		

		return $result;

	}

	                                        

	function calculateFare($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $couponCode = "", $tripId, $fMaterialFee =0, $fMiscFee =0, $fDriverDiscount =0, $waitingTime = 0) {

		global $generalobj, $obj;

		$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);

		

		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);

		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');

		/*changes for rental*/

    $sql="select fPickUpPrice,fNightPrice,iQty,eFareType,eFlatTrip,fFlatTripPrice,fVisitFee,fTollPrice,eTollSkipped,fWaitingFees,eHailTrip,iRentalPackageId from trips where iTripId='".$tripId."'";

		$data_trips = $obj->MySQLSelect($sql);

		/*added for rental*/

		$iRentalPackageId = $data_trips[0]['iRentalPackageId'];

		/*added for rental*/

    $fPickUpPrice = $data_trips[0]['fPickUpPrice'];

    $fNightPrice = $data_trips[0]['fNightPrice'];

    $iQty = $data_trips[0]['iQty'];

    $eFareType = $data_trips[0]['eFareType'];

    $eFlatTrip = $data_trips[0]['eFlatTrip'];

    $fFlatTripPrice = $data_trips[0]['fFlatTripPrice'];

    $iWaitingFeeTimeLimit = $Fare_data[0]['iWaitingFeeTimeLimit'];

    $fWaitingFees = $Fare_data[0]['fWaitingFees'];

    $eHailTrip = $data_trips[0]['eHailTrip'];

    if($eHailTrip == "Yes"){

       $fWaitingFees = 0;

    }

    /*if($eFlatTrip == "No"){

		$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);

    }else{

    $surgePrice = 1;

    }*/ 

    $surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);

    $fVisitFee = $data_trips[0]['fVisitFee'];

		$tripTimeInMinutes = ($totalTimeInMinutes_trip != '') ? $totalTimeInMinutes_trip : 0;

		$fPricePerKM = getVehicleCountryUnit_PricePerKm($vehicleTypeID,$Fare_data[0]['fPricePerKM']);

    $fTollPrice = $data_trips[0]['fTollPrice'];

    $eTollSkipped = $data_trips[0]['eTollSkipped'];

    $TaxArr = getMemberCountryTax($iUserId,"Passenger");

    $fTax1 = $TaxArr['fTax1'];  

		$fTax2 = $TaxArr['fTax2']; 

		

		if($eTollSkipped == "Yes"){

			$fTollPrice = 0;

		}

		

		$Fare_data[0]['TripTimeMinutes'] = $tripTimeInMinutes;

		$Fare_data[0]['TripDistance'] = $tripDistance;

    $Fare_data[0]['eFlatTrip'] = $eFlatTrip;

    $Fare_data[0]['fFlatTripPrice'] = $fFlatTripPrice;

		$Fare_data[0]['iTripId'] = $tripId;

		$Fare_data[0]['eFareType'] = $eFareType;

		$Fare_data[0]['iQty'] = $iQty;

		$Fare_data[0]['fVisitFee'] =$fVisitFee;

		$Fare_data[0]['fMaterialFee']=$fMaterialFee;

		$Fare_data[0]['fMiscFee']=$fMiscFee;

		$Fare_data[0]['fDriverDiscount'] =$fDriverDiscount;

		$Fare_data[0]['fPricePerKM'] =$fPricePerKM;

    $Fare_data[0]['waitingTime'] =$waitingTime;

    $Fare_data[0]['iWaitingFeeTimeLimit'] =$iWaitingFeeTimeLimit;

    $Fare_data[0]['fWaitingFees'] =$fWaitingFees;

    $Fare_data[0]['eHailTrip'] =$eHailTrip; 

		

	    //added for rental

	    $Fare_data[0]['iRentalPackageId'] = $iRentalPackageId;

		$result = getTripFare($Fare_data, "1");

		//$resultArr_Orig = getTripFare($Fare_data,"1");

		

		

		$total_fare = $result['FinalFare'];

		$fTripGenerateFare = $result['FinalFare'];

		//$fTripGenerateFare_For_Commission = $result['FinalFare'];

		$fTripGenerateFare_For_Commission = $result['FinalFare_UFX_Commission'];

		

		//$fSurgePriceDiff = round(($fTripGenerateFare * $surgePrice) - $fTripGenerateFare, 2);

    $fSurgePriceDiff = round(($fTripGenerateFare_For_Commission * $surgePrice) - $fTripGenerateFare_For_Commission, 2);

		$total_fare = $total_fare + $fSurgePriceDiff;

		$fTripGenerateFare = $fTripGenerateFare + $fSurgePriceDiff;

		

    /* Waiting Fee  Calculation */

    //$fWaitingFees = $result['fWaitingFees'];

    $fWaitingFees = getTripWaitingFee($tripId);

    if($fWaitingFees > 0){

      $total_fare = $total_fare+$fWaitingFees;

			$fTripGenerateFare = $fTripGenerateFare+$fWaitingFees;

      $fTripGenerateFare_For_Commission = $fTripGenerateFare_For_Commission+$fWaitingFees;

    }  

    /* Waiting Fee  Calculation */

		$iMinFare = $result['iMinFare'];

		

		// make changes for rental

	    if($eFlatTrip == "No" && $iRentalPackageId == 0){

		if ($iMinFare > $fTripGenerateFare) {

			$MinFareDiff = $iMinFare - $total_fare;

			$total_fare = $iMinFare;

			$fTripGenerateFare = $iMinFare;

			$fTripGenerateFare_For_Commission = $iMinFare;

			} else {

			$MinFareDiff = "0";

      $fTripGenerateFare_For_Commission = $fTripGenerateFare_For_Commission+$fSurgePriceDiff;

  		}

		}else{

      $fTripGenerateFare_For_Commission = $fTripGenerateFare_For_Commission+$fSurgePriceDiff;

		}

		

		/* Toll Calculation */

		if($fTollPrice > 0){

			$total_fare = $total_fare+$fTollPrice;

			$fTripGenerateFare = $fTripGenerateFare+$fTollPrice;

		}

    /* Toll Calculation */


    // Brainatwork - Food Temporario

        $itemSql="SELECT iUserId, iAdminUserId, fFarePricePreview, rota_Latitude_1, rota_Latitude_2, rota_Latitude_3, eAmountReturn FROM cab_booking WHERE iTripId=".$tripId;
        $item=$obj->MySQLSelect($itemSql);


        $log =  "
        Trip: {$tripId}    
		iAdminUserId  {$item[0]['iAdminUserId']}
		-------------------------
		itemSql: {$itemSql}  		
        ";

		// brainLog(' Brainatwork - FOOD INICIO ', $mensagem);


    if ($item[0]['iAdminUserId']>0) {    


 		// Verificar Saldo    
		$itemSql2= "SELECT iAdminId, iGroupId, fAmountReturn, fAmountRoute, vFirstName, vLastName, vEmail, vContactNo, vPassword, iUserId, eStatus, eDefault FROM administrators WHERE iAdminId=".$item[0]['iAdminUserId'];
		$item2=$obj->MySQLSelect($itemSql2);
		$adminUser = $item2[0];


		// Aplica taxa de retorno
		if($item[0]['eAmountReturn'] == 'Yes'){
			if ($adminUser['fAmountReturn']>0) {
				$total_fare = $total_fare+$adminUser['fAmountReturn'];
				$fTripGenerateFare = $fTripGenerateFare+$adminUser['fAmountReturn'];
			}
		}
		

		// Aplica taxa de Rotas
		if ($adminUser['fAmountRoute']>0) {
			if($item[0]['rota_Latitude_1'] != ''){	
				 $total_fare = $total_fare+$adminUser['fAmountRoute'];
				 $fTripGenerateFare = $fTripGenerateFare+$adminUser['fAmountRoute'];
			}
			if($item[0]['rota_Latitude_2'] != ''){	
				 $total_fare = $total_fare+$adminUser['fAmountRoute'];
				 $fTripGenerateFare = $fTripGenerateFare+$adminUser['fAmountRoute'];
			}
			if($item[0]['rota_Latitude_3'] != ''){	
				 $total_fare = $total_fare+$adminUser['fAmountRoute'];
				 $fTripGenerateFare = $fTripGenerateFare+$adminUser['fAmountRoute'];
			}
		}

		// Verifica se existe configuração previa na Empresa

		require_once($_SERVER['DOCUMENT_ROOT'] . "/app/Brainatwork.php");
		Brainatwork::run();

		$add = '';
		$admin = new AdminModel();
		$itemExtras = $admin->getExtras($item[0]['iAdminUserId']);

		if ($itemExtras['tExtras'])
			$itemExtras = json_decode($itemExtras['tExtras'], true);
// Custo maximo da empresa para o valor da entrega fixa
		if (strlen($itemExtras['custo_maximo'])>0) {
			$custoMaximoLimite = $itemExtras['custo_maximo'];

			$log .= ' CUSTO PERSONALIZADO: ' . $custoMaximoLimite;
			$add .= '-PERSONALIZADO';

		} else {
			$oneSql = "SELECT vValue FROM configurations WHERE iSettingId = '212' LIMIT 1";
			$oneVle = $obj->MySQLSelect($oneSql);
			$custoMaximoLimite = $oneVle[0]['vValue'];

			$log .= ' CUSTO GLOBAL: ' . $custoMaximoLimite;
		}

		/* 	
		$oneSql = "SELECT vValue FROM configurations WHERE iSettingId = '212' LIMIT 1";
		$oneVle = $obj->MySQLSelect($oneSql);
		$custoMaximoLimite = $oneVle[0]['vValue'];
	     */

		$custoMaximo = number_format($item[0]['fFarePricePreview'] + ($item[0]['fFarePricePreview']*$custoMaximoLimite), 2);
		

	$log .=  "
		-------------------------
        Admin User ID: {$item[0]['iAdminUserId']} //adiciona o usuario administrador
        Taxa Global: {$oneVle[0]['vValue']}  
        Taxa Personalizada: {$itemExtras['custo_maximo']}  
        Custo Previsto: {$item[0]['fFarePricePreview']}  
        Custo Viagem: {$fTripGenerateFare}  
        Custo Maximo: {$custoMaximo}  
		-------------------------
        ";
 /* 	*/
		$add = 'n';
		if ($fTripGenerateFare>$custoMaximo && $item[0]['iAdminUserId']>0) {
			$fTripGenerateFare = $custoMaximo;
			$total_fare  = $custoMaximo;
			$log .= ' --------- !!!!!!!!!!  CUSTO MAXIMO APLICADO!!!!  --------- ';
			$add .= '-APLICADO';
		}	

		// Se valor da entrega for menor que o custo previsto, paga o valor minimo previsto
		if ($fTripGenerateFare<$item[0]['fFarePricePreview'] && $item[0]['iAdminUserId']>0) {
			$fTripGenerateFare = $item[0]['fFarePricePreview'];
			$total_fare  = $fTripGenerateFare;
			$log .= ' --------- CUSTO MINIMO APLICADO  --------- ';
			$add .= '-MINIMO';
		}

         brainLog(' custoMaximo  ', $log, 'cm-'.$tripId.'-'.$add.'-'); 	


	    // brainLog(' Brainatwork - Entrou - adminUser ', $adminUser);
	    $available_balance = $generalobj->get_user_available_balance($adminUser['iUserId'], "Rider");
	    // brainLog(' Brainatwork - Balanco ', $available_balance);

	    if ($available_balance>0 && $adminUser['iUserId']>0) {
			
	        if ($available_balance>=$fTripGenerateFare) {
	            $available_balance = $fTripGenerateFare;
	        }
	       
	        // brainLog(' Brainatwork - Credito / Debito ', $available_balance);

	        // Creditar
	        $iTripId = $tripId;
	        $tDescription = ' Credito Temporario de Entrega';
	        $eType = 'Credit';
	        $ePaymentStatus = 'Settelled';
	        $dDate = Date('Y-m-d H:i:s');
	        $eFor = 'Booking';
	        $eUserType = 'Rider';

//echo "test: ".$available_balance;
		//print_r($Fare_data);
		
	        $generalobj->InsertIntoUserWallet($item[0]['iUserId'], $eUserType, $available_balance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);

	        // Debitar
	        $iTripId = $tripId;
	        $tDescription = ' Solicitação de Entregador #' . $tripId;
	        $eType = 'Debit';
	        $ePaymentStatus = 'Settelled';
	        $dDate = Date('Y-m-d H:i:s');
	        $eFor = 'Booking';
	        $eUserType = 'Rider';
	        $generalobj->InsertIntoUserWallet($adminUser['iUserId'], $eUserType, $available_balance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);


	    }
	}


      

    /* Checking For Passenger Outstanding Amount */

    $fOutStandingAmount = 0;

    $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId);

    if($fOutStandingAmount > 0){

      $total_fare = $total_fare+$fOutStandingAmount;

			$fTripGenerateFare = $fTripGenerateFare+$fOutStandingAmount; 

    }

    /* Checking For Passenger Outstanding Amount */

		

		//$result['fCommision'] = round((($fTripGenerateFare * $Fare_data[0]['fCommision']) / 100), 2);

		//$fTripGenerateFare_For_Commission = $fTripGenerateFare_For_Commission+$fSurgePriceDiff;

		$result['fCommision'] = round((($fTripGenerateFare_For_Commission * $Fare_data[0]['fCommision']) / 100), 2);

		/* Check Coupon Code For Count Total Fare Start */

		$discountValue = 0;

		$discountValueType = "cash";

		if ($couponCode != '') {

			$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');

			$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true');

		}

		if ($couponCode != '' && $discountValue != 0) {

			if ($discountValueType == "percentage") {

				$vDiscount = round($discountValue, 1) . ' ' . "%";

				$discountValue = round(($total_fare * $discountValue), 1) / 100;

				} else {

				$curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');

				if ($discountValue > $total_fare) {

					$vDiscount = round($total_fare, 1) . ' ' . $curr_sym;

					} else {

					$vDiscount = round($discountValue, 1) . ' ' . $curr_sym;

				}

			}

			$fare = $total_fare - $discountValue;

			if ($fare < 0) {

				$fare = 0;

				$discountValue = $total_fare;

			}

			$total_fare = $fare;

			$Fare_data[0]['fDiscount'] = $discountValue;

			$Fare_data[0]['vDiscount'] = $vDiscount;

		}

		/* Check Coupon Code Total Fare  End */

    /* Tax Calculation */

    $result['fTax1'] = 0;

    $result['fTax2'] = 0;

    if($fTax1 > 0){

       $fTaxAmount1 = round(((($fTripGenerateFare-$discountValue) * $fTax1) / 100 ),2);

       $fTripGenerateFare = $fTripGenerateFare + $fTaxAmount1;

       $total_fare = $total_fare+$fTaxAmount1;

       $result['fTax1'] = $fTaxAmount1;  

    }

    if($fTax2 > 0){

       $total_fare_new = $fTripGenerateFare-$discountValue-$fTaxAmount1;

       $fTaxAmount2 = round((($total_fare_new * $fTax2) / 100 ),2);

       $fTripGenerateFare = $fTripGenerateFare + $fTaxAmount2;

       $total_fare = $total_fare+$fTaxAmount2;

       $result['fTax2'] = $fTaxAmount2;  

    }

    /* Tax Calculation */

		

		/* Check debit wallet For Count Total Fare  Start */

		$user_wallet_debit_amount = 0;

    $eWalletAdjustment = get_value('register_user', 'eWalletAdjustment', 'iUserId', $iUserId, '', 'true');

    if($eWalletAdjustment == "Yes"){

    	$user_available_balance = $generalobj->get_user_available_balance($iUserId, "Rider");

		if ($total_fare > $user_available_balance) {

			$total_fare = $total_fare - $user_available_balance;

			$user_wallet_debit_amount = $user_available_balance;

			} else {

			$user_wallet_debit_amount = $total_fare;

			$total_fare = 0;

		}

		

		// Update User Wallet

		if ($user_wallet_debit_amount > 0) {

			$vRideNo = get_value('trips', 'vRideNo', 'iTripId', $tripId, '', 'true');

			$data_wallet['iUserId'] = $iUserId;

			$data_wallet['eUserType'] = "Rider";

			$data_wallet['iBalance'] = $user_wallet_debit_amount;

			$data_wallet['eType'] = "Debit";

			$data_wallet['dDate'] = date("Y-m-d H:i:s");

			$data_wallet['iTripId'] = $tripId;

			$data_wallet['eFor'] = "Booking";

			$data_wallet['ePaymentStatus'] = "Unsettelled";

			$data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING#" . $vRideNo;

			

			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);

			//$obj->MySQLQueryPerform("user_wallet",$data_wallet,'insert');

  		}

		}

		/* Check debit wallet For Count Total Fare  End */

		

		if ($Fare_data[0]['eFareType'] == 'Fixed') {

			$Fare_data[0]['iBaseFare'] = 0;

			} else {

			$Fare_data[0]['iBaseFare'] = $result['iBaseFare'];

		}

		

    $total_fare = round($total_fare,2);

		$finalFareData['total_fare'] = $total_fare;

		$finalFareData['iBaseFare'] = $result['iBaseFare'];

		$finalFareData['fPricePerMin'] = $result['FareOfMinutes'];

		$finalFareData['fPricePerKM'] = $result['FareOfDistance'];

		//$finalFareData['fCommision'] = $result['FareOfCommision'];

		//$finalFareData['fCommision'] = round((($fTripGenerateFare*$result['fCommision'])/100),2);

		$finalFareData['fCommision'] = $result['fCommision'];

		$finalFareData['fDiscount'] = $Fare_data[0]['fDiscount'];

		$finalFareData['vDiscount'] = $Fare_data[0]['vDiscount'];

		$finalFareData['MinFareDiff'] = $MinFareDiff;

		$finalFareData['fSurgePriceDiff'] = $fSurgePriceDiff;

		$finalFareData['user_wallet_debit_amount'] = $user_wallet_debit_amount;

		$finalFareData['fTripGenerateFare'] = $fTripGenerateFare;

		$finalFareData['SurgePriceFactor'] = $surgePrice;

    $finalFareData['fTax1'] = $result['fTax1'];

    $finalFareData['fTax2'] = $result['fTax2'];

    $finalFareData['fTax1Percentage'] = $TaxArr['fTax1'];  

		$finalFareData['fTax2Percentage'] = $TaxArr['fTax2'];

    $finalFareData['fWaitingFees'] = $fWaitingFees;

    $finalFareData['fOutStandingAmount'] = $fOutStandingAmount;

		return $finalFareData;

	}

	

	function calculateFareEstimate($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $surgePrice = 1) {

		global $generalobj, $obj;

		$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);

		

		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);

		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');

		

		if ($surgePrice > 1) {

			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;

			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;

			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;

			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;

		}

		

		if ($Fare_data[0]['eFareType'] == 'Fixed') {

			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'];

			$Fare_data[0]['fPricePerMin'] = 0;

			$Fare_data[0]['fPricePerKM'] = 0;

		}

		

		$resultArr = $generalobj->getFinalFare($Fare_data[0]['iBaseFare'], $Fare_data[0]['fPricePerMin'], $totalTimeInMinutes_trip, $Fare_data[0]['fPricePerKM'], $tripDistance, $Fare_data[0]['fCommision'], $priceRatio, $defaultCurrency, $startDate, $endDate);

		

		$resultArr['FinalFare'] = $resultArr['FinalFare'] - $resultArr['FareOfCommision']; // Temporary set: Remove addition of commision from above function

		

		$Fare_data[0]['total_fare'] = $resultArr['FinalFare'];

		

		if ($Fare_data[0]['iMinFare'] > $Fare_data[0]['total_fare']) {

			$Fare_data[0]['MinFareDiff'] = $Fare_data[0]['iMinFare'] - $Fare_data[0]['total_fare'];

			$Fare_data[0]['total_fare'] = $Fare_data[0]['iMinFare'];

			} else {

			$Fare_data[0]['MinFareDiff'] = "0";

		}

		

		if ($Fare_data[0]['eFareType'] == 'Fixed') {

			$Fare_data[0]['iBaseFare'] = 0;

			} else {

			$Fare_data[0]['iBaseFare'] = $resultArr['iBaseFare'];

		}

		$Fare_data[0]['fPricePerMin'] = $resultArr['FareOfMinutes'];

		$Fare_data[0]['fPricePerKM'] = $resultArr['FareOfDistance'];

		$Fare_data[0]['fCommision'] = $resultArr['FareOfCommision'];

		return $Fare_data;

	}

	

	function calculateFareEstimateAll($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $couponCode = "", $surgePrice = 1, $fMaterialFee =0, $fMiscFee =0, $fDriverDiscount =0, $DisplySingleVehicleFare = "",$eUserType = "Passenger", $iQty = 1,$SelectedCarTypeID = "",$isDestinationAdded = "Yes",$eFlatTrip = "No", $fFlatTripPrice = 0, $sourceLocationArr,$destinationLocationArr) {

		//                                          1                   2               3            4           5           6                7               8                 9                   10             11                12                  13                             14                   15              16                   17                      18                  19                     20                     21

		global $generalobj, $obj,$tconfig,$APPLY_SURGE_ON_FLAT_FARE;

		

		if ($eUserType == "Passenger") {

			$vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId,'','true');

			$userlangcode = get_value("register_user", "vLang", "iUserId", $iUserId, '', 'true');

			$eUnit = getMemberCountryUnit($iUserId,"Passenger");

      $TaxArr = getMemberCountryTax($iUserId,"Passenger");

		}else{

			$vCurrencyPassenger=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iUserId,'','true');

			$userlangcode = get_value("register_driver", "vLang", "iDriverId", $iUserId, '', 'true');

			$eUnit = getMemberCountryUnit($iUserId,"Driver");

      $TaxArr = getMemberCountryTax($iUserId,"Driver");

		}

		

		if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {

			$vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');

		}

		$priceRatio=get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger,'','true');

		$vSymbol=get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger,'','true');

		

		

		

		if ($userlangcode == "" || $userlangcode == NULL) {

			$userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

		}

		//$eUnit = getMemberCountryUnit($iUserId,"Passenger");

		$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");

		

		if($DisplySingleVehicleFare == ""){

      $ssql = "";

      if($SelectedCarTypeID != ""){

         $ssql .= " AND iVehicleTypeId IN ($SelectedCarTypeID) ";

      }

			$sql_vehicle_type = "SELECT * FROM vehicle_type WHERE 1 ".$ssql;

			$Fare_data = $obj->MySQLSelect($sql_vehicle_type);

			$result = array();

			for($i=0;$i<count($Fare_data);$i++){

				$fPickUpPrice = 1;

				$fNightPrice = 1;

				

				$data_surgePrice = checkSurgePrice($Fare_data[$i]['iVehicleTypeId'],"");

				

				if($data_surgePrice['Action'] == "0"){

					if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){

						$fPickUpPrice=$data_surgePrice['SurgePriceValue'];

						}else{

						$fNightPrice=$data_surgePrice['SurgePriceValue'];

					}

				}

        

        $Fare_data[$i]['TripTimeMinutes'] = $totalTimeInMinutes_trip;

				$Fare_data[$i]['TripDistance'] = $tripDistance;

				//$result = getTripFare($Fare_data[$i], $surgePrice);

				/** calculate fare **/

				$Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['iBaseFare'] ;

				$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerMin'];

				$Fare_data[$i]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Fare_data[$i]['iVehicleTypeId'],$Fare_data[$i]['fPricePerKM']);

				$Fare_data[$i]['fPricePerKM'] = $Fare_data[$i]['fPricePerKM'];

				$Fare_data[$i]['iMinFare'] = $Fare_data[$i]['iMinFare'];

				$iBaseFare = $Fare_data[$i]['iBaseFare'];

				$fPricePerKM = $Fare_data[$i]['fPricePerKM'];

				$fPricePerMin = $Fare_data[$i]['fPricePerMin'];

				

				if ($Fare_data[$i]['eFareType'] == 'Fixed') {

					$Fare_data[$i]['fPricePerMin'] = 0;

					$Fare_data[$i]['fPricePerKM'] = 0;

					//$Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['fFixedFare'] * $Fare_data[$i]['iQty'];

					$Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['fFixedFare'] * $iQty;

					} else if ($Fare_data[$i]['eFareType'] == 'Hourly') {

					$Fare_data[$i]['iBaseFare'] = 0;

					$Fare_data[$i]['fPricePerKM'] = 0;

					

					$totalHour = $Fare_data[$i]['TripTimeMinutes'] / 60;

					$Fare_data[$i]['TripTimeMinutes'] = $totalHour;

					$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerHour'];

				}

				

				$Minute_Fare = round(($fPricePerMin * $totalTimeInMinutes_trip) * $priceRatio, 2);

				$Distance_Fare = round(($fPricePerKM * $tripDistance) * $priceRatio, 2);

				$iBaseFare = round($iBaseFare * $priceRatio, 2);

				$fMaterialFee = round($Fare_data[$i]['fMaterialFee'] * $priceRatio, 2);

				$fMiscFee = round($Fare_data[$i]['fMiscFee'] * $priceRatio,2);

				$fDriverDiscount = round($Fare_data[$i]['fDriverDiscount'] * $priceRatio,2);

				$fVisitFee= round($Fare_data[$i]['fVisitFee'] * $priceRatio,2);

        

        if($isDestinationAdded == "Yes"){

          $data_flattrip = checkFlatTripnew($sourceLocationArr,$destinationLocationArr,$Fare_data[$i]['iVehicleTypeId']);

          $eFlatTrip = $data_flattrip['eFlatTrip']; 

          $fFlatTripPrice = $data_flattrip['Flatfare'];

        }else{

          $eFlatTrip = "No"; 

          $fFlatTripPrice = 0;

        }

        $Fare_data[$i]['eFlatTrip'] = $eFlatTrip;

        $Fare_data[$i]['fFlatTripPrice'] = $fFlatTripPrice;

        

        if($APPLY_SURGE_ON_FLAT_FARE == "No" && $eFlatTrip == "Yes"){

           $fPickUpPrice = 1;

				   $fNightPrice = 1;

        }

				$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);

				

        if($eFlatTrip == "No"){

          $total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;

  				$fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);

  				$SurgePriceFactor = strval($surgePrice);

  				$total_fare = $total_fare+$fSurgePriceDiff;	

  				$minimamfare = round($Fare_data[$i]['iMinFare'] * $priceRatio, 2);

  				if($minimamfare > $total_fare){

  					$fMinFareDiff = $minimamfare - $total_fare;

  					$total_fare = $minimamfare;

  					$Fare_data[$i]['FinalFare'] = $total_fare;

  					}else{

  					$fMinFareDiff = 0;

  				}

        }else{

           $total_fare = round($fFlatTripPrice * $priceRatio,2);

           $fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);

  				 $SurgePriceFactor = strval($surgePrice);

  				 $total_fare = $total_fare+$fSurgePriceDiff;

           $Fare_data[$i]['FinalFare'] = $total_fare;

           $fMinFareDiff = 0;	

        }  

        

				$Commision_Fare = round((($total_fare * $Fare_data[$i]['fCommision']) / 100), 2);

        

				

				$discountValue = 0;

				$discountValueType = "cash"; 

				if($couponCode != ""){

					$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');

          $discountValue_orig = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');

					$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true');

					if($discountValueType == "percentage") {

						$vDiscount = round($discountValue, 1) . ' ' . "%";

						$discountValue = round(($total_fare * $discountValue), 1) / 100;

						}else{

						$curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');

            $discountValue = round(($discountValue * $priceRatio),2);

						if ($discountValue > $total_fare) {

							$vDiscount = round($total_fare, 1) . ' ' . $curr_sym;

							}else{

							$vDiscount = round($discountValue, 1) . ' ' . $curr_sym;

						}

					}

					$total_fare = $total_fare - $discountValue;

					$Fare_data[0]['fDiscount_fixed'] = $discountValue; 

					if ($total_fare < 0) {

						$total_fare = 0;

						//$discountValue = $total_fare;

					}

					if($Fare_data[0]['eFareType'] == "Regular"){

						$Fare_data[0]['fDiscount'] = $discountValue;

						$Fare_data[0]['vDiscount'] = $vDiscount;

						}else{

						$Fare_data[0]['fDiscount'] = $Fare_data[0]['fDiscount_fixed'];

						$Fare_data[0]['vDiscount'] = $vDiscount;

					}

				}

        /* Tax Calculation */

        $fTax1 = $TaxArr['fTax1'];  

		    $fTax2 = $TaxArr['fTax2'];

        if($fTax1 > 0){

           $fTaxAmount1 = round((($total_fare * $fTax1) / 100 ),2);

           $total_fare = $total_fare+$fTaxAmount1;

           $Fare_data[$i]['fTax1'] = $vSymbol." ".number_format($fTaxAmount1,2); 

        }

        if($fTax2 > 0){

           $total_fare_new = $total_fare-$fTaxAmount1;

           $fTaxAmount2 = round((($total_fare_new * $fTax2) / 100 ),2);

           $total_fare = $total_fare+$fTaxAmount2;

           $Fare_data[$i]['fTax2'] = $vSymbol." ".number_format($fTaxAmount2,2);  

        }

        /* Tax Calculation */ 

				/** calculate fare **/

				$Fare_data[$i]['FareOfMinutes'] = $Minute_Fare;

				$Fare_data[$i]['FareOfDistance'] = $Distance_Fare;

				$Fare_data[$i]['FareOfCommision'] = $Commision_Fare;

				$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerMin'];

				$Fare_data[$i]['fPricePerKM'] = $Fare_data[$i]['fPricePerKM'];

				$Fare_data[$i]['fCommision'] = $Fare_data[$i]['fCommision'];

				$Fare_data[$i]['FinalFare'] = $total_fare;

				$Fare_data[$i]['iBaseFare'] = ($Fare_data[$i]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;

				$Fare_data[$i]['iMinFare'] = round( $Fare_data[$i]['iMinFare'] * $priceRatio, 2);

				if($Fare_data[$i]['eFareType'] == "Regular"){

					//$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($total_fare,2);

					$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($total_fare,2);

					}else{

					$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($Fare_data[$i]['FinalFare'],2);

				}

				// For calculation for rental vehiclefare

        		if(ENABLE_RENTAL_OPTION == 'Yes') {

					$checkrentalquery = "SELECT iRentalPackageId,iVehicleTypeId,vPackageName_".$userlangcode.",fPrice,fKiloMeter,fHour,fPricePerKM,fPricePerHour FROM  `rental_package` WHERE iVehicleTypeId = '".$Fare_data[$i]['iVehicleTypeId']."' ORDER BY `fPrice` ASC";

					$rental_data = $obj->MySQLSelect($checkrentalquery);

					$totalrental = count($rental_data);

					if($totalrental > 0) {

						$Fare_data[$i]['eRental']= 'Yes';

            $fPrice = $rental_data[0]['fPrice'];

            if($couponCode != ""){

              $discountValue_rental = $discountValue_orig;

              if($discountValueType == "percentage") {

                $discountValue_rental = round(($fPrice * $discountValue_orig), 1) / 100;

              }

            }

            $total_rental_fare = $fPrice - $discountValue_rental;

            if ($total_rental_fare < 0) {

  						$total_rental_fare = 0;

  					}

            $Fare_data[$i]['eRental_total_fare'] = $vSymbol." ".number_format(round($total_rental_fare * $priceRatio,1),2);

						//$Fare_data[$i]['eRental_total_fare'] = $vSymbol." ".number_format(round($rental_data[0]['fPrice'] * $priceRatio,1),2);

					} else {

						$Fare_data[$i]['eRental']= 'No';

						$Fare_data[$i]['eRental_total_fare'] = "";

					}

				} else {

					$Fare_data[$i]['eRental']= 'No';

					$Fare_data[$i]['eRental_total_fare'] = "";

				}

				// For calculation for rental vehiclefare

				$Fare_data[$i]['iBaseFare'] = $vSymbol." ".number_format($Fare_data[$i]['iBaseFare'],2);

				$Fare_data[$i]['fPricePerMin'] = $vSymbol." ".number_format(round($Fare_data[$i]['fPricePerMin'] * $priceRatio,1),2);

				$Fare_data[$i]['fPricePerKM'] = $vSymbol." ".number_format(round($Fare_data[$i]['fPricePerKM'] * $priceRatio,1),2);

				$Fare_data[$i]['fCommision'] = $vSymbol." ".number_format(round($Fare_data[$i]['fCommision'] * $priceRatio,1),2);

			}

			}else{

			$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);

			$fPickUpPrice = 1;

			$fNightPrice = 1;

      		

			$data_surgePrice = checkSurgePrice($Fare_data[0]['iVehicleTypeId'],"");

      		

			if($data_surgePrice['Action'] == "0"){

      			if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){

      				$fPickUpPrice=$data_surgePrice['SurgePriceValue'];

      				}else{

      				$fNightPrice=$data_surgePrice['SurgePriceValue'];

				}

			}

      

      if($APPLY_SURGE_ON_FLAT_FARE == "No" && $eFlatTrip == "Yes"){

           $fPickUpPrice = 1;

				   $fNightPrice = 1;

      }

      

			$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);

			$Fare_data[0]['TripTimeMinutes'] = $totalTimeInMinutes_trip;

			$Fare_data[0]['TripDistance'] = $tripDistance;

			//$result = getTripFare($Fare_data[0], $surgePrice);

			/** calculate fare **/

			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'];

			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];

			$Fare_data[0]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Fare_data[0]['iVehicleTypeId'],$Fare_data[0]['fPricePerKM']);

			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];

			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'];

			$iBaseFare = $Fare_data[0]['iBaseFare'];

			

			

			$fPricePerKM = $Fare_data[0]['fPricePerKM'];

			$fPricePerMin = $Fare_data[0]['fPricePerMin'];

			

			if ($Fare_data[0]['eFareType'] == 'Fixed') {

				$Fare_data[0]['fPricePerMin'] = 0;

				$Fare_data[0]['fPricePerKM'] = 0;

				//$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $Fare_data[0]['iQty'];

				$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $iQty;

				} else if ($Fare_data[0]['eFareType'] == 'Hourly') {

				$Fare_data[0]['iBaseFare'] = 0;

				$Fare_data[0]['fPricePerKM'] = 0;

				$totalHour = $Fare_data[0]['TripTimeMinutes'] / 60;

				$Fare_data[0]['TripTimeMinutes'] = $totalHour;

				$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerHour'];

			}

			

			$Minute_Fare = round(($fPricePerMin * $totalTimeInMinutes_trip) * $priceRatio, 2);

			$Distance_Fare = round(($fPricePerKM * $tripDistance) * $priceRatio, 2);

			$iBaseFare = round($iBaseFare * $priceRatio, 2);

			$fMaterialFee = round($Fare_data[0]['fMaterialFee'] * $priceRatio, 2);

			$fMiscFee = round($Fare_data[0]['fMiscFee'] * $priceRatio,2);

			$fDriverDiscount = round($Fare_data[0]['fDriverDiscount'] * $priceRatio,2);

			$fVisitFee= round($Fare_data[0]['fVisitFee'] * $priceRatio,2);

			 

      if($eFlatTrip == "No"){

        $total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;

  			$fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);

  			$SurgePriceFactor = strval($surgePrice);

  			$total_fare = $total_fare+$fSurgePriceDiff;

  			$minimamfare = round($Fare_data[0]['iMinFare'] * $priceRatio, 2);

  			if($minimamfare > $total_fare){

  				$fMinFareDiff = $minimamfare - $total_fare;

  				$total_fare = $minimamfare;

  				$Fare_data[0]['FinalFare'] = $total_fare;

  				}else{

  				$fMinFareDiff = 0;

  			}

      }else{

        $total_fare = round($fFlatTripPrice * $priceRatio,2);

        $fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);

			  $SurgePriceFactor = strval($surgePrice);

			  $total_fare = $total_fare+$fSurgePriceDiff;

        $Fare_data[0]['FinalFare'] = $total_fare;

        $fMinFareDiff = 0;

        $Minute_Fare = 0;

        $Distance_Fare = 0;

      }  

      

			$Commision_Fare = round((($total_fare * $Fare_data[0]['fCommision']) / 100), 2);

			

			## Calculate for Discount ##

			//$fSurgePriceDiff = $farewithsurcharge - $minimamfare;

			

			$discountValue = 0;

			$discountValueType = "cash"; 

			if($couponCode != ""){

				$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');

				$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true');

				if($discountValueType == "percentage") {

					$vDiscount = round($discountValue, 1) . ' ' . "%";

					$discountValue = round(($total_fare * $discountValue), 1) / 100;

					}else{

					$curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');

          $discountValue = round(($discountValue * $priceRatio),2);     

					if ($discountValue > $total_fare) {

						$vDiscount = round($total_fare, 1) . ' ' . $curr_sym;

						}else{

						$vDiscount = round($discountValue, 1) . ' ' . $curr_sym;

					}

				}

				$total_fare = $total_fare - $discountValue;

				$Fare_data[0]['fDiscount_fixed'] = $discountValue; 

				if ($total_fare < 0) {

					$total_fare = 0;

					//$discountValue = $total_fare;

				}

				if($Fare_data[0]['eFareType'] == "Regular"){

					$Fare_data[0]['fDiscount'] = $discountValue;

					$Fare_data[0]['vDiscount'] = $vDiscount;

					}else{

					$Fare_data[0]['fDiscount'] = $Fare_data[0]['fDiscount_fixed'];

					$Fare_data[0]['vDiscount'] = $vDiscount;

				}

			}

			## Calculate for Discount ##

       /* Tax Calculation */

      $fTax1 = $TaxArr['fTax1'];  

	    $fTax2 = $TaxArr['fTax2'];

      if($fTax1 > 0){

         $fTaxAmount1 = round((($total_fare * $fTax1) / 100 ),2);

         $total_fare = $total_fare+$fTaxAmount1;

         $Fare_data[0]['fTax1'] = $vSymbol." ".number_format($fTaxAmount1,2); 

      }

      if($fTax2 > 0){

         $total_fare_new = $total_fare-$fTaxAmount1;

         $fTaxAmount2 = round((($total_fare_new * $fTax2) / 100 ),2);

         $total_fare = $total_fare+$fTaxAmount2;

         $Fare_data[0]['fTax2'] = $vSymbol." ".number_format($fTaxAmount2,2);  

      }

      /* Tax Calculation */

			/** calculate fare **/

			$Fare_data[0]['FareOfMinutes'] = $Minute_Fare;

			$Fare_data[0]['FareOfDistance'] = $Distance_Fare;

			$Fare_data[0]['FareOfCommision'] = $Commision_Fare;

			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];

			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];

			$Fare_data[0]['fCommision'] = $Fare_data[0]['fCommision'];

			$Fare_data[0]['FinalFare'] = $total_fare;

			$Fare_data[0]['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;

			$Fare_data[0]['iMinFare'] = round( $Fare_data[0]['iMinFare'] * $priceRatio, 2);

			if($Fare_data[0]['eFareType'] == "Regular"){

				//$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($total_fare,2);

				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($total_fare,2);

				}else{

				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($Fare_data[0]['FinalFare'],2);

			}

			$Fare_data[0]['iBaseFare'] = $vSymbol." ".number_format($Fare_data[0]['iBaseFare'],2);

			$Fare_data[0]['fPricePerMin'] = $vSymbol." ".number_format(round($Fare_data[0]['fPricePerMin'] * $priceRatio,1),2);

			$Fare_data[0]['fPricePerKM'] = $vSymbol." ".number_format(round($Fare_data[0]['fPricePerKM'] * $priceRatio,1),2);

			$Fare_data[0]['fCommision'] = $vSymbol." ".number_format(round($Fare_data[0]['fCommision'] * $priceRatio,1),2);

		/*	// For calculation for rental vehiclefare

			if(ENABLE_RENTAL_OPTION == 'Yes') {

				$checkrentalquery = "SELECT iRentalPackageId,iVehicleTypeId,vPackageName_".$userlangcode.",fPrice,fKiloMeter,fHour,fPricePerKM,fPricePerHour FROM  `rental_package` WHERE iVehicleTypeId = '".$vehicleTypeID."'";

				$rental_data = $obj->MySQLSelect($checkrentalquery);

				$totalrental = count($rental_data);

				if($totalrental > 0) {

					if($eUserType == 'Driver'){

						$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iUserId,'','true');

						if($iDriverVehicleId > 0){

							$rentquery = "SELECT `vRentalCarType` FROM `driver_vehicle` WHERE  iDriverVehicleId = '".$iDriverVehicleId."' AND FIND_IN_SET('".$vehicleTypeID."', vRentalCarType)";

							$rentalData_Arr = $obj->MySQLSelect($rentquery);

							if(count($rentalData_Arr) > 0){

								$Fare_data[0]['total_fare'] = $vSymbol." ".number_format(round($rental_data[0]['fPrice'] * $priceRatio,1),2);

							}

						}

					}

				} 

			} 

			// For calculation for rental vehiclefare*/

			$vVehicleType = get_value('vehicle_type', "vVehicleType_" . $userlangcode, 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$vVehicleCategoryData = get_value('vehicle_category', 'vLogo,vCategory_' . $userlangcode . ' as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);

			$Fare_data[0]['vVehicleCategory'] = $vVehicleCategoryData[0]['vCategory'];

			$vVehicleFare = get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$eType = $Fare_data[0]['eFareType'];

			$tripFareDetailsArr = array();

      if($eFlatTrip == "Yes"){

          $i = 0;

          $displayfare =  round($fFlatTripPrice * $priceRatio,2);

          $displayfare = $vSymbol." ".number_format($displayfare,2);

          $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $displayfare;$i++;

          if ($fSurgePriceDiff > 0) {

            $tripFareDetailsArr[$i][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = $vSymbol." ".formatNum($fSurgePriceDiff);$i++;

          }

          if($discountValue > 0) {

    				$farediscount = $vSymbol." ".formatNum($Fare_data[0]['fDiscount']);

    				$tripFareDetailsArr[$i][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- " . $farediscount;

    				$i++;

    			} 

          $tripFareDetailsArr[$i][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $Fare_data[0]['total_fare'];

          $Fare_data = $tripFareDetailsArr; 

      }else{

    			$i = 0;

    			$countUfx = 0;

    			if($eType == "UberX") {

    				$tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $Fare_data[0]['vVehicleCategory'] . "-" . $vVehicleType;

    				$countUfx = 1;

    			}

    			if ($eType == "Regular") {

    				// 17-09-2021
					$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vSymbol." ".formatNum($iBaseFare);

    				if ($countUfx == 1) {

    					$i++;

    				}

    				if($eUnit == "Miles"){

    					$tripDistanceDisplay = $tripDistance * 0.621371;

    					$tripDistanceDisplay = round($tripDistanceDisplay,2); 

              //$DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT'];

              $LBL_MILE_DISTANCE_TXT = ($tripDistanceDisplay > 1)? $languageLabelsArr['LBL_MILE_DISTANCE_TXT'] : $languageLabelsArr['LBL_ONE_MILE_TXT'];

              $DisplayDistanceTxt = $LBL_MILE_DISTANCE_TXT; 

    					}else{

    					$tripDistanceDisplay = $tripDistance;

              //$DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];

              $LBL_KM_DISTANCE_TXT = ($tripDistanceDisplay > 1)? $languageLabelsArr['LBL_DISPLAY_KMS'] : $languageLabelsArr['LBL_KM_DISTANCE_TXT'];

              $DisplayDistanceTxt = $LBL_KM_DISTANCE_TXT;

    				}

            $tripDistanceDisplay = formatNum($tripDistanceDisplay);

    				if($isDestinationAdded == "Yes"){      

    				  $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $tripDistanceDisplay . " " . $DisplayDistanceTxt . ")"] = $vSymbol." ".formatNum($Fare_data[0]['FareOfDistance']);

    				}else{                           

              $priceperkm = getVehiclePrice_ByUSerCountry($iUserId,$fPricePerKM);

              $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT']] = $vSymbol." ".formatNum($priceperkm)."/".strtolower($DisplayDistanceTxt);

            }

    				$i++;

            //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $totalTimeInMinutes_trip . ")"] = $vSymbol . formatNum($Fare_data[0]['FareOfMinutes']);

            $hours =  floor($totalTimeInMinutes_trip/60); // No. of mins/60 to get the hours and round down

            $mins =   $totalTimeInMinutes_trip % 60; // No. of mins/60 - remainder (modulus) is the minutes

            $LBL_HOURS_TXT = ($hours > 1)? $languageLabelsArr['LBL_HOURS_TXT'] : $languageLabelsArr['LBL_HOUR_TXT'];

            $LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];

            if($hours >= 1){

               $tripDurationDisplay = $hours." ".$LBL_HOURS_TXT.", ".$mins." ".$LBL_MINUTES_TXT;

            }else{

               $tripDurationDisplay = $totalTimeInMinutes_trip." ".$LBL_MINUTES_TXT;

            }

            if($isDestinationAdded == "Yes"){

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $tripDurationDisplay . ")"] = $vSymbol." ".formatNum($Fare_data[0]['FareOfMinutes']);

            }else{

              $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT']] = $vSymbol." ".formatNum($fPricePerMin)."/".$languageLabelsArr['LBL_MIN_SMALL_TXT'];

            }  

    				$i++;

    				} else if ($eType == "Fixed") {

    				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($Fare_data[0]['iQty'] > 1)?$Fare_data[0]['iQty'].' X '.$vSymbol." ".formatNum($vVehicleFare) : $vSymbol." ".formatNum($vVehicleFare);

    				if ($countUfx == 1) {

    					$i++;

    				}

    				$total_fare = $vVehicleFare +  $Fare_data[0]['fVisitFee'] - $Fare_data[0]['fDiscount_fixed'];

    				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format(round($total_fare * $priceRatio,1),2);

    				} else if ($eType == "Hourly") {

    				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $totalTimeInMinutes_trip . ")"] = $vSymbol." ".$Fare_data[0]['FareOfMinutes'];

    				if ($countUfx == 1) {

    					$i++;

    				}

    			}

    			$fVisitFee = $Fare_data[0]['fVisitFee'];

    			if ($fVisitFee > 0) {

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = $vSymbol." ".$fVisitFee;

    				$i++;

    			}

    			if ($fMaterialFee > 0) {                                                                     

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = $vSymbol." ".$fMaterialFee;

    				$i++;

    			}

    			if ($fMiscFee > 0) {

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = $vSymbol." ".$fMiscFee;

    				$i++;

    			}

    			

    			

    			if ($fSurgePriceDiff > 0) {

    				if($isDestinationAdded == "Yes"){

                //$normalfare = $total_fare-$fSurgePriceDiff+$discountValue-$fTaxAmount1-$fTaxAmount2;

    				//$normalfare = formatNum($normalfare * $priceRatio);

                $normalfare = $iBaseFare + $Distance_Fare + $Minute_Fare;

            $normalfare = formatNum($normalfare);

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = $vSymbol." ".$normalfare;

    				$i++;

             }  

    				//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = $vSymbol." ".formatNum($fSurgePriceDiff * $priceRatio);

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " " . $SurgePriceFactor] = $vSymbol." ".formatNum($fSurgePriceDiff);

    				$i++;

    			} 

          if($fMinFareDiff > 0 && $isDestinationAdded == "Yes") {

    				//$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;

    				$minimamfare = formatNum($minimamfare);

    				$tripFareDetailsArr[$i + 1][$vSymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = $vSymbol." ".formatNum($fMinFareDiff);

    				$Fare_data[0]['TotalMinFare'] = $minimamfare;

    				$i++;

    			} 

    			if ($fDriverDiscount > 0) {

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = "- " . $vSymbol." ".$fDriverDiscount;

    				$i++;

    			}

    			if ($discountValue > 0) {

    				//$farediscount = $vSymbol." ".number_format(round($Fare_data[0]['fDiscount'] * $priceRatio,1),2);

            $farediscount = $vSymbol." ".formatNum($Fare_data[0]['fDiscount']);

    				//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- " . $vSymbol . $Fare_data[0]['fDiscount'];

    				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- " . $farediscount;

    				$i++;

    			} 

          if($fTax1 > 0){

             $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fTax1. " % "] = $Fare_data[0]['fTax1'];

             $i++;

          }

          if($fTax2 > 0){

             $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fTax2. " % "] = $Fare_data[0]['fTax2'];

    				$i++;

    			} 

    			

          if($isDestinationAdded == "Yes"){

    			$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $Fare_data[0]['total_fare'];

          } 

          		include TPATH_CLASS . '../../label_list.php';

    			//$Fare_data = array_merge($Fare_data[0], $tripFareDetailsArr);

    			$Fare_data = $tripFareDetailsArr;

      }   

      

		}

		

		return $Fare_data;

	}

	

	function getVehicleFareConfig($tabelName, $vehicleTypeID) {

		global $obj;

		$sql = "SELECT * FROM `" . $tabelName . "` WHERE iVehicleTypeId='$vehicleTypeID'";

		$Data_fare = $obj->MySQLSelect($sql);

		

		return $Data_fare;

	}

	

	function processTripsLocations($tripId, $latitudes, $longitudes) {

		global $obj;

		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";

		$DataExist = $obj->MySQLSelect($sql);

		

		if (count($DataExist) > 0) {

			

			$latitudeList = $DataExist[0]['tPlatitudes'];

			$longitudeList = $DataExist[0]['tPlongitudes'];

			

			if ($latitudeList != '') {

				$data_latitudes = $latitudeList . ',' . $latitudes;

				} else {

				$data_latitudes = $latitudes;

			}

			

			if ($longitudeList != '') {

				$data_longitudes = $longitudeList . ',' . $longitudes;

				} else {

				$data_longitudes = $longitudes;

			}

			

			$where = " iTripId = '" . $tripId . "'";

			$Data_tripsLocations['tPlatitudes'] = $data_latitudes;

			$Data_tripsLocations['tPlongitudes'] = $data_longitudes;

			$id = $obj->MySQLQueryPerform("trips_locations", $Data_tripsLocations, 'update', $where);

			} else {

			

	  	$sql = "SELECT tStartLat,tStartLong FROM `trips` WHERE iTripId = '$tripId'";

		  $TripData = $obj->MySQLSelect($sql);

      $tStartLat = $TripData[0]['tStartLat'];

      $tStartLong = $TripData[0]['tStartLong']; 

      if($latitudes != ""){

        $insertlat = $tStartLat.",".$latitudes;

      }else{

        $insertlat = $tStartLat;

      }

      if($longitudes != ""){

        $insertlong = $tStartLong.",".$longitudes;

      }else{

        $insertlong = $tStartLong;

      }

			$Data_trips_locations['iTripId'] = $tripId;

			$Data_trips_locations['tPlatitudes'] = $insertlat;

			$Data_trips_locations['tPlongitudes'] = $insertlong;

			

			$id = $obj->MySQLQueryPerform("trips_locations", $Data_trips_locations, 'insert');

		}

		return $id;

	}

	

	function calcluateTripDistance($tripId) {

		global $obj;

		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";

		$Data_tripsLocations = $obj->MySQLSelect($sql);

		$TotalDistance = 0;

		if (count($Data_tripsLocations) > 0) {

			$trip_path_latitudes = $Data_tripsLocations[0]['tPlatitudes'];

			$trip_path_longitudes = $Data_tripsLocations[0]['tPlongitudes'];

			$trip_path_latitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_latitudes);

			$trip_path_longitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_longitudes);

			$TripPathLatitudes = explode(",", $trip_path_latitudes);

			$TripPathLongitudes = explode(",", $trip_path_longitudes);

			$previousDistance = 0;  

      $isFirstProcessed = false;

			for ($i = 0; $i < count($TripPathLatitudes) - 1; $i++) {

          if($isFirstProcessed == false){

              $firsttemplat = $TripPathLatitudes[0]; 

              $firsttempLon = $TripPathLongitudes[0];

              $nexttempLat = $TripPathLatitudes[$i];

             	$nexttempLon = $TripPathLongitudes[$i];

              $TempDistance_First = distanceByLocation($firsttemplat, $firsttempLon, $nexttempLat, $nexttempLon, "K");

              if($TempDistance_First > 2){

                 continue;

              }else{

                 $isFirstProcessed = true;

                 $previousDistance = $TempDistance_First;

                 continue;

              }

          }

        $tempLat_current = $TripPathLatitudes[$i];

        $tempLon_current = $TripPathLongitudes[$i];

        $tempLat_next = $TripPathLatitudes[$i + 1];

        $tempLon_next = $TripPathLongitudes[$i + 1];  

        if($tempLat_current == '0.0' || $tempLon_current == '0.0' || $tempLat_next == '0.0' || $tempLon_next == '0.0' || $tempLat_current == '-180.0' || $tempLon_current == '-180.0' || $tempLat_next == '-180.0' || $tempLon_next == '-180.0' || ($tempLat_current == $tempLat_next && $tempLon_current == $tempLon_next)) {

				//if ($tempLat_current == '0.0' || $tempLon_current == '0.0' || $tempLat_next == '0.0' || $tempLon_next == '0.0' || $tempLat_current == '-180.0' || $tempLon_current == '-180.0' || $tempLat_next == '-180.0' || $tempLon_next == '-180.0' || $tempLat_current == $tempLat_next || $tempLon_current == $tempLon_next) {

					continue;

				}

				$TempDistance = distanceByLocation($tempLat_current, $tempLon_current, $tempLat_next, $tempLon_next, "K");

				if (is_nan($TempDistance)) {

					$TempDistance = 0;

				}

        if(abs($previousDistance - $TempDistance) > 0.1){

					$TempDistance = 0;

				}else{

				 	$previousDistance = $TempDistance;   

				}

				$TotalDistance += $TempDistance;   

			}

		}

		return round($TotalDistance, 2);

  }

	/*function calcluateTripDistance($tripId) {

		global $obj;

		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";

		$Data_tripsLocations = $obj->MySQLSelect($sql);

		

		$TotalDistance = 0;

		if (count($Data_tripsLocations) > 0) {

			$trip_path_latitudes = $Data_tripsLocations[0]['tPlatitudes'];

			$trip_path_longitudes = $Data_tripsLocations[0]['tPlongitudes'];

			

			$trip_path_latitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_latitudes);

			$trip_path_longitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_longitudes);

			

			$TripPathLatitudes = explode(",", $trip_path_latitudes);

			

			$TripPathLongitudes = explode(",", $trip_path_longitudes);

			

			$previousDistance = 0;

			for ($i = 0; $i < count($TripPathLatitudes) - 1; $i++) {

				$tempLat_current = $TripPathLatitudes[$i];

				$tempLon_current = $TripPathLongitudes[$i];

				$tempLat_next = $TripPathLatitudes[$i + 1];

				$tempLon_next = $TripPathLongitudes[$i + 1];

				

				if ($tempLat_current == '0.0' || $tempLon_current == '0.0' || $tempLat_next == '0.0' || $tempLon_next == '0.0' || $tempLat_current == '-180.0' || $tempLon_current == '-180.0' || $tempLat_next == '-180.0' || $tempLon_next == '-180.0') {

					continue;

				}

				

				$TempDistance = distanceByLocation($tempLat_current, $tempLon_current, $tempLat_next, $tempLon_next, "K");

				

				if (is_nan($TempDistance)) {

					$TempDistance = 0;

				}

				if($previousDistance == 0){

					$previousDistance = $TempDistance;

					}else if(abs($previousDistance - $TempDistance) > 0.1){

					$TempDistance = 0;

					}else{

					$previousDistance = $TempDistance;

				}

				$TotalDistance += $TempDistance;

			}

		}

		

		return round($TotalDistance, 2);

	}*/

	

	/*function checkDistanceWithGoogleDirections($tripDistance, $startLatitude, $startLongitude, $endLatitude, $endLongitude, $isFareEstimate = "0", $vGMapLangCode = "") {

		global $generalobj, $obj, $GOOGLE_SEVER_GCM_API_KEY;

		

		if ($vGMapLangCode == "" || $vGMapLangCode == NULL) {

			$vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');

			$vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];

		}

		

		$GOOGLE_API_KEY = $GOOGLE_SEVER_GCM_API_KEY;

		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $startLatitude . "," . $startLongitude . "&destination=" . $endLatitude . "," . $endLongitude . "&sensor=false&key=" . $GOOGLE_API_KEY . "&language=" . $vGMapLangCode;

		

		try {

			$jsonfile = file_get_contents($url);

			} catch (ErrorException $ex) {

			// return $tripDistance;

			

			$returnArr['Action'] = "0";

			echo json_encode($returnArr);

			exit;

			// echo 'Site not reachable (' . $ex->getMessage() . ')';

		}

		

		$jsondata = json_decode($jsonfile);

		$distance_google_directions = ($jsondata->routes[0]->legs[0]->distance->value) / 1000;

		

		if ($isFareEstimate == "0") {

			$comparedDist = ($distance_google_directions * 85) / 100;

			

			if ($tripDistance > $comparedDist) {

				return $tripDistance;

				} else {

				return round($distance_google_directions, 2);

			}

			} else {

			$duration_google_directions = ($jsondata->routes[0]->legs[0]->duration->value) / 60;

			$sAddress = ($jsondata->routes[0]->legs[0]->start_address);

			$dAddress = ($jsondata->routes[0]->legs[0]->end_address);

			$steps = ($jsondata->routes[0]->legs[0]->steps);

			

			$returnArr['Time'] = $duration_google_directions;

			$returnArr['Distance'] = $distance_google_directions;

			$returnArr['SAddress'] = $sAddress;

			$returnArr['DAddress'] = $dAddress;

			$returnArr['steps'] = $steps;

			

			return $returnArr;

		}

	} */

  

  function checkDistanceWithGoogleDirections($tripDistance, $startLatitude, $startLongitude, $endLatitude, $endLongitude, $isFareEstimate = "0", $vGMapLangCode = "",$isReturnArr=false) {

		global $generalobj, $obj;

		

		if ($vGMapLangCode == "" || $vGMapLangCode == NULL) {

			$vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');

			$vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];

		}

		

		$GOOGLE_API_KEY = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_GCM_API_KEY");

		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $startLatitude . "," . $startLongitude . "&destination=" . $endLatitude . "," . $endLongitude . "&sensor=false&key=" . $GOOGLE_API_KEY . "&language=" . $vGMapLangCode;

		

		try {

			$jsonfile = file_get_contents($url);

			} catch (ErrorException $ex) {

			// return $tripDistance;

			

			$returnArr['Action'] = "0";

			echo json_encode($returnArr);

			exit;

			// echo 'Site not reachable (' . $ex->getMessage() . ')';

		}

		

		$jsondata = json_decode($jsonfile);

		$distance_google_directions = ($jsondata->routes[0]->legs[0]->distance->value) / 1000;

		

		if ($isFareEstimate == "0") {

			$comparedDist = ($distance_google_directions * 85) / 100;



			if($isReturnArr == true) {



				if ($tripDistance > $comparedDist) {

					$distance_google_directions_val = $tripDistance;

				} else {

					$distance_google_directions_val = round($distance_google_directions, 2);

				}



				$duration_google_directions = ($jsondata->routes[0]->legs[0]->duration->value);

				$sAddress = ($jsondata->routes[0]->legs[0]->start_address);

				$dAddress = ($jsondata->routes[0]->legs[0]->end_address);

				$steps = ($jsondata->routes[0]->legs[0]->steps);

				

				$returnArr['Time'] = $duration_google_directions;

				$returnArr['Distance'] = $distance_google_directions_val;

				$returnArr['GDistance'] = $distance_google_directions;

				$returnArr['SAddress'] = $sAddress;

				$returnArr['DAddress'] = $dAddress;

				$returnArr['steps'] = $steps;

				

				return $returnArr;



			} else {

				if ($tripDistance > $comparedDist) {

					return $tripDistance;

				} else {

					return round($distance_google_directions, 2);

				}

			}

		} else {

			$duration_google_directions = ($jsondata->routes[0]->legs[0]->duration->value) / 60;

			$sAddress = ($jsondata->routes[0]->legs[0]->start_address);

			$dAddress = ($jsondata->routes[0]->legs[0]->end_address);

			$steps = ($jsondata->routes[0]->legs[0]->steps);

			

			$returnArr['Time'] = $duration_google_directions;

			$returnArr['Distance'] = $distance_google_directions;

			$returnArr['SAddress'] = $sAddress;

			$returnArr['DAddress'] = $dAddress;

			$returnArr['steps'] = $steps;

			

			return $returnArr;

		}

	}

	

	function distanceByLocation($lat1, $lon1, $lat2, $lon2, $unit) {

		if ((($lat1 == $lat2) && ($lon1 == $lon2)) || ($lat1 == '' || $lon1 == '' || $lat2 == '' || $lon2 == '')) {

			return 0;

		}

		

		$theta = $lon1 - $lon2;

		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));

		$dist = acos($dist);

		$dist = rad2deg($dist);

		$miles = $dist * 60 * 1.1515;

		$unit = strtoupper($unit);

		

		if ($unit == "K") {

			return ($miles * 1.609344);

			} else if ($unit == "N") {

			return ($miles * 0.8684);

			} else {

			return $miles;

		}

	}

	

	function getLanguageLabelsArr_01092017($lCode = '', $directValue = "") {

		global $obj;

		

		/* find default language of website set by admin */

		$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";

		$default_label = $obj->MySQLSelect($sql);

		

		if ($lCode == '') {

			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';

		}

		

		

		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE lPage_id >= 27 AND  `vCode` = '" . $lCode . "' ";

		$all_label = $obj->MySQLSelect($sql);

		

		$x = array();

		for ($i = 0; $i < count($all_label); $i++) {

			$vLabel = $all_label[$i]['vLabel'];

			$vValue = $all_label[$i]['vValue'];

			$x[$vLabel] = $vValue;

		}

		

		/* 

			$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label_other`  WHERE  `vCode` = '" . $lCode . "' ";

			$all_label = $obj->MySQLSelect($sql);

			

			for ($i = 0; $i < count($all_label); $i++) {

			$vLabel = $all_label[$i]['vLabel'];

			

			$vValue = $all_label[$i]['vValue'];

			$x[$vLabel] = $vValue;

		} */

		

		$x['vCode'] = $lCode; // to check in which languge code it is loading

		

		if ($directValue == "") {

			$returnArr['Action'] = "1";

			$returnArr['LanguageLabels'] = $x;

			

			return $returnArr;

			} else {

			return $x;

		}

	}

	

	function getLanguageLabelsArr($lCode = '', $directValue = "") {

		global $obj;

		

		/* find default language of website set by admin */

		$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";

		$default_label = $obj->MySQLSelect($sql);

		

		if ($lCode == '') {

			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';

		}

		

		

		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label` WHERE  `vCode` = '" . $lCode . "' UNION SELECT `vLabel` , `vValue`  FROM  `language_label_other` WHERE  `vCode` = '" . $lCode . "' ";

		$all_label = $obj->MySQLSelect($sql);

		

		$x = array();

		for ($i = 0; $i < count($all_label); $i++) {

			$vLabel = $all_label[$i]['vLabel'];

			$vValue = $all_label[$i]['vValue'];

			$x[$vLabel] = $vValue;

		}

		

		

		/*$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label_other`  WHERE  `vCode` = '" . $lCode . "' ";

		$all_label = $obj->MySQLSelect($sql);

		

		for ($i = 0; $i < count($all_label); $i++) {

			$vLabel = $all_label[$i]['vLabel'];

			

			$vValue = $all_label[$i]['vValue'];

			$x[$vLabel] = $vValue;

		}     */

		

		$x['vCode'] = $lCode; // to check in which languge code it is loading

		

		if ($directValue == "") {

			$returnArr['Action'] = "1";

			$returnArr['LanguageLabels'] = $x;

			

			return $returnArr;

			} else {

			return $x;

		}

	}

	

	function sendEmeSms($toMobileNum, $message) {

		global $generalobj,$MOBILE_VERIFY_SID_TWILIO,$MOBILE_VERIFY_TOKEN_TWILIO,$MOBILE_NO_TWILIO;

		$account_sid = $MOBILE_VERIFY_SID_TWILIO;

		$auth_token = $MOBILE_VERIFY_TOKEN_TWILIO;

		$twilioMobileNum = $MOBILE_NO_TWILIO;

		

		$client = new Services_Twilio($account_sid, $auth_token);

		try {

			$sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);

			return 1;

			} catch (Services_Twilio_RestException $e) {

			return 0;

		}

	}

	

	function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {

		$date = new DateTime($time, new DateTimeZone($fromTz));

		$date->setTimezone(new DateTimeZone($toTz));

		$time = $date->format($dateFormat);

		return $time;

	}

	

	/**

		* Sending Push Notification

	*/

	function send_notification($registatoin_ids, $message, $filterMsg = 0) {

		// include config

		// include_once './config.php';

		//global $generalobj, $obj,$FIREBASE_API_ACCESS_KEY,$ENABLE_PUBNUB;

    global $generalobj, $obj;

		

		$FIREBASE_API_ACCESS_KEY = $generalobj->getConfigurations("configurations", "FIREBASE_API_ACCESS_KEY");

		$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");

		

		$fields = array

		(

		'registration_ids'  => $registatoin_ids,

		'click_action' => ".MainActivity",      

		'priority' => "high",

		//'data'          => $msg

		'data'         =>  $message 

		);

		

		$finalFields = json_encode($fields, JSON_UNESCAPED_UNICODE);

		

		

		if ($filterMsg == 1) {

			$finalFields = stripslashes(preg_replace("/[\n\r]/", "", $finalFields));

		}

		

		$headers = array

		(

		'Authorization: key=' . $FIREBASE_API_ACCESS_KEY,

		'Content-Type: application/json',

		);

		//Setup headers:

		// echo "<pre>";print_r($headers);exit;

		//Setup curl, add headers and post parameters.

		$ch = curl_init();

		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

		curl_setopt( $ch,CURLOPT_POST, true );

		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true  );

		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

		curl_setopt( $ch,CURLOPT_POSTFIELDS, $finalFields );

		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    

		//Send the request

		$response = curl_exec($ch); //echo "<pre>";print_r($response);exit;

		if ($response === FALSE) {

			// die('Curl failed: ' . curl_error($ch));

			if($ENABLE_PUBNUB == "No"){ 

				$returnArr['Action'] = "0";

				$returnArr['message'] = "LBL_SERVER_COMM_ERROR";

				$returnArr['ERROR'] = curl_error($ch);

				echo json_encode($returnArr);

				exit;

			}   

		}

		$responseArr = json_decode($response);

		$success = $responseArr->success; 

		//Close request

		curl_close($ch);

		return $success;

	}

	

	function sendApplePushNotification($PassengerToDriver = 0, $deviceTokens, $message, $alertMsg, $filterMsg,$fromDepart = '') {

		//global $generalobj, $obj, $IPHONE_PEM_FILE_PASSPHRASE,$APP_MODE,$ENABLE_PUBNUB, $PARTNER_APP_IPHONE_PEM_FILE_NAME, $PASSENGER_APP_IPHONE_PEM_FILE_NAME;

		global $generalobj, $obj;

    $sql = "select vValue,vName from configurations where vName in('IPHONE_PEM_FILE_PASSPHRASE','APP_MODE','ENABLE_PUBNUB','PARTNER_APP_IPHONE_PEM_FILE_NAME','PASSENGER_APP_IPHONE_PEM_FILE_NAME','PRO_PASSENGER_APP_IPHONE_PEM_FILE_NAME','PRO_PARTNER_APP_IPHONE_PEM_FILE_NAME')";

		$Data_config = $obj->MySQLSelect($sql);

    for($i=0;$i<count($Data_config);$i++){

			$temp_val = $Data_config[$i]['vValue'];

      $temp_vName = $Data_config[$i]['vName']; 

      $$temp_vName = trim($temp_val);

		}

		if($message == ""){

			return "";

		}

		$passphrase = $IPHONE_PEM_FILE_PASSPHRASE;

		//$APP_MODE = $APP_MODE;

		//$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");

		

		$prefix = "";

		$url_apns = 'ssl://gateway.sandbox.push.apple.com:2195';

		if ($APP_MODE == "Production") {

			$prefix = "PRO_";

			$url_apns = 'ssl://gateway.push.apple.com:2195';

		}

		

		if ($PassengerToDriver == 1) {

			//$name = $generalobj->getConfigurations("configurations", $prefix . "PARTNER_APP_IPHONE_PEM_FILE_NAME");

      $name1 = $prefix."PARTNER_APP_IPHONE_PEM_FILE_NAME";

      $name = $$name1;                                    

		} else {

			//$name = $generalobj->getConfigurations("configurations", $prefix . "PASSENGER_APP_IPHONE_PEM_FILE_NAME");

      $name1 = $prefix."PASSENGER_APP_IPHONE_PEM_FILE_NAME";

      $name = $$name1;

		}

		$ctx = stream_context_create();

		

		if($fromDepart == 'admin') { $name = '../'.$name; }

		stream_context_set_option($ctx, 'ssl', 'local_cert', $name);

		

		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		$fp = stream_socket_client(

		$url_apns, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

		

		// echo "deviceTokens => <pre>";

		// print_r($deviceTokens);

		// echo "<pre>"; print_r($fp); die;

		if (!$fp) {

			if($ENABLE_PUBNUB == "No"){

				$returnArr['Action'] = "0";

				$returnArr['message'] = "LBL_SERVER_COMM_ERROR";

				$returnArr['ERROR'] = $err.$errstr." ".PHP_EOL;

				echo json_encode($returnArr);

				exit;

				//exit("Failed to connect: $err $errstr" . PHP_EOL);

			}   

		}

		

		// Create the payload body

    if(is_array($alertMsg)){

      for ($device = 0; $device < count($deviceTokens); $device++) {

        $body['aps'] = array(

          'alert' => $alertMsg[$device],

          'content-available' => 1,

          'body' => $message[$device],

          'sound' => 'default'

  		  );

  			// Build the binary notification

        // Encode the payload as JSON

    		$payload = json_encode($body, JSON_UNESCAPED_UNICODE);

    		//        $payload= stripslashes(preg_replace("/[\n\r]/","",$payload));

    		if ($filterMsg == 1) {

    			$payload = stripslashes(preg_replace("/[\n\r]/", "", $payload));

    		}

  			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceTokens[$device]) . pack('n', strlen($payload)) . $payload;

		

  			// Send it to the server

  			$result = fwrite($fp, $msg, strlen($msg));

  		}

    }else{

		$body['aps'] = array(

        'alert' => $alertMsg,

        'content-available' => 1,

        'body' => $message,

        'sound' => 'default'

		);

		

		// Encode the payload as JSON

		$payload = json_encode($body, JSON_UNESCAPED_UNICODE);

		//        $payload= stripslashes(preg_replace("/[\n\r]/","",$payload));

		if ($filterMsg == 1) {

			$payload = stripslashes(preg_replace("/[\n\r]/", "", $payload));

		}

		

		for ($device = 0; $device < count($deviceTokens); $device++) {

			// Build the binary notification

			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceTokens[$device]) . pack('n', strlen($payload)) . $payload;

			

			// Send it to the server

			$result = fwrite($fp, $msg, strlen($msg));

			

  		}

		}

		// Close the connection to the server

		fclose($fp);

	}

	

	function getOnlineDriverArr($sourceLat, $sourceLon,$address_data=array(),$DropOff="No",$From_Autoassign="No",$Check_Driver_UFX="No",$Check_Date_Time="",$destLat="", $destLon="",$eType = "Ride") {

		global $generalobj, $obj, $RESTRICTION_KM_NEAREST_TAXI,$LIST_DRIVER_LIMIT_BY_DISTANCE,$DRIVER_REQUEST_METHOD,$COMMISION_DEDUCT_ENABLE,$WALLET_MIN_BALANCE,$RESTRICTION_KM_NEAREST_TAXI,$APP_TYPE,$vTimeZone,$PROVIDER_AVAIL_LOC_CUSTOMIZE;

		

		$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);

		$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));

		$LIST_DRIVER_LIMIT_BY_DISTANCE = $From_Autoassign =="Yes" ? $RESTRICTION_KM_NEAREST_TAXI : $LIST_DRIVER_LIMIT_BY_DISTANCE;

		$vWorkLocationRadius = $RESTRICTION_KM_NEAREST_TAXI;

    /*$LIST_DRIVER_LIMIT_BY_DISTANCE = $generalobj->getConfigurations("configurations", $From_Autoassign =="Yes" ?"RESTRICTION_KM_NEAREST_TAXI" : "LIST_DRIVER_LIMIT_BY_DISTANCE");     $DRIVER_REQUEST_METHOD = $generalobj->getConfigurations("configurations", "DRIVER_REQUEST_METHOD"); 		$COMMISION_DEDUCT_ENABLE=$generalobj->getConfigurations("configurations","COMMISION_DEDUCT_ENABLE");		$WALLET_MIN_BALANCE=$generalobj->getConfigurations("configurations","WALLET_MIN_BALANCE");

		$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");*/

    //if($APP_TYPE == "UberX"){  

    if($eType == "UberX" && $PROVIDER_AVAIL_LOC_CUSTOMIZE == "Yes"){

       //$vLatitude = "COALESCE(NULLIF(vWorkLocationLatitude,''), vLatitude)"; //$vLongitude = "COALESCE(NULLIF(vWorkLocationLongitude,''), vLongitude)";

       //$vLatitude = "IF(register_driver.eSelectWorkLocation = 'Fixed',COALESCE(NULLIF(vWorkLocationLatitude,''), vLatitude),vLatitude)"; //$vLongitude = "IF(register_driver.eSelectWorkLocation = 'Fixed',COALESCE(NULLIF(vWorkLocationLongitude,''), vLongitude),vLongitude)";

       $vLatitude = "IF(register_driver.eSelectWorkLocation = 'Fixed',COALESCE(NULLIF(vWorkLocationLatitude,''), vLatitude),vLatitude)";

       $vLongitude = "IF(register_driver.eSelectWorkLocation = 'Fixed',COALESCE(NULLIF(vWorkLocationLongitude,''), vLongitude),vLongitude)";

    }else{

       $vLatitude = 'vLatitude';

       $vLongitude = 'vLongitude';

    } 

		

		$param = ($DRIVER_REQUEST_METHOD == "Time") ? "tOnline" : "tLocationUpdateDate";

		

    $sourceLocationArr =array($sourceLat,$sourceLon);

		$destinationLocationArr =array($destLat,$destLon); 

		if($DropOff == "No"){

			$address_data['CheckAddress'] = $address_data['PickUpAddress']; 

			//$allowed_ans = checkRestrictedArea($address_data,"No");

			$source_array = $sourceLocationArr;

			//$allowed_ans = checkRestrictedAreaNew($source_array,"No");

      $allowed_ans = checkAllowedAreaNew($source_array,"No");

			$allowed_ans_drop = "Yes";

			}else{

			$address_data['CheckAddress'] = $address_data['PickUpAddress'];

			//$allowed_ans = checkRestrictedArea($address_data,"No");

			$source_array = $sourceLocationArr;

			//$allowed_ans = checkRestrictedAreaNew($source_array,"No");

      $allowed_ans = checkAllowedAreaNew($source_array,"No");

			$address_data['CheckAddress'] = $address_data['DropOffAddress'];

			//$allowed_ans_drop = checkRestrictedArea($address_data,"Yes");

			$dest_array = $destinationLocationArr;

			//$allowed_ans_drop = checkRestrictedAreaNew($dest_array,"Yes");

      $allowed_ans_drop = checkAllowedAreaNew($dest_array,"Yes");

		}

    $ssql_available = "";

    if($Check_Driver_UFX == "No"){

      $ssql_available .= " AND vAvailability = 'Available' AND vTripStatus != 'Active' AND tLocationUpdateDate > '$str_date' ";

    }

		

		if($allowed_ans == 'Yes' && $allowed_ans_drop == 'Yes') {

 		$sql = "SELECT ROUND(( 6371 * acos( cos( radians(" . $sourceLat . ") )

			* cos( radians( ROUND(".$vLatitude.",8) ) )

			* cos( radians( ROUND(".$vLongitude.",8) ) - radians(" . $sourceLon . ") )

			+ sin( radians(" . $sourceLat . ") )

			* sin( radians( ROUND(".$vLatitude.",8) ) ) ) ),2) AS distance, concat('+',register_driver.vCode,register_driver.vPhone) as vPhonenumber, register_driver.*  FROM `register_driver`

			WHERE (".$vLatitude." != '' AND ".$vLongitude." != '' $ssql_available AND eStatus='active')

			HAVING distance < " . $LIST_DRIVER_LIMIT_BY_DISTANCE . " ORDER BY `register_driver`.`" . $param . "` ASC";

			

			$Data = $obj->MySQLSelect($sql);

			

      $newData = array();

      $j=0;

			for($i=0;$i<count($Data);$i++){

				$Data[$i]['vPhone'] = $Data[$i]['vPhonenumber'];

				

				if($COMMISION_DEDUCT_ENABLE == 'Yes') {

					$user_available_balance = $generalobj->get_user_available_balance($Data[$i]['iDriverId'],"Driver");

					if($WALLET_MIN_BALANCE > $user_available_balance){

						$Data[$i]['ACCEPT_CASH_TRIPS'] = "No";

						}else{

						$Data[$i]['ACCEPT_CASH_TRIPS'] = "Yes";

					}

					}else{

					$Data[$i]['ACCEPT_CASH_TRIPS'] = "Yes";

				}

				//if($APP_TYPE == "UberX"){

        if($eType == "UberX"){

					$eUnit = getMemberCountryUnit($Data[$i]['iDriverId'],"Driver");

					/*if($Data[$i]['vWorkLocationRadius'] == "" || $Data[$i]['vWorkLocationRadius'] == "0" || $Data[$i]['vWorkLocationRadius'] == 0){

						$Data[$i]['vWorkLocationRadius'] = $vWorkLocationRadius;

					}else{

						if($eUnit == "Miles"){

							$Data[$i]['vWorkLocationRadius'] = round($Data[$i]['vWorkLocationRadius'] * 1.60934,2);  

						}

					}  */

          if($Data[$i]['eSelectWorkLocation'] == "Fixed" && $Data[$i]['vWorkLocationLatitude'] != "" && $Data[$i]['vWorkLocationLongitude'] != "" && $PROVIDER_AVAIL_LOC_CUSTOMIZE == "Yes"){

             $Data[$i]['vLatitude'] = $Data[$i]['vWorkLocationLatitude'];

             $Data[$i]['vLongitude'] = $Data[$i]['vWorkLocationLongitude'];

          }

         	$Data[$i]['PROVIDER_RADIUS'] = $Data[$i]['vWorkLocationRadius'];

          $sqlcount = "SELECT count(iRatingId) as TotalReview FROM ratings_user_driver as r LEFT JOIN trips as t ON r.iTripId=t.iTripId WHERE r.eUserType='Passenger' And t.iActive = 'Finished' AND t.iDriverId =  '".$Data[$i]['iDriverId']."'";

          $dbcount = $obj->MySQLSelect($sqlcount);

          $Data[$i]['PROVIDER_RATING_COUNT'] = $dbcount[0]['TotalReview'];

				}

        if($Check_Driver_UFX == "Yes"){

           //$currentdate = date("Y-m-d H:i:s");

           //$Check_Date_Time = date("Y-m-d H:i:s"); 

           $systemTimeZone = date_default_timezone_get();

           $Booking_Date_Time = converToTz($Check_Date_Time,$systemTimeZone,$vTimeZone);

           

           $Checkday = date('l', strtotime($Check_Date_Time));

           $hours = date('H', strtotime($Check_Date_Time));

           $hr1 = $hours; 

           if($hours == "12" || $hours == "00"){

             $hr1 = "12";

             $hr2 = "01"; 

           }else{

             $hr2 = $hr1+1;

             $hr1 = str_pad($hr1, 2, '0', STR_PAD_LEFT);

             $hr2 = str_pad($hr2, 2, '0', STR_PAD_LEFT);

           }

           $CheckHour = $hr1."-".$hr2;

           $sql = "SELECT * from driver_manage_timing WHERE iDriverId ='".$Data[$i]['iDriverId']."' AND vDay = '".$Checkday."' AND vAvailableTimes LIKE '%".$CheckHour."%'";

			     $availdriver = $obj->MySQLSelect($sql);

           

           if(count($availdriver) > 0){ 

             

            $sql_book = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='".$Data[$i]['iDriverId']."' AND dBooking_date = '".$Booking_Date_Time."' AND eStatus IN('Assign','Accepted')";

			      $availdriverbooking = $obj->MySQLSelect($sql_book);

    

            if(count($availdriverbooking) == 0){

                $newData[$j] = $Data[$i]; 

                $j++;

            }

           }   

        }else{

           $newData[$j] = $Data[$i]; 

           $j++;

        }

			}

			

			//$returnData['DriverList'] = $Data;

      $returnData['DriverList'] = $newData;

			$returnData['PickUpDisAllowed'] = $allowed_ans;

			$returnData['DropOffDisAllowed'] = $allowed_ans_drop;

			}else {

			/*$Data = array();

			$returnData['DriverList'] = $Data;*/

      $newData = array();

      $returnData['DriverList'] = $newData;

			$returnData['PickUpDisAllowed'] = $allowed_ans;

			$returnData['DropOffDisAllowed'] = $allowed_ans_drop;

		}

		

		return $returnData;

	}

	

	function checkRestrictedArea($address_data,$DropOff){

		global $generalobj, $obj;

		$ssql = "";

		if($DropOff == "No"){

			$ssql.= " AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";

			}else{

			$ssql.= " AND (eRestrictType = 'Drop Off' OR eRestrictType = 'All')";

		}

		if(!empty($address_data)){

			$pickaddrress = strtolower($address_data['CheckAddress']);

			$pickaddrress = preg_replace('/\d/', '', $pickaddrress);

			$pickaddrress = preg_replace('/\s+/', '', $pickaddrress); 

			//$pickArr = explode(',',$pickaddrress);

			$pickArr = array_map('trim',array_filter(explode(',',$pickaddrress)));

			$sqlaa = "SELECT cr.vCountry,ct.vCity,st.vState,replace(rs.vAddress, ' ','') as vAddress FROM `restricted_negative_area` AS rs

			LEFT JOIN country as cr ON cr.iCountryId = rs.iCountryId

			LEFT JOIN state as st ON st.iStateId = rs.iStateId

			LEFT JOIN city as ct ON ct.iCityId = rs.iCityId

			WHERE eType='Allowed'".$ssql;

			$allowed_data = $obj->MySQLSelect($sqlaa);

			$allowed_ans = 'No';

			if(!empty($allowed_data)){

				foreach($allowed_data as $rds){

					$alwd_country = $alwd_state = $alwd_city = $alwd_address = 'allowed';

					if($rds['vCountry'] != ""){

						//if($rds['vCountry'] == $address_data['countryId']){

						if(in_array(strtolower($rds['vCountry']),$pickArr)){

							$alwd_country = 'allowed';

							}else {

							$alwd_country = 'Disallowed';

						}

					}

					if($rds['vState'] != ""){

						if(in_array(strtolower($rds['vState']),$pickArr)){

							$alwd_state = 'allowed';

							}else {

							$alwd_state = 'Disallowed';

						}

					}

					if($rds['vCity'] != ""){

						if(in_array(strtolower($rds['vCity']),$pickArr)){

							$alwd_city = 'allowed';

							}else{

							$alwd_city = 'Disallowed';

						}

					}

					if($rds['vAddress'] != ""){

						if(strstr(strtolower($pickaddrress), strtolower($rds['vAddress']))){

							$alwd_address = 'allowed';

							}else{

							$alwd_address = 'Disallowed';

						}

					}

					if($alwd_country == 'allowed' && $alwd_state == 'allowed' && $alwd_city == 'allowed' && $alwd_address == 'allowed'){

						$allowed_ans = 'Yes';

						break;

					}

				}

			}    

			

			if($allowed_ans == 'No') {

				//$sqlas = "SELECT * FROM `restricted_negative_area` WHERE (iCountryId='".$address_data['countryId']."' OR iStateId='".$address_data['stateId']."' OR iCityId='".$address_data['cityId']."') AND eType='Disallowed' AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";

				$sqlas = "SELECT cr.vCountry,ct.vCity,st.vState,replace(rs.vAddress, ' ','') as vAddress FROM `restricted_negative_area` AS rs

				LEFT JOIN country as cr ON cr.iCountryId = rs.iCountryId

                LEFT JOIN state as st ON st.iStateId = rs.iStateId

                LEFT JOIN city as ct ON ct.iCityId = rs.iCityId

				WHERE eType='Disallowed'".$ssql;

				$restricted_data = $obj->MySQLSelect($sqlas);

				$allowed_ans = 'Yes';

				if(!empty($restricted_data)){

					foreach($restricted_data as $rds){

						$alwd_country = $alwd_state = $alwd_city = $alwd_address = 'Disallowed';   

						if($rds['vCountry'] != ""){                                               

							if(in_array(strtolower($rds['vCountry']),$pickArr)){

								$alwd_country = 'Disallowed';

								}else {

								$alwd_country = 'allowed';

							}

						}

						if($rds['vState'] != ""){

							if(in_array(strtolower($rds['vState']),$pickArr)){

								$alwd_state = 'Disallowed';

								}else {

								$alwd_state = 'allowed';

							}  

						}

						if($rds['vCity'] != ""){

							if(in_array(strtolower($rds['vCity']),$pickArr)){

								$alwd_city = 'Disallowed';

								}else{

								$alwd_city = 'allowed';

							}   

						}

						if($rds['vAddress'] != ""){      

							if(strstr(strtolower($pickaddrress), strtolower($rds['vAddress']))){

								$alwd_address = 'Disallowed';

								}else{

								$alwd_address = 'allowed';

							} 

						}

						if($alwd_country == 'Disallowed' && $alwd_state == 'Disallowed' && $alwd_city == 'Disallowed' && $alwd_address == "Disallowed"){

							$allowed_ans = 'No';

							break;

						}

					}  

				}

			}

		}

		return $allowed_ans;  

	}

	

	function getAddressFromLocation($latitude, $longitude, $Google_Server_key) {

		$location_Address = "";

		

		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latitude . "," . $longitude . "&key=" . $Google_Server_key;

		

		try {

			

			$jsonfile = file_get_contents($url);

			$jsondata = json_decode($jsonfile);

			$address = $jsondata->results[0]->formatted_address;

			

			$location_Address = $address;

			} catch (ErrorException $ex) {

			

			$returnArr['Action'] = "0";

			echo json_encode($returnArr);

			exit;

			// echo 'Site not reachable (' . $ex->getMessage() . ')';

		}

		

		if ($location_Address == "") {

			$returnArr['Action'] = "0";

			echo json_encode($returnArr);

			exit;

		}

		

		return $location_Address;

	}

	

	function getLanguageTitle($vLangCode) {

		global $obj;

		

		$sql = "SELECT vTitle FROM language_master WHERE vCode = '" . $vLangCode . "' ";

		$db_title = $obj->MySQLSelect($sql);

		

		return $db_title[0]['vTitle'];

	}

	

	function checkSurgePrice($vehicleTypeID, $selectedDateTime = "",$iRentalPackageId = "0") {

		$ePickStatus = get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

		$eNightStatus = get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

		

		$fPickUpPrice = 1;

		$fNightPrice = 1;

		

		if ($selectedDateTime == "") {

			// $currentTime = @date("Y-m-d H:i:s");

			$currentTime = @date("H:i:s");

			$currentDay = @date("D");

			} else {

			// $currentTime = $selectedDateTime;

			$currentTime = @date("H:i:s", strtotime($selectedDateTime));

			$currentDay = @date("D", strtotime($selectedDateTime));

		}

		

		/* added for rental */

		if (($ePickStatus == "Active" || $eNightStatus == "Active") && $iRentalPackageId == 0) {

			

			$startTime_str = "t" . $currentDay . "PickStartTime";

			$endTime_str = "t" . $currentDay . "PickEndTime";

			$price_str = "f" . $currentDay . "PickUpPrice";

			

			$pickStartTime = get_value('vehicle_type', $startTime_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$pickEndTime = get_value('vehicle_type', $endTime_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$fPickUpPrice = get_value('vehicle_type', $price_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			

			$nightStartTime = get_value('vehicle_type', 'tNightStartTime', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$nightEndTime = get_value('vehicle_type', 'tNightEndTime', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			$fNightPrice = get_value('vehicle_type', 'fNightPrice', 'iVehicleTypeId', $vehicleTypeID, '', 'true');

			

			$tempNightHour = "12:00:00";

			if ($currentTime > $pickStartTime && $currentTime < $pickEndTime && $ePickStatus == "Active") {

				     

				$returnArr['Action'] = "0";

				$returnArr['message'] = "LBL_PICK_SURGE_NOTE";

				$returnArr['SurgePrice'] = $fPickUpPrice . "X";

				$returnArr['SurgePriceValue'] = $fPickUpPrice;

			} 

			// else if ($currentTime > $nightStartTime && $currentTime < $nightEndTime && $eNightStatus == "Active") {

			else if((($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime > $tempNightHour) || ($currentTime <$nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime > $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour)) && $eNightStatus == "Active"){

				

				$returnArr['Action'] = "0";

				$returnArr['message'] = "LBL_NIGHT_SURGE_NOTE";

				$returnArr['SurgePrice'] = $fNightPrice . "X";

				$returnArr['SurgePriceValue'] = $fNightPrice;

				} else {

				$returnArr['Action'] = "1";

			}

			} else {

			$returnArr['Action'] = "1";

		}

		

		return $returnArr;

	}

	

	function check_email_send($iDriverId, $tablename, $field) {

		global $obj, $generalobj;

		$sql = "SELECT * FROM " . $tablename . " WHERE " . $field . "= '" . $iDriverId . "'";

		$db_data = $obj->MySQLSelect($sql);

		//print_r($db_data);//exit;

		//$valid=0;

		if ($tablename == 'register_driver') {

			//echo "hi";exit;

			if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vLicence'] != NULL && $db_data[0]['vCerti'] != NULL) {

				//global $generalobj;

				$maildata['USER'] = "Driver";

				$maildata['NAME'] = $db_data[0]['vName'];

				$maildata['EMAIL'] = $db_data[0]['vEmail'];

				$generalobj->send_email_user("PROFILE_UPLOAD", $maildata);

				//header("location:profile.php?success=1&var_msg=" . $var_msg);

				//return;

			}

			} else {

			if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vCerti'] != NULL) {

				$maildata['USER'] = "Company";

				$maildata['NAME'] = $db_data[0]['vName'];

				$maildata['EMAIL'] = $db_data[0]['vEmail'];

				//var_dump($maildata);

				//var_dump(($generalobj));

				$generalobj->send_email_user("PROFILE_UPLOAD", $maildata);

			}

		}

		return true;

	}

	

	function checkmemberemailphoneverification($iMemberId, $user_type = "Passenger") {

		global $obj,$DRIVER_EMAIL_VERIFICATION,$DRIVER_PHONE_VERIFICATION,$RIDER_EMAIL_VERIFICATION,$RIDER_PHONE_VERIFICATION;

		if ($user_type == "Driver") {

			/*$EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_EMAIL_VERIFICATION', '', 'true');

			$PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_PHONE_VERIFICATION', '', 'true'); 

      $eEmailVerified = get_value('register_driver', 'eEmailVerified', 'iDriverId', $iMemberId, '', 'true');

			$ePhoneVerified = get_value('register_driver', 'ePhoneVerified', 'iDriverId', $iMemberId, '', 'true');*/

      $EMAIL_VERIFICATION = $DRIVER_EMAIL_VERIFICATION;

			$PHONE_VERIFICATION = $DRIVER_PHONE_VERIFICATION;

			$sqld = "SELECT eEmailVerified,ePhoneVerified FROM register_driver WHERE iDriverId = '".$iMemberId."'";

      $driverData = $obj->MySQLSelect($sqld);

      $eEmailVerified = $driverData[0]['eEmailVerified'];

      $ePhoneVerified = $driverData[0]['ePhoneVerified']; 

			} else {

			/*$EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_EMAIL_VERIFICATION', '', 'true');

			$PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_PHONE_VERIFICATION', '', 'true');

      $eEmailVerified = get_value('register_user', 'eEmailVerified', 'iUserId', $iMemberId, '', 'true');

			$ePhoneVerified = get_value('register_user', 'ePhoneVerified', 'iUserId', $iMemberId, '', 'true');*/

      $EMAIL_VERIFICATION = $RIDER_EMAIL_VERIFICATION;

			$PHONE_VERIFICATION = $RIDER_PHONE_VERIFICATION;

      $sqld = "SELECT eEmailVerified,ePhoneVerified FROM register_user WHERE iUserId = '".$iMemberId."'";

      $driverData = $obj->MySQLSelect($sqld);

      $eEmailVerified = $driverData[0]['eEmailVerified'];

      $ePhoneVerified = $driverData[0]['ePhoneVerified'];

		}

		

		$email = $EMAIL_VERIFICATION == "Yes" ? ($eEmailVerified == "Yes" ? "true" : "false") : "true";

		$phone = $PHONE_VERIFICATION == "Yes" ? ($ePhoneVerified == "Yes" ? "true" : "false") : "true";

		

		if ($email == "false" && $phone == "false") {

			$returnArr['Action'] = "0";

			$returnArr['message'] = "DO_EMAIL_PHONE_VERIFY";

			echo json_encode($returnArr);

			exit;

			} else if ($email == "true" && $phone == "false") {

			$returnArr['Action'] = "0";

			$returnArr['message'] = "DO_PHONE_VERIFY";

			echo json_encode($returnArr);

			exit;

			} else if ($email == "false" && $phone == "true") {

			$returnArr['Action'] = "0";

			$returnArr['message'] = "DO_EMAIL_VERIFY";

			echo json_encode($returnArr);

			exit;

		}

	}

	

	function sendemailphoneverificationcode($iMemberId, $user_type = "Passenger", $VerifyType) {

		global $generalobj, $obj;

		if ($user_type == "Passenger") {

			$tblname = "register_user";

			$fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';

			$condfield = 'iUserId';

			$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');

			} else {

			$tblname = "register_driver";

			$fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';

			$condfield = 'iDriverId';

			$vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');

		}

		if ($vLangCode == "" || $vLangCode == NULL) {

			$vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

		}

		$languageLabelsArr = getLanguageLabelsArr($vLangCode, "1");



		$str = "select * from send_message_templates where vEmail_Code='VERIFICATION_CODE_MESSAGE'";

        $res = $obj->MySQLSelect($str);

		$prefix = $res[0]['vBody_' . $vLangCode];



		//$prefix = $languageLabelsArr['LBL_VERIFICATION_CODE_TXT'];

		

		$emailmessage = "";

		$phonemessage = "";

		if ($VerifyType == "email" || $VerifyType == "both") {

			$sql = "select $fields from $tblname where $condfield = '" . $iMemberId . "'";

			$db_member = $obj->MySQLSelect($sql);

			

			$Data_Mail['vEmailVarificationCode'] = $random = substr(number_format(time() * rand(), 0, '', ''), 0, 4);

			$Data_Mail['vEmail'] = isset($db_member[0]['vEmail']) ? $db_member[0]['vEmail'] : '';

			$vFirstName = isset($db_member[0]['vName']) ? $db_member[0]['vName'] : '';

			$vLastName = isset($db_member[0]['vLastName']) ? $db_member[0]['vLastName'] : '';

			$Data_Mail['vName'] = $vFirstName . " " . $vLastName;

			$Data_Mail['CODE'] = $Data_Mail['vEmailVarificationCode'];

			

			$sendemail = $generalobj->send_email_user("APP_EMAIL_VERIFICATION_USER", $Data_Mail);

			if ($sendemail) {

				$emailmessage = $Data_Mail['vEmailVarificationCode'];

				} else {

				$emailmessage = "LBL_EMAIL_VERIFICATION_FAILED_TXT";

			}

		}

		

		if ($VerifyType == "phone" || $VerifyType == "both") {

			$sql = "select $fields from $tblname where $condfield = '" . $iMemberId . "'";

			$db_member = $obj->MySQLSelect($sql);

			

			$mobileNo = $db_member[0]['vPhoneCode'] . $db_member[0]['vPhone'];

			$toMobileNum = "+" . $mobileNo;

			$verificationCode = mt_rand(1000, 9999);

			$message = $prefix . ' ' . $verificationCode;

			$result = sendEmeSms($toMobileNum, $message);

			if ($result == 0) {

				$phonemessage = "LBL_MOBILE_VERIFICATION_FAILED_TXT";

				} else {

				$phonemessage = $verificationCode;

			}

		}

		

		$returnArr['emailmessage'] = $emailmessage;

		$returnArr['phonemessage'] = $phonemessage;

		return $returnArr;

	}

	

	function getTripPriceDetails($iTripId, $iMemberId, $eUserType = "Passenger", $PAGE_MODE = "HISTORY") {

		global $obj, $generalobj, $tconfig;

		$returnArr = array();

		if ($eUserType == "Passenger") {

			$tblname = "register_user";

			$vLang = "vLang";

			$iUserId = "iUserId";

			$vCurrency = "vCurrencyPassenger";

			

			//$currencycode = get_value("trips", $vCurrency, "iTripId", $iTripId, '', 'true');

      $sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iMemberId."'";

      $passengerData = $obj->MySQLSelect($sqlp);

      $currencycode = $passengerData[0]['vCurrencyPassenger'];

      $userlangcode = $passengerData[0]['vLang'];

      $currencySymbol = $passengerData[0]['vSymbol'] . ' '; 

		} else {

			$tblname = "register_driver";

			$vLang = "vLang";

			$iUserId = "iDriverId";

			$vCurrency = "vCurrencyDriver";

			

			//$currencycode = get_value($tblname, $vCurrency, $iUserId, $iMemberId, '', 'true');

      $sqld = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$iMemberId."'";

      $driverData = $obj->MySQLSelect($sqld);

      $currencycode = $driverData[0]['vCurrencyDriver'];

      $userlangcode = $driverData[0]['vLang'];

      $currencySymbol = $driverData[0]['vSymbol'] . ' ';

		}

		//$userlangcode = get_value($tblname, $vLang, $iUserId, $iMemberId, '', 'true');

		if ($userlangcode == "" || $userlangcode == NULL) {

			$userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

		}

		$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");

		if($currencycode == "" || $currencycode == NULL) {

			$sql = "SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'";

		  $currencyData = $obj->MySQLSelect($sql);

      $currencycode = $currencyData[0]['vName'];

      $currencySymbol = $currencyData[0]['vSymbol'] . ' ';

		}

		

		//$sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";                                                                      

    $sql = "SELECT tr.*,vt.vVehicleType_".$userlangcode." as vVehicleType,vt.vRentalAlias_".$userlangcode." as vRentalVehicleTypeName,vt.vLogo,vt.iVehicleCategoryId,vt.iCancellationTimeLimit,vt.fFixedFare,vt.eIconType,COALESCE(vc.iParentId, '0') as iParentId,COALESCE(vc.ePriceType, '') as ePriceType,COALESCE(vc.vLogo, '') as vLogoVehicleCategory,COALESCE(vc.vCategory_".$userlangcode.", '') as vCategory from trips as tr LEFT JOIN  vehicle_type as vt ON tr.iVehicleTypeId = vt.iVehicleTypeId  LEFT JOIN vehicle_category as vc ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE tr.iTripId = '" . $iTripId . "'";

		$tripData = $obj->MySQLSelect($sql); 

		$priceRatio = $tripData[0]['fRatio_' . $currencycode];

		$iActive = $tripData[0]['iActive'];

		// Convert Into Timezone

		$tripTimeZone = $tripData[0]['vTimeZone'];

		if($tripTimeZone != ""){

			$serverTimeZone = date_default_timezone_get();

			$tripData[0]['tTripRequestDate'] = converToTz($tripData[0]['tTripRequestDate'],$tripTimeZone,$serverTimeZone);

			$tripData[0]['tDriverArrivedDate'] = converToTz($tripData[0]['tDriverArrivedDate'],$tripTimeZone,$serverTimeZone);

      if($tripData[0]['tStartDate'] != "0000-00-00 00:00:00"){

			$tripData[0]['tStartDate'] = converToTz($tripData[0]['tStartDate'],$tripTimeZone,$serverTimeZone);

      }

			$tripData[0]['tEndDate'] = converToTz($tripData[0]['tEndDate'],$tripTimeZone,$serverTimeZone);

		}

		// Convert Into Timezone

		

		$returnArr = array_merge($tripData[0], $returnArr);

		if ($tripData[0]['iUserPetId'] > 0) {

			$petDetails_arr = get_value('user_pets', 'iPetTypeId,vTitle as PetName,vWeight as PetWeight, tBreed as PetBreed, tDescription as PetDescription', 'iUserPetId', $tripData[0]['iUserPetId'], '', '');

		} else {

			$petDetails_arr = array();

		}

    	/*Added For REntal*/

    	$iRentalPackageId = $tripData[0]['iRentalPackageId'];

    	if($iRentalPackageId > 0){

	       $returnArr['eRental'] = "Yes";

	    } else {

	    	$returnArr['eRental'] = "";

	    }

	    /*Added For REntal*/

    

    $iPackageTypeId = $tripData[0]['iPackageTypeId'];

    if($iPackageTypeId != 0){

    	$sqlnew = "SELECT vName_".$userlangcode." as vName FROM package_type WHERE iPackageTypeId='".$iPackageTypeId."'";

		$pkgdata = $obj->MySQLSelect($sqlnew);

		$returnArr['PackageType'] = $pkgdata[0]['vName'];

       	//$returnArr['PackageType'] = get_value('package_type', 'vName', 'iPackageTypeId', $iPackageTypeId, '', 'true');

    }

		

		if (count($petDetails_arr) > 0) {

			$petTypeName = get_value('pet_type', 'vTitle_' . $userlangcode, 'iPetTypeId', $petDetails_arr[0]['iPetTypeId'], '', 'true');

			$returnArr['PetDetails']['PetName'] = $petDetails_arr[0]['PetName'];

			$returnArr['PetDetails']['PetWeight'] = $petDetails_arr[0]['PetWeight'];

			$returnArr['PetDetails']['PetBreed'] = $petDetails_arr[0]['PetBreed'];

			$returnArr['PetDetails']['PetDescription'] = $petDetails_arr[0]['PetDescription'];

			$returnArr['PetDetails']['PetTypeName'] = $petTypeName;

			} else {

			$returnArr['PetDetails']['PetName'] = '';

			$returnArr['PetDetails']['PetWeight'] = '';

			$returnArr['PetDetails']['PetBreed'] = '';

			$returnArr['PetDetails']['PetDescription'] = '';

			$returnArr['PetDetails']['PetTypeName'] = '';

		}

		

		/* User Wallet Information */

		$returnArr['UserDebitAmount'] = strval($tripData[0]['fWalletDebit']);

		/* User Wallet Information */

		

		/*$vVehicleType = get_value('vehicle_type', "vVehicleType_" . $userlangcode, 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');

		$vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');

		$iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');

		$vVehicleCategoryData = get_value('vehicle_category', 'iParentId,ePriceType,vLogo,vCategory_' . $userlangcode . ' as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);

		$vVehicleFare = get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');                    

    $iParentId = $vVehicleCategoryData[0]['iParentId'];*/

    $vVehicleType = $tripData[0]['vVehicleType'];

    $vRentalVehicleTypeName = $tripData[0]['vRentalVehicleTypeName'];

    $vVehicleTypeLogo = $tripData[0]['vLogo'];   

    $iVehicleCategoryId = $tripData[0]['iVehicleCategoryId'];  

    $vVehicleCategoryData[0]['vLogo'] = $tripData[0]['vLogoVehicleCategory']; 

    $vVehicleCategoryData[0]['vCategory'] = $tripData[0]['vCategory'];        

    $vVehicleFare = $tripData[0]['fFixedFare'];         

    $iParentId = $tripData[0]['iParentId'];                                   

    if($iParentId == 0){

    $ePriceType = $tripData[0]['ePriceType']; 

    }else{ 

    $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 

    }    

    //$eIconType = get_value('vehicle_type', "eIconType", 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');

    $eIconType = $tripData[0]['eIconType'];

		

		$TripTime = date('h:iA', strtotime($tripData[0]['tTripRequestDate']));

		$tTripRequestDateOrig = $tripData[0]['tTripRequestDate'];

		

		// Convert Into Timezone

		// $tripTimeZone = $tripData[0]['vTimeZone'];

		// if($tripTimeZone != ""){

		// $serverTimeZone = date_default_timezone_get();

		// $tTripRequestDateOrig = converToTz($tTripRequestDateOrig,$tripTimeZone,$serverTimeZone);

		// }

		

		// Convert Into Timezone

		$tTripRequestDate = date('dS M Y \a\t h:i a', strtotime($tripData[0]['tTripRequestDate']));

		$tStartDate = $tripData[0]['tStartDate'];

		$tEndDate = $tripData[0]['tEndDate'];

    $tDriverArrivedDate = $tripData[0]['tDriverArrivedDate'];

    $iCancellationTimeLimit = $tripData[0]['iCancellationTimeLimit']; 

    ## Checking Minutes For Waiting Fee ##

    $Vehicle_WaitingFeeTimeLimit = $tripData[0]['iWaitingFeeTimeLimit']; 

    $Vehicle_WaitingFeeTimeLimit = $Vehicle_WaitingFeeTimeLimit * 60;

    $waiting_time_diff = strtotime($tStartDate) - strtotime($tDriverArrivedDate) - $Vehicle_WaitingFeeTimeLimit;

    $waitingTime = ceil($waiting_time_diff / 60);

    //$waitingTime = $waitingTime - $iCancellationTimeLimit;  

    if($waitingTime > 1){

     $waitingTime = $waitingTime. " " . $languageLabelsArr['LBL_MINUTES_TXT'];

    }else{

     $waitingTime = $waitingTime. " " . $languageLabelsArr['LBL_MINUTE'];

    }

    

    ## Checking Minutes For Waiting Fee ## 

		$totalTime = 0;

		if($tStartDate != '' && $tStartDate != '0000-00-00 00:00:00' && $tEndDate != '' && $tEndDate != '0000-00-00 00:00:00'){

			if ($tripData[0]['eFareType'] == "Hourly") {

				// $hours 		=	0; 

				// $minutes 	=	0;

				$totalSec 	=	0;

				$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";

				$db_tripTimes = $obj->MySQLSelect($sql22);

				

				foreach($db_tripTimes as $dtT){

					if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {

						$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);

					}

				}

				

				$years = floor($totalSec / (365*60*60*24)); $months = floor(($totalSec - $years * 365*60*60*24) / (30*60*60*24));

				$days = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

				$hours = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));

				$minuts = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);

				$seconds = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

				

				if ($days > 0){

					$hours = ($days * 24) + $hours;

				}

				if ($hours > 0) {

					$totalTime = $hours.':'.$minuts.':'.$seconds;

				}else if ($minuts > 0) {

					$totalTime = $minuts.':'.$seconds. " " . $languageLabelsArr['LBL_MINUTES_TXT'];

				}

				if ($totalTime < 1) {

					$totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];

				}

				}else {

				$days = dateDifference($tStartDate, $tEndDate,'%a');

				$hours = dateDifference($tStartDate, $tEndDate, '%h');

				$minutes = dateDifference($tStartDate, $tEndDate, '%i');

				$seconds = dateDifference($tStartDate, $tEndDate, '%s');

        $LBL_HOURS_TXT = ($hours > 1)? $languageLabelsArr['LBL_HOURS_TXT'] : $languageLabelsArr['LBL_HOUR_TXT'];

        $LBL_MINUTES_TXT = ($minutes > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];

        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);

        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

	      if ($days > 0){

					$hours = ($days * 24) + $hours;

				}

				  if ($hours > 0) {

					//$totalTime = $hours * 60;

					//$totalTime = $hours.':'.$minutes.':'.$seconds." " .$languageLabelsArr['LBL_HOUR'] ;

          $totalTime = $hours.':'.$minutes.':'.$seconds." " .$LBL_HOURS_TXT ;

					}else if ($minutes > 0) {

					//$totalTime = $totalTime + $minutes;

					//$totalTime = $minutes.':'.$seconds. " " . $languageLabelsArr['LBL_MINUTES_TXT'];

          $totalTime = $minutes.':'.$seconds. " " . $LBL_MINUTES_TXT;

				  }

				//$totalTime = $totalTime . ":" . $seconds . " " . $languageLabelsArr['LBL_MINUTES_TXT'];

				if ($totalTime < 1) {

					$totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];

				}

			}

		}

    if($iActive == "Canceled"){

       $totalTime = 0;

    }

    if($totalTime == 0){

       $totalTime = "0.00 ".$languageLabelsArr['LBL_MINUTE'];

    }

    	if($tripData[0]['iRentalPackageId'] > 0){

			$returnArr['carTypeName'] = $vRentalVehicleTypeName;

    	} else {

		$returnArr['carTypeName'] = $vVehicleType;

    	}

		$returnArr['carImageLogo'] = $vVehicleTypeLogo;

		if ($eUserType == "Passenger") {

			$TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Driver"', 'true');

			$returnArr['vDriverImage'] = get_value('register_driver', 'vImage', 'iTripId', $tripData[0]['iDriverId'], '', 'true');

			//$driverDetailArr = get_value('register_driver', '*', 'iDriverId', $tripData[0]['iDriverId']);

			$eUnit = $tripData[0]['vCountryUnitRider'];

		} else {

			$TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Passenger"', 'true');

			//$passgengerDetailArr = get_value('register_user', '*', 'iUserId', $tripData[0]['iUserId']);

			$eUnit = $tripData[0]['vCountryUnitDriver'];

			//$eUnit = $tripData[0]['vCountryUnitRider'];

		}

		

		if($eUnit == "Miles"){

			$DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT']; 

			}else{

			$DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];

		}

		

		if ($TripRating == "" || $TripRating == NULL) {

			$TripRating = "0";

		}

		

		$iFare = $tripData[0]['iFare'];

		//$iFare = $tripData[0]['iFare']+$tripData[0]['fTollPrice'];

		$fPricePerKM = $tripData[0]['fPricePerKM'] * $priceRatio;

		$iBaseFare = $tripData[0]['iBaseFare'] * $priceRatio;

		$fPricePerMin = $tripData[0]['fPricePerMin'] * $priceRatio;

		$fCommision = $tripData[0]['fCommision'];

		$fDistance = $tripData[0]['fDistance'];

		if($eUnit == "Miles"){

			$fDistance = round($fDistance * 0.621371,2);  

		}

		$vDiscount = $tripData[0]['vDiscount']; // 50 $

		$fDiscount = $tripData[0]['fDiscount']; // 50

		$fMinFareDiff = $tripData[0]['fMinFareDiff'] * $priceRatio;

		$fWalletDebit = $tripData[0]['fWalletDebit'];

		$fSurgePriceDiff = $tripData[0]['fSurgePriceDiff'] * $priceRatio;

		$fTripGenerateFare = $tripData[0]['fTripGenerateFare'] * $priceRatio;

		$fPickUpPrice = $tripData[0]['fPickUpPrice'];

		$fNightPrice = $tripData[0]['fNightPrice'];

    $eFlatTrip = $tripData[0]['eFlatTrip']; 

    $fFlatTripPrice = $tripData[0]['fFlatTripPrice'] * $priceRatio;

		$fTipPrice = $tripData[0]['fTipPrice'] * $priceRatio;

		$fVisitFee = $tripData[0]['fVisitFee'] * $priceRatio;

		$fMaterialFee = $tripData[0]['fMaterialFee'] * $priceRatio;

		$fMiscFee = $tripData[0]['fMiscFee'] * $priceRatio;

		$fDriverDiscount = $tripData[0]['fDriverDiscount'] * $priceRatio;

		$vVehicleFare = $vVehicleFare * $priceRatio;

		$fCancelPrice = $tripData[0]['fCancellationFare'] * $priceRatio;

    $fWaitingFees = $tripData[0]['fWaitingFees'] * $priceRatio;

		$fTollPrice = $tripData[0]['fTollPrice'] * $priceRatio;

    $fTax1 = $tripData[0]['fTax1'] * $priceRatio;

    $fTax2 = $tripData[0]['fTax2'] * $priceRatio;

    $fWaitingFees = $tripData[0]['fWaitingFees'] * $priceRatio;

    $fOutStandingAmount = $tripData[0]['fOutStandingAmount'] * $priceRatio;

		if($fTollPrice > 0){

			$eTollSkipped = $tripData[0]['eTollSkipped'];

			}else{

			$eTollSkipped = "Yes";

		}

    $tUserComment = $tripData[0]['tUserComment']; 

		

		$returnArr['tUserComment'] = $tUserComment;

		$returnArr['vVehicleType'] = $vVehicleType;

    $returnArr['eIconType'] = $eIconType;

		$returnArr['vVehicleCategory'] = $vVehicleCategoryData[0]['vCategory'];

		$returnArr['TripTime'] = $TripTime;

		$returnArr['ConvertedTripRequestDate'] = $tTripRequestDate;

		$returnArr['FormattedTripDate'] = $tTripRequestDate;

		$returnArr['tTripRequestDateOrig'] = $tTripRequestDateOrig;

		$returnArr['tTripRequestDate'] = $tTripRequestDate;

		$returnArr['TripTimeInMinutes'] = $totalTime;

		$returnArr['TripRating'] = $TripRating;

		$returnArr['CurrencySymbol'] = $currencySymbol;

		$returnArr['TripFare'] = formatNum($iFare * $priceRatio);

		$returnArr['iTripId'] = $tripData[0]['iTripId'];

		$returnArr['vTripPaymentMode'] = $tripData[0]['vTripPaymentMode'];

		$returnArr['eType'] = $tripData[0]['eType'];

    if($tripData[0]['eType'] == "UberX" && $tripData[0]['eFareType'] != "Regular"){

      $returnArr['tDaddress'] = "";

    }

		if ($tripData[0]['vBeforeImage'] != "") {

			$returnArr['vBeforeImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vBeforeImage'];

		}

		if ($tripData[0]['eType'] == "UberX") {

			$returnArr['vLogoVehicleCategoryPath'] = $tconfig['tsite_upload_images_vehicle_category'] . "/" . $iVehicleCategoryId . "/";

			$returnArr['vLogoVehicleCategory'] = $vVehicleCategoryData[0]['vLogo'];

			} else {

			$returnArr['vLogoVehicleCategory'] = "";

			$returnArr['vLogoVehicleCategoryPath'] = "";

		}

		if ($tripData[0]['vAfterImage'] != "") {

			$returnArr['vAfterImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vAfterImage'];

		}

		$originalFare = $iFare;

		if ($eUserType == "Passenger") {

			$iFare = $iFare;

		} else {

			//$iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision - $tripData[0]['fTax1'] - $tripData[0]['fTax2'];

      $iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision - $tripData[0]['fTax1'] - $tripData[0]['fTax2'] - $tripData[0]['fOutStandingAmount'];

      $iFare_Detail_Earning = $tripData[0]['fTripGenerateFare'] - $fCommision - $tripData[0]['fTax1'] - $tripData[0]['fTax2'] - $tripData[0]['fOutStandingAmount'];

		}

		$surgePrice = 1;

		if ($tripData[0]['fPickUpPrice'] > 1) {

			$surgePrice = $tripData[0]['fPickUpPrice'];

			} else {

			$surgePrice = $tripData[0]['fNightPrice'];

		}

		$SurgePriceFactor = strval($surgePrice);

		

		$returnArr['TripFareOfMinutes'] = formatNum($tripData[0]['fPricePerMin'] * $priceRatio);

		$returnArr['TripFareOfDistance'] = formatNum($tripData[0]['fPricePerKM'] * $priceRatio);

		$returnArr['iFare'] = formatNum($iFare * $priceRatio);

    $returnArr['iFare_Detail_Earning'] = formatNum($iFare_Detail_Earning * $priceRatio);

		$returnArr['iOriginalFare'] = formatNum($originalFare * $priceRatio);

		$returnArr['TotalFare'] = formatNum($iFare * $priceRatio);

		$returnArr['fPricePerKM'] = formatNum($fPricePerKM);

		$returnArr['iBaseFare'] = formatNum($iBaseFare);

		$returnArr['fPricePerMin'] = formatNum($fPricePerMin);

		$returnArr['fCommision'] = formatNum($fCommision * $priceRatio);

		$returnArr['fDistance'] = formatNum($fDistance);

		$returnArr['fDiscount'] = formatNum($fDiscount * $priceRatio);

		$returnArr['fMinFareDiff'] = formatNum($fMinFareDiff);

		$returnArr['fWalletDebit'] = formatNum($fWalletDebit * $priceRatio);

		$returnArr['fSurgePriceDiff'] = formatNum($fSurgePriceDiff);

		$returnArr['fTripGenerateFare'] = formatNum($fTripGenerateFare);


		$iAdminUserId = get_value('cab_booking', 'iAdminUserId', 'iTripId', $tripData[0]['iTripId'], '', 'true');

		// Ajuste de exibir valor pago corretamente ao motorista
		if ($iAdminUserId>0 && $returnArr['fTripGenerateFare']>0 && ($returnArr['fWalletDebit']<$returnArr['fTripGenerateFare']))
			$returnArr['fWalletDebit'] = $returnArr['fTripGenerateFare'];


    $returnArr['fFlatTripPrice'] = formatNum($fFlatTripPrice);

    $returnArr['fWaitingFees'] = formatNum($fWaitingFees);

    $returnArr['fOutStandingAmount'] = formatNum($fOutStandingAmount);

		if($eTollSkipped == "No"){

			$returnArr['fTollPrice'] = formatNum($fTollPrice);   

		}

		if($fTipPrice > 0){

			$returnArr['fTipPrice'] = $currencySymbol.formatNum($fTipPrice);

		}

		$returnArr['SurgePriceFactor'] = $SurgePriceFactor;

		$returnArr['fVisitFee'] = formatNum($fVisitFee);

		$returnArr['fMaterialFee'] = formatNum($fMaterialFee);

		$returnArr['fMiscFee'] = formatNum($fMiscFee);

		$returnArr['fDriverDiscount'] = formatNum($fDriverDiscount);

		$returnArr['fCancelPrice'] = formatNum($fCancelPrice);

    $returnArr['fWaitingFees'] = formatNum($fWaitingFees);

		$returnArr['fTax1'] = formatNum($fTax1);

    $returnArr['fTax2'] = formatNum($fTax2);

		// echo "<pre>"; print_r($tripData); die;

		

		$iDriverId = $tripData[0]['iDriverId'];

		$driverDetails = get_value('register_driver', '*', 'iDriverId', $iDriverId);

		$driverDetails[0]['vImage'] = ($driverDetails[0]['vImage'] != "" && $driverDetails[0]['vImage'] != "NONE") ? "3_" . $driverDetails[0]['vImage'] : "";

		$driverDetails[0]['vPhone'] = '+'.$driverDetails[0]['vCode'].$driverDetails[0]['vPhone'];

		$returnArr['DriverDetails'] = $driverDetails[0];

		

		$iUserId = $tripData[0]['iUserId'];

		$passengerDetails = get_value('register_user', '*', 'iUserId', $iUserId);

		$passengerDetails[0]['vImgName'] = ($passengerDetails[0]['vImgName'] != "" && $passengerDetails[0]['vImgName'] != "NONE") ? "3_" . $passengerDetails[0]['vImgName'] : "";

		$passengerDetails[0]['vPhone'] = '+'.$passengerDetails[0]['vPhoneCode'].$passengerDetails[0]['vPhone'];

		$returnArr['PassengerDetails'] = $passengerDetails[0];

    $TaxArr = getMemberCountryTax($iUserId,"Passenger");

    //$fUserCountryTax1 = $TaxArr['fTax1'];  

		//$fUserCountryTax2 = $TaxArr['fTax2'];

    $fUserCountryTax1 = $tripData[0]['fTax1Percentage'];  

		$fUserCountryTax2 = $tripData[0]['fTax2Percentage'];

		

		$iDriverVehicleId = $tripData[0]['iDriverVehicleId'];

		$sql = "SELECT make.vMake, model.vTitle, dv.*  FROM `driver_vehicle` dv, make, model WHERE dv.iDriverVehicleId='" . $iDriverVehicleId . "' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId`";

		$vehicleDetailsArr = $obj->MySQLSelect($sql);

		$vehicleDetailsArr[0]['vModel'] = $vehicleDetailsArr[0]['vTitle'];

		//if ($eUserType == "Passenger" && $tripData[0]['eType'] == "UberX") {

    if($tripData[0]['eType'] == "UberX") {

			

			//$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");

      $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";

			

			

			$fAmount = "0";

			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {

				

				

				$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $tripData[0]['iVehicleTypeId'] . "'";

				$serviceProData = $obj->MySQLSelect($sqlServicePro);

				

				$vehicleTypeData = get_value('vehicle_type', 'eFareType,fPricePerHour,fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId']);

				if ($vehicleTypeData[0]['eFareType'] == "Fixed") {

					$fAmount = $currencySymbol . $vehicleTypeData[0]['fFixedFare'];

					} else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {

					$fAmount = $currencySymbol . $vehicleTypeData[0]['fPricePerHour'] . "/hour";

				}

				

				if (count($serviceProData) > 0) {

					$fAmount = $serviceProData[0]['fAmount'];

          $vVehicleFare = $fAmount * $priceRatio;

          //$vVehicleFare = formatNum($vVehicleFare);

					if ($vehicleTypeData[0]['eFareType'] == "Fixed") {

						$fAmount = $currencySymbol . $fAmount;

						} else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {

						$fAmount = $currencySymbol . $fAmount . "/hour";

					}

				}

				

				$vehicleDetailsArr[0]['fAmount'] = strval($fAmount);

			}

		}

		$returnArr['DriverCarDetails'] = $vehicleDetailsArr[0];

    

    if($iActive == "Canceled" && $eUserType == "Driver"){

      $sql = "SELECT * from trip_outstanding_amount WHERE iTripId = '".$iTripId."'";

		  $tripCanceledData = $obj->MySQLSelect($sql);

      $fcancelCommision = $tripCanceledData[0]['fCommision']; 

      $fDriverPendingAmount = $tripCanceledData[0]['fDriverPendingAmount'];

      $ePaidByPassenger = $tripCanceledData[0]['ePaidByPassenger'];

      $ePaidToDriver = $tripCanceledData[0]['ePaidToDriver'];

      $returnArr['fCommision'] = formatNum($fcancelCommision * $priceRatio);

      $returnArr['iFare'] = formatNum($fDriverPendingAmount * $priceRatio); 

    }

		

		if ($eUserType == "Passenger") {

			$tripFareDetailsArr = array();

			if($eFlatTrip == "Yes" && $iActive != "Canceled"){

        $i = 0;

        $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $currencySymbol." ".$returnArr['fFlatTripPrice'];

        if ($fSurgePriceDiff > 0) {

            $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";

            $i++;

        }

        if($fWaitingFees > 0) {

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fWaitingFees']:$currencySymbol . $returnArr['fWaitingFees'];

            $i++;                                                                                                                                     

			  }

		include TPATH_CLASS . '../../label_list.php';

        if($fOutStandingAmount > 0) {

          $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fOutStandingAmount']:"--";

          $i++;

  			}

        if ($fDiscount > 0) {

            $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";

            $i++;

				}

        if ($fWalletDebit > 0) {

            $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";

            $i++;

				}

        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";

      

      }/*elseif($eFlatTrip == "Yes" && $iActive == "Canceled"){

        $tripFareDetailsArr[0][$languageLabelsArr['LBL_Total_Fare']] = $currencySymbol." 0.00";

      }*/elseif($fCancelPrice > 0 || ($iActive == "Canceled" && $fWalletDebit > 0)){

            if($fWalletDebit > $CancelPrice){

              $CancelPrice = $fWalletDebit+$fCancelPrice-$fWaitingFees;

              $subtotal = formatNum($fCancelPrice); 

            }else{

              $CancelPrice = $fCancelPrice-$fWalletDebit;

              $subtotal = formatNum($fCancelPrice+$fWaitingFees+$fWalletDebit);

            }

            

            $tripFareDetailsArr[0][$languageLabelsArr['LBL_CANCELLATION_FEE']] = $currencySymbol.formatNum($CancelPrice);


            // $i brainatwork

            $i = 0;

            if($fWaitingFees > 0){

               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = $currencySymbol . $returnArr['fWaitingFees'];

               $i++;

            }

            include TPATH_CLASS . '../../label_list.php';


            if($fWalletDebit > 0) {

                 $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- " . $currencySymbol . $returnArr['fWalletDebit'];

                 $i++;

				    }

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $currencySymbol.$subtotal;

            

	  }else{

                $i = 0;

                $countUfx = 0;

                if ($tripData[0]['eType'] == "UberX") {

                    $tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];

                    $countUfx = 1;

				}

				

                if ($tripData[0]['eFareType'] == "Regular") {

                    //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];

                	/*Changes For Rental*/

                	if($tripData[0]['iRentalPackageId'] > 0) {

						$rentalData = getRentalData($tripData[0]['iRentalPackageId']);

						$tripData[0]['vPackageName'] = $rentalData[0]['vPackageName_'.$userlangcode];

						$tripFareDetailsArr[$i + $countUfx][$tripData[0]['vPackageName']." ".$languageLabelsArr['LBL_RENTAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";

						if ($countUfx == 1) {

							$i++;

						}

						$TripKilometer = getVehicleCountryUnit($tripData[0]['iVehicleTypeId'],$rentalData[0]['fKiloMeter']);

						if($eUnit == "Miles"){

							$TripKilometer = round($TripKilometer * 0.621371,2);

						}

						if($fDistance > $TripKilometer) {

							$extradistance = $fDistance-$TripKilometer;

						} else {

							$extradistance = 0;

						}

						$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_ADDITIONSL_DISTANCE_TXT'] . " (" . $extradistance . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";

						$i++;

						$Extra_Time = calculateAdditionalTime($tripData[0]['tStartDate'],$tripData[0]['tEndDate'],$rentalData[0]['fHour'],$userlangcode);

						$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_ADDITIONAL_TIME_TXT'] . " (" . $Extra_Time . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

						$i++;

					} else {

					// 17-09-2021
                     $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")? $currencySymbol . $returnArr['iBaseFare']:"--";

                    if ($countUfx == 1) {

                        $i++;

					}

                    //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";

                    $i++;

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

                    $i++;

	                }

	                /*Changes For Rental*/

					} else if ($tripData[0]['eFareType'] == "Fixed") {

					//  $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);

          			$vVehicleFare = ($tripData[0]['iFare']*$priceRatio) + $fDiscount + $fWalletDebit + $fDriverDiscount - $fVisitFee - $fMaterialFee - $fMiscFee - $fOutStandingAmount - $fWaitingFees;  

					$SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . formatNum($vVehicleFare) : $currencySymbol . formatNum($vVehicleFare);

					$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =  ($iActive != "Canceled")?$SERVICE_COST:"--";

                    if ($countUfx == 1) {

                        $i++;

					}

					} else if ($tripData[0]['eFareType'] == "Hourly") {

                    $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

					

                    if ($countUfx == 1) {

                        $i++;

					}

				}

				

                if ($fVisitFee > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";

                    $i++;

				}

                if ($fMaterialFee > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";

                    $i++;

				}

                if ($fMiscFee > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";

                    $i++;

				}

                if ($fDriverDiscount > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";

                    $i++;

				}

                

				

				

                // print_r($tripFareDetailsArr);exit;

                // echo $tripData[0]['eFareType'];exit;

                if ($fSurgePriceDiff > 0) {

                    $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fWaitingFees - $fOutStandingAmount;

                    if($eTollSkipped == "No"){

                    $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fWaitingFees - $fOutStandingAmount - $fTollPrice;

                    }

                    $normalfare = formatNum($normalfare);

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";

                    $i++;

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";

                    $i++;

				        }

                

                if ($fWaitingFees > 0) {         

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fWaitingFees']:$currencySymbol . $returnArr['fWaitingFees'];

                    $i++;

				        }    

				include TPATH_CLASS . '../../label_list.php';

                

                if ($fMinFareDiff > 0) {

                    //$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;

                    $minimamfare = $fTripGenerateFare - $fOutStandingAmount - $fTax1 - $fTax2;

                    if($eTollSkipped == "No"){

                    $minimamfare = $fTripGenerateFare - $fTollPrice - $fOutStandingAmount - $fTax1 - $fTax2;

                    }

                    $minimamfare = formatNum($minimamfare);

                    $tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = $currencySymbol . $returnArr['fMinFareDiff'];

                    $returnArr['TotalMinFare'] = ($iActive != "Canceled")?$minimamfare:"--";

                    $i++;

				}

        if($eTollSkipped == "No"){

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";

            $i++;   

				}

               

        

                

        if ($fOutStandingAmount > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fOutStandingAmount']:"--";

                    $i++;

				}

        if ($fDiscount > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";

                    $i++;

				}

        if ($fWalletDebit > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";

                    $i++;

				}

				

                /*if ($fTipPrice > 0) {

                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";

                    $i++;

				} */

        if($fTax1 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax1']:"--";

           $i++;

        }

        if($fTax2 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax2']:"--";

           $i++;

        } 

                

                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:$currencySymbol . $returnArr['fWaitingFees'];

                 $i++;

			}    

			include TPATH_CLASS . '../../label_list.php';



			$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";

			$returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;



			$FareDetailsArr = array();

			foreach ($tripFareDetailsArr as $data) {

				$FareDetailsArr = array_merge($FareDetailsArr, $data);

			}

			$returnArr['FareDetailsArr'] = $FareDetailsArr;

			$returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;	
		
			if ($tripData[0]['eType'] == "UberX") {

				//if($fCancelPrice == 0){

        if($iActive != "Canceled"){

          array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);

        }

        if($PAGE_MODE == "DISPLAY"){

           array_splice($returnArr['FareDetailsNewArr'], 0, 1); 

        }

			}

			} else {

			$tripFareDetailsArr = array();

       if($eFlatTrip == "Yes" && $iActive != "Canceled"){

        $i = 0;         

        $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $currencySymbol." ".$returnArr['fFlatTripPrice'];

        

        if($fSurgePriceDiff > 0) {   

          $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";

  				$i++;   

  			}

        

        if($PAGE_MODE == "DISPLAY"){

  				if($fWaitingFees > 0) {

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fWaitingFees']:"--";

            $i++;

			    }

			    include TPATH_CLASS . '../../label_list.php';

  				if($fOutStandingAmount > 0) {

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fOutStandingAmount']:"--";

            $i++;

  			  }

          if ($fDiscount > 0) {

  					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";

  					$i++;

  				}

          if ($fWalletDebit > 0) {

  					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";

  					$i++;

  				}

        }else{

          if($fWaitingFees > 0) {

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fWaitingFees']:"--";

            $i++;

			    }

			    include TPATH_CLASS . '../../label_list.php';

          if($fOutStandingAmount > 0) {

            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?"- ".$currencySymbol . $returnArr['fOutStandingAmount']:"--";

            $i++;

  			  }

        }

        

        

      }elseif($fCancelPrice > 0 || ($iActive == "Canceled" && $fWalletDebit > 0)){

            if($fWalletDebit > $CancelPrice){

              $CancelPrice = $fWalletDebit+$fCancelPrice-$fWaitingFees;

              $subtotal = formatNum($fCancelPrice); 

            }else{

              $CancelPrice = $fCancelPrice-$fWalletDebit;

              $subtotal = formatNum($fCancelPrice+$fWaitingFees+$fWalletDebit);

            }

            $i = 0;    

            $tripFareDetailsArr[$i][$languageLabelsArr['LBL_CANCELLATION_FEE']] = $currencySymbol.formatNum($CancelPrice);

            if($fWaitingFees > 0){

               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = $currencySymbol . $returnArr['fWaitingFees'];

               $i++;

            }

            include TPATH_CLASS . '../../label_list.php';

            /*if($fWalletDebit > 0) {

                 $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- " . $currencySymbol . $returnArr['fWalletDebit'];

                 $i++;

				    } */

            //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $currencySymbol.$subtotal;$i++;

             

	     } else{

      			$i = 0;

  			$countUfx = 0;

  			if ($tripData[0]['eType'] == "UberX"  && $PAGE_MODE == "HISTORY") {

  				$tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];

  				$countUfx = 1;

  			}

			

			if ($tripData[0]['eFareType'] == "Regular") {

				//$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];

					/*Changes For Rental*/

					if($tripData[0]['iRentalPackageId'] > 0) {

						$rentalData = getRentalData($tripData[0]['iRentalPackageId']);

						$tripData[0]['vPackageName'] = $rentalData[0]['vPackageName_'.$userlangcode];

						$tripFareDetailsArr[$i + $countUfx][$tripData[0]['vPackageName']." ".$languageLabelsArr['LBL_RENTAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";

						if ($countUfx == 1) {

							$i++;

						}

						$TripKilometer = getVehicleCountryUnit($tripData[0]['iVehicleTypeId'],$rentalData[0]['fKiloMeter']);

						if($eUnit == "Miles"){

							$TripKilometer = round($TripKilometer * 0.621371,2);

						}

						if($fDistance > $TripKilometer) {

							$extradistance = $fDistance-$TripKilometer;

						} else {

							$extradistance = 0;

						}

						$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_ADDITIONSL_DISTANCE_TXT'] . " (" . $extradistance . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";

						$i++;

						$Extra_Time = calculateAdditionalTime($tripData[0]['tStartDate'],$tripData[0]['tEndDate'],$rentalData[0]['fHour'],$userlangcode);

						$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_ADDITIONAL_TIME_TXT'] . " (" . $Extra_Time . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

						$i++;

					} else {

				// 17-09-2021 - AQUI 
				 $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";

				if ($countUfx == 1) {
					$i++;
				}

				//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";


				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";

				$i++;

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

				$i++;

					}

					/*Changes For Rental*/

				} else if ($tripData[0]['eFareType'] == "Fixed") {

				//$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);

                $vVehicleFare = ($tripData[0]['iFare']*$priceRatio) + $fDiscount + $fWalletDebit + $fDriverDiscount - $fVisitFee - $fMaterialFee - $fMiscFee - $fOutStandingAmount - $fWaitingFees;

                $SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . formatNum($vVehicleFare) : $currencySymbol . formatNum($vVehicleFare);

                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($iActive != "Canceled")?$SERVICE_COST:"--";                    

				if ($countUfx == 1) {

					$i++;

				}

				} else if ($tripData[0]['eFareType'] == "Hourly") {

				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";

				

				if ($countUfx == 1) {

					$i++;

				}

			}

			

			if ($fVisitFee > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";

				$i++;

			}

			if ($fMaterialFee > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";

				$i++;

			}

			if ($fMiscFee > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";

				$i++;

			}

			if ($fDriverDiscount > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";

				$i++;

			}

      

      

			

			if ($fSurgePriceDiff > 0) {

				$normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fWaitingFees - $fOutStandingAmount;

        if($eTollSkipped == "No"){

				$normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fWaitingFees - $fOutStandingAmount - $fTollPrice;

        }

        $normalfare = formatNum($normalfare);

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";

				$i++;

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";

				$i++;   

			}

      if($fWaitingFees > 0) {

        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WAITING_FEE_TXT']." (".$waitingTime.")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fWaitingFees']:$currencySymbol . $returnArr['fWaitingFees'];

        $i++;

			}

			include TPATH_CLASS . '../../label_list.php';

      if ($fMinFareDiff > 0) {

				//$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;

				$minimamfare = $fTripGenerateFare - $fOutStandingAmount - $fTax1 - $fTax2;

        if($eTollSkipped == "No"){

				$minimamfare = $fTripGenerateFare - $fTollPrice - $fOutStandingAmount - $fTax1 - $fTax2;

        }

				$minimamfare = formatNum($minimamfare);

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MINIMUM'] . ' (' . $currencySymbol . $minimamfare . ')'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMinFareDiff']:"--";

				$returnArr['TotalMinFare'] = $minimamfare;

				$i++;

			}

			if($eTollSkipped == "No"){

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";

				$i++;   

			}

			

			if($PAGE_MODE == "DISPLAY"){

				if($fDiscount > 0) {

					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";

					$i++;

				}

				if($fTax1 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax1']:"--";

           $i++;

        }

        if($fTax2 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax2']:"--";

           $i++;

        }

        if($fOutStandingAmount > 0) {

          $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fOutStandingAmount']:"--";

					$i++;

				}

				if ($fWalletDebit > 0) {

					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";

					$i++;

				}

      }else{

        if($fTax1 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?"-". $currencySymbol . $returnArr['fTax1']:"--";

           $i++;

        }

        if($fTax2 > 0){

           $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?"-". $currencySymbol . $returnArr['fTax2']:"--";

					$i++;

				}

        /*if($fOutStandingAmount > 0) {

          $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = ($iActive != "Canceled")?"- ".$currencySymbol . $returnArr['fOutStandingAmount']:"--";

          $i++;

  			}  */

			}

      /* Informa o cupom de desconto do MOTORISTA */

       if ($fDiscount > 0) {

                //$coupon = '<b>' . $languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE'] . '</b>';
                
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";

				$i++;

				}
/*
				if ($fWalletDebit > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";

				$i++;

			} */

			

			/*if ($fTipPrice > 0) {

				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";

				$i++;

			} */

       }    

			$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";

			$returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;

			$FareDetailsArr = array();

			foreach ($tripFareDetailsArr as $data) {

				$FareDetailsArr = array_merge($FareDetailsArr, $data);

			}

			$returnArr['FareDetailsArr'] = $FareDetailsArr;

			//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_Commision']] = ($iActive != "Canceled")?"-" . $currencySymbol . $returnArr['fCommision']:"--";

      		// Brainatwork
      		include TPATH_CLASS . '../../label_list.php';


   			$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_Commision']] = "- " . $currencySymbol . number_format($returnArr['fCommision'], 2, ',', '');
      			$i++;

      		// Print Invoice / Motorista



      		$tripFareDetailsArr[$i + 1]['Tarifa RYD'] = "- " . $currencySymbol . number_format($returnArr['fRydTax'], 2, ',', '');

			$i++;



			//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = $currencySymbol . $returnArr['iFare'];

      		$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = $currencySymbol . $returnArr['iFare_Detail_Earning'];

			$returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;

			

			if($tripData[0]['eType'] == "UberX" && $iActive != "Canceled") {

           array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);

			}

		}

		$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";

		//passengertripfaredetails

		

		$HistoryFareDetailsArr = array();

		foreach ($tripFareDetailsArr as $inner) {

			$HistoryFareDetailsArr = array_merge($HistoryFareDetailsArr, $inner);

		}

		$returnArr['HistoryFareDetailsArr'] = $HistoryFareDetailsArr;

		


		brainLog(' LOGGGGGGGG - sql ::: ', $returnArr);

		//drivertripfarehistorydetails

		//echo "<pre>";print_r($returnArr);echo "<pre>";print_r($tripData);exit;

		// FareDetailsNewArr
		// FareDetailsArr
		// HistoryFareDetailsNewArr
		// HistoryFareDetailsArr

		// Mostra no painel do motorista (FareDetailsNewArr)

	 	unset($returnArr['FareDetailsNewArr'][0]); // Tarifa Base

		 $returnArr['FareDetailsNewArr'] = array_values($returnArr['FareDetailsNewArr']);

		// unset($returnArr['HistoryFareDetailsNewArr'][0]); // Tarifa Base */

		// unset($returnArr['FareDetailsNewArr'][3]); // Tarifa Minima
		// unset($returnArr['FareDetailsNewArr'][0]); // Tarifa Base

		 // $returnArr['FareDetailsNewArr'][] = array('RYD 2021'=>'A melhor');
		// $returnArr['HistoryFareDetailsNewArr'][] = array('RYD 2021!'=>'A melhor');
		
		 // $returnArr['FareDetailsArr']['RYD 2021'] = 'A melhor!!';
		// $returnArr['HistoryFareDetailsArr']['RYD 2021!'] = 'A melhor';

		// $returnArr['FareDetailsNewArr'][0] = array('RYD 2021'=>'A melhor');
		// $returnArr['HistoryFareDetailsNewArr'][0] = array('RYD'=>'A melhor');

		
      // $returnArr['FareDetailsNewArr']['Tarifa base'] = "R$ 2.05";
//		$returnArr['FareDetailsArr']['Tarifa base'] = "R$ 2.04";
//		$returnArr['HistoryFareDetailsNewArr'][0]['Tarifa base'] = "R$ 2.03";
//		$returnArr['HistoryFareDetailsArr']['Tarifa base'] = "R$ 2.02";


		return $returnArr;

	}

	

	function formatNum($number) {

		return strval(number_format($number, 2));

	}

	

	function getUserRatingAverage($iMemberId, $eUserType = "Passenger") {

		global $obj, $generalobj;

		if ($eUserType == "Passenger") {

			$iUserId = "iDriverId";

			$checkusertype = "Passenger";

			} else {

			$iUserId = "iUserId";

			$checkusertype = "Driver";

		}

		

		$usertotaltrips = get_value("trips", "iTripId", $iUserId, $iMemberId);

		if (count($usertotaltrips) > 0) {

			for ($i = 0; $i < count($usertotaltrips); $i++) {

				$iTripId .= $usertotaltrips[$i]['iTripId'] . ",";

			}

			

			$iTripId_str = substr($iTripId, 0, -1);

			//echo  $iTripId_str;exit;

			$sql = "SELECT count(iRatingId) as ToTalTrips, SUM(vRating1) as ToTalRatings from ratings_user_driver WHERE iTripId IN (" . $iTripId_str . ") AND eUserType = '" . $checkusertype . "'";

			$result_ratings = $obj->MySQLSelect($sql);

			$ToTalTrips = $result_ratings[0]['ToTalTrips'];

			$ToTalRatings = $result_ratings[0]['ToTalRatings'];

			//$average_rating = round($ToTalRatings / $ToTalTrips, 2);

			$average_rating = round($ToTalRatings / $ToTalTrips, 1);

			} else {

			$average_rating = 0;

		}

		return $average_rating;

	}

	

	function deliverySmsToReceiver($iTripId) {

		global $obj, $generalobj, $tconfig;

		

		$sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";

		$tripData = $obj->MySQLSelect($sql);

		

		$SenderName = get_value("register_user", "vName,vLastName", "iUserId", $tripData[0]['iUserId']);

		$SenderName = $SenderName[0]['vName'] . " " . $SenderName[0]['vLastName'];

		$delivery_address = $tripData[0]['tDaddress'];

		$vDeliveryConfirmCode = $tripData[0]['vDeliveryConfirmCode'];

		$page_link = $tconfig['tsite_url'] . "trip_tracking.php?iTripId=" . $iTripId;

		$page_link = get_tiny_url($page_link);

		

		$message_deliver = $SenderName . " has send you the parcel on below address." . $delivery_address . ". Upon Receiving the parcel, please provide below verification code to Delivery Driver. Verification Code: " . $vDeliveryConfirmCode . ". click on link below to track your parcel. " . $page_link;

		

		//echo $message_deliver;exit;

		return $message_deliver;

	}

	

	function get_tiny_url($url) {  

		$ch = curl_init();

		$timeout = 5;        

		curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;

	}

	

	function addToUserRequest($iUserId, $iDriverId, $message, $iMsgCode) {

		global $obj;

		$data['iUserId'] = $iUserId;

		$data['iDriverId'] = $iDriverId;

		$data['tMessage'] = $message;

		$data['iMsgCode'] = $iMsgCode;

		$data['dAddedDate'] = @date("Y-m-d H:i:s");

		

		$dataId = $obj->MySQLQueryPerform("passenger_requests", $data, 'insert');

		

		return $dataId;

	}

	

	function addToDriverRequest($iDriverId, $iUserId, $iTripId, $eStatus) {

		global $obj;

		$data['iDriverId'] = $iDriverId;

		$data['iUserId'] = $iUserId;

		$data['iTripId'] = $iTripId;

		$data['eStatus'] = $eStatus;

		$data['tDate'] = @date("Y-m-d H:i:s");

		$data['dAddedDate'] = @date("Y-m-d H:i:s");

		

		$id = $obj->MySQLQueryPerform("driver_request", $data, 'insert');

		

		return $id;

	}

	

	function addToUserRequest2($data) {

		global $obj;

		$dataId = $obj->MySQLQueryPerform("passenger_requests", $data, 'insert');

		return $dataId;

	}

	

	function addToDriverRequest2($data) {

		global $obj;

		$data['dAddedDate'] = @date("Y-m-d H:i:s");

		$id = $obj->MySQLQueryPerform("driver_request", $data, 'insert');

		return $id;

	}

	

	function UpdateDriverRequest($iDriverId, $iUserId, $iTripId, $eStatus) {

		global $obj;

		

		$sql = "SELECT * FROM `driver_request` WHERE iDriverId = '" . $iDriverId . "' AND iUserId = '" . $iUserId . "' AND iTripId = '0' ORDER BY iDriverRequestId DESC LIMIT 0,1";

		$db_sql = $obj->MySQLSelect($sql);

		$request_count = count($db_sql);

		

		if ($request_count > 0) {

			$where = " iDriverRequestId = '" . $db_sql[0]['iDriverRequestId'] . "'";

			$Data_Update['eStatus'] = $eStatus;

			$Data_Update['tDate'] = @date("Y-m-d H:i:s");

			$Data_Update['iTripId'] = $iTripId;

			$id = $obj->MySQLQueryPerform("driver_request", $Data_Update, 'update', $where);

		}

		

		return $request_count;

	}

	

	function UpdateDriverRequest2($iDriverId, $iUserId, $iTripId, $eStatus = "", $vMsgCode, $eAcceptAttempted = "No") {

		global $obj;

		$sql = "SELECT * FROM `driver_request` WHERE iDriverId = '" . $iDriverId . "' AND iUserId = '" . $iUserId . "' AND iTripId = '0' AND vMsgCode='".$vMsgCode."'";

		$db_sql = $obj->MySQLSelect($sql);

		$request_count = count($db_sql);

		

		if ($request_count > 0) {

			$where = " iDriverRequestId = '" . $db_sql[0]['iDriverRequestId'] . "'";

			if($eStatus != ""){

        $Data_Update['eStatus'] = $eStatus;

      }

      $Data_Update['tDate'] = @date("Y-m-d H:i:s");

			$Data_Update['iTripId'] = $iTripId;

      $Data_Update['eAcceptAttempted'] = $eAcceptAttempted;

			$id = $obj->MySQLQueryPerform("driver_request", $Data_Update, 'update', $where);

		}

		return $request_count;

	}

	

	function getDriverStatus($driverId = '') {

		global $generalobj,$obj;

		

		$vLangCode=get_value('register_driver', 'vLang', 'iDriverId',$driverId,'','true');

		if($vLangCode == "" || $vLangCode == NULL){

			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');

		}

		

		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");

		//$userwaitinglabel = $languageLabelsArr['LBL_TRIP_USER_WAITING'];

		

		$sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$driverId."' ) dl on dl.doc_masterid=dm.doc_masterid  

		where dm.doc_usertype='driver' and dm.status='Active' ";

		$db_document = $obj->MySQLSelect($sql1);

		if(count($db_document) > 0){

			for($i=0;$i<count($db_document);$i++){

				if($db_document[$i]['doc_file'] == ""){

					$returnArr['Action'] ="0";

					$returnArr['message'] ="Please upload your ". $db_document[$i]['doc_name'];

					echo json_encode($returnArr);exit; 

				}

				if($db_document[$i]['status'] != "Active"){

					$returnArr['Action'] ="0";

					if($db_document[$i]['status'] == "Inactive"){

						$returnArr['message'] ="Please activate your ". $db_document[$i]['doc_name'];

						echo json_encode($returnArr);exit;

					}

					if($db_document[$i]['status'] == "Deleted"){

						$returnArr['message'] ="Current status is deleted of your". $db_document[$i]['doc_name'];

						echo json_encode($returnArr);exit;

					}

				}

			}

		}

		

		$sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '".$driverId."'";

		$db_drv_vehicle = $obj->MySQLSelect($sql);

		if(count($db_drv_vehicle) == 0){

			$returnArr['Action'] ="0";  # Check For Driver's vehicle added or not #

			$returnArr['message'] ="LBL_INACTIVE_CARS_MESSAGE_TXT";

			echo json_encode($returnArr);exit;

			}else{

			$DriverSelectedVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId',$driverId,'','true');

			if($DriverSelectedVehicleId == 0){

				$returnArr['Action'] ="0"; # Check Driver has selected  vehicle or not if #

				$returnArr['message'] ="LBL_SELECT_CAR_MESSAGE_TXT";

				echo json_encode($returnArr);exit;   

				}else{

				# Check For Driver's selected vehicle's document are upload or not #

				$sql= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$DriverSelectedVehicleId."' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='car' and dm.status='Active'";

				$db_selected_vehicle = $obj->MySQLSelect($sql);

				if(count($db_selected_vehicle) > 0){

					for($i=0;$i<count($db_selected_vehicle);$i++){

						if($db_selected_vehicle[$i]['doc_file'] == ""){

							$returnArr['Action'] ="0";

							$returnArr['message'] ="Please upload your ". $db_selected_vehicle[$i]['doc_name'];

							echo json_encode($returnArr);exit; 

						}

					}

				}

				# Check For Driver's selected vehicle's document are upload or not #

				# Check For Driver's selected vehicle status #

				$DriverSelectedVehicleStatus = get_value('driver_vehicle', 'eStatus', 'iDriverVehicleId',$DriverSelectedVehicleId,'','true');

				if($DriverSelectedVehicleStatus == "Inactive" || $DriverSelectedVehicleStatus == "Deleted"){

					$returnArr['Action'] ="0";

					$returnArr['message'] ="LBL_SELECTED_VEHICLE_NOT_ACTIVE";

					echo json_encode($returnArr);exit;

				}

				# Check For Driver's selected vehicle status #

			}

		} 

		

		$sql = "SELECT rd.eStatus as driverstatus,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='".$driverId."' AND cmp.iCompanyId=rd.iCompanyId";

		$Data = $obj->MySQLSelect($sql);

		

		if($Data[0]['driverstatus']!="active" || $Data[0]['cmpEStatus']!="Active"){

			

			$returnArr['Action'] ="0";

			

			if($Data[0]['cmpEStatus']!="Active"){

				$returnArr['message'] ="LBL_CONTACT_US_STATUS_NOTACTIVE_COMPANY";

				}else if($Data[0]['driverstatus']=="Deleted"){

				$returnArr['message'] ="LBL_ACC_DELETE_TXT";

				}else{

				$returnArr['message']="LBL_CONTACT_US_STATUS_NOTACTIVE_DRIVER";

			}

			

			echo json_encode($returnArr);exit;

		}

		

	}

	

	function fetch_address_geocode($address, $geoCodeResult = "") {

		global $generalobj,$GOOGLE_SEVER_API_KEY_WEB;

		$address = str_replace(" ", "+", "$address");

		//$GOOGLE_SEVER_API_KEY_WEB=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_API_KEY_WEB");

		$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$GOOGLE_SEVER_API_KEY_WEB;

		//$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";

		

		if($geoCodeResult == ""){

			$result = file_get_contents("$url");

      $result = preg_replace("/[\n\r]/", "", $result);

		}else{

			$result = $geoCodeResult;

      $result = stripslashes(preg_replace("/[\n\r]/", "", $result));

		}

		//$result = stripslashes(preg_replace("/[\n\r]/", "", $result));

		$json = json_decode($result);

		

		$city = $state = $country = $country_code = '';

		

		foreach ($json->results as $result) {

			foreach($result->address_components as $addressPart) {

				if(((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types))) || ((in_array('sublocality', $addressPart->types)) && (in_array('political', $addressPart->types)) && (in_array('sublocality_level_1', $addressPart->types)))) {

					$city = $addressPart->long_name;

					}else if ((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types))) {

					$state = $addressPart->long_name;

					}else if ((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))){

					$country = $addressPart->long_name;

					$country_code = $addressPart->short_name;

				}

			}

		}

		

		// if(($city != '') && ($state != '') && ($country != '')) 

		// $address = $city.', '.$state.', '.$country;

		// else if (($city != '') && ($state != ''))

		// $address = $city.', '.$state;

		// else if (($state != '') && ($country != ''))

		// $address = $state.', '.$country;

		// else if ($country != '')

		// $address = $country;

		

		$returnArr = array('city'=>$city,'state'=> $state,'country'=>$country,'country_code'=>$country_code);

		

		

		return $returnArr;

	}

	

	function get_address_geocode($address) {

		global $generalobj,$GOOGLE_SEVER_API_KEY_WEB;

		$address = str_replace(" ", "+", "$address");

		//$GOOGLE_SEVER_API_KEY_WEB=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_API_KEY_WEB");

		$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$GOOGLE_SEVER_API_KEY_WEB;

		$result = file_get_contents("$url");

		$result = stripslashes(preg_replace("/[\n\r]/", "", $result));

		$json = json_decode($result);

		$city = $state = $country = $country_code = '';

		

		foreach ($json->results as $result) {

			foreach($result->address_components as $addressPart) {

				if(((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types))) || ((in_array('sublocality', $addressPart->types)) && (in_array('political', $addressPart->types)) && (in_array('sublocality_level_1', $addressPart->types)))) {

					$city = $addressPart->long_name;

					}else if ((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types))) {

					$state = $addressPart->long_name;

					}else if ((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))){

					$country = $addressPart->long_name;

					$country_code = $addressPart->short_name;

				}

			}

		}

		$returnArr = array('city'=>$city,'state'=> $state,'country'=>$country,'country_code'=>$country_code);

		return $returnArr;

	}

	

	function UploadUserImage($iMemberId,$UserType="Passenger",$eSignUpType,$vFbId,$vImageURL=""){

		global $generalobj,$tconfig,$TWITTER_OAUTH_ACCESS_TOKEN,$TWITTER_OAUTH_ACCESS_TOKEN_SECRET,$TWITTER_CONSUMER_KEY,$TWITTER_CONSUMER_SECRET,$GOOGLE_SEVER_API_KEY_WEB;

		$vimage = "";

		if($UserType == "Passenger"){

			$Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$iMemberId."/";

			$OldImage=get_value('register_user', 'vImgName', 'iUserId', $iMemberId,'','true');

			}else{

			$Photo_Gallery_folder =$tconfig["tsite_upload_images_driver_path"]."/".$iMemberId."/";

			$OldImage=get_value('register_driver', 'vImage', 'iDriverId', $iMemberId,'','true');

		}

		unlink($Photo_Gallery_folder.$OldImage);

		unlink($Photo_Gallery_folder."1_".$OldImage);

		unlink($Photo_Gallery_folder."2_".$OldImage);

		unlink($Photo_Gallery_folder."3_".$OldImage);   

		unlink($Photo_Gallery_folder."4_".$OldImage);   

		if(!is_dir($Photo_Gallery_folder)) {                  

			mkdir($Photo_Gallery_folder, 0777);

		}

		if($eSignUpType == "Facebook"){

  			

      if($vImageURL != ""){

        $vImageURL = str_replace("type=large","width=256",$vImageURL);

        $baseurl = $vImageURL; 

      }else{

        //$baseurl =  "http://graph.facebook.com/".$vFbId."/picture?type=large";

  			$baseurl =  "http://graph.facebook.com/".$vFbId."/picture?width=256";

  			//$url = $vFbId."_".time().".jpg";

      }  

			  

      $url = time().".jpg";

			/* file_get_content */

			$profile_Image = $baseurl;

			$userImage = $url;

			$thumb_image = file_get_contents($baseurl); 

			$thumb_file = $Photo_Gallery_folder . $url;

			$image_name = file_put_contents($thumb_file, $thumb_image);

			/* file_get_content  ends*/

			if(is_file($Photo_Gallery_folder.$url)) {

				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");

				$vimage = $imgname; 

			}  

		}

		if($eSignUpType == "Google"){

      if($vImageURL != ""){

          $baseurl = $vImageURL;

          $url = time().".jpg";

      }else{

    			//$GOOGLE_SEVER_API_KEY_WEB = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_API_KEY_WEB");

    			//$baseurl1 =  "https://www.googleapis.com/plus/v1/people/114434193354602240754?fields=image&key=AIzaSyB7_FaMl2gU1ItcomolF2S1Fzh8prnvNNw";

    			$baseurl1 =  "https://www.googleapis.com/plus/v1/people/".$vFbId."?fields=image&key=".$GOOGLE_SEVER_API_KEY_WEB;

    			//$url = $vFbId."_".time().".jpg";

    			//$url = time().".jpg";

          $url = time().".jpg";

    			try{

    				$jsonfile = file_get_contents($baseurl1);

    				$jsondata = json_decode($jsonfile);

    				$baseurl = $jsondata->image->url;

    				$baseurl = str_replace("?sz=50","?sz=256",$baseurl);

    				}catch (ErrorException $ex) {

    				$imgname = "";

    				$vimage = $imgname; 

    			}

      }

      /* file_get_content */

			$profile_Image = $baseurl;

			$userImage = $url;

			$thumb_image = file_get_contents($baseurl);

			$thumb_file = $Photo_Gallery_folder . $url;

			$image_name = file_put_contents($thumb_file, $thumb_image);

			/* file_get_content  ends*/

			if(is_file($Photo_Gallery_folder.$url)) {

				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");

				//$imgname = $generalobj->general_upload_image($url, $url, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);

				$vimage = $imgname;

			}

		}

		if($eSignUpType == "Twitter"){

      if($vImageURL != ""){

          $baseurl = $vImageURL;

      }else{

    			require_once('assets/libraries/twitter/TwitterAPIExchange.php');

    			/*$TWITTER_OAUTH_ACCESS_TOKEN = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN");  

    			$TWITTER_OAUTH_ACCESS_TOKEN_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN_SECRET");

    			$TWITTER_CONSUMER_KEY = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_KEY");

    			$TWITTER_CONSUMER_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_SECRET");*/

    			$settings = array(

                'oauth_access_token' => $TWITTER_OAUTH_ACCESS_TOKEN,

                'oauth_access_token_secret' => $TWITTER_OAUTH_ACCESS_TOKEN_SECRET,

                'consumer_key' => $TWITTER_CONSUMER_KEY,

                'consumer_secret' => $TWITTER_CONSUMER_SECRET

    			);

    			$url = 'https://api.twitter.com/1.1/users/show.json';

    			$getfield = '?user_id='.$vFbId;

    			$requestMethod = 'GET';

    			$twitter = new TwitterAPIExchange($settings);

    			$twitterArr = $twitter->setGetfield($getfield)

    			->buildOauth($url, $requestMethod)

    			->performRequest();

    			$jsondata = json_decode($twitterArr); //echo "<pre>";print_r($jsondata);exit;   

    			$profile_image_url = $jsondata->profile_image_url;

    			$baseurl = str_replace("_normal","",$profile_image_url);

      }

      

			//$url = $vFbId."_".time().".jpg";

			$url = time().".jpg";       

			/* file_get_content */

			$profile_Image = $baseurl;

			$userImage = $url;

			$thumb_image = file_get_contents($baseurl);

			$thumb_file = $Photo_Gallery_folder . $url;

			$image_name = file_put_contents($thumb_file, $thumb_image);

			/* file_get_content  ends*/

			if(is_file($Photo_Gallery_folder.$url)) {

				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");

				$vimage = $imgname; 

			}  

		}

		return $vimage;

	}

	

	function getMemberCountryUnit($iMemberId,$UserType="Passenger"){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

		

		if ($UserType == "Passenger") {

			$tblname = "register_user";

			$vCountryfield = "vCountry";

			$iUserId = "iUserId";

			} else {

			$tblname = "register_driver";

			$vCountryfield = "vCountry";

			$iUserId = "iDriverId";

		}

    

    $sql = "SELECT co.eUnit FROM country as co LEFT JOIN $tblname as rd ON co.vCountryCode = rd.$vCountryfield WHERE $iUserId = '".$iMemberId."'";

    $sqlcountryCode = $obj->MySQLSelect($sql);

    $vCountry = $sqlcountryCode[0]['eUnit']; 

		//$vCountry = get_value($tblname, $vCountryfield, $iUserId, $iMemberId, '', 'true'); 

		

		if($vCountry == "" || $vCountry == NULL){

			$vCountryCode = $DEFAULT_DISTANCE_UNIT;

		}else{

			$vCountryCode = $vCountry;

		} 

		return $vCountryCode;

	}

	

	

	function getMemberCompanyUnit($iMemberId,$UserType="Driver"){

		global $generalobj,$obj;

		

		if ($UserType == "Driver") {

			$tblname = "register_driver";

			$iCompanyfield = "iCompanyId";

			$iUserId = "iDriverId";

		} else {

			$tblname = "register_user";

			$iCompanyfield = "iCompanyId";

			$iUserId = "iUserId";

		}

    

	    $sql = "SELECT c.iCompanyId, c.fRydTax FROM company as c LEFT JOIN $tblname as rd ON c.iCompanyId = rd.$iCompanyfield WHERE $iUserId = '".$iMemberId."'";

	    $sqlcountryCode = $obj->MySQLSelect($sql);

	    $result['iCompanyId'] = $sqlcountryCode[0]['iCompanyId']; 

	    $result['fRydTax'] = $sqlcountryCode[0]['fRydTax']; 

		return $result;

	}



	function getVehicleCountryUnit_PricePerKm($vehicleTypeID,$fPricePerKM, $iMemberId="", $userType=""){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

		

    $iLocationid = get_value("vehicle_type", "iLocationid", "iVehicleTypeId", $vehicleTypeID, '', 'true'); 

		$iCountryId = get_value("location_master", "iCountryId", "iLocationId", $iLocationid, '', 'true');

    if($iLocationid == "-1"){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

			}else{

			$eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');

		}

		

		if($eUnit == "" || $eUnit == NULL){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}

		

		if($iMemberId != "" && $userType != ""){

      $vCountry = get_value("register_user", "vCountry", "iUserId", $iMemberId, '', 'true'); 

  		if($vCountry == ""){

  		  $userUnit = $DEFAULT_DISTANCE_UNIT;

  		}else{

  			$userUnit = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');

  		}

  		if($userUnit == "" || $userUnit == NULL){

  			$userUnit = $DEFAULT_DISTANCE_UNIT;

  		}

			if($userUnit == "Miles" && $eUnit == "Miles"){

				return $fPricePerKM * 0.621371;

			}else if($userUnit == "KMs" && $eUnit == "Miles" ){

				return $fPricePerKM * 1.60934;

			}else if($userUnit == "Miles" && $eUnit == "KMs"){

				return $fPricePerKM * 0.621371;

			}else if($userUnit == "KMs" && $eUnit == "KMs" ){

				return $fPricePerKM; 

			}

		}

		if($eUnit == "Miles"){

			$PricePerKM = $fPricePerKM * 0.621371; 

			}else{

			$PricePerKM = $fPricePerKM;

		}

		return  $PricePerKM;

	}

  function getVehiclePrice_ByUSerCountry($iUserId,$fPricePerKM){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

    $vCountry = get_value("register_user", "vCountry", "iUserId", $iUserId, '', 'true'); 

		if($vCountry == ""){

		  $eUnit = $DEFAULT_DISTANCE_UNIT;

		}else{

			$eUnit = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');

		}

		if($eUnit == "" || $eUnit == NULL){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}

		if($eUnit == "Miles"){

			$PricePerKM = $fPricePerKM * 1.60934; 

			}else{

			$PricePerKM = $fPricePerKM;

		}

		

		return  $PricePerKM;

	}

	/* Added For Rental */

	function getRentalPrice_ByCountry($iUserId,$UserType,$vehicleTypeID,$fPricePerKM){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

		if($UserType == 'Passenger'){

    		$vCountry = get_value("register_user", "vCountry", "iUserId", $iUserId, '', 'true');

		} else {

			$vCountry = get_value("register_driver", "vCountry", "iDriverId", $iUserId, '', 'true');

		}

		if($vCountry == ""){

		  $eUnit = $DEFAULT_DISTANCE_UNIT;

		} else {

			$eUnit = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');

		}

		if($eUnit == "" || $eUnit == NULL){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}

		$iLocationid = get_value("vehicle_type", "iLocationid", "iVehicleTypeId", $vehicleTypeID, '', 'true');

		$iCountryId = get_value("location_master", "iCountryId", "iLocationId", $iLocationid, '', 'true');

		if($iLocationid == "-1"){

			$eUnit_vehicle = $DEFAULT_DISTANCE_UNIT;

		}else{

			$eUnit_vehicle = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');

		}

		if($eUnit_vehicle == "" || $eUnit_vehicle == NULL){

			$eUnit_vehicle = $DEFAULT_DISTANCE_UNIT;

		}

		if($eUnit == $eUnit_vehicle){

			$PricePerKM = $fPricePerKM;

		}  else {

			if($eUnit == 'Miles' && $eUnit_vehicle == 'KMs'){

				$PricePerKM = $fPricePerKM * 0.621371;

			} else if($eUnit == 'KMs' && $eUnit_vehicle == 'Miles'){

				$PricePerKM = $fPricePerKM * 0.621371;

			} else {

				$PricePerKM = $fPricePerKM * 1.60934;

			}

		}

		return  $PricePerKM;

	}

		function getRentalKilometer_ByCountry($iUserId,$UserType,$vehicleTypeID,$fPricePerKM){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

    	if($UserType == 'Passenger'){

    		$vCountry = get_value("register_user", "vCountry", "iUserId", $iUserId, '', 'true');

		} else {

			$vCountry = get_value("register_driver", "vCountry", "iDriverId", $iUserId, '', 'true');

		}

		if($vCountry == ""){

		  $eUnit = $DEFAULT_DISTANCE_UNIT;

		} else {

		  $eUnit = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');

		}

		if($eUnit == "" || $eUnit == NULL){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}

		$iLocationid = get_value("vehicle_type", "iLocationid", "iVehicleTypeId", $vehicleTypeID, '', 'true');

		$iCountryId = get_value("location_master", "iCountryId", "iLocationId", $iLocationid, '', 'true');

		if($iLocationid == "-1"){

			$eUnit_vehicle = $DEFAULT_DISTANCE_UNIT;

		}else{

			$eUnit_vehicle = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');

		}

		if($eUnit_vehicle == "" || $eUnit_vehicle == NULL){

			$eUnit_vehicle = $DEFAULT_DISTANCE_UNIT;

		}

		if($eUnit == $eUnit_vehicle){

			$PricePerKM = $fPricePerKM;

		}  else {

			if($eUnit == 'Miles' && $eUnit_vehicle == 'KMs'){

				$PricePerKM = $fPricePerKM * 0.621371;

			} else if($eUnit == 'KMs' && $eUnit_vehicle == 'Miles'){

				$PricePerKM = $fPricePerKM * 0.621371;

			} else {

				$PricePerKM = $fPricePerKM * 1.60934;

			}

		}

		return $PricePerKM;

	}

	function getVehicleCountryUnit($vehicleTypeID,$fPerKM){

		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;

    	$iLocationid = get_value("vehicle_type", "iLocationid", "iVehicleTypeId", $vehicleTypeID, '', 'true');

		$iCountryId = get_value("location_master", "iCountryId", "iLocationId", $iLocationid, '', 'true');

   		if($iLocationid == "-1"){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}else{

			$eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');

		}

		if($eUnit == "" || $eUnit == NULL){

			$eUnit = $DEFAULT_DISTANCE_UNIT;

		}

		if($eUnit == "Miles"){

			$KMvalue = $fPerKM * 1.60934; 

		}else{

			$KMvalue = $fPerKM;

		}

		return  round($KMvalue, 2);

	}

	/*End added for rental*/

	function TripCollectTip($iMemberId,$iTripId,$fAmount){

		global $generalobj,$obj,$APP_PAYMENT_METHOD;

		$tbl_name = "register_user";

		$currencycode = "vCurrencyPassenger";

		$iUserId = "iUserId";

		$eUserType = "Rider";

		if($iMemberId == "") {

			$iMemberId = get_value('trips', 'iUserId', 'iTripId', $iTripId,'','true');

		}

		$vStripeCusId = get_value($tbl_name, 'vStripeCusId', $iUserId, $iMemberId,'','true');

		$vStripeToken = get_value($tbl_name, 'vStripeToken', $iUserId, $iMemberId,'','true');

    $vBrainTreeToken = get_value($tbl_name, 'vBrainTreeToken', $iUserId, $iMemberId,'','true');

		$userCurrencyCode = get_value($tbl_name, $currencycode, $iUserId, $iMemberId,'','true');

		$currencyCode = get_value('currency', 'vName', 'eDefault', 'Yes','','true');

		$currencyratio = get_value('currency', 'Ratio', 'vName', $userCurrencyCode,'','true');

    $UserCardData = get_value($tbl_name, 'vPaymayaCustId,vPaymayaToken', $iUserId, $iMemberId);

    $vPaymayaCustId = $UserCardData[0]['vPaymayaCustId'];

    $vPaymayaToken = $UserCardData[0]['vPaymayaToken'];

		//$price = $fAmount*$currencyratio;

		$price = round($fAmount/$currencyratio,2);

		$price_new = $price * 100;

		$price_new = round($price_new);

		if((($vStripeCusId == "" || $vStripeToken == "") && $APP_PAYMENT_METHOD == "Stripe")){

			$returnArr["Action"] = "0";

			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";

			echo json_encode($returnArr);exit;

		}

    if($vBrainTreeToken == "" && $APP_PAYMENT_METHOD == "Braintree"){

			$returnArr["Action"] = "0";

			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";

			echo json_encode($returnArr);exit;

		}

    if((($vPaymayaCustId == "" || $vPaymayaToken == "") && $APP_PAYMENT_METHOD == "Paymaya")){

			$returnArr["Action"] = "0";

			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";

			echo json_encode($returnArr);exit;

		}

    

		$dDate = Date('Y-m-d H:i:s');

		$eFor = 'Deposit';

		$eType = 'Credit';

		$tDescription = "#LBL_AMOUNT_DEBIT#";

		$ePaymentStatus = 'Unsettelled';

		$userAvailableBalance = $generalobj->get_user_available_balance($iMemberId,$eUserType);

    

    if($fAmount == 0 || $fAmount == 0.0 || $price_new < 0.51){ 

     	$returnArr["Action"] = "0";

			$returnArr['message']="LBL_REQUIRED_MINIMUM_AMOUT";

			$returnArr['minValue'] = strval(round(0.51 * $currencyratio,2));

			echo json_encode($returnArr);exit;

    }

    

		if($userAvailableBalance > $price){

			$where = " iTripId = '$iTripId'";

			$data['fTipPrice']= $price;

			$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);

			$vRideNo = get_value('trips', 'vRideNo', 'iTripId',$tripId,'','true');

			$data_wallet['iUserId']=$iMemberId;

			$data_wallet['eUserType']="Rider";

			$data_wallet['iBalance']=$price;

			$data_wallet['eType']="Debit";

			$data_wallet['dDate']=date("Y-m-d H:i:s");

			$data_wallet['iTripId']=$iTripId;

			$data_wallet['eFor']="Booking";

			$data_wallet['ePaymentStatus']="Unsettelled";

			$data_wallet['tDescription']="#LBL_DEBITED_BOOKING#".$vRideNo;

			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'],$data_wallet['eUserType'],$data_wallet['iBalance'],$data_wallet['eType'],$data_wallet['iTripId'],$data_wallet['eFor'],$data_wallet['tDescription'],$data_wallet['ePaymentStatus'],$data_wallet['dDate']);

			//$returnArr["Action"] = "1";

			//echo json_encode($returnArr);exit;

			}else if($price > 0.51){

    			$Charge_Array = array("iFare"=>$price,"price_new"=>$price_new,"currency"=>$currencyCode,"vStripeCusId"=>$vStripeCusId,"description"=>$tDescription,"iTripId"=>$iTripId,"eCancelChargeFailed"=>"No","vBrainTreeToken"=>$vBrainTreeToken,"vRideNo"=>$vRideNo,"iMemberId"=>$iMemberId,"UserType"=>"Passenger");

          $ChargeidArr = ChargeCustomer($Charge_Array,"submitRating");   // function for charge customer

          $ChargeidArrId = $ChargeidArr['id'];

          $status = $ChargeidArr['status'];

          if($status == "success"){

             $where = " iTripId = '$iTripId'";

    				 $data['fTipPrice']= $price;

    				 $id = $obj->MySQLQueryPerform("trips",$data,'update',$where);

          }else{

             $returnArr['Action'] = "0";

      			 $returnArr['message']="LBL_TRANS_FAILED";

      			 echo json_encode($returnArr);exit;

          }

         /* try{

				$charge_create = Stripe_Charge::create(array(

				"amount" => $price_new,

				"currency" => $currencyCode,

				"customer" => $vStripeCusId,

				"description" =>  $tDescription

				));

				$details = json_decode($charge_create);

				$result = get_object_vars($details);

				//echo "<pre>";print_r($result);exit;

				if($result['status']=="succeeded" && $result['paid']=="1"){

					$where = " iTripId = '$iTripId'";

					$data['fTipPrice']= $price;

					$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);

					//$returnArr["Action"] = "1";

					//echo json_encode($returnArr);exit;

					}else{

  					$returnArr['Action'] = "0";

  					$returnArr['message']="LBL_TRANS_FAILED";

  					echo json_encode($returnArr);exit;

				}

				}catch(Exception $e){

				//echo "<pre>";print_r($e);exit;

  				$error3 = $e->getMessage();

          $returnArr["Action"] = "0";

          $returnArr['message']=$error3;

				  //$returnArr['message']="LBL_TRANS_FAILED";

  				echo json_encode($returnArr);exit;

    			}  */

			}else{

  			$returnArr["Action"] = "0";

  			$returnArr['message']="LBL_REQUIRED_MINIMUM_AMOUT";

  			$returnArr['minValue'] = strval(round(0.51 * $currencyratio,2));

  			echo json_encode($returnArr);exit;

		}

		return $iTripId;

	}

	/* changes for rental*/

	function sendTripMessagePushNotification($iFromMemberId, $UserType, $iToMemberId, $iTripId, $tMessage) {

		global $generalobj, $obj, $FIREBASE_API_ACCESS_KEY;

		//$FIREBASE_API_ACCESS_KEY = $generalobj->getConfigurations("configurations", "FIREBASE_API_ACCESS_KEY");

		if($UserType == "Passenger"){

			$tblname = "register_driver";

			$condfield = 'iDriverId';

			$field = 'vFirebaseDeviceToken';

			$Fromtblname = "register_user";

			$Fromcondfield = 'iUserId';

			$pemFileIdentifier = 1;

			$vImageName = "vImgName";

		}else{

			$tblname = "register_user";

			$condfield = 'iUserId';

			$field = 'vFirebaseDeviceToken';

			$Fromtblname = "register_driver";

			$Fromcondfield = 'iDriverId';

			$pemFileIdentifier = 0;

			$vImageName = "vImage";

		}

		$vFirebaseDeviceToken = get_value($tblname, $field, $condfield, $iToMemberId, '', 'true');

		$iGcmRegId = get_value($tblname, "iGcmRegId", $condfield, $iToMemberId, '', 'true');

		$eDeviceType = get_value($tblname, "eDeviceType", $condfield, $iToMemberId, '', 'true');

		$eLogout = get_value($tblname, "eLogout", $condfield, $iToMemberId, '', 'true');

		$MemberName = get_value($Fromtblname, 'vName,vLastName', $Fromcondfield, $iFromMemberId);

		$FromMemberImageName = get_value($Fromtblname, $vImageName, $Fromcondfield, $iFromMemberId, '', 'true');

		$FromMemberName = $MemberName[0]['vName'];

		// ." ".$MemberName[0]['vLastName']

		if($eLogout != "Yes"){

			if($eDeviceType == "Ios"){

				$msg_encode['Msg'] = $tMessage;

				$msg_encode['MsgType'] = "CHAT";

				$msg_encode['iFromMemberId'] = strval($iFromMemberId);

				$msg_encode['iTripId'] = strval($iTripId);

				$msg_encode['FromMemberName'] = strval($FromMemberName);

				$msg_encode['FromMemberImageName'] = strval($FromMemberImageName);

				$msg_encode  = json_encode($msg_encode,JSON_UNESCAPED_UNICODE);

				$deviceTokens_arr_ios = array();

				array_push($deviceTokens_arr_ios, $iGcmRegId);

				sendApplePushNotification($pemFileIdentifier,$deviceTokens_arr_ios,$msg_encode,$tMessage,0);

			}else{

				$registrationIds = (array)$vFirebaseDeviceToken;

				$msg['aps'] = array

				(       

				'iFromMemberId' => $iFromMemberId,

				'iTripId' => $iTripId,

				'FromMemberName' => $FromMemberName,

				'Msg' => $tMessage,

				'MsgType' => "CHAT",

				'FromMemberImageName' => $FromMemberImageName

				);
				send_notification($registrationIds, array ("message" => $msg['aps']),0 );

		
			}

		}

		

		

	}

	function UpdateOtherLanguage($vLabel,$vValue,$vLangCode,$tablename){

		global $generalobj, $obj;

		$sql = "SELECT vCode,vLangCode FROM `language_master` where vCode!='".$vLangCode."' ORDER BY `iDispOrder`";

		$db_master = $obj->MySQLSelect($sql);

		$count_all = count($db_master);

		if($count_all > 0){

			for($i=0;$i<$count_all;$i++) {

				$vCode = $db_master[$i]['vCode'];

				$vGmapCode = $db_master[$i]['vLangCode'];

				$url = 'http://api.mymemory.translated.net/get?q='.urlencode($vValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vGmapCode;

				$result = file_get_contents($url);

				$finalResult = json_decode($result);

				$getText = $finalResult->responseData;

				$resulttext = $getText->translatedText;

				if($resulttext == ""){

					$resulttext = $vValue;

				}

				$sql = "SELECT LanguageLabelId FROM $tablename where vLabel = '".$vLabel."' AND vCode = '".$vCode."'";

				$db_language_label = $obj->MySQLSelect($sql);

				$count = count($db_language_label);

				if($count > 0){

					$where = " LanguageLabelId = '".$db_language_label[0]['LanguageLabelId']."'";

					$data_update['vValue']=$resulttext;

					$obj->MySQLQueryPerform($tablename,$data_update,'update',$where);

				}

			}

		}

		return $count_all;

	}

	function get_currency($from_Currency, $to_Currency, $amount) {

		$forignalamount = $amount;

		$amount = urlencode($amount);

		$from_Currency = urlencode($from_Currency);

		$to_Currency = urlencode($to_Currency);

		//$url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

    $url = "https://finance.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";

		$ch = curl_init();

		$timeout = 0;

		curl_setopt ($ch, CURLOPT_URL, $url);

		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

		//curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$rawdata = curl_exec($ch);

		curl_close($ch);

		$data = explode('bld>', $rawdata);

		$data = explode($to_Currency, $data[1]);

    $ftollprice = round($data[0], 2);

    if($ftollprice == 0 || $ftollprice == 0.00){

      $ftollprice = $amount;

    } 

		//return round($data[0], 2);

    return $ftollprice;

	}

	function Updateuserlocationdatetime($iMemberId,$user_type = "Passenger",$vTimeZone){

		global $generalobj, $obj;

		if($user_type == "Passenger") {

			$tableName = "register_user";

			$iUserId = 'iUserId';

			}else{

			$tableName = "register_driver";

			$iUserId = 'iDriverId';

		}

		$systemTimeZone = date_default_timezone_get();

		$currentdate = @date("Y-m-d H:i:s");

		// $tLocationUpdateDate = converToTz($currentdate,$systemTimeZone,$vTimeZone);

		$tLocationUpdateDate = $currentdate;

		$where = " $iUserId = '$iMemberId' ";

		$Data_update['vTimeZone']=$vTimeZone;

		$Data_update['tLocationUpdateDate']=$tLocationUpdateDate;

		$obj->MySQLQueryPerform($tableName,$Data_update,'update',$where);

		return true;

	}

	

	function getusertripsourcelocations($iMemberId,$type="SourceLocation"){

		global $generalobj, $obj;

    $ssql = "";

		if($type=="SourceLocation"){

			$fields = "tStartLat,tStartLong,tSaddress";

      $ssql .= "";

			}else{

			$fields = "tEndLat,tEndLong,tDaddress";

      $ssql .= "AND eType != 'UberX'";

		}

		

		$sql = "SELECT $fields FROM trips where iUserId = '".$iMemberId."' AND iActive = 'Finished' $ssql ORDER BY iTripId DESC";

		$db_passenger_source = $obj->MySQLSelect($sql);

		

		if(count($db_passenger_source) > 0){

			$db_passenger_source = array_slice($db_passenger_source, 0, 10);

			}else{

			$db_passenger_source = array();

		}

		

		return $db_passenger_source; 

	}

	

	function fetchtripstatustimeinterval(){

		global $generalobj, $obj,$FETCH_TRIP_STATUS_TIME_INTERVAL;

		

		//$FETCH_TRIP_STATUS_TIME_INTERVAL = $generalobj->getConfigurations("configurations", "FETCH_TRIP_STATUS_TIME_INTERVAL");

		$FETCH_TRIP_STATUS_TIME_INTERVAL_ARR = explode("-",$FETCH_TRIP_STATUS_TIME_INTERVAL);

		$FETCH_TRIP_STATUS_TIME_INTERVAL_MIN = $FETCH_TRIP_STATUS_TIME_INTERVAL_ARR[0];   

		$FETCH_TRIP_STATUS_TIME_INTERVAL_MIN = $FETCH_TRIP_STATUS_TIME_INTERVAL_MIN-4;

		if($FETCH_TRIP_STATUS_TIME_INTERVAL_MIN < 15){

			$FETCH_TRIP_STATUS_TIME_INTERVAL_MIN = 15;

		}

		$FETCH_TRIP_STATUS_TIME_INTERVAL_MAX = $FETCH_TRIP_STATUS_TIME_INTERVAL_ARR[1];

		$range = rand($FETCH_TRIP_STATUS_TIME_INTERVAL_MIN,$FETCH_TRIP_STATUS_TIME_INTERVAL_MAX);  

		

		return $range;

	}

	

	function fetchtripstatustimeMAXinterval(){

		global $generalobj, $obj,$FETCH_TRIP_STATUS_TIME_INTERVAL;

		

		//$FETCH_TRIP_STATUS_TIME_INTERVAL = $generalobj->getConfigurations("configurations", "FETCH_TRIP_STATUS_TIME_INTERVAL");

		$FETCH_TRIP_STATUS_TIME_INTERVAL_ARR = explode("-",$FETCH_TRIP_STATUS_TIME_INTERVAL);

		

		$FETCH_TRIP_STATUS_TIME_INTERVAL_MAX = $FETCH_TRIP_STATUS_TIME_INTERVAL_ARR[1];

		

		return $FETCH_TRIP_STATUS_TIME_INTERVAL_MAX;

	}

  function CheckAvailableTimes($str){

    if($str != ""){

      $str = str_replace("00", "12", $str);

    $strArr = explode(",",$str);

    $returnArr = array();

    for($i=0;$i<count($strArr);$i++){

        $number = $strArr[$i]; 

        $numberArr = explode("-",$number);

        $number1 = $numberArr[0];

        $number2 = $numberArr[1]; 

        $number1 = str_pad($number1, 2, '0', STR_PAD_LEFT);

        $number2 = str_pad($number2, 2, '0', STR_PAD_LEFT);

        $finalnumber = $number1."-".$number2;

        $returnArr[] = $finalnumber;  

    }

    $vAvailableTimes = implode(",",$returnArr);

    }else{

      $vAvailableTimes = "";

    }  

    return $vAvailableTimes; 

  }

  

  	function checkRestrictedAreaNew($Address_Array,$DropOff) {

  		//print_r($Address_Array);die;

		global $generalobj, $obj;

		$ssql = "";

		if($DropOff == "No") {

			$ssql.= " AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";

		} else {

			$ssql.= " AND (eRestrictType = 'Drop Off' OR eRestrictType = 'All')";

		}

		if(!empty($Address_Array)){

			$sqlaa = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Allowed'".$ssql;

			$allowed_data = $obj->MySQLSelect($sqlaa);

			

      $allowed_ans = 'No';

			if(!empty($allowed_data)){

				$polygon = array();

				foreach($allowed_data as $key => $val) {

					$latitude = explode(",",$val['tLatitude']);

					$longitude = explode(",",$val['tLongitude']);

					for ($x = 0; $x < count($latitude); $x++) {

						if(!empty($latitude[$x]) || !empty($longitude[$x])) {

							$polygon[$key][] = array($latitude[$x],$longitude[$x]);

						}

					}

					//print_r($polygon[$key]);

					if($polygon[$key]){

						$address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';

						if($address == 'IN'){

							$allowed_ans = 'Yes';

							break;

						}

					}

				}

			} 



			if($allowed_ans == 'No') {

				$sqlas = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Disallowed'".$ssql;

				$restricted_data = $obj->MySQLSelect($sqlas);

				$allowed_ans = 'Yes';

				if(!empty($restricted_data)){

					$polygon_dis = array();

					foreach($restricted_data as $key => $value){

						$latitude = explode(",",$value['tLatitude']);

						$longitude = explode(",",$value['tLongitude']);

						for ($x = 0; $x < count($latitude); $x++) {

							if(!empty($latitude[$x]) || !empty($longitude[$x])) {

								$polygon_dis[$key][] = array($latitude[$x],$longitude[$x]);

							}

						}

						if($polygon_dis[$key]){

							$address_dis = contains($Address_Array,$polygon_dis[$key]) ? 'IN' : 'OUT';

							if($address_dis == 'IN') {

								$allowed_ans = 'No';

								break;

							} 

						}

					}

				}

			}  

		}

		return $allowed_ans;

	}





	function contains($point, $polygon)

	{

	    if($polygon[0] != $polygon[count($polygon)-1])

	        $polygon[count($polygon)] = $polygon[0];

	    $j = 0;

	    $oddNodes = false;

	    $x = $point[1];

	    $y = $point[0];

	    $n = count($polygon);

	    for ($i = 0; $i < $n; $i++)

	    {

	        $j++;

	        if ($j == $n)

	        {

	            $j = 0;

	        }

	        if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >=

	            $y)))

	        {

	            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -

	                $polygon[$i][1]) < $x)

	            {

	                $oddNodes = !$oddNodes;

	            }

	        }

	    }

	    return $oddNodes;

	}

  

  function GetVehicleTypeFromGeoLocation($Address_Array){

		global $generalobj, $obj;

		

    $Vehicle_Str = "-1";     

    if(!empty($Address_Array)){

			$sqlaa = "SELECT * FROM location_master WHERE eStatus='Active' AND eFor = 'VehicleType'";

			$allowed_data = $obj->MySQLSelect($sqlaa);   

			if(!empty($allowed_data)){

				$polygon = array();

				foreach($allowed_data as $key => $val) {

					$latitude = explode(",",$val['tLatitude']);

					$longitude = explode(",",$val['tLongitude']);

					for ($x = 0; $x < count($latitude); $x++) {

						if(!empty($latitude[$x]) || !empty($longitude[$x])) {

							$polygon[$key][] = array($latitude[$x],$longitude[$x]);

						}

					}

					//print_r($polygon[$key]);

					if($polygon[$key]){

						

            $address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';

						if($address == 'IN'){

							$Vehicle_Str .= ",".$val['iLocationId'];

              //break;

						}

					}

				}    

			} 

		}     

		return $Vehicle_Str;

	}

  

  function DisplayBookingDetails($iCabBookingId){

		global $generalobj, $obj;

    $returnArr = array();

    $sql = "SELECT * FROM `cab_booking` WHERE iCabBookingId = '".$iCabBookingId."'";

    $db_booking = $obj->MySQLSelect($sql);

    $serverTimeZone = date_default_timezone_get();

    $db_booking[0]['dBooking_dateOrig'] = converToTz($db_booking[0]['dBooking_date'],$db_booking[0]['vTimeZone'],$serverTimeZone);

    $seldatetime = $db_booking[0]['dBooking_dateOrig'];

    $selecteddate = date("Y-m-d", strtotime($seldatetime));

    $newdate = explode(" ",$seldatetime);

    $time_in_12_hour_format = date("a", strtotime($seldatetime));

    $timearr = explode(":",$newdate[1]);

    $timearr1 = $timearr[0];

    $timearr1 = $timearr1%12;

    $timearr2 = $timearr1+1;

    $number1 = str_pad($timearr1, 2, '0', STR_PAD_LEFT);

    $number2 = str_pad($timearr2, 2, '0', STR_PAD_LEFT);  

    $selectedtime =  $number1."-".$number2." ".$time_in_12_hour_format;

    $scheduletime1 = $timearr[0];

    $scheduletime2 = $scheduletime1+1;

    $scheduletime1 = str_pad($scheduletime1, 2, '0', STR_PAD_LEFT);

    $scheduletime2 = str_pad($scheduletime2, 2, '0', STR_PAD_LEFT);

    $scheduledate =  $selecteddate." ".$scheduletime1."-".$scheduletime2;

    $userId = $db_booking[0]['iUserId'];

    $sql1 = "SELECT vLang,vCurrencyPassenger FROM `register_user` WHERE iUserId='$userId'";

		$row = $obj->MySQLSelect($sql1);

		$lang = $row[0]['vLang'];

		//if($lang == "" || $lang == NULL) { $lang = "EN"; }

    if ($lang == "" || $lang == NULL) {

			$lang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

		}

    $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];

			if($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL){

				$vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes','','true');

		}

		$UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyPassenger);

		$priceRatio = $UserCurrencyData[0]['Ratio'];

		$vSymbol = $UserCurrencyData[0]['vSymbol'];

    $driverId =  $db_booking[0]['iDriverId'];

    $sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '".$driverId."'";

		$db_drv_vehicle = $obj->MySQLSelect($sql);

    $iDriverVehicleId = $db_drv_vehicle[0]['iDriverVehicleId'];

    $iVehicleTypeId = $db_booking[0]['iVehicleTypeId'];

    $sql2 = "SELECT vc.iVehicleCategoryId, vc.iParentId,vc.vCategory_".$lang." as vCategory, vc.vCategoryTitle_".$lang." as vCategoryTitle, vc.tCategoryDesc_".$lang." as tCategoryDesc, vc.ePriceType, vt.vVehicleType_".$lang." as vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM vehicle_category as vc LEFT JOIN vehicle_type AS vt ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE vt.iVehicleTypeId='".$iVehicleTypeId."'";

		$Data = $obj->MySQLSelect($sql2);

    $iParentId = $Data[0]['iParentId'];

    if($iParentId == 0){

		$ePriceType = $Data[0]['ePriceType'];

    }else{ 

    $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 

    } 

    $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";

    $fAmount = 0;

    if($Data[0]['eFareType'] == "Fixed"){

			//$fAmount = $vCurrencySymbol.$vehicleTypeData[0]['fFixedFare'];

      $fAmount = $Data[0]['fFixedFare'];

		}else if($Data[0]['eFareType'] == "Hourly"){

			//$fAmount = $vCurrencySymbol.$vehicleTypeData[0]['fPricePerHour']."/hour";

      $fAmount = $Data[0]['fPricePerHour'];

		}

    $iPrice = $fAmount; 

    if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){

				$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";

				$serviceProData = $obj->MySQLSelect($sqlServicePro);

				if(count($serviceProData) > 0){

					$fAmount = $serviceProData[0]['fAmount'];

				}else{

          $fAmount = $iPrice;

        }

        $iPrice = $fAmount;

		 } 

    $returnArr['selectedtime'] = $selectedtime; // 01-02 am

    $returnArr['selecteddatetime'] = $scheduledate; // 2017-10-25 01-02

    $returnArr['SelectedFareType'] = $Data[0]['eFareType'];

    $returnArr['SelectedQty'] = $db_booking[0]['iQty'];

    $returnArr['SelectedPrice'] = $iPrice;

    $returnArr['SelectedCurrencySymbol'] = $vSymbol;

    $returnArr['SelectedCurrencyRatio'] = $priceRatio;

    $returnArr['SelectedVehicle'] = $Data[0]['vVehicleType'];

    $returnArr['SelectedCategory'] = $Data[0]['vCategory'];

    $returnArr['SelectedCategoryId'] = $Data[0]['iVehicleCategoryId'];

    $returnArr['SelectedCategoryTitle'] = $Data[0]['vCategoryTitle'];

    $returnArr['SelectedCategoryDesc'] = $Data[0]['tCategoryDesc'];

    $returnArr['SelectedAllowQty'] = $Data[0]['eAllowQty'];

    $returnArr['SelectedPriceType'] = $Data[0]['ePriceType'];

    $returnArr['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ALLOW_SERVICE_PROVIDER_AMOUNT;

    return $returnArr;

	}

  function getTripChatDetails($iTripId){

		global $obj, $generalobj, $tconfig, $FIREBASE_DEFAULT_URL, $FIREBASE_DEFAULT_TOKEN, $GOOGLE_SENDER_ID;

    require_once('assets/libraries/firebase/src/firebaseInterface.php');

    require_once('assets/libraries/firebase/src/firebaseLib.php');  

    //$DEFAULT_URL = 'https://ufxv4app.firebaseio.com/';

    //$DEFAULT_TOKEN = 'xcmWvKUsFF9rP7UmZp9qd14powmT1VH8GW1457aO';

    //$DEFAULT_PATH = '835770094542-chat';

    /*$FIREBASE_DEFAULT_URL = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_URL");

    $FIREBASE_DEFAULT_TOKEN = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_TOKEN");

    $GOOGLE_SENDER_ID = $generalobj->getConfigurations("configurations", "GOOGLE_SENDER_ID");            */

    $DEFAULT_PATH = $GOOGLE_SENDER_ID."-chat"; 

    $firebase = new \Firebase\FirebaseLib($FIREBASE_DEFAULT_URL, $FIREBASE_DEFAULT_TOKEN);

    $fetch = $firebase->get($DEFAULT_PATH.'/'.$iTripId.'-Trip'); // reads value from Firebase

    $fetchdeco = json_decode($fetch);

    

    foreach($fetchdeco as $Tripobj){

       $Data['iTripId'] = $Tripobj->iTripId;

       $Data['tMessage'] = $Tripobj->Text;  

       $iUserId = $Tripobj->passengerId;

       $iDriverId = $Tripobj->driverId; 

       $Data['dAddedDate'] = @date("Y-m-d H:i:s");

       $eUserType = $Tripobj->eUserType;

       $Data['eUserType'] = $eUserType;

       $Data['eStatus'] = "Unread";

       $Data['iFromMemberId'] = ($eUserType == "Passenger")? $iUserId :$iDriverId;

       $Data['iToMemberId'] = ($eUserType == "Passenger")? $iDriverId :$iUserId;

       $id = $obj->MySQLQueryPerform("trip_messages",$Data,'insert');

    }

    $delchat = $firebase->delete($DEFAULT_PATH.'/'.$iTripId.'-Trip');        // deletes value from Firebase

		return $iTripId;

  }

  

  function getMemberAverageRating($iMemberId,$eFor="Passenger",$date=""){

		global $generalobj,$obj;

		

		$ssql = "";

    if($eFor == "Passenger") {

			$UserType = "Driver";

      $iUserId = "iUserId";

      $ssql .= "AND tr.iUserId = '".$iMemberId."'";

		} else {

			$UserType = "Passenger";

      $iUserId = "iDriverId";

      $ssql .= "AND tr.iDriverId = '".$iMemberId."'";

		}

    

    if($date != ""){

       $ssql .= " AND tr.tTripRequestDate LIKE '".$date."%' ";

    }

    

    $sqlcount = "SELECT vRating1 FROM ratings_user_driver as rsu LEFT JOIN trips as tr ON rsu.iTripId=tr.iTripId WHERE rsu.eUserType='".$UserType."' AND tr.eHailTrip = 'No' And tr.iActive = 'Finished'".$ssql;

    $dbtriprating = $obj->MySQLSelect($sqlcount); 

    $avgRating = 0;

    $totalRating = 0;

    $count = count($dbtriprating); 

    if(count($dbtriprating) > 0){

			for($i=0;$i<count($dbtriprating);$i++){

          $vRating1 = $dbtriprating[$i]['vRating1'];

          $totalRating = $totalRating + $vRating1;

      }

      

      $avgRating = round(($totalRating/$count),2);

    }

    

		return $avgRating;

	}

  

function checkAllowedAreaNew($Address_Array,$DropOff) {

  		//print_r($Address_Array);die;

		global $generalobj, $obj;

		$ssql = "";

		if($DropOff == "No") {

			$ssql.= " AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";

		} else {

			$ssql.= " AND (eRestrictType = 'Drop Off' OR eRestrictType = 'All')";

		}

		if(!empty($Address_Array)){

			############### Check For Allow Location ######################################

      $sqlaa = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Allowed'".$ssql;

			$allowed_data = $obj->MySQLSelect($sqlaa);

			

      if(count($allowed_data) > 0){

			  $allowed_ans = 'No';

      	$polygon = array();

				foreach($allowed_data as $key => $val) {

					$latitude = explode(",",$val['tLatitude']);

					$longitude = explode(",",$val['tLongitude']);

					for ($x = 0; $x < count($latitude); $x++) {

						if(!empty($latitude[$x]) || !empty($longitude[$x])) {

							$polygon[$key][] = array($latitude[$x],$longitude[$x]);

						}

					}

					//print_r($polygon[$key]);

					if($polygon[$key]){

						$address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';

						if($address == 'IN'){

							$allowed_ans = 'Yes';

							break;

						}

					}

				}   

			}else{

        $allowed_ans = 'Yes';

      }        

      ############### Check For Allow Location ######################################

      ############### Check For DisAllow Location ######################################

      if($allowed_ans == 'Yes'){

         $sqldaa = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Disallowed'".$ssql;

			   $disallowed_data = $obj->MySQLSelect($sqldaa);

         

         if(count($disallowed_data) > 0){

           $allowed_ans = 'Yes';

           $polygon = array();

    			 foreach($disallowed_data as $key => $val) {

    					$latitude = explode(",",$val['tLatitude']);

    					$longitude = explode(",",$val['tLongitude']);

    					for ($x = 0; $x < count($latitude); $x++) {

    						if(!empty($latitude[$x]) || !empty($longitude[$x])) {

    							$polygon[$key][] = array($latitude[$x],$longitude[$x]);

    						}

    					}

    					//print_r($polygon[$key]);

    					if($polygon[$key]){

    						$address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';

    						if($address == 'IN'){

    							$allowed_ans = 'No';

    							break;

    						}

    					}

    			 }   

         }else{

           $allowed_ans = 'Yes';

         }

      }

      ############### Check For DisAllow Location ######################################

		}

		return $allowed_ans;

}

############### Insert Pushnotification Message Into Firebase  ######################################

function InsertMessageIntoFirebase($UserType,$iMemberId,$Message_arr){

		global $obj, $generalobj, $tconfig, $FIREBASE_DEFAULT_URL, $FIREBASE_DEFAULT_TOKEN, $GOOGLE_SENDER_ID;

    require_once('assets/libraries/firebase/src/firebaseInterface.php');

    require_once('assets/libraries/firebase/src/firebaseLib.php');  

    //$DEFAULT_URL = 'https://ufxv4app.firebaseio.com/';

    //$DEFAULT_TOKEN = 'xcmWvKUsFF9rP7UmZp9qd14powmT1VH8GW1457aO';

    //$DEFAULT_PATH = '835770094542-chat';

    /*$FIREBASE_DEFAULT_URL = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_URL");

    $FIREBASE_DEFAULT_TOKEN = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_TOKEN");

    $GOOGLE_SENDER_ID = $generalobj->getConfigurations("configurations", "GOOGLE_SENDER_ID");            */

    $FIREBASE_DEFAULT_URL = "https://cubetaxiplus-app.firebaseio.com/";

    $FIREBASE_DEFAULT_TOKEN = "FlKf2SLG0J015ZHyxz4T69njoYD8ssDFsYEYjm6g";

    $GOOGLE_SENDER_ID = "835770094542";

    $DEFAULT_PATH = $UserType; 

    $firebase = new \Firebase\FirebaseLib($FIREBASE_DEFAULT_URL, $FIREBASE_DEFAULT_TOKEN);

    $insert = $firebase->push($DEFAULT_PATH.'/'.$iMemberId,$Message_arr); // Insert value into Firebase

    $returnJSON  = json_decode($insert);

		return $returnJSON;

}

############### Insert Pushnotification Message Into Firebase Ends ######################################

############### Get User Country Tax ###################################################################

function getMemberCountryTax($iMemberId,$UserType="Passenger"){

		global $generalobj,$obj;

		$returnArr = array();

		if($UserType == "Passenger") {

			$tblname = "register_user";

			$vCountryfield = "vCountry";

			$iUserId = "iUserId";

		} else {

			$tblname = "register_driver";

			$vCountryfield = "vCountry";

			$iUserId = "iDriverId";

		}

    $fTax1 = 0;

    $fTax2 = 0;               

    $sql = "SELECT COALESCE(co.fTax1, '0') as fTax1,COALESCE(co.fTax2, '0') as fTax2 FROM country as co LEFT JOIN $tblname as ru ON co.vCountryCode = ru.$vCountryfield WHERE $iUserId = '".$iMemberId."'";

    $sqlcountryTax = $obj->MySQLSelect($sql);

    if(count($sqlcountryTax) > 0){

      $fTax1 = $sqlcountryTax[0]['fTax1']; 

  		$fTax2 = $sqlcountryTax[0]['fTax2'];

    }

    $returnArr['fTax1'] = $fTax1;  

		$returnArr['fTax2'] = $fTax2;

		return $returnArr;

}

############### Get User Country Tax ###################################################################

############### Check FlatTrip Or Not  ###################################################################

function checkFlatTripnew($Source_point_Address, $Destination_point_Address,$iVehicleTypeId,$iRentalPackageId="0") {

	global $generalobj,$obj;

	$returnArr = array();

	$returnArr['eFlatTrip']="No";

	$returnArr['Flatfare']=0;

	return $returnArr;

}

############### Check FlatTrip Or Not  ###################################################################

############### Get User's  Country Details From TimeZone ####################################################################

function GetUserCounryDetail($iMemberId,$UserType="Passenger",$vTimeZone,$vUserDeviceCountry = ""){

   global $generalobj,$obj,$DEFAULT_COUNTRY_CODE_WEB;

	 $returnArr = array();

   if($UserType == "Passenger") {

			$tblname = "register_user";

			$vCountryfield = "vCountry";

			$iUserId = "iUserId";

	 }else{

			$tblname = "register_driver";

			$vCountryfield = "vCountry";

			$iUserId = "iDriverId";

	 } 

   $returnArr['vDefaultCountry'] = '';  

	 $returnArr['vDefaultCountryCode'] = '';

   $returnArr['vDefaultPhoneCode'] = '';

   $sql = "SELECT vCountry as vDefaultCountry, vCountryCode as vDefaultCountryCode, vPhoneCode as vDefaultPhoneCode FROM country WHERE vTimeZone = '".$vTimeZone."' AND eStatus = 'Active'";

   $sqlcountryCode = $obj->MySQLSelect($sql);

   if(count($sqlcountryCode) > 0){

      $returnArr = $sqlcountryCode[0]; 

   }else{

     if($vUserDeviceCountry != ""){

       $vUserDeviceCountry = strtoupper($vUserDeviceCountry);

       $sql = "SELECT vCountry as vDefaultCountry, vCountryCode as vDefaultCountryCode, vPhoneCode as vDefaultPhoneCode FROM country WHERE eStatus = 'Active' AND  vCountryCode = '".$vUserDeviceCountry."'";

       $sqlusercountryCode = $obj->MySQLSelect($sql);

       if(count($sqlusercountryCode) > 0){

          $returnArr = $sqlusercountryCode[0];

       }else{

          $sql = "SELECT vCountry as vDefaultCountry, vCountryCode as vDefaultCountryCode, vPhoneCode as vDefaultPhoneCode FROM country WHERE eStatus = 'Active' AND vCountryCode = '".$DEFAULT_COUNTRY_CODE_WEB."'";

          $sqlcountryCode = $obj->MySQLSelect($sql);

          $returnArr = $sqlcountryCode[0];

       }

     }else{

       $sql = "SELECT vCountry as vDefaultCountry, vCountryCode as vDefaultCountryCode, vPhoneCode as vDefaultPhoneCode FROM country WHERE eStatus = 'Active' AND  vCountryCode = '".$DEFAULT_COUNTRY_CODE_WEB."'";

       $sqlcountryCode = $obj->MySQLSelect($sql);

       $returnArr = $sqlcountryCode[0];

     }

   }

   

   return $returnArr; 

}

############### Get User's  Country Details From TimeZone  ###################################################################

############### Get User  Country's Police Number   ###################################################################

function getMemberCountryPoliceNumber($iMemberId,$UserType="Passenger",$vCountry){

		global $generalobj,$obj,$SITE_POLICE_CONTROL_NUMBER;

		

  	if($vCountry != ""){

    	if($UserType == "Passenger") {

  			$tblname = "register_user";

  			$vCountryfield = "vCountry";

  			$iUserId = "iUserId";

  		} else {

  			$tblname = "register_driver";

  			$vCountryfield = "vCountry";

  			$iUserId = "iDriverId";

  		}

      

      $sql = "SELECT co.vEmergencycode FROM country as co LEFT JOIN $tblname as rd ON co.vCountryCode = rd.$vCountryfield WHERE $iUserId = '".$iMemberId."'";

      $db_sql = $obj->MySQLSelect($sql);

      $Country_Police_Number = $db_sql[0]['vEmergencycode']; 

  		

  		if($Country_Police_Number == "" || $Country_Police_Number == NULL){

  			$Country_Police_Number = $SITE_POLICE_CONTROL_NUMBER;

  		}

    }else{

      $Country_Police_Number = $SITE_POLICE_CONTROL_NUMBER;

    }  

      

		return $Country_Police_Number;

}

############### Get User  Country's Police Number   ###################################################################

########################### Get Trip Waiting Fee    ###################################################################

function getTripWaitingFee($iTripId){

		global $generalobj,$obj;

    

    $sql = "SELECT tStartDate,tDriverArrivedDate,iVehicleTypeId,vTripPaymentMode,eHailTrip,iWaitingFeeTimeLimit,eType,iRentalPackageId FROM trips WHERE iTripId='".$iTripId."'";

		$tripdata = $obj->MySQLSelect($sql);

    

    $startDate=$tripdata[0]['tStartDate'];

    if($startDate=="0000-00-00 00:00:00"){

      $startDate=@date("Y-m-d H:i:s");

    }    

    $eFlatTrip=$tripdata[0]['eFlatTrip'];

    $tDriverArrivedDate=$tripdata[0]['tDriverArrivedDate'];

    $waiting_time_diff = strtotime($startDate) - strtotime($tDriverArrivedDate);

    $waitingTime = ceil($waiting_time_diff / 60);   

		$vehicleTypeID=$tripdata[0]['iVehicleTypeId'];

    $eHailTrip=$tripdata[0]['eHailTrip'];

    $eType=$tripdata[0]['eType'];

    $iRentalPackageId=$tripdata[0]['iRentalPackageId'];

    $sql = "SELECT fWaitingFees FROM vehicle_type WHERE iVehicleTypeId='".$vehicleTypeID."'";

		$tripvehicledata = $obj->MySQLSelect($sql);

    $fWaitingFees=$tripvehicledata[0]['fWaitingFees'];

    $iWaitingFeeTimeLimit=$tripdata[0]['iWaitingFeeTimeLimit'];

    if($waitingTime > $iWaitingFeeTimeLimit){

      $waitingTime = $waitingTime-$iWaitingFeeTimeLimit;

      $fWaitingFees = $fWaitingFees * $waitingTime;

      $fWaitingFees= round($fWaitingFees,2);

    }else{

      $fWaitingFees = 0;

    }

    

    if($eHailTrip == "Yes"){

      $fWaitingFees = 0;

    }

    

    return $fWaitingFees; 

}

########################### Get Trip Waiting Fee    ###################################################################

########################### Update Trip Outstanding Amount#############################################################

function UpdateTripOutstandingAmount($iTripId,$ePaidByPassenger = "No",$ePaidToDriver = "No")

{

  global $generalobj,$obj;

    

  $sql = "SELECT iUserId,iDriverId,fCancellationFare,fWalletDebit,vTripPaymentMode,vRideNo,iVehicleTypeId FROM trips WHERE iTripId='".$iTripId."'";

	$tripdata = $obj->MySQLSelect($sql);

  $iUserId=$tripdata[0]['iUserId'];

  $iDriverId=$tripdata[0]['iDriverId'];

  $fCancellationFare=$tripdata[0]['fCancellationFare'];

  $fWalletDebit=$tripdata[0]['fWalletDebit'];

  $vTripPaymentMode=$tripdata[0]['vTripPaymentMode'];

  $iVehicleTypeId=$tripdata[0]['iVehicleTypeId'];

  $fCommision = get_value('vehicle_type', 'fCommision', 'iVehicleTypeId',$iVehicleTypeId,'','true');   

  

  if($fCancellationFare > 0){

    $fPendingAmount = $fCancellationFare;

  }else{

     $fPendingAmount = 0;

  }

  

  if($fPendingAmount < 0){

    $fPendingAmount = 0;

  } 

  

  ## Calculate Driver's Commission and PendingAmount ##

  $DriverTotalAmount = $fWalletDebit+$fCancellationFare;

  $Site_Commision = round((($DriverTotalAmount * $fCommision) / 100), 2);

  $fDriverPendingAmount = $DriverTotalAmount - $Site_Commision;  

  $fTripGenerateFare =  $fWalletDebit+$fCancellationFare;

  $iFare = $fCancellationFare;

  ## Calculate Driver's Commission and PendingAmount ## 

  

  $Data_trip_OutstandingAmount["iTripId"] = $iTripId;

  $Data_trip_OutstandingAmount["iUserId"] = $iUserId;

  $Data_trip_OutstandingAmount["iDriverId"] = $iDriverId;

  $Data_trip_OutstandingAmount["fWalletDebit"] = $fWalletDebit;

  $Data_trip_OutstandingAmount["fCancellationFare"] = $fCancellationFare;

  $Data_trip_OutstandingAmount["vTripPaymentMode"] = $vTripPaymentMode;

  $Data_trip_OutstandingAmount["ePaidByPassenger"] = $ePaidByPassenger;

  $Data_trip_OutstandingAmount["ePaidToDriver"] = $ePaidToDriver;

  $Data_trip_OutstandingAmount["fPendingAmount"] = $fPendingAmount;

  $Data_trip_OutstandingAmount["fCommision"] = $Site_Commision;

  $Data_trip_OutstandingAmount["fDriverPendingAmount"] = $fDriverPendingAmount;

  if($ePaidByPassenger == "Yes"){

     $Data_trip_OutstandingAmount["vTripAdjusmentId"] = $iTripId;

  }

  $currencyList = get_value('currency', '*', 'eStatus', 'Active');

			

	for($i=0;$i<count($currencyList);$i++){

		$currencyCode = $currencyList[$i]['vName'];

		$Data_trip_OutstandingAmount['fRatio_'.$currencyCode] = $currencyList[$i]['Ratio'];

	}

  

  $iTripOutstandId = $obj->MySQLQueryPerform("trip_outstanding_amount", $Data_trip_OutstandingAmount, 'insert');

  

  if($iTripOutstandId > 0 && $ePaidByPassenger == "No" && $fPendingAmount > 0){

     $updateQuery = "UPDATE register_user set fTripsOutStandingAmount = fTripsOutStandingAmount+'".$fPendingAmount."' WHERE iUserId = ".$iUserId;

		 $obj->sql_query($updateQuery);

  }

  

  ### Debit  User Wallet & Credit Driver Wallet ###

  if($fWalletDebit > 0){

    ### Debit  User Wallet  ### 

    $vRideNo=$tripdata[0]['vRideNo'];

    $data_user_wallet['iUserId'] = $iUserId;

		$data_user_wallet['eUserType'] = "Rider";

		$data_user_wallet['iBalance'] = $fWalletDebit;

		$data_user_wallet['eType'] = "Debit";

		$data_user_wallet['dDate'] = @date("Y-m-d H:i:s");

		$data_user_wallet['iTripId'] = $iTripId;

		$data_user_wallet['eFor'] = "Booking";

		$data_user_wallet['ePaymentStatus'] = "Unsettelled";

		$data_user_wallet['tDescription'] = "#LBL_DEBITED_CANCELLED_BOOKING#" ." ". $vRideNo;

    $generalobj->InsertIntoUserWallet($data_user_wallet['iUserId'], $data_user_wallet['eUserType'], $data_user_wallet['iBalance'], $data_user_wallet['eType'], $data_user_wallet['iTripId'], $data_user_wallet['eFor'], $data_user_wallet['tDescription'], $data_user_wallet['ePaymentStatus'], $data_user_wallet['dDate']);

    ### Debit  User Wallet  ###

    ### Credit Driver Wallet ###

    if($fCancellationFare == 0){

      $fWalletDebit = $fWalletDebit - $Site_Commision; 

    }

    $data_driver_wallet['iUserId'] = $iDriverId;

		$data_driver_wallet['eUserType'] = "Driver";

		$data_driver_wallet['iBalance'] = $fWalletDebit;

		$data_driver_wallet['eType'] = "Credit";

		$data_driver_wallet['dDate'] = @date("Y-m-d H:i:s");

		$data_driver_wallet['iTripId'] = $iTripId;

		$data_driver_wallet['eFor'] = "Deposit";

		$data_driver_wallet['ePaymentStatus'] = "Unsettelled";

		$data_driver_wallet['tDescription'] = "#LBL_AMOUNT_CANCELTRIP_CREDIT#" ." ". $vRideNo;

    $generalobj->InsertIntoUserWallet($data_driver_wallet['iUserId'], $data_driver_wallet['eUserType'], $data_driver_wallet['iBalance'], $data_driver_wallet['eType'], $data_driver_wallet['iTripId'], $data_driver_wallet['eFor'], $data_driver_wallet['tDescription'], $data_driver_wallet['ePaymentStatus'], $data_driver_wallet['dDate']);

    ### Credit Driver Wallet ###

  }

  ### Debit  User Wallet & Credit Driver Wallet ###

  

  if($ePaidByPassenger == "Yes" && $ePaidToDriver == "Yes")

  {

    $updateQuery = "UPDATE trips set iFare = '".$iFare."',fTripGenerateFare = '".$fTripGenerateFare."',ePaymentCollect = 'Yes',eDriverPaymentStatus = 'Settelled',fCommision = '".$Site_Commision."' WHERE iTripId = ".$iTripId;

		$obj->sql_query($updateQuery);

  }else{

    $updateQuery = "UPDATE trips set iFare = '".$iFare."',fTripGenerateFare = '".$fTripGenerateFare."',fCommision = '".$Site_Commision."' WHERE iTripId = ".$iTripId;

		$obj->sql_query($updateQuery);

  }

                           

   

  return $iTripOutstandId;  

}                                      

########################### Update Trip Outstanding Amount#############################################################

########################### Get Passenger Outstanding Amount#############################################################

function GetPassengerOutstandingAmount($iUserId){

  global $generalobj,$obj;

  

  $sql = "SELECT SUM(fPendingAmount) as fPendingAmount FROM trip_outstanding_amount WHERE iUserId='".$iUserId."' AND ePaidByPassenger = 'No'";

	$tripoutstandingdata = $obj->MySQLSelect($sql);

  $fPendingAmount = $tripoutstandingdata[0]['fPendingAmount'];

  

  if($fPendingAmount == "" || $fPendingAmount == NULL){ 

     $fPendingAmount = 0;

  }

   

  return $fPendingAmount; 

}

########################### Get Passenger  Outstanding Amount#############################################################

########################### GenerateCustomer App Payment Method Wise #############################################################

function GenerateCustomer($Data){

  global $generalobj,$obj,$STRIPE_SECRET_KEY,$STRIPE_PUBLISH_KEY,$gateway,$BRAINTREE_TOKEN_KEY,$BRAINTREE_ENVIRONMENT,$BRAINTREE_MERCHANT_ID,$BRAINTREE_PUBLIC_KEY,$BRAINTREE_PRIVATE_KEY,$BRAINTREE_CHARGE_AMOUNT,$PAYMAYA_API_URL,$tconfig,$XENDIT_PUBLIC_KEY,$XENDIT_SECRET_KEY,$APP_PAYMENT_METHOD;  // Stripe,Braintree

  foreach($Data as $key => $value) 

	{

		//$value = urldecode(stripslashes($value));

    $$key = $value;

	}

  

  if($UserType == "Passenger"){

			$tbl_name = "register_user";

			$vEmail = "vEmail";

			$iMemberId = "iUserId";

			$eUserType = "Rider";

      $eMemberType = "Passenger";

      $UserDetailPaymaya = get_value($tbl_name, 'vName,vLastName,vEmail,vPhone,vPhoneCode as phonecode,vPaymayaCustId,'.$vEmail.' as memberemail', $iMemberId, $iUserId);

	$eMemberTypeENum = 'passenger';

	}else{

	$eMemberTypeENum = 'driver';

	$tbl_name = "register_driver";

	$vEmail = "vEmail";

	$iMemberId = "iDriverId";

	$eUserType = "Driver";

      $eMemberType = "Driver";

      $UserDetailPaymaya = get_value($tbl_name, 'vName,vLastName,vEmail,vPhone,vCode as phonecode,vPaymayaCustId,'.$vEmail.' as memberemail', $iMemberId, $iUserId);

	} 





// brainLog(' CADASTRO GenerateCustomer - sql ::: ', $data);



$sqlp = " SELECT tokenizer_token FROM tokenizer WHERE tokenizer_id = '$iUserId' AND tokenizer_type = '$eMemberTypeENum' ";

$tokenizer_token = $obj->MySQLSelect($sqlp);



// brainLog(' CADASTRO REDE (antes) - sql ::: ', $sqlp);



if (count($tokenizer_token)>=1)

	$APP_PAYMENT_METHOD = "Rede";



// brainLog(' CADASTRO APP_PAYMENT_METHOD :::  ', $APP_PAYMENT_METHOD);



  // BRAINATWORK::REDE - Cadastro do cartão / Zero Dollar 

  if($APP_PAYMENT_METHOD == "Rede"){



			// $arr = get_defined_vars();

			// brainLog(' REDE - APP_PAYMENT_METHOD ', $arr);

			// $returnArr['Action'] = "0";

			// $returnArr['message']= 'Teste Diogo ';

			// brainLog('  APP_PAYMENT_METHOD eMemberType  ', $eMemberType);

			// $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

			

			if($eMemberType == "Passenger"){

				$profileData =  getPassengerDetailInfo($iUserId);

			}else{

				$profileData =  getDriverDetailInfo($iUserId);

			}



			$tokenizer_token = $tokenizer_token[0]['tokenizer_token'];



			require_once($_SERVER[DOCUMENT_ROOT] . "/app/BrainatworkCry.php");



		    $ce = new Cry($iUserId);

		    $token = $ce->d($tokenizer_token);

		    $token = json_decode($token, true);



			$token[rd_cn] = $NameCardBox;

			$token[rd_ci] = $CpfNo;

			$token[rd_cc] = str_replace(' ', '', $token[rd_cc]);



				    $Data_update = json_encode($token);

				    $tokenizer_token = $ce->e($Data_update);



				    $sql = "INSERT INTO tokenizer SET tokenizer_id='$iUserId', tokenizer_type='$eMemberTypeENum', tokenizer_token='$tokenizer_token', tokenizer_date=now() ON DUPLICATE KEY UPDATE tokenizer_token='$tokenizer_token', tokenizer_date=now();";

				    $insert = $obj->sql_query($sql);



		  	foreach($token as $key => $value) 

				$$key = $value;



		  	require_once($_SERVER[DOCUMENT_ROOT] . "/gatewayRede.php");

			$transaction = (new \Rede\Transaction(0,'z'.time()))->creditCard(

			    $rd_cc, $rd_cs, $rd_cm, $rd_ca, $rd_cn

			);



			// brainLog(' token  ', $token);

			// brainLog(' inicio  ', $transaction);

			// brainLog(' store  ', $store);



			// Autoriza a transação

			try{

				$transaction = (new \Rede\eRede($store))->create($transaction);

				// brainLog(' Certo  ', $transaction);   	

			}catch(Exception $e){

				$errMsg = $e->getMessage();

				brainLog(' Erro Retorno Rede  ', $errMsg);   			

			}	



			if ($transaction->getReturnCode() != '174') {

				// 175 zero dolar denied

			    // 00 printf("Transação autorizada com sucesso; tid=%s\n", $transaction->getTid());

				$returnArr['Action'] = "0";

				$returnArr['message']= 'Cartão não autorizado ou inválido, tente novamente';

			    echo json_encode($returnArr);

				exit;

			}



			$where = " $iMemberId = '$iUserId'";

			$updateData['vCreditCard']= 'XXXX XXXX XXXX ' . substr($rd_cc, -4);

			$updateData['CpfNo']=$CpfNo;

			$updateData['NameCardBox']='Rede';



		  	 $updateData['vStripeToken']= 'tok_'.md5($iUserId);

		  	 $updateData['vStripeCusId']= 'cus_'.md5($iUserId);

		  	 $updateData['NameCardBox']=$NameCardBox;

			 $updateData['vGeoCityRider']=$vGeoCityRider;



			$id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);



			$returnArr['Action'] = "1";

			$returnArr['message'] = $profileData;

			$returnArr['message1'] = 'Cartão aceito e cadastro com sucesso';



			echo json_encode($returnArr); exit;



	  }	      

	  else if($APP_PAYMENT_METHOD == "Stripe"){



      require_once('assets/libraries/stripe/config.php');

	    require_once('assets/libraries/stripe/stripe-php-2.1.4/lib/Stripe.php');

      $UserDetail = get_value($tbl_name, 'vStripeCusId,'.$vEmail.' as memberemail', $iMemberId, $iUserId);

  		$vEmail = $UserDetail[0]['memberemail'];

  		$vStripeCusId = $UserDetail[0]['vStripeCusId'];

      

      try{

    			if($vStripeCusId  != ""){

    				$customer 	= Stripe_Customer::retrieve($vStripeCusId);

    				$sources = $customer -> sources;

    				$stripeData = $sources -> data;

    				

    				if(count($stripeData) >0 && $stripeData[0]['id'] != ''){

    					$customer->sources->retrieve($stripeData[0]['id'])->delete();

    				}

    				

    				$card = $customer->sources->create(array("source" => $vStripeToken));

    				

    			 }else{

    				try{

    					$customer 	= Stripe_Customer::create(array( "source" => $vStripeToken, "email" => $vEmail));

    					$vStripeCusId = $customer->id;

    					} catch (Exception $e) {

          				$error3 = $e->getMessage();

          				$returnArr['Action'] = "0";

          				$returnArr['message']=$error3;

          				echo json_encode($returnArr);exit;

    				  }

    			}

    	}catch(Exception $e){

    			$errMsg = $e->getMessage();

    			if (strpos($errMsg, 'No such customer') !== false) {

    				try{

    					$customer 	= Stripe_Customer::create(array( "source" => $vStripeToken, "email" => $vEmail));

    					}catch(Exception $e){

    					$error3 = $e->getMessage();

    					$returnArr['Action'] = "0";

    					$returnArr['message']=$error3;

    					

    					echo json_encode($returnArr);exit;

    				}

    				

    				$vStripeCusId = $customer->id;

    				}else{

    				$returnArr['Action'] = "0";

    				$returnArr['message']=$errMsg;

    				

    				echo json_encode($returnArr);exit;

    			}

		 }

     

     

     $where = " $iMemberId = '$iUserId'";

  	 $updateData['vStripeToken']=$vStripeToken;

  	 $updateData['vStripeCusId']=$vStripeCusId;

  	 $updateData['vCreditCard']=$CardNo;

  	 $updateData['CpfNo']=$CpfNo;

  	 $updateData['NameCardBox']=$NameCardBox;

	 $updateData['vGeoCityRider']=$vGeoCityRider;

  		

  	 $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

  	 if($eMemberType == "Passenger"){

  			$profileData =  getPassengerDetailInfo($iUserId);

  	 }else{

  			$profileData =  getDriverDetailInfo($iUserId);

  	 }

  		

  	 if($id >0){

  			$returnArr['Action'] = "1";

  			$returnArr['message'] = $profileData;

  		}else{

  			$returnArr['Action'] = "0";

  			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

  	 }

  }else if($APP_PAYMENT_METHOD == "Braintree"){

     require_once('assets/libraries/braintree/lib/Braintree.php');

     $UserDetail = get_value($tbl_name, 'vBrainTreeCustId,vName,vLastName,'.$vEmail.' as memberemail', $iMemberId, $iUserId);

  	 $vEmail = $UserDetail[0]['memberemail'];

  	 $vBrainTreeCustId = $UserDetail[0]['vBrainTreeCustId'];

     $vName = $UserDetail[0]['vName'];

     $vLastName = $UserDetail[0]['vLastName'];

     

     try{

  			if($vBrainTreeCustId  != ""){

           ## Charge First Transaction Amount For existing customer##

            try{

               $charge = $gateway->transaction()->sale([

                            'amount' => $BRAINTREE_CHARGE_AMOUNT,

                            'paymentMethodNonce' => $paymentMethodNonce,

                            'customerId' => $vBrainTreeCustId,

                            'options' => [

                              'storeInVaultOnSuccess' => true,

                            ]

                          ]);

                

                $result = $charge->success;

                if($result == 1){

                   $transaction_id = $charge->transaction->id;

                   $creditCardArr = $charge->transaction->creditCard;

                   $paypalArr = $charge->transaction->paypal;

                   $payerEmail = $paypalArr['payerEmail']; 

                   if($payerEmail != ""){

                      $vBrainTreeCustEmail = $payerEmail; 

                      $vBrainTreeToken = $paypalArr['token'];

                      $CardNo = "";

                      $message1 = "LBL_SUCESS_ADD_PAYPAL_BRAINTREE_TXT";

                   }else{

                      $vBrainTreeCustEmail = "";

                      $vBrainTreeToken = $creditCardArr['token'];

                      $CardNo = "XXXXXXXXXXXX".$creditCardArr['last4'];

                      $message1 = "LBL_SUCESS_ADD_BRAINTREE_TXT";

                   }

                   $generalobj->InsertIntoUserWallet($iUserId,$eUserType,$BRAINTREE_CHARGE_AMOUNT,'Credit',0,'Deposit','#LBL_AMOUNT_CREDIT#','Unsettelled',Date('Y-m-d H:i:s'));

                }else{

                   $returnArr['Action'] = "0";

  			           $returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

                   echo json_encode($returnArr);exit;

                }          

            }catch(Exception $e){

               $error3 = $e->getMessage();

      				 $returnArr['Action'] = "0";

      				 $returnArr['message']=$error3;

      				 echo json_encode($returnArr);exit;

            }          

            ## Charge First Transaction Amount For existing customer##  

  			}else{

  				try{

  					$customer 	=  $gateway->customer()->create([

                        'firstName' => $vName,

                        'lastName' => $vLastName,

                        'email' => $vEmail,

                      ]);

  					$vBrainTreeCustId = $customer->customer->id;

            ## Charge First Transaction Amount ##

            try{

               $charge = $gateway->transaction()->sale([

                            'amount' => $BRAINTREE_CHARGE_AMOUNT,

                            'paymentMethodNonce' => $paymentMethodNonce,

                            'customerId' => $vBrainTreeCustId,

                            'options' => [

                              'storeInVaultOnSuccess' => true,

                            ]

                          ]);

                  

                $result = $charge->success;

                if($result == 1){

                   $transaction_id = $charge->transaction->id;

                   $creditCardArr = $charge->transaction->creditCard;

                   $paypalArr = $charge->transaction->paypal;

                   $payerEmail = $paypalArr['payerEmail']; 

                   if($payerEmail != ""){

                      $vBrainTreeCustEmail = $payerEmail; 

                      $vBrainTreeToken = $paypalArr['token']; 

                      $CardNo = "";

                      $message1 = "LBL_SUCESS_ADD_PAYPAL_BRAINTREE_TXT";

                   }else{

                      $vBrainTreeCustEmail = "";

                      $vBrainTreeToken = $creditCardArr['token'];

                      $CardNo = "XXXXXXXXXXXX".$creditCardArr['last4'];

                      $message1 = "LBL_SUCESS_ADD_BRAINTREE_TXT";

                   }

                   $generalobj->InsertIntoUserWallet($iUserId,$eUserType,$BRAINTREE_CHARGE_AMOUNT,'Credit',0,'Deposit','#LBL_AMOUNT_CREDIT#','Unsettelled',Date('Y-m-d H:i:s'));

                }else{

                   $returnArr['Action'] = "0";

  			           $returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

                   echo json_encode($returnArr);exit;

                }          

                          

            }catch(Exception $e){

               $error3 = $e->getMessage();

      				 $returnArr['Action'] = "0";

      				 $returnArr['message']=$error3;

      				 echo json_encode($returnArr);exit;

            }          

            ## Charge First Transaction Amount ##          

  				

          } catch (Exception $e) {

        				$error3 = $e->getMessage();

        				$returnArr['Action'] = "0";

        				$returnArr['message']=$error3;

        				echo json_encode($returnArr);exit;

  				}

  			}

  		}catch(Exception $e){

  			$errMsg = $e->getMessage();

  		  $returnArr['Action'] = "0";

  			$returnArr['message']=$errMsg;

  				

  			echo json_encode($returnArr);exit;

  		}

      

      $where = " $iMemberId = '$iUserId'";

  		$updateData['vBrainTreeToken']=$vBrainTreeToken;

      $updateData['vBrainTreeCustEmail']=$vBrainTreeCustEmail;

  		$updateData['vBrainTreeCustId']=$vBrainTreeCustId;

  		$updateData['vCreditCard']=$CardNo;

        $updateData['CpfNo']=$CpfNo;

        $updateData['NameCardBox']=$NameCardBox;

		$updateData['vGeoCityRider']=$vGeoCityRider;



      $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

      if($eMemberType == "Passenger"){

  			$profileData =  getPassengerDetailInfo($iUserId);

    	}else{

   			$profileData =  getDriverDetailInfo($iUserId);

    	}

    		

    	if($id >0){

    			$returnArr['Action'] = "1";

    			$returnArr['message'] = $profileData;

          $returnArr['message1'] = $message1;

    	}else{

    			$returnArr['Action'] = "0";

    			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

    	}

  }else if($APP_PAYMENT_METHOD == "Paymaya"){

     $vName = $UserDetailPaymaya[0]['vName'];

     $vLastName = $UserDetailPaymaya[0]['vLastName'];

     $vPhone = $UserDetailPaymaya[0]['vPhone'];

     $phonecode = $UserDetailPaymaya[0]['phonecode'];

     $phone = "+".$phonecode.$vPhone;

     $vEmail = $UserDetailPaymaya[0]['memberemail'];

     $vPaymayaCustId = $UserDetailPaymaya[0]['vPaymayaCustId'];

     

     if($vPaymayaCustId == ""){

      $POST_URL = $PAYMAYA_API_URL."payments/v1/customers";

      $postdata =  array('firstName' => $vName,'lastName' => $vLastName,'contact' => array('phone' => $phone,'email' => $vEmail));

      

      $result = check_paymaya_api($POST_URL,$postdata);

      $vPaymayaCustId = $result['id'];

      if($vPaymayaCustId != ""){

        ## Vault a Card ##                                                                                              

        $postdata_vault =  array('paymentTokenId' => $vPaymayaToken,'isDefault' => true,'redirectUrl' => array('success' => $tconfig['tsite_url'].'/assets/libraries/paymaya/success.php','failure' => $tconfig['tsite_url'].'/assets/libraries/paymaya/failure.php','cancel' => $tconfig['tsite_url'].'/assets/libraries/paymaya/cancel.php'));        

        $POST_URL_Vault = $PAYMAYA_API_URL."payments/v1/customers/".$vPaymayaCustId."/cards";

        $result_vault = check_paymaya_api($POST_URL_Vault,$postdata_vault);

        $verificationUrl = $result_vault['verificationUrl'];

        if($verificationUrl == "" || $verificationUrl == NULL){

          $error = $result_vault['message'];

          $returnArr['Action'] = "0";

  				$returnArr['message']=$error;

  				echo json_encode($returnArr);exit;

        }

        ## Vault a Card ##         

        $updateData['vPaymayaCustId']=$vPaymayaCustId;        

      }else{

        $error = $result['message'];

        $returnArr['Action'] = "0";

				$returnArr['message']=$error;

				echo json_encode($returnArr);exit;

      } 

    }else{

      ## Vault a Card ##                                                                                              

      $postdata_vault =  array('paymentTokenId' => $vPaymayaToken,'isDefault' => true,'redirectUrl' => array('success' => $tconfig['tsite_url'].'/assets/libraries/paymaya/success.php','failure' => $tconfig['tsite_url'].'/assets/libraries/paymaya/failure.php','cancel' => $tconfig['tsite_url'].'/assets/libraries/paymaya/cancel.php'));        

      $POST_URL_Vault = $PAYMAYA_API_URL."payments/v1/customers/".$vPaymayaCustId."/cards";

      $result_vault = check_paymaya_api($POST_URL_Vault,$postdata_vault);

      $verificationUrl = $result_vault['verificationUrl'];

      if($verificationUrl == "" || $verificationUrl == NULL){

        $error = $result_vault['message'];

        $returnArr['Action'] = "0";

				$returnArr['message']=$error;

				echo json_encode($returnArr);exit;

      }

      ## Vault a Card ## 

    }

    

    $where = " $iMemberId = '$iUserId'";

		$updateData['vCreditCard']=$CardNo;

		$updateData['CpfNo']=$CpfNo;

		$updateData['NameCardBox']=$NameCardBox;

		$updateData['vGeoCityRider']=$vGeoCityRider;

		

    $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

		

		if($id >0){

			$returnArr['Action'] = "1";

			$returnArr['message'] = $verificationUrl;

		}else{

			$returnArr['Action'] = "0";

			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

		}

  }else if($APP_PAYMENT_METHOD == "Omise"){

      require_once('assets/libraries/omise/config.php');

	    $UserDetail = get_value($tbl_name, 'vOmiseCustId,vOmiseToken,'.$vEmail.' as memberemail', $iMemberId, $iUserId);

  		$vEmail = $UserDetail[0]['memberemail'];

  		$vOmiseCustId = $UserDetail[0]['vOmiseCustId'];

      $vOldOmiseToken = $UserDetail[0]['vOmiseToken'];

         

      try{

    			if($vOmiseCustId  != ""){

            //$customer = OmiseCustomer::retrieve($vOmiseCustId);

            //$card = $customer->getCards()->retrieve($vOldOmiseToken);

            //$card->destroy();

            

            //$card->isDestroyed(); # => true

          

            /*$customer = OmiseCustomer::create(array(

                'email' => $vEmail,

                'description' => $eMemberType."_".$iUserId,

                'card' => $vOmiseToken

              )); */

            $customer = OmiseCustomer::retrieve($vOmiseCustId);

            $customer->update(array(

              'card' => $vOmiseToken

            ));

            

            $customer1 = OmiseCustomer::retrieve($vOmiseCustId);

            $cards = $customer1->getCards(); 

            $cardArr = $cards['data'];

            $lastcardArr = end($cardArr); 

            

            $vOmiseCardId = $lastcardArr['id'];

            $LastFour = "XXXXXXXXXXXX".$lastcardArr['last_digits'];

    				    

            //$vOmiseCardId = $customer['default_card'];

            //$LastFour = "XXXXXXXXXXXX".$customer['cards']['data'][0]['last_digits'];

    				

    		  }else{

    				try{

    					     

                $customer = OmiseCustomer::create(array(

                    'email' => $vEmail,

                    'description' => $eMemberType."_".$iUserId,

                    'card' => $vOmiseToken

                  ));

        				    

                $vOmiseCustId = $customer['id'];

                $vOmiseCardId = $customer['default_card'];

    					  $LastFour = "XXXXXXXXXXXX".$customer['cards']['data'][0]['last_digits'];

              

              }catch(Exception $e){

          				$error3 = $e->getMessage();

          				$returnArr['Action'] = "0";

          				$returnArr['message']=$error3;

          				echo json_encode($returnArr);exit;

    				  }

    			}

    	}catch(Exception $e){

    			$errMsg = $e->getMessage();

          $returnArr['Action'] = "0";

    			$returnArr['message']=$errMsg;

          echo json_encode($returnArr);exit;

		 }

     

     

     $where = " $iMemberId = '$iUserId'";

  	 $updateData['vOmiseToken']=$vOmiseCardId;

  	 $updateData['vOmiseCustId']=$vOmiseCustId;

  	 $updateData['vCreditCard']=$LastFour;

  		

  	 $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

  	 if($eMemberType == "Passenger"){

  			$profileData =  getPassengerDetailInfo($iUserId);

  	 }else{

  			$profileData =  getDriverDetailInfo($iUserId);

  	 }

  		

  	 if($id >0){

  			$returnArr['Action'] = "1";

  			$returnArr['message'] = $profileData;

        $returnArr['message1'] = "LBL_SUCESS_ADD_BRAINTREE_TXT";

  		}else{

  			$returnArr['Action'] = "0";

  			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

  	 } 

  }else if($APP_PAYMENT_METHOD == "Adyen"){

     $returnArr['Action'] = "1";

  	 $returnArr['message'] = $tconfig['tsite_url']."/assets/libraries/adyen/clienttoken.php?iUserId=".$iUserId."&UserType=".$eMemberType;

  }else if($APP_PAYMENT_METHOD == "Xendit"){

     require_once('assets/libraries/xendit/config.php');

	   require_once('assets/libraries/xendit/src/XenditPHPClient.php');

     $options['secret_api_key'] = $XENDIT_SECRET_KEY;

     $xenditPHPClient = new XenditClient\XenditPHPClient($options);

     $external_id = substr(number_format(time() * rand(), 0, '', ''), 0, 15);

     $token_id = $vXenditToken;

     $amount = 0;

     $response = $xenditPHPClient->captureCreditCardPayment($external_id, $token_id, $amount);

     $result = $response['status'];

     $CardNo = $response['masked_card_number'];

     if($result == "AUTHORIZED"){

        $where = " $iMemberId = '$iUserId'";

    		$updateData['vXenditToken']=$vXenditToken;

        $updateData['vCreditCard']=$CardNo;

        $updateData['CpfNo']=$CpfNo;

        $updateData['NameCardBox']=$NameCardBox;

		$updateData['vGeoCityRider']=$vGeoCityRider;

        $id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);

        if($eMemberType == "Passenger"){

    			$profileData =  getPassengerDetailInfo($iUserId);

      	}else{

     			$profileData =  getDriverDetailInfo($iUserId);

      	}

        if($id >0){

    			$returnArr['Action'] = "1";

    			$returnArr['message'] = $profileData;

      	}else{

      		$returnArr['Action'] = "0";

      		$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";

      	}

     }else{

          $error3 = $response['message'];

    			$returnArr['Action'] = "0";

    			$returnArr['message']=$error3;

    			echo json_encode($returnArr);exit;

     }

  }

  

  return $returnArr;

}  

########################### GenerateCustomer App Payment Method Wise #############################################################

########################### Charge Customer App Payment Method Wise ##############################################################

function ChargeCustomer($Data,$eChargeEvent = "CollectPayment"){

  global $generalobj,$obj,$STRIPE_SECRET_KEY,$STRIPE_PUBLISH_KEY,$gateway,$BRAINTREE_TOKEN_KEY,$BRAINTREE_ENVIRONMENT,$BRAINTREE_MERCHANT_ID,$BRAINTREE_PUBLIC_KEY,$BRAINTREE_PRIVATE_KEY,$BRAINTREE_CHARGE_AMOUNT,$PAYMAYA_API_URL,$ADYEN_MERCHANT_ACCOUNT,$ADYEN_USER_NAME,$ADYEN_PASSWORD,$ADYEN_API_URL,$XENDIT_PUBLIC_KEY,$XENDIT_SECRET_KEY,$APP_PAYMENT_METHOD;  // Stripe,Braintree

  

  $iFare = $Data['iFare'];

  $price_new = $Data['price_new'];

  $currency = $Data['currency'];

  $vStripeCusId = $Data['vStripeCusId'];

  $description = $Data['description']; 

  $iTripId = $Data['iTripId']; 

  $eCancelChargeFailed = $Data['eCancelChargeFailed'];

  $vBrainTreeToken = $Data['vBrainTreeToken'];

  $vRideNo = $Data['vRideNo']; 

  $iMemberId = $Data['iMemberId']; 

  $UserType = $Data['UserType'];

  $RedeChargePrice = bcdiv($price_new, 100, 2);

  $vBrainTreeChargePrice = $price_new/100;

  $vPaymayaChargePrice = $price_new/100;

  //$vAdyenChargePrice = $price_new/100; 

  $vAdyenChargePrice = $price_new;  

  if($UserType == "Passenger"){

  			$eMemberTypeENum = 'passenger';

			$tbl_name = "register_user";

			$iUserId = "iUserId";

      $UserDetailPaymaya = get_value($tbl_name, 'vPaymayaCustId,vPaymayaToken,vAdyenToken,vName,vLastName,vEmail,vXenditAuthId,vXenditToken,vCurrencyPassenger as vCurrency', $iUserId, $iMemberId);

	}else{

			$eMemberTypeENum = 'driver';

			$tbl_name = "register_driver";

			$iUserId = "iDriverId";

      $UserDetailPaymaya = get_value($tbl_name, 'vPaymayaCustId,vPaymayaToken,vAdyenToken,vName,vLastName,vEmail,vXenditAuthId,vXenditToken,vCurrencyDriver as vCurrency', $iUserId, $iMemberId);

	} 

   



// brainLog(' PAGAMENTO GenerateCustomer - sql ::: ', $Data);



$sqlp = " SELECT tokenizer_token FROM tokenizer WHERE tokenizer_id = '$iMemberId' AND tokenizer_type = '$eMemberTypeENum' ";

$tokenizer_token = $obj->MySQLSelect($sqlp);



if (count($tokenizer_token)>=1)

	$APP_PAYMENT_METHOD = "Rede";



	// brainLog(' PAGAMENTO REDE (antes) - sql ::: ', $sqlp);

	// brainLog(' PAGAMENTO APP_PAYMENT_METHOD :::  ', $APP_PAYMENT_METHOD);



  // BRAINATWORK::REDE - Processando Pagamentos

  if($APP_PAYMENT_METHOD == "Rede"){



  		$tokenizer_token = $tokenizer_token[0]['tokenizer_token'];



		require_once($_SERVER[DOCUMENT_ROOT] . "/app/BrainatworkCry.php");



	    $ce = new Cry($iMemberId);

	    $token = $ce->d($tokenizer_token);

	    $token = json_decode($token, true);



	  	foreach($token as $key => $value) 

			$$key = $value;

     

      try{

			if($iFare > 0){



			  	require_once($_SERVER[DOCUMENT_ROOT] . "/gatewayRede.php");

				$transaction = (new \Rede\Transaction($RedeChargePrice,'c'.time()))->creditCard(

				    $rd_cc, $rd_cs, $rd_cm, $rd_ca, $rd_cn

				);



				$transaction = (new \Rede\eRede($store))->create($transaction);



				brainLog(' Rede transaction :::  ', $transaction);

			}

			

			

				if($iFare == 0 || ($transaction->getReturnCode()=='00')){

					

					$pay_data['tPaymentUserID']= $iFare == 0? "":$transaction->getTid();

					$pay_data['vPaymentUserStatus']="approved";

					$pay_data['iTripId']=$iTripId;

					$pay_data['iAmountUser']=$iFare;



					$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');



					// Brainatwork - Baixa automatica do cartão na carteira

				$sql = "SELECT vRideNo, iDriverId, iFare, fDiscount, fWalletDebit, fCommision, fRydTax from trips WHERE iTripId = '".$iTripId."' AND eDriverPaymentStatus = 'Unsettelled'";

				$db_trip = $obj->MySQLSelect($sql);



				if (count($db_trip)>0) {

					$vRideNo = $db_trip[0]["vRideNo"];

					$fAmount = $db_trip[0]["iFare"]+$db_trip[0]["fDiscount"]+$db_trip[0]["fWalletDebit"]-$db_trip[0]["fCommision"]-$db_trip[0]["fRydTax"];



					$iDriverId = $db_trip[0]["iDriverId"];



					$tDescription_credit = '#LBL_CREDITED_BOOKING# '.$vRideNo;

					$dDate = Date('Y-m-d H:i:s');

					$generalobj->InsertIntoUserWallet($iDriverId,'Driver',$fAmount,'Credit',$iTripId,'Withdrawl',$tDescription_credit,'Settelled',$dDate);



					$query = "UPDATE payments SET ePaymentDriverStatus = 'Paid' WHERE iTripId = '" .$iTripId. "'";

					$obj->sql_query($query);

					

					$query = "UPDATE trips SET eDriverPaymentStatus = 'Settelled' WHERE iTripId = '" .$iTripId. "'";

					$obj->sql_query($query);

				}





            $returnArr['status'] = "success";

  					

  				}else{

            $returnArr['status'] = "fail";

  					if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

            }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

            }

  				}

		}catch(Exception $e){

			$returnArr['status'] = "fail";

			$error3 = $e->getMessage();

			if($eChargeEvent == "cancelTrip"){

			$eCancelChargeFailed ='Yes';

			}else{

			$returnArr['Action'] = "0";

			$returnArr['message']=$error3;

			//$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

			echo json_encode($returnArr);exit;

			}  

		}







  } else if($APP_PAYMENT_METHOD == "Stripe"){



     require_once('assets/libraries/stripe/config.php');

	 require_once('assets/libraries/stripe/stripe-php-2.1.4/lib/Stripe.php');

      

      try{

				if($iFare > 0){

					$charge_create = Stripe_Charge::create(array(

					"amount" => $price_new,

					"currency" => $currency,

					"customer" => $vStripeCusId,

					"description" =>  $description

					));

					

					$details = json_decode($charge_create);

					$result = get_object_vars($details);



					// brainLog(' PAGAMENTO - Stripe Result :::  ', $result);

				}

				

				

  				if($iFare == 0 || ($result['status']=="succeeded" && $result['paid']=="1")){

  					

  					$pay_data['tPaymentUserID']= $iFare == 0? "":$result['id'];

  					$pay_data['vPaymentUserStatus']="approved";

  					$pay_data['iTripId']=$iTripId;

  					$pay_data['iAmountUser']=$iFare;





  					

  					$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');





  					// Brainatwork - Baixa automatica do cartão na carteira

					$sql = "SELECT vRideNo, iDriverId, iFare, fDiscount, fWalletDebit, fCommision, fRydTax from trips WHERE iTripId = '".$iTripId."' AND eDriverPaymentStatus = 'Unsettelled'";

					$db_trip = $obj->MySQLSelect($sql);



					if (count($db_trip)>0) {

						$vRideNo = $db_trip[0]["vRideNo"];

						$fAmount = $db_trip[0]["iFare"]+$db_trip[0]["fDiscount"]+$db_trip[0]["fWalletDebit"]-$db_trip[0]["fCommision"]-$db_trip[0]["fRydTax"];



						$iDriverId = $db_trip[0]["iDriverId"];



						$tDescription_credit = '#LBL_CREDITED_BOOKING# '.$vRideNo;

						$dDate = Date('Y-m-d H:i:s');

						$generalobj->InsertIntoUserWallet($iDriverId,'Driver',$fAmount,'Credit',$iTripId,'Withdrawl',$tDescription_credit,'Settelled',$dDate);



						$query = "UPDATE payments SET ePaymentDriverStatus = 'Paid' WHERE iTripId = '" .$iTripId. "'";

						$obj->sql_query($query);

						

						$query = "UPDATE trips SET eDriverPaymentStatus = 'Settelled' WHERE iTripId = '" .$iTripId. "'";

						$obj->sql_query($query);

					}

	



            $returnArr['status'] = "success";

  					

  				}else{

            $returnArr['status'] = "fail";

  					if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

            }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

            }

  				}

			}catch(Exception $e){

         $returnArr['status'] = "fail";

         $error3 = $e->getMessage();

         if($eChargeEvent == "cancelTrip"){

           $eCancelChargeFailed ='Yes';

         }else{

  				 $returnArr['Action'] = "0";

  				 $returnArr['message']=$error3;

  				 //$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

  				 echo json_encode($returnArr);exit;

         }  

		}

  }else if($APP_PAYMENT_METHOD == "Braintree"){

      require_once('assets/libraries/braintree/lib/Braintree.php');

      try{

				if($iFare > 0){

					$charge_create = $gateway->transaction()->sale(

              [

                'paymentMethodToken' => $vBrainTreeToken,

                'amount' => $vBrainTreeChargePrice

              ]

            );

					

					$status = $charge_create->success;

          $transactionid = $charge_create->transaction->id;

				}

        

				

				

  				if($iFare == 0 || $status == "1"){

  					

  					$pay_data['tPaymentUserID']= $iFare == 0? "":$transactionid;

  					$pay_data['vPaymentUserStatus']="approved";

  					$pay_data['iTripId']=$iTripId;

  					$pay_data['iAmountUser']=$iFare;

  					

  					$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');

            $returnArr['status'] = "success";

  					

  				}else{

            $returnArr['status'] = "fail";

  					if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

            }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

            }

  				}

			}catch(Exception $e){

         $returnArr['status'] = "fail";

         $error3 = $e->getMessage();

         if($eChargeEvent == "cancelTrip"){

           $eCancelChargeFailed ='Yes';

         }else{

  				 $returnArr['Action'] = "0";

  				 $returnArr['message']=$error3;

  				 //$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

  				 echo json_encode($returnArr);exit;

         }  

			}

  }else if($APP_PAYMENT_METHOD == "Paymaya"){

      $vPaymayaCustId = $UserDetailPaymaya[0]['vPaymayaCustId'];

      $vPaymayaToken = $UserDetailPaymaya[0]['vPaymayaToken'];

      $Ratio= get_value('currency', 'Ratio', 'vName', 'PHP','','true'); 

      $vPaymayaChargePrice = $vPaymayaChargePrice * $Ratio;

      $vPaymayaChargePrice = round($vPaymayaChargePrice,2);

      $postdata_charge =  array('totalAmount' => array('amount' => $vPaymayaChargePrice,'currency' => 'PHP'),'requestReferenceNumber' => 'REF'.$vRideNo);

			$url = $PAYMAYA_API_URL."/payments/v1/customers/".$vPaymayaCustId."/cards/".$vPaymayaToken."/payments";

      $result_charge = check_paymaya_api($url,$postdata_charge);

      $PaymentId = $result_charge['id'];

      $paymentstatus= $result_charge['status']; //PAYMENT_SUCCESS  

      if($vPaymayaChargePrice == 0 || $paymentstatus == 'PAYMENT_SUCCESS'){

						$pay_data['tPaymentUserID']=$PaymentId;

						$pay_data['vPaymentUserStatus']="approved";

						$pay_data['iTripId']=$iTripId;

						$pay_data['iAmountUser']=$iFare;

						$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');

            $returnArr['status'] = "success";

			}else{

        $returnArr['status'] = "fail";

				if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

           $eCancelChargeFailed = "Yes";

        }else{

           $returnArr['Action'] = "0";

					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

					 echo json_encode($returnArr);exit;

        }

      }

  }else if($APP_PAYMENT_METHOD == "Omise"){

      require_once('assets/libraries/omise/config.php');

      $UserDetailOmise = get_value($tbl_name, 'vOmiseCustId,vOmiseToken', $iUserId, $iMemberId);

      $vOmiseCustId = $UserDetailOmise[0]['vOmiseCustId'];

      $vOmiseToken = $UserDetailOmise[0]['vOmiseToken'];

      

      try{

				if($iFare > 0){

					$charge = OmiseCharge::create(array(

              'amount' => $price_new,

              'currency' => $currency,  

              'customer' => $vOmiseCustId,

              'card' => $vOmiseToken

            ));  

				}

				

				

  				if($iFare == 0 || ($charge['status']=="successful" && $charge['paid']=="1")){

  					

  					$pay_data['tPaymentUserID']= $iFare == 0? "":$charge['transaction'];

  					$pay_data['vPaymentUserStatus']="approved";

  					$pay_data['iTripId']=$iTripId;

  					$pay_data['iAmountUser']=$iFare;

  					

  					$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');

            $returnArr['status'] = "success";

  					

  				}else{

            $returnArr['status'] = "fail";

  					if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

            }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

            }

  				}

			}catch(Exception $e){

         $returnArr['status'] = "fail";

         $error3 = $e->getMessage();

         if($eChargeEvent == "cancelTrip"){

           $eCancelChargeFailed ='Yes';

         }else{

  				 $returnArr['Action'] = "0";

  				 $returnArr['message']=$error3;

  				 //$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

  				 echo json_encode($returnArr);exit;

         }  

			}

  }else if($APP_PAYMENT_METHOD == "Adyen"){

      $vAdyenToken  = $UserDetailPaymaya[0]['vAdyenToken'];

      $shopperReference = $UserDetailPaymaya[0]['vName']." ".$UserDetailPaymaya[0]['vLastName'];

      $shopperEmail = $UserDetailPaymaya[0]['vEmail'];

      $reference = rand(111111,999999);

      $USERPWD =  $ADYEN_USER_NAME.":".$ADYEN_PASSWORD;

      $result = array();

      // Pass the customer's authorisation code, email and amount

      $postdata =  array( "selectedRecurringDetailReference" => $vAdyenToken, "recurring" => array("contract" => "RECURRING"),"merchantAccount" => $ADYEN_MERCHANT_ACCOUNT,"amount" => array("value" => $vAdyenChargePrice,"currency" => $currency),"reference" => $reference,"shopperEmail" => $shopperEmail, "shopperReference" => $shopperReference,"shopperInteraction" => "ContAuth");



      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $ADYEN_API_URL);

      curl_setopt($ch, CURLOPT_HEADER, false); 

      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

      curl_setopt($ch, CURLOPT_USERPWD, $USERPWD);   

      curl_setopt($ch, CURLOPT_POST,count(json_encode($postdata)));

      curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-type: application/json"));

      

      $request = curl_exec ($ch);  //echo "<pre>";print_r($request);exit;

  

      curl_close ($ch);

      if ($request) {

        $result = json_decode($request, true);

        $resultCode = $result['resultCode'];  //Authorised

        $authCode = $result['authCode'];

        

        if($resultCode == "Authorised"){

           $pay_data['tPaymentUserID']=$authCode;

					 $pay_data['vPaymentUserStatus']="approved";

					 $pay_data['iTripId']=$iTripId;

					 $pay_data['iAmountUser']=$iFare;

					 $id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');

           $returnArr['status'] = "success";

        }else{

           $returnArr['status'] = "fail";

    			 if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

           }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

           }

        } 

      }else{

           $returnArr['status'] = "fail";

    			 if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

               $eCancelChargeFailed = "Yes";

           }else{

               $returnArr['Action'] = "0";

    					 $returnArr['message']="LBL_CHARGE_COLLECT_FAILED";

    					 echo json_encode($returnArr);exit;

           }

      }

  }else if($APP_PAYMENT_METHOD == "Xendit"){

     require_once('assets/libraries/xendit/config.php');

	   require_once('assets/libraries/xendit/src/XenditPHPClient.php');

     $options['secret_api_key'] = $XENDIT_SECRET_KEY;

     $xenditPHPClient = new XenditClient\XenditPHPClient($options);

     $external_id = substr(number_format(time() * rand(), 0, '', ''), 0, 15);

     $IDRCurrencyRatio = get_value('currency', 'Ratio', 'vName', 'IDR', '', 'true');

     $famount = $iFare*$IDRCurrencyRatio;

     $famount = round($famount);

     $vXenditAuthId = $UserDetailPaymaya[0]['vXenditAuthId'];

     $vXenditToken = $UserDetailPaymaya[0]['vXenditToken'];

     $response = $xenditPHPClient->captureCreditCardPayment($external_id, $vXenditToken, $famount);

     $resultCode = $response['status'];

     if($resultCode == "CAPTURED"){

        $pay_data['tPaymentUserID']=$response["id"];

			  $pay_data['vPaymentUserStatus']="approved";

			  $pay_data['iTripId']=$iTripId;

			  $pay_data['iAmountUser']=$iFare;

			  $id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');

        $returnArr['status'] = "success";

     }else{

        $returnArr['status'] = "fail";

			  if($eChargeEvent == "cancelTrip" || $eChargeEvent == "addMoneyUserWallet" || $eChargeEvent == "ChargePassengerOutstandingAmount"){

           $eCancelChargeFailed = "Yes";

        }else{

           $error3 = $response['message'];

           $returnArr['Action'] = "0";

					 $returnArr['message']=$error3;

					 echo json_encode($returnArr);exit;

        }

    } 

  }

  

  $returnArr['id'] = $id;

  $returnArr['eCancelChargeFailed'] = $eCancelChargeFailed; 

  

  return $returnArr;

}

########################### Charge Customer App Payment Method Wise ##############################################################

############################## Display Price in Member's Currency    #############################################################

function getPriceUserCurrency($iMemberId,$eUserType="Passenger",$fPrice){      

   global $obj, $generalobj, $tconfig;

             

   $returnArr = array();

   if($eUserType=="Passenger"){

      $UserDetailsArr = getUserCurrencyLanguageDetails($iMemberId);

   }else{

      $UserDetailsArr = getDriverCurrencyLanguageDetails($iMemberId);

   } 

   

   $currencySymbol = $UserDetailsArr['currencySymbol'];

   $Ratio = $UserDetailsArr['Ratio'];

   $fPrice = round(($fPrice*$Ratio),2);

   $fPricewithsymbol = $currencySymbol." ".formatnum($fPrice);

   

   $returnArr['fPrice']=$fPrice;

   $returnArr['fPricewithsymbol']=$fPricewithsymbol;

   $returnArr['currencySymbol']=$currencySymbol;

   return $returnArr; 

}

function getUserCurrencyLanguageDetails($iUserId=""){      

   global $obj, $generalobj, $tconfig;

   

   $returnArr = array();

   if($iUserId != ""){

     $sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol,cu.Ratio FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iUserId."'";

     $passengerData = $obj->MySQLSelect($sqlp);

     $currencycode = $passengerData[0]['vCurrencyPassenger'];

     $vLanguage = $passengerData[0]['vLang'];

     $currencySymbol = $passengerData[0]['vSymbol'];

     $Ratio = $passengerData[0]['Ratio']; 

     

     if($vLanguage == "" || $vLanguage == NULL){

    		$vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

     }

     if($currencycode == "" || $currencycode == NULL){

       $sqlp = "SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'";

       $currencyData = $obj->MySQLSelect($sqlp);

       $currencycode = $currencyData[0]['vName'];

       $currencySymbol = $currencyData[0]['vSymbol'];

       $Ratio = $currencyData[0]['Ratio'];

     }

   }else{

     $vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

     $sqlp = "SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'";

     $currencyData = $obj->MySQLSelect($sqlp);

     $currencycode = $currencyData[0]['vName'];

     $currencySymbol = $currencyData[0]['vSymbol'];

     $Ratio = $currencyData[0]['Ratio'];

   }

   $returnArr['currencycode'] = $currencycode;

   $returnArr['currencySymbol'] = $currencySymbol;

   $returnArr['Ratio'] = $Ratio;

   $returnArr['vLang'] = $vLanguage;

   return $returnArr;

} 

function getDriverCurrencyLanguageDetails($iDriverId=""){      

   global $obj, $generalobj, $tconfig;

   

   $returnArr = array();

   if($iDriverId != ""){

     $sqlp = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol,cu.Ratio FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$iDriverId."'";

     $passengerData = $obj->MySQLSelect($sqlp);

     $currencycode = $passengerData[0]['vCurrencyDriver'];

     $vLanguage = $passengerData[0]['vLang'];

     $currencySymbol = $passengerData[0]['vSymbol'];

     $Ratio = $passengerData[0]['Ratio']; 

     

     if($vLanguage == "" || $vLanguage == NULL){

    		$vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

     }

     if($currencycode == "" || $currencycode == NULL){

       $sqlp = "SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'";

       $currencyData = $obj->MySQLSelect($sqlp);

       $currencycode = $currencyData[0]['vName'];

       $currencySymbol = $currencyData[0]['vSymbol'];

       $Ratio = $currencyData[0]['Ratio'];

     }

   }else{

     $vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

     $sqlp = "SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'";

     $currencyData = $obj->MySQLSelect($sqlp);

     $currencycode = $currencyData[0]['vName'];

     $currencySymbol = $currencyData[0]['vSymbol'];

     $Ratio = $currencyData[0]['Ratio'];

   }

   $returnArr['currencycode'] = $currencycode;

   $returnArr['currencySymbol'] = $currencySymbol;

   $returnArr['Ratio'] = $Ratio;

   $returnArr['vLang'] = $vLanguage;

   return $returnArr;

}  

########################### Display Price in Member's Currency    #############################################################

########################### Paymaya Payment API  ##############################################################################

function check_paymaya_api($url,$postdata = array()) {

		global $generalobj, $obj,$PAYMAYA_SECRET_KEY,$PAYMAYA_PUBLISH_KEY;

		$result = array();

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,$url);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($postdata));  //Post Fields

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$paymaya_auth = base64_encode($PAYMAYA_SECRET_KEY.":");

  	$headers = [

      'Authorization: Basic '.$paymaya_auth,

      'Content-Type: application/json',

    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $request = curl_exec ($ch);  //echo "<pre>";print_r($request);exit;

    curl_close ($ch);

    if ($request) {

      $result = json_decode($request, true);

    } 

    return $result;

}

########################### Paymaya Payment API  ##############################################################################

########################### Add For Rental Amount#############################################################

function getRentalData($iRentalPackageId){

	global $generalobj,$obj;

	$sql = "SELECT * FROM rental_package WHERE iRentalPackageId='".$iRentalPackageId."'";

	$rentalData = $obj->MySQLSelect($sql);

	return $rentalData; 

}

############################ Calculate Additional Hours For Rental ##########################

function calculateAdditionalTime($startDate ,$endDate, $rentalTimeHours,$userlangcode){

	$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");

	$TotalTimeInMinutes_trip=@round(abs(strtotime($startDate) - strtotime($endDate))/ 60,2);

	$RentalDefineMinutes =  $rentalTimeHours * 60;

	if($TotalTimeInMinutes_trip > $RentalDefineMinutes){

		$MinutesDiff= $TotalTimeInMinutes_trip - $RentalDefineMinutes;

		$AdditionTime = mediaTimeDeFormater($MinutesDiff,$userlangcode);

	} else {

		$AdditionTime = "0.00 "." ".$languageLabelsArr['LBL_MINUTE'];

	}

	return $AdditionTime;

}

function mediaTimeDeFormater($minutes,$userlangcode) {

	$seconds = @round(abs($minutes * 60));

	$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");

    $ret = "";

    $hours = (string)floor($seconds / 3600);

    $secs = (string)$seconds % 60;

    $mins = (string)floor(($seconds - ($hours * 3600)) / 60);

    if (strlen($hours) == 1)

        $hours = "0" . $hours;

    if (strlen($secs) == 1)

        $secs = "0" . $secs;

    if (strlen($mins) == 1)

        $mins = "0" . $mins;

    if ($hours == 0){

        $mint="";

        $secondss="";

        if($mins > 01){

            $mint = "$mins";

        }else{

            $mint = "$mins";

        }

        if($secs > 01){

            $secondss = "$secs";

        }else{

            $secondss = "$secs";

        }

        $LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];

        if($mins > 0){

       	 	$ret = $mint.":".$secondss." ".$LBL_MINUTES_TXT;

       	} else {

       		$ret = $secondss." ".$languageLabelsArr['LBL_SECONDS_TXT'];

       	}

    } else {

        $mint="";

        $secondss="";

        if($mins > 01){

            $mint = "$mins";

        }else{

            $mint = "$mins";

        }

        if($secs > 01){

            $secondss = "$secs";

        }else{

            $secondss = "$secs";

        }

        if($hours > 01){

          $ret = $hours.":".$mint.":".$secondss." ".$languageLabelsArr['LBL_HOURS_TXT'];

        }else{

          $ret = $hours.":".$mint.":".$secondss." ".$languageLabelsArr['LBL_HOUR_TXT'];

        }

    }

    return  $ret;

}

########################### End For Rental Amount#############################################################

########################### General Icon Banner #############################################################

function getGeneralVarAll_IconBanner() {

        global $obj,$APP_TYPE;

        //$listField = $obj->MySQLGetFieldsQuery("setting");

        $ssql = "";

        /*if(ENABLE_RENTAL_OPTION == 'No') {

           $ssql .= " AND eRentalType = 'No' ";

        }*/

        $wri_usql = "SELECT iSettingId,vName,TRIM(vValue) as vValue,eImageType,eRentalType FROM configurations_cubejek where 1".$ssql;

        $wri_ures = $obj->MySQLSelect($wri_usql);

        return $wri_ures; 

}

########################### General Icon Banner #############################################################

########################### Change Driver's Selected Vehicle  to  0 if Ride Delivery Feature Enable ##############################################

function ChangeDriverVehicleRideDeliveryFeatureDisable($iDriverId) {

        global $obj,$APP_TYPE,$generalobj;

        $eShowRideVehicles = "Yes";

        $eShowDeliveryVehicles = "Yes";

        $sqldata = "SELECT iTripId FROM `trips` WHERE ( iActive='On Going Trip' OR iActive='Active' ) AND iDriverId='" .$iDriverId. "'";

		    $TripData = $obj->MySQLSelect($sqldata);

        $TripRunCount = count($TripData); 

        if($APP_TYPE == "Ride-Delivery-UberX" && $TripRunCount == 0){

           $RideDeliveryIconArr = getGeneralVarAll_IconBanner();

           for($i=0;$i<count($RideDeliveryIconArr);$i++){

              $vName = $RideDeliveryIconArr[$i]['vName'];

              $vValue = $RideDeliveryIconArr[$i]['vValue'];

              $$vName = $vValue;

              $Data[0][$vName] = $$vName;   

      		 }

           if($Data[0]['RIDE_SHOW_SELECTION'] == 'None' && $Data[0]['RENTAL_SHOW_SELECTION'] == 'None' && $Data[0]['MOTO_RIDE_SHOW_SELECTION'] == 'None' && $Data[0]['MOTO_RENTAL_SHOW_SELECTION'] == 'None'){

               $eShowRideVehicles = "No";

               $sql = "SELECT eType FROM `driver_vehicle` as dv LEFT JOIN register_driver as rd ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.iDriverId='" .$iDriverId. "'";

		           $DriverVehicleType = $obj->MySQLSelect($sql);

               $eType = $DriverVehicleType[0]['eType']; 

               if($eType == "Ride"){

                  $sql = "UPDATE register_driver set iDriverVehicleId='0' WHERE iDriverId='".$iDriverId."'";

    					    $obj->sql_query($sql);

               }

           }

           if($Data[0]['DELIVERY_SHOW_SELECTION'] == 'None' && $Data[0]['MOTO_DELIVERY_SHOW_SELECTION'] == 'None'){

               $eShowDeliveryVehicles = "No";

               $sql = "SELECT eType FROM `driver_vehicle` as dv LEFT JOIN register_driver as rd ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.iDriverId='" .$iDriverId. "'";

		           $DriverVehicleType = $obj->MySQLSelect($sql);

               $eType = $DriverVehicleType[0]['eType']; 

               if($eType == "Delivery"){

                  $sql = "UPDATE register_driver set iDriverVehicleId='0' WHERE iDriverId='".$iDriverId."'";

    					    $obj->sql_query($sql);

               }

           }

        }

        return $iDriverId;

}

########################### Change Driver's Selected Vehicle  to  0 if Ride Delivery Feature Enable ##############################################

########################### Check Ride Delivery Feature Enable ##############################################

function CheckRideDeliveryFeatureDisable() {

        global $obj,$APP_TYPE,$generalobj;

        $eShowRideVehicles = "Yes";

        $eShowDeliveryVehicles = "Yes";

        $RideDeliveryBothFeatureDisable = "No";

        if($APP_TYPE == "Ride-Delivery-UberX"){

           $RideDeliveryIconArr = getGeneralVarAll_IconBanner();

           for($i=0;$i<count($RideDeliveryIconArr);$i++){

              $vName = $RideDeliveryIconArr[$i]['vName'];

              $vValue = $RideDeliveryIconArr[$i]['vValue'];

              $$vName = $vValue;

              $Data[0][$vName] = $$vName;   

      		 }

           if($Data[0]['RIDE_SHOW_SELECTION'] == 'None' &&  $Data[0]['MOTO_RIDE_SHOW_SELECTION'] == 'None'){

               $eShowRideVehicles = "No";

           }

           if($Data[0]['DELIVERY_SHOW_SELECTION'] == 'None' && $Data[0]['MOTO_DELIVERY_SHOW_SELECTION'] == 'None'){

               $eShowDeliveryVehicles = "No";

           }

        }

        if($eShowRideVehicles == "No" && $eShowDeliveryVehicles == "No"){

          $RideDeliveryBothFeatureDisable = "Yes";

        }

        $returnArr['eShowRideVehicles']=$eShowRideVehicles;

        $returnArr['eShowDeliveryVehicles']=$eShowDeliveryVehicles;

        $returnArr['RideDeliveryBothFeatureDisable']=$RideDeliveryBothFeatureDisable;

        return $returnArr;

}

########################### Check Ride Delivery Feature Enable ##############################################

############### Check  User's  SMS Resending Limit and Reset Verification count and date if restriction time is  over  ###################################################################

function CheckUserSmsLimit($iMemberId,$UserType="Passenger"){

		global $obj, $generalobj, $tconfig,$VERIFICATION_CODE_RESEND_COUNT,$VERIFICATION_CODE_RESEND_TIME_IN_SECONDS;

    if($UserType == "Passenger"){

			$tblname = "register_user";

			$fields = 'vVerificationCount,dSendverificationDate,vLang';

			$condfield = 'iUserId';

		}else{

			$tblname = "register_driver";

			$fields = 'vVerificationCount,dSendverificationDate,vLang';

			$condfield = 'iDriverId';

		}

    $sql = "select $fields from $tblname where $condfield='".$iMemberId."'";

    $db_user = $obj->MySQLSelect($sql);

    $vVerificationCount = $db_user[0]['vVerificationCount'];

    $dSendverificationDate = $db_user[0]['dSendverificationDate'];

    $vLang = $db_user[0]['vLang'];

    if($vLang == "" || $vLang == NULL) {

			$vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');

		} 

    $languageLabelsArr= getLanguageLabelsArr($vLang,"1");

    $currentdate = @date("Y-m-d H:i:s");

    $totalMinute = @round(abs(strtotime($dSendverificationDate) - strtotime($currentdate)) / 60);

    $hours =  floor($totalMinute/60); // No. of mins/60 to get the hours and round down

    $mins =   $totalMinute % 60; // No. of mins/60 - remainder (modulus) is the minutes

    $LBL_HOURS_TXT = ($hours > 1)? $languageLabelsArr['LBL_HOURS_TXT'] : $languageLabelsArr['LBL_HOUR_TXT'];

    //$LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];

    $LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINS_SMALL'] : $languageLabelsArr['LBL_MINUTE'];

    if($hours >= 1){

       $timeDurationDisplay = $hours." ".$LBL_HOURS_TXT." ".$mins." ".$LBL_MINUTES_TXT;

    }else{

       $timeDurationDisplay = $mins." ".$LBL_MINUTES_TXT;

    }

    $message = $languageLabelsArr['LBL_SMS_MAXIMAM_LIMIT_TXT']." ".$timeDurationDisplay;

    if(($dSendverificationDate < $currentdate) && $dSendverificationDate != "0000-00-00 00:00:00"){

       $updateQuery = "UPDATE $tblname set dSendverificationDate='0000-00-00 00:00:00',vVerificationCount = 0 WHERE $condfield = ".$iMemberId;

			 $obj->sql_query($updateQuery);

       $vVerificationCount = 0;

       $dSendverificationDate = "0000-00-00 00:00:00";

    } 

    if($vVerificationCount == $VERIFICATION_CODE_RESEND_COUNT){

       $returnArr['Action'] ="0";

			 $returnArr['message'] = $message;

			 echo json_encode($returnArr);exit;

    }

    return $iMemberId;

}

############### Check  User's  SMS Resending Limit and Reset Verification count and date if restriction time is  over  ###################################################################

############################# Update  User's  SMS Resending Limit and Rest Verification count and date  ###################################################################

function UpdateUserSmsLimit($iMemberId,$UserType="Passenger"){

		global $obj, $generalobj, $tconfig,$VERIFICATION_CODE_RESEND_COUNT,$VERIFICATION_CODE_RESEND_COUNT_RESTRICTION;

    if($UserType == "Passenger"){

			$tblname = "register_user";

			$fields = 'vVerificationCount,dSendverificationDate';

			$condfield = 'iUserId';

		}else{

			$tblname = "register_driver";

			$fields = 'vVerificationCount,dSendverificationDate';

			$condfield = 'iDriverId';

		}

    $sql = "select $fields from $tblname where $condfield='".$iMemberId."'";

    $db_user = $obj->MySQLSelect($sql); 

    $vVerificationCount = $db_user[0]['vVerificationCount'];

    $dSendverificationDate = $db_user[0]['dSendverificationDate'];

    $currentdate = @date("Y-m-d H:i:s");

    $checklastcount = $VERIFICATION_CODE_RESEND_COUNT-1;

    if($vVerificationCount == $checklastcount){

       $minutes = $VERIFICATION_CODE_RESEND_COUNT_RESTRICTION;

       $expire_stamp = date('Y-m-d H:i:s', strtotime("+".$minutes." minute"));

       $updateQuery = "UPDATE $tblname set dSendverificationDate='".$expire_stamp."',vVerificationCount = vVerificationCount+1 WHERE $condfield = ".$iMemberId;

			 $obj->sql_query($updateQuery);

    }else{

       $vVerificationCount = $vVerificationCount+1;     

       if($vVerificationCount > $VERIFICATION_CODE_RESEND_COUNT){

          $vVerificationCount = $VERIFICATION_CODE_RESEND_COUNT;

       }

       $updateQuery = "UPDATE $tblname set vVerificationCount = '".$vVerificationCount."' WHERE $condfield = ".$iMemberId;

			 $obj->sql_query($updateQuery);

    }

		return $iMemberId;

}

############################# Update  User's  SMS Resending Limit and Rest Verification count and date  ###################################################################

############################################################## Display Trip Charge To Driver For UberX Trip ####################################################################################

function DisplayTripChargeForUberX($TripID){

  global $obj, $generalobj, $tconfig;

  $returnArr = array(); 

  $where = " iTripId = '".$TripID."'";

	$sql = "SELECT * from trips WHERE iTripId = '".$TripID."'";

	$tripData = $obj->MySQLSelect($sql);

	// echo "<pre>"; print_r($tripData); die;

	$eType = $tripData[0]['eType']; 

  if($eType == "UberX"){

  $iDriverVehicleId = $tripData[0]['iDriverVehicleId'];

	$iVehicleTypeId = $tripData[0]['iVehicleTypeId'];

	$fVisitFee = $tripData[0]['fVisitFee'];

	$startDate = $tripData[0]['tStartDate'];

	$endDateOfTrip = $tripData[0]['tEndDate']; 

	$iQty = $tripData[0]['iQty']; 

  $destination_lat = $tripData[0]['tEndLat'];

  $destination_lon = $tripData[0]['tEndLong']; 

	//$endDateOfTrip=@date("Y-m-d H:i:s");

	/*$iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');

	$iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');*/

	$sql = "SELECT vc.iParentId from vehicle_category as vc LEFT JOIN vehicle_type as vt ON vc.iVehicleCategoryId=vt.iVehicleCategoryId WHERE vt.iVehicleTypeId = '".$iVehicleTypeId."'";

	$VehicleCategoryData = $obj->MySQLSelect($sql);

	$iParentId = $VehicleCategoryData[0]['iParentId']; 

	if($iParentId == 0){

		$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  

		}else{ 

		$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 

	}

	//$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  

	$ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";

	if($tripData[0]['eFareType'] == 'Hourly'){

		$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$TripID'";

		$db_tripTimes = $obj->MySQLSelect($sql22);

		$totalSec = 0;

		$iTripTimeId = '';

		foreach($db_tripTimes as $dtT){

			if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {

				$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);

			}

		}

		$totalTimeInMinutes_trip=@round(abs($totalSec) / 60,2);

		}else {

		$totalTimeInMinutes_trip=@round(abs(strtotime($startDate) - strtotime($endDateOfTrip)) / 60,2);

	}       

	$totalHour = $totalTimeInMinutes_trip / 60;

	$tripDistance=calcluateTripDistance($tripId);

	$sourcePointLatitude=$tripData[0]['tStartLat'];

	$sourcePointLongitude=$tripData[0]['tStartLong'];

	if($totalTimeInMinutes_trip <= 1){

		$FinalDistance = $tripDistance;

	}else{

		$FinalDistance = checkDistanceWithGoogleDirections($tripDistance,$sourcePointLatitude,$sourcePointLongitude,$destination_lat,$destination_lon);

	}

	$tripDistance=$FinalDistance;

  $fPickUpPrice = $tripData[0]['fPickUpPrice'];

	$fNightPrice = $tripData[0]['fNightPrice'];

	$eFareType = get_value('trips', 'eFareType', 'iTripId', $TripID, '', 'true');

	$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);

	$fAmount = 0;

	$Fare_data = getVehicleFareConfig("vehicle_type", $iVehicleTypeId);

	// echo "<pre>"; print_r($tripData); die;

	$fPricePerKM = getVehicleCountryUnit_PricePerKm($iVehicleTypeId,$Fare_data[0]['fPricePerKM']);

	/*$Minute_Fare = round($Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip * $surgePrice,2);

	$Distance_Fare = round($fPricePerKM * $tripDistance * $surgePrice,2);

	$iBaseFare = round($Fare_data[0]['iBaseFare'] * $surgePrice,2);

	$total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare;*/

  $Minute_Fare = $Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip;

	$Distance_Fare = $fPricePerKM * $tripDistance;

	$iBaseFare = $Fare_data[0]['iBaseFare'];

	$total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare;

  $fSurgePriceDiff = (($total_fare * $surgePrice) - $total_fare);

  $total_fare = $total_fare + $fSurgePriceDiff;

  $iMinFare = round($Fare_data[0]['iMinFare'] * $surgePrice,2); 

	if($iMinFare > $total_fare){

          $total_fare = $iMinFare; 

	}        

	if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){

		$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";

		$serviceProData = $obj->MySQLSelect($sqlServicePro);

          if(count($serviceProData) > 0){

			$fAmount = $serviceProData[0]['fAmount'];

			if($eFareType == "Fixed"){

				$fAmount = $fAmount * $iQty;

				}else if($eFareType == "Hourly"){

				$fAmount = $fAmount * $totalHour;

				}else{

				$fAmount = $total_fare;

			}

			}else{    

			if($eFareType == "Fixed"){

				$fAmount = round($Fare_data[0]['fFixedFare'] * $iQty,2);

				}else if($eFareType == "Hourly"){     

				$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);

				}else{

				$fAmount = $total_fare;

			}

		}

		}else{   

		if($eFareType == "Fixed"){

			$fAmount = round($Fare_data[0]['fFixedFare'] * $iQty,2);

			}else if($eFareType == "Hourly"){

			$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);

			}else{

			$fAmount = $total_fare;

		}

	}

	$final_display_charge = $fAmount+$fVisitFee;

	$returnArr['Action']="1";

	/*$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'],'','true');

		$currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);

	$returnArr['message']=$currencySymbolRationDriver[0]['vSymbol']." ".number_format(round($final_display_charge * $currencySymbolRationDriver[0]['Ratio'],1),2);*/

	//$currencySymbol = get_value('currency', 'vSymbol', 'eDefault', 'Yes','',true);

	$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'],'','true');

	$currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);   

	$currencySymbol = $currencySymbolRationDriver[0]['vSymbol'];

	$currencyRationDriver = $currencySymbolRationDriver[0]['Ratio']; 

	$final_display_charge = $final_display_charge * $currencyRationDriver;

	$final_display_charge = round($final_display_charge,2);

	//$final_display_charge = formatNum($final_display_charge);

	$returnArr['TotalFareUberX']=$currencySymbol.' '.formatNum($final_display_charge);

	$returnArr['TotalFareUberXValue']=$final_display_charge;

	$returnArr['UberXFareCurrencySymbol']=$currencySymbol;

 }else{

  $returnArr['TotalFareUberX']="";

	$returnArr['TotalFareUberXValue']="";

	$returnArr['UberXFareCurrencySymbol']=""; 

 }  

  return $returnArr; 

}

############################################################## Display Trip Charge To Driver For UberX Trip ####################################################################################



function getSocketURL() {

	global $tconfig;

	$url = $tconfig["tsite_sc_protocol"] . $tconfig["tsite_sc_host"] . ":" . $tconfig["tsite_host_sc_port"] . $tconfig["tsite_host_sc_path"];

	return $url;

}



function publishEventMessage($channelName, $message) {

	global $tconfig, $PUBSUB_TECHNIQUE,$PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY, $uuid;

	if ($PUBSUB_TECHNIQUE == "SocketCluster") {

		$optionsOrUri = ['secure' => false, 'host' => $tconfig['tsite_sc_host'], 'port' => $tconfig['tsite_host_sc_port'], 'path' => $tconfig['tsite_host_sc_path']];

		$websocket = \SocketCluster\WebSocket::factory($optionsOrUri);

		$socket = new \SocketCluster\SocketCluster($websocket);

		

		//echo "";print_r($channelName ); exit;

		$dataCHK = $socket->publish($channelName, $message);

		//echo "";print_r($message ); exit;

		$websocket->close();

	} else if ($PUBSUB_TECHNIQUE == "PubNub") {

		$pubnub = new Pubnub\Pubnub(array(

			"publish_key" => $PUBNUB_PUBLISH_KEY,

			"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY,

			"uuid" => $uuid

		));

		$info = $pubnub->publish($channelName, $message);

	}

	return true;	

}



/* Start : Advertisement Banner */



function getAdvertisementBanners($iUserId, $eMemberType)

{

    global $obj, $ADVERTISEMENT_TYPE, $tconfig, $SHOW_ADVERTISE_AFTER_MINUTES;

    //ini_set("display_errors", 1);

    //error_reporting(E_ALL);

    $todayDate = date("Y-m-d");

    $getBanners = $obj->MySQLSelect("SELECT iAdvertBannerId,tRedirectUrl,eValidityType,eImpression,dStartDate,dExpiryDate,iImpression,vBannerImage,iCompanyId,eUserType FROM advertise_banners WHERE eStatus='Active' ORDER BY iDispOrder ASC");

    $finalBannerArr = $idArr = array();

    if ($iUserId > 0) {

        $getUserCount = $obj->MySQLSelect("SELECT count(iBannerImpLog) as TOTAL FROM banner_impression WHERE iUserId='" . $iUserId . "' AND eUserType='" . $eMemberType . "'");

        $usedCount = 0;

        if (count($getUserCount) > 0) {

            $usedCount = $getUserCount[0]['TOTAL'];

        }

        for ($r = 0; $r < count($getBanners); $r++) {



        	$filtro='show';



            $validity = $getBanners[$r]['eValidityType'];

            $impression = $getBanners[$r]['eImpression'];

            $dStartDate = $getBanners[$r]['dStartDate'];

            $dExpiryDate = $getBanners[$r]['dExpiryDate'];

            $iImpression = $getBanners[$r]['iImpression'];

            $iAdvertBannerId = $getBanners[$r]['iAdvertBannerId'];

            $path = $tconfig['tsite_upload_advertise_banner'] . '/' . $getBanners[$r]['vBannerImage'];



            if ($getBanners[$r]['eUserType']!='All')

	            if ($eMemberType!=$getBanners[$r]['eUserType'])

	            	unset($filtro);





            if ($getBanners[$r]['iCompanyId']) {



        		if ($eMemberType == "Passenger") {

					$user_iCompanyId = get_value("register_user", "iCompanyId", "iUserId", $iUserId, '', 'true');

				} else if ($eMemberType == "Driver") {

					$user_iCompanyId = get_value("register_driver", "iCompanyId", "iDriverId", $iUserId, '', 'true');

				}



            	if ($user_iCompanyId!=$getBanners[$r]['iCompanyId'])

            		unset($filtro);

            }

        





            

            // if ($getBanners[$r]['iCompanyId']!=NULL && $eMemberType!=$getBanners[$r]['iCompanyId'])

            // unset($filtro);





            if ($filtro=='show') {



	            $type = pathinfo($path, PATHINFO_EXTENSION);

	            $data = file_get_contents($path);

	            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

	            $getBanners[$r]['dataImg'] = $base64;



	            if ($validity == "Custom" && $dStartDate >= $todayDate && $dExpiryDate >= $todayDate) {

	                if ($impression == "Unlimited") {

	                    $finalBannerArr[$iAdvertBannerId] = $getBanners[$r];

	                    $idArr[] = $iAdvertBannerId;

	                } else if ($iImpression > $usedCount) {

	                    $finalBannerArr[$iAdvertBannerId] = $getBanners[$r];

	                    $idArr[] = $iAdvertBannerId;

	                }

	            } else if ($validity == "Permanent") {

	                if ($impression == "Unlimited") {

	                    $finalBannerArr[$iAdvertBannerId] = $getBanners[$r];

	                    $idArr[] = $iAdvertBannerId;

	                } else if ($iImpression > $usedCount) {

	                    $finalBannerArr[$iAdvertBannerId] = $getBanners[$r];

	                    $idArr[] = $iAdvertBannerId;

	                }

	            }

        	}

        }

    }

    //print_r($finalBannerArr);die;

    if ($eMemberType == "Passenger") {

        $tableName = "register_user";

        $fieldName = "iUserId";

    } else if ($eMemberType == "Driver") {

        $tableName = "register_driver";

        $fieldName = "iDriverId";

    } else {

        $tableName = "company";

        $fieldName = "iCompanyId";

    }

    $getLastBanner = $obj->MySQLSelect("SELECT iAdvertBannerId,tSeenAdvertiseTime FROM " . $tableName . " WHERE " . $fieldName . "='" . $iUserId . "'");

    $lastBannerId = $newAdvertBannerId = 0;

    if (count($getLastBanner) > 0) {

        $lastBannerId = $getLastBanner[0]['iAdvertBannerId'];

        $tSeenAdvertiseTime = $getLastBanner[0]['tSeenAdvertiseTime'];

    }

    $dateTime = date("Y-m-d H:i:s");

    //echo "<pre>";

    $minutes = 1;

    if ($tSeenAdvertiseTime != "0000-00-00 00:00:00") {

        $minutes = round(abs($dateTime - $tSeenAdvertiseTime) / 60, 2);

    }

    $start_date = new DateTime($tSeenAdvertiseTime);

    $since_start = $start_date->diff(new DateTime($dateTime));

    $minutes = $since_start->i;

    //echo $SHOW_ADVERTISE_AFTER_MINUTES;die;

    if ($minutes > $SHOW_ADVERTISE_AFTER_MINUTES) {

        if ($ADVERTISEMENT_TYPE == "Sequential") {

            if (count($getLastBanner) > 0) {

                $lastBannerId = $getLastBanner[0]['iAdvertBannerId'];

            }

            $seqData = $update_banner = array();

            foreach ($finalBannerArr as $key => $val) {

                if ($key > $lastBannerId) {

                    $seqData = $finalBannerArr[$key];

                    $newAdvertBannerId = $key;

                    break;

                }

            }

            if ($newAdvertBannerId == 0) {

                $newKeysArr = array_keys($finalBannerArr);

                if (count($newKeysArr) > 0) {

                    $newAdvertBannerId = $newKeysArr[0];

                    $seqData = $finalBannerArr[$newAdvertBannerId];

                }

            }

            return $seqData;

            //Sequential Banner Code here

        } else if ($ADVERTISEMENT_TYPE == "Random") { //Random Banner here

            $randKey = array_rand($idArr);

            $randValue = $idArr[$randKey];

            return $finalBannerArr[$randValue];

        } else {

            $finalBannerArr = "";

            return $finalBannerArr;

        }

    } else {

        $finalBannerArr = "";

        return $finalBannerArr;

    }

}

function insertBannereImpressionCount($iAdvertBannerId, $iUserId, $eMemberType)

{

    global $obj, $tconfig, $_REQUEST;

    ini_set("display_errors", 1);

    error_reporting(E_ALL);

    $dateTime = date("Y-m-d H:i:s");

    if ($iAdvertBannerId > 0 && $iUserId > 0) {

        $insert_banner = array();

        $insert_banner['iAdvertBannerId'] = $iAdvertBannerId;

        $insert_banner['dDateTime'] = $dateTime;

        $insert_banner['eUserType'] = $eMemberType;

        $insert_banner['vIP'] = get_client_ip();

        $insert_banner['iUserId'] = $iUserId;

        $id = $obj->MySQLQueryPerform("banner_impression", $insert_banner, 'insert');

        $returnArr['Action'] = "1";

        $getBanners = $obj->MySQLSelect("SELECT vBannerImage FROM advertise_banners WHERE iAdvertBannerId='" . $iAdvertBannerId . "'");



        $base64 = $path = "";

        if (count($getBanners) > 0) {

            $path = $tconfig['tsite_upload_advertise_banner'] . '/' . $getBanners[0]['vBannerImage'];

            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = file_get_contents($path);

            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        }

        if ($eMemberType == "Passenger") {

            $tableName = "register_user";

            $fieldName = "iUserId";

        } else if ($eMemberType == "Driver") {

            $tableName = "register_driver";

            $fieldName = "iDriverId";

        } else {

            $tableName = "company";

            $fieldName = "iCompanyId";

        }

        $whereUserId = " $fieldName = '" . $iUserId . "'";

        $update_banner['iAdvertBannerId'] = $iAdvertBannerId;

        $update_banner['tSeenAdvertiseTime'] = $dateTime;

        $obj->MySQLQueryPerform($tableName, $update_banner, 'update', $whereUserId);

        //header("Location:".$path);die;

        if (isset($_REQUEST['deviceHeight']) && $_REQUEST['deviceHeight'] != "" && $_REQUEST['deviceWidth'] != "") {

            $path = $tconfig['tsite_url'] . "resizeImg.php?src=" . $path . "&w=" . $_REQUEST['deviceWidth'] . "&h=" . $_REQUEST['deviceHeight'];

        }

        header("Location:" . $path);

        die;

        //$path = 

        die;

        //echo $base64;

        //die;

    } else {

        $returnArr['Action'] = "0";

        $returnArr['message'] = "LBL_NO_DATA_AVAIL";

    }

    print_r($returnArr);

    die;

    return $returnArr;

}

/* End : Advertisement Banner */

?>