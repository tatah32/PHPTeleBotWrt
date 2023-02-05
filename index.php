<?php
require_once __DIR__ . "/src/PHPTelebot.php";
require_once __DIR__ . "/src/xc.php";
error_reporting(E_ALL); ini_set('display_errors', 1);

// Read token & username
function readToken($input)
{
    $data = file_get_contents("databot");
    $raw = explode("\n", $data);
    return $input == "token" ? $raw[0] : $raw[1];
}

$bot = new PHPTelebot(readToken("token"), readToken("username"));

function sendAd() {
    $options = ["parse_mode" => "html", "reply" => true];
    $ads = [
        "<span class='tg-spoiler'>Donate me: <a href='https://helmiau.com/pay'>https://helmiau.com/pay</a><br>Keep PHPTeleBotWrt up-to-date updated with <code>phpmgrbot</code> commands</span>",
        "<span class='tg-spoiler'>Donate me: <a href='https://helmiau.com/pay'>https://helmiau.com/pay</a><br>Keep PHPTeleBotWrt up-to-date updated with <code>phpmgrbot</code> commands</span>",
    ];

    // Select a random advertisement message
    $selectedAd = $ads[array_rand($ads)];

    Bot::sendMessage($selectedAd, $options);
}

// Ping Command
$bot->cmd("/ping", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    $start_time = microtime(true);
    Bot::sendMessage("<code>pong</code>", $options);
    $end_time = microtime(true);
    $diff = round(($end_time - $start_time) * 1000);
    Bot::sendMessage("<code>Time taken: " . $diff . "ms</code>", $options);
    return sendAd();
});

// start bot
$bot->cmd("/start", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>
Welcome to PHPTeleBotWrt\nRun [/cmdlist] to see all available comands\n Source: https://github.com/helmiau/PHPTeleBotWrt\n Donate: https://helmiau.com/pay
</code>", $options);
    return sendAd();
});


// list of commands
$bot->cmd("/cmdlist", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "
📁Aria2 Commands
 ↳/aria2add<code>      | Add task</code>
 ↳/aria2stats<code>    | Aria2 status</code>
 ↳/aria2pause<code>    | Pause all</code>
 ↳/aria2resume<code>   | Resume all</code>
 
📁OpenClash Commands
 ↳/oc<code>        | OC Information</code>
 ↳/proxies<code>   | Proxies status </code>
 ↳/rules<code>     | Rule list </code>
 ↳upload yaml<code>| Openclash yaml config upload</code>

📁MyXL Commands
 ↳/myxl<code>      | Bandwidth usage </code>
 ↳/setxl 087<code> | Set default number</code>

📁System Information
 ↳/vnstat<code>    | Bandwidth usage </code>
 ↳/vnstati<code>   | Better Bandwidth usage </code>
 ↳/memory<code>    | Memory status </code>
 ↳/myip<code>      | Get ip details </code>
 ↳/speedtest<code> | Speedtest </code>
 ↳/ping<code>      | Ping bot</code>
 ↳/sysinfo<code>   | System Information</code>",
        $options);
    return sendAd();
});

// yaml config upload
$bot->on('document', function() {
  // download file
    $token = readToken("token");
    $message = Bot::message();
    $file_id = $message['document']['file_id'];
    $raw = json_decode(Bot::getFile($file_id),true);
    $file_path = $raw['result']['file_path'];
    $wget = shell_exec("wget -P /etc/openclash/config https://api.telegram.org/file/bot$token/$file_path");
    Bot::sendMessage("OpenClash config yaml \[$file_path\] uploaded to OpenWrt, please check it manually.");
 });

// OpenWRT Command
// OpenClash Proxies
$bot->cmd("/proxies", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>" . Proxies() . "</code>", $options);
    sendAd();
});

// vnstat
$bot->cmd("/vnstat", function ($input) {
    $options = ["parse_mode" => "html", "reply" => true];
    $input = escapeshellarg($input);
    $output = shell_exec("vnstat $input 2>&1");
    if ($output === null) {
        Bot::sendMessage("<code> Invalid input or vnstat not found</code>", $options);
    } else {
        Bot::sendMessage("<code>" . $output . "</code>", $options);
    }
    sendAd();
});

// vnstati
$bot->cmd("/vnstati", function () {
    $options = ["parse_mode" => "html", "reply" => true];
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
    sendAd();
});


// Check RAM/Memory
$bot->cmd("/memory", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    $meminfo = file("/proc/meminfo");
    $total = intval(trim(explode(":", $meminfo[0])[1])) / 1024;
    $free = intval(trim(explode(":", $meminfo[1])[1])) / 1024;
    $used = $total - $free;
    $percent = round(($used / $total) * 100);
    $bar = str_repeat("■", round($percent / 5));
    $bar .= str_repeat("□", 20 - round($percent / 5));
    $output =
        "<code>Memory usage: \nBar: " .
        $bar .
        "\nUsed: $used MB \nAvailable: $free MB \nTotal: $total MB \nUsage: $percent%</code>";
    Bot::sendMessage($output, $options);
    sendAd();
});

// Systemm info
$bot->cmd("/sysinfo", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/sysinfo.sh -bw") . "</code>",
        $options);
    sendAd();
});

// OpenClash
$bot->cmd("/oc", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/oc.sh") . "</code>",
        $options);
    sendAd();
});

// My IP Address info
$bot->cmd("/myip", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>" . myip() . "</code>", $options);
    sendAd();
});

// OpenClash Rules
$bot->cmd("/rules", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>" . Rules() . "</code>", $options);
    sendAd();
});

// Speedtest
$bot->cmd("/speedtest", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>Speedtest on Progress</code>", $options);
    Bot::sendMessage("<code>" . Speedtest() . "</code>", $options);
    sendAd();
});

//Myxl cmd
$bot->cmd("/setxl", function ($number) {
    $options = ["parse_mode" => "html", "reply" => true];
    if ($number == "") {
        Bot::sendMessage(
            "<code>Masukan nomor yang mau di set sebagai default /setxl 087x</code>",
            $options
        );
    } else {
        shell_exec("echo '$number' > xl");
        Bot::sendMessage(
            "<code>Nomer $number disetting sebagai default\nSilahkan gunakan cmd /myxl tanpa memasukkan nomor</code>",
            $options
        );
    }
});

$bot->cmd("/myxl", function ($number) {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>MyXL on Progress</code>", $options);
    Bot::sendMessage("<code>" . MyXL($number) . "</code>", $options);
    sendAd();
});
//Myxl cmd end

//adb cmd
$bot->cmd("/adb", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage("<code>ADB on Progress</code>", $options);
    Bot::sendMessage("<code>" . ADB() . "</code>", $options);
    sendAd();
});

//Aria2 cmd
$bot->cmd("/aria2add", function ($url) {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/add.sh $url") . "</code>",
        $options
    );
    sendAd();
});

$bot->cmd("/aria2stats", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/stats.sh") . "</code>",
        $options
    );
    sendAd();
});

$bot->cmd("/aria2pause", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/pause.sh") . "</code>",
        $options
    );
    sendAd();
});

$bot->cmd("/aria2resume", function () {
    $options = ["parse_mode" => "html", "reply" => true];
    Bot::sendMessage(
        "<code>" . shell_exec("src/plugins/resume.sh") . "</code>",
        $options
    );
    sendAd();
});

//Aria2 cmd end

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

    $options = [
        "cache_time" => 3600,
    ];

    return Bot::answerInlineQuery($results, $options);
});

$bot->run();
