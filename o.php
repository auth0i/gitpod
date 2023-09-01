<?php
//error_reporting(0);

$o_setup = ["mail_list"=> "omails.txt",'file_leads' => 'sorted/o365_leads.txt', 'file_leads_1' => 'sorted/godaddy_o365_leads.txt', 'file_leads_2' => 'sorted/adfs_o365_leads.txt', 'file_leads_3' => 'sorted/okta_o365_leads.txt', 'file_leads_4' => 'sorted/redirect_o365_leads.txt', 'file_test' => 'sorted/not_o365_leads.txt', 'deletesent' => 'Yes'];

function dns_exists($email) {
    $mail_part = explode("@", $email);
    $domain = $mail_part[1];
    if (checkdnsrr($domain, 'MX')) {
        return true;
    }
}
function is_email($email) {
    $valid = true;
    if (strlen($email) < 3) {
        return $valid = 'email_too_short';
    }
    if (strpos($email, '@', 1) === false) {
        return $valid = 'email_no_at';
    }
    list($local, $domain) = explode('@', $email, 2);
    if (!preg_match('/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]+$/', $local)) {
        return $valid = 'local_invalid_chars';
    }
    if (preg_match('/\.{2,}/', $domain)) {
        return $valid = 'domain_period_sequence';
    }
    if (trim($domain, " \t\n\r\0\x0B.") !== $domain) {
        return $valid = 'domain_period_limits';
    }
    $subs = explode('.', $domain);
    if (2 > count($subs)) {
        return $valid = 'domain_no_periods';
    }
    foreach ($subs as $sub) {
        if (trim($sub, " \t\n\r\0\x0B-") !== $sub) {
            return $valid = 'sub_hyphen_limits';
        }
        if (!preg_match('/^[a-z0-9-]+$/i', $sub)) {
            return $valid = 'sub_invalid_chars';
        }
    }
    if ($valid == true) return true;
}
function isValidEmail($email) {
    if (is_email($email) === true) return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function checkO365($email) {
    global $o_setup;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://login.microsoftonline.com/common/GetCredentialType?mkt=ar-SA");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"username\":\"" . strip_tags($email) . "\",\"isOtherIdpSupported\":true,\"checkPhones\":false,\"isRemoteNGCSupported\":false,\"isCookieBannerShown\":false,\"isFidoSupported\":false,\"originalRequest\":\"rQIIAYVSz2vTYABt2q1uIjo8efAwwZPaNmmT1BWGZEkbW_sl7Zq2Sy6lzY_2S5Mvv762WU4ePc6DMAdePA4E8SQD_4Gdhkf_AhEFEUEv_ljZxZvv8Hg83uHBe3cyVJ6q3CYvwOaWnCMti8rp5lL9g_D65Y3Cn8NfJ4cPuFfzI_X12o_vx8StCcZ-VCkUfC_EQyfvWRbUzbzuuQVniAyIxm8J4owgPhLEUXp1GOY63HE6YktsuVgskwxDb21RFENTeUkY08DmYpAArNrqAnRIUhbr-01lTKl2G8tKz9UULpbsnguKbUZNdEru91ypr2JVaSeAJ0mg6KWmMrHVPlh6saRMY0nctWVBm35IX5O5GZ4Ul-SFMDG_pdctL3QHvhfho8x7QvZNVDd4DyFTx_llzEQY6kMMPdQKPd8MMTSj7VguskoU0DWYNOuIAfuOvAerOBd3dtmdGCf-ZJcRuR5fnCCEYkNwgVAFQwOL_cYCh5wE2U5Z2Rk1jNa0uxg_5GtCSZqOZnQrWMhcO3G6M6fxqDfoC7WmABbVuN1kgi7XcQV1GAe8hiJH6-eoTjBwSdEx1XmXbne1Wc3xImUUabHU6LnULBw0vEB9k8meD-F66DRz9bw_gsamH3oWdMyzFeLTyhUyU1lby25kbqQ2Uz9XiJer5xN_OXx678W73-Dx55vw2XM2dbpa2DOrPjl2_Pm8wTNbpSY_set4Srt3Rdiy_FlwXy-x-xpjiQbYLleogyxxkM1-zRJPLqVO1v9_kL81\",\"country\":\"EG\",\"flowToken\":\"AQABAAEAAADXzZ3ifr-GRbDT45zNSEFEWJXpyuWjnvuUvXgJzUEtusd4IYw8zGEFR87894cMVWHBV0KMOIX4HvhWhpRPKt0zby4imQvu543UIjM7HGhKAOontuB4djF2c79B_Imyfzoa0pdt5hf6z4tOEO2ITeEjfN_EDBq4Y-6ll2jdnNfN5aJi4RNUnBqcCJH3rtEidxVCt9zZqC5CrlIBLUGt7YoZHrU9-ubXG9jaCyAuCr7Yf-DfBOwHRsRBOP0rUv_eHYWsNI3mtQ-4ymHCyKXwpvn2v7qoCDxFxOkvvGvVxSzCrLRQOvkbyr-Zmo9NSIL1kaz555TFp6HpjkeTn-hhpDiusMv3Vor8wmg7MNMXeEEjnwREbIm0hLnRUz2tVOk_0FAyLqZf6XvrQDirQlwLwn0qIAA\"}");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array();
    $headers[] = "Cache-Control: no-cache";
    $headers[] = "Origin: https://login.microsoftonline.com";
    $headers[] = "Accept-Encoding: gzip, deflate, br";
    $headers[] = "Accept-Language: ar,en-US;q=0.9,en;q=0.8";
    $headers[] = "Client-Request-Id: fd98ff2f-98ba-413f-ad76-9a59b" . rand(1000, 9999) . "f7f4";
    $headers[] = "Canary: AQABAAAAAADXzZ3ifr-GRbDT45zNSEFE1Yij0xjKCPe46utuEjix0xFhYMANXhFa5p7IhJRNOcM32dsHTJKaZiWoSWxkXD_i3Dhku9LA8o9sDbigPtjoUyLP2HqK726RpwczMbvHwKIgMXRsG_bPwtaUjz7nlFvKO3Iy2YEV32b-RqvumAgCY6goH_C8EO_mFENwkG1KTVLH1yGDOmnWehzg8YeuCh2OjqaviJClLxVF7nGZ5-NWCCAA";
    $headers[] = "Cookie: esctx=AQABAAAAAADXzZ3ifr-GRbDT45zNSEFEwPEnhXbFSf1iMCwkCQPrCrBXJSZHS_xkv5uqffLZwxbaL-CZ3esKaQG_FdmvsAeMNipA_0DBhG2EjayAcJ9UDFp6NtFvxM7L1w40ElTmn31V-_Yj2W5Pb7fY3eTzKaYIxWWo3FyCw0L8PsJvgjWobM6gYCRi1St7gUe3rPW2BiogAA; stsservicecookie=ests; ESTSAUTHPERSISTENT=AQABAAQAAADXzZ3ifr-GRbDT45zNSEFEc64pmD-OsSat82EijNRKHkOusB8V2HKIpaTsQVNUZjvisD0pnx9G4O__FAd3WcbFHsANVMyltaYQR_PLijhDFoq5qbugb2DVWXb_GkP8wHJIUghAvMYphw6F2Tfrw_3c_GBnXd1LDjwOQ_dzaca--Ck0kFafxRkUl96gxhwom8diqc9yqsqPB268EL1_Kc6vfZRrz6oxBPRGzjlgcPFKJnnWEN_zbRvy5D0ZAaNFBGiljWTxSFdIo87W52dDPk41CwO1zDcNT1Wu-MUIoQH7gfixbByJgqULjz0_j38uh9JpfijpxonFj5MLfaTFAvkN1NmojK4ZfpNLkpAt1AwZYOyRIbVpuNv91WF6XWueekYLx2e3_we1IjwwtB_Mek5hcEzQIQirziaKlAggUinW4EuyTo3J40tN-Kh7bUrt2G27-jPQHGVIZ71XwCcw7floeY8woY1Yuit8_z6w_e7fFyAAIABAAOAAAAA; ESTSAUTH=AQABAAQAAADXzZ3ifr-GRbDT45zNSEFEjsO0IywAqbPIaBFYrP6ZOrVNRQ06NFY4OPovae861_Iq9YXBf8BME8sbknXQcnOfmVbGkYk1umL9F4m4jldK-01C6tMzeuJ1yQcllRiDQfhsH_wbfBGgy6dCTx7VdWyCEsbPnZ4XRG7iVLQReGQuROl-sRo_pjxbeVE60BvIE7Th6ahPC0yo3ftTxAkDr1tar-TJmb3oUMO0Wc9BiwpeMSAAIABAACAAAAA; ESTSAUTHLIGHT=+; ESTSSC=00; buid=AQABAAEAAADXzZ3ifr-GRbDT45zNSEFEtvIKH5yEG81RDCw061lh2rlzCc6EA503_OOpBtBOyBM2fXrA9IRl84m4Oz6CQVniCJaRpTzLG20S4IJrvlpF05NzDrLh9jpEt1xSCGdqMDJcOgypHz8RSyDPN2Y6nKzxmoWXJ7usTerqVXjheVvMTSAA; x-ms-gateway-slice=017";
    $headers[] = "Connection: keep-alive";
    $headers[] = "Hpgact: 1800";
    $headers[] = "Hpgrequestid: 2c9198ed-bd9d-4dfe-80ec-1c699" . rand(1000, 9999) . "600";
    $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    $headers[] = "Hpgid: 1104";
    $headers[] = "Accept: application/json";
    $headers[] = "Referer: https://login.microsoftonline.com/common/oauth2/authorize?client_id=00000006-0000-0ff1-ce00-000000000000&response_mode=form_post&response_type=code+id_token&scope=openid+profile&state=OpenIdConnect.AuthenticationProperties%3dxO26Tsq4FizLIn5MylOXiEt-xSR6BxtzphR5GAVC2hnnnxdDmMDEMadtGWJwtrANi6S7TBbJdPkUwgHCFD3Nkbu4PqwOAQzlUulJKV_WDFLDMwExQL5qUASmDYaxqCZnslZW-1Sq_m0GleYvU4QUZuFlosTbsZxNJVm1ur_JoqY&nonce=636722705549911541.NDg4MjAxMzMtYjYwMS00OGIyLTg1YjQtOTVmZTAxNjVmM2Q5Yzc1OWVmNWYtYTQzMC00MTc3LThjYWMtYTQxNTkxNGRjODZk&redirect_uri=https%3a%2f%2fportal.office.com%2flanding&ui_locales=ar-SA&mkt=ar-SA&client-request-id=fd98ff2f-98ba-413f-ad76-9a59b008f7f4";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
    $check = curl_exec($ch);
    $result = json_decode($check, true);
    $ExistsResult = trim(strip_tags($result['IfExistsResult']));
    $prefcredential = trim(strip_tags($result['Credentials']['PrefCredential']));
    $fedredirectUrl = trim(strip_tags($result['Credentials']['FederationRedirectUrl']));
    $domaintype = trim(strip_tags($result['EstsProperties']['DomainType']));
    $usertenant = !empty($fedredirectUrl) ? '' : $result['EstsProperties']['UserTenantBranding'];
    if (!empty($usertenant) && (($ExistsResult == 0))) {
        $BannerLogo = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['BannerLogo']));
        $TileLogo = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['TileLogo']));
        $TileDarkLogo = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['TileDarkLogo']));
        $background = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['Illustration']));
        $bgColor = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['BackgroundColor']));
        $boilerText = trim(strip_tags($result['EstsProperties']['UserTenantBranding'][0]['BoilerPlateText']));
        $save = 'yes';
    }
    if ((empty($usertenant) && ($ExistsResult == 0)) || (($domaintype == 3) && ($ExistsResult == 6) && ($prefcredential == 1)) || (($domaintype == 4) && ($ExistsResult == 6) && ($prefcredential == 4)) || (($domaintype == 3) && ($ExistsResult == 0) && ($prefcredential == 1))) {
        $save = 'yes';
    } else {
        $save = 'no';
    }
    if ($save == 'yes') {
        if (!empty($fedredirectUrl) && (strpos($fedredirectUrl, 'https://sso.godaddy.com') !== false  || strpos($fedredirectUrl, 'https://sso.secureserver.net') !== false)) {
            file_put_contents($o_setup['file_leads_1'], strtolower($email) . "
", FILE_APPEND);        echo "[+] Saving Email: $email to o365 GoDaddy file [+]". "\r\n";

        } elseif (!empty($fedredirectUrl) && (strpos($fedredirectUrl, '/adfs') !== false)) {
            file_put_contents($o_setup['file_leads_2'], strtolower($email) . "
", FILE_APPEND);
        echo "[+] Saving Email: $email to o365 Adfs file [+]". "\r\n";
        } elseif (!empty($fedredirectUrl) && (strpos($fedredirectUrl, 'okta') !== false)) {
            file_put_contents($o_setup['file_leads_3'], strtolower($email) . "
", FILE_APPEND);        echo "[+] Saving Email: $email to o365 Oktafile [+]". "\r\n";

        } elseif (!empty($fedredirectUrl) && (strpos($fedredirectUrl, 'okta') === false) && (strpos($fedredirectUrl, 'okta') === false) && (strpos($fedredirectUrl, '/adfs') === false) && (strpos($fedredirectUrl, 'https://sso.godaddy.com') === false)) {
            file_put_contents($o_setup['file_leads_4'], strtolower($email) . "
", FILE_APPEND);        echo "[+] Saving Email: $email to o365 Redirect file [+]". "\r\n";

        } else {
            file_put_contents($o_setup['file_leads'], strtolower($email) . "
", FILE_APPEND);        echo "[+] Saving Email: $email to o365 file [+]". "\r\n";

        }
//        echo "\(O^O)/ : [+] Saving Email: $email to file [+] : \(O^O)/";
    }
    if ($save == 'no') {
        file_put_contents($o_setup['file_test'], strtolower($email) . "
", FILE_APPEND);
        echo "-(O,O)- : [+] Invalid o365 Email: $email [+] : -(O,O)-". "\r\n";
    }
}

$hostname = 'mineme';
$expired = (time() > strtotime('2023-11-19'));
$hotdata = 'mineme';

if($hostname !== $hotdata){
echo 'Unauthorized Usage';
}else{
	if(!$expired){

    if(!is_file($o_setup['mail_list'])) {
         echo " [ \e[0;31m MAILIST NOT FOUND - PLEASE CHECK YOUR MAILIST NAME !\e[0m ]\r\n";
         die();
    }
@mkdir('sorted');
    $file = file_get_contents($o_setup['mail_list']);

    if ($file) {
        $ext = explode("\n", $file);
        echo "\r\n";
        echo "      \e[101m Sorting Information \e[0m\n";
		echo "\r\n";
        echo "      Total Number of Emails  \e[0m:\e[0m ".count($ext). "\r\n";
        echo "      Email Source            \e[0m:\e[0m ".$o_setup['mail_list']. "\r\n";
        echo "\r\n";
        echo "\e[0m   █████████████████████████████\e[1;32m o365 Email Juggler - Sorting started \e[0m█████████████████████████████\e[0m\n";
        echo "\r\n";

            $time_start = microtime(true);
        foreach ($ext as $num => $email) {

 if (isValidEmail($email) && dns_exists($email)) {
                            checkO365($email);
}
        }
                    $time_end = microtime(true);
            $execution_time = ($time_end - $time_start) / 60;
        echo "\r\n";
        echo "\r\n";
        echo "      Total Execution Time Spent  \e[0m:\e[0m ".sprintf('%1.4f', $execution_time). " minutes\r\n";

echo "\r\n";
        echo "\e[0m   █████████████████████████████\e[1;32m o365 Email Juggler - Sorting Completed \e[0m█████████████████████████████\e[0m\n";
        echo "\r\n";
        echo "\n";
    }
}else{
		echo 'Expired';
	}
}

?>
