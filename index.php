<?php
require_once __DIR__ . "/src/PHPTelebot.php";
require_once __DIR__ . "/src/xc.php";
error_reporting(E_ALL); ini_set('display_errors', 1);
$banner = file_get_contents("src/plugins/banner");
$options = ["parse_mode" => "html", "reply" => true];

// Read token & username
function readToken($input)
{
    $data = file_get_contents("databot");
    $raw = explode("\n", $data);
    return $input == "token" ? $raw[0] : $raw[1];
}

// token user
$bot = new PHPTelebot(readToken("token"), readToken("username"));

// random messages
$ads = [
		"<span class='tg-spoiler'>Donate me: <a href='https://helmiau.com/pay'>https://helmiau.com/pay</a>.</span>",
		"<span class='tg-spoiler'>Keep PHPTeleBotWrt up-to-date with <u><strong>phpmgrbot u</strong></u> command in Terminal or through /update in telegram bot chat.</span>",
		"<span class='tg-spoiler'>Read PHPTeleBotWrt wiki and information <a href='https://www.helmiau.com/blog/phptelebotwrt'>here</a>.</span>",
		"<span class='tg-spoiler'>PHPTeleBotWrt devs: <a href='https://github.com/radyakaze/phptelebot'>radyakaze</a>, <a href='https://github.com/OppaiCyber/XppaiWRT'>OppaiCyber-XppaiWRT</a>, <a href='https://github.com/xentolopx/eXppaiWRT'>xentolopx-eXppaiWRT</a> and <a href='https://helmiau.com/pay'>Helmi Amirudin</a>.</span>",
		"<span class='tg-spoiler'>Make sure your device always connected to network.</span>",
    ];
$randAds = $ads[array_rand($ads)];

// Ping Command
$bot->cmd("/ping", function () {
    $start_time = microtime(true);
	Bot::sendMessage("Pinging...", $GLOBALS["options"]);
    $end_time = microtime(true);
    $diff = round(($end_time - $start_time) * 1000);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"Ping time taken: " . $diff . "ms"
		. "\n\n" . $GLOBALS["randAds"]
		,$GLOBALS["options"]);
});

// start bot
$bot->cmd("/start", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"Welcome to PHPTeleBotWrt!\nRun /cmdlist to see all available comands.\n\n Source: https://github.com/helmiau/PHPTeleBotWrt\n Donate: https://helmiau.com/pay"
		. "\n\n" . $GLOBALS["randAds"]
		,$GLOBALS["options"]);
});

// list of commands
$bot->cmd("/cmdlist", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
"📁Bot Manager
 ↳/update : Update PHPTeleBotWrt binaries
 
 📁Aria2 Commands
 ↳/aria2add : Add task
 ↳/aria2stats : Aria2 status
 ↳/aria2pause : Pause all
 ↳/aria2resume : Resume all
 
📁OpenClash Commands
 ↳/oc : OC Information
 ↳/ocst : Start/Restart Openclash
 ↳/ocsp : Stop Openclash
 ↳/ocpr : Proxies status 
 ↳/ocrl : Rule list 
 ↳/ocup : Update Openclash version

📁MyXL Commands
 ↳/myxl : Bandwidth usage 
 ↳/setxl 087 : Set default number

📁File Uploader
 ↳/upload : Upload file to OpenWrt

📁System Information
 ↳/vnstat : Bandwidth usage 
 ↳/vnstati : Better Bandwidth usage 
 ↳/memory : Memory status 
 ↳/myip : Get ip details 
 ↳/speedtest : Speedtest 
 ↳/ping : Ping bot
 ↳/sysinfo : System Information"
		. "\n\n" . $GLOBALS["randAds"]
		,$GLOBALS["options"]);
});

// yaml config upload
$bot->on('document', function() {
  // download file
    $token = readToken("token");
    $message = Bot::message();
    $file_name = $message['document']['file_name'];
    $file_id = $message['document']['file_id'];
    $raw = json_decode(Bot::getFile($file_id),true);
    $file_path = $raw['result']['file_path'];
    $file_path_custom = $message['caption'];
    if ($file_path_custom === null) {
        $file_out_path = '/root';
    } else {
        $file_out_path = $file_path_custom;
    }
    $wget = shell_exec("wget -O \"$file_out_path/$file_name\" \"https://api.telegram.org/file/bot$token/$file_path\"");
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"File <strong>$file_name</strong> uploaded to <strong>$file_out_path</strong> on OpenWrt, please check it manually.\n\nFile <strong>$file_name</strong> telah di unggah ke <strong>$file_out_path</strong> di OpenWrt, silahkan periksa file secara manual."
		. "\n\n" . $GLOBALS["randAds"]
		,$GLOBALS["options"]);
 });

// OpenWRT Command
// OpenClash Proxies
$bot->cmd("/ocpr", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . Proxies() . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
		,$GLOBALS["options"]);
});

// OpenClash Start
$bot->cmd("/ocst", function () {
	Bot::sendMessage(
		"Start/Restarting Openclash ... "
        ,$GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . shell_exec("uci set openclash.config.enable=1 && uci commit openclash && /etc/init.d/openclash restart >/dev/null 2>&1 &") . "</code>"
		. "Openclash started successfully!."
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// OpenClash Start
$bot->cmd("/ocsp", function () {
	Bot::sendMessage(
		"Stopping Openclash ... "
        ,$GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . shell_exec("uci set openclash.config.enable=0 && uci commit openclash && /etc/init.d/openclash stop >/dev/null 2>&1 &") . "</code>"
		. "Openclash stopped successfully!."
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// OpenClash Update
$bot->cmd("/ocup", function () {
    $ocver = shell_exec("echo -e $(opkg status luci-app-openclash 2>/dev/null |grep 'Version' | awk -F 'Version: ' '{print$2}')");
	Bot::sendMessage(
		"Checking Openclash version update ... "
        ,$GLOBALS["options"]);
    Bot::sendMessage(
		"<code>" . shell_exec("/usr/share/openclash/openclash_update.sh") . "</code>"
        ,$GLOBALS["options"]);
    $ocver2 = shell_exec("echo -e $(opkg status luci-app-openclash 2>/dev/null |grep 'Version' | awk -F 'Version: ' '{print$2}')");
	if ($ocver2 === $ocver) {
		$ocupinfo = "Openclash is already at latest version";
	} else {
		$ocupinfo = "Openclash updated to $ocver2";
	}
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"$ocupinfo"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// vnstat
$bot->cmd("/vnstat", function ($input) {
    $input = escapeshellarg($input);
    $output = shell_exec("vnstat $input 2>&1");
    if ($output === null) {
        Bot::sendMessage(
			$GLOBALS["banner"] . "\n" .
			"Invalid input or vnstat not found"
			. "\n" . $GLOBALS["randAds"]
			,$GLOBALS["options"]);
    } else {
        Bot::sendMessage(
			$GLOBALS["banner"] . "\n" .
			"<code>" . $output . "</code>"
			. "\n" . $GLOBALS["randAds"]
			,$GLOBALS["options"]);
    }
});

// vnstati
$bot->cmd("/vnstati", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" 
		. $GLOBALS["randAds"]
		,$GLOBALS["options"]);

    $image_files = [
        'summary' => 'vnstati -s -i br-lan -o summary.png',
        'hourly' => 'vnstati -h -i br-lan -o hourly.png',
        'daily' => 'vnstati -d -i br-lan -o daily.png',
        'monthly' => 'vnstati -m -i br-lan -o monthly.png',
        'yearly' => 'vnstati -y -i br-lan -o yearly.png',
        'top' => 'vnstati --top 5 -i br-lan -o top.png',
    ];
    
    foreach ($image_files as $image_file) {
        shell_exec($image_file);
    }
    
    foreach ($image_files as $file_name => $command) {
        Bot::sendPhoto($file_name . '.png');
    }
    
    shell_exec("rm *.png");
	
});


// Check RAM/Memory
$bot->cmd("/memory", function () {
    $meminfo = file("/proc/meminfo");
    $total = intval(trim(explode(":", $meminfo[0])[1])) / 1024;
    $free = intval(trim(explode(":", $meminfo[1])[1])) / 1024;
    $used = $total - $free;
    $percent = round(($used / $total) * 100);
    $bar = str_repeat("■", round($percent / 5));
    $bar .= str_repeat("□", 20 - round($percent / 5));
    $output =
		$GLOBALS["banner"] . "\n" .
        "<code>Memory usage: \nBar: " .
        $bar .
        "\nUsed: $used MB \nAvailable: $free MB \nTotal: $total MB \nUsage: $percent%</code>"
		. "\n\n" . $GLOBALS["randAds"];
    Bot::sendMessage($output, $GLOBALS["options"]);
});

// Systemm info
$bot->cmd("/sysinfo", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/sysinfo.sh -bw") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// OpenClash
$bot->cmd("/oc", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/oc.sh") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// My IP Address info
$bot->cmd("/myip", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . myip() . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// OpenClash Rules
$bot->cmd("/ocrl", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . Rules() . "</code>"
		. "\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

// Speedtest
$bot->cmd("/speedtest", function () {
    Bot::sendMessage("<code>Speedtest on Progress</code>", $GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] .
		"<code>" . Speedtest() . "</code>"
		. "\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

//Myxl cmd
$bot->cmd("/setxl", function ($number) {
    if ($number == "") {
        Bot::sendMessage(
            "Masukan nomor yang mau di set sebagai default /setxl 087x",
            $GLOBALS["options"]
        );
    } else {
        shell_exec("echo '$number' > xl");
        Bot::sendMessage(
            "Nomer $number disetting sebagai default\nSilahkan gunakan cmd /myxl tanpa memasukkan nomor",
            $GLOBALS["options"]
        );
    }
});

$bot->cmd("/myxl", function ($number) {
    Bot::sendMessage("Checking number $number MyXL on progress...", $GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . MyXL($number) . "</code>"
		. "\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});
//Myxl cmd end

//upload cmd
$bot->cmd("/upload", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"Upload a file to a directory (default path: /root).\nAdd file caption to upload file in to customized directory.\nMultiple files upload is unsupported.\n\nUnggah berkas ke folder tertentu dalam OpenWrt (folder default: /root)\nTambahkan caption ketika upload file, agar file di unggah ke folder yang sudah ditentukan di caption.\nTidak mendukung upload banyak file."
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

//adb cmd
$bot->cmd("/adb", function () {
    Bot::sendMessage("<code>ADB on Progress</code>", $GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"<code>" . ADB() . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

//Aria2 cmd
$bot->cmd("/aria2add", function ($url) {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/add.sh $url") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

$bot->cmd("/aria2stats", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/stats.sh") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

$bot->cmd("/aria2pause", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/pause.sh") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

$bot->cmd("/aria2resume", function () {
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
        "<code>" . shell_exec("src/plugins/resume.sh") . "</code>"
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

//Aria2 cmd end

// phpbotmgr
$bot->cmd("/update", function () {
    Bot::sendMessage(
		"Updating PHPTeleBotWrt..."
        ,$GLOBALS["options"]);
    Bot::sendMessage(
		"<code>" . shell_exec("chmod 0755 phpbotmgr && ./phpbotmgr u") . "</code>"
        ,$GLOBALS["options"]);
    Bot::sendMessage(
		$GLOBALS["banner"] . "\n" .
		"PHPTeleBotWrt updated..."
		. "\n\n" . $GLOBALS["randAds"]
        ,$GLOBALS["options"]);
});

//inline command
$bot->on("inline", function ($cmd, $input) {
    if ($cmd == "proxies") {
        $results[] = [
            "type" => "article",
            "id" => "unique_id1",
            "title" => Proxies(),
            "parse_mode" => "html",
            "message_text" => "<code>" . Proxies() . "</code>",
        ];
    } elseif ($cmd == "rules") {
        $results[] = [
            "type" => "article",
            "id" => "unique_id1",
            "title" => Rules(),
            "parse_mode" => "html",
            "message_text" => "<code>" . Rules() . "</code>",
        ];
    } elseif ($cmd == "myxl") {
        $results[] = [
            "type" => "article",
            "id" => "unique_id1",
            "title" => MyXL($input),
            "parse_mode" => "html",
            "message_text" => "<code>" . MyXL($input) . "</code>",
        ];
    }

    $GLOBALS["options"] = [
        "cache_time" => 3600,
    ];

    return Bot::answerInlineQuery($results, $GLOBALS["options"]);
});

$bot->run();
