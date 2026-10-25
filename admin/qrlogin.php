<?php

//请勿删除版权信息，否则出现问题将不再保持售后修复！
error_reporting(0);
session_start();
header("Content-type: application/json");
class wj_qrlogin
{
	public function getqrpic()
	{
		$_var_0 = "https://wj.qq.com/api/session/authorization/create_token";
		$_var_1 = $this->get_curl($_var_0, "post=1");
		$_var_2 = json_decode($_var_1, true);
		if (isset($_var_2["status"]) && $_var_2["status"] == 1) {
			return array("code" => 0, "qrurl" => "https://wj.qq.com/api/session/authorization?token=" . $_var_2["data"]["token"] . "&scene_type=user", "token" => $_var_2["data"]["token"]);
		} else {
			return array("code" => -1, "msg" => "二维码获取失败");
		}
	}
	public function qrlogin($token)
	{
		if (empty($token)) {
			return array("code" => -1, "msg" => "token不能为空");
		}
		$_var_3 = "https://wj.qq.com/api/session/authorization/check_token?token=" . urlencode($token) . "&scene_type=user";
		$_var_4 = $this->get_curl($_var_3, 0, 0, 1, 1);
		$_var_5 = json_decode($_var_4["body"], true);
		if ($_var_5["data"]["code"] == 3) {
			$_var_6 = "";
			foreach ($_var_4["cookie"] as $_var_7) {
				if (strpos($_var_7, "session_user") !== false) {
					$_var_6 = $_var_7;
				}
			}
			$_var_8 = json_decode($this->get_curl("https://wj.qq.com/api/account", 0, $_var_6), true);
			if (isset($_var_8["data"]["user_id"])) {
				$_SESSION["thirdlogin_type"] = $_var_8["data"]["user_type"];
				$_SESSION["thirdlogin_uin"] = $_var_8["data"]["user_id"];
				return array("code" => 0, "uin" => $_var_8["data"]["user_id"], "type" => $_var_8["data"]["user_type"]);
			} else {
				return array("code" => -1, "msg" => "登录成功，获取用户信息失败");
			}
		} elseif ($_var_5["data"]["code"] == 1) {
			return array("code" => 1, "msg" => "请扫描二维码");
		} elseif ($_var_5["data"]["code"] == 2) {
			return array("code" => 2, "msg" => "正在验证二维码");
		} else {
			return array("code" => -1, "msg" => "二维码失效，请刷新页面");
		}
	}
	private function get_curl($url, $post = 0, $cookie = 0, $header = 0, $split = 0)
	{
		$_var_9 = curl_init();
		curl_setopt($_var_9, CURLOPT_URL, $url);
		curl_setopt($_var_9, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($_var_9, CURLOPT_SSL_VERIFYHOST, false);
		$_var_10[] = "Accept: application/json";
		$_var_10[] = "Accept-Encoding: gzip,deflate,sdch";
		$_var_10[] = "Accept-Language: zh-CN,zh;q=0.8";
		$_var_10[] = "Connection: close";
		curl_setopt($_var_9, CURLOPT_HTTPHEADER, $_var_10);
		if ($post) {
			curl_setopt($_var_9, CURLOPT_POST, 1);
			curl_setopt($_var_9, CURLOPT_POSTFIELDS, $post);
		}
		if ($header) {
			curl_setopt($_var_9, CURLOPT_HEADER, TRUE);
		}
		if ($cookie) {
			curl_setopt($_var_9, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($_var_9, CURLOPT_REFERER, "https://wj.qq.com/login.html?s_url=https%3A%2F%2Fwj.qq.com%2Fmine.html");
		curl_setopt($_var_9, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
		curl_setopt($_var_9, CURLOPT_ENCODING, "gzip");
		curl_setopt($_var_9, CURLOPT_RETURNTRANSFER, 1);
		$_var_11 = curl_exec($_var_9);
		if ($split) {
			$_var_12 = curl_getinfo($_var_9, CURLINFO_HEADER_SIZE);
			$header = substr($_var_11, 0, $_var_12);
			$_var_13 = substr($_var_11, $_var_12);
			$_var_11 = array();
			$_var_11["header"] = $header;
			$_var_11["body"] = $_var_13;
			preg_match_all("/Set-Cookie: (.*?);/m", $header, $_var_14);
			$_var_11["cookie"] = $_var_14[1];
		}
		curl_close($_var_9);
		return $_var_11;
	}
}
if (strpos($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]) === false) {
	exit("{\"code\":-1}");
}
$login = new wj_qrlogin();
if ($_GET["do"] == "qrlogin") {
	$array = $login->qrlogin($_GET["token"]);
} elseif ($_GET["do"] == "getqrpic") {
	$array = $login->getqrpic();
}
echo json_encode($array);