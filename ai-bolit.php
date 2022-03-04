<?php
///////////////////////////////////////////////////////////////////////////
// Version: 4.4.2
// Created and developed by Greg Zemskov, Revisium Company
// Email: audit@revisium.com, http://revisium.com/ai/

// Commercial usage is not allowed without a license purchase or written permission of the author
// Source code and signatures usage is not allowed

// Certificated in Federal Institute of Industrial Property in 2012
// http://revisium.com/ai/i/mini_aibolit.jpg

////////////////////////////////////////////////////////////////////////////
// Запрещено использование скрипта в коммерческих целях без приобретения лицензии.
// Запрещено использование исходного кода скрипта и сигнатур.
//
// По вопросам приобретения лицензии обращайтесь в компанию "Ревизиум": http://www.revisium.com
// audit@revisium.com
// На скрипт получено авторское свидетельство в Роспатенте
// http://revisium.com/ai/i/mini_aibolit.jpg
///////////////////////////////////////////////////////////////////////////

ini_set('memory_limit', '1G');
ini_set('xdebug.max_nesting_level', 500);

$int_enc = @ini_get('mbstring.internal_encoding');

define('SHORT_PHP_TAG', strtolower(ini_get('short_open_tag')) == 'on' || strtolower(ini_get('short_open_tag')) == 1 ? true : false);

// Put any strong password to open the script from web
// Впишите вместо put_any_strong_password_here сложный пароль

define('PASS', '????????????????');

//////////////////////////////////////////////////////////////////////////
$vars = new Variables();

if (isCli()) {
    if (strpos('--eng', $argv[$argc - 1]) !== false) {
        define('LANG', 'EN');
    }
} else {
    if (PASS == '????????????????') {
        die('Forbidden');
    }

    define('NEED_REPORT', true);
}

if (!defined('LANG')) {
    define('LANG', 'RU');
}

// put 1 for expert mode, 0 for basic check and 2 for paranoid mode
// установите 1 для режима "Обычное сканирование", 0 для быстрой проверки и 2 для параноидальной проверки (диагностика при лечении сайтов)
define('AI_EXPERT_MODE', 1);

define('AI_HOSTER', 0);

define('CLOUD_ASSIST_LIMIT', 5000);

$defaults = array(
    'path'              => dirname(__FILE__),
    'scan_all_files'    => (AI_EXPERT_MODE == 2), // full scan (rather than just a .js, .php, .html, .htaccess)
    'scan_delay'        => 0, // delay in file scanning to reduce system load
    'max_size_to_scan'  => '650K',
    'max_size_to_cloudscan'  => '650K',
    'site_url'          => '', // website url
    'no_rw_dir'         => 0,
    'skip_ext'          => '',
    'skip_cache'        => false,
    'report_mask'       => JSONReport::REPORT_MASK_FULL,
);

define('DEBUG_MODE', 0);
define('DEBUG_PERFORMANCE', 0);

define('AIBOLIT_START_TIME', time());
define('START_TIME', microtime(true));

define('DIR_SEPARATOR', '/');

define('AIBOLIT_MAX_NUMBER', 200);

define('DOUBLECHECK_FILE', 'AI-BOLIT-DOUBLECHECK.php');

if ((isset($_SERVER['OS']) && stripos('Win', $_SERVER['OS']) !== false)) {
    define('DIR_SEPARATOR', '\\');
}

$g_SuspiciousFiles = array(
    'cgi',
    'pl',
    'o',
    'so',
    'py',
    'sh',
    'phtml',
    'php3',
    'php4',
    'php5',
    'php6',
    'php7',
    'pht',
    'shtml'
);
$g_SensitiveFiles  = array_merge(array(
    'php',
    'js',
    'json',
    'htaccess',
    'html',
    'htm',
    'tpl',
    'inc',
    'css',
    'txt',
    'sql',
    'ico',
    '',
    'susp',
    'suspected',
    'zip',
    'tar'
), $g_SuspiciousFiles);
$g_CriticalEntries = '^\s*<\?php|^\s*<\?=|^#!/usr|^#!/bin|\beval|assert|base64_decode|\bsystem|create_function|\bexec|\bpopen|\bfwrite|\bfputs|file_get_|call_user_func|file_put_|\$_REQUEST|ob_start|\$_GET|\$_POST|\$_SERVER|\$_FILES|\bmove|\bcopy|\barray_|reg_replace|\bmysql_|\bchr|fsockopen|\$GLOBALS|sqliteCreateFunction';
$g_VirusFiles      = array(
    'js',
    'json',
    'html',
    'htm',
    'suspicious'
);
$g_VirusEntries    = '<script|<iframe|<object|<embed|fromCharCode|setTimeout|setInterval|location\.|document\.|window\.|navigator\.|\$(this)\.';
$g_PhishFiles      = array(
    'js',
    'html',
    'htm',
    'suspected',
    'php',
    'phtml',
    'pht',
    'php7'
);
$g_PhishEntries    = '<\s*title|<\s*html|<\s*form|<\s*body|bank|account';
$g_ShortListExt    = array(
    'php',
    'php3',
    'php4',
    'php5',
    'php7',
    'pht',
    'html',
    'htm',
    'phtml',
    'shtml',
    'khtml',
    '',
    'ico',
    'txt'
);

if (LANG == 'RU') {
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // RUSSIAN INTERFACE
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $msg1  = "\"Отображать по _MENU_ записей\"";
    $msg2  = "\"Ничего не найдено\"";
    $msg3  = "\"Отображается c _START_ по _END_ из _TOTAL_ файлов\"";
    $msg4  = "\"Нет файлов\"";
    $msg5  = "\"(всего записей _MAX_)\"";
    $msg6  = "\"Поиск:\"";
    $msg7  = "\"Первая\"";
    $msg8  = "\"Предыдущая\"";
    $msg9  = "\"Следующая\"";
    $msg10 = "\"Последняя\"";
    $msg11 = "\": активировать для сортировки столбца по возрастанию\"";
    $msg12 = "\": активировать для сортировки столбцов по убыванию\"";

    define('AI_STR_001', 'Отчет сканера <a href="https://revisium.com/ai/">AI-Bolit</a> v@@VERSION@@:');
    define('AI_STR_002', 'Обращаем внимание на то, что большинство CMS <b>без дополнительной защиты</b> рано или поздно <b>взламывают</b>.<p> Компания <a href="https://revisium.com/">"Ревизиум"</a> предлагает услугу превентивной защиты сайта от взлома с использованием уникальной <b>процедуры "цементирования сайта"</b>. Подробно на <a href="https://revisium.com/ru/client_protect/">странице услуги</a>. <p>Лучшее лечение &mdash; это профилактика.');
    define('AI_STR_003', 'Не оставляйте файл отчета на сервере, и не давайте на него прямых ссылок с других сайтов. Информация из отчета может быть использована злоумышленниками для взлома сайта, так как содержит информацию о настройках сервера, файлах и каталогах.');
    define('AI_STR_004', 'Путь');
    define('AI_STR_005', 'Изменение свойств');
    define('AI_STR_006', 'Изменение содержимого');
    define('AI_STR_007', 'Размер');
    define('AI_STR_008', 'Конфигурация PHP');
    define('AI_STR_009', "Вы установили слабый пароль на скрипт AI-BOLIT. Укажите пароль не менее 8 символов, содержащий латинские буквы в верхнем и нижнем регистре, а также цифры. Например, такой <b>%s</b>");
    define('AI_STR_010', "Сканер AI-Bolit запускается с паролем. Если это первый запуск сканера, вам нужно придумать сложный пароль и вписать его в файле ai-bolit.php в строке №34. <p>Например, <b>define('PASS', '%s');</b><p>
После этого откройте сканер в браузере, указав пароль в параметре \"p\". <p>Например, так <b>http://mysite.ru/ai-bolit.php?p=%s</b>. ");
    define('AI_STR_011', 'Текущая директория не доступна для чтения скрипту. Пожалуйста, укажите права на доступ <b>rwxr-xr-x</b> или с помощью командной строки <b>chmod +r имя_директории</b>');
    define('AI_STR_012', "Затрачено времени: <b>%s</b>. Сканирование начато %s, сканирование завершено %s");
    define('AI_STR_013', 'Всего проверено %s директорий и %s файлов.');
    define('AI_STR_014', '<div class="rep" style="color: #0000A0">Внимание, скрипт выполнил быструю проверку сайта. Проверяются только наиболее критические файлы, но часть вредоносных скриптов может быть не обнаружена. Пожалуйста, запустите скрипт из командной строки для выполнения полного тестирования. Подробнее смотрите в <a href="https://revisium.com/ai/faq.php">FAQ вопрос №10</a>.</div>');
    define('AI_STR_015', '<div class="title">Критические замечания</div>');
    define('AI_STR_016', 'Эти файлы могут быть вредоносными или хакерскими скриптами');
    define('AI_STR_017', 'Вирусы и вредоносные скрипты не обнаружены.');
    define('AI_STR_018', 'Эти файлы могут быть javascript вирусами');
    define('AI_STR_019', 'Обнаружены сигнатуры исполняемых файлов unix и нехарактерных скриптов. Они могут быть вредоносными файлами');
    define('AI_STR_020', 'Двойное расширение, зашифрованный контент или подозрение на вредоносный скрипт. Требуется дополнительный анализ');
    define('AI_STR_021', 'Подозрение на вредоносный скрипт');
    define('AI_STR_022', 'Символические ссылки (symlinks)');
    define('AI_STR_023', 'Скрытые файлы');
    define('AI_STR_024', 'Возможно, каталог с дорвеем');
    define('AI_STR_025', 'Не найдено директорий c дорвеями');
    define('AI_STR_026', 'Предупреждения');
    define('AI_STR_027', 'Подозрение на мобильный редирект, подмену расширений или автовнедрение кода');
    define('AI_STR_028', 'В не .php файле содержится стартовая сигнатура PHP кода. Возможно, там вредоносный код');
    define('AI_STR_029', 'Дорвеи, реклама, спам-ссылки, редиректы');
    define('AI_STR_030', 'Непроверенные файлы - ошибка чтения');
    define('AI_STR_031', 'Невидимые ссылки. Подозрение на ссылочный спам');
    define('AI_STR_032', 'Невидимые ссылки');
    define('AI_STR_033', 'Отображены только первые ');
    define('AI_STR_034', 'Подозрение на дорвей');
    define('AI_STR_035', 'Скрипт использует код, который часто встречается во вредоносных скриптах');
    define('AI_STR_036', 'Директории из файла .adirignore были пропущены при сканировании');
    define('AI_STR_037', 'Версии найденных CMS');
    define('AI_STR_038', 'Большие файлы (больше чем %s). Пропущено');
    define('AI_STR_039', 'Не найдено файлов больше чем %s');
    define('AI_STR_040', 'Временные файлы или файлы(каталоги) - кандидаты на удаление по ряду причин');
    define('AI_STR_041', 'Потенциально небезопасно! Директории, доступные скрипту на запись');
    define('AI_STR_042', 'Не найдено директорий, доступных на запись скриптом');
    define('AI_STR_043', 'Использовано памяти при сканировании: ');
    define('AI_STR_044', 'Просканированы только файлы, перечисленные в ' . DOUBLECHECK_FILE . '. Для полного сканирования удалите файл ' . DOUBLECHECK_FILE . ' и запустите сканер повторно.');
    define('AI_STR_045', '<div class="rep">Внимание! Выполнена экспресс-проверка сайта. Просканированы только файлы с расширением .php, .js, .html, .htaccess. В этом режиме могут быть пропущены вирусы и хакерские скрипты в файлах с другими расширениями. Чтобы выполнить более тщательное сканирование, поменяйте значение настройки на <b>\'scan_all_files\' => 1</b> в строке 50 или откройте сканер в браузере с параметром full: <b><a href="ai-bolit.php?p=' . PASS . '&full">ai-bolit.php?p=' . PASS . '&full</a></b>. <p>Не забудьте перед повторным запуском удалить файл ' . DOUBLECHECK_FILE . '</div>');
    define('AI_STR_050', 'Замечания и предложения по работе скрипта и не обнаруженные вредоносные скрипты присылайте на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<p>Также будем чрезвычайно благодарны за любые упоминания скрипта AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. Ссылочку можно поставить на <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>. <p>Если будут вопросы - пишите <a href="mailto:ai@revisium.com">ai@revisium.com</a>. ');
    define('AI_STR_051', 'Отчет по ');
    define('AI_STR_052', 'Эвристический анализ обнаружил подозрительные файлы. Проверьте их на наличие вредоносного кода.');
    define('AI_STR_053', 'Много косвенных вызовов функции');
    define('AI_STR_054', 'Подозрение на обфусцированные переменные');
    define('AI_STR_055', 'Подозрительное использование массива глобальных переменных');
    define('AI_STR_056', 'Дробление строки на символы');
    define('AI_STR_057', 'Сканирование выполнено в экспресс-режиме. Многие вредоносные скрипты могут быть не обнаружены.<br> Рекомендуем проверить сайт в режиме "Эксперт" или "Параноидальный". Подробно описано в <a href="https://revisium.com/ai/faq.php">FAQ</a> и инструкции к скрипту.');
    define('AI_STR_058', 'Обнаружены фишинговые страницы');
    define('AI_STR_059', 'Мобильных редиректов');
    define('AI_STR_060', 'Вредоносных скриптов');
    define('AI_STR_061', 'JS Вирусов');
    define('AI_STR_062', 'Фишинговых страниц');
    define('AI_STR_063', 'Исполняемых файлов');
    define('AI_STR_064', 'IFRAME вставок');
    define('AI_STR_065', 'Пропущенных больших файлов');
    define('AI_STR_066', 'Ошибок чтения файлов');
    define('AI_STR_067', 'Зашифрованных файлов');
    define('AI_STR_068', 'Подозрительных');
    define('AI_STR_069', 'Символических ссылок');
    define('AI_STR_070', 'Скрытых файлов');
    define('AI_STR_072', 'Рекламных ссылок и кодов');
    define('AI_STR_073', 'Пустых ссылок');
    define('AI_STR_074', 'Сводный отчет');

    define('AI_STR_075', 'Сканер бесплатный только для личного некоммерческого использования. Информация по <a href="https://revisium.com/ai/faq.php#faq11" target=_blank>коммерческой лицензии</a> (пункт №11). <a href="https://revisium.com/images/mini_aibolit.jpg">Авторское свидетельство</a> о гос. регистрации в РосПатенте №2012619254 от 12 октября 2012 г.');

    $tmp_str = <<<HTML_FOOTER
   <div class="disclaimer"><span class="vir">[!]</span> Отказ от гарантий: невозможно гарантировать обнаружение всех вредоносных скриптов. Поэтому разработчик сканера не несет ответственности за возможные последствия работы сканера AI-Bolit или неоправданные ожидания пользователей относительно функциональности и возможностей.
   </div>
   <div class="thanx">
      Замечания и предложения по работе скрипта, а также не обнаруженные вредоносные скрипты вы можете присылать на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<br/>
      Также будем чрезвычайно благодарны за любые упоминания сканера AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. <br/>Ссылку можно поставить на страницу <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>.<br/> 
     <p>Получить консультацию или задать вопросы можно по email <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
    </div>
HTML_FOOTER;

    define('AI_STR_076', $tmp_str);
    define('AI_STR_077', "Подозрительные параметры времени изменения файла");
    define('AI_STR_078', "Подозрительные атрибуты файла");
    define('AI_STR_079', "Подозрительное местоположение файла");
    define('AI_STR_080', "Обращаем внимание, что обнаруженные файлы не всегда являются вирусами и хакерскими скриптами. Сканер минимизирует число ложных обнаружений, но это не всегда возможно, так как найденный фрагмент может встречаться как во вредоносных скриптах, так и в обычных.<p>Для диагностического сканирования без ложных срабатываний мы разработали специальную версию <u><a href=\"https://revisium.com/ru/blog/ai-bolit-4-ISP.html\" target=_blank style=\"background: none; color: #303030\">сканера для хостинг-компаний</a></u>.");
    define('AI_STR_081', "Уязвимости в скриптах");
    define('AI_STR_082', "Добавленные файлы");
    define('AI_STR_083', "Измененные файлы");
    define('AI_STR_084', "Удаленные файлы");
    define('AI_STR_085', "Добавленные каталоги");
    define('AI_STR_086', "Удаленные каталоги");
    define('AI_STR_087', "Изменения в файловой структуре");

    $l_Offer = <<<OFFER
    <div>
     <div class="crit" style="font-size: 17px; margin-bottom: 20px"><b>Внимание! Наш сканер обнаружил подозрительный или вредоносный код</b>.</div> 
     <p>Возможно, ваш сайт был взломан. Рекомендуем срочно <a href="https://revisium.com/ru/order/#fform" target=_blank>проконсультироваться со специалистами</a> по данному отчету.</p>
     <p><hr size=1></p>
     <p>Рекомендуем также проверить сайт бесплатным <b><a href="https://rescan.pro/?utm=aibolit" target=_blank>онлайн-сканером ReScan.Pro</a></b>.</p>
     <p><hr size=1></p>
         <div class="caution">@@CAUTION@@</div>
    </div>
OFFER;

    $l_Offer2 = <<<OFFER2
       <b>Наши продукты:</b><br/>
              <ul>
               <li style="margin-top: 10px"><font color=red><sup>[new]</sup></font><b><a href="https://revisium.com/ru/products/antivirus_for_ispmanager/" target=_blank>Антивирус для ISPmanager Lite</a></b> &mdash;  сканирование и лечение сайтов прямо в панели хостинга</li>
               <li style="margin-top: 10px"><b><a href="https://revisium.com/ru/blog/revisium-antivirus-for-plesk.html" target=_blank>Антивирус для Plesk</a> Onyx 17.x</b> &mdash;  сканирование и лечение сайтов прямо в панели хостинга</li>
               <li style="margin-top: 10px"><b><a href="https://cloudscan.pro/ru/" target=_blank>Облачный антивирус CloudScan.Pro</a> для веб-специалистов</b> &mdash; лечение сайтов в один клик</li>
               <li style="margin-top: 10px"><b><a href="https://revisium.com/ru/antivirus-server/" target=_blank>Антивирус для сервера</a></b> &mdash; для хостинг-компаний, веб-студий и агентств.</li>
              </ul>  
    </div>
OFFER2;

} else {
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ENGLISH INTERFACE
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $msg1  = "\"Display _MENU_ records\"";
    $msg2  = "\"Not found\"";
    $msg3  = "\"Display from _START_ to _END_ of _TOTAL_ files\"";
    $msg4  = "\"No files\"";
    $msg5  = "\"(total _MAX_)\"";
    $msg6  = "\"Filter/Search:\"";
    $msg7  = "\"First\"";
    $msg8  = "\"Previous\"";
    $msg9  = "\"Next\"";
    $msg10 = "\"Last\"";
    $msg11 = "\": activate to sort row ascending order\"";
    $msg12 = "\": activate to sort row descending order\"";

    define('AI_STR_001', 'AI-Bolit v@@VERSION@@ Scan Report:');
    define('AI_STR_002', '');
    define('AI_STR_003', 'Caution! Do not leave either ai-bolit.php or report file on server and do not provide direct links to the report file. Report file contains sensitive information about your website which could be used by hackers. So keep it in safe place and don\'t leave on website!');
    define('AI_STR_004', 'Path');
    define('AI_STR_005', 'iNode Changed');
    define('AI_STR_006', 'Modified');
    define('AI_STR_007', 'Size');
    define('AI_STR_008', 'PHP Info');
    define('AI_STR_009', "Your password for AI-BOLIT is too weak. Password must be more than 8 character length, contain both latin letters in upper and lower case, and digits. E.g. <b>%s</b>");
    define('AI_STR_010', "Open AI-BOLIT with password specified in the beggining of file in PASS variable. <br/>E.g. http://you_website.com/ai-bolit.php?p=<b>%s</b>");
    define('AI_STR_011', 'Current folder is not readable. Please change permission for <b>rwxr-xr-x</b> or using command line <b>chmod +r folder_name</b>');
    define('AI_STR_012', "<div class=\"rep\">%s malicious signatures known, %s virus signatures and other malicious code. Elapsed: <b>%s</b
>.<br/>Started: %s. Stopped: %s</div> ");
    define('AI_STR_013', 'Scanned %s folders and %s files.');
    define('AI_STR_014', '<div class="rep" style="color: #0000A0">Attention! Script has performed quick scan. It scans only .html/.js/.php files  in quick scan mode so some of malicious scripts might not be detected. <br>Please launch script from a command line thru SSH to perform full scan.');
    define('AI_STR_015', '<div class="title">Critical</div>');
    define('AI_STR_016', 'Shell script signatures detected. Might be a malicious or hacker\'s scripts');
    define('AI_STR_017', 'Shell scripts signatures not detected.');
    define('AI_STR_018', 'Javascript virus signatures detected:');
    define('AI_STR_019', 'Unix executables signatures and odd scripts detected. They might be a malicious binaries or rootkits:');
    define('AI_STR_020', 'Suspicious encoded strings, extra .php extention or external includes detected in PHP files. Might be a malicious or hacker\'s script:');
    define('AI_STR_021', 'Might be a malicious or hacker\'s script:');
    define('AI_STR_022', 'Symlinks:');
    define('AI_STR_023', 'Hidden files:');
    define('AI_STR_024', 'Files might be a part of doorway:');
    define('AI_STR_025', 'Doorway folders not detected');
    define('AI_STR_026', 'Warnings');
    define('AI_STR_027', 'Malicious code in .htaccess (redirect to external server, extention handler replacement or malicious code auto-append):');
    define('AI_STR_028', 'Non-PHP file has PHP signature. Check for malicious code:');
    define('AI_STR_029', 'This script has black-SEO links or linkfarm. Check if it was installed by yourself:');
    define('AI_STR_030', 'Reading error. Skipped.');
    define('AI_STR_031', 'These files have invisible links, might be black-seo stuff:');
    define('AI_STR_032', 'List of invisible links:');
    define('AI_STR_033', 'Displayed first ');
    define('AI_STR_034', 'Folders contained too many .php or .html files. Might be a doorway:');
    define('AI_STR_035', 'Suspicious code detected. It\'s usually used in malicious scrips:');
    define('AI_STR_036', 'The following list of files specified in .adirignore has been skipped:');
    define('AI_STR_037', 'CMS found:');
    define('AI_STR_038', 'Large files (greater than %s! Skipped:');
    define('AI_STR_039', 'Files greater than %s not found');
    define('AI_STR_040', 'Files recommended to be remove due to security reason:');
    define('AI_STR_041', 'Potentially unsafe! Folders which are writable for scripts:');
    define('AI_STR_042', 'Writable folders not found');
    define('AI_STR_043', 'Memory used: ');
    define('AI_STR_044', 'Quick scan through the files from ' . DOUBLECHECK_FILE . '. For full scan remove ' . DOUBLECHECK_FILE . ' and launch scanner once again.');
    define('AI_STR_045', '<div class="notice"><span class="vir">[!]</span> Ai-BOLIT is working in quick scan mode, only .php, .html, .htaccess files will be checked. Change the following setting \'scan_all_files\' => 1 to perform full scanning.</b>. </div>');
    define('AI_STR_050', "I'm sincerely appreciate reports for any bugs you may found in the script. Please email me: <a href=\"mailto:audit@revisium.com\">audit@revisium.com</a>.<p> Also I appriciate any reference to the script in your blog or forum posts. Thank you for the link to download page: <a href=\"https://revisium.com/aibo/\">https://revisium.com/aibo/</a>");
    define('AI_STR_051', 'Report for ');
    define('AI_STR_052', 'Heuristic Analyzer has detected suspicious files. Check if they are malware.');
    define('AI_STR_053', 'Function called by reference');
    define('AI_STR_054', 'Suspected for obfuscated variables');
    define('AI_STR_055', 'Suspected for $GLOBAL array usage');
    define('AI_STR_056', 'Abnormal split of string');
    define('AI_STR_057', 'Scanning has been done in simple mode. It is strongly recommended to perform scanning in "Expert" mode. See readme.txt for details.');
    define('AI_STR_058', 'Phishing pages detected:');

    define('AI_STR_059', 'Mobile redirects');
    define('AI_STR_060', 'Malware');
    define('AI_STR_061', 'JS viruses');
    define('AI_STR_062', 'Phishing pages');
    define('AI_STR_063', 'Unix executables');
    define('AI_STR_064', 'IFRAME injections');
    define('AI_STR_065', 'Skipped big files');
    define('AI_STR_066', 'Reading errors');
    define('AI_STR_067', 'Encrypted files');
    define('AI_STR_068', 'Suspicious');
    define('AI_STR_069', 'Symbolic links');
    define('AI_STR_070', 'Hidden files');
    define('AI_STR_072', 'Adware and spam links');
    define('AI_STR_073', 'Empty links');
    define('AI_STR_074', 'Summary');
    define('AI_STR_075', 'For non-commercial use only. In order to purchase the commercial license of the scanner contact us at ai@revisium.com');

    $tmp_str = <<<HTML_FOOTER
           <div class="disclaimer"><span class="vir">[!]</span> Disclaimer: We're not liable to you for any damages, including general, special, incidental or consequential damages arising out of the use or inability to use the script (including but not limited to loss of data or report being rendered inaccurate or failure of the script). There's no warranty for the program. Use at your own risk. 
           </div>
           <div class="thanx">
              We're greatly appreciate for any references in the social medias, forums or blogs to our scanner AI-BOLIT <a href="https://revisium.com/aibo/">https://revisium.com/aibo/</a>.<br/> 
             <p>Contact us via email if you have any questions regarding the scanner or need report analysis: <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
            </div>
HTML_FOOTER;
    define('AI_STR_076', $tmp_str);
    define('AI_STR_077', "Suspicious file mtime and ctime");
    define('AI_STR_078', "Suspicious file permissions");
    define('AI_STR_079', "Suspicious file location");
    define('AI_STR_081', "Vulnerable Scripts");
    define('AI_STR_082', "Added files");
    define('AI_STR_083', "Modified files");
    define('AI_STR_084', "Deleted files");
    define('AI_STR_085', "Added directories");
    define('AI_STR_086', "Deleted directories");
    define('AI_STR_087', "Integrity Check Report");

    $l_Offer = <<<HTML_OFFER_EN
<div>
 <div class="crit" style="font-size: 17px;"><b>Attention! The scanner has detected suspicious or malicious files.</b></div> 
 <br/>Most likely the website has been compromised. Please, <a href="https://revisium.com/en/contacts/" target=_blank>contact web security experts</a> from Revisium to check the report or clean the malware.
 <p><hr size=1></p>
 Also check your website for viruses with our free <b><a href="http://rescan.pro/?en&utm=aibo" target=_blank>online scanner ReScan.Pro</a></b>.
</div>
<br/>
<div>
   Revisium contacts: <a href="mailto:ai@revisium.com">ai@revisium.com</a>, <a href="https://revisium.com/en/contacts/">https://revisium.com/en/home/</a>
</div>
<div class="caution">@@CAUTION@@</div>
HTML_OFFER_EN;

    $l_Offer2 = '<b>Special Offers:</b><br/>
              <ul>
               <li style="margin-top: 10px"><font color=red><sup>[new]</sup></font><b><a href="http://ext.plesk.com/packages/b71916cf-614e-4b11-9644-a5fe82060aaf-revisium-antivirus">Antivirus for Plesk Onyx</a></b> hosting panel with one-click malware cleanup and scheduled website scanning.</li>
               <li style="margin-top: 10px"><font color=red></font><b><a href="https://www.ispsystem.com/addons-modules/revisium">Antivirus for ISPmanager Lite</a></b> hosting panel with one-click malware cleanup and scheduled website scanning.</li>
               <li style="margin-top: 10px">Professional malware cleanup and web-protection service with 6 month guarantee for only $99 (one-time payment): <a href="https://revisium.com/en/home/#order_form">https://revisium.com/en/home/</a>.</li>
              </ul>  
    </div>';

    define('AI_STR_080', "Notice! Some of detected files may not contain malicious code. Scanner tries to minimize a number of false positives, but sometimes it's impossible, because same piece of code may be used either in malware or in normal scripts.");
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$l_Template = <<<MAIN_PAGE
<html>
<head>
<!-- revisium.com/ai/ -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<title>@@HEAD_TITLE@@</title>
<style type="text/css" title="currentStyle">
    @import "https://cdn.revisium.com/ai/media/css/demo_page2.css";
    @import "https://cdn.revisium.com/ai/media/css/jquery.dataTables2.css";
</style>

<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/jquery.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/datatables.min.js"></script>

<style type="text/css">
 body 
 {
   font-family: Tahoma, sans-serif;
   color: #5a5a5a;
   background: #FFFFFF;
   font-size: 14px;
   margin: 20px;
   padding: 0;
 }

.header
 {
   font-size: 34px;
   margin: 0 0 10px 0;
 }

 .hidd
 {
    display: none;
 }
 
 .ok
 {
    color: green;
 }
 
 .line_no
 {
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #DAF2C1;
   padding: 2px 5px 2px 5px;
   margin: 0 5px 0 5px;
 }
 
 .credits_header 
 {
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #F2F2F2;
   padding: 10px;
   font-size: 11px;
    margin: 0 0 10px 0;
 }
 
 .marker
 {
    color: #FF0090;
    font-weight: 100;
    background: #FF0090;
    padding: 2px 0 2px 0;
    width: 2px;
 }
 
 .title
 {
   font-size: 24px;
   margin: 20px 0 10px 0;
   color: #9CA9D1;
}

.summary 
{
  float: left;
  width: 500px;
}

.summary TD
{
  font-size: 12px;
  border-bottom: 1px solid #F0F0F0;
  font-weight: 700;
  padding: 10px 0 10px 0;
}
 
.crit, .vir
{
  color: #D84B55;
}

.intitem
{
  color:#4a6975;
}

.spacer
{
   margin: 0 0 50px 0;
   clear:both;
}

.warn
{
  color: #F6B700;
}

.clear
{
   clear: both;
}

.offer
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #F2F2F2;
   color: #747474;
   font-family: Helvetica, Arial, sans-serif;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}

.offer2
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #f6f5e0;
   color: #747474;
   font-family: Helvetica, Arial, sans-serif;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}


HR {
  margin-top: 15px;
  margin-bottom: 15px;
  opacity: .2;
}
 
.flist
{
   font-family: Henvetica, Arial, sans-serif;
}

.flist TD
{
   font-size: 11px;
   padding: 5px;
}

.flist TH
{
   font-size: 12px;
   height: 30px;
   padding: 5px;
   background: #CEE9EF;
}


.it
{
   font-size: 14px;
   font-weight: 100;
   margin-top: 10px;
}

.crit .it A {
   color: #E50931; 
   line-height: 25px;
   text-decoration: none;
}

.warn .it A {
   color: #F2C900; 
   line-height: 25px;
   text-decoration: none;
}



.details
{
   font-family: Calibri, sans-serif;
   font-size: 12px;
   margin: 10px 10px 10px 0;
}

.crit .details
{
   color: #A08080;
}

.warn .details
{
   color: #808080;
}

.details A
{
  color: #FFF;
  font-weight: 700;
  text-decoration: none;
  padding: 2px;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;
}

.details A:hover
{
   background: #A0909B;
}

.ctd
{
   margin: 10px 0 10px 0;
   align:center;
}

.ctd A 
{
   color: #0D9922;
}

.disclaimer
{
   color: darkgreen;
   margin: 10px 10px 10px 0;
}

.note_vir
{
   margin: 10px 0 10px 0;
   //padding: 10px;
   color: #FF4F4F;
   font-size: 15px;
   font-weight: 700;
   clear:both;
  
}

.note_warn
{
   margin: 10px 0 10px 0;
   color: #F6B700;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.note_int
{
   margin: 10px 0 10px 0;
   color: #60b5d6;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.updateinfo
{
  color: #FFF;
  text-decoration: none;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0;   
  padding: 10px;
}


.caution
{
  color: #EF7B75;
  text-decoration: none;
  margin: 20px 0 0 0;   
  font-size: 12px;
}

.footer
{
  color: #303030;
  text-decoration: none;
  background: #F4F4F4;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 80px 0 10px 0px;   
  padding: 10px;
}

.rep
{
  color: #303030;
  text-decoration: none;
  background: #94DDDB;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0;   
  padding: 10px;
  font-size: 12px;
}

</style>

</head>
<body>

<div class="header">@@MAIN_TITLE@@ @@PATH_URL@@ (@@MODE@@)</div>
<div class="credits_header">@@CREDITS@@</div>
<div class="details_header">
   @@STAT@@<br/>
   @@SCANNED@@ @@MEMORY@@.
 </div>

 @@WARN_QUICK@@
 
 <div class="summary">
@@SUMMARY@@
 </div>
 
 <div class="offer">
@@OFFER@@
 </div>

 <div class="offer2">
@@OFFER2@@
 </div> 
 
 <div class="clear"></div>
 
 @@MAIN_CONTENT@@
 
    <div class="footer">
    @@FOOTER@@
    </div>
    
<script language="javascript">

function hsig(id) {
  var divs = document.getElementsByTagName("tr");
  for(var i = 0; i < divs.length; i++){
     
     if (divs[i].getAttribute('o') == id) {
        divs[i].innerHTML = '';
     }
  }

  return false;
}


$(document).ready(function(){
    $('#table_crit').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
        "paging": true,
       "iDisplayLength": 500,
        "oLanguage": {
            "sLengthMenu": $msg1,
            "sZeroRecords": $msg2,
            "sInfo": $msg3,
            "sInfoEmpty": $msg4,
            "sInfoFiltered": $msg5,
            "sSearch":       $msg6,
            "sUrl":          "",
            "oPaginate": {
                "sFirst": $msg7,
                "sPrevious": $msg8,
                "sNext": $msg9,
                "sLast": $msg10
            },
            "oAria": {
                "sSortAscending": $msg11,
                "sSortDescending": $msg12   
            }
        }

     } );

});

$(document).ready(function(){
    $('#table_vir').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
       "iDisplayLength": 500,
        "oLanguage": {
            "sLengthMenu": $msg1,
            "sZeroRecords": $msg2,
            "sInfo": $msg3,
            "sInfoEmpty": $msg4,
            "sInfoFiltered": $msg5,
            "sSearch":       $msg6,
            "sUrl":          "",
            "oPaginate": {
                "sFirst": $msg7,
                "sPrevious": $msg8,
                "sNext": $msg9,
                "sLast": $msg10
            },
            "oAria": {
                "sSortAscending":  $msg11,
                "sSortDescending": $msg12   
            }
        },

     } );

});

if ($('#table_warn0')) {
    $('#table_warn0').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
                     "iDisplayLength": 500,
                    "oLanguage": {
                        "sLengthMenu": $msg1,
                        "sZeroRecords": $msg2,
                        "sInfo": $msg3,
                        "sInfoEmpty": $msg4,
                        "sInfoFiltered": $msg5,
                        "sSearch":       $msg6,
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst": $msg7,
                            "sPrevious": $msg8,
                            "sNext": $msg9,
                            "sLast": $msg10
                        },
                        "oAria": {
                            "sSortAscending":  $msg11,
                            "sSortDescending": $msg12   
                        }
        }

     } );
}

if ($('#table_warn1')) {
    $('#table_warn1').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
        "paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
                     "iDisplayLength": 500,
                    "oLanguage": {
                        "sLengthMenu": $msg1,
                        "sZeroRecords": $msg2,
                        "sInfo": $msg3,
                        "sInfoEmpty": $msg4,
                        "sInfoFiltered": $msg5,
                        "sSearch":       $msg6,
                        "sUrl":          "",
                        "oPaginate": {
                            "sFirst": $msg7,
                            "sPrevious": $msg8,
                            "sNext": $msg9,
                            "sLast": $msg10
                        },
                        "oAria": {
                            "sSortAscending":  $msg11,
                            "sSortDescending": $msg12   
                        }
        }

     } );
}


</script>
<!-- @@SERVICE_INFO@@  -->
 </body>
</html>
MAIN_PAGE;

$g_AiBolitAbsolutePath = dirname(__FILE__);

if (file_exists($g_AiBolitAbsolutePath . '/ai-design.html')) {
    $l_Template = file_get_contents($g_AiBolitAbsolutePath . '/ai-design.html');
}

$l_Template = str_replace('@@MAIN_TITLE@@', AI_STR_001, $l_Template);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$g_Mnemo = array();
$db_location = 'internal';

//BEGIN_SIG 18/11/2019 04:26:21
$g_DBShe = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$gX_DBShe = unserialize(gzinflate(/*1574083581*/base64_decode("bVX9b+I4EP1XvDn2oFIpCUmgpNvqKLDtXT9V2L1dVVVkEhOsOB9rO7Swuvvbzx67tFodv5A8j59n3htPcDTwop80ck9ENIyclKxwQrhzQiNPIf0gcr5XDUpwiepGIoyKNERCclpmOqavYo4jp17XYk0Y05CvoEE/cj6ldIMShoU4dZasSnK0lNuaeM7Z+5VUVvUvyFIWzplmCgx5MhrtyUMD8XC4hwYm8dvFFKfpViNDu4+u90HHJujzC5BpZKQQT6X5N1miuQ5DS9jsaSE8TyuxoWw3f2XwQI4wci5xkpNURaMxW+ElkdVXymWDTZQV5NaX+7M93zLOZcWL4RtjYPHrKmnEOzy0qb1WeaTkhQVd6ihycCnpG/vQshRbwMgLSQA/NpmY2u5ywKBoZakO6ji8QF2OuiswEsrWmRPUpJhVAu1wxpsdlbCsqw/VKa2CCIEzgk4R4SSLOamZapiO8zGcfOz3nUPkmL/XwIMT2K918VTuteociZx5jQuSts8+LTlY3dcaBW7kCCKTqsop6SBd0Q8WP5NljNOClnEjCC/VPgdZUi2gr2wlTBC66qyaMpG0KmPyQoUUHQcEiUGRgwPYoaUNVRp0hTpUxAlmDC+ZSt8EIVym6IM6CnOOtxY9bKVU6CjNb3m0E35geDqtmvBCoN+R+zJxXfcAnZ7uH39COHikqkspR727C9T7BrC2yB8oljJhTUo6rXg+e/g6e3hsXy4W9/EX9RaPL2a3i/aTOXZk2njJ3WfhwyX1tW+Bqqi9lrJOj5KqXLUP25t1pRR4fUtWme4h/aQAal5gt2fr6NW8SnpiK3q50piw3hYXuFdLrryNRVLVBMK1i31fCb7BrLOijMQZkbEilaTUgusY3zaSkveZU2nk7W0w78FyYFuwdXF9dz6+nj+2Y/WDJTAnhJ17Y9ragvavxhhUNdk7ZwRwaGOUormrpIB74Gvt+wrKCkxZVxSy7tLyiB1lVZUxoiQqIMxOCNVt3HWh5/2RvVlTPQng2oPkgR2V07dRGXg29BHdr5WUL1v0BLgdB49JlRK+M5gWyNcD8kO3+5suBCVFeuq0wPPxZDK7Xzio2zUjMLCz4OmRV02ZdtyDJ5A5CF+HCi3GUr4lpwXwlIhTzPMJJ8+fOSVlCuIEQ5N43mfM91OAbNlX+ZXn+0OARpbhcjy5mk3R+Xc0X9w93MD4da17N+qKqW+CEQXIQ89uu+FH87Xq6DUuv3mjAaz1zeCaEppijv+F7g99eyn+omWtRlpV7AAOTKxXDv1Eem4JYGjPvVLfnXnOm1pd7MqcO/h/mqGhmZAyo7tLbGiODcj9jV9mQWkIRgY8v1b1frn9E5IbuHbUckmTnJE/nJN//gM=")));
$g_FlexDBShe = unserialize(gzinflate(/*1574083581*/base64_decode("7P0LQxrLsj+AfhXDyl6AIDDDS2IQjTHGPDRRYx6O4Y+ASoLAAtQY4bvfrqp+Tw9gkrXPOffevXYQZnp6+lldz181nuQrBf/JfedJbm30JF96Ensa1AaXg2CUCh7/c1yNjcb9QT2TqgWnQXItmAa19WC0HFvrPPGgfNEoP3pRjQW3KSyetIr7rHjRM4rvvNl/tvnmEEqfBK0UewO8Y9r+0RkHCXqcFautw+N59nihrB5fDjYGw/ZFfdgedBvNdpDIZNkzmWA5SGbbrMb0UibDqoBHC+zR8qp4dOmi2z9rdJeCxyeNlZ+5lcppak3/UW0Mh407Vp/sw9p1b9SGFqlCvOYiq7nEGtU5hwaxAvUXu2+2D4OTzAuvm4caHtff7R8esQt7jat2hsZwtNxuXvbZn8z+68za0jbr7toUqiux6jx/Va/vYPv9h22sYNAYNq48VsXff4ev+1g1e+g+eHzO3sv7EFVwTRSpn3e64/aQ/YSmsOdVY8rQGM9cD9D/U1Z4NB6O+9eDAT7ZGbHRYX/bN40uNZt9PL7HTzVgo+UpXDlhH3F5NQ6XTuEDhwVLyAWzyhrgF7TRHS3jq9Q76ic77w4Ot06D23s/XaL6T74Gp6cpXqn8uFcP3d7esr/VhStgwz1attYadCLTaGXa0IE0/NqAbxn2jw1Nfdgfe3kqNez07uKyGVAgDtMCjUjKhxutVlwOAqx+HAwYgwpMQqWoL3v2NGuuly7kpqofidqTne2jCay1ydb+/uvd7cnh9sHx9sGEz38yOImznRnnyySqU9lGK2t1Kli8V+wFdq+wSxrNACJTLMi9iGsmETv5GjtNxVhF2nqNZ6CxGXGPVUSEwAO64+UZJQkeZx4yAtCSDWhmBqgNTvAjelhcwN7UsJi8ljBvP8Ep6PTG7NfjDBIBz8edUtAmyRzYWJYNTLYd4wMTa5/E4pmbxmmKdT1u95pPUibGxk4+ARtmc+VLMGK7ps5HA4cV3w+kscxeb771JB47DYIf/jkbGfY3k0mm8BJVCV/NG2n4wBJE2zwgm6sVrVc0WRc/O73zbmPMXqGtirPGqF0q1FvtZr/F7sR5i1l7U9lgdJrafF+txoGeEk1VjQcS6rMFns3+FaSqQYp9Mlqi/cJSQBlXiRQkgsRVqxgk2BzVaaKDk9GogxNUhePnPu9PY/ii+/Zw2B/CkPSH407vIkjk2Ms3iPJdNQaMyOOmw63mIcHL4boKbzB605okakjRqvpPoGzyTnAKRTvneATwqwmxYeTu9oDErfpPYjSy1hhuLLqv1Vif8g3OZ7Bik3DW2pv2cNTp93jrY3DsZthHDI4EGN0NdiK064Prcb3Z743bvfEIt0CqttHpNbvXrXa938Pjll257nU7ve/4vZArLO31x0sv+te9VkYc2j5s93IeZrczGjeGjHaffP0rWP7P6X2lUkmXcrnctGZPEmwwNk+MDtf0YqyKdq+FtQIFKLI145xe7CVbmezCaKm6FGuMsG84KnE5Nj5s2gJrWbM/YAdl/HI8HjzJZllBSb+AmmXYsAERQ6KMz+WR+hTDpxI/kuROxtG9TcXUESc+7jPLtY2auVvjGvciWrBRc1AGbBo/LVg9uI78gqBAv9uo9o/xsNEcG5wD2zG1VqctCLyg8/r7YRN7JY07g6U0qsHra7BZ+dfwgXsSGzRGo9v+sBWD9sAsjWpVtjJrsRO2nmHj0Hbmt+AgN0eOVx0LgozjQD+JwV5idWdg19WAoayJtYncVoE4UrbPWLsn1SRSf/gJW3LC2tYejmF42JVssMzW5PLpfS5dYMtyGiwzyracrOFgLbJRgUAI5oIt7zWsKTddo+pZsybBlyQ2DUiRX9aPfT6xUI4dW9jMBzBDJ6Mgyf7ew2OX7UarPRTPAcF/eXT07uQr+5Kq8RNCkilrmfKHMlgukUlmqAeB1w5ua5madr5sOCbb4q7EMYSvXpZvpi6uJWv0JZhiZ60BWsVdWHHQalzejqYn1IlXYwTzHKgla1Z+qo5FR4nUzLv685ysAMk1Jm6DaPviPBrrTXEaF5KCkH6AkHrFVY0bP7/uNceMki8lWI+T8Aak3NfDbppRMfx+0TapuCgAVXuc6I0Y1Wt1g0xr2B+c9X9cswXPHwkyzf4Vq0pQedYFai9r5uHWwe67I5R29jbfbvPm8rZ6nB+RTc0yBoitFN/L4TQ5T7uYOsWy7OhHJsccAl+cz3JsaVPCIvTwiBA7MsSoRHMnWfaq+yJ7Oj2Nh5iTPBJ7v2AcoGK9VeN//fUXO+noTImv8ZupKnJFkq7/FU8vxdk/TjXXqExz2GYNq4s5ZOXiaaMEyb+qJUjhCzm982w+RFuSVbU6TzZXXsj1OU3eV6a4wVhBRrOgGM69Vp4VZo/I8qzcVPGDSBbkzyDD17rWMqD9ReIxiA5VY2z/sbadMN6NUd5qPL6GV9kHkmhGleP1urlkkBYXCybBS+AGvmqMm5dI+ZqXQzYsLTh7GM+aSSEziMNZrZ43uqN2kISl2+ldt9eMqWBjnUlFr4Egla0C55RMI7smTrczNkffQQcxRTUGthMI82rOvR5iGUZ4YOmxNQkrEdjjYfuGs8lSGMjE0rFYWr5GG4RVzuQaUjsd0zW5bdgXqyfWEZ+xDnYgopJ9ySNLmPPDPfAlBd0k3QEjRfHqeiY1GjBWagxDLYoljEfSZh3QIVhYXyasW0isC0i7vBxx74IvcZ4OMCanxLpjra3q6PqMDV+QWE17rOLzPpsTWA1cvwEvaqXSsBhqgs62+PJEzqSAwqKPg8oI34h3kR6H0ww5Q9q7Hw7eSJ4ey54MG70WYyqhh03G12Kz8Q6M6wr7x9pEnL52qMbe9JsN2NRPRK0xoYdCClbwxV5hA1Efd67a9W7nCjVerRRXAUTytTBAoxS0Mc62Jlu0aw+Ty4LHw8uREMMCbRsXkNSVVh0nqi5ocSZ0TXEkfCEv3av28TFEKuZ4WOtLdWYpTjOl/EybJk1UkqaXGF/kAy4651U44IJEqzPsNa7YlzoeUPU6EIx4tnPVuGiPssjWs8LxtI/EFp7DT6GDTOD21C+oXyRc0e+EmNaiEpRxXJZ0TWAcNIHYn6X7q/4NO38H3T5bLC1QvbUzKVAFLsU/96+XGsP2ElvgZ51Wq917FNcUYvgWJJF5VA4+4AXOkifx8dWgDmMEAsXSorzJHj0AM6PUhAXkVQsV81ieudNPkEejfc4XAhPUQivV/ZhUoQK955o9HMNYp9dqjBt13DwxXZMGXP9yEo+Lq0any9dsOvTJG0S1jRgHVO9/j2nv0m6ol4jpWdX2tiCsjaoag4VGuHfd7dYZvRXjjLU0q1wHtkaaVzxT2GV+epw3u/0RDtg5zAyT1NaWTEVqAYh+HsTwMLecQfGK72dtykIKqSRXL9u7PFRSUSTJ0NBUCJZm+odf9sAi5/1BuyfXACOkt3GDulKh22Fn3NZKaX1RA666R/p8vBLqLhoK8AyssF0CzAGToGDY4QnkWCUr/wPXJ5JVNQCNKtxHZr4xvBiJeh16mjhbIHyF44PBSY4UBvjd07772ve80MBPjabAyfgVxicXxzvD9vh62MOH+rT1i3C4rnpS66AmVNByuWOxOxuOO87SZFvxue5oQygllKgr5ljozDdAPyFvE/usJpTzQlQrnnOmIUmbdyk9RixQKqsvUMmVgcppMOo2RpftUVjvop4LrQ9TQ16EI60kF8oRe3zl6GBz6/XKm929bblksEJQlGKpbFQxrBG1M4WiNU8jy8CEnP0tCbOSNt8HQsqC9i9nkzVzNnQtEbRDlNtwK4zMqohFK+LBtuqH9TCzDxGlSqHF9auaihlVz7mtWPci6o79nIPCCm76pAGqpCATxFHUWNugUTnRhJrThQ8KszK5Y9Billu1pKg5ihhQkxP5QoLJBB487u65mWoBQ1mUQYlbk9JgR8poPCk3HwFTFraFIdEW+1q3HxfhFAM+w6k+iLNhe7XzuTJhHxfvklydpfS9JdRES+VBhrTyTP5jgjGbq9vlamYZZcg1ueoyy3xumNRaJW2E3LePsU4ULnLKmoWSUvwpY+KultqMjt4N2tWr6+64M2gMx1m4vAIsCtdG39Obovg1kkymU+JicBjoraQHydvi1EIaSEGE5JRTY0F8YhfiAZt/nH68H19hv6ZkmQayuZr/VWMQE1VMJYttFywVjPqXRPO4kBNaWLwrsLpcOvI48mdxtUFLQAMrkuozlrfBWE5DMP8BYjkdQcDhNxYeVHVsLYm3AUnzfVhtMLxMYpuxGtjdq/b4st+qDvqjMQzI006PnevsOnDoKNO8bYyY6JrCKuD3OlhfyHKh2HtZkDH4gr8HGrZYLwaN8SXYHd31UV38wBInFnYVKV+5FKWJDUiPrJ+x6DzQ7d8qJTTX9BJBJJkLzMpwBiRTUhcdcjHgpzv/oXOLgfI1EPREU4oo9UAJKaYfqUeOaj26Pjys9UiJbaX94m0Wpa0RWLQOrcuoASrbXBs/zeccsvxloJGJcqGQj8iBOwmywIysnLJXnMDxT9+jn9QKaQ0vkxrJjzKzQw/gWM+CGS3bdtq6NV1nKnlfTE8ztk1c6TdNi0MZaX3JVpxJecGhbmdrSFkVY3Ia+JH8AMqCVm0gSxq/aYpWYeeZss95c3bQ7eektfp62FUKd+FYkWFHIHBHuamonB5JxOgOKtelOh5rRy66iPxOHQovV2PSKEICpbiuHQuZZa6kE0JsnF2Jy6v1k9w+rBHxN5ZhmzAJ9Isu4AFslDRukUIJq8QTv4yMtEeMNDflSr54Y9j+57ozlMbs5ZrBwKqyWBP6KOR144uce6kxpvFt/2CHOYwusQ7A2J0EK0EKFvQpcXlJZHO45rKWTDmkTybct3sXl+cX//RRG9Uefmv+c9tyKC7kSaq0aaoy9qhxV7WGdfNUahu1Z1fYRfNgLuNRVoha95pyiYvuutg9Rx1lPWGJ2S7ytJAShUnAPVOJYDI7dNoKMg+HLltptad4Iq/bardyWTO8YAM7A8Y5sK3e7rEVHDvYfrt/tF3ffP78IEZ+dcFjUgaPloJMdWHdGjtpO91N8JgC358g6KG2h6uVNZ0yvIC3bBX9NXVun/dTk+RmaZNDUvsM4s8KPkdrJ8wqeN8sXlyXK/XrC0t2msNbUjuEhzOfFfR8pY6GK/ETTD2cxG/UnIJE4Ekh3TmaYvTR0OvrjEM1TtLuI9p7RLKDJFtba0J3wujGVb913W2vrEsj6+X4qstdkFZzKMDRZlv4nMiA4+1ltRr3/HKQyQUZDz164nReiCUUByt/lt3ylvxcXvq0TKfOAp4q4Lifz/nk6AOl5H25RpeIa1fc9yq5ChfC/gzs45HuqSI9gkP8GDcEacyfvJ2WV1tAlxiJS2W/nsrfuIgWKSN5NaP2iPfZF/Y+vHkTWUfV+OW4Pb9xs+pc8UQBPt4+srXSTrkEa0mOs+Z2PUXNHh0AwgD/IwiC1ilpwbgVldvg+LmTAG9IPNOy4AgduqCfZ5YCVQ5U5KFVtWuLPqGwp3mxY36Bm6UNnhpdtrvdevtHuzn/ubVff82GJr3MfAX2qyCsPWz/A68gDayMw4mvJVPaBeM2kzuSKXZ/o6YYRYfZNvL7SSx+yhhDtn/hG1QNXWC88pTaVeQei7qxR/nhMqmcGJ8k+hUyNoPNFVQGDc6w2uXdIMP1z2fNU5xQ1G2sAreR9/05pmJzhO/NsoK7QLcYzgfc0zpMVtm53URGCi2C8TVxXedKPOBeNmpO5iVv2c6gDz4jt3C9J81jwE6Q4L/MRfsY0G+QAYQiIObQBMTWheS/TGXgpbEl1ALEAi+Wte8fXp9ddcZwnZgXxaJN+YGC3EvetzSCbDcztgf/Cknc8O7hJ0v9w8Eudigdb49GDVTZJe+5G2iQMMeSnWfnnYuVTu+8L70mAy6daPzUKtrJypWQ9mq+zhOkOJgsTUNN4igsQXCBojUYtiMupFGFSj1RNdoU5AbydTF0FY1pOV0VRjp4LoGTdr62rvvpaK0TprzQDQcbEi4UR/aUzTUnI4oRz+kyxFNikbpo5/J1egz/SctSHj4y8DRuy/6whcU1YwW7DyYgvBP/ERe0XTlxsTrIaR71VjMHBMMZcug/U2ZkzejXqNnotTrWKny+v/Xh7fbeUf1gf/9I2kNF58XOZR33nwrvDBw03+glX+8dOsE81jtu+4rTIpUFzRbB1rsC54zZbcrEsrGMqJUayN2GaJTg37h/DS4rDq+ENM1Cco0Xme1hJ0sHZEoLWU0Tto4da9w+eP7y4MWOrqeUE8mVUo/Yv1b7vNNrt/B3/Pnm0eab3Rfb23s7u3vbKtJDjBVZd9hqftlofief7cZ43L4ajB8pRcc0sLRexEjddNq3dSiLamNeKTjauG5Wcc8MR6OY/npcf91Rm9fOxGVW+qI/vKt3Wlpl7God6dGJWQJEztFlf1wfD7pxFQcSV/18zO6srAPpV41JPLBOjIrJ4E8xIsJnIap6mEjzCdw0yD2DrPH/JZsmgF0T/IFto0otvnOal0wCW+SBXKFQ4NxYBfnpUl7OAJMhJygVBpmfnQH+CBNrh04QSzLpaPIVvSonQtfoirnwOHOB7MSg2WUvCjLdzhmerhnrXb02BLW9a3a/sFLy8BBCVn5lXTn2J8W2ypnbObYNeoKAHAZheuApuLQLyz0xHl63Jf3HQQHWu1iBgxBEWDwAQBbEL4ztaSyB9nMF9Gw31dgWdWzliDEusSXezWpszNqVRQl4qXnZGDIqUb3t9Fr929HK9fh8ZTWGtY074257nXX3aZZ/ZRez8mVn/dadcf6gHW4p/vTSW0fNDvsbFxL9Eih7YjCm0mlDZ3ykNkd4slChmK46qgYBqo3Yn0BxdOyXy7oTxKANStzdcFj8dD2aoNLyC9IkMVOsqtlKNfW4YFNjp2lQbzpK4F3BsMqONtjn5bB9Dh0iToB1Ab88zTbWkXPB+gwl4Iy9pO9SzVKhGQCfZuUM4logVqGAikct0AZUJ+1qNU59jxvEKtqMyvoJF0RftXExVIRzRjWyirQ5eWY5NcBi8c0rKgkgDkER1SZA+OXSTmJwCsSnmFq9ielMmpRaPnDo9qcqoDjS6PIVQ0SUoYk1F2NUvLRXFDEqwM1PGLUQ0SuVKbfS0yzMqj8ZPGdV5adWxAf0x6mHS84R+wT7QtpIRiYj62eUf05VGGNYKXH/AkHjY0aLYmsUDITUNRFnki75jIsjoiz8/0xxnHaIdF4nebmVmiBLDSI2qBbhTxJG/oSx26lgBdXzc4qC1AWiPk6xD5asGvdOAFOolOexbShzFfMROvxN6UuNjGR1Hf+ka1RzLj2V21WGKxBzH/KOYy/mbknG0GVSXFhwPiO1hBWQrFYLIR8DPfAmuAXDXSk9heW6RuGBU+3mfT49TUbZ8Lxcjgf9ZbN//fVXdWtz6+X20uHR5sFRFWI5lmv69e2953CVHvRCgSCoUnto5HWEQtYIntQEZ3Q1ATVdICUoL+cbngzWXJ7wRRuMgoxYuKRfkauX9HJ6+EMryGTRP4O7Z6zpr4NTvuKb0u7J12WgC+DRwaPgHhzlpDmCizchwa+gB3qCNiCPkomtEWQD6s/xFhdofYHVEIcf6J9NV+qD/gAld2EWDPKBcK+XJdm3PMn33LN7LVNTdy9+ssXevxoM26ORCBOAORd7oM4maERhVYwgZKpxcYMN4d9/PwoViwf5uNRZqI2U4m0QBr0qGQnvtR4HPmsYtNtjfyQUBXKGNHBFPkVRTrU1oSeO1+SZJ5aD5bWouyzyh7R44hyp557E2CD7cFJbL5LTSqWRJAKDaKoqFo+W1JwYon3MAogNcEAWhKNkYkC0U9nTlBQmwQNN0/ATY+JwRaP+ABkFm7dpxHaw+XGwst5m+Pkgn6/g8zkeixIID2xgRwOHOU7yVEAo2Ci/2D7YPhAADpt7zwPNdhLlcguLjZVo9vvfkceHm4go4OEfLqsl2aGjrMGhOJlE7KLfv2AcUYaevWNyZ/uH+nXZ74sfjX5XfL0a9cTXYePqrMtonrgDLsryafHtDPQI8N2QWIPHnSrXbXUC0mxZoTjippBf+TgKHeeMgUyr7p5ADWzQHslIMkmC409HTbZHxuutfvP6Ck12XW7pCjLAKIuJjIPzFExsJh5be5rlT8U1kclD+AuMsnEFvbNdkZuAeUSwN71OXfMWgmOt1RmxhX9H8QcjzXclx8N5jCB9/uDoboQLFHQNddAYiCUv0Tti2cPtw8O67sKhI0Eg5IZfqTjtd3YYcsJktbhutZaQNlz8qfk2GYZlVQ+oB6iobz3Bzv7bU2Rrq4Gwz1LdevgyEtoNw2XMJAnqVQTRQ2/RatIbKdgJNxOM7srIBlscB2KDFFbzOqSPXI6M0h18rh8eHezu7ZDOCOXyeAe2l5JvDO8F2JR4dFRjQMU0z8FAiaIOOdQyHaCAM1pG0wJJPehWaBYaoXmB/WJDeN2ufsBy67qjBFvveJEWldJQi+v8NVWraAYmgrQ6muFFPUJeIcqeotab7fMYesg2zugNCTmExJ5e5tf3XzOpM79uRAyRD6xVcG/fUVD/TjOeV0ERrtM4hgyMotDGvcATMRHaNT0owkNUFt+L4uUjPLU14SEcu8fVn8r7TWweYkjmxmWF3sBV1GrrERSN2U4w+mrBxDqLi5gwXh5CP37dp59tRM5ZIJcB7AYfU85qJKnhEXgE0l9FjDu59K4asrgGCpGqUUjDSDkx2CEVnEPX3EnIczSbZTX0WA2PCenAdHcJ+EPA0SM/DEHjnT6jf0WnOwGOro9jxdiQxzAHP+LpIJMIYmx8yV97fDYqFVown3UmRKWEtxh1PCa7jJF9ANmmdVl7EU5rEjhjf21Op0Em5XKx9JEF6TWYznvS9ZQwwj8Cg8Od5tEQ9inT/VaVSVwW537vjM12vOeeiZZ8JFCABpSisJvwiblE2XEa050nMHr1hB3bzeuf4K8eW9QfWzjFsVkJjFmpiGiegzYGqW33Ljo99uYUyuX84lYfbCCp/9wjy/OB1V7f3NneO2LNTjHubdjvtIJJ46bRG1/0g8lZowVBCT/bY/bj57gdMPaVtsfeFsxEitd6cI3kPPgKnscp5dwanByk3wSnBIaFPkxoct49f4vOTuDh3m+xMaKgukxzfbGGBgnOd06Ix5wASzkBFnOCzGJyKTjZP8Cl4qyNc3oLVaXqgE4usS4CGA90s0PMLvf7PHmD73saZEXviGb5pCEoPcxzwYWSoookNmoh8Wy2SoyvVwQ38oq+jgfkOlM0k9qG1A/MwxcIDAghzqoyGhbcAs6XdmbyK/K85DynOioJS6lALrpERUmbIDcj5ym4P9mIrNHqJym/8mmI4LEPFw1SQ+k86MGv4osKRFExg3ybEaQSEj+zMXhCsJ4FGlrOV8d1bFvKaIB2xOgVfHVUKf0VbwW6DJw1AKPildKlwjRZo1YWebCgdExWRM5FZewTUA+p5ocvwiL5IZeRwEKUilyHtqlYrgbp90AuZ+dBFMdiRRB0WlVWmr43r5nM3RsHkQY2DS3QbVXTJkL4GlO3yzaEFbsvsKmqhI2mnTkjA+0zHLbsivSvEXe9RL47xFwL755unxHx2BIx2kzcfhRbgo0gmO1AM27pnBLiIK0WI5jB2Alt6vZNNxZkTq1wlnkTKQamgnaYkgNtbbu+abjyBaYIoF0xjqql/R5it8AY4hRJFZkitbqN2RXorJek2TRiuST8aaAc5SVwWtq9dEJv1yhVnhTHuv6XSTL1BgfvgZpvB3XQ7LrjGHV9H2H7BNJ2xekz2TEW1o6JJcd3GgmO8p28dh7kIBq4wpUDoyxjWcC2z5UYfBRNmzGTCgALCpanUnVa71CuRI+hLHlfQEvYYHDdLVxXDzoiPLVlgkbs1EfGBoiH9HqleopdWVkHc9ew321bjdIdCPSNgphUhgJDStVn60/PhvgvjkNRv+aSKdRH+By8HAnAJ1+fnqaeZrXH1lwvlFGaknmmKOUwtJw27VJUiaLXkToK2qwIWOWXdOyt0NKj3XBvjX38hE59OvTrQUaAy+iFtGhPRou5OCitOKS0Exq7x52n0gVNBHVoWjr1fq56ozdojmj6zeSKp+ZWc6YPyHN4qtmX5AmgnWmInuUDCpApvJnEMritZoMaP6SScN0qwn5qVGYiHTBIucx5ItRlyi/BrTiMkqkwi8IflVTJuOr4ITtnvUYGISXkQigK3fusHisUtGrdEFpm9XdOZ8UuNPsrcRUWGQRXfxAkwPMiTjo74JhVsAa8dfNB/p81zm3VbNNUviy8sSPOWUMGVBLgRk0dtTEVsxwLdC3FolGAaW0xyYatasNC2AlcB6FahzKG0DnGLDELkDFTNck0qj15K3g54boQaDyceDuFknLMKGOqg7mBs2bU7IiHOuA7UTOgxc2ONEUzY0/uAckyCIXC6hRtViwtqZsI4RjV8au5B3RAxS27OxBYPYgKWja29uiBkcB6F1DuJNS4OV2IilMGCWoavTdnPhbVyV+ZDDwxSzymdAbcxALY7xTGAMrIKIwPq8YHQMkLdGw6armil92Q2qhFN7ZRKy5o6aP3kCpo5hKBcgZUREfqdPXzJWHeD26dMdDWiaT3n657mjwvppCiVgpGVBy34rM1wg/uxMnX6Wlqusa5BdAxU1QP8AboZ4a/pBAfn6JlTe4z0SRah7Cs7sltQUvUoEl2CM8m4sY1GrkxR4rVvKnhF1u7y2qB1+EXUOZloUdZi1sjxIkk4bblZuvDEOs6VZupo+JqZrc6ytJGIShbseBSWMrIVe6pB156sTAvbhcTDn2OolZ42Gg5x+Uxbn8NQv4+EaQG5zL748obDpoklWgHwCNDVBSVsTsT9h8X6MJ30WWAxMxR52dbvwWm5Hwul1u8FmktJ/BHw9tZFAVRvZSD4svGN2+GQIvsTrvX9OSAsB++/iOv/ygIIi+1KbeXZGdD5juw4j/0YdcwvATrTOsFY4lziIOBElfjAlDuxn2eHyQw3AFgmv7aHH3Hdjcv+c3m9bBb7/QMPR9eY1uyP8Bd2byEhbD14eDN/juILnqDbCOiENe4JrkGr8e3VzW2hRWzGBckAH+3+hDs7Sr5cp/gwU7lgh22R9fdsd5YDPnjbv2XZpsJ4k1ud+229Bqm+rQxxMAgD0OC/7luD+/4qxwOHbbhNymgc6NKwAkc13cDOSmA5a5q7DV8MUrZjIVbETpOWnZ1RrRSdcFWE5Zgu3FFq/DHmEvsUAubLiYxSt8KqSPhDKUTWxpwp2FtQ8PS6rEf47BQrq1CUYMaRkLKK+d03bXsn8GEJcwxg04bgWs6n2EqHsQHamB4OBt/FQ9qEzMvPSTihBtqqEpMwy+i4fm5fCQI/0IqU4G4FmhgaslQtoCADmotZ4ANva6r660IfMdRL90bdHQ2eawjEh+irT+0Z3zxsd8JchicmikGrAexFUZhN4ycGnNkwgoFTcxNJTdqRLTqNw0tXhzftXUo33WvdZgfkNBrbazMWwFnZGHk2GF4xAjR1uabN882t17zq4o6mgNpDn91PdDQ8SMaxGfgNhWakqlaLcTeIF6gBzpgPaETNkM+aTROYfQ5jE/hRqAooWqayu3kfliWtAcypY2hcUH0A53d8y4AYtOl2yEULrgOg0iN64JQFgY3PMu1aOEdr5MR4Ub/e7SDbQKMTwsx54iz6JfKBnNOVW8dHpxikhqs3c2fIevNGW9zCkwZQUp+loZHfNPrUU2M0gr9ymMxpV8RDVsL1WuMDKp3igZ9E4tjtoXWrXrXiA6elxY5lt2Qhwu+ZLZ5V9soqPNZtZMSiHGYgUQ1qkn1YXj8BCwuKQ/wpJOKvdtAe0hC46rnowBy4ep5Xz5JvnHcdIlwjgBDZvgWpAJG/YOTzZUvlCJuhXUd8f7R2/gxV2Kj9FO77LLl6wkpCCstkUKH1WpjqwvMm4tef0hWiXrjrD+UKKxq5jVZAePgLseNZrOtRaACAdeAOe5dAwmsrNP1MqE7XeJjWsAs9QH5CAOh1ongI7xDEzHTKTSWXsLOPgYv1S7A+lUjZb7HjbNRv3s9bjuLGTIfbmlT1MulFeOpV5TW3o3ntZk5irHO/f64jncziO0OQZSWZhNRLvMeUit2jiOQkuSO3718x368eSHRnTE4r/r06dOXR28BhcWE4HRiLSwJN0qsP7ZkADUoFEZunyVT7gvEY9CwGbJgAMpqpQ3bLlua3KBLliOYaSosrbrQXlp+S7o/k+XeCO8FnTbGNcLK2X8tZT8eU4ykn9+m8dBWFHJJRZcJ939iIcnZ15aTsXz+zLpCj2bKBsepGSegiDVaCCe2sxPS7O4c3zWvKnefMikO/0qPIxocufG7XddEuArJ2HQWiqLw+15FNCGhlzmEplZF1Zgs5WEpymR2Sq5DOhIgx2yw/DC1pKrBVPdcDs60AD6+SEoCPHiGA70FDMLlKulNI+RR5UJ/H0W9AuXenoib6y7O/dmhjIuMZ1Jgexd87hJY3jEq2GFdnqo9gM6MFS1PjfIZrLNhgnw0VR4JZy0EksZkxN9IqvcFwaIateC4BNSYlOH2MF0iqFAF24XqTGOtEissxFLmZbj8VL435KmCalke6BTuocpogcXAKcL+YpFhYDYKHi52tfcW0CTq+zaifEilqLavrVg0qUBIw2jcXrKpBNb940oE3JjvcSgdMbq5eTlU6jj2w9d/5JeU/u3Pa+4CXXlHDvlh9V3/esxuaDQe2KkS8FPunJc1uduANlhhJMpQQgQfDlx8i9z5UEScupy4ZGIBBC495lEJVbgsQhRmnryBDGGQVpoMvjPIaGYbfh7ji9mRDH9VeAIeynBJnY/8MXwtfD9X1/gprR/RcIdOaV6GDmr4wb085KN0VsPvsN+xOKcdTVHeYI9H7e551YFjsTY76NxZKV4VQQ78mmDA4T1B5vnuwfbW0T6oD7ffbR5ssq86qnN0rVqNsAhQs+3A9cC3JNcEswFP7L/G4VmcI0EsXxBhJJ6Grt+KZ8BvFCUG6WNRhV3p8ThoX6xYdjHN2yBOPX5KI14vplCMOn546mGnwB0EMxIjSHsAyYihss1LrWhVKDA0BZysQrxx5uvkMSqiDnWH4NlPCu1CIFORBEqORIRgfzVvkPb/EocH0LcL8niCf9J4O9DFZlI2PoyywRiRjWSruRrXKZkXnH/Lxgc7MELXTC0joh2bSbgNe2sok6HyQeWR7zHps8SZNoTW8OAPmw1opciY6yFsMcUabAaa26epboJYjpg4vuz3v3teuYU2IHizAm4msI3EU8Z7PHq+v3X0+d02qhQRcmNigHctU/RambyIShxDGZhQ1IVi7Hs4wpkj7hG/o1Srea5aDYdpCG7oJBhlDAIggB91wAGPzkgEGWiIJ+OJncMkiEZxGJ21wCcmEFPdj+LouMBe5gPrw+eyxAEMN1gLuX/khMsRSS5IsDvhvKSI5z7RsnWIa0k2zOAIwXdVWeSXAUZfLAVIXsW/nspsTr9fQDh0yQuIBIHuF7gZphnuCEfLj7eQbH1WHk0zaoECIvVQYqrREQxmFV0L5BEgckxEJeaCguhmh2hTd6Nx+4otXuFviXnR3wLu4EX7Wb91hxnRR8topuVZklZGdsHD67Nv7eaYl6U7rOwRxCfzlOCBcmfSU3w5mm0k+uKrh9InGtkOqhLNvN3qN9k5Xii1R42zuKRdhtdkoAciwNS4MoHGKJwhm5UyKyEDEx9+2R+NgeQRDDX8OrtjElEIw1GHMhJs8Bkjrt9ZG0FWgOzY3Cxw8jUpt5xIsWgXbowYZw1f6UTKpAxZD/P/CWgLmBaBbpGz0S1stTfPhO6JmL/f1oBruM2MH+2Nl5r9bn9Y/SuH/1sPTsjBFxzf1IRj6TPAmILJpqTzCKObh0Tjgo+J2wyuM5+HHpVr8aykJlR5PCICbrHshaYqErbf0LDQ9rO8wR3u5XNQp1TcnBZAG6puoxaKv414Lh1RuxHTK+zBMjuph3C++VXcX4L0RZDGf/W2TlOdlJHuEWl8fO+ub1q1UHBqMrOOqJvQUfXEhFF1pf/0LWi60RTWtiX9Ak1IQZhG3C5yEnrSuUntEvF4tDOetoTnV2bsefQjpNYiOrGdnlco5mMnJqBTRkanhbBxFHkOlC4EwYm9Sk6HeAIjpZ3Cixy1g9uslsSae0s+GHSf3JIWLM0EPcvvKjBsM5xEUmfKwndX47HOR3UMRKqP2HHK2S4Vq8Iv1kfjhrKb4JFzeLi7v8dmBx8Gvk7y2mkNV0QreNvocpRMmBhnaSCIEfepA5i6x3RK0IOzIpaQGZsWSizofsBta5zhYGkXWcDH0lrP2huVJ4p2XGJUVrnssJYHLYBsy3Ezrs5ycxAgOe6ch6YRF4BDy7zCvB1bnldvUMpsvHsfy1B2Lx8RoYATlx4sEipqKp1KEmadJ+GfcfGLO/FgEc29WrMAi/PDsOrS8V0JJwxicpsKelipn6aSNkdrBBBKr3gpvy2H5DfxVoUpxMlFhTyzTV4RStEBI0XjnMhsHWihM+QB26lSyExLxMzgishUkeCcgSdo08oujncqQTLtpcPX8z67kUsrmbzFzodOmsu79N+azAE0EjuN0E/JmTQUaG8hQJpCmxFxFIRNpaQHNVZeivOg/LEq47uwPfQ+nmDlHuslN+IAFaZoZ7fvKN/TF6zN27TXjBK2VvVeblpyUZb7DcFOC5DkGbnLhGAEReo2SiMTzMV3P1v/8O7N/ubzJeCMnkAUWWDZDgnzHQzQ1ZhfjBn2xxiPOVuHp5dA34JVLBkVgM+deExiTKoKrdIiDFUYKrF1MQnyIo3jDoB9gcNJhw+BLtuMUly0wAyPhQojoFxmcJMzq+Tzwo/uTERxh47SfNAyGyEKqZljR0XKhE7jYLRsKgg4p6JzEQjqWa4YRAnTbVWdubmE+gsW5j3UC0CPrw+/D/Zvq9WYzaJUCKXEj2LXQsfRPIZrxgOhU+zffKGL20PITd9zeYwB7etI4ueOVpvhC6Pu2UqoWyuYf953uTzJOe+qMUAswTV0QEHdAtR3fTFqtu/uRud36W3ycd4+OGDcnCDAq8L/kgn/jZEwj1FMXTbL3/R41G4MpTd2PE6k0of680FCW1zx4LHHI5xjYC3jz2ViQWYDVSa3AxQR2ZlIGGboC4wUP8g0RyMN4g8hO32I2dmoRenZN5Sdl4owLjGeVllgtPvsjrQBm0mqJOYb7Wrhv+/L8UVHUbBQ7fPkQanA6QevplpEoqrwg2rgDEANbo1kCP5MV3o/R/gnGAPzbvPwsKqlWHevJNfJKLLczL7U0Bk4/UbOeeFheSP4eInYLoHeWufyjYyy5WvBJ4zUonBElduce21zkLtkYGXKhhJRuGwL6Xyq64h2lHQTIYcHnZ8jy0fOITo4X8yIEmgO14IERh5qozc3higg45RmYV3kkUWrFTYvLlYs/iLs0amxnrigRww0DRMleSwLz2IgXhrtN2Uac87/22eBT+CxkJ5T22NEbi4ZZ9Jta57gD6TegUm+F1udkbKhsE8Z42V44geGbAMOMM5+QHnjhl4BH5SilmtntKyndLBBMVVgDQfOgwSVFI42IbjPpAglkQpyRgWeNpYQKh4cWbKMfVqqYcz4Xbdd5QxVkmD7GwTUP1yPcfcVcEjgeYSiIWT9XEkT8ueYnHhslWUxUsT1kjAS8XDk0FUyKUCQYGfesH3VH7fZHzZK7Zt2HUDp9RsXxNliPZn4dYdxmfFvFOmT6Q8vsvR9xQsypSCTDzJXnV6Q+TbSwUQCEzcEXJfO+6zuIfdd8nS0UB8BdPOQX5zP30xn5GpVsp33CRCJk3DUt4in1VRPmvfCzvaRmSiL26VDRYIQmJqozLSECidi+ZwvllNDTFKCPe5x448IZhIexY+DfAQ4C+vk9PS+ghqCgvQvfiySeiAk+/5rkUqVDx+yTaWilRsuole2/UJPWPFkqXPVuGhnvw3aF8qAMWxzXElTeDAM7lqlzvyIWnFqMwaR5XlUsZgHp8KdviTDDgm2UUA5qZ/kViqNlXPEd43rs4luHc1rNg13dTiDZSRxQmsBr9UoyFtAeg9cwoiojIbXPM6F8IYasl11xVrnI/Od5RqWjUVTxDqSrokkMvLlJ1//AkR4wCWfxjlgVitF/uY+Qu/6Zd1tXqMV9Zv2sHN+BxvzBsxxt50WW4AjJCD3mWVEYNeLw+psdLu8GC5UqW5AiU2yZkFSPK/v/FijxcgDIgYR+nFEAwSSlfnqAeOyG0NRCN7ifAcEY/LaQw/xisGrhmRH36M0Q2yAoqCN6FwgaiE2QRS9NYDnjO32L4AVLT0IqkgnWA8BKtKfo3w7JlQRoW4KXjxvYxThjFNH+kMHWhFNgi+iuUzvDwIF9SGng2hCLGAcUgBIyKtpEDACYt7mlGQycmzt5Ov/O733K5A8oUQg/rSJM6C2hVlkUmPyPpcuTqFkSgbhcRlbcSfqw8Q99hEF1/eLFgLhbGOHEIIsZfMsRsupn1ahLLMfxkm+DbjrMq5mM7ZF1pq2L9i8skfIByWRWoL9j/0BAjcLNW3DcMqU0jH8EAIyCPGk5tHyhDsSd+I9/d3ULHLgiQSCDDT4bF/BZ0s+8h4rDSA73Nk1yG9X5GeBsQh6QGosKwYHIE6XYicgM8YRdQ7fS14atqua9tZT9NlICu0MXMsogEV+OnqEAlQwEngcqgweQbSzOZn46iKyOcEk8+2Do/rm1tHu8TZ/KceYyqSM2rU8ID5C4q6iIqZxPb6sg9t1NUZBBpaEnyLXfjlN9F1Roc9vAyXXZ0xRGvFmgXF5KlWExFxy2xs9CBm51tBZmd9s91rWLU7Z0WslX5RQJQ4PAj7mKsFmSNqCZmtAQZYjolUViKEeF0bteEcCKfURKNbjAQchC3smtaFtuET0D/5EJvX00l/HFGbgcPI0y34FEamlfARrxUwM0TEI8f3zc8Gi2Wsqjnt4tnLcYKhDanEtQf0cDujawf9Y1UArtCQPjEMb3oBI4aVVfgevXRL0Y6TwEkT7gf2DZx5VPdFkMACFU0/wm8rI7CiU1q4CDAO2sSoSK8jqZcYK0d7gFF6vdghdrnrKEucQDGzkLzITAbEJt0GXrXzSAWnGwVTCkbgprJkSzoHSjFjNElj4JBhNgozmIZjk6sakmfpZe4O2IsIxJw94C+9cJvXbtdCRppkUfALpLa+6iUfodGWVj9ujcf162JU43tH+WTw/ADzB/vS/N+5EhDfYOXSQ2AahyMM/OoxJIyNcf7mCUYZ7nCuRCpF8y3nT3RGf/zDs7raozejrCfFQFX4cVfnVU8KYl4i+916OLyCMV68oRo0DifMU7qnl7Mopp+7gePuFHipxB5VgmbhJSp4E/pfE7U4sl1T0SBUX8QcMowamC062XrBMtZd5T2XG8fCG0bRHHEkIyCKMSOas8T1p+KivkeSEILNermgTarF/T74yhspJ7IwSStLOpDTdz7nGTkKIGqMRXGJD5Fl5aqFUVa2y4/x+gwy1wsALGiV8AFFaw67Tzo2NIju7wiZcIUljXrH/MNY4l0uXgC82nxSHZI1NKv0dSbw3fL4o/YB9BBz1cp7kvB7JBLvw6Oabg+3N55/rBx/2eHY0F/wGbjIrgN72GNGYUMQc9WGlyyyZfVZxanvYuTm/ufxJhdDFzs9Z+M6O2FNL0xAkl/7+eylSybBUrS7FiRPS9XFsazaXqktSkWA+iEuCHd0QnnPI9QpLW4x2PVnSQ3Z4BvTblLRTx7JcoZhd32M3n4gfxgNk+Q4/EbjDdUXJQ7ZRYktWoG6IkYjzecZgUj02YMOMSo0pwC4zxNlHjE5E2uZrTKxNts7qGSYzM/YppUm54LePozjBHZYUYopuOwqm89XmYSiZr+lTsbTY07F2Jn7c6J4KRybpAa+/JyPe4nydYCENbyexMyiJWNE8eXUE+GStcx66hP23ShlucRqUihTywrVgzo6Tr+wcDP+6N37Jfa9freo/0OBCgkWcvAkxDOrka/2US8rRLTNfvab/cKcYMto2tcRtBCjNF5HaiD6HB84at1DB+eNoDuOcK/dzX2D0SYNjNOdhZqvMqdTfZo+pc3yN6Ywea/mLRhsOxIqdgocWS1WJQeLYOF3jHI8K2JZCO2UkNjzkYwaNqAijquFExisg52SNdM8CwLlnO/nrxinJVSlH5r+ZqSWm3GNm47rX7fS+G2GM2FLCUvWUR6Ifxu8R1kpLj2FketHOWvRkLE7tUzaPyU45lT35WjsVsVM+gaEWbZRg5RqCjNOyUJXNTQ1m5ICcVdB4i2WgVKoi0UoMKwDpU8NmXixrCI5KkjEoU11ZVlhEWbbmKh9GVgoV4UPmvKeMmBwktiWOEXJ35iAp3PeAwkrQkpMixaHDbQcjaaiMbkjhHA6CrubzpTCOO7yL897OppqCIs5LAjZ8ylk68MwCGyEQXzXbM+3XdkWztKH2I1JPWTWaNOXwxxuuZuhXtMowfanBCUie21iZKCqFkssaQ1NwjjCy08tx573ATM7A26q2Yjr0K7yd/ru1ggNM5M6e13npZeOoOxd6/qlRxqoblrSgIkY7MoGCy+fvAp+l6GVgXFHs4NfFh4WU6nOGWSnSGXc9crYn4gELhtFVpYXJ+W+0epFXiL2Cfpz5mXtlBj2KujVnI0U9Zh5sf6Ihs28Z7ivCGjAadDsymGLW3juRlUs7zIrHv7w72N6pH757s3tU39uvb799d/TZmCJ+jrjmqDEy27nuOJZDx2bE/lh0d2GAed5YIM6jrXM16GoMg5rmGZTJUdGsYZQCmkknZj4j32O4M5r0iB+9ILahp+dJZ3h60mufnjT/OT3pXp+eXHdOT1rD0zZjAkhFVePKOsZZANvM1SWSUywLo5S+c/jcaLtStle7dupc7PxZOOXCnF1g4OurkYiq3hx/ZEJETebMCEKA8dR5ZWNLSPF8jsGTFOHeogXn4ANWpVqLrSuMq8ZOj247Y7FfFjHANhmfBI4UKRHzassJ2hiugD6EqxaJT0uAsjFJHsV1DJEQDJw/6rTaZ42hukCeT4rDC3QoTBpa1ADmVk1d1SiKgTJHw6Lvuup5dI2IfkEU3ZtrcjbBL+nhR843hir/VV/XMBvqOMA0ozxfnUVyRy4tKBIZw6slIEiKzQIWT7kRq4sQ2Mjt+sCtngE4i1wa/RVs5ZzsmtF1T4rpI3IQ0kdPReWPZgh2XE0ML56aviRBUn8VWZNmYTaKYVXAkeZ4276NoYPCsqJYyGhW9TKxaZieO+C7w+9yHyUzCyxANsVoYYrkVQHFpsXVONfejOQX9glozAm6BIMG3j5Df2132zPx53f33M1N/UKfDr9kDp/V3Oo8F/KkmESnGDn/UTHI5JEB1oRW5waypbZIUY9QM7UnTxuQKJZchHnWzhQaJ8TvdZ5ODR2Ek/c+22dPKepknS8KSJyYVZeeZtmL+MtR6wkIRmZAdojmJEFhMk96JBWGxrZCzPZUjv4CrwgVCe0R50t89RJnQ8MTNIt7dCgoQi/F14S7hEl9/LSi5ab6KvwacYtmA/ig4ipgBY2WA2G3IgOnku7MUbOKUj0VEXaAkUg94CirtgOF9GC4V9/YQqH8L65RWFAB4ODzhF0XGoNOP83LdvM7sbKIN2xwsr9Oxm5NMmZp+AkV2C/onlwud/CRlbdIYpcTQ64BGkkiFg7dcp9VG7VwFIRAjhEjDBelbJh00hLeX3TN+GR0WSNyJV9kXHMZsbU8ePwgssIZDI8VAlv+G+zH3O/f+SB9QcAz/bmsDMWkhpFjY2WW2G+rRdGZTVjE2Bbz0tELEOVOCxKI1xSuDDYsH3lZbTq0sfV869GL3jre9PMaAXMxCsPE1RA0fE1ekBkGKVRVJhfTc4iNll3FUmsksiyu1ZhN9R6032cqegjvt2AgZaSaI5SFOiYrkAqM+FF624+rrli5VWfmFIyrFJ4MVk0NHS5Wy/5YE85Fw7aR8Urn+7QX8wmQGnWe3cP4YEWoV0bCbShLw1AS0baEFrYQr1BdjGvCmsCqPhq0m51Gt3nZGEqudBF2NXxaRXG/FHjCD2twbuUuSO1eS3QUuYpS0R0o0Owy2ZZJtt2WGVLmkIIM8CXAfo8u7ngmNEigyZxTSyblfqNrpIZXWgGXQjai9a669PyQyrpGBkP3PRIPQroUDjccLa3OUo8uroaPUGxq4a+Rg7zifDalao1SMzpU9pftH4Dg4XxXhNY+QqGnmwxscchgZgzfj9CMhohmOph9MCCr5rt14FXlV5dH+ZkfWhozzeheTRXjiXc5UBLPnQolNaUTTzopMlbqGSX8Mrl/RR/NuAbRuUtFSzhYdodZidrIbXjJlDMZk7Im8tEhCNrcnPZEHG9zFPoh1j2kC5a2cnWkJB7wVDAKRcVCJ2mFEytEALK+AY4RAlRaiAFOm7tnjSaBSXx9Sew3RpcNL3pbOgw6VbUnUX/qEHRDgejOuqlN1OW8AMmXXdZ9M7V+zxKgM+HGmpO8ElCWkU0mbvSHkIuEV+zp5f/gm8WKRWc23wZQe6iycI6kY+v4wjKuEk1ma7nm7By9u6BFCPBUuAoEQKNZmiP34MUI2HLSNYKxUkE6mpXIGuQV+5lwCXQ7dD+U5e2NVnISRG8hH/aIkJKoUydl5vYWVzVPHcuBT78I15FbD90BAgOzwth5al0JU22A1GradwVqM/8pnYnE5BMqvwwBrJ+d1dELpz7oXl90epkm27QjFQsodwRECkJqHwqjBHXqZb//3fGaUAy+eQq6uBxUWIDG4q3+qlb7X3uZlubC+ZyFuTD73MYApxz/krdf01CS5u+9RieWyEgXCiEF7EwypcmKxiNcfxHnrkpChaWxBkFIZGql0lKh1Qrh2wIxbYzsSpIiIFqvV3Jvhljtc6+qiIYobyYJrhseiAJXZixjoG6QNyxsydBTCR18hg/zqgg9tJFzFzsT5KzNTnW2eGW8WWi7M4CuooSbqoT+0mn/DOzaSI7ZNuW6rpkClyP66ndqd/s6LNqsKMF10UrcJkDEjs57v2ZFRR9n8hDk0Xy2MZUYRPfq/r9hSKU+ImnR+hgxmB5PW6dxSACpwqNK3u0cDFodpRbjyuMQbDo8klyTlaJHaMk26YSWgEZrT9UQL8R5Iekz4aec6zx6vE3eT4A2ItYWBWuKB1XV9jxGq+oQoFHtnwWrUue+cl5Z7FFN1YyY04Dqm2X8xmjcoCfD2QGMKCwjPitJ8dsaJ8SjsUV99B6RdkyEnyGuutDOnsgZxQpaFEpAuQXoccQ2VrauVJUEzqR06Lb/znHpZo2kwH4uzhG8se7QlsXM5SsgjbR77Eom9e/ZVxyaOrE5kI3gRha5ORanYEGYR4lWQiZsLWRS3gnpIeWtX1dE/ku9CJSk7P9mIyPVpKvk9pT7bbbD3Q93Dg72kZzDpTgPojnoZO4zwsin/LA1r4W4Gj3gKidHeI9GjxBTEQypfz3KXo+G2bNOL9vu3VDmv1FYafNgAbzOn4YHK3IazINv3tkT0YrFKMKsbW+ohnFACMeZm5/wkYVT90EMsUyNh90mPIhZqfGoGMoL6YjvgmQixHO+VJhBmh6gFkH0dE0tMp8L+lUFou5VZehFdB2ILONSljxYE5J1PDRfFaI/JXUhNPS+UK7qWTi/SoJJo+8CpZ4HvYlHudquxOiIfL7B4+thN3DxLPeyZgioFEZxWvcWjxJdFAryRQ9cCaQvSwT3JJxxvGRo4Cllc36SzSLbyngFqatBPiR4zD4AegU0JJKn5Wu2ICCCzWTXo2ifghB5lEnNTPIsD8Q/5IfkVs8udqrwzqLttvh7srF0ofj14w1oPqN3E/SppWGaM84njPdKZYOV4OvpnJclhdNzZIJXRUmBxSt6q7OGY3GCZZ8IZpcjfEoI/Ug/ZgUES4hPgfV8dPBhe/HSDlPVAhpktrdhP9XPrjvdVp0AHUeWK4v5rionS1GWXNPu5rAvtRtXxEb+GHP8sfDW0euQxCPSGB46egXayqxjmBYF8ddlzU9Wg8W1lke05XqjcwVTXQcpCQBAbhrDTuOsq3bu3OgbiyPWiFL4vW53TKetWWX2GtmKJRjIq8ZFp8lmvT9uj+oXA2WOVd1zTOACZiNkZiKjBo1OG1Osyz4V8qkrPtiv2DkSv8iYSPVISgd0qpo6gRmsfDQISbTeJVTHw+K4CMYtamu4Z+L348ukilq3nvw3myfEDqWM0OKtdS0W4rYXyotGBfzP3lsktrQ/9vJibFwOC3Ncx/6NRxZ3VXE4iszytMdjzaFVeSx0XhTtS/xePkfaX9/pzpRi2/JZf2waHTIpCEafw7gZ21/X2Sc2AGABmNVJWLdOQVEcYEY8fap9tda/s0yQjKL4TnIfUUR9GIWNmHO1Y/I5goyLduYwN51bHU4I/gIQWI4eTmgLXD5b7fPGdZcYlp/9Xpuw6cyCErJOsxsZJWYhTiq8O1pgOtfkslCpX7Wu9/LTj+85GJ0nXc/7IYap6xX320HC/fhD4AhmFGYr+oyxSd/VtETvTuBickGtcWZGqyU11xfz5f+pmnfmhULmKX9AxW3a+hf4aVDT8goHHVm1zWSOfi2qKvVfjJXMY0oB05N4UTlhJuNv+MVooj6cDWft8W273cukAMK7NwZa0OtDPeeYj5TqEWjfoJIAhWdjMOh2CK47+23U7xl0nlMUuF5v92bjgKzpvS8IUFwz2TIUgY936NvAvghIqzd9+v2x0e1qwa2Rwxx9Z56N3/yV059f/FFLsshj/oGS782Pz0mFt7ResZuaOk7ABXebXFORmQOj5Rw9AB0z7Px2q2cYSmzWcOvDwZv9d0d19if4DR93vSqMp3HsogdW8WJ3+83zw8UbFTWDv9Ab21pLa6+kRckZflHm0mMLBhTE48vh9aT9o92cMKGu263j1wEw9xPKTD0BMqkpuBYyIciNDyJ2oeBIbvwr50QITmkGyCZRjRCb8xCvh5A0ofT/7ltKD6mLdm7PZbdsOeu9OhE++fqYDblgKBet0r61mOTElxU620DeTfRAYzfecoNx+wc7QFp05aJd57w4DPbg+oydJYFxMCEo4KZBVTVVSu2PRZGF2g+CXzmnu2StBKTMGrHmbzX3rq/ONHOXy8Ku8URYJ2ZWKJaMRJfBrwtAM+9FKTTmSisuddG/YrhEbQfFcLPNp/2Kpl2oc8Ow7CsEbI4saB6ia+Kp+uB6AYWgJVJyS9k8/gUNHeDHuPnu3fbec31NbIz71xFILM5AgllayDxmnyjnIiIp3b4+tymwnvDofD4vOtnFZAp+saBpNhdbTQ/acr+AiBCg6rF5qzBlxGhsMzoEJjluIkuZAKUKB5Xd4milF/1H8Cu77oBD0FlvzMjgGa7r5N+hB+py0ZS3KiYz1sCt8XgQVmWFq9Dtt+LVwPcWiw9HR/iTWkyX1/nvqS/nK43+j+gt/+faFa2wl2unyLEivuoL21yncXudctMoYg/VGlW2PP9uVvVsMHx1ss/H9JaSgGRcVB86+g24ogXqDYvf1cVXu4pEuk3NlOjNcBB44vCqMRzf5Z88GbZbnWFbhfKQnXoU5m0xJUYB3MairUZ/zKhozXt4AzoCCwNSCYXJf5RL5aZu9ItGhVFHqcPT3vnYydf705STxnDvlZOv66cpM9bDKKk77likkhh957kb8ZQyk+cx30ge4vhsN3a5lBz+7a4wdUhhlw7kieCIPeEOD9TSCPSCNQVJpY8vx40KwHU/lHyKv9BqZ9puulxKiqNFwwGQFx4Qw3pFw1WAp5vtDrEhemRoHuvJBma8aCFIpnSWpiKIipXLCaiTuZLN4AacHB6jbeqY7WImQZl3f4Eq+NSqbME0ZZhXrQLp9wB9CZJS32O2mNqTIPghs8qBmwTPIMCn0i6zufLCKHYq9MYm2kWecsN4GDoCaU3ad4HLCdkVzRqSPJU4qnmxOy9qOwIzxHhl35Zp+GNqfyaEoOjSBW2Y5sEHNAIffvCpS00HrrcEYycQ3EQqi2VNqI2UfSIldLk0w+K0eStaX/ZnXoAxO6GxjvDHMOU88yl4jBLeQ77t+v5r46k56iqXNnxOd8wKIjffn5iD6NctqPpJO+u2VAqK1vmk2LeQt0d/nkl6DDA/uD1mcja/Kx7M4VA574OpZfK5soNKzHdS5jU7DtYHayPgpPjVcBj2uwaZVSuVaf9M5hebwbNru0kqjI14IE9fJKzOdo+ts+71SNEAjVhh+Cp40C7caXhYAmI+YNTlcxtm+L7yY2yL7EKYEINCX+UGEPCleczh4xV9E779gVOGDQnBKVmYuYYHWnKximHgqZnAlxfzIa3cjKXye1y6k5YtLvtYqoewEGoVeDdsXFw1nixdNprfZ5Uzo8HdS3r2I8IVVSWwx0tAOkdabH6U0cj9kodgLc5tptLS8ozEMzewkJFomaBC2zeSWZiP/pegBPOY7smHVDpmWoSRwx1wAQ9lt3MZ41spkzRlCdaScQjiqzmoJPWs01NF+rgndp7dTnPSifvV13Ib6NYWwYe51BzyA8cgL5NoWKkhFk7hEs66EqtnoIfF3LQW5SEs2k1tQNa3VAkbq2js75FATlEbICW2sHbcyUKxqQC17tmoVBgNx4PhRbcZjCBJaIg6iJfhmxyvEQsZ/vJ2+7aj0//KvELoHgWDMKHMQWbRRVPfLJg+aMH0NrrohfnBDPQrO/1v5HdjVfBdjfmxPM+BT2GCRzjFOTccBeEYGkEP+hXTBoBJtjCBzgyY/t84+jROLlJR7KDUDi055cXydN+YUPJgsS9m4Z88tENh3cODZETL7DzPaBMRNWK6sYS996IMQKFn5WgC+1MyHVzvY6CJQAw94VqYigWnVfSXlLOSkGvIqA/OSb9UcXrJa0nzXnjdfFwjhdCHh1jbXWF21rX0by5ZW7ax7K0RM+AoJccGYcAKRZvVNM+7ER6Dcsz+ewmX8piaysjXpS9TxtldzV6ySkVjL9nwq6Te6BccqxfvrG0Hc6o9Fgw+kE2n7JE56dqQ2mG0rAbZvkaiypsGsp+PL/uYsLWKieRRMcg+kL+QalUYq6ePVlbg+SWkYysr60jpJ8FjAFQKosxrkBQU9ogdNgcPwcPjy85oZf0Nb1dkHVRMdju5luTIMtMkqiCp02gVLeYcUlKvfcs+YQzkEES7a5l6m5WA9FHUeOv5WYawkH2fGklI5mx/UQ5RUZXph4ezZvkawHhv7b811i2QLhDRYSwMRz+r8ncv3+3uvdiv7x5KaEDylAPoI/HLklgjdqX7ldQ3jNDzZqbRmbNnIgOFItDOzCwAYYITCiiYZwOKID+GnIXpU/LFvMHZCx+iOc5CdVjXrFXXTfRNF0XD6uY5/tYij67YEciYzl7Jc+IAkgKHyWADDZW2uS7gVyG/qqf8gKNGs+1Zj+uu+OrDvVc5Eg2NNyWZyf0JheQCeWUWr3hBX2qTNK8KPaMWupFQakNtUahTAaPUNADFB7EMlJ3aSWd0E6JkRyTMRIi/sx8h6RypNo9QlIL536epsLPxvKoVDyhU1oEXtudgJhkvB/YcNNtBCbJM+eeTbPIkCIIVcGWRmdLrWRBL4UQLMhMohsLUAL6Wc/D1Er6WVvWrIvAjS/m485h7BdegZgDE6Gy+eE3jrsKLC5O5ev3N7l7UTTnCoSX6cOplnLk6Z4PZVLyK5T8k1Ay8TdCIaliwk7cCB3UNJWJZjP6KOiOpr8X9mKKC6BTpDFwQdRLSFTvhIpROlHV+7ABZRIEbM4yHiq3xItESlkXg6TGhp2F/71GH5edyU6fLqPt5bJ1UUoVtRIlZDyWD6RqHKtB0MFxphCldPDM9wa87VVrurwu5WksdGuV9WXWZbiWojC7t4nSlzNhNIPkLoGVF8Dz3QZTDrNPNwFGpdDoI5iNS214IziZZQjYNVFHt6BExaRziSht7nh4h4siK4LAEOFiYnw3zyCGMW4WIAQ+Aa1f3wHJGioawzWNCGkQQBz4LiPPlYFIFXwrQ5sJBCXtjipqwylSbY+04feBUOyx4Ti8LgUgOG5CdNqkwcdDzfYYNmwKDUu0rTmFKOR7S5uSNnG2JWJ7Y7XnZDEHt+Vfw9T9/swE+NcPH5Fk9Y+zYOgrYDNO5jCl8PFDaQrIgsQzncegP1SxZPn8IcE2vXxXayQSocaGWBZIb8pMPxx6sCPz8y4hGBLfBijjiEYGFHyW8+FqCPU7K1iJhPeXtiDDULFNxqF4dsFFykYDpYh/vON5RQFhZ0ozz0DDqxRKkzaM1Cygh+M7FXDoIFztTU/sAAcfCTaNtFSHyhBAaHyb0cI+462G33unxEGJ2huAF8oM1JC+nNDZyWovWtEuquFNT53gyLEtgIqGib5h3UPF+1RgrNx07J4B1DlCWnQ/sYn1zZ3vvKMpfc8YIiTURrU6RLC0+xGhefxB9YoYj4xwG1IdUdLB99OFg7+hgc+/wBXY8FCH3kNq29vf2treOjnbfbu9/EPF2EfvBOWYQkDZ7H2lrbWa5aKw/Wh8+YsB7cn3Mz0qPVW9icMijhUrL9cHKgnsfGJjC0ezwFeNhuHgrUlu4kZQbwoVWgi3DeetwEWmFjY1Pm/3rHp9Ea5dap3OSb1xGLUG2ntVPG8pA+04j9kiTgnX5nY14/ap/BirQBB8sMkyYfrBTbc7yqFxymGwVI7eQR8qfjIPSXR9nVxUVdTAzh/iv+jup6hfYK1GWexp1FDS8IhfvlV7v5Gs1K5i7e8zQ5UozcvJ1Yy1YebouSlLdwJtUphtSYuWqG5tJC11yKne4VocreZJR8ycVwJ02pVtJEqYY9RKkhIIRGqEWGR+X6gOCEwK34msOftP/b4OBz4iPKVGcMxmcSNzByduohZLUzN+ES/OLIC8bdxjaMW2Wp+EKarRkscNAsopOc87MKmhY+IJFnr6guaKmdP2TlvJPwUUS704eQ0bsr5a2QuzIUFEotxQOnDFw/8UgIe5SsRAFvvjb2ygyrC2Ckj3Q1BIWSIEM65k8fsmivQgTqA3Yv2FOn+HSEL0H6RygvFPgEGrEeaRCnj6imSFRCzYUm5878bvfOx+ys+2f4eDsHytGRE/DItsZdqUAEmA59lA6Kk/lQ/bkKBsRT2brVCyZPTXqIVlmIzwBWroz23AQMWXznrBXt1LyU0IqgFjvXPT6Q7ZIRpAL5qyvXJrHw2tpcoAWL5RfaDbo5N6HhwBaGhZXTf3EyB0TFZjISF/cw2N6ydihbvBB44DK2JKnU5l58fEumjE/AvsB7JYGYmQrIjHtlF/OL+RGPzI9ASPI1H/vqQ47pG7CazMd+hXtHsaz0kava2DBFFjfKIBUvEhSAG3N0lxs1NQGVvmSY5TCSgD/i/OoXBIsrKHtjrQI6KtjBq1+yC0Tmh07jSEJPMrDtGtEZa2QY4/ZUa0sVHlKRMRVHokFJVopE80uOFJgyB6lfggogxAbYrabR80GJu2co1AKHiStolLAJbGOwsKxEFqNCI0oqXRkaPRNymIi49G4UuahokVnOJ9C9qUgiGfgI9CPeklcpJ1aoKkuXAU1hFxTxS/eqgoGPkTHctMrOO5RlE+nFszsWP8PZJuGuo9ZSJJ1WTNmM14LcjZ/tqaNB0SOzfTDccdhEIB+iEt9ol0NufTLFao7Y+sP8MBHwTEROcB8SIWcZxC9+qAxHLUP2v8c9K9noTHPy9L76oDwj588YaT+uBEVq07aMjOmwVKxOljkxdOAzMDJsenmfBDkmr1LI0cnpFq3eGlpduJ8mKWipg2MKZbyEEto6VWSpl1oEfOEi1mkXcYzYkeaJ2YcMs76/nRliIn352qkkQUGuTIzd72WwUraimb6YM+06WBuJa/i2doJKZDTLADLb/P7lIpWsA1RCeTdT2HWJNKmQfY/KFk1NGpAP+BHWqtUMfBa1Yb0vloQKdAXwP38FdUpTED7B6OwuvNxOJp90QQ2OsuNXRIpToxfybCzO+aa8lZDDt2BhlSBIt/su//jAiGmtCoxuWJDsss2txzNLNu8MuahMjy7gWWXKh5ckif7ufppCpZnnHLATViLk2IlMoE7k4pxzANRK8YVrHpWfIwcwX8Fo+zfzaCU1hfhbyRRMvScmBWp4jvCiFwKCpEcDZdgiFDSvVBMO38Tphvyy0VzlyuhQfHO4iTWY5IXhKGLnhs10HFtdTk4dyKamI7oNKpd6us8umEE1OnQOQAqBohkhC5GI4SugOaatTGnAwXhC+OS2a7v7R/tblEyDzCN61i1sfVg4gWTpyYPPDU+dEP33EDY+UjEmD/IAMBHdR3Rid8C+8TMPUa0yRzubJ63SzR9w1w+XlE/kBbl0n9dO/pgna4YlyL6d7AtPC+vsYXtnMvlovCd62eYNOu87+6j3GXRDmmGYWnElm7XVFMJFYA9ixvGrDEWuj9wQyRbjJhu9NVbOAt0XK3+udDkmOEH4EmDRAyTx3XBfy7bGDSal23MJAfKptbSyvPDwzexdCw7gmujuxEbxxb7HZy0x5e54FTe+o43sGrUonilmbu+JRMdw8oTWcFgEWMaTLwzboy+1zutqryEWhk2NDpYZkKrQc6DmHlViFWMglv9etiVOyS67Kjf/N4WKXeoPOL2nAcGYE/C7gGK6bf0AC5B6dNzrrgnUTm/hVUM5CMG8UHFiZmWalbSBPcG1olQ6Px9AFicg23Ag4Caio7zq3nbYpXA1aiiwuv0CqMWElc57h5OCmmtnE8gfo9i7+hsrI8YHWSTOGIM29n1RR0YMzZh1z0IqRL18DK8CGVXUsMOvShgEgy/VPwT0SAPfpY1OBJWb5bCmMvI1lf17ge1wkyYyxdiAbNawClIkBNPs63OzXpccFOpICQTzs5qjCn11qhmXwC7KK/OhYKZ6QTm5FSegZuHbO0e1Te3jnaPRc49dSjY2FtuK5szU70+GIhh5KMzKIxqEnJHji+DVhC2HDPe8fn+1oe323tH9YP9/SOOZjbi2B1Z2sBZNiBZAtjxczn00w0e49JVrrmUOVLYmRO6fZ0UZa1hH9MVSP9SRsbHw86PbAcSGoyg/oztbSrlVvPlCbCFT5PUW8QuQmsg+PGtrIccsWl8qXBRZb0ODKs4DmyWTYDVqg47mZvf2R9skw28SZWi6wKI04xUiSTLZl5leEGNdxtjdXhaQ1AlYPgND725xE/+fTChe9o1KpuULL90bykgNDuwsug7iwmbu92zC4PaVuNq6QsP+YQmGsdEvmdVQ7vXoupXRR+Dx43BoPrqRaM57g/vUD24qZJMSM36Y1r4xSqWz2Cmym7jrn89LjLpgeeHAfkhee9UyyR4Ww3qhzQ874CfT+i6dqTU5jkg9OuDDs8TQiRXCC6wcYngKOOLyfxplgOTIiNoeN4nFcN/iRDPAFt9ICF1BGv9el3R0sDCKk1ay4ihjegxYbUeybwuPzRnGFOmhk7ya8kUwq0RrIyzGEQS4eLl84oRVTlX9E1DcxjQQzzCJtUrQwU2L8bYWcfv+blET8kiWth5V5LhDz6BeALl/lCeiNm+bjPcWiIWTwCAJDOHwnnCUs8KIm8CsAPNdo+EPwHOewl2EHZYPT1Nwc8s/Q400NSHq/5D53+kclw/mXiDsqqJ1P5iCD3dEfZju84Yq1zlZH+QPqHgkY0erdcnS6dQMfucmacXiyXx8x4+UaMoH9UbDr+reGuh2jL46am6FnpsDd8ahT0FxTjtQAkz5/1GT51926gJsW52RWn4MMOIqP41XgsBsibUIOATDnfFxUdUDRCrn4uO2iAnFx47ssUDDDdxUFq8UnizZiJpie0yPFIgQZBvwcZ55ZpvZ+uoXch7uFzTLVM/f1F8xKQD29RPB6vE4KuaT57wZ3ioMWmcNdVDnAs0je7gsnHWHlvKR50rIdxh8BQx4OZNimgGUi3gJOWk/dFqSoVLX/A94Z+zmPC0YWJ4mDDOYQIdESazoTPh4lEmt+p+nhoDxC1vZl2ZBV4vZEVELPbKwpFeh2+7aQzrreurAWXOnmASbVxnXGWiZWsKpWySepPfxLvU3IRdh7ZP3nY5fYUjQqBz/iO1p+OrAS7aPwvzJLU5qrkFgYTAbWLquAwZ+JH0PgBJzDFQ/KVFzABCcN5djINFNuuv1nkxBuI7u9jt11mlh7v7e5lUXNjeNFVUAYFXEaDIBZLCCPBWv3feudgfmHsqAitlkbQe0WTygUhbUm+7cd1jg95pdGcChyqFbgFhXH1zddksoUVCeYIYexUAcPJGYzjqzwP4jcgta1/5E5yoZtqyWG63AYeGhEymT2IbUhiPBxmheGAUMS4Ji1Reunna+0qONCM8gFp3IyInRK6abg6beV9quAKu2lQ/nUvMzo/AWgkPxNNaSdtdLyEEae1QQlBQhH+C1ggV6uM6G/wguZ6D4/Fxs17NMTGtmasK/4FE/G94k1RZxdmUHHyuHx4d7O7toM0S9t2U/Y8CUVkNVY/VBQtU1X/CbnhkHnzcLFTRbgFfi1U4VR83S/CXHxdLccQzWrkdrCA2LhLhlUbrqtMjJdAaaQJYi9emnD3gYKN5ywE1Fb1CI9Ip/SZwHCr1eXw+8Aek6wAvkJhg4/maQphRf3Wme+FVQ8E3wCHl9J2aKwTAAQCRxr/4uOzXfKC4X8MBdGem5ps0nxfYN6EEAAeNXqt/tXcNSnqO5U8JHvT5fd2+G4l34GZSTiBnkDwvIhgWs1EhT7Qiv1kyHpRToRnQqwgDizq+CMQ0b6L6NYbNy85Nu446YklUjBD79o/xsNEcRxfRvEyNvHHw/qlGG/dfW9DzBcI1tfCtfnYGgQ6I9aUz2KRWhmkpFAa49/G1QNFTvYexAybUnSRq3sIidCc90iJgZ0+QYUQtpmxhRmcIUh2MBZfjqy4K4WDjxy9Z+e2s37rDL6PxHZppITtaoDRo4HCNwru4H6lBGDSa38X3+MsgKpdtSBZHVNFCzvszMDemm2rMAOh4eP0nUDH7ch8sEi4pYa4Fj7vBbQgbBu/qAfAPB8MPPHwZ/KSBOYG9xJ1vbGrhzG8TTiShD8HTxLgz7rbXk4VcYWmvP156AQman2YBV1sLexLsgRI0NaojgdL+O5m5dcLoBQbrQkapLs/g7NI58sSBv6WOXEwVhu3neRIjML/kCkfvecZc0RFonKsJ29TQflF6cbzj3fS/b75XTDvWU8hF6qLZeTZsq5CZdqvfZIuzUGqPGmcmfXGIoQ9ROiIYq1+Z3wb0tDJl4IWWjVwgZqsca2TeIDhcmH4N+pmDS0HQL8/9qy1Rt28JuYlqu4iQYH1tF6WcAexCU0YQ+ALsLXlfTE8zqWgcjaWjgw/bqhNUrQpG1C4kzJ/hfs8uZPbaE6gpZBNF/Cex6hEGdpXcA7hNxAjchHe4opJpELSUg0LjS22oaRsLQVzLlScxxq3fb7heYJvTZ58mFKE05TOGKKp5FHR0kiNB7CRAEHu+x57vcTdsjQbNLXvVKtYd5Zfd5c3IrYufaONFUjVRJGW2ioYvMEa6MO9YekroSgo+qoCoquCTC5HTbCeeVjceBam6JL913J0bK3W5Lfmw0nVeVpPVqVaUuc24pl8/PPiOXYQoLFqvtQOkW7frqrYfohS12s5H0doB/oJtQ4EwLl4Ql4tQO+8T8S3aHStHjDeDZTFmTHAWuDp4TfMSnLXG1VvGwvdvRyueX/TihhDsgRe841XafoZ1lSVQLCZS19aZ8P+tz5Z8IhZLI4ujhGj2XybO9nRchospsZogQUHrqIibpx+53OaVTM0kUebDCU796CiUYpxWIhkiX9Y949RzEDdao4gt6nukF1o0ohFdBpDVAmdeVttVe3zZF/4tEGEw6I/GnM9qM3mc89d8L15dd8edQWM4Rl/gFXDGgsvrIlvxslUeJoP2MOYPTJeKpouKaE3/O50dU/I/pukhvFGwhZrZE0OTEbc9wPlAZarSZyFitKWXDHx+obfi0Qd2vt/e+tF8v205jEUH2RtWw198sba/OUxnjgjmL9T3UK1wqC3UDMqb5kJdDzFmc1Msu9KQGo8+wGuAxGaMYtkdt4eNcV+KMEYARJSc2OEP1RuDQddtAXCmaLWiK9wqKhq6ogiOW8S9HPFGFxYugq8zirp7ghxPVBBrjQTy//3NpJFF80Jhpmz/AJExgUskGSU0OrwHHlZ/4BnRuRFQI4TIaTEAxskPKYCo/6jbmA/hq6UjGi0iHru4nIXqWYscpoUfD73bRCXz+PKIs4G5nTSSQao2J537v5UIHWbGJaMla65pI4acT5sy/C+qMoo8neYlMlqsKk6okL1C4fv3dhLCtCdrzkUmRse18v/QO55obrAIUeqbW+Q3GIL7P9F612lvZ9jSFYszhQCS5Nj6ou6SP2EhggVzaAL0F/2OYwTnxkrkGOCy3xmQRaCV1FytfinqdN7Kzjxw/RMmox9SPrlBDBxFKANGJCXTFD1yGKITTDvzYdvJEZnM7CDW9uEawpMOUz63ou4BzTP1KPqoIucI0XUSKgJlmSUurVRdMoobCuHXKdJFH/4zKZHc0L+CVMhX0EjzBxHuIBFv2ZjtYbJAhe7APseDoVZIxSF+8HmhND7+Iop3+50PUJAsQEdcNUKL7cibKK+LwAFVRV0kI1U57IuFFm2sUvc64374YvuCHoRJpMiALJHSl8rL4J9bbh0Tbob4GEaQpWUN2CSNqdAK6S4DCMbo5ypGtiHHbIcg008DpQsIUSnIG4nZBi4bUizVIEy4bpCjh9IsmnQtWsw1iQUVRKWN5hpBV6l/qyJ1gQ0R4lRUY2ekOoeKhAphASawPNYKzawn3O5FrsygbcQx6QDoEWDlMFuY1DWX09TuHOpClcygogODjowK5stLC4jLszYRl6gQGNHP607TpsOWg8FczN0jBCO++IHmeq07dYDNxIRLRWSUps5TkLxuoEe+enAd9X4zsVEkE+60pYZYPw5QqjNkPGpy5Hbg5BtcQ4wsEJ6iYU2V4IaJeKszYtzHXR2p4Uha0HNc1xpFJPmx8IgRv8Hz5mW7+X0LvIaeXQyEl5qMhQNTSftsyob5caN70efkUQR/uu+Gq12qLsUb1210Ap7qq1Om/vujaC7RljJBOCQmjXAos2mCprArk6asODvuAF3fSA92PuxfDZS5UNd3QLWaWR6Z5vtK2i9OJYIJGGmgLlbDBL+02ozP7N8llbwcCDiTqWUicAwS6/YZTBaPjhRYv0IfXGGkSYF8h/Z/UuINsz6wq2Ri1NDPdcHat1gTMcmEZFOa67kD85D52RlwX3HdcwcWkAoAzKTG/evmJV8YlocPpwiMaMh4XdjHVxzG3n4zn3KxMqeB5vHz/VHMoKWE8+w9iW2E0iRDq2owNolMMsOkORg+rx3c1uiqpOlzhBc13AYlNGVCxFKsmCg4UOwWbHTpabCA3BeSA9yeCIQuWJL7M3NfZocZLJqczJulK/xX1nHf1of9sz4aZ5OhA8TTfLCbgIhQh3ZJeb9ql8QqedNNrjeUV6OAsIN+GXXaTJS/aQ+DB6j1MuCdkWZDOI1M9DBDwWR72GmFuIOd81jla210feYQJucrrXOBFYdtAf1pZAwh97xCKVJrHbO0+BFq4BnOHxGxDOFHbBUwNRBPa3AqlrxkKnnydUN5Cjw2EkRp+EIQeu67hEgzlIZPvFDahHQ2Cf3kRaA2zAvkith1bWPkCGEL+rA/Rredsb4gFtIAwpJoNpCgQnUku8K3J4HmS7EARq6pfjJ4kDM2zd/13TTfJo0Ycn5p1cWFhKZ0EUBms3TuF4v+on8OBzL4uHmwt7u3w0tlyDdrFXl1fibQKhZ1SF5BB6QrICAdBlE7WBg9QiqGBiZ4DdKY/5/MocBWGvikTvrfJ15S0ScuYtXW+QFH2HflmTG4i+rTKRTtqjFY+Fnorp1Wc3GdI+Ld5VfzAqeBc3Tnsz0DnmM6bNqtXI96z713EqbLX3JmRSfcMyeYJie//FrUayUYpznf/4kvjkXiamXTYMYf0DqCV4PYdxV/luSss9iL0yRx9CHxaZVy26H2evhPILkBdkDQth51hOsFQSFlAKgDd2huWhPL9ikkSr8cts+riIFM8CHs2zppj55mG+tPz4brMZ2J5IQTGKiCF52LPphrdH0I5Kf9rOnpfq4s6WHtjxsdHFy55yW2tB5aLIGiZgygPHlRasQIr86v5oORTuM2N+xgwwQhR+axMAM0jlyi3KQOXmaf+E6Gj5/u/PGwaslcDaGz+qXh0+1gtiILRChKtBFA1EVvddVSftEzeUt/iBsuWgv7AGUXRPu4DK8RuryoqTYiLBEeMW+iOGxEzMgiZp340yxGPrjh8Rbcje56Mdril+uVG8oC994Il3LATISGnY8e2vwKOR0VCH3hBDTQhEf9ib8aVBBWNwdHm/CZOFgR3kM4oYvO+eTb4IL9a19MUP3R7CfV4qamoZcwE2IMGAxMuOAOnV2ITzDla4J0zJUW0kqhlyScbZngNn6aMpkP3PDCSmRiUQicT11+RXhGEF8N0xJI6Jp7M+ZHNo134GebGVwCOICddb2AaIR+xV88EYmhR8tmEdItAD/F0ahxASs3WXtIvKhrJ0Tn4Zgd4fR7+VPc28nI4UJDhkxDueCc/0cYYCkQDWfQJNACrTo0/8LDbb/XFrKHKrESUMSUkZ5zQcsAb/xqWGy1rUQz4TxkJzj7pSmLWI8gee6Jndr3HutcEB6hFubIta2RFhelO17SOfOcsmAO1E+SsNhjUZEguaMmow8wpN1G7+Ka1nE1xiY3xkc8NEvKchM2Q7jOqd8N0g846o1sKdKEIsImevnKHw6nOok7rI7WlLK3Gbiu5ugWEbjQL5ZDjuo6LEgDVUnop565L2IEQS0c918fgzUpggjgByVrQCrfj05IGd4ZUVivXGksahpftq/a9XH7agCqh+uB8w0O1lLLmVAkuEUMTAzBvxAnBTUma0ijAUiPXbrsT24aSYqDR1Miz5YOxyCcgOMfYzwOk5ywy7Q6tTV6JxpQChW3eY8Nl4CFZF/lGIGR+LYai8kjit1udAGTpcv63+x22hDdLh+2YmJi3BTA9sY0JvesiBOAQIGkoSEpIsohGYrlaFaFx7i6EomBVkhP10z0bwGEBvj5twIDjT3jukw2j3vDCx1N2VH1BDMrWhOTDUd1yTdZZS6vGx1JJIF5nspwCPxFDHQhzesquS3pqWSIKMVxEyFcwjQeYgajmEzpBoEzhV/0xIjz/Y3iERypS7ub5CsT3R8AalowmQkiKfVu/6Jfv7juKKnP2Fdc/+DcgEC5zruSXJm0NG10NYIN13DUAgkT2JA7+cF01Ik2QgOwqkNGBCMTcomyHEAFh9dnV50Q3JLcmUArIXsV5/KVmuCq8YNr9nBhAOYG3YCMe0hhNU8YEWnMKDLaFAmYnrBuTtdIaz2tUVNiS2g7q8b4z3WOO0/WW+obysreLAUdji1QLRpMNpZsJNkoshFM0uKTMxrOqug4jEJSssuPxXh3xEvCJ56TCyx6ORGnAfJMNYaDRLr93NRwi6nygAnBxpx8TZxqqBV6SZ0wxKFexAf5ZHnZwA2xjxBQEliYMIOF0jbCKLiRWxMK/CkR+w+SbR8PGYzK4tX7Ar7erTzSvKds/4+ImA9BKkwyiFNInKAUSJOpeAY1C7lppM83pw+8sXnB285IK6Ypsnbe7D/bfHOISyAuysWlnZFnSiFKFg1P76CPvOLMfYE4GptKiai/BzjbzZGg1Ac/VT0Ry6qnQvnr9D4/rdKfIGQfoaDbgsDbmdZCD8jK0ZVQTzidSmSzf/31V5X9Y+s7mPKtBaTtEeOaxne4ajmc+Eldmz+eHYMswa1Om2tYi9gCJaMl1HSdxOXzdSRUMmWOZk0RJWKybmuwvMDWBhcRxtEroD8wQjpXdXAeA0xaQtmBrreqBpnRuGa//x26UcSpN3DQdReEGKOafcCvzHApg47L4RVRc+sJsR3LYuBdm50s1CiU1EaoDT+Hnd1047IKFamRl0T7rrI2z2AAElA3tj70CiR3fP86qkro3SI/At1I9lfQgmf96V+BLtHQWprNfUekW+MyHlrYgMrVpGJiVuT9X1m9IRSISG1G4dEvOSijWnzO4Ys1Q8zthjluG6cp4wrx1LgvpvpkCij3IoEi5gpz7AhrhP9stqq6odMngFbSeC1rwhT0Fb3WE1HYIVX4TA2fqbdBZ5mrhqYbn6fmDCbBZN5T5DLhXCq6Z0TBXDq244VjUVDP/ZDfZBhOZQv8V1Yg2nrY7z4xHPJpiBmlvGerrAZh8QEbtVOhU/h9uyZXukTHrixisRSbwKKTHE6xYLguO3zeNmrRYUohWIYsB7fJspcNG7eTVv+2BxZqVL4CjQxqneokmxQvsd1ArTS0KqiOmlxYszKDufh+s6pEiJiRgkJxR4iZ6FeifWaC30a2DG+ehc15Ua6sob2wkBXISijgLmT6rzrK0LDhOWtsnghq5eymQRrj6ofY+bGMFSNokjENB9+ycBUJyXHVQMJ9ZGepEdQmcTtA7rb+7Z/r9vAuaap3cf/pzK+ygWtn1GV/NBaTJ/dCnJOGKXpUViro/zA1SCYQjlOLJt7zkEP1mYAsTg2BbMiamySWQ4r6gUR24D4WOjxU4ANFi9sF8YMsIUWCefTMiaSM7hdsxdXHfZVECvpoxFDH/tocfQ+CUbD8qt2+aY8441cgnbjku1CF+5LtklNzyh4P26Prrhg99DZHvzxaeE3ONYlbVnxn81KvS9J6WaVleSkSSiWcrxsu5Z1l1kL7r8EuKpcXYv2kbWsNZhG5kSBz0Tk3Xd6KCCsJGlRiroG7DkT6FXwt90oqcq+kpExzIYpTNaSJNQBKNxY4WAKAiLu7GxqUxzD/hPamy6FM+F5EWX3lTo9wSiODpSN9zENOMRoIXwyErUkDTNEs9yPUzxHb4HTytUq7cuo47ywLqh9EqtxCJNhKoGCEhGsnWyoQHkNRaD0TyxsInkqmAv+Eu9fQxs0LM2k4s0yCzibhGcnOqCTPOxJwx+43kK/sQMRqJeRqRhjRrGQOCZGx7AgAIkHTIz9IwBQBqnOvcaScRGNSC0GupyqDyaks4PBkk6h5CHWEPuwwp9IiuYYVT4niTWv0bkFtMf2PoG4cv9GBi/5wdmyj9m+6hD/AAa1IQI6r+fC0hDiiON8Q8eCr8StS7+H0xVBPpbVLfsWsmP+OYm80nQf1Am2wRcxh8qHX+XHUuWq/aYzG262OOBIMQwRV+Lbdu95s9mmxO4uRCO3r7l3RzvK0Bg4RRPdVf4vzd9w5lJqJB2RF5MmW9CY0dphUKklLFrnMCoxH4IUHmjYPu0tOaAvMgkVbZnnQOJ3IZxm5oh153FNo09OMPP74SYvghgDzRgySizop6+Hu8wcQpUBopxH3MO9ZAEKGH6wzNyS0yIV36HJphw7bnspzHeEXs5BrrKnl8c6bycYUdE8+2OJrKlKLKCzo5TL4xY7VirjPvnuBS77gg4meVMVfiyp2uu6psf6FMG2OS30qfs+J0X6IJ/S8qt1u0PZTc0K19Yy+jBDg3tDy+hYLlLMPTm7ueqxrRVspUIhWueLaIlbEK+bSXpGOXceTuiqVfUvTH7Q+PKTwFP9DL2lqNOrlDUCkaHcQLdF7RpzeZl7Lh52+yk3FZivpXF4AA2PWe06U27KlJSlIBK0/55lhvdWp5OGpNSbuvBtg3B9fDq+JMySpj30aPZ/IJEkTaSLinCS4+Ex/p8ko2P7nbxG0U5m6FEyIlYlx/PM2qTBbxkQmWND3n8TE3oz94QAFiE4w46aKiG9ZKYoN6WLzYkFATAY4jksFrnbV80KXTQWF3EnI9pRmQouxHWTsV2LdBVFwm7JMGyR/ltjvIPghmwks0lRVCfe4jassrJ5TwwpVWBVsmggQlOzVmqZQstwuHH4e4Usg754sVNJxCZAM9WYC01FYFTEZpP2zkZQz9+US41DROVV8x6eLORGYFmwulI9o1r0FCJKTOVGGCUJl9JVH56uD9j/X7dEYE1oeNwABFFSyg2G/dd0cx9O5dBw2MTjmTILJAqVpt1OGS/INIJsTzyKZXKOdgUCNHJsoKqZQGja4ZWMtWCA6dRZDqiFFFglKMV8S8xp9WOIq5r0QNsnkYk0hV43RHJg1XcuI0IplXWnmJhoTttaWg0lHwY+EMxGnLI0RYg96Pga0/MwJiiB5ZhleVnOitpm2HofZcN7oe8rBp1gSTuJM9IEHivjAH88WqGye89MGinWBVLTiRRpvFsG0pOw0RSSKwl/IEnYisoL8AVB7YzmhjGlkvoiEgMjcr8Jo5ecIjrYjnGbtlT803z6uCCdPTtnMtcAw7eG4/gWoTJqZWggfs9zNlGeG0MpSt4Fmlz3Tw46zPoLJ0XigJN9UWvsdY1kKB0HzUD8upwquu8EkUkTaXqougeuPL1QMsKyrMFCsxiAzE5vfIn4ynZYyP+Ffw1my5IU07qqBntZA6YKSlwpiZY1I6bPbkNv8TKTxa1ZNBkJ6xjCWoFXFzBwJzMmh+wlSAq0lViJIcOMr5CxKsysN+DjD/k3XROIezQMkzSkuuys76ocNdhnRRzaJga69KfIxlPtQrSghxdLOR8S3CHtdVVGh0NmPSio+U9KB1aDaCaHUBDIn+lAw4RGCCMgVpQR5DF4nCD1RVUPYOBv1u9fjNlxmxy+7Cj7O9g1AlW82uugwAg00MapjWtVsXRoprGRz4eyICO+KMjImUbqiYBuQMNGE4xWEPSKQ7GuaLUx6C54LmNB+0yEY/Q59rJrt04z5v8JSKcFcPztKFPxRNkcpm3WDqytk9SWBq349Pl9ZBWsah+yXy09z0ZTXDA+9jFiBwGRz5C1Sj9/nubu1UJLLC6dBwppk5MghjNFE5GJTxJ3tBA1J1lzuA1SUE5yiXlpYDubNHUyc8MolUBsZ2V1EfC0/P9OPxeXBN9d9D5gMFHuE+hTtjNzxkAiu6BlPLkcdcnpGmW8TIkXIZSIaNCEISbyIw7UagvEUQ83OFxpmp2giiSBJVrHAAZteJLArfyGwK8VawsGwcOI2p2AiR4U3w1fpC7QEwiElC957WA7hBepYM1Itz0qSHPV+yKQxK99vUiX8LZbzwsWb5A/0huyMnvVRSmL/cS2nFhkjvQ3R7H2w/WL7YPsgzhcXBbk8UqWFPyWlk1ToTdILka0NEtBIPn981h8bXt/stzq6A8oIJpcoP9B5QsGR6V+KqFfAfunxJO1ea6u5d311BpRQUWBu2cdoN80an0A7fFLTpRAUlF+MkBirmvv+V7Heoj2QLbklHk9zczkSuXiI3UwYrAKhOc2M8uTnFSVhZLvmarQCSipTffT33xq/i06ZS7Ft4AKe9GKW7pcPAp4w+Zx9wsgFz8+N2ZZRLJBJYQIHTacCYdrTNSmoc1Xt1P0OMdWYscsrUaSC6Wb7vX3H3kqRVAGXGV0afLIhcI6NfFdA93cfmjQBL6FvBtuXAt8nYpaFm8w0y+jTuD9sS4daZUmX4c1UMqbbUnQiLxsO8KJICPvfTTkBmqk7yWg4pLHzRqerSnPQEnggeZ+nyOliuSKYnyiFiLZYHm7ac8U6zHwiFkvPipSwxSPCiMrNZWhDZI1yxfD9IciZsTPUNmBEpHMu5QBEfVrVXfo3OP6IClkxTmMtS7NmHqd4GqMk11Oskpaq7JYJwJUXhTvBfOV5JtoFhOWI70kl6clOkqLKw1iSRAidSsBSIZEx5FekMkQP6pD7z10gMa8AzAas1t51t8tHpSBcPHVeBMhEhh6u4V5BnsRIC+vocGhxJh37PobEUMCkmWs3c1/OCVg57uCEbcmSUx0XLBAViEdlh7e2IlDQcIFMfYj6gW3yG9MaaEwOKbIM6xVb13UADKx3O1dISlsp17JP2LywYoLNpYiNIuVFiLvjMxfPxDWXfNE2sgdYyRQXawGGjOrOtKIdIqE7mocWMSxJk5JoK1YXqlrt6VUZdx0+WsHCJwTBhYcPzpaqRk3S7G0UR37b6H53rEYmRHMdCw2uXR2PhHQaaKkPhARMCgo2TtUJ64juDEgaDl/3hnbLRyYGXZKQ5+4lEqJS/Ek3OIm8tGBtVNmpUZslfyC2i18AAeQxGzh54CzwCn3fyt5THWFkw2oolt7JLRiPVENEKzrKgx0e1k4WSqMwlomaVxoCjxuBskHwo1RU9rjHmFY8b8TLxYPHHk+mGaOn8RjxK45wkJmjmFxTuJJq40UcHdH+OCH+HIFQfE+FpxjZuOJ8TUifb6ltktV76n0ocItvdnI0qtPUhblqYdVHvmDGBbkZeaN93mh+eiMWi5/P/ZmMQJGweRqbZ9FEqWpGUhiikMk/2w7eaTjpKg7IhzkICwHwXMFo2VUMg/77w1bmvgSKhHJRd8qqSFx9xfcJ7DM28+vCcqc08HQ66GeHOjTM82LG1pCkkE26pl+Rr87q75ZtpSMxP0NuE1MiVYjaZnA4CaleRZ185FuRXKzi8Fal9EDFSKGAu4kLS5MrViLG1XFOI+o8ucBpVkJwlHzelQBuljU7VHk4oiQTqkJzS6+ZHj5igY6iDWIurW+EtzgXGAICk1bVhK7oUoPrvkg1s3CFaveWCLGl4kYEZWvtqsXWWSPJvf5+w9PJxp8nVWfI1iwSGMzzM66BGKthYQXi5CzlPGF0dqGMtHuasyyYRMnZS6iGiqQZmlb1p8w4GXC6eHzGiBRBQZ8GjBW2fIVp0ZYQY6WSs/RHWmPgLXZkekKFhIrTzWqIrUwtEa5KOS+zEVu7qhpWaIploTsESrbOCU+2aMCDq1JJKzXcAjFGwv3gFrA2Gq06wmwPR0L/JfrOlWVoLMb21ceNC7Q7XbXa40an2+YI3kqBV8pRjhmIDFRWgjzaCNCdIkJZoOzVMNtUqtu/EK6yEi04XJaVsrIbOKuMSISwf37O1YDcghWEXLyVxxK451MnyYKFrB4EOwU6bNfWB3aFrBqMCX7RaXdbIyEBUPiTaezefS6pJtS1sr7ZAg7W08TJ3piLKqwsVLVOvQzkSahB33PdlGR2qc43m4dH9e2Dg/0DToLwjCwUF9DjREq6YpksdVo+N1R2WsoDWepZLYUv4Dgl1/QmcuODsUQprqwsMudGhXjM9PvTsOVdYSI0sQXUs4lK5FRI1ePh1sHuuyN8297m220R9Y/JAqgwzxsA71oJkEWGIiIBgV51Wj2a5PNQ4WE4szJHaJGQqO7ImBc3QByUMkROOBLExTsQp8Qveq449dnIssCx968HA5mMIimdDMK+6C54F7tShbYyOx0lO2I41wi7MvlbtWrECaFSfL9iAMg/KLQncGWxmckxklSg8b4OJ9xfeF2G32J//PW/PfUr1GdfWDEAS+IOdkBgMI1CryZWgcsVLZ7NLGfbMryWc5v5nHJajcORbHA5Xl4SyYjEhMi2xdfcECHgWsiVCppaZwuNWvJNDvA2YYa3lXnSwsXRisPaPs6RB/KMRwSVQskQLR8Z+BNzxBcpzpnL1bFewlaQRVT5RA7mvcDcpo9+HUBjbpv+6NvCL4sCGQsc5MsyXygywXdFUYCPKRZcsAsxxVLE0mT8RG8acdtkJbQizhM0SjCX6JFrYSM8K8bOsfQSb9zSPD8yad7QzcDSAWgJa1sLpvoBS0g3RtTOAww5cqCNQxYOdG2vrqyAA5x2UvPGiS8y7hOeiz09W4+pwBHZzDJX6StfcfA59yUsFGJDTOBSGwSTAXwt5+DrJXwtrepXY4o4AX8BWnO3O0Xj4+eL11svvn/5+GVwdnX8/fVW69nRi1cv3n84+PLJe3V81BXuU/KQhYMchtNd4dZV92p36+Lyy87xVeNjsftl68KugRBcDKRGC4NyYQ2S1PMs7JLq3JdYhY6aXfJDtjihinn38h07TD8QhzVEWxlqI+D+UuwFCAutpXF/iUqA37y5Lfl8I6oKBDfJqLV49iS3Umms/Nxc+VIPVhAkxCvmpiK8D9HqRtnOWbff/M7+YBS95H0IqMSplDVBJCRoDEdqY7w0rGxpxAhODraPD7YP6+ypRLvVbzI2slBqjxpnQaLbuGlrPi9rduV0ldqDVjXPpZtS4PHx5qXQ1sl9tRTbfx0T8hyNneJfxSRs9pZwEy/1m4zNbrdia6HxRf8KyNOy4VDYsrrPXQl1Q9fTDyv+0OsShVIOW0n4rImA0A+DFvppiuzgDenrFXu3eXj4cf/gOex2tDZIeOKUoQ3hbAHik5uuNzbQF1uuYrkZkF2idShM+SopvcbNKP1q89I3dLCum6DLokiEUhFOBik1nQ8UPTflR+1QQzAOius2bLa4EH3pGEwhPXL1IA5Wck3E61mla+tUFb2gwqmmy7Z/1hg3zvr9K2XN4PCQWYEPqVjAKW9xPqwJ25hzDm9EHcSkSAL/0+nCh6WwKIkTE96vBt3kD13RSjQD+rmap4ySqyGzuU5hxdAGejSEffpu1zffvEEfz+f7bzd39xhNhQ4WkKr20FwNUndyTaaiRVEzLS+rxQPiLs6w+crohlFX0FHPc4cVhAbfK8HoY7ZA3h5LkkXJgdcU9HRnSo+NP7sC/vJm2lzPHPmEkECLUKMRuwypBd2acTnJFo9PXcyLnRtt2ZcZK35cdfXUwfJFGXZDC4IKpIP745P9nDBeKBdN4epvrJqCgJJzeF57TFKSUiCGX8KvtSToLKcYI4wp6YT6KjpYR0XMKIBFLepANoZUeJEJqx6CIW5lIdckE5Pnr5rYdM5wKIQIS2XbytmpLu9Z9g9pNDSygRknYV5CPYZ4GeFAB68wRWaDlGTuKzyYmifIpPE5l81Tz1rrmpUxVq9M82rOA2JR5fVFgakFYHHoG4ug2K/Puh3MSfgY0qkHLjuNLKOpw+t1zB563TRxRXUEvDkReKDzgg3FX+uSuexNSYpSCxDL6DoaxHImrNQ5f/rEUt9n4rElNvlFRAGXmnx08KkLe3P/WmXlFq59EetCojo6NHEnpJWKM7mGvbKcnnI9ryxXPYF9yO8X01Opn4th+sFgXloFM5ivhEAVqxoF5nlwUkErdRKkghVGwbOnqHFUW8lapDLeNkieUoCN6ixiN1Q0bjRlcoWxLEQgsi2X5nJNVIQMR5zIy7APxhqlYzHl8lMi3AI9XCvVP6uPxo3hWLOxKAcIzSuRTd7gWoC6sIcQTrDbbvSEwYInV0ddEKjXMiJWeXajJVS95tfnoIeFcMhQwqGt4FyFDGwIhAxmGtyYQNNoZdvxtAb9ZbwNj4I8m5OTzvD0pNc+PWn+c3rSvT49ue6cnrSGp3ORx2z+daTpDONMds/Wc/9BxB2YLMirA8kEKLdO+2JydjWwcuqUMBrfN/KdJESkBKsnnxfmnSiYhgciBUgqZBk0bfhMLXZDvOw3wRZnZP6RhCKMG1wqRB4m2jg4Ykq5w190ARtO0UZVtawTDs23WFblsF3UHeX+5eNe/+zu2VXj44/uJ7/Vbb2ofDtjrOeXj8Vc8+6i03h5kGsKnyo7l0OJ4v0rDgFX064LkQFTgMfDSqQAIHLhXrtFjFVJCbnxp40lzLSHNmFkbTPxdf0HZNuLS7qu+2pjFYkYjFYssJW+PIEXfsOUW9SfCpepNsykA0Kphe88DU4kFozmPGAV4beTUorHFxB2gBHw9MvhZMKQz/jO+mkKEmbwJTVhRwQrcJ/nMHy8ABeaaYyBjxGzWJRSDNvmCW7FxXMQJQQVXGxJLUKLImUXvazbBOtFFcOj4XrgLr6h6QMFOAVbo8Tlg1zjcafgEmIQFFf1I47bXtjB0GYje8NWBMbaIEUviugpEZIph4QSqbCzWHAWfC00G91uHSEvEQvUQLhcCLvtYU9oanNFoBFQoFIOtW1+bQ98J38dOlrrJ2L4kNPwfKXhiK3G9GnKTsOhkw/EDPD9aJje+RbLkNwQxkyIiJcQNHPjd/AnrnuRiFc2TS4SlnveZZjVHOqMm8L2G+MHuLLb4e6uEb9XdkaPRCRkAJ9/MR4P8BKLsLZQz9B8D8ED2stCKZToDWyWDeA8EgwZPT3XpG14iv0MEuzERP4PqwGg/ISU9WL2CyR+2WWj15KJUJBD5O/W3oI13AYiJFoqVYznTR1GTJyC0rYILWu1zzu9dqvOWFnpUcPVXYhYgOD9oEBT0Ah1RkJgz4B8wJrE7UC8veFkWKxYIgZgwU+CIAv/TyBOSJJ9hUXq5wLUTwobCZO2Wv2rRgdcQGnRC4WQqNw0Pglp1C4rQiYIA0nrFmImIMbhoqn4HlV/KUh7kaR0hlC3sG1/BoT6H24VjZgnvMd+x8RjmlotcVXbTFry5ZkbfB7UI2+7L3z9wm0PHyOSpobfPc+cPKs51BI8knNGaJiJAf8gZVW0X8eaFgxZIkwHK7GK4NYIbYP06rnphgzSXFZBmssUky87ljYVzaQ1WezJXKFQ4DGcBhpA+BnjkC1R9vSZMfZzPHznHMPm7YV8GUxsTFeGPz7phF4713BtaGWkSs+rahwCI5AIXeah/0sVMk4nLts/WlYsmJkSJRNxJ+XxGCd5GLgCTcQMlDXv0XnIgA9M+KBvgAj8oYW5PdFuY/WQbi7k1zOXToJVvd/4tPfzy6f3/VcvKocHxy+OD473Pny8Q35GAnDFwo0Q70blnOfbGSW5Hw7uctjuMl6Sa2Mo4vJeBGBFlsOkhmrE0AKBG4zWHmI04MYJeXWQQHvTad/q8qwMPWJjA1IJ2VcsWR/UMt/6HcmPmSyqjnbo2hWWM70cMgpY0eg2wj+sVoxNH42G5gmgUEmUpLsmFF0jM7SwGS6DSpJMKmV/McgbFEL7XESoCmhScrM1diJuxRSbI/EF9iAEsvoL77nAMnrpzcUzxMs5plRGDKfUlArLjn0TZXrD8zbGIVw9xoxPn2bPrsfjfm9dIrnSywkg1MBQSIVJ5e2g/n3UHtW5hj0tLvYEBrHGN2NBSnsTIQ81aILRBOSjwlYDiwhU1IIdN52Kc+pg67yDCKf6YHZaSBKUFjn1cSo9i5cmpAjPDKTneg3NHVpeU2k/+NrDi/yLQKjQo7ud6ArUCE6NCCDCiGJV82iaaCKEKafjW3Tek7+NK2uCwV/TOTwHx8NZnMctbQJlH1AGrRSclkfrQCeb2GSm0J0g5y4BLiC+KDTp8OMWLzCjAtH45H1pijA+YRh1qzLQFt/7jnVlgdrZrxT5pjmCVYYHMYhBW1WB5vNIKbpmowJzQxIPcqE3MDio3ory1Aa970h2inLu1ru97yvrZ9edbqtO9xPSRQ96+yh43Hnd699+7l/r6wv0mkeMWQ9ckZqxjBQfE0/BRDUKAFAgSXKl+AVVsJ8nwdd1tojw0nqQzIy0GFkvCIZMKH7a6txgSB537hH9IAiOp1m4HTz2lQZGtk7f3YgzgdCRYQB44fIDYqi5mBEqwkSnMNjAYaN3ARWkZQyDcRuPHYxelzsQA3+De8cd8UpyryaOJEGh64/PGr1ee3gFttpklREZEN4hCOKmMdTnZYQyPRFei+94jOrvepNR1Iv+8E5q2fnoEJz2qjo3lhJCe5nkGhFyQw8eN5LVuLoZFw6dIlF2YkO3HfkQ18woPYfjO03bV5KPqlXuicOOKjp2BamqtjpDVKZo9mw262YNawhASYxebqqnJ4Ja8ubbxNry+Nanzhe4JePpVRvOwxRoQlbAjeumGhu2z4ft0SUrn+IKjWqMAjJSbAKqRMWQH8AVPUr1e2/6jVY1Nu58Hze+436K8ZlFo1e+MktKivTad6S7Cz28gN43XJNb/yarU9mWFpWnhTUiMDltxLyo5G1KhJb71A5buAb9sckV/lpZh3U/bF/1UZFmxvxgCT6hZREypqFqBm45s9nvmnk7tHut9nnjujsGXXy98a3xgxdDMvELYuyDRNxZGYOokyg1AWynMl2kYmDpmQRBrlhGF+bzJLAheKYLv+ZcsaS8mzvo0lyBy2WPfXpFDy434WshjzfzcKGP97DO0rnm9UyQFflo7bqQV4wT3ZUdPHBlQnHk1jaNI7pVn68zhJ0wxQXOlHHJIC5EA1KpdBnduhKyreG+Gu/0Wu0fqPkMtVV37uR6n/qHg10D6yNrufAqNxGN8iJExKruGmWdpjBXL+BjGz42xU8NTcJA1osppkaOiB9CdAmNCCmZphGmQcMyGASgGuJuIzGjppjOghgKJCfXazRCMoSi1XkeU6rsy6nZTNGqxIEPyEvcJVlS3QWxbDXjNXKpWosCFSaPPHEqIJNIEV3kcLj+ZHeLAsRw5uHwy4APpmp3RhYV6WayeKTNRjQygNMjirAf/Oik2otj28+6R5qrTEq6FHDMWL0lZaF6cuRR4hk81wzfZDOVwMnX6WnKUNspPv9eZvy8l+7VxiigSSs3kx0AkijSu4QH13Wazzrh1fybrge/UOcMVbrsH8oehiu2kY0vZmT026g9JHFOKPHK6cw77mwdD6xEWR7ALULhgYdwPCzkjvnGHI1Yl3OEmlQ0NDia6K/x4tEILQnUPiYDww/G5X0YN33ueNKZcEHgo/UXMB5c6KhokRc07xeNiYefRsbQxjljJsmYWkfrqpU4lMYApS6v8CekLpGdIFL4MgqISZBpJiJj1UMqSHnHwvCGFQT5TkS0SBo+5AmiXfEhDY4YgbxALY2ModOwCIBjwy5xGSfI1JIpA1yJn9au7MmRFci2kE7RzGALD4kFmrTS2GICQiuTrRknQ6+0ELqgzub1cAhqCWTwmo2erG/YbrRUHUlynlO2SF/uNk0a1jLhmomhOdU5+fro6fp//g5gmU4QwLkoAgBHAmWTrQ1DpoRnoWsYtKPlVMSvvmyhOvKmNIbo32JE4s0zbkBdr3Ze3DW7o9tPRzk0aJTUosGdzScIXVxWizPiog0PdP18E1pNQ0VXNRV2IucNWxOkKysIIDkt8FpTfnEVmW6GFKpS41rE96StqowwbRrPaDdY4xlhSHLgFdOvskwwFTBWkUjVRPzTGBbowytnTNC755XbxsvNva2r42+NndWL3e5Bv/Hp7UXTvxy0tp7lz/Kvhs277c7+be41hoDudH82/ePc662D84Pv3bcHx3tnu1dfBmc7x9efP3rd3W5u8BpfDs7PRTbMBPoKvrE46WuEal7OkfRn28xS2gnL/rwbdm5W2dWXfWAhUgds1RoxlUVhINMeu2hcwMpM0UkMV2AYp2pB63QSxT/IbSEs99xuxllTyaraPKquY4SZ03hJ/EkX1cGtnb3afUcp07WgTEgVhhstp4GumU+YKEdCkAtjH2Wi1sQvBLPo9YaNpRs1O0m6Z962/HmNu2KaCKWCkBht04DdBqdqYtFuoZ+Tjduo1eUAbwwo1iPClIHhF+Q1KbnHhUEDdALsSUheS4EgGGK3J5teLQ/mNfMF8eheg3TEScbjvsyBiMcuI36FX1k1LYMJXMaKtUoidyi9A3Ao/g4Uxp6X0AokzRJs+n1aRNUA503FlMUNVlCn70kqj1dZ1WuyahjD0W1nzCczg8cPQa5bjjohR7zIebEnBz/4iZtl/z2uyWbh2UsDBxwI+P5B5IgkNA/YbfhSLvyXEamhVMHaiNqwwdvgpEQ22/qe1i47qiRv/VUjAwYTPxd0Z+Kzh3KFC6XmARH2sipNA0tNpNgvm0lIsRl/DJ6fnu7eqV+shpB31ygahpJvOQWG0AaVZIaSUhvJqGdv5pPIp12uJVYAGPWcEiaH3EvYcANFScoX4NIvote3eIUpNauQHHYbRFUORAwW1uD2VA8tCW5DYSU6IqHb9d/c+SNyorlX6ijF8JLEoE2J2Ex6cELgCSh74opEmSTZL3G7r3H8emTsahazhBAUYI02VnU4gcssLyDWuhoNRk1N2GOsHvEp8obyWCYPtcSeiSmHTToXPXbMEBlvnPX1ZtzSIqnJRQAYg7xelwQslhY7ZthyF3nS1/ggtdC4cK9C0O25s80h8gDGOWleXvVb0YXNLMw4OaNuu63op6PA7aWmGeGmiNCJ74eDxh0R9nJnewJTG2g8uJcEj1udUeOsq05EYT/BgO0LqAWeCJXiXCorCBnM2JAVibmE60/P4KRH/pLtatgwQEWmgsXUTwS9Kxg0brgZiagUbHtFsBGWBBuQ38M9pfvVbq5NHA/z1bt1KFYvf8cJbW5Cz/awXacofJIkBDXw9D+wi8INOwkSadaW5Kksrk+XzdtpD1ocr7wcWU6OF6kOck9iLldsnZijZUMjxWHdgEa9pfQ8aIzG7bNOL8g0+1fZYeOW9T8paJQSSgmXBO19BtX9P7sMKYZ9NVrIDmwjLK0GRWH8QrpSkmdfLQmB9mWNZc1AAMZqYfoQnF33FXxn1BmbDH8Y66ckXB7Mjp58/X8U2JouEXLliDvCKeQ77gmHHy+GffDtHzP6ybjDYPSxP2y9o3RPjcGg22k2UHVEr0TepBwaW003hHHqDj3TAePauyHlEhDsAvqjFQo5Q3jAoo8NdsaOsjE2seD9a2r/SuLOjT9OptZoEpep7vXDRrx9Suvfvowmo8CYF8RiKYenBXMELjIvKfzYgFyLjQvQB7zq96+6jSBz2Bm3+UxUBEpm1EzwuCxKwKlNRLaGSsR283v/ejxptK46vUkwgSHLT7M1YzH+ioiMUmAE58fdlCPFek2EFIOZpwATF3Wat+ISuOTCCk1YcxXMKYIpyJ2rguM1OPdtuLT0QT06+LAduutgZjQUBFJ3r6wzMum284XowgwYPJLCEIjGX521SwX9dQ2bbCzxrnlCngnHlOHAetrOdHTUjkwxRQx9a8tOuIwavvGstkTzziGz6GVBFnIZOcsIdqNnRZTevvyxhXPg6T0jL6fw4pPdRA5enx7G1RskgDY64tT4BVekIB8MdjRmRHOafZSMTIo560FUp8Bx3IEny/aTrochWIYe1gZGvBapKMcrys8eAcf5KG5hMjJMvQQjgAzKakktZ7Dq8YQrZtADYK8Kxc/EmKKJRILg0Am1X6Fw+JBqtLxEPVSSTE1OMF38Qh1Bv2XOIGt9iLD2L5hSU2uedVryXGnyAyVCHZvWkSrZtol6fBXYzAdi6PjgpunYr9zsB8VbKV1+cu1vOsWB7Vfv5IaMlohzoEMdrRlrBj1AM7+QMxaugyjAybxHTlWbaS61qRTglOZMOlxWYS48zuVSM7Pg14Ax0myLVIx0e2VEv/HLM467UIITO2NYUsryajHU3BlARLqm6LKzi/H8C66lp522yI9itFWxyDFiovWKKnYcMw6MTBi6MkH0FEx0IMGv6Wzv1DA0h3DgDOcQaTizQsdcYQ3iAEoTQpnPfYq0KUTgHq+Yi2qiTNXKHjiJMVEmQvjTuCkp/6V5y+/hQf0Mjc1iUHkcj9FIT8S+mOvMBUHehiMpk42FTeYCMRPGIq3Bi3kUnZ6GZNPSw01tHsQD8nO+reFkqwCTY9YPTCO6nSxZ9bIs9dxCKAzb9DmOkbTfE5YamQy18ULs2lLOuCbGEJ8LMkpW1RwnjI6ps1p17GTpVAHjSugcWnkCltEy4hCMfOecFV0/pSTuQjoKHp/sel1qvyyvDK0t05vALE3DoV3Jkr4vHVkHeS2qByyz+VpofxYoJVNh5v7ECTPD4Fnb2KxkMfw2oxZ5XM4WLvI4LXLHZbiRkVWkNadPd2l0+xKRu3qRVV4kuYZOoPD9qbgHWkV+00YrLheky7joL9C04EehPNlJ8sXK1pcSJWkVyOHAHiTMnmvbe3bPk2tnjFR9Z63a2jzcJoywJ8G9E54k4agkLSBUNcchVxMo4k68i/6j3pNjQ8HO3WtNfEEyhKzV6K3HhUEuhaj+P0T+Zv1v9EB364BedbyfngjuuVLgF98oEaw3anIZGUSbFw5TXlShFFbnDBWeYWyR/MjlVoIf5RdAWdN+kQ03lhGoPVeNTld1QmAhFox08Pw3a3b6Xy0Yt0vGjaKGq3OZg0VVZhIKOV9Vcv3WcvAZiYjPOYzrmpxNs4qYe87ndU6fZTHJoswqggWFJpcSQ5aRCoxI3xEh7jpsZibvHoiQQOhKXpxOsl1NIpvbP5rtAXInjyEw554cqtWRskaYJDME77R9Ry1c7BOhVOVC6ekjgma54ho96JKW/mdDQyrlxQNP+CdaJq0iJewoPPnFOHmrV9rh5dBJ/QLQx0PeIXWQmvbB7i4qISqrhkQhjWz0pYZWUC5bcrX6BP7y5NfJIAGQfiDA1aEXrc5wMmqct+tXrCfJjAigZJsCGiChdYNFBWFXaldkBkB56Ls6hRYOwE9MhCDWkkFYN69YGm7vLEjLtZXZdoHGamyVY8ZFvl3DV1Z/wp0YpIxwW/5qSEAT0U2JmmuXSUOUbZSNTKvrSBUjV0OEYmOi66kmSg8iqSZVk0Qc4YdNOkl+vpT8uLyWDEjXXpq6NjAmqgyRDYeE4XB1dQ6i5sXsmacJYYeFZ8UJShmVi3fR5R5oYm+ojfQSm6N/4CvNlwUZfHzhhR+qwxipssCxsH0sXMDLyMdmkhmy1Ade+0+YB6xDLnT+hFXCsvGrgs18kGJkAb1eQnt70lLrGXTDpZ1GDYleG/kXyypCcqk5unjC0wAXjAHOa6NjmF1nHCtyqCoCXyJqR4wuG9IJ55cgW2XL3Ide1dokfiGkH7KP3j/xbqeDjRHOYi+z0NiVSINT/nMKZ6RnNQsE5/2RBwA4re4rLzd4/fHY//xRmugZrxdWpWlqZUllS+QxautSrPPSkIdMA48Qhu1R0geJa+s9seATNbtObgcyDTmu1LlmymSubE5g5XqUsLsNTCQPTxYyTvmQJis65kaRdS0/vWOlIIoLbk5fJAucam8PNMMtQcvwGUGmp2hCZCLDhvzWhP9FGOcJz0Ei/hK2Mx9iOxgCBQGF3PyjO/oxafWb8O/HZNA65zjOF5NB72LSafYno3+6k5+dgbKACi5Ejh0yM5Wck54+yGdzQb+52SfBzPt69+ftcEFPyXVE/rT2e6gOXlJ/hb3bijwGOOEC1Nd0aDAmUGWSkwslHxmnMyicxYqa8lcQK2OuZ+K3RB/5SECaGPK91cbefI3Bu/xfnSf3TBB4jaDRmt9jyOMw4MyzNwEnPBV5YEWMQamccvjSs1LPiHeUHqFORVHEZRVFb4aMUmXaCI7vBgK7FB4dYVYdPjxsWV3rN4U/JRZcJkkLEaf4ffL2Igg3HWU3BfzLSXBa3XgUpOpE6ut1SGJ8X5+ur7Mfa7xFlC+1XiXdBj7FSgYp/gjNGVzeWKkTXCF8h1odjCjhucEhmyA1ODms6oRwMotkJomFIjlx9aE0kfDuL35Ouv2LybeRIpG4IW0bPIG/FULmvrl0XTYSXX9+s5nf21p0nj6UZfIhneEL5JacORHXoPAkozlbW2Kdkex7Q9BA7uWEqEfG/o0+NvWBJgCEkHQiiCwuxEqlQvB0gi1wyy6ZWkh6CW5rGd3DSanpQaszTd4Xcunyqq0Y44YfMeoWISIUuRxFFJCSw1BtOJH2ocIvn17dneVfnTevjm/Z31zjY7FHpKLEs6JziVIzoCWE8oRejcd3LuTn41JIG0L0Rs0dLetwqDN5+JzOjXHHa57rqKhpbzQ9LT27ztMflXLhbsnVEFoMGP5hhGrIE1B2ipPrjSSMc0I7ACEr+ql8Ca+RkneHhCLAYoGXc+MzrN0s3+ALScVsXWlrxkAnof4DU7+qUBUDS67T5SLN3UBzHSkT/EHZ0KQlf2eRFRZdZChzF138iJv1cYWAGkC3SEb0nLwalsvch9cCdAwgMuOLIZWdUGvcjnP98unL5dnWZefzp73u3reD8y87x9/O/IMuX0tFkRVL1WBvdJKoSw6rNf0f2BzU2XbOkzXpQbMw+jQxJm79QtRDwrfdn4YzbXghv0suuEK/EoEWIjcJ670n3PFNiLBg/VRuWip0IinXrI2Zw1/0ZSJ33yoZEoomuk09t38aojamp4k0eYFP2Sp3ndDPEcNvFhDldy96nXF7yN+Lx2NYEznL7Me5UwQeSpfIDjUNO4Tg4+xfeqNmJh78VbOismEp5xQF4cyJAQHP5Ssm75ukcRAHMOM19jCI+EX/utfSrnJ19nJWc0gmulTLUjiZ9MmzDwLy95DPEUNI1XnUMlL4+6GWhVefQ4+taa+VUluuUlI252D9W8Ys5VJtqkxIuDJ8qhdzdg8yfOUUBELmnFACwwokfEiU4iSqsG8axdnsi4fmBEB5GvcTXVo7o/LijFqT+xcHxw9c/sQZ+4qpo82Z3IS9zYsCpE/sTs4ZW7mrrD2AjDGwv4IVRtaX8cJJjabrlphV6YHo0NK5crtCPUZ6LL7tkL0TSV457yQDbpJEfEo53DqPQaoMdDy8rQ/sCk1zY/ii0+62ROALR7uiaMV8kXYLldx9Lkk7VLiyvtlqQVNlDdhb9iQ4aZRzmk+81n0UgIszsCSi6alxdqcsuFoeJVXhKXDoJ8ezgPq4r4jIMBSvrtNfWCbkY8mjZaS9hrNXolnLwFupc2S+w7t2ymicERAXeTgTxl4pdDiz1p9o4ogLa5ne3xmk+SRC75JL3IeA91vWt1FrXlLqESjcbYzG8Eu0rlmpjC65Rl47DCMbTc6MRacPhBUsIiGqFNaHgliLUD5o1kzdnqmg0AzVSrh9HKAvJF/Y5EZrrKR84MV1AiFb5IWg/yBLpvS2zUB81ZRGXysUTXTmDWvFc6OszGq2g4IKdynIO+vDNPAVLWATJP1sjOzKk2F05wCy9wgzUT5QAWyONwu3ytCrXcxhgW9aragcoaAo5jlyrAhtASUiYuo1pXWYbidch5ASshBzLxg5BXyglTZnhsiBmPDwKUoc8HogwPgFIF6XXACvSxa8K4ibVcyJA0pyifKalTVJz4NLbx0gXzu9i0wmwwp4ZvI3ahOe+QXvz7Upk8ITaam69LYxvgwy591+n1EQ+jFs9Fp98ENgJGd5CYNGaDyX2GOtfvP6CtIBZXhKotjTztXF0mjYrMZjS0FqqQsfsbjInvw0O2oOO4Pxg3rNpT3Zfzy+cUE8pdrYzIEeshpDmMlvjZsGXcdh7vYFy6SAMI25CLeMH2CILZgH5Kzn7KRujvvDu12E8QTXFAHnyX4ctLHz272LTq+9tN9Tl541UK+cZaJ+Vl3d6qMz4H/uBdqnyJAIK/PRSmvhkueq5ME16ny1dmXYbK3gbn6DKrJ//+XB10CgdRstqXVa1eAx8d+IkogYpWq4b1nZ/m2QGfdZq9V0YSJFOVWNlZ+bK19yK5WVTH0pJbd3LabPHb2CwBDLzv1BBZfcywXXrXgjovRmLvr9i277qtPrNAYdilNu9C6uuw3GgV1B5PK3Ed/M2sJ+u320uQRp+1bYyO0eV2MH2y8Otg9fxpa29veOGHNXjXlrHw7eVGcTA1fOxzJCIVKITlTvtDHlrDKMLP8qx5f/hlEOCFH46WnK6IajSequ3Pm8Htr/nKuIC+hK0n2ZtWJl0JfVnJS3jS2EyNeAMaWvw9R/7jERIoSv1Dd32CCyZZhqNEeMChnLMKUtw8fIXaXYFkgfVPM5Pzil93oirt313uX574Wlzt6w2WsN+50WvuzxrO3A2uFqhi9c0ec1Y4k3YnNra/vd0XSJVu4Na1/mtsE2ze1VN5ho4qF26weskiD146qL+K0ne1vp/YPgVI2a3OaObj6iZTRauWq3Oo0VSH0J0kUKqtGJimPkOfY55a6xOy7Ri57unr/tt9jTS1f9Vn1ItbFttj6TrOpDwrbW9sH2wZReDy9PiMUXZDR6tEBvxfNXo047mPQH7WGDWBWrt7MJY9Z1NoglodqjF6shsCHQSHzb+8PNNNHsp1kxPnzDILy8H14w+ujok3HCaCajnEA2WYUwKBlcD49Rvkjx158N6d0pPCvoTRgJ6Fqb+CrGcTkHlM9GwIV0JiFm2FSI/QEDCZB6+wdJCs5KpqzxyWDQvzyecdMcpGWjSmLd/MVKPdve2d1jfz/mDp6/Y+T1kH3/Sy4o9kNfUpxK4cL6y+rSMnbpL/vMVpdEHwOuqLTnPeCcMh2KD3+qpT2lRkKuEf6Ly19g2n4TcLvXX9oKgZ/s3/YeyO5SbUQDh8r5EtpUR8tzhigg2+z+QIBkBKkX/W63f3t4d/WGI+nbI6gTLI3pcdBEDpMWtHA1nqxkkRaFFjqc4eCWG00EQjQnrYjPUvDY52ub964+aAwbVyNY538PGhdtSHlRhVQf+LaKGJ5fPA9SDX4SwBZi3XGSzeSDCCd51ASHbAeMQgQUsQjBSvTLW/78NrTlEdoPbchWhwO7x4Hd5Qd26jZYCZAugKX57uek0zvvTwa3SVoy2CQSro0+w2kpHWidWzh0/grCo8k5GbGNoG6PjMdf6AUk66FUPou+0vpOmeubH0mOEyH1aCVDApbWvCWdV06FeWWtVQUV3CC3ZGhDpt5ed8ed4077Fn6glAL4bqntH+3m1s4uZm4Adnmz1XqJ+WJBJdS86KwIAWo5g3I7E475a4H+5xkPnc2+QIDalaP21QD03awvoWv0SEk4biQ0VWeSTotf9CgPXMalkYallBKUkJhNaog0RSr5sAtcO9v3VcXuA5GT4R6H4yFEc2bOh/2rrcvGELP+ohoIA8NhMXKrnmBs6YWG6LFK+HCg8oRsOqAOsZQhSl4mS802ky3gZ8Lh9sgxWzwmGJGTTSCUpcD9BZrggsVFYSbD8JdF92kk4t0BOgiWhlRjQi3Udqp6JN6rWn7RHvNmj57dHTUu9jCtDrbBFw2n0UAEmJLQf///h+TJqp8T9hq5NJfR+QCVh+sRQyRiHQE7rEBR6lyFqtrGzjOzjyekwOT+bmVAk2GPyTeIusHjjCsNgwTd7VRZ26CfWIVXDPBFtysra6A3xARN4v4tanGlphQ2IjpVq0ugafT5uyg505RHVtJl0QYo2EC/RHbf3FW+DAV3m5EA6KMBlAWg0/IU6rFRU7p7hD27V2XR/+L0flUoPomihJD0a/RyMmWazrKUzqjVOT+vX39v34kVxG+wl2IKKzYJSXK/1rJnJU3PZGlbr1XXKaAZnVacRTSPqlVfilDZgE3nSCKoL43GjeG43gS3p1F9xDhAAgZIkpmdN5Rfr580R6cqkmlNIBrAR5LHfiOSxX0gICyiH+UGeMccYXQI+XCZngGaL29ejDvYNYJpFtLn9QdjESHfSokAecChTAVT4+lHolGdFndt1aripSlYcBXB5PIFC/wAXcpKwiBlJTxLznSKWHPVwBbfIs+kfq34Ro1H1Xp0NcnhIh0/JDx2QlwYGS5ybFIpcjVZozSaydpalEtsnFrwBL9iquHUmsPzddUnFBnPAQgUP4njbm22L7qDIWSw8eEuz4bpDgfXOg9d41fjWe5BldVSgmpe9Ey2oMyJwulJ5TbCUWFrkj2tOYvQ8uag6KsEXQcZPAnpB0EuDPMcGHDbI8qdh8lP2cuBCMsRDh6PQPDghJlR4f+cpmo8T30KMoKBU0FL8yFWb6ImkP20rGE81/tn39ocGv2QSby7+3usm/U6zy4GnT2JwQDGdIiURwuVX1lnm4dcfO5FmthAqvzYN8lCvWKn7KG4yvjVfu95A+XHkCJ0zrOgboXTL2U/iFnBamJkBCUmx2BAiJ+bzZkiM1TS3suzrdV/zq5+DJovu1dnH/eG7cP+zeudbuezP+40r175r7cOfjY+Hd+1d17dfT68vXj1svv99eH38lYvV3Vk+l5FdDsjy6bTwy3WFmj9Wy++f8komI33g8pz9i9GO7QmjBuriCZXsXFyJZsazmsGRNF4yccvg7Or4++vt6Tvie56IhDEV/Mk7ZXnRr6GfWlT0pc2wveSFblqjy8RrlYUGfRHY2U3DserhDwKVxH1zPfM1E4h3gi5xHc7F3fvvv343vj0+aKZPyie7Xyo7Pae3TT9bq7xsXK933l2+bm3d3P28jj35ShpjIx0U+GJTcJDzAOLdA8P5Zoe9Rxn/kJvMo9xwk6rhCzBYcfo0W+GQBtn4r3wOIgApK5ZE2pBKbns3YLquUM3jCgEs3XKWcuf75FNBwzirfmQAyWRzf7111/VKvtgs/qXDk0h4D8QxMNkqJRLj1xOQVJjXm8dd5OLVZF4kNNDYtF2Wbh0U7UvNSAFOWIejRICvoST7cxCVhz1h8M76T7oQqYMTK9ayWSffK2CcJKuCBCgr0GCpBXP3OiQAsdMBOoO1JH+x57eCuok9a8ksIw0WqkPv4TYQJi/KATjTJDJUujMSNPJx9Nayka1b2Ex5YKvuSD5998kBIyurxZyKU6SU86cgpYnsYk1q20bTsNR0ZH3nNFxvxpPbnpinZrR5MbW5U4kU52ekQIk5I8YCm9VxAMGuH5NuXlBWRnhy08ptSPolVb+emC/4DTgoPq/TL5EzOuq9F0RxTVU/BDO8ypBwVXyzulhPDzmY7YprwvoLmr4JaMpgcdm1kVyMfESdkpnfaVGBLKuFnLisHLDp5PTVyulkGF0D1iJOARSZo7LBEb2kGiGggDWzvuSZLj8jMitBSkSWDma/eve2FWYn8BekKLAsKjI+SBTg3od/IDPUWFkXVNtZJkkaxzxBGRXzocwNSW6iXAA5YuJMwsmRqMOjA616xE0wqT/xBlKQ27vmnOdEOzE2tUDoSxJjnDwnKEVYUw4BwNhrL4kk2sLU9ejXJrV76QX/mUmgDLv2Nt01R3y6bhtD0ReyGLOrazmkBPRBFpMPtWTURGu0fH+dmynwLZkqy6irki06uiH07OrNha2czyIA3PjXGzUFNMqwp2iMD9DzIWIA6AtNe5foyOj6SNdCzlJ86oikmnNAhHl8VDjPgSb0X5a4ZrgW8ShkWKT2EuhvegcoKI4CI0jS++FsBW93T56uf9cjrxCpdBObJMTcxAreIYzJJed87FxsIjZdDziihcJRqeYQBJGRkY5REifzkAbgTkqOTUC35NRCiIOOBjNcAq/aI+bty1RUYQ8pAkVNdNU4FxboZfIAOdwfdaOiESWUo1zBGFbgk+NeNkaCj3fBGAjD4KWsQ/i3CibW0yQXSutdOBgDkkXSzFJmGgrDzCkU02PYFdDHaHEBa77OovqbggmoUDoYuJbbRdu/NC8o6VG/kFpDIwBomRMtke9BRDxCzxvYO40J0s9D5uYsjhB3Nts3i0KbgssotIE6lJJzMI81vdiiCtFBEB0Pp+BTPQnYJOiY7ID4tDsyOxo/nyWNmkq9pHjRaKmfIXJBY+0X7xec4QMpCIcK0QW9CHaOzJ3biiqcT6WDVfLEv8fBVMTzfcbINkLvRKnASqUitMlxylpt8R3TCbJnYR8WJyx8x4hT6SfNo+gL2QAZGUmwYRK4QGC93rX3a540cnXb3o8duDicRKY2xRzJTHqdNLtyDOZz2G718IGzfoeOhF4Fqy59/QdRciI+VVXIliVJQmCzxVKNleomhlxpAbsvhZI34QQXoqAPiAEzBqAdAtmX+V2pYYR2nMlHIAGkM+gg53+D8D90cO/DvLnzQb5U/pCyYwRJKLn21AsSXGOnQLqvgQHmJ05I4MiUwjmLKFXhJs3c19cTReLq9NAICsaSGJmxhAB+U2aeoAy1gmoCpCR2w1kSIoqpi6iza0IkVBTOS8mlJWdQkEeNY4MfLrkDio9dXAnkTekV5gJY+c9A99Js09giBDBwjrQbnVQeAWmOwMfaHaOCBht7w320OiMkONFLaKWRvh/fDF7CwBPrhLEYaFg+BQslMV7d6d7tbt1cSFA2na3Ds7f5yrvDnPdFx/vxEFczJnrU2lOa+s8hurRygqEVKRzKxVON1dW1rntk4MYhkIVf4GxcnDM1ADn0cbzk8iE7L8zn1GjqHFOLnlaMq9E5QV/wdMwOQS9IrFTNmKHNljsATzqeZKAWco+0ftFuUc3FsM8DWBiwRwrTsrgTCCrSYFRAnGJWClGgzEbVmC5OUFvXIhldThK2Tuv0eo9q+OkhZFeJrCczAm21ES+bLNTNFT0mTEp+QjJTyyPCMuVXaeeVIsGhSCk56WQD2F/BgqfK7TAfyX1IsBaNW4nrf5tD8RX3KPcHbU6ySYt9lOX9wNn9kmNkxWFqL8y4sVCKHFT+z/SWicrFzlImqU3gvrIncDuih6C4VoorohU2Ks/b8uqmiZYPAmriFbyf2MOcVzm9PIPHYqJmj0HgYAOn31QIvwjAig8vo8BCBZjD4IfmRSHILnPpE6n6OeD/i5rU5LaEAOx4tDVOqA2OQiTIgS2YcQuyi+BRzdn/wkP0UgUMEOyNr28knOVV89FKugo5YNvPTHn7NCJ3gJMx/zzSi4mjjHEzs5Jq9NOIpvGGMEpz47j2hVlARMzC8E2eq/+qlHTYVK3W4YsUMEF3nguVzRl83hQY+idGoODpg/nBEg6as+eA3XgEE5uh1kT9OFs5Nq9GxJED7bf7h9t1zefPz9QjETGdZDRKBACogNVcCFm9dXOwc3u1mYFmNSD3DEgCX8/u2u19sldqQ/33x3lLl753dyru275+fv+9y87lZ+NnePRGXdfKgqHqSh+FhtK8IcASfr0cOtg990RO1PecJczTvGPn0m3s3Wab+CBA56qnMP2YX7nZO35sD94wVbdHmFfin00umle9kfjIMNYJN6cjxDQ8Ry8/3T1FeKRFAiXI0sN0t7J7g/R7uYVNaa77KmsK9rRuCwAj+hY+V8g4fxhcd3aeIivmM/NRYp8IAnQTth/ZxT/hWQGswaOcxs5kSlcH0FkNUpzE/TNQ3sz+DxlJc1z83TgzqYKiXuN3OkccEcmRnws4ox0y3xxmkyrOyMD36moIPuK0j1BGc99ac5X3x1HTVkLo9JHRbOJ8C2t006HT58kSwJLJnQIGq8tykhnp3lipsXcdVZHmgMWq8kSb6OXxcJLddbLRNtnG7uN5kiKXtKyL/1v01rqHZEpZBeibmjTWy07I1neYmzlPtq05bnMKs7yxT7KMgbovHMhtcimQKunKVPCjLQ2yDIjdCjht8jfsZWyJkwT57yw44h765848vzmObvvlMFQwrbReD3N+kLAnqAmBABmtF/MtsLYp8Pifm8chAvOBw6qvybwpLl5Nma+RzjRFjTDUxRq+clXs6hyGrO0BmonUP8rgiUwZxlnUAih+qpR7NysZRNIc4xIehsxq8Ei0xqdDFtBNeamOBwqzodYzrSJpBV4oYHBYSBUTgSPftwZaM4D/MARDO5E869IOrhd2IvJSXAqNDc8bQJfeVeMVeyN+2I9Et6kvWSbEOwBlCXarwyLUG52XZ9APfF40EBiLRmEDAJzJS2oHsT87CRYFpiDVK9vBFrq4KW5fd1ZQHcJQJjSNXGsJIyycMoKZwAvjXEtddgFjXHaKPdnfrGXKXeBaSB91xQ6afxUM7oo/wDjMvcP4OcH4nwy2hzG+QzREOHPUxHuPLNdUUK3Df/VqBI37aEIxIvYNTxd2gxQbEt5GRb0QwK+7p6q+CKMQZpgqrZAAtTDNuCrqeDEbUzN9tXELaM5a2qNfQBLEclw6crvU7PzMgFKSaPFM/aWK9tBNOO4SmlTQ+lr/ifypBHsXqSEoXWuiIPx7WX349Vg5zx38+L5rnfxrNF9uSinQtilAAAQ2i+sZG6ClvykvS7bw/ZFRx577FVXjQ7ZCCc6QUuLs1l3h9PxY3S/BRnEZir33U4OcvYZ7Wr2+987mh7F0y3vbnUoP3DI8wq08pGpU2ZwQaeWn0lov0euQntHaUxyhC3h7Zutbgej5YXwYzBUNttGpxH5NUe0FJ+R2Oca+5fA/PJkwCUwU1/zbE+JgwQ7w10SMo7tzFbPu+eV28bLzb2tzubF7tbB98anweDs20RdXr15t7U9TpJRnGtllL7KcAIjhFJoSRahg4guFHLCl3c5K8ekLGCkZ5TUJvy8c6fPNVvFbBkboKQ4ICNh1EYsUgNVWAxJDBobGowY7+/F8/f9mze5H+8ObnOvMW5RpdOOOXqM4KEhxGfrZW75lYdFomONXXegaTlTQZaoAIJvLpridE4QA+5iinpY+Jlf1mUvpLYOCZ2E9rlasbW+qWQ0LK9h1FDnbpaOpDxHVLZbpz8lGPbzL89bZ+P3t9u7x+dfDkv/+IPtw+5WvvulsHfnvf/8uvTPgFiVojjriCMUCdt9tmhWAbyY+oKneKkYCjv4PUXabGPFbPus7WysnROaUfNBejJPb4H0PZ51niN6KGL/GeOSMmdIp2J8iha1ytEc5dNaqJDjfK3Q+boaPT249muLDghxeoVcKK1VgrI9REwMiXhBwEE09LD8VSni6VgaoZw1fjhnjZPpNDpfFg4mUZ1nK6U7APhZtTyNAABSqkuYpZkmsDoWq9eNBWpnw4VgRTwHdW4eHhBxjJjNLHCr77DDviwSNfYyN4yeOZgzY/Cdo2/Yg4VaEMwyEzy+HdRx+ipyW832k1dN1dzQE1otnPfQDKDqJU4/edNJ3ufPc5ZG5WaYWczifEj4RyuMiA0Huplk1JeWACoVPMKpJyUNSSqIheqXnVb1sLjCRxo10oaSGi3lwcg2qsHPZC2Jeue1gHhlrskPhT5FHQqWXxzahLxS2J61FpgO0twlxXWwhQ4cWnKS0zRWTwUhVv1wYrAZklxe6bY5Q/mf09R/DCWhxgM56LyUY0hJViDosRlHgzNriSpGfLunCLxqawEtIEpEdlCbCqK9+qWSjBPmJOKE208CCzH9hD2frrH+JkkpANMURlDntKGmp00SOEfmJPE1AAQWOwr97PRRSBPEOhI73Zcr6N63497CK0Jj6PKBwlXnyhW8x23xNDBoa/PcWWTJmg/qpmB5koWu9rKUe3JKKqheMjkB1gibgK6vJXB9rVqptf4jtrsI/5RZvBJ6rDwSIlULEYYkiiqZGrWFrYB7WIVT1j62ibV3gr90axl8oQGq30dbE0zeKuG1AWle1cJffNe7TGWDLXGgtwof5jUxPsYiy4vzfCGrm2GtAVivSAOKFN9qxqnlJlc1hbiiMM80zmQdGJMCzmAHFJ6jSXM0mvDod5EfcAQ4jHxFcbTEdakWzeVcO4z8q3MO/2pItTFNBPY68ngrgh67lnQvfnUwc1rKFXVEngK5BXsyhcNohkPTwnCDEoiKqA11EJnGsq1gTf0bDqrQbfCOeL91cA5pS7gsHz4vhNCm8ONo+04AwyXJZ6bkbrgNk+DDoeuXOWxGtHXHIGA+Kl+DaDuAB3O2Jk8AmftMknAtcYu4R6JYhRBr8yE/gGgzKFTw5eNe/+xus/duZ6971jvoNr8VVj8fPus3r46v3n28zLVebpbe3FXyrXzz+svV7vVnvzJ+k+92P3+8vPzsj7vNDm6olHOwqV2o+Vi11YF/1oPRnQGcx4iUBJaF233RUu4VHcq9CuHdVpyE39UTHNukYZ3xXXOuc1gSATB5X8HzERcKTXnBJwHWtWbyqhj31ygEPOmYOvgcfUJIXIU8YcR4zMlVpanZHCV1hYX0GdY0bTIQQfIfLmVBiLoI/fwy93ECBgKXgcG3Jp06tJBKHDEDlQazglC++ZztAwLxIhlQqGH+SIN3qFK9a0IjhgMiciJVQKsQPM6knFwja3Bjwgjd5GLSnQwmw0l9EgSMycVPHz7zZfgsFOGzWMDvHn7m8bMMaVmEBnIGb51xf18z+O0olAAD4yItdQ4Op42K5xvHmb7HZ2P7JKs8pZY9Z9xCkUMREfYvaM/XCJvoEbkRJqz8PBxpUwFpGl3zqYT+TFVUGkyF+jYV5F0AfRVCOg67CDGeqrHyE2OYinKG8YBFk6Hn8WA8uN7+wbgG6ByIUqNlkZVL4e2IoChVY7KKMtcKpXKNeB+qGlbYP08nu4SCHEq7m9J0SVo4gYlnouUqFPMD0/XmrnDz5f1JFZlakjGIwq1ycgUtMpKrJtDJCEE6qbOkSJVXzZPCI0VTyIwmTjAzHaNjl7f3/ume73R/bnvtfLmbHVy/fPXubP/15963g2d86aGm2MvrWVLdK7okAjZNWi8kdj/SoQuqLR/lyx+D1L4/GmbPy+Wb7EWQ4rPti9yM/5Zf3b8Rf1XxysIrK2o1pYzV5BS7XbKsHi5k2lH8YLbUjLySIYBoUu1Dy2tMEvScUdy84RoikzjrPmzsplHYuY6Q+ykz7mfD2hnyKAgCdCXMT9no6xyMNLwx4eXJj1Jxws6DJP0olyZeqcR/lDx2xxM/mhN2YCTNaleVzG8lyZZFIP+2J8VNanqI5xGc8Ml+PWf7SPiEHYmwCWaJjPEzbd4E/wUxDUZuVb+c9iuG+wI2+EfhnJ2AP/JwVv4onk88rzzJsZOTnZhJwzaYcE4HoUmDU+ND9e3qD9li5X6yCm3Uhp2e5uMud8FJINSsQD2lpkZdGS2bhhbzlqFJQv1yQl6hrnkCvC7EwRDTRJsMjHYpE5LRUm1pYTnzMkqCDASCf8R2M4fuhK2EtECzpNWCej08m0lSzaFLiZtdJVjpYmg5JjATJXUR9dGUjPJBAaKvd754Z1d7iHz5yT8uND7t5Zp3F70z/9V5M39w2ey956uyKKQ+U+AJDO/6CmJNexA+nc1qQ/trSe1paBAXsJwjLo1sxYp8qbfQ61G7kNeNzRG6Xcv1GNlZsh6rO0lS5UJAMnqgFTiCgELvpgWxxheNjATj2hGhJcGEgxOexVX8pSyEju3CyTIPmHacKK4lomCULWtkdDhTnMA04rrN1VUMzt7b/rClMW1ocIaOfnxXP2ZTtvV8wr5tPU+qdS8PMMJBQu1VXHcgqGnaYeoD6R8ixXh56qtzPuRXDy4v2nmf4Cd7Ad9YXb3OZrPFYrGcHY2y2R/Pfj4bfPr2qpjtHV/uXr990x58epurPN+9OAgdeotwCoTBnFvV117w+Gw8Fse9jmVrAGB0RoRk3e1fXLRb9U6POxMI2xSaekCE7w9s4ty4atc7A31LGg+hF6Tw6vXsu6wDKAhMcppF1uZL5M4mCJ2Q46BpHYM7HKsFdU1s7FFrPguV5fGpDulq4JnN91U3OuTicnQeLOR1GzZ4OUBY3YYoK04kBJ5TQdhnPxeK9tb0lIY7oO4DGzkOSgE8u/cut4con2M+AJEdstY5wk77XijkP1IZpK09UgwTPnrgC4Omx92MDC9vKFUj3RE3rsC9DT61EZkDHLYUw19ABp9zXsKyfhjdRK4iPwPZYEGXcU0XZ6J3/e+dYV8zI9vD7KZDdcSvHw8bPUiIdwuonUXbY84LZmCmhLhKEs/M8eCgD7JK28ferwj0hUWth5FrhC8QC56uEgHCHSUOnvD045h/PEpu1oCRf1kN7PJ1ihAHz0GiDvMVmnTUmNwmg1TNruuc5+0NuG4yLbZskJRa2ILT6EMg3KFIPa5TqPAzgvPmHDg+dGLA/tBMATjVNR5xoVWKVkzwV8a1kqj9IWKP7GEp5zr4CTx7IYJItf8/YqcZK4TaEFe5WDzIsC3RvgGERS8nbFbECwkXlMpUUzXTrnex2aHoiUiqOMsNqZKXSDwhMSvQDQ62d6StH2HdHVz2e+2QFxg4AJtxIV8NOu4mfFxghNQtYXs3URPTi0ZSPg2PXKF0zug/8XelMI8tTu4IO4XJZuFO4j4Eijxzd2XuPWAKLOv2Plb+N9KLIB/ohgYgOELRFoQ8ddlAnV2fn7eHOl1bFeh1M9wZaSL9aLVpRWhmZbr5/ycs5AHPLGMcdkheUQY71c45eZHrtcX25dyC3MHa08aPNMWRgk8XVJII7kX8ApADKkfxI0m0mOeNO0EhYFuNvIYqBMvtO4y5C0nYYJvd/XjQ/XLVvf7y8X1/t/ts98OLyouj3K3Eug7n3AgvPULTzuc0+PpU8rcsVREb6qtYVK7HiOyUojQfea5ljCvPeRWXX9HYcXxJQd9pgTKbKbVOwTP2m+x4yLEBbo474257nW9EbrUK/gdZrFC8nj2jxGJhoBqcuNfjy2StXhsQ3JE5Q74Bbq/zPMrHLXqWg0ArF66EvfzRZCO5mH7Gh7MV9I+lYtJ25URyqp8uy8bYCU8eUAfoZHdZTjqBG1QQPjsfsoVE4FdxT8+RQhgN4T3DTaG1tqdXBTrOYZe0TS09iR/AM4lF4gcz0Jj0GTd3jHEsn1PEoFx3trXBRT8Kbg5lG+N1DmbE68zw/avAKeNJXD1iTxZT86llRMbe0GKit6RPU6QD5DZAOsDQt5OONbubRSfKbwRzHhrAsM9cEg7Le9cwkJvZopoo0yAlvd4mjKNnP9o9FaAVSIWjHGflt+j015AnfoXnr3JOmUTYcg1byQhlsOBEijn2Ju6B5VoP3NOWM6gEJ+0Sxecpe+WsB1/ZfH+l2Z9NkF19KaOfYk6Pgyblpzbh9QSX1I2obuNMUFvOTXcgr4PxDHrlsn2j+Xx7WrSE44VrXPIx6bHiSjX/D3n6aHyHVIeqVWYsQm09ISeUt1ZPHl/MMz4Ehia2QI5LpgU9av4p2+hXMRBfQwsCMUBdgxilrEk7XmWpck++UmpdpFJehAYH0atVXK++rmVSq/HwToxtqGtziLRnYIoq8qyIzajZoJNDceXo7gCVC58IvB0X8L7YfSydCgR7QwoBKfmmyDCgpdMMOIvRbIyVTLH9o9mWWpqoyKNQ8IXQXRKYdTl0CM8L8LR9hAxV7JrhgiJFBJiMihgb1hSn6gZ3xUmoLxHnNh714UWGz/v6c9Rpw5IprMOwQ5IpZWmyFhfhWq+GnHiFH1RBCMKWxfG/C6QulrovBlZ5GTj9owxXqRGBmt6LMMC8FFPMdFUqDdNy9HApfncBru4XhmJZHwVpiPs/4GBSNDGYHGoFrtKEWBuQE4R7OaCPBdoJfxspKMzK5MbI96jbabVPvj49BVsp9wpn787g7yDT7wEK6/BmEmSu+medUbeRdCbi1DJJSEqHznNY/5ArjevnmA5eLRQbCJWOoCJhMK2GQ2QXkbnPPr73Xm8dnH/Yrhwdvxj1mjsvfjbzmr/uLD0XwmOHUXpRr+jClOEm9//p1cZpV3tvcNQ5Ws2Pzt+NvMH+ywm70PrefDHczm1//nEOP7sfvtPPybfjg/FZZVR4/q7Q3n2XVBtpoYVLaW9DjqMLzpNGwJo7l7ef8sfXnz963ddb3xedp3Lk8S5Cy3gwiKR2pJ/SXY81Y39qLTnhUyGLP4wmhbhIZIDIY9VqhKGUNPWvRLy46wB7KzkK1MTYmY3TjVmy+rD/sT10q+6h+9+2xLkg5V7pmbe3mfPzTOa8d5PJ9P6ZsFX9+t0/31/mX72YDD9+e/nz/J/W98H43fak/aI8uvnYzRe+fzt8/e6O7YF/Wu3M9btXL7pekRxwUguvetQIlkPUAUcfVPJ1S4djWWZUlfrTttuO2iAYRSXOtoicVdqGcmwbwdMRqvaqw5M3JNKt8fe6NQBwm8KknPc1N2rT/Z37U7iAMgQXRIyLS9Qw1GGrxnCCR7vka7R5DDH1PHLWN+CeHco5gtoOMyuRXaU+PhFCBcZPicilLJ2Iyvn1dA2SEyKKknIHFIQmx8SZp8adNdNtEBIRkje39XSQIUKlFz6xfgr6hJGKiZztkXi6NtW8UNdcI4OuaKCTTdSePtpI/pJXF1dKpolPIOV3IOPBQ5YYenHe6dTNHvghRn0BL9OithF/x8lUU03dKzAoe6wo7517v/0upfhfRIqlNpAHLCxKSUtFI9Pb/81DiLFTlY/nmaNt76Of8ZrXcLbsfTzvHm1XeuxCa/Ll06u7s/yr8+bV8S37m2t8LPZeP98c7N9tfm/fvXr5wOMHAcQ93wExFYbUmXdgaKAye1sXudfP3w9cIW1uFoLwuEshDYHyZf6PCA9HANRpeoRYYucy1Bf0fLhnr1BvIVqVQRXSY76xELudasStDJuwUJ54uXJyspOEl6RsYq6l5rLbTFBEIUWnajO40nIQoQfqsgURyXnJZNiJNeWmEBWnx2Mq0Nx6vRLXI9jos+YkozJaW71yUUs14HyTA3fsXHwHE5R2PrQG5uKjLCIUvLnrXxx7e+92NWuD2yBitwqT6JZCfgoR9jGAVGI1vO70/+F+9K+7t3tbvdt/drc2L3ZffNl6f/js6HD7+G3IK3Qt1Fc3Lr6V8sRury9yAmjt1V0K9DMjfGT4OevMcDNs0kF5LiiTLsPI2CWy4U8dFPiPybAmD7vQ+suLEyTK9nkb5hYdRaE5nZrtNeNUsjkA9W0b6CzfK05lpvp5iUq2x6cSqjQaJTUYWSHn42Hn4qI9rON5KYsUJNlxcVKIbu1IQhQR34WY4TiXg8YdeJ5N2jedWjcZdiTI274MfDn+Lzq0eXaRhfRyBMddXg3r5SIsITNzYgh1pba5RlbUC3m5Oh1b9cwPIae92RdYY0PXksr33p3k6MRKVc+BJYzLNEgSmmquWvePbTLXxp6/58I8auBMtec5xiPqoCk7PIQNXzD3QiGbHaxO5aKYwARB+IAFoIwyy+QkhLpVlIIfVrPGzXyzh0AyS2RK4iIXNtgDAu/BWZFCWyZH2Y3oOrqLFUPzPh90G3TRrfZ5p9du6Yvg47v6/ruj3f29+uvtz6FTnlKXh/UG5EqJsnU+T7bv8WVntBJQ4g573M2BkT7J/wkcxiLlbyCKmcpqe0AoZDDEl2e5a6TKQVgz2Lk/TuLIpWCWdmgR4oc42jN0q8rTUTAlxlITauQF+A1OGBMabcEenaLlKeVIfRMy+aSjLupobhlXIcod4c25b7ZUGRBNeCuhZkPgbs8FAmxo92ZyxAlNPGOs6F63md8bnPlFHpKnqJlXVI6D0YSKML/RwUPJUjvggFMow2n2Br82mTh1KryxTzWFW14gH61Z4ticKgLdCkweqVOIYPUwjBU+CuzDb8trefFNu1Z0lKNrakUDMjkCrfN2DaAx5VxS81ZjX4d41YevXbzalOApKiKaxivvtu0CEnm9v396r3m6wnM1sdDwh1gUEYVNOhz8r+OOfoFYFGxikfhvYoMZ6F6ZGpp35gJ++cIkUxc+zVUxiaYMpS1ht+SXMF6GbX31ouIAB3PJfqsE/BAV88gpngH/ME+cb+/9M/6YGf/c9j9mMg+cSFQu5UNU//da4328Gd9sV9p+ptveeWCDCF07nAMSF5UTmx6CQgL0Jv3NrUWr4wGeCA7W/o9UMpOyrxpxv/862Bir9f3Osf/5422fLfL3R97eh4+TL5++XJ5tXXz/5D1798E7CFHVeX1AN62QHXk2Kx/l6hByqhEDP9PHz/LsNnGntdmLqoQ8yHnyRaUEWCxnYsiVUDVNMOLSF8eIzLXGsZJzeO1pIDLBAxWHKJxklIiygNJupvLQhl4QSs5l6SdF3SDHKj9SqQOvg6goJQDOEvz4bYnaqetF5NQTT8eDUD2RA04yWO7qdcCoExfkXY7JVHrIvqMKEZ9ktUkylgXh2qiSnoyEi0zFFxkXHXv/F5M2w3DdXvYbVx1+xNLE0Hye8DxyRJzk+9gnWzIUkiGfoNXzhRqad+NXLOIoEotzoL7m6peryt3Z4bPLz/kDxgsXKrud3Yuzj8e5xk7l+7vDV7dn+b2cntX89fazb2f5Z8VQpvNZxAih0L2Q0e53BjWyf3ynND5+vngt015OxE/yXUpK7lkmD9Nck622o8aqmMedo2E26gG0kahjhoMqvMa2elemAl0MJ1isjxDmmLCGRjqwWDofPQRDebNoDp+UeOp04cdpLCgnXCgUIKSMUTgegdDChPwM0sl7AK3AXYrGoYpE6QgZOkDaADmjhOYqsB7DZyUJn1tyMis8FyMTayrJ+wKKRc9CuixretHiFgHB+wDnuFc7L7zWzoUziGPeWV1ZdYMFol5gmTjvXH2f4vd1qYfNoibySKdwrbCkiQJm3ZwDruXiTXI+6J0wYn6Cr2Ln4yngxKDqpQD2izziM21Ill1rpcxfRTNcIqTnuYmmhBQ31YKG6UoUBhNikOchU3BiwOSwdnMMSjA9PGXU7rUMLy9+WKTDl/jpxdmPqaBw7oMmEfiA1bWyftEebzX3rq/O2kNsJAfpQKV/kd/e/jF42++NL3mBrIItiTpkg9sqIPMVZh7HjlSUJTocSdUThj9GNE9xRnMlED5XDtDCYXrA450pjjSEVcJQ5x3xrLaKEt6Zo/VOxxwk19KYyMXUTYYW35/+jpe38NmGRej0hddpoCWTyoDgtuEMYxmDDNdAwR7a0+MLkZWHiCjOg40u6rh8z4hHdVZDFtZBt9l7ddPsVu6+fHp20+yxA/yq2X+79b0sKDkoFAF598unV11ddm7vdHOOA5wUNawhvsB7MukRndRavjYNXUveY7zF1fdWZ0j0SwxQdtT/Lo/ZBcozzrn+rd/vX3UbI/UcLaVZzSBzjxulW60qnegpSsI6TjyVfb6FRHXOzM89GJ5/aXjPr3/uvvj+IfMlUzre/Lk32NveOdtpXX9+9+rqi3+sjHzpfMF1XEScGaytBeF2QU5js0+qGImTl53Pn/a6e98Ozr/sHH878w+6r7deNT6+GPQ/f9/rN467n4+ufuw3uq+uPn5vfWxc7d59uPrx5n1O6UaLYYZJrBrUuoSsF/Pzusxzpdnburrsfv540G12LnpHO5Vvnz8hr1rafXlw0/CPrzXNbWR8Z2j4kJUphOTjhTOra9zSu4/HhebOj5svOx/6/DJleER4kn2fRPuQWnRm85AtKUQ5gfyqBwgMz+f83s83fnf8+WOr27yrDN70DgqtrYepF1gDKTUsZDeSiPfju4GerX3c/jHOfmvcNHjwA1UL4mqr37y+gqRpGQV+QnzedS5yrT1l/7TAiIfOmD4ETb9b+OS3uq2Xz/Ktj93vr6n7IfXi3EGoOENjlw1OqGoSoDXn/dvBSqN11em5Swhah6jdZJ8rkGcR+ToQOyXyheqPpn/x1xxGjbhssBcDyqjM4aA7w5I3rItj83IcKtx3Mt0IyWwH1hDWne7LWSMoiyhElBCQRkrFxv2tCWVG9Bx2AmN6zag6xMvX1QnWbQOkwbfezkaAKxym4bQVbCzw1C/OzBZfB7h5uZKzxBSHZDRN6TZLSuSPZ28HwQrjMYMVYlUtmc+2wLGG+gLCOJyVFGRLpbYxRiogwaQiuLdaBPs2eTfZmhxODiSnVhLRdYsp/B6gDg4xBOGdrnweWYOTNecizgv4ELffo2bKk0sOAUqzIMkW2O0Ol2LZ1zGa0Qq6GU2zs4Eg24ZtTxjX3ILuNhty8pn2Vg20W0OQAigWB4+0tgCL5BUEHuNvBRkimUjJcJ6ZsI1ijs01Djm0IAC93rweDtlZgk/JsobLWOBjiF+d5yN1l+FMuEf+02U99ahJWYQzTxXQkZxS4ePbQetsZT0JoG7D9ui6KwgYrboXB/tvERoeWjwyQjxJZrOBjHwHEtM6vGoVe0B3zQSnMpupHXoqPhRsUUFhGUXh7goijrahem1Y4/ahRFCUQgMhnJcqWqZKF+7HrAx+btlvNsKdOYtY/eDaXX2C9kBSkzjNpznZ1J/hidO0QhxBdmYZNlQTc9cQyhUqEe/JiPFHzGB/qBqnNG7DTZickNQJeDLqTUqty4SPC6SDG37UCbRoixcwCbmXjEvRU/3tF/MElDoo3O9UJ/YMYSmsGmO3ID+LXkIUjRmhBoiMkpC2xDy50x2/eHZ+fLz34kO38uwgd7x/vDU5fnHw/pN3fHRw/Or8/YfWi6Pu+8nhi4MPH15Ujj/kjg8/5V68PPhQ/JDUeWZoytVx75PvXbZ29vqT5stX3S9eZczktW+NzcmXj63zs48vcp/9C/uxxqf3/ddHowkoJl5vfS+zpl8OWlsXZlOdYidCsKMg92cs64Nxt+vdbOeaHy5vMq8/v/74z/PDM96KghQxY/EFxXSCUS/rclKq2+hdXDcudFlJsV7oYve/zn/lgSe7WO9hkyVUJuPb+RC5TW6/6SDR/ZhRDhL/vGvmLgfP335722zvFI+OaD5XV8kF3xbIoycT9UMFB3DdwlEturgO35O1MDp7+jRlNSYg59I1bih0tg0ZMz8EVr6cnKvv/F/oN+W7V47g0ny3h4/FjqWqoFSMK2c5EeyytiHBz92NS4Y1t15AwDguGQ5B1n3dV+vd5Tv2Zfu48Uashmebo21W3/Pt5v7zCMXE9FP+2WUz//ZCu9Ta6d6cdTYru1utncbHH91mzrs86zTLWx3b7LX7/P3t3taz/bP8+4uDq4p3dvV+svd8M7e7XbxpbT3bOcsfX3/ZTEYFozn7hWyLXzKZ/SpmvtfTcFvyMN1/02+00B6CeUO9lXWyBsXNkEzM1qBD3NDDz58F0nm1hP69U0rWhByerhRIaLWBCHt6X5RpdQns0ikM04bLcyPYmlxYqNbKFcVpcrJEdpqpXNuWvYi7nnNXBcPHPA/zJ3zMtdV1CsmdK4Q0AE5zPC9mgsKbcYNnkzhuiv/yrN8Jc/O6d0toOisC/iORJSZ0MXzCCEgxbR1Z+LfaisrydyO+ucOvAvxVlLtKhNnE2CbTL73ja3JXKJ4Dn9DMH/w0XBS2Dl4ebVe23n/4cfTx7sFKPYIotyD55TqwF0HgMHjz4EShRiFUOdohfk7MvKDArDbUzU920QWM7U/GW6SAwbAiSUI6MV20kWvSrRDSvfyJhT0ReSfIvhmOgeF4WZ4xnW4VFiKbe5VQBOjvRyzyeI93F/1VRghzZzsfJlvffuQanw5GX44Kk3cvDwatnR/ddxeT3efysuQZ0c1o98Wzu8anzxcf/Mvu2c7twrG4rFtuD5tftga9+NL2hqOz19+/n50XP5z17nZ3vuy9Gm6uvrr+Uv548P3L1WeOTVgwlN8L8Zn5goGg8XB5whry9zt7XvPqx3nTP86d5Z/lJp/zx3dnLyo/v3w6uGm+fJ+0yuMa3py0rl6MJmDtSBoa9XD+VFxRvOl4mFdCWE0LNz0yGtxeRmq5iNUkVhKXbQ4PPnx58eGCiz+vXhx8Pz6cgHj04fvxzsHxbnKxDhH0Y8jTPbwf+HbIzHINJSsYW+C3QRYwfSbvnq/Sr6TCNcXEgKxw62NxxLo/fL05Obv6wSTBUX9yttO9btxd6KWL7ua71pVkAX7JTqaMOfmiNQnd1k3z6uCWTdrP5t2z54ya5758LDJ25csAZobPUJA69plk+/IZk273fu5u791AWtmzHvA43dGX93wGi5/YxH8HgRP4p/d+5bq1c3wNbA8wTQ9iegi0O+TxCT12ODnogegzYF3tcOIFh4+17bP/A3hCdjzsjT6zIeDTiNhxFT7rux+esaneG7U+HnQ/sb5/9j/0P19MvnQuLt+zyWdVeF92js/ZEH/7cnjZmbzeerHJVsm3s5fH378cV27Ys93XO7vJh5+VaACTwAkP37pIOWZqKZSlBrQLP+DE736etD69GkFmrS8f3wrKxMS8429S/e2LJx6mj8jP2tcIGO4IR+OwNBj7sHi/0SVxp/sTKCz4J/YbbHa/fHo/gQxiu1vA1+x1W5uiaZijtkK9Yq0enG1dMELbHbGd3pD0FviLiC44Zq9AMf3l/8bsOVoEe2dDGJ82btrDEeNQ6BqmodgYNJrfSU3yCj1BRLPREJWbFQtmsRu/R18rQk/27ih3sdsddFvHbzuTd4ebnY9Xx96Hu93J7tarxpdPx0fsG7/9a8S2kHenxZyJ0n1ClvMfhUIQeF6ZfeTy7MPnLrpFadI39A7gJHmOGYDBxHSOQVyWD4ClEvesx8MRxvDA4VVjOL4LWstPngzbrc6w3RRCn3oinDwyIiZZBs9PaxYjGho5ggWKBCEJGcg8Ox2kZBq10aoi3grhdHpIbMF4ln6x++Zo+6B+vPlm9/nm0XZ995249OLN5g77eVwIZJ6FzsDv9iFoN9JeZ3rFP7jBYsxkZCBOnSuYgQ0TWrdKrtN8ztnmEnoijrdk6s8dcEI/jO7bl/KQY0fbZv+1PNo+sB140ZcnmmJO/QVdcNnIkGsz23oHbXRK2er3IJb6P/cvj47e1T+wzVbf3NneO2KdTzV6rWG/0wombIROTjE1gkhNcLK3ld4/gNWTWqCi4GsAOIk8Y4/QLvMHD667bSrzmP0BwOInPDFjBeBlb1HQz8pdluG+2CcH6TfBKe8V5dn1pN3JZRhRiVKcUItuMAN3Xej8MLtKzYT0W49v1BTCcEhlUQypLAi/I3CDoemqRYTpDnF/Uv4XMNLr+oGZYvMyOuwA5jybsQ4TzS/63Va7R4E+aqmJtVZx78LfPXMnUYag5lXl7lOe8cY7lTvGV19/ubu4ff1ebjQpPei5SpsoL3N+63YiFC2vt1o/GzvHI8FwgcnmZ+vl2/6r/F7/y8cfgivjlRcedgASQnbBJXK7HYzpjFKuxfCaVmc06DbuCL8FvUfTvOQv2xM4I0DJvvW0MpbqkxNzTF/OHas9nYqiAd2krc5hQMYsnNlLZS4XOgdbUxapOBMoHVrL+ctpLVNUEXs16nJcLt4QqmMaAZL6EGXPOr0se/0l6xn3fp+udNiD63/DBmm1b7Lj5iCLcS0Zfj8btJCIAdlqsXKtsA6RtwoZI5m+xIRzTgh/M0K5g9aphhpt5NQs1bwedqm055eDTA7/7026/Waje9kfQYRRaqUvmt277rLSKX9d/mJTmRpftntLVBGWXV5h/2SauZBvMp/m2Z7Ja7Wk6DCqc0IQMDxNT8od9mRzBBRNM9MZjDc4yAZZBZk3e4sWhQNQGGCQNW6CSzyp9y4lmEkrVvE3w2mh1tsL3PdmL0ZyPaRW9nVDRSioydk/tOV4jg3wB9rbbLGDJMiOrwYwHtR6amRCwQ+njO7oKGPNyytI3pcql8vQL5/HmbC7uIVhfQZ8uS7ThmJf/nZuqLKh5DY2VJIjCfyZKWpCG0SPWW23NdbrSfd2sCKyLk7O2+Pm5aR71/sxgQ2VpFAAldk8R6K1trnWTlPZxAklmAK6mwgy4x9jwRoM2rgr2cvgvZC925ep3DALoshNxfOm+zx+A8vUtAEwRsyVviN4qCJMiJK6zuL1lq7O6E+kluNO10tIJesWKlmZ2K/UrERNFzxiKwIY7+mjYCVYgYVXgAFm39fRz1r5WN80hsiombDdynubEn9ud9v4U4SWds6Hjau2bA1C+GKWx9GwSSY/g/ZwWF8f7V4wp5ZnKwVLsTO1hKlrIYPjHWOMM9juoi6LrLCzZcCkzEdcgQArjTfu2d1uyw4JzjQGbNW2ti473RaF5sAC0cH3aYSygUdDh1jPXqnyx0lDKE0r7zwjpXc6bGedM/3BiRADDJY4UnFF0MvFBbN//Yqjxyy8ACsR5v3v05W5gJDBaJa9qkTIPaGck8sy+19lrtuY5uAFenweiVkzRoK/W/m/JTSMfBSmeA5mp2ubcPCSs/qvYW+ovrBGhY6KUt6BpGmkbH7kgB5LaMxIhFGSnlLr3cNE1NyhIdARzRMnKrt3Mkj8HTw++Vrlv63wClC7XHT7Z5jPlStWKLY4g278MMOIVhRgCoESOSEIr4RSusDdpjOsXXkr344JOiM9aMfD67Zgb6iOHJclCaw6FEjnSIUAGRNg9CoV5W6IWRSYtGhegVKeV1DusVaSUIG1h1k7M+LYKakUZqqkkTlB0Q+g4Wn7vkjdbD+jtENfwOVV7DB3zjgo96dYCt4OePvVjeDyYJo7zb7+k73YoI0qSjCciXONXAhmJn7kwEYU71AmRYzP11GAblo1XouCV/NFah8f8uERnXIeziViPueYnJ3kT7IHm/3dnVeXTf+Dv/didSKufb568fPL0efcp9uJHQo4EeAzsoy4cvFaq0le23oWflR7ZRJHoMjBbS16LIJJSmV3UsERk8bBxWE0bgwp+kjAuGor2Ffgg2AiUDxW76Z5Oere/HMaSEdm48jXVE8cyUFMLY8NQlqDswyfeHNd7Kk0BabkczmBH5xAJfzh4e7+HoJmQ5A7xnz/h54PZDbvE8ZbngrnJ2pcOihw1T2VXQS6iY3bqnF0aQgMlHPAVxFnSBG5G0nCuJ+scnkRon+C5eDrf/5GeDklNhq1AQTACes6QgCcBlOEAPiPzLad4yFo+jNp45cMJhN8d2U6vO6NrptNNtvqGoZIyRbogWaFMsKSK+k2pgWbpaLjzErE686B0o7EEOEbpdeGRMNXPy5bOx/6zfwBqNJuWs+3f6rdUfn+5ePeDSqi+Yb4kh8Mzq6+jD5/Oui+frmXA7XcWf79j7d34RhcuYse9BAHiLowHtp69rP1EjSDldzbo7fatpQtpO3J81oaB5rcnIRAPls/ubjbi3d8PD6qjG6335WbmYvB0fHh4fH1+MXh3ubnF97F91Lj4E15c/f23dbW2bdXz87/wfblo3K1Rgs2iFDu50N8SjS8AqPdKxLwhFX3bXAxueicT9gJIliWiJMAxNR6p9fhti1NkpEWrrC2gUvrQDzQLLnBc95yXqZSCScZxfcQCiPKJ9xrK82BTozu+w63tWCmBUxs+CmJaNKT9BLDzlb1uLR7H25oEJAj/JpPUq8f4ePQM8UVMqGmydZE82qgcZ8kSIgliD5Oqo9YbFZUm9Zk2zIwxdFBB2lQltQY588DWFv6YaszKQh07pVzfypy4J8Xx1cYOfA+0xmfvS7tZgr9g/Jeodw97r8vDBsfvh/d5A7fH3dz/uDnp8q3j598jn4jd2M4rMC91Avu0zPMV3KWJc+hRsVh6qvA10XKRoG4MUHCCOULCzLQcm/COG72/e+/f7MCXX404KUf14U6PZgp+CH6OM44G7fGn8B3OHjx/Nrf3Xx/ePN2ePP2x+bo5+vmcOvn6t3bq9Vvb38+L1YyF8eN/PGr44x33C6dP8vP84twM9QICe4V54JILtryZ80Xr77/07/d3Hz+/jpz+M+192Oz+2an1O5sd7a+7Tae9V7+3Mp9/6ysOBHU2Dw4ysJ/4S9SK5Wnf0ExtX7aV4Ox9JMVS7A4Ncm7Qo8Ty/JpgpQyoBrUUQaCIB4bNwdMjrjpZL/9aJymONQ2VatqCIKUUFsREndCe7MqMxp0O1CA66yAmYPx5RooPgKod4QJRPCjIMBROWHs3Cmme9Rip58G2SC/LtTfXHcf+PL+X1K1hNDf6Irzq76lvZfPh+/+efHh+PzV2bvmzu3ZTuvZUWvzLJ//2TnsVCrFYuamc/7x4PNdL7PVHLfaHzPex+bheeVT5ejLN/+wVV49bjfONj/uaPZ3rxC1RnmzyZJpuuIon3lK25oHNCk813jSFeVLsOpwoJaWMfKbDpVUMjnCpniSj7ECiYPo9Jxx8phmLG9xvsc06tlrSjGhhcZ7RrfkNiCc7/yv2Xe5FOoVUeHLDvSz28lZ/uCGcZatm9YOiFdrIjN5IRd2vuUMKC/GOYXGy4Px2cu9W8YDDj5//PGtsfPirulJnrAP+meJCKN4xklrxxs188+6n/3u5dnOXv/zp1c/P7Eyn3327OEleAjLOjQJ8+zjj59NVl3j44vRZ//ystnbU4AzL4/vznZkFZKvFQ0u5YxAPxdbj2Dg+VxIFT8bH2ryThoL2eCGIh/MjFEQdj9pJ+lruTS54V9L3qQhvjYnXfyaAB7FR3bpAjknxO7+iQwSFhZx/tI3cZXD1JX8yRkUaGABT+erEgbKNgAAlCYl8bUwEU7uxXRe5HiRnARhgvBl6joGCbk8hDcH5RA+lMyzRb6sk/O8+B/GJr362b355H3a+dk82n/Xz79rnJ+///iz0trP7+zsjb6ffezfXhw829Ws1qU556TRtbwI33cSpNoTd1Q/nAdaCEQe1DSur6RCMHI+p1B5rxaSou6UWN1yD0iQpC3ykybk3KXSQT4MaSo4c5UawNTWF/hJ5SHUnOYRj5IriQurQCb9shL3nANXcJi3qL3owTH9dcb41c/R+Xk3v7PaPlh9m8m1M4Xzg8v9681X/Ys349L397u9/O5xoXO43++8rBQ2d55tvt/UIvbyq1Hz71zbqHn0QplEBTcLXbEVoaYGOewosugh/PJV/uW365tm8/uX82Lzze3t5ueXPzevdjaP999+HhUHq/23xeLd1uGRL71DIf5mVqSHs4vkqhYOfrQt8JqXQcCOw2AluGVczlfTaVNBfUhIWr1Grp1Fy7NUwlLA2ylocvHGxDCxT4LWfZ6tfOEEAPWnESU+r1yu6H0gV/JOIetYWER6tzacGQNVmer4DiEPC3EROupPThqggb7pniZNv4fAKfQvahrjmnKV8yYf5cbmnF3S8P3x2dUjyeCaHkhpsB/RGgzFHY371wqfZMZj5HBnIkoGNoaGnlBEc8+U6fqo1uVs0jlcFaH2cLABoKCFukOAveEJWQRZ4t4cRo+AVxRKkFjXmHqMlbkHrYQJwVlPXA/A56HdQkwnZZgjZN45YOpX/Zt2XUtbxlugZ5PiaaZSUTl1vBwimvuYY3LRnHZEPAuoWnEBpjbQw9Wy+qJWyCeI05pLCSeSIoGXdEAM+4IRqXmNOwo5evrS98rC/W51NE6P16QbOgkjPUxXI9jKhReXOYeOkbhoj5u30nqqzH3hcykyi52Ipuw1rnSDuN2SNc0vDReTsRqNdfWYraE8MRAFbvJ4mj3vD6+kPCvlnYovSFYCtmng9hbHw0LkhQ8yYH31SlN1SRnmQo+aQDQZ80ril4BYuD0Txc56q3N+Xr/+3pYCqL4NZNZ4OTwhWBwqZlI1RymxzlDvGIZ73qiRP01dj9aVBFatcIe9Qmm/og+WSsF5sKCCGIob1SZD9QpSciKplXH7NDDMz1ZY3L37oen/h7o/YUgj67bH4a+SpJMWgiJVxSAxBo2zSdQ4K2W8CKhEpgac4bv/z977zHUKNd33vr/33qeNQg2nTp1hD2uvlXwqEqJaUO5G8K0KGjMG9YrDTXKzJ5+Y71ymQ2pn1c5pKgm3FRJsHIHpP4PBLeacXOdqZxKlKREvSXeR0q82lW8vbvby/Yf79e2rwk56L139Vljqnx8v/X44/Lra36sWa/nB/s31Rr3zbXln9SGmstFt5CN/O3LZu9IDKtH2ViWIVKaDfCQenk9GzvnEow14AlsmO0gJShmO6IFZO/AfPe6tShFYvFtWXUrELJLrJvVLsuR+wUixCiSUr2QwxXwNbh/9XlU7WmaBAOV4d8eWuFK/06o32e7YaF92oZJNVm1I0gqJ4GQX+aJRnMKfvJ0zRtWhgTp7KT29XTO6sfb1qn54f3t8+FMLpeV4OGmxfXBDRcNDXk/Mi4n1NAyGna66NeRsGi62oHJAFd646kfF8lccJz8Ua+Oa41yKM2apC3HoteqDK8DHKlalTn9AJ2NNYZDTUxnz1U5XD0XLzdy822ky+tGk2F2Nz1Mukw2eR1QFqAi6ycVnjUwvQ1UQkQJHcLu4Rw6R6eSfJ6ugo1fyK/vNle93jwvH6cV+enGzuXDyfWfr2+a3tWF9JXfwuzj45zKz8POqWVzZWfr59fvDz+bBw/L+Pye3x5khnN5cLfYuHxd+3vfS364O1/cOO/9seL2T9eNN7+6kvlTRiiBnXuXiekiF7vsRmdiPSa0PctgHf5oB6Z101yDnW9ipNv8J2t/qB+nKmp+/9Ls733qPe2s/D5az3zNrR5XWcDG996M9CIKr2+utXGv/sFdcuWvubNa/1S9+Xjc7G3eZ/dw/uaO97v2MCPLhVPMz4zx7K8XjIeu6Vxj31t1P/OoNhmIxW9v5x/RGIX1VXD3+ll1oNbSWZ8e03JFK9ZA43c/H8jGZSUYTyK3bteZxxmPYJjA4+r9E+XAinC+hQ5sE8seEYH9U7lUA6J00QIvDvib8nAizQEYZ8pykFybxoinTmAtcuiTiInnHUDa6JhsnKPRn47a70+xVizfZmfMfhUw6vVD5erX3e7W10WnmDs83uxtrdRVcmoxL38gBaEw6DJ25ZPP+HLGX+M8owcy1NgqWUqVMarWAsryPsi4vBYV5KarME4+MobSCK9jCwQQBCBv+watyQror6436nc+W0H7x2z8PV//8sy7zq7hExr2rMQslGDEB0FVNtzv11nm9hrj1QWPQrMNvUxth+muY3grTx1P8DIwv+Y7KjzF+Lhexn0CiQvRPxN6Izfs7jKb21Skyvx9J7puIAz29r6Yo68hGt9+ssNHG1zd5bf26KqoIF57lz2rSU/yRuI8q3ECTB0hHDortk6Odi2PBMrL2ozMEmhGklF8p/hZUJtWHy+H56sHFiX+QOfIFc8nO47cFuTPy/JxZ2CEwZFEl1bFjgVjVowigKKYyjNeUZi/+bNBo1c+ajRbEtTISgkm1K67YuQYa0RZoP4K3mg5Tc7yKMsjpWdt/L9usrxTw60vE6FmPYZ1l1kwW8DNji91lMIZozedfMYp8pSvz/NQmvnXfBi+mIm9u+Wzh+/fw1/LZ5tbe+uKyAEIJoCq54AZbRGl7bftsees7RedP5+r33aZWL8qTpMWR7offnLPODhNpMhBwsZoViAbVV2l1pyd+kwhWK4acwENCdQ9ma6yP41xV+1eV7uXtecVO6XFHEUuQCDZHTIK+mANxLopHJOfRmfQCRcwXkp6HEhAFVT2e/fVLo9F2MFYgWWzVRCpKCJ2pjVBFERPg2qZQzkDDU4XEhC+xh853h7ZDIRMzk8x14NN0WArTc4afSBMqByFB1+wzKmfYl+rQZxGa4iXhs5gjwXGj+ZKFR+xbJOE29MwVC4z0Tt4ZWX6JivszGYHIk8vphyfD/F7aWtz/sby5d7aztbXHZzz2pVyvpBnuxp8SzhWXlb9BO14ax5zac8Rtb4N3nX03qV0uOTtfoqQRfMGm8aADu0x5rPgq67iCYaw4Q/LuGLZ06JPubnxRqiUmOv7CK04aB0sq0zDG6/dmbGOFPyuPPhvUqdHcnFFbHZezMG2nP3l6LVCSJK5iugo9rP6JaZFFnrbopjqKsUtSIuDKe1BLw8U9krmG/oLmqMJmV0IvLiCUNK5c/jUpK0rkQ7u/1jvVHulmuk77ArKHGow52nN+RuiwvOBtvyhhran22KMhEpH7PxP/djXJ3R+Kr9WyTZlrX2/f4q1hquws/9jaY8bS0tKOwsHY5/zuM0vZtN/cmhUCywOAEjvdxUWPuaO6z/oJ8nLDbdYbd51eLfmJfwnmUrNz2WiH5Ql24I04sCsOREA41NvAwSFVCnHTkwtXFzG0lsCFOgk9T0F6USaVnMXe9KrNDhtvPu8xjDRlpGUpielj4tlYKjE9RZ43LjNajbSy1coA9+xjTVGaj2hB4SZeFYaJChFhdef4jXfzcNI8haPPX94KlG78DNCiuqJXyHd1Xzl0OjBQcIohZPay50txq6NW44crP98xaWG2cqdiTdRDRX4MrP/fzDk+BsHv7Q8v2bo6rNxdD+8rvUuIjqXSrMGjcAhL3JDZANUahwBAL02fAmsDJmxEhoLzbQBjhaIFgElphfQwfvI3P28IA9agrsBFUyY+YIv8YpGtCPaKVPh3Sd/m3CtAzjavlJsvcDOhjW5IOs0/nooXTjI/kRvgAsfyGV4lu/SYfAN1Yi7nAibbGPgYFMGrTBLDwLDz/rGbMPHWFyNhdj0WZEZ1Xpxoj6vnGOPMONL+r72f2dPMzGRvRLVep7eWK3AsxqnWqIudWNdO4j2HhunMmKJ4tHGbnTtNlMp0wvkDnP1Y3lvbWuJrEt79rT2ejIVPgHvUrQjEcF1/0PFcEab95KRmE0csE1pG44YYVLB6+pI5aXaowsSDp+goYPGIWD+aIdBhadrEEJPiBSuzMX1S2Ejx5ILKUdRzBG4UrQ4oTJQt2eRxsw7FC4wWlmHJGPRumv2LStiHSMcTL62NT/cRLX+A8JkSUbBIa/hl9NduaVc9BW6snwICIUwfxc3EVwDeMOTsD7yI91VtViDXZERbUvj4zELqgyGx/X1hb2Vr58eZMNsh389syFsMm6fC9wn7HfDIEzMc8hhgoopMtq0Na53qfQnxBuC9fzhNSeS1Fp6K9A9NuMFVoz/1ReMt1Io3RhHnMzoyUIwCl1dtIeA7Cu4uYTD1Bfbem4GUc7Zfb0BBxkgCSL7b1+kJ9/on3/rXxxcnM/mdhcftmZtub+Hxx/3WTuukpWV+HDq9MXnHgOKMkaUsgpQv8qianqyzF29FLgFwPjWhE6LYDxhG+Gm4AQdUVV+Uwtx6fEUzASXgQ19g9GXQ44IhbHEUQFFmkN6dplywUY+4/aNgqD/LydENCP69WWleH6S7hxfFhYVCZ+1gqbPQaOYbg2Hh3OvVijd+pvF7Jl2599KX24uVbu1hPT1obd1faITJuUzsy3OsrMT8n49Q3/7xoyydXKwt9TdXly8P0vfnde/u5+bj3VXH/7Z1fJ1rX5382KpXVWO9OPi6aCzYhCV4DZF3gAirKOT7T2sxT1Z6/f31y8vd7Yx39HB3ff9Psbrx43Hmsa61dmxK3tW7JO2T1SBTL5Nnw0sBrffGSnFt52DnfKj+AGb266NMbeVgRRYFEaSm7m/eVteuO8Njv3hX3x3S3/IgyhZtBRyfM5R0/0kJ4wGAz5gMg/FsBUfEKaZYRrOdEpY5Nen81VEqYyP3PaNUBpCAKJOqEYZGafX1DqaNTy8VMd+uQTniEa9/fNUYX2xcgaGSjAzZ5npK4oMMgyOU0b+INSytLllharoTc2j0/Qr/5u/7c3iHHmTEczSmUlHQgmjsbQnx+jCQoxtOYUIFCOQC7om37IuOjrxA5palBMJ9WsD4dgc9CNSlL3qd1uJVpbcoZ4Jv3irNc0jyQxFBq0XS3bNku9g64cLw0luJhsPC1ElmqgilTpO5TKR+kDnfuuCcl80YfrbJ46YBSU2HTOhHU/EL0ND1Ol5wxm6KD1JCpac0BDxOn7IYBeAfsQPrt8yuwVqyUvIpGOmGQuglSzFSs4CBTHGrOGFYJ8guHmbn0DCvIQMK5szFH5DCQVD1JMLhActNlOTJWeQk75NSOKcpF4gi/GCWcj1o7+RGKFo94mkx1AtwlKbISo2MwsO+VMVSLpMuVadvi7Xb42bxka2XVy221v1GNbQm0CZy0mG/2DgK5NYp2BVtbYuhKY4xNL+XqyucmrurrC0Mv7e/dqoLw7vKgrp2tAA+ijkZfr4atJp8UpJOgbNOy17VhAVsKZyLtBgsLFEKZBiJmeFFpdmvJ4V2MJmzrnXbna1C9w/omtDozvPSURwodj5fM4sXCae+Mh6n7s6NaXVRcekySwYhIYoR2ThjdoT2cGd33bNKrdVon51XejJPJR5LOMraCcbxV3UUqDOXOi3k84Iwz+fT1OdGu3szsHgJ2Jf4MfdqRmItpyCgvZrrTwRxZtXEbq9+oT1JfPM54IVkEGYcPMkJnquHG3/6i29Wf2EZ32zS/ZZNxFUMtMMm8aAUab9e6VWvTG9Zi36ILTEdZqnqFbTNn6QxyVZhCeZLnCkwn8pKkyK77XOwJTAHAco80lNxALvwEwCtQdgQXyxnxAbiZPp96eKlLMusZbPPwlEAQEu1od85SX8/VUHWq1DQH9bCxMTZxvLR8uIEnJjq9PBg5k1O7NQB+1QdALEiP20CAvq89XknXDQVVUEps8l1+uTLxWus8pp8JlTaO9zsnD98bbnBTZpR6ggyRCjyw4/hR9F2yROiGVlvjcj9q/WVI6krms73DcG3PgvTSM65+VIUc2oXlsq8+MsP5ujR6OEJncfGQAXppanGUYJyMdAnOLEQR5PEsh8ddZ0JNvj0wjBZcJkUwwGaME1hHQi7DK7qkIVq3lw22smpVqd206z3MVQaZcvg6WnkFpdz3dOsd0dgKUu61w7ZKBe8jE2TGmWAJVtXPzVFy20K6NynGiV6PCX2miJCr/5QtLmMSh0+6LgUY/kdSc0DBjQxH4fEPX3P2zQHo22e+xq0VMVwSEIbfcXr5YJHmMsX7pdByG1fGZfipgKtXwGPPedISjOSRft35FWD26Pm4753cfj9Nnew8q13WzhZzD6etLa9xaa2yjnjSs6sDIkY5P8zyqKVk97aY2ez0r8+v+ju19v3x4tVb3Hz4bHb3ljZa7f5suVbIg39j+aqEJtFIuWDaM2B3A5c4gpxsddvixuVw5Vux66uiTrmcCtcG8FYQ+Vv0R60gqIiEAYS4j0fopDwuht272qY/lMJZG0ahXdUjeuQoZOLv03s8ey70RCyUl3Z0d+u7s7iGuAo8Ri7BvzpEmBXGlh2zuddqvD6xcXHig5+NOAeDwNu4GAGlp5fpDbB4VKgwrR2M4yU0omupUAaQfPadqpF75mZpEc8tZUg5yRq/FcJY+0RjA3YjaNSsRZqIAcKatHyaNwvl3evXxF216gBatRYUrUgsnH5I2Jw2Ds4Wlu6uV9e3r47+t75OlNdzi2ubdw//m7dFBuFreLSzU1VMOXociiujBVKJDjE3l+5etHSdbKyfHO3uny9u51Np3/+vH68mln+/iOz+t07uZYeZv7layvGsjwHiN8G7KT0ambaTl4FfM86Z7Xulpplzy+121yDiS7Nn5Cg+5Ei6Th8mGtev5sI08wAD9m6ymMwRUlDEck1e6T1hmHEoouL0w0000uKmQ04IqE2LWOshVO0T+0XmidQ1zPW/Jh3JSgPEkj2lakOa2s7d0nxOWGUjo82Ho6PrjugE3VV371MqqlAjtJW8LVZbd0Nq6sHD+cL6msu3fq9vZOtDdcXc5k6CgDDt9mMQEAZglEPQ10k6ptR76A7Sa6wNKoVRIty4J31ZbKp/58ICVhj1RykfOg6EA0ql+t+AKrki3AQYWP7JZyaF6UhJhFFS9wgs9BCYrEHGNTPG+0wzUw7qDPuVe6GQkcF2cFQBa7UmBtO68W6YrnsP1cR4ST7t1N0YwqFIjj4KEwjOcmPhXilBcFyBN/71F18fk0CT1b/Y7VXDXh0GYxmDWHi6VVE+txhT2qUjsilW74Cx+wKRDFFcbJooEBcXZcVrpnDK0OX0Bhr8YMsqWPcwPebk8izfzHYvVktEDXmKXJuXG4MRA+fiD0QmzrsidjDJLU3Kp+jzLUYKdUh5HIdBdslEVorlX/JkiM31leSSLw2V6MjWMblZ7TO0gq4w9ETMj46eo4CMpF05nxp+Y/8HXb9lZOK1+8frv++3k/ngsOjheKPe6/S33j88TiEfGcf851bkO+8ujppNjpXtdbF6nk9GR+Wktusy6zIF9wZ2T/nxz3G9i/vHx5lc83d9W8nDxurJ9njRvO+cdI8+Ue1c7ztY6yuGAIJXCo8duSLLMTgYhiGmVKhwC5Cv+WBOyioD5F/7EVhMWooh6Y4squ8aUUnS+8f99/KyfnK0s3j4cr1z/TG48XD3Wr+ceXw4PKkdbLd7j++4i3rHYj07qCxqMpEdwUCeKdOk7vLET6lT4vbw8VupV0XWQ2kWfees1SejT6YwYfNg5vBbcY73k97Fzed697y3v7vYmNjy9uvasn74FVPSe585F0kQIIE8LwT7ypTj5mp4hzHu86pj0SNEDkwqD1FGG+2F9ARE+8+f4FEV2lO+qMfZ5POdgQRKgOBTgXznIfl/x3AlwvJJXT5wSmpgzetcorJkMNgM8IQ5lKImKudmurcDCiZIfXShHML86egfwGmNe69E4qzXMwD5CdXDIX/K8/MpRfZRV701Pqz+XHP5mvP5suBxj2SQszOGHEURLQ1oeKss5Aq4p5B4gLB+HxbtjCUDjfm8+fPfKIsb+3ypYfdvKwJ0bIvZiUjEVfCjF7oAnoSkyhpqXrArzxdarRr9XuDug+NrQtKvDNLaFIRjVmJPWUWecpqgt8E8oLIy8GXc0df4KLIxMia5QE7buggUfyXXqUYmZh5yBVs+/a9YLUB4ZLM2anZRjbUooqqHoaSdZiKdmYovEpYDp6GYTkZ1jDxOxpybDd2pFeWIlU888Xjcca1KBdrqqKYwgj5ST/rEAdXmi5xEikeEo/7QQSd6LwVtBWiJli+zTzkwV3j8rzTIM+ZAq/4GDAXKXBFdFFitIwEQEF8ESt1PvOc1DnVHtuXTzuOjis3LkjmyNc8eQ6fvCRqinOoDI++wYj+P7FaHyx1WpVGGzKW/SqwP/XwjRIfItdF8sLxUhd5ow+EjYoAq1efxHt9MuZtRD+Hok5Hj81knHI6KdfI5xFnJNCQye6Y4+66UxC2gMz2kLu/Q55W6nPkb8zZTrljIIXRpInm+53qNXOse/UKSkD+D3sLGJAnouxZK0AzimgQubRHnIMyZorNUDxDi9JSvCoOZ7RAOCP72X6FKYW4HC3u73zf2t4722Gm0s7m3s7C5u7K8s5kr3PTJqFLTUSHvV1PQoNUDp09a6SA39l+xLGAVatjh/S8v1xtEU6ElFcTNn5izkROACaJ+1gUfgpHL7u6voXMOY+bNQ/RHn2C8CmT2mfqV+wRzg0wsgcF7wm05aIhYaFkaAasJ5FyRjwP36PCMh61tPx1fxV+SYrYB5UjqcOw3qL/8RNhGfC9I57hA/uq/Ek836cQBt4TPMEIhjZGR9j4oKAozuLO+Vm9XTu7aN70r8QQFZQguLufUXGgqHm6bwxiRzKFOHLOjRyfiy+LeQz0hf6syJeS7T5v0na6fp+MOwRT43L/SAq0l/H+wuSkfthkkp+sjuG2HLGE+y+rPi7/ehKFnnEMrmadPPUchEQVF6KIwshEFGRxLKoBqhhEHTlavEZmbD1C8OqA1CjkgO2IIG24oncyNdRlkUZMfkza2CUevWHfT4vKvDF5NWWba142tNssxnNZq5GAaIJjGflLDcxLiKUsq1l7RCqe18DbrjxBNEw05oHKnEaMXa1U9qfySII+aDWHg2RJ7FRjwRhy+aO9Ua8gMiku9SPgqnGFSFoMa9I4zUxtGfN4xq0mRKP21U7ZsFttD+hrDaQ1m3yijRqWhPD9HHOYn0RMjb0zc4V1RaOI2jsTiUZx4Ju6kQBUOqwLg3scIGCFkeu4iIH/PzLYM3vTxiVc3k0tkE5DIRWxEVKaeWAg0omJO8r48K/fgfCR1So0hBAB+N5T6HmjLjp7oj5w4/at4l2i1RTOMbvA1A6sEpjHTd0P0VuGS2FIGU6gkC77O/xYis+nEr22ER8fU1vnroqNIdl46cEyd/lnkfRIH/8Xfqlj4BOfdhANXUWXpPmYZzeXoSgTuKsqV49qYvybeKDYXH1ZNSSnORHU8xChSFAkDXW07Dxo5KndJVkm14t+R90Iz7Oda5r5EehEQGKs2u8PG63KZb2fnB63ZieteCIljrj3wQ5DXBuX77ZuaKDcRsbEpvhX7Av0x21z4k6mqCShWeCpQKWy275k/9YvlVSlg6HyhQgwz4i4u15Mzl26axtLcDXOIhcaleOvTLIm5ej02OjkjqTBEUj0fWOlAiB6xTn+vXpEI+AlLRIL/idE48Z0jZvY6V8zNqCRRdIJs6B2nYIFG42Aa4gmiDIRGEeIM9YWa1yeh2yhhiU6iWv032yN5iFiYG5I6SU5V3CYuQFMq68H91hryUyQYFbMomn8nVuxtU4b+h6CkBp9w0dJ3/A3H5yRzaDgDr5wKEXU99UJv53oWl1bAIpsJgO2zFCiO1Kb6mEAin9mOGdqEPJtAot+kppCQIIqbRKaJ51IGkAv8TlhOjUQcXRVoYqbUFMPkJ9EhhoG6vT0YkJxWerRhKSxTvmiJE3GFYQNXTQLRfTCIz0qzXeDT6epT6EI8QqCOP2YUx6Qjj/AzLUaBU2TgIETBXg44rLguPv2tpXQ8LVuZBSyi0exF+NRWxFawXjW6P127+fN5t7ydrG6sZ1fq17d1vzf3d83y62Dnz+Xfy+v7gyUZqYfk7ByvmEfab+9jKMsLR4ZR4UkMpACDOdcahsamyvgghNiGIHGQ2CErEf8zhihKjoUKf4A7MYeC3LTSzd7x8vXh0dYi7v8mM93lq8eq49+d/XQz8w83KW7w6UDSA/vr/eawVZto7qUfTza7921fzxu3V8/9B9/HN166dbP55LZzq70/6NENmWxsbL42+B6a20/PdP9vbfYPVjP3P3I5TK3/e8/2ptr6a2sRPG9vJEurE0M1OKV6BFilkzIHdhzGnx8nbLhPW7PN43bLdtr2SgKYCiOcZLN/Tcrubtx3Qwm4WrwHkC+EBbgsuj3aod9w5qUiFAz2F2XFXSTWr2rs+otgUFoXjRJS9mkopBgfdOt9Pr19TZHtHiT6lSUdEpq9p1zteTxOYgmx5fCir1TsKjS387yVl+Ou2AxF2SDCIsHVrSKglbWMZ/fTgGZUmrqS7LEeycXV17BewuPhy6Ts8GQnOlOViark9eT9cma7LQP0Hzd2EJqjNRO/XL5viuecaSLxeAPVTl8Jz+XEOM78Rf/ja8eH+Bt3w3DWjIJiEEiXv6Q+ZBRhUnJpxnQzhDrrKYZmpR2Cmr5yuuGQzpgMqxNkjBXtDN576FNlwWRlF2ztwC9N6gMbvp8qDc71Qp0GsGcXW8Tw+CNix5B0+Po1TRpi9Rdoza44kdlMNjRuLwaaB+wrZg1p9Zgj4eCubArt5kVJlv5eZpuKMoMYwZNOBVOYUlNCn77Qi7OCe8CtM68GUeWNCDtiWg1qmb9Q/1flEVaWy5samil9WmwQkfgey4O6HHwPdO1cZgLPrGhFyJB0Y9JA3X2WnpfY639U3pgQYWOK3/oD8MgepwKQcQcoJVfkBSJjJkKtMqyGYhLKvn0WbGeFMVw0HF2dFv+PJGNiR7QhZ1NaANBWfB/fr106EIKvO464kmJQh1R57bPgDLfZQ4ZmoZcnJefnPGKPlcAFzv8n5xHtamITIrWh+D2bzlEmo/h6VhILqIWsKspj8KAoXBHij+uJxK6RnG69Qgu52qkNTtaKCMvkNavFYkEmO/EZcvQamKLtIELxskY8LJ93HaTEh0MeYJxThf33JIR14sDV4TDT3FlYDcgi1nsr8TMXrCzR/81Pbi9iPw5TfgLqgic149UcIFvMWTdkzQj904bDencg0y0TNG1OYgQ/quCqSmpOhf3SK+/WqS8RQRy3BGcP40Dp8aHgH2PMq2OEoL/SIrkx/f08srMye+dk3Z/v3edvjr4sbVR+Xmy6xW7J8H19cHm4CiTV6xC2deLk/icFD4aOOT4vzjFwWcAcYZytjKOY45mSyri3tQprmDwEGg7SjxrT6CUULsuuwhbk3kUNaA1Rn5D6fO+hnwziqK05dfZSXmcJlEWLDsVLdduAk9EK4+xvj0moqNbXpOhaYnZf/N1Q5nuAVQgsv9yRvlSpD7KN5P0kyU+i5CFmO0ZhRG6Ec4aSDMYFCBjmC4VjDpLAp4QCbj7xBOfjwQwTCMu1n92WgXuxTVVfqcr1fAbvHZnM5d3o5pBy6Hok90zSiytNVh/LVqU1fdm3HmIZ1aRl4fD2A3b+we1+uZgYXntLkgvFau9y2+1zrC338z/vPmRXd7OzRxc3K1l8zf7uz/73WBNFSeMC485kNS+V3QXWDzjVUjinmiG5I+cir4mCKFZXch+fQfwY9QKkjPdcKyIux3iUrYnjoQDKSvMx+m29CZTiAEjDNU1nWyLOO5h7SxkRl4mC7PaY6am/o8/Sfus5+UmsV25GfgiW1A/PQ8/8jIe/UXX8fGv4gyvCAT/ISuviYfz+/lGzWC8k42k7QFQkBsdQb2QOAvviHMhrxb9Mh9vYci+SuG6Auzokzpmg06XZ2sdWZYrE1X+COnIWXEqwTX44kkQaYBcIaGUfkHRnsqgc47RGliPv3CMUy5y9JyMCpSNL1hL7u6TWBqVgpg/n/XGMVgVThVg7C52B1KO2HbR34u00Rj33IXdIr6dy7rBtyO2TBmpqDXDdK3X6Z537oGrnh9JxYgIAZC6ploOSUDKBZ2JDyj8QCvmE2tb3K7p2DSRSB6C/Hr1DzuIG/vDXv2fm0avntStfo40hXerqLx5y/hVMaqYeyZqHBv1PjnaeDgPNi6qrYM79m+mcphrg5bXQ7VVfDgKNprV1eJDbbV5c3KngM4Y3NpY6zd27oZbDwvMvdlgH6wNq/Wln0FyuL64U1hnH1xXh8377OZSUq2evCj3JxcLG3INMfpbHebx41CsdQnaA0KtHDFsxBVd1olPocQIw4BJAcyZYZ/EFDSphdWs+svgTgm9T5/U+cqNnIQvCIkJujGU8lRfy4As3/fAHpo71aUm03Oth/4/zbN/buo98BGDNJ0DANN/bjrMjK8PKjKXxMt8dAwnojPPfmwtLaNFiBRrzwAnZcQz0oOUIh6jeRC+xd4UODEaTnIFEZjmFE94tyqD6hUNO/g0Md9oN84IoQT4Sx4YPMP538fPJjNCBsK3V4WAr3SiAPy+1ex1q0MCZAsI9tBAZieTNKWJsavIdhvEKQqM1NOpsR/D4kKRYmD7zE59IZCkntANR5EuKzhq/2V9iwZS1NbjGOl0CFxIvwLDFxjKUH8SRMoUm2OH6Mpy2tEl4G/MsvErRqEXtyRg0TBBVF1zTLdriBodwgkJ/VEJcZr8b0zY8n9UMBst2eZG6xytJQLzXRFvYY5QZuFzagn0tBS/tpQyeC8VY9gU3Zga463PClykxuuij53Ojcz/PHtwrwJMNB8xHEbsmkmywp89k1luLXGXMtsBT80zuIITEEh+wf78DMsUO6B2mkLwfK9+wWzTK+oyZi3Z/jSyxPCKMDiIWwqBZDvVNrSYAP2LZKOs4u6RmJVkWtRblUZTg9aaJ84aJ8ApUgiJxit+icBurLomSsu8G5iMsU+BUYv1AsDEUwFBaJ0cGIEFLDGmZUAmaiQ1QLLD+MrmcWq/1+ildV8j4iELmUWxeKu6L6NiYV4vDZkwRRwQRiozlHYIhD0Imzv1Csqs+bhEYw0MJ/a0ixpgP9Tv/HZO4gB5GQc+xfjGBLT7jTTUvgynE7U8WA/usDZFwLlRRi+eDEb6YjKytPIz2KvaXioiU+v60l26unoFFhXzBnj+HXaU88Oif3JQ9KqrIHO/0zxZKSKPIbOAvNta6+CidvS1f+6vXJ8c7lygrCuY4yEbDaG+YPMYMHh3p1YYmDjkwVx08WLyuD+gv0KwOMkz9JQijJNMk00DOpHmDZwX8jM5AG7E9x0xNY0VWTY3/DgdaW5WGCIuDqpQvlw/8g7ot5eojeHGBDuUyf5hbE5iHzLiK6bKDMeSOo4MeQhPvae4ODddRZ6gmRshBo4s9Li7y3JujjLmbMTPfLqes5LErTnkXBbyjnUBrKxeo9JsPHI7tfJJf7JXTH3eDWMXgL5BiOsqxwj0HsUfrjUA7N9snC03Wm/XOkfs+SFVn/paqV7XOp0eP5Mwgr59akJssZAvg5y8rm88tDp+aO50+GerctmonqEn0D/rsb2ffT2sNuuVHqT3q6wP60NuVWtRaZldy016mVGEUM4VfZK8ZWKb54UIosz38hHNbIo/6dGnhArB2ak/TGonBS7FNp2J5D5SpA55sMs2Gx9n4LefVc47oAQ96N2AW+O2Bhx80/CxdJOUaTUvaoeccsHFIi/4gE1CxpSxHhZonNgYKwvFyDL8gXzJsJ6V8Z8kPrHz8MmXXoPDQlQpiMtPIwr9nF3cyl9dlkw0F2e3TW0VF4KHkyflD9hhVYQdPkirnwpkHmP3UKRdfgF/pEtD5pxAnB1M0bKsKy9SdbKvyh9DLn/F3uAsvkKX408c+RZJn9SGiAASeaRS4VCTkRwdh5/SuL1sds5xpdanD4eIiYectR4Ct7qoCcJL+04rfdUhc18cCQ219mrtDmh6zmrUwLYhmCVDMFotFX0zFf0Zcf8RTw+6APQK1kKD8Ve/hAv4/6TLv6PD8EszHHXMGyHH9FaJ2lEKAj2/hKvpWfqE3koiJK1JvT7TXt8MiFxkGJkc+CapqpPPfnP/+/fhiiC1D2Ngfbjq5Hl9c9yE00E02s4U8hogs7jy9Qlcwk3mVZG1UGJrVbrQwk+iDlbKffZ5/aMQ2ybXEWU41ObMO47YX31hXPr2BoPUlyHhSkCDIpBMRmpXevYkQZFAM43ZstySKY5KfJdJU+AcIt0JBH6Uf50xt2zqQ/hrWlAho6z9CFETEl8k2SY5ZWlmkpOW+shR72UjLAJ/ypO1dFD3HvvH3/rtm+r2frV798/ewUprbfV78b569Rx22Ln0Ydo6eEmkAa5XT6STaQ519VGgXCWbXCUqbF/s1W/lsJO5JDGzRI4DDd6kNv0FM7vFcDzmeGOy4ujSTWb3w2O40osEfCNbHy11uOKUPkEmRit8ZwOFs4baCfpnqnbSsSU7jif1EcxOXaLjE5S2I7+v8zmR6iywUgh/yNP1+7j4vTKzsbD84+L28uhh7Xhn2W/eXnZ0THjM2MMMIW/SjMCDi5EWRHcWnuZLyWVMjw82+mfnnYFwl4ok+rHCduPl0PK3uDRmIvpKaFCSQ05GTUR6UURdcMSLRU0WpwsqSDuSiGeB8xhTR5jAcIweajQCjKS2LOsbzAKGbFEq07hKJVT9mNVjpkgd7QKipD69tre3fbbP/jxbWF3e3EvbYa+UEfSn9BM6DZbPkTTj3QTPQx8Ws9OKFvELp2rlhBnku2IcgHux09P8CGC+5t9j34Uy1MWdArYYD6GjbJAAdVeO0tAuZRrS8kFnBQkPPk7rTqujAuWpyGl/YnLs4zjDMzz/teVTDuu4tfJ4snecOfJ3msd+8fpkGMevDnks9ylJ3SfXceFk3MxCfJRn44mhPohyDMcuAu6HKXzPrB1/vd9oNvZb3/3gWz0ofivu946ya7tXi+tfL5cf5MLtXgnCvoKy5HwRptGlvEqfTEym6kNvUoWU2Oov8PIWClXgMZnpV60M7AHJls+RBkCQpylIO6VXlpt1/NOIrYe8Jk8k8tLokg8WBsyCPL9hji2i0b92erV6L0kHI5Z9qK6RFIC77Ejd8rxTewjTlW633q6xEdCsKRSMlQvPBU6nVwtUvZViKwr7aYSN3KzS4wHrrNdmZNcLv5iLwhKPBzIIeF7GnbISpriZpzgVjh/rw2qnc01WlQq7IyCEB/wDgYhSs9wwunIjTMs/ZZEQz7EL5rKC2kcbbC4fl4zED1RBJjg7XJU/pU8fYIMeIUFLNpfRY2JSVENeni/OUpZAjIOcZtN/NCXRWBuyiojL54Pi9Tewx5CsmTEqK1NGcElsOzwzKheA8C5l4Rz40MMAKef1p6SGICNzD6wIaiJmEKoK3oTwviKerc/tH3kpLjkIy7pE1fF819OpLveKG7HY34wgBd0hir/jlP6RGfgvJUkuLpp7y95tvliY2e0d3F6sVzeamWbz69HMydev+5ruhK+s+/7zQDJk9se0BA2YyEjmFo/4lJ3/CbJ9GqOkpo2ZQFYqbfyxcXM3J5cJ6Q0konqaPndIn0IulaAN4CIQksKEAwb+PLlkHrljVOkhStLlQBZPN2OQb75MIkhXCIrKA+EgB3qF21K1JgeUpiJebvROy+36abn6z2m5eXNavmmclmu9U5tB4dk7aThHc6EquqlS/m2hvSqu556rLznQDNoUo0Ie8RKCYFQErrp15CMlUVPMgU/1Shf4SYiGOuJYNNyP5ZzoA5TUAPKOSuAxvFsQ8WdGf37EtgU3PrccltlMZyMZwlIjGY7QfHbXMdxmBYs1wbF9pI+guwEJtQTRHbmald2QEB3k5JDmC2BppW+q6yvSUUlyYshX5T3jicGtbVgxMqE4BUX1XQr7HUj/RpFKvBJ/uyAYkZom1wmc2srUI9J85qFdYQrycOB3YoSVZHfYEssOgi/prGnFjlVWdXj6vV32C+kFRDk4/5h2u/b9/Lq1ubCwsPSzmG7tUKBJsAG+dJXMByLsOSbh91KBtijBOjdjeHrOYKRlT392YxCd0fpAwPdYQMwLGmBiuqVohhMXI2ExdCdyZ3n8r0TQOoCOGQaXiHGhFgBF7z6/nZrC26ampr7AfXhV6ZiBe9Ou96uVrvFOPwTVD/nih3z+Q8H/kPc+5Gsf8jlD9/SD738o5OGgQoCH+vgTTytkPxSKH4LKBz/zIT+Dn2fxf7kP+To7UboStnMiR2ro8SeTLMiNC/7oif5VhS0YQ/ydErkYM/4D4pdIXjgSajbfU/kXcYpRpfiT+SdB6J/dHyydhNe2GREtYEDpaBbmfms027zn8u5y//k458NZKBXGAAfOxiUk9a91y5Se2LN8IbHmPIOOd4HepNUI/RC9k68HUDhBqZdTkrNiIkWWooKbn0JkgB0vGJxQfMectCtZMl42GQTKDmCH3wGVbqEA1t4l/JrHX+v4ay6JRUJwQBZlVvrwu5+RdZizyaeZSS87iq/PQFiWiDyghoE/49SaM/CcJpxTADLg34xyNuKw3pSgoILtMd4sm7LcP5jXskURH0cFvdI6t6BOWjjmMG1cjVeR09XfJTI7X4yZO7Ge+/ickACagZ9dvSJ8sWXARAFxyCBcOTcJyEqw+tU6dyS5V0O4bim61XF3S6g8++IwFaEXNDeCvU26aOY51LsJTdQ2P+IoVLfljGoPSirMZVxgZ5vSf8+8I44pPxAQqqP1y+P29WV95cf9+erBzfHj5vVQAK+qja+N+u7X7P7jSv/kMPf7h/9zuL5ycHe+Wrw6WT14WF/dyK2vXe39OLxvnrd+PJ5omttePuPGzxpPiDbhTN4JMeFoMANIoz8Kz1PMDmOPkOwSs7CrUbdBExKGnBD8KQSFcPOzYBsJqew6Br1BmhXZ/84QPKl4SzeP6yvX++mTdP5g4XG7W/n5/fz8sPOceIi7p82SXm3Zck83aT1pReDgTRkZhaKxGLG+mxXbeFalH1BqIu1MpDtiFTytL/YSwwlJzhEa9ImzIMh3GiYsTyU0otxFU8DT0kFAuuVpFPEsi/QqXicVfpzmnZeNS4WbkT6t/jUmCGYWS1pABL7kuJAqoa4AyuPhzG8UfYG0ev8DnRUMw2zyf3ShEC6BlEHeQz9iJj1nejtI95611iWCjAtvRBLG5fG0xU5u+AkTUCES34bip3YNcAzPzm8azZosJpGpOHFqVuiwGs62jLNYgIs+z/ji6Ho3wdZd/ClGFFbT8XoTrXlZYrrmNcdIGy22MD5a1Q8RkysQj00kZPJMt41DoLy6u3hHqFyx/kCxknfje4jvmlpRjoY8MaotSewjupy+bPQamVfEdIhtEPgmuv1mhRmYfS3nrOrY/iQm5SSXtfXJRLIlzsokkl7ddEZ9DwVe0lYdXK0+gogRrzCNKA6wbmeXqxwed07abFMPdrrnfg70HbvVYOfx22Lt9rhZfByeNK5aNbax11abt+dNUHxsPtbWfnQ2/GIjKUiaRIXR96B29/2weXPs33ts93/87ncfh9VFr3ve2uzXwFx4KP4zrAULgwq75PnaATMuftxW2j9uhVXAoZ/f21871cVqd1hZW2gPAQK+8XDdTapMW4HXvQXd65PDzduT1f3OUKYP6y1KF8qL+rrRKRYe4r1xaaPEwABVsDcWGyaCHUVRyeaik7eKmbBWUuGvZC0rUuGXknxVDwWAzlRlhgyzrXOrb+qk6pGPsIWlkuVprGjmVKSj5/K6EDKDG4dhWLuvnFdrF/pay+cvNBarkcLErOQCd5wGu9Q7pSwVcSISfyDnaHaLFyM5x3uFQH+Bq1dgzmClN3bLM37NfzSDtILPtZ2H+uHJ47m/2TsPvjaH1XazVfWLvyt+8Y4Z2BfD2tpG7qS9eXvsD26rzBy3GRSxavSkyyz1ixP/IHPkb96et3ea7L/HIZRIHPm1Zm2lyGZKLsMumBliecRBsX1ytHPhpGOMdp9vY1lcKVQ5XdyJEXmiWF1DFf1J6EkMAtsVZY6tH02HvYBn+d+Iq9LDlzmTIiriWs9dtlgW1VzyTSzLzHhayLeir/QMDg60Qat7ZpSNWj5oNIybcIVRY0NecEUvrOG/9MDy+uJhfIk4RXFtQpaTi35Vadea9Z5srrwUXUQrVbS/5CkXjp2Hr0I3HbM/k3V3na21EwElw54ax0aTkui8JAGQwN2W39jBhjM1fnEEaKE1/rdxYx1FoTKtHiJzy2xkkI36SQVG9CVYyYfxKJrxmBaPjqELrgDPwlokRRSEnegyBOYp0s4YB95NRjPgtGLTOBPjSpYKxbN/mdFgiCwnRMkfvQgHQ+orLk83eAk3lu/0hklQJQo7mS9xyR0oKcH88KyoWsnqarZvX9t4R3zcuIQOeE8KOxoGW5o9gbEAvFWgt/+g31zCybPCYcwSYs8cbAXcXLMG+DISQvANZ7fIfN2Y39UUMziLIGmXRjWUEdAX6X/mjD8VniEW5ZayDTs9uu7r5RhE7sFNMygXJwKkMqdoZivjTAweKASSo7RRq1ZQEYEAZzLvPwwf57JOVL2lgStLj1y3ZBNbHH529n19E3PxBrLLGaRRJsCMTtiJQRo4BquXdIivcNrMnZ8I0iOm3Zi5GDhNtWKkwBm5eAjfTg4Ohd/do81asZ/GsmIJU8Iqn6QCY80d5e/bXedsMyya8lHCUuA11YiYDQzgqYzNpzCzA1lRQwmNu4sW/keOcm5oFGPk/GKBKu+1/dDAwAK834+WYfgyBmg9r3Ed17wDLC0ppyVL9pOVf2HHRmuQ0O+WklrDiMWn43DxIrZ2lAkBEZKmAmosFq+i9zKR51dhnTrebb3+8I/XePx+cfnPUfH+Iufd5Vdv0vmsBnMKcmNDtbrfjxoyKNUW8V9ejtXpNm8uG+2zWqN31q0Mrvg6YWN2uJcD7wwFV0rDRusyKamAELPEBUtKqFgyvGxcDM9b3eQQMiqtOh4xuB8MW7VkUlnT/EGwyrroTJU9u3PDxZj3ZYTyE1Ab/tODOvDrzeFG4+vgZGm40WJ/SKPeoxDDkbexsn9wsDLcWCnu7hys2N8jOHlYa630v3E8ctKOMMqXRLlyWVlcJEvVsYI/A7ZgGzTbk9nuzAz0JHZxbuTm5FCL6mvOj4lIID2vhegPCRskDEOaSi+SCvJFGBDeB+f7+DwNgF/2DxFgwIJVEtgK2Ws5Q/r9P5l6K7m96sXFfS34nlk8zh3+fqxmfiwt3/0oVDODpcXvB9myKdErgkT2qoDGYKAloTQQaVnRVPxpU99NsKbmm/v9lX/urhf20xv79cV/Bj8bDekCFIlDhPpMu/9/c3P7NsT9y5+d8JRZVy0N72fYf9MUMimOYl+GwIKR3LoedTj2ize11YOb2pozQlEduoIZSVX3BFGYtR12XG1th/23oAcTx09UNLW8KPQgakzZVk/U2JGUZzashTu2fascKpEW2gX+cxpGzzFOyqKoQLujHLxoQeUjlgDfF1+SSYE+o66zB1nCXeLOv38516R6Y04Mm/ih7eex7dYugqdwTF/ExQsyGXc5PeH8mA+WRVh9nDuMOQrdKXZGjuGmNbDwR6Bryn7SHyrhYnO9hpgI1WWN5SZhITCZyRbehVNansjmr4RWnbNrXM/GrhP88GhlURiv5hagpAwKrb9sb/sjgnwztGLuWObA4llgc5nJWXhT9kLZyjPkqdIsB35ynDM5KWGgNZqK38xmyeXD2ruCjB9RGtB2iGS8HnRopA+iyljGshzZemI0S545Swby6IggJPJ3XZxXr6qnKwgiz/fyxIQoSUzip7DfF0eo0Gq9P+6SBCSJk3Hoqb3I1INNQ8bZs5c1/6BZax3067uXXWZvbmYqh8Wb9bVakxluzfO1685QBt0bX4OTwwP/5PA+pwLuAVmLxmXkqdbhGQXpDg2j3JgUWempJqi3klD191Hwnsa52jJOHn9cPpu/yAeFXL6Wz+XrhaxfLxQKfr5YyOZz/oxfCKr5Yj7PPvHgCNbFAfs9CGq+n58pZNn/Z4KKf+Ff5AuFbBAEvp0XwzBnxyLT64tSBZw/fNZJBZzqVaVHo1Rq7GBYU6uf12KokXj45+nQ+8L7LSf67Q99gISbnfPb0kJ3GGHnfLi805IcAc+YVIOvD+yczEnjsrFxtN/4vtq8Y+cyP3z1uLPeOPDWG/fsYmsbXWZ6tL4trjdru+v9ytHCgxpQHIy0vdpsVVsrg5Pdr4/V1o/ieusqw2yS/PCkvXF1frh/WQ02fm/vbnSYsXK31Zi5ZTbPycPxoddcX9t8OH7MNNCWWercRswYIzISZPKGEJDoNoPGw5TI1Qk8+lGe/QWlukD7AfF4JKNLsBEPoqKIUMrkMnuSy+SqsBhmKW3hIBFjeD5aE8P3gSt0INc7gtexB8hOitE3GebiZmvB8DZeUCc+dgw+ZB73HhbvbvYawc7++vV29TLzo/Yzs9U4ONl+NDxUh3VjWqUBydYUI1X6vMQLyzPYKUD2M2x2mA8/fIaWDz+EDaDS7w+7d+x/NUI0dnq1Z86yojAqzZ0QHl78lcVRHhodxM3nMGLGfUADkWtC66uT0yqhjL4j1/3yYEKncrT5eHL0s0NRAc0SfnlkYUzkIKvYeheH9dVm5tuwwm727aWhhYA0bKIMDtocgk25IWhktYpYZU3h1/VSPwpeiNCN8TW7n6r0bb/IkXpE7ExHg/pMioUi1LJSo7KkiRzFoHsCFK9x0MG/vPycV9/rC6eyZty0JC+S6yWO9KzbHtCCdAEJyUQjsP/bu5rYiJj502dn9L6tMnd6dTNgXnTzpvJwBS7375r/s8OspvvueavfAdf6OKhdR4yjWJpFF8Xi8Dw4aR4FEvt7feQDDGG/oy5btHcyV7cFkVylXRT0GvknkVDLGgUnH1UrKJT2X4G6HK/zlU1WYxU4d4w1T4l3iRnDuwwtz5msQQL70nnicU4Bm2thWPX3/c2VosJX2TQNmh3lc/jKxtrm7Tlb8tgq2j1usYFy7t8z87r5WDnc6LMrFvtscexW/WYjad79pAXwliwbk8HX5nnjcgjQlm8L9sLZrj1UjnbY+As2ujVoE7PRMie7lxHERsROl32VE7FZ3XWN0ufpi6FdqqN/Bwkc9HcTL37VSasm6nVDUBY0+RpzgEpN6ZmoP7imjq3Wew0DoLn8M+WCr9d3gx9PoYwzs8GA7yzmLq+eTPI2LwUDeOMDIcKVRUkab8ZRsfx/pOAUJR9yBMxmea/6vFdtg8mbcet0vxBfFVp44v8EYlXSUznRhii1J91Y0TQ59Y+VdpNWyRVM5kcCsSE65qXSFIFXdPP9uwLGRJIZaZRuWFngaSHYS31QyBiozJte808U0MpG03TiXZvfdTz0mOs16XWFs+YnvuoyfT9HVRwPQmMJ4BsK++SdvoArDKsRxnOFsccaDQFbikHVJG0nHBhB1exelElNP4Njbzlz86SJrOIPgMHOaAVSKHjC/0O9pd7+VZ4UyAvV9Hr3YXWmtbXUbnq7W82jH0tdrrwE3+eqsN1uF5b6He/+YKHKfILesLvd3354rOyu7WSyR98GzHxM0imDwQGINbFTDi4e1qqNNDvkIHdeqQ+bF52LZs87qOzXfuc3vK//XAoVksJYjfNQo5gzXjdavRYB4P9ab+k6VP2L39+KKzu7meuC8dAHF5ntB6+wP9i/+p23voG+MDqV9+hV/d91QvB/2gnnx4O+v7KWq19eZNea/czlKjxTFj/d9mcWbjPb37dqW5tdGECD4u7NenYlXSxsVy9ur39cZxcf4ePCyeCf7MptsfCjuH3b+dbrLvyuPqfOPr4TiAoyKhH5L/K5G4+Dw0Pv0X883k3fZE/Sd1X1moKYFrobl4urnHgJWtmJJNM3YT1tYVLAayw7ceBpKyAbcyni5MV4+2caM816WyzDSYFQArAyh7GV2bGnCrlkFhZp2DraFRJCHhXoj9i6DYsiN35Q9MbzAP7NE/pJ6uF/9YoNzz3vysWHgs2MN6PgJnj703rK7s5Jr5obrM58/5HPpIt3j78Xvh63HPLVjpykSLYGJPcS1UVNCPK9j6fUnf5IPpQObJNHhM5cq1YT8XxFRGikVuOeYJZes3bjlIGnExOmaJRMPE9wwFqrqRdFKBIj/AcaegsRcjR2FZreBUR9L9DTzP90MSDzJZK1dEhWapJCOCNpOs9fNDtQcmqEm/XWzl8IHShPGajUNMUmacRM0QuzQLEB6bS4OAESl51BJ6nY+Wb1HNAnyNOLa/nmQfzXTyYXnvjF1FwAQ3G4Pdwd7gwXk5oNSNERj+DGUUysncjz9bbNYpqcl66MgwsjipHbttOy93y12MgHYcvZJ95fnhHnfD7NPN41NLxu/kgk9QOcJnIOIXbJIdPsOUWYZb84tLhDFb4hObyI2c2f0/9fwBv+c52+uNmueDf+Wue8+C3YL2wWerWL49zWzIWf7UndX7DDX4I3DEgMZSaIDTC9qoHgZWz+s5k+ut4ubDUqa9mTYvNka/XbTbZw+723dHHS6jX7D0M45vB2sLeyeXGYPqjmD7Y6q82tr5liRsQ6ocYLt/2XwSYDEknRpb31rPgLykii+XnKMpU4VYMZYh6XqPc54aETKiuhtKqO5EluiyjroaL6uD5yxkiPZ/KdwSD7IPiclw4YpcUB6aJ4YgtzUYeS4iZ7m6ZjhS5YdmTlMiaTT8DkDKiHACkzcRsKx6S8DZ03uVxj+ddTSDynzNy5qQ46qPYmSv5QrO6wf7UL4S1+qUPiY2LGD9SWTMm0gOPG4ntu66LF5JniAfijqmn0Wa9PA2AHAXFtRywBsddi+owA7NZk+rfVj7H7vOve8N5dVoo8hEwVp01AGi6WujXkn4H6xybmsDcYCXuQhSwRiN2v9wCo83Qi2yjGymZG0pke39O3GijOt3oray8h5q4941a5fxln2hgNP7UZ4NjRyRjii6ueuXA0iSDGZYw+l1Z/PulYrD1nUgI9g9joqNF5qOice22M9AXOL7wp28NvFSCGUb+rPW+wWy8ZxVBexnsiax6c9UnRcpoEFiwkuSzmhAbQlKtaLqZcKAeReeqCrF1BmNdi9K6CJdFHMjKb4JTRgQ8oxBIGamE7a7TZ7gT4fLbWDPv1YaOfnI6S+isqIahuT7FdoyCDYyif4vsOu1bNET58ZfFZ/PCF3YTGXJIXosrOrd8PepWqFWp01S/RcJk5q1U7IexwSGvpZTG1QFusQvlljWV8MvqRNuzZD/HMvtsVer5WPD6h2964rTaLDydHX2+r7Z3ueava+bF4XZAQIMF3QIzp7XqreXPSur+CHFgkcQfM6SdBl13kpH98tNP8trpxFUnmITxhtZZnTWidr65kTnajPOwKlQD33rnu5ndXB/f77Ohh/fqqVz34yhowOBjutrvrlWDlJ7tZL8lDqMiOPXLOt0DIpDuUv/KZMZVGY2BAc5GlxLGi+bKklwwHJPbTr1MWyATBMKjmnDKUoujZqKedtY1SV2QctVL8IBNXRo9EEcAT0ehwvYYgNnA0j3XLMMp0KQcMaU+8wxanuUIh3/1YM4COiYrI2E/VeWmcLj7nfQV4f1r0ZbUDbxapTqlqDWls1FIRpafQqirE7MEEbJ7NHhezXcIuNI5kZvXpadak1Hv1ywb/nkPxEw4nPWntjpEsKeZEVY7URLO7SgBSsgSAh+90SoNnoNXCXMzm4wKSz5Lr/SlShJgMSp8OH2rrBys7P4+8g72dg42L4e7Kzv7+SvFgP3OwO9zIXO0frHy9ODjYXLHPrB+udM79mca3XbZsNb4+1tY2gPviYVhZLd6uN64vz4P1S/igcrSZqTYuYW26uj1/WO+us/asr7EPW0CGsW5hRKCgY/UACeSGJ4dIQSePMASzeN8V7AVZc70ghDmGBmdCIcYFxiCey6O2uvIwjOPyOAo2M8dHGxnZUA6ikVQ3B8Xb89Zmc8jZcS7YHX4PBTPOkV+8OfZ1sIyfkRw3ailf2/SO25uZobZsP9YON7APh/quUA0OGtDzTvCNtSBTzDJS6h5jvaO/x37cD8MaFZkZykTcZoiJCoplyiqdeeaymDlBb594XeKO9Z1WPOnHAOQj1pdVwyCnUVT2P4Jnn5sscG+VQGK6c6o2qEmZEhAmPQXaJcedR/ZN94ZEnhGoyj/rXzUuBvJDTg2WHu/00vfcV413pSUJMd/a2CYygys7HTap+Eno+dSkIhkZ33NDPpxq4tqmHFcfyJ940OqyXj8DGuhkiT+5XvkUsRUjlU8TnFZT1TW6OTBiU9pk0XF8C4rF+EEkYfdeAm7Lv+ZE/XBoebKAW/FlRc97Dg/U3F9tn46JIJGDm4W4uHs3IuCOBOrw+hOtLAju8RQQzC1MiM4WBybp4i5IQ45qiR3Fqjxi9KIWufZLyFcI0MIQ8kRnbCA2Bg8GC4t85a9hkpbbqgZRUjoxTifkJWVIsWTirzuZJgHveTteARuCxNlj/XtCij5olyjNihSRU5oNZtSg3iNqCv0gDEWORP5PGbIyEmh8wXtOclfqL/+FFDDh+BjIi16mEWQXiYuXkXWPvZRjhobJeItvzIhOWErcvOPQlJ2JcizE8lXW27rZEX5QYKfoKQrwg4d/ENgU4pZL0JIEVbemYATVwhEbrc4iN+nNAPnqmbxlZGSJ9IXm9eMbDUxr90nUzZihFRWisKmxeWflBVVxgleDQ5dR/himgtBysPUbYgVT+6m7Rg15C/hzZ4Ye5NGu6o3Lq0Hk4/7gATchcd3bRr9x3mg2kCkHJtFVo1ZDcBk0u9vpN/guD99Vzvud5g16ltwyoNZzEQj+hJiQhh0zEeL7mH8t/PJP59BsgnpaLOkzBumfJe9LQy0zXtMaNo9oBfSgc9Pt1hUsgagKZTtgnAVgLY1bIOD2T2w6jV7hkb2443TF28hGh2YglBSAakMCavNSSXyNMSOvpKP+++AwpziCkVtPc6CkMoFc3ym8xDnqoEWGXqJV6V022smYkRn67oHcr/Y6zSZ5xuY3rxr4rkYZoxe6Q5Xj5TOOwWPEvKOp5VdtjWWnBIS1RNtyBRHT6d/bDmMNQ3vwoDSNLVcfujjqXVGMMQKLHqm3ia+fkQajZdh0cF8M9ddT+nxpVtHoEoemaVhaGV2VOZgoHpeDI3Q7Vq74ed+gQeHLUNgOe7iH6GRpJVun8wWjqj+2aBwlk/I8CmmUlKVA7EdHNUSIwefMj8qwsxFagJpXgwuYtWpT9CFd9ZRvInoIUb+icj9cIy1wU/qPy6WJB8WR8xx9X1myNEtudBURBKonignGcTtAexMySTO0IipcG4ZUR9xE+AHp52Qj8YY4VYdIbk9/y5omDEYRXrNtRHHztOpoGQLfrKb3Is0pS9JsAkhLXYcwW+YS4JIL2O4HNB8zEfORWXZbZxlF6zAntDuEGPfcvE4PgZbg/6iUly6iy2x8ERDh9blheJ8twI8q/LiAHz788MRnOUpRjUItuZ+9AEWUHP4MMhBp4ewJFLBwPhyCBPPMGJrnhGYJnYGqHCYmuOr1rKAgFtMPt1StPgAzVPfN/v2w1qnCf/fDbu0CeaZ+dy+HwD4FvFP9f5rDx0YXmKguh5ePUBo7vK4/ABfVsNFuDH/3uTdcDpN0bwwZcQWfgGRoZiKVlTHz7ueaKmCiiNT+ysbGwe9LrBKvsX/3rX/X174+VI6Os8P1la+7uwfH2WR07r5w0/OMURvpetQIhB08Ngr3/o6zySrrX1Mu11Dw3cHgStVnUP9pmT1gcsPozpCXfibFBZ7UpNCdzqKoAi9BRKLIPdB80Z1nj+fiVpUjMbl/oUSogbLMYvjItQGMKEZz5EuDkJhDAedFCfQ8laWAPGzZ5JPSKpfjXqYFKhMvNm23QjpZ9utGDRicaS+tZH+uOewOSycnK73+/nr/evco4x093C3f/1OsLv143MjNtIZLJ8den317fX3Ivt2/+pm5u88cV56Di/OZVvDE25ZEGVwGWYJPcNHEapGPA1IagJX5rtGude7CdAcrisSb0aU6+YF6LFQXVlWSbElT8zihMTyCr5/WNY5pf0yCLLcesB7U7wfTvyu3FU6NogLPWeZHUO4NCX74p4FL89gIvyp0EWu6eBr4UGPLIBWZnENoZDww8iWoFNsZi/PDHCGSoRb589WbRlsGcIPx0SKTdcBExXwWuSaloZ5RGuramgAXgX+/lG1mzrHBa9vxjIlaG1gYM0w4yeeREjT0X+tt8K7KSpF7GzjuDgG6ZL7H2oiIkZPz0vTQ3bzjv8JP4wJUWruM1HNWMJRHCB2Is5SQMOno55RSNuFastLMBBeRbEzg/UtEKuJlV3LF68FK7+73ws/72/TBtoxyzcTLuScEEd+/LXh4N/HcrfTSC5J9gcd+/cR/gRtl4aR0UFOs9o60lPU96t9EcLTLyMlRMJLjkqlNG8AJMYKTSjdgAuFE4Pm6GPPJivJNxSXt68D5uIKtl0KpXtzc0GOpiCmSRoQlGZTFWP3Hac1oUKCTrClACr64DMz6wk7LS6iN0xDnGjH+mLzIn+i8/nmcjh7JRlMP3WqUQ+lp0skk7/7KRoskGO/EvNB1dS/KxEMiRl7RKGLTdb3wWWmUgZA0+2xqCxZsEJyeLv+SgXktbi9kpBFMBDLSH0PQke4RK1vYN0xA3khHcYFIeZG4S5QoyACQykTKcHu4CBUgSYRq50dRcVaLzUXf9VTQTksg8UMdu6YjPVxWDITiFp71bQonibClI/ssN00rw7tkmCrpQMqUNU2CkZnvSpmbx4wnEl7/kfix1n0oeywV6JTYcUL6H8Cly5WkRZkbmjfsxRl2DfOmWIPQ6MGB9IVnmOdRJTkRMZ2MV2Tg0uFwUjx2vxdd1zXSaSNtNpDwS0Rz6yU+K/nMXx9ODmuEUlpd+X1isnsc7Rab64276yPv6/a+tzP87u83vi/uXOwvF/cOVoYnu+v9jZXizz1vc/8wghfauusWa/4VEs7U1ja8k93r4WLbCyqrzf7J7lWm2j5oftsbXhzVOsBg821t56F2uN/dchIR6HuQvZ6igouuVqu2f0NrM+1AGm0vFe8qawuxkJzIvbIip63bgwkRy+EFKyilNDIXaXdWJwnoPUOmwh77SMcuCrugQA5zIJACYZf4JPJx/G8C9EiXl7avzIhwA3h70seWsee+oT/Kk6GiES22gtgha44Qei81D4kyCuYb2oxTU19kQ8wpTmTSDjfpz+U2XR7x18xj9aF+sdE68Su35pOaxpRAmszkpfOrLddirY4Q/Tvg5XHGPbPTDeteDEpb+yYRW0maEPa2VnZrrAuhYtOHl0tk3r/mOC7mM1u+KpCHYiNkqv7PTeNWa2avfsFuxmNJKW4Aad9nhOeWy5hZGB0xi9Ii1sJdq1802nUs5Qu1uijdTqFD0N6bRNtsaX3n7Cyy8GkpLlNBBdZZ45riVIFazmWcJIZ9C0kObdFZL86qlWbzHCRP6cjPUDmDriuzljp37FLt+h1WgFbrXYk5MzxMw/p2eNHcEUdNES8TsRTEGDE8FhdGMgzLYYgCbvUL1D/VopZ8tPA92CJy4/odDqmCseM5o8MJNTOfmZxy40tqW5hk7fDNt/SkyQTHOeDUcE3UPJQaOglIBmi3eVKynH4kT5l46Q2JXj0U7CkYMUmYPH8k31GIZoFKn5YxqLsTCeomSy9+m/WVfO1wxbvNXh/vp3dvC/vrzdo/lV72Yany8M/vOyd8W4ONWs7vf9Om526qcdIHRU/4g2NkSfQVIFpLxazhHbCHhTVsB8CMLLWe8fZflsS2DolxlWRT43jjIu2U64lanvmoha5g35ZlhTV3ckVIW/SdFO9TL9BlBmPTNM9hR7RhjCRF6JnehnFOsqxFGQicH5RjECAapSTiOS9Ms0DssXCVaq8a+ORIR9+Ma5aOHYn8MqrSv5AZSaOiSBBDB+MEe+I+uxwE0PqDSm8gOn34EnQA5EzBBmCuLpBnGjUL7DnX9va2z/bZJ2cLq8ube5R2ExmWQAKOQ5MnlZ847YXpDGtiNpNlPzc74KaudG7aNeU+vbdFDW2Oao6kta7+vVOtwMzDRd9TMTvbhkUxFCO3kxhTdH06F19tPSFpnQgDR2XXhIYTe495B9yJYtLK4mnZhBVGlpnXnZWxz1kZkYnGgoSOn9hGHG2gLECkSJsbHc7ibTni0Iz1HWasGMuSndjRW0u1b0Hvn83KwvrR1X3u+rHT/9rv/3PcaH3f3fh6fd/p9X+v5r4+1LX4cM4Rh1RmBOqgeL6Ngvn4ksb0dq6AXmV9pre5V7soXnVa65v+18ur4vevzdXHmy7X9s1BFlv3i2JGVUEossDMEbv1n/jtxlufewnI6AXi5sY1o4K/E6JMyONjl+CaCfpLVgqF585UQTFW9bnarPT75jgXzoMMj7Kvz85kMYGeNVMTgk9/9hSDq0Z/6ksyoaYKWyK1v5AFBKozpExlAIM5F2rTK3oHpaSe1ZpaBr8Tcr6F0MxzTLxjCz0EAtFsxvI0ImgjO5xXdbwP8yGV8QuznqxndGpERorEUGYcvCK6YrbYKhzDOPG84q+ITWREBRCqWS8M1xdz4P0P4U9L79quEfq2uLO2t1xcNGqFTnYvr3cy99uRmqGN5doP64LfdpuFc3/jgrXrqtr+2RnWtQql4bejgaxnkgEQJXEgKOazXCElwsIMfbXLQRsgl0IihLSFyAVdJ1yJZqLiElFxcXw55OS6IMYUEcmELmfL5tPAYIXNbi6/7YInRjunNogMNLRO5aLVBUcXqCxJpAQFu6LkKaIUKvwZt/0zX8LAnTBdIzU3dpDUUbYfmi7rHB8lY4KOqTp7Bw9dAZdu3TQHjS4za1DMa6pWGVRMXozIw/uRuFVsyMkwdHeHi8ogTxrPYdp1Bg7VK5vhkrIhN2xgH7UexkdQRmHc3tWr3GlIfLuxZY1nLRIr4RaTZoylwxoZY/Q6InGrLGmkQDWOoRkzgaI1ao5K4ZgFyR+aGK9yjGKlQkn+heaS4r/TCWc0M3FOFzqWNYvsBlrdNNrUZTZzyNzUE13AkARLM0kUYVA70UPj9BdUZj/RWMaBNkL7qt6uNS6Ej5xFwRUvgzLkbFzPf+AMwsmX4e6Trr39OaUtsZFkUbUkmnp5KfgyL+OkODKXKr3rxV79TgynQKhlyHGBid+ZWFSol3cEUXwVRTHqhNjBcCz/DNOgvFKkr6klcZZCsrGRJ5VKZKN3NC6rQE0RCIB2QMwp0RiAkbuDdUHiAlx2YRZlPfxC1l523wuMYTwS2zdiTmOkLxTSOR6e5mcMUN8LqR6N/YvGQoZEyfU/RvTWhDkShvcJ5PdA/qWkXiHt7CEsDgkcwtb/CdsgTL2Nh8dBYev++8xlZ3/5crA787izv/5Pd9gv1B/7u+n1bytf17/v3/7IHCx++2fIDj84aF/PNLf7lXTuaqN/eT9sHhzktjd3eivp9Hpj4bLLPjgpbG9uF1aDmW6nuyCkCrIv4f00nr5oiCub4yPH1UwqN4OrM5I0ET9A0ITUTe4i8+wjT8zJQRb3tuXIQFM5EqnXu/5fiup5Vl/wDrHi6VlSE8nY+TxLFuN8ob/Mrr80RrQPbVilnLK3c7C5t7tf3Dp8qDWOD7tXlYdabVj1DzLfIJ+XOdjbzzS395r99nFr5Z/jw357WA02m7XFy+sjb3Nl39vc2Mvkzjf8jatKa6W34WW6wkz1Hax1LrK3rOc51P2UnOlLAjM8b6DPTPgYCBuNeONs6IgyEIFGUgs/vYjwOqhr66aOyZ2Io7B+kvTVjuUzYp+EHq6n/qx8ojS7IJwz4iH7LAmXSGLV13IvWjU4YmtNumBGS/ER1vAwEl510y7CaRHWdhl6F1cVNyhb5FuaY50l6ZF8lAih9Gm+5OYyjXLlR5eFhFxb7OqZ5/Vm/hWnvs7fNC7kPmlFfVzbBmqMeEG8xsgrOSq7tf2+d5PJLPy86B5uNo9/1BQvpTsM5WxVTkxyHvr4qMUm+DDp3pw3G1WzviQmAmLEBDVtZBrZJs+r5tAYOadprSJF2QgQBknwqQ6mljHNoxpYvJgIF6GGikM5UrIO/Etg3ZceBIMhIHDBew4NykJU+hbGuhnLxrtTOPtl5W5DuILB7gtHiZTQEwJUtIhB/F45tjTij2jTYzZG1/6BlqTv4EWIj7I9M8LYY6d4nBUinCq8FfYnNbKnU47SIHchxakYWftkoMuyOt85cB6TRaWtZZ+QLEUjY1kUA/Gy7uQGelPcjXKzJMVVMUsmD747xGq1kLpRrOGGyhue725eD9AGvfqQVxolExGx+9e6eeOa4meMNJCZ0o6QyJqeOr3tMD1ZqZ7Xa5eNq1an3e33bga3D2dAcQOxb6NcLLRTJsmSzkAj1xZ7yo8J60pP5sPfQ1XaNmT/38eCN1klg4vMExKhoneI0M2XxMIx2ABJP8iL8E7zZl0Q3pSYTLsL28tnMGH5TPkfZtyjrZdFbSlRr1Qc6ev1m3iioZisjw4V0N0sTojBhXG5AYU5mRck4zRw4mWnc9msDx8q7Vr9PimTbH+fpvoVGU+jpRifuNps8PoTsYzTGo4pU+H8o6ZG4EeCDVAomDmzWGlQzn1C51d1HcW+u+tOoaOBU6Y/rNRaDWaGkyWS1BxX7Wwcyyr057gwJjPYu6lB8BT4AJALhxdL5RTZ6jy+YR8JgX3sbI6FkNYVWHBa+iNayWhbOyPBCmR+HFehmEWRDqjoTkx94YM+APgvbDQGx98rsj98ZKn1g99KCrQ5vM6AEOTnzC24JoPVipbjEeptfoUDz3arnV6XFgHz8CAzZs8ExFa/W682Kk0Q8O1bpllEY8i1g0I/UjgmO7KtbfXCRP7cN1WMDPPbvbrm4pjfX4gyXWwdPDho76wkZ7d70ljoSAvYc7PODUH1sNqQ0YZJofAJAp6X3a07EvFcvC6QjudePx5PaiJ8sqiiYQvR0ZjwIgmCKH7bQGHoy30M2e8LLmPG9HUs1D2biSHbK1jH8c0CxlynVwu1RCfbJdjvgo5LRoDfqiNmy2zrGwF8uUzcmwgoFNApFVZzYpF5r2GVbiGvuSHPuDfqpbU3b4/9wS0qCy5uBLWgelMLvj5Wg/WbYx/ECX9XfFIhXG/XgtpDLqiubT5WG7nf535meO5v9rg4YYN9G3xv1+6qwebD99bm7flPXQvRQf2plvIZAYfTLW7Nt5zlMmVQ8wP7BlnJSPpOMDUj022ig7J8T/u7xP4ndmAxLqgo3CWSwe9Z/vUXPUBBUMohbpwL96q0F7wSKloaWRutli+I8CPrWQNpgGhV63bWoMy2kVNlPRQNOJycMR9hVolJgzxtFFaeGc3pe4EW1wXKLSqvR0ak0E3LZga3zGCqdTrb5eYc35TZEzydAkEMmxEjI7I+gh7/SHZ6Ti/pMa6MjdjQpMpz0Tgn315QXMTzI1rWH5PzksVJxY8CKOMvFLCWvw6/5pnXM/ySfJLINR2kbXRJGLIJnpoz0Z+GJ8UbhLG3LOF0NWzzK8EZPAL0uvMcyT89JBI+xQRE/gA7MmttfSNprPBu8N3p83ipiQR6CdKFioCgrC10kkZUdvI1PoOB5Xi514AZBzKPJz1OS+mi4Ea5F9hIBLDJD3XBiT4v8cPfxFfRPGgQGNRq/6/o5aigZLTAwsZC/p+J5WQDaV5q/RSJNr4E2vSC9GeI+XX29j9/eRt+nJz+9OHv8BcSx7ijObC9tSrdsyRtJDSCZyWIb1wojULXBZvCMLTJPnkSVcqw542FTFB3875C87IQJXLCU96rHhbvGMbymDRe/Z7KVuJFPr0Q1NPtU7/Yl1MDJzCMIY9DDOl2okrGLpEhVshe/bbeQ7GqAKcqrdIRV7ioZSbgOPDVsmLpJt6Wgm6gRIH6xIiAG3gB7SvWyUaKmQebZIo5kl8OJnl2OSB2+Xm9mpzHr+Qn3D3LcTshjHoNQcHBICLgNuCDmEE7X4fGlT45bRh8C+ZgDDII7RdxVmj5baWXVEE5yJnzZ/aNT0Mj9AJGiwxfTky+YQ/Hhm1xNCwrpnX6OIeABhE5BboaviyQTeu+i7kuiVODkQ6K87TQXyBLhPXQQmbLDhpoUivatzH5UDEcotcxYwSkDkQxJyiNZHZIhhfWFCk2VRxJII2YGHzjeuJWzeoQaY1AufQvXgtFDPyWaq0+sWy3NWXEGHyJAFNrX0wcgXROXOkyXggXJkpue2p79SpzfninufmZ0U9/5YY5nd3jw6zxubbDlITfrJJcGlCUGoWiJihQ/Zr2QBJvZXkvs7l/ePB1ZX35an9v/25IrVRiEDT0qq2DFvvvscJ85ZOHk6PNznC9vdE8Yf9V/cvhRsB/N8577VNQ0DACohuTaL8ENFt/cP5QqdX0EaOiLRRsAQKt2hmk9fvJ0IqLRhfyBE60Sl9bDc1FWytYQN1n5l/dQFxPFp0BbYANT9TxYey7PnNWJz1UW4qD64e2lEBEncoenShW4udAuo14anj1Tng3F95NhTXBMwyGuuDXkTQ5kPBNdCtsS1lvCwWbGm9tGV1rSFkz3+4uOSciYBhhmUGFL8k/2+82pQLOnXiUu7nyu4n6bZiqNE+fZpCeNtSry9kFK4v12sVVq9N7N4F5EEqLnIqDsorjhTI78EzswiHS50C/fJ7mD807I4gVPN+NAfIoniktNuMOLgujPY6sLkaoJNY0U7aQIVoigKkubRIzRBanTpJ3oqtSrsWNNEok7+V4O9wwTi1wrSPwMOg1WqFyIPRwiR97XQ00p21xYnb7rioWrsSOpIX9q5uLCyAo4GIe9QpGXebJrUFFUMnhF2hlQNIIdz8s/5X3Wc7gvXx5n8UV4OA4DIyw7pjGWFchI4iKuR0uW5ie45stmhRRCSvtE6WrggB/aUrmqLPKBlWiAPGHWSu654Noawq7La8ZIdm821nmaBv3s9qGW9EZe3J2rWO1d8SfYF5R/Y42tYw7CCkvjNLPa3E5PSxnKcRij+teqAiRBBKirPDaorO0N2xPUjR+PUdBSHznlX/xqg1dgMwceApuxGxHBBylkEdcgI2k1IFc2Kw+FfMKcwAawtp8QxDngWgneOAJMVfBgZlXVSXz0Roa00sOeOnJmFmjZyQQeSxfPe9FKfYndkqxTWJTyyi5gVgre0dE45r3CR2Tid9Sgc8O3u4UHDjDt0+07JNz7g2PHaxvRGxTZX6tV8jz4KnEypQh94dbLSUBP4uNlC4/x/8NU+IXHONCYdPcTvnZ8BHbTtn1IPB3KpO60Z0VjWDgMpVG5os3Omj84OBg8zw3WFjezgxyh986V1yNBRIOLiRwIExEkinJO3KjFuihIpHymhkmwzJBRmVc3jf658zyGrN2oKwfqfqFmpZfUVtBNZMUj1Zyfp7yPyIhjBJbHeEf14bOB1iS7+GGUyC2bh3AQpomXuAwnqlC3fQbTRNBJTH4xa3H0qYY5hv9jHWCeXGJz6MvX8twqi/Oht56ltRL/HFo5udk4aDRuZ2r3n7xJlu8vd0KttOXq42KFnkfI/we6fRAutKJXcVtKYSHQlH5LFHEPyqDqzBdGVSI3IH+FH+Z2VKRpM4IDLx+hWq9ITanO1EzaY1axX5Jq+le/X6wKXsB+usdm+J5FIkTFxDn8nWszHbzdJudww6+qfPtLIfWFIAv0he9TmvxqtJbRJ8SOqdGAdNUGEyCZ58Zmc8U6uSWWVIiCYoWMyj2GxojrkdoE6uJ5ulNcWtm1u4BoY2Jz4orbG780wb0tAfa02bFGN11PXP/o3hs2BOmwjv21F5RPLT8NObxc0aWC2oR6YywD9f8GPYh597roFsYebb3YaLaabWQqDw1dct+IHC9xs0usxCOHSwYctnKg5FPKLcR8Q+BVCOhECQcglUMlqTU1EW///0a7BKyiyVWVS64wNc86PT8u/r5sNPGotN0KOkL+qwj+kSVhlnlVDIcojWSCv9m/99PTZ832tP9q5BzoglYMgmIwOCo33dRCTDK8GooNvCok0WuzyNLuAL2Bam7XOv7V7SHU3Q6PTh/9MNQRJM91Xuedtji1vbx2e7e/sqKZTxBP/T7V9NDtuhc9arDR/wZfixdNfqsfx6G8C+yJ3d7Hfy3do6IOtacEuUG4VL88QtCvsLtVbAGncF49gFvYcEvo4IEPvWDn2G2CrP/xmTEeCIsJkXABXhGsFVjBoAiEfzyHnpkUCWPbIn8bs/VSIkLjHBzIdNOXjWNd/J4+RQtiXgXYQ/MOFx8Mh2i3ULJpyF+F1WaZF9jKh5iruE03sezchzyMdmibEn8cJLot3KwQSi0Y0ZC50u6yA/nNsCEj9C3LWe27DCrPoANNkh9A3fki/m+qcaO2LKKAlJhbaEIBY7D10UxvgrgiwGlOHqf8O5DCBrCUxF4b1lR6bviGPqWq3s/JFhi6UzTG497p/DGo2OBfxF5YhoGgMjWy3LjFO5geYhy7H0WQBRRdYvyM7E8xuzPL2ia0klYd/tFW1kQR+TqC1n08q+YDfvPSGuoAKwDXQyx4auelfTlBYXqCxxDkXNf3jxqh2ghH3fkpglnJiFIPp2ECkJfjecFSaxkJpIXlCGbBN/BxG4Kv+P1n4WlvSzFalY749rxiZeCCmMOb49OUKD7LaH/yTVsHXWn3N3HlGSYlR4xBfRjEqoKR8r3YRIoGdNTvKVGq0WMyQC2vbX6x9k1w+3drT1ODiGXUm/MCxm79xn9MF5Z16zz91SgNfhEqC1LbvIJrKWR0VNS4NnhmwY8SmClBTXkUZa2f2ZBBtyCfMrKlJgeoc676n3wk/A+n2M/Cnn4DSQ+8ovshz+jVZJR/g0kMXhSntk9cAj88OWPKrOCxEHWDSNrUEyGHUsrHQWVfVdNpRZ35zu3g12JQ13eLm0t7h1vL3OjlWMUXSTRtsoQLdO02k3K+eRc203yBTaGZnIUmlBJNWjt5+lB5bxZ58JKJWf/YIY9cFAKJzTmzE9/sWXyLxwPCEl21hr//uem3nvQo1siYOZ0ThJ0EO66CPILfd4kWTUte41WdpFgxfTqcFXgYCf9rMJc82VrzvXulPMDhk2WuH8Tr4eEfVA013BTUrYJyQSkFn5GPFsV8IBv4VffS3LqUYjAZoTQORqQWQqLcQZRjoqrD79w1LsrjUaaKgDBH0MlmvjjkulyZepxDnkVMZQ4OXuKG1pWlqSXYVc4TXEjN1905rGkzZVTXuEdhSN1mA31fkIHkRLQKbSp8DhIRIKeJFLKNz6mvB4K1Pt6a5XDJFrBLuyjPZtTtmtsvfNLmTgESotjRAQo3YjLFCSTjT7fTKLsJIKvVMg69J/vIUP0OA5ZyGUNeCJGKEKb+yDneXGRvJQthpfIAbHNca5jWS2txXf+ccoUWZRrcVTFRmAnl/VB9a4mQqIJ/jYcAdH50v9J5av7zkgvrm/wtMHound+qAjHS8RtV1J7nTGsSDzZrkr7mHgh6WUSxCf2vZXby98LP2mdBJdXhPQTZfgk4CaOBwf/vva+32UW1LGiOIQbJ+yg/MrB6mC7k1nY50YDLffaUZHHIJPOfsUfXTUIcUvF2AXB8B9eLBsAr+q/EQ8YUiU9XSMJmHoLVqx8nhSwK4Z9GBsyCm/vBlysxVUoHpkVvAxyuBrXcwKflRs5fTYEhKYjqToD8tKu99iz71TatU6L7/yJ1uAMRQt15H5fCawppUlkmKeAwShaPKIhWNST4QQ/FYuZJnvnxUyTnLv4T/OnNfxtTOApTttT5xmzWAVMJ0YfgcuOMmZzQVA1GezUWqPfbVYeztDJ6vMRTZXaYq3xyjq7AWbVqSphpFnlfHlxAnlMAVKZ4SDJF1vMl7vgeTEgmKXgFVwl1FY5mPNoe7/S1T75oaEUiTMYLKJJtUmFEfB1f6ugRasisPYE51HgNxPGTpnLklL1szkDsQ4oN2NAFs+MEDdftvTP5sHOgnGQ1qpcy7MhnMteGW39Ku3GM476hVhDLbZi5Y2pRSPSWqqtJoClyk5FDIVQL1uibQD66nCle744PD5s9r8tDCuHd4o9MCuq1vrnw33/qnm+ejc8Obzv6wfwq45pHQYADfj4x+SztBEixIJiDsFGpnKYa39bWhhaZXd334YnR19vq+2d7nmr2vmxKJsGqQUAEZ4csRP0D7UCO1FeR9V1P+19XjwUPQfKoThWGafrBB64b7M9uSS2yiIIZxoVWlgzFpvAAwfWgNEslAiX9kdNAY/IFUOduPiFhrJ5P5tO2xk4REkULxdhdi2r2ip955kz8SFAQsPuUZm6uGdODABLjJorGaU2SKu0E4UKFto9Y8cqKZBEPR+N/iOlI7uRhIH9ypasAQRRoYo8p0VeMMHma0Rn3V7jlueK34uL4ZgRddUyas0+gNP4CayjPA2QEc8nCL2YmvqCLDcip0sJDWVu4DHizoK4Q8jH4HEi6hBMfQFbCVUK3eEY1CvxVMWhMM3zmdFzkUt9HInFqHK00z/Zy22xWXu9H6x4x4c/L3dXV5Yq/sHDcHttp1tbvW9uX+ea1dbOXvXo4Opk8ev68f5mj53wOGSLQuZ8dT+11zp4OFnZvK8drlyvL19d/fQHzWr7x4yi9TIgBdx2EVsiyqIYUq1arZMdyRSRGb1QpzTrNEMt+1OoHTrMUGURW9aJyXIRqZyxDaOZXJSM3bHA2HATMSXxC2akdLoDlSiBvXdxf+f71vbeGfsnVCKFMes4B3R/fyjeHR9tZk4ON7rnUPQ63GrM3FZXVx5rqweNymH25nj4Pfh6VQ12mset5g0UvkbA3WLqwtb9aVrR4MQ9FcJitByPsVz0BQbWOa7zDl0z6JswMQsVgBnY+5zZq7KuRAUNTpzGqfwye6deaZ1xuWUia1CmpLQzG32U8oDIH9h3vBNMSiGpFS0AklTOJUoBeXSARx6e1J6kyJKdPNKpSbt3eaQhZYQk0OsPlVNPSipRtWPegWh1PdN9WQFyM3yWP+DLlt0lcjf5jJ4leEE4dgqIw8leUvm10E6whWFJ5Nj4r0g4Tck1aQm4/L0Zqagcnq+ur8wwq/+uRBADEEwP3cCj8HkuP2dM0OQxMjICGhQuGQqeEOfCQjzX8aLA8TYDbEZkg208MMPuwjLwukMHucLdsNreuK02iw+6sSdNPZ/2juraRvPEKw6Oj3Z+VxYXOkMQA2PWbebYv7xklz1oH/neVW11U55JfkNCETN8W9z4ce5vsoOat5yc4bKxt1r8za7ZPfezecHc0Fm/vr899ldYs4s3Ojm22FC40YhCKyjwF5fyo+pNkZouxqXbyjqzjHmCJ5JiEoLrEwRXkBESM2PfFs/JFj1DPOdFVuKfcZYDE/jwpHHVqh3mfkPnnjeLTWZ8P9bWfnQ2NO5w9WZ8rnv+c7WWZ2Ojdb66kjnZvVqoBgcNdumHYZ1tEiete/au9js/1za94/ZmZngSdNnYOOmz99X8tvz1sXa4we6xrr1xT7g9On25pC43acslrbMBCiSJE+A2M00DoC3/OP2nHbXxfEett8Z1lPHooj+0npN9YfSc6M7/tIMCyY35TCk6c82j3rr09UuyeE9sapEqdLcEh68kOGyQvsxceALsql05VohDUUzjGk57goDBSJoaM2GbMurPx8TBtX0T1Uz8vEuXaSwRhlktJh8DbdC4UCYMU75TAOnOZFhLlrRaeV7TGU9orBE7kln1LAEpYvnrbG+jT0MVBi+blZPGXoMiJX7GkSBATimiZeK5X0r7YhL4jn65qxFN1qndfo3Aqt0ZNKp17YDPK1ube8Ae1ml2epo99zydu9Y9yC6Wsbxjx+5fJEsz7wRSI+kI1biiXzlHJQGyLEm0zGhGmav38CJe/XytLpZSp9yZ5odKt7kw0i5ikQ+iBdHgLxoNIUq5cRCaulfyC7SUb1XESUw0MTzDKiIVPBGMWWBlpI1k+pf/GVsii2otHqi18NIKRD99sVaGWrt90Kjf7Q4q5M3K6XJb6ZVVjZJEA1Ml84Jdc8JtbF/C6DjvraNqAZVUfIgYJRwx6ahGwUtpEmw5K76e+ALKEwnhq6jwfIkrCydkBZp+Lj7L2dlVpQkx+laX2cI9DQ4h+rsoNsCE1sH9epvZrbiuSchr0u2bCeGXy/pgvX3RWW/3B5V2VcYYIuEyilH8qFzWz/TIKo7ePBC6BqreQJ31u99py4npYibS84FU6yqsLKRJ9ENs4WJ1sVETZSzErgnBZpO3iErGR9RBOS5s4kWWcWyjjyHn5FxC/W7VjhuHSSnuP/hWq8bXjsPQVxzyUhcjCXNTX2SBnYj18/MXOeOBGGvlX3/Nw/vIKgJyVfBGryZvaW9qczhHgiZe9tO75/iE+bK2zqzzEzBkDpl5wmz3FjN1mlAszoygg+uIhQ82DrPd2QbMvITjQ+8CHIKr84Piby3IgGZUq9k/OSi2T452Lo794k1t9eAGjK8h6Poe+bVmbaX4m5lumZPDXGZ4vnpwceIfZI78zdvz9k6T/feoX9B0B3KkXJKPWNxQM94Z2EVy+h6gsnyLSmM7LFuEkTxLJSBldlIFr5gSCRi+FKXoGNKGE2FAL2cuJgry0f+nJ6DN4jlztMXxuRROs//3xa3+rnXYTGmXdRpM1+sPDNkAHUUhUrKCfCtSRWgFLoZxXvHQ1C/hCKIgJn5kLCeaqFYiykMCOV/fokhXRdB6tpcKRGyyvlwm63TExLwp6sbXtr+xtp1p/tzdW7g+8opB/aCTYXb95nlrPzhcafZrvnd0cv3j1soI6C9Tq102GeDrtU61XjvL5uv9ynkE80emohnc83FyHbdWHk/2jtks2GmyOXM9rPr7/uZK8frkcPP2hHkZw+Ojzebm750LNlHZ5NlpqvlJsYCTK+Zu33WGtdZK33Qt+rHQ7Fwm52BlAYqm57CqTzJq4KaHbtVydp8RHJ/4qWYi1W3GCC0ixUzAARH0GMLQVUSz9Lm4JMSkOEbEZIioNjt96zN2p4RkVh1ScjzJEZlPqg0jKONUL1EAkrmTlssQE4+LAqv0iWLVyZIsKxTvfFpl5qaBm6Y0PWh1EUqMH99dgkmTUjU7uzTnpyWPAVat/O5eDn9365fDy8bFsFFlXlP7MgmtTHXrvSZBYcVWnCdSHt7ogqGv8bx2wH8BSB/vuv0v3pg9GlvGZLmrLSiQy8wY419Lr/5JnFTemifweH5CT2FESLkhdxGXsnjp/T79395uVqy5RUc6t6zRuDjANjQLZk8/op3LD0pfDSrVKuCBwjQbx+wnG9ol+BcCQ2IdST7NjAhyL0I2fOC8RcuUi9H1P0Z8eoUqIRs22b0ZJKQVC2/amwx9mHdB9FxcvatXrY4G1FGgCQ8r5wedG6SgyU4aieWRgEf4EXhEzsu4ydyJhzlaYTPvBuT90TTRU0Cak2s0zxP+4AtEweXCpLEVsj8lxMQ4BHoI6Og0MfBfZKnpH4smclNhJLZ5VD4BWJceLydc5rxIOCRcuMvJ+XGoTMs9y6HOCBYtG/HJj89WknBIIuzG54tXDbVnQwYdV2MomPfAot77cbCR3/U2e5sP192tO45hTLkol3Ok7eHnxrfn+WJ+sQ/dnqzt7PZ/3K2sV24LB1vLP5Y73zgaKZeRSYxQMc5o0aQcSnp4nm0zmHUNpS8CVRhDVbq9VAStzUsprLm7ILyT3Pjb467rZcf0xUsKyuO7oPhsBxTE2/hXJe3dnXaxWuxkizcX1cftzPa3m+3Vm7W1hVWHBmeI1W3OxmCEJFeQUyIZRzhHNTlQMgjbsWngDc2anKRLAxtgGDrHihvoqRCdEgpuuRPSmxBgT/CNNAykmIt61QQ4vsKQ0/iIXCYt6lD4MHcj0VQtkApJW89Scoocy6Oc7ybMQKvjQxn58wQTyxNJP0GEkNv5uYzl4Cj8ROzpucjpRCsr9xl6Zp+AVQ7fb7wgVCReZuwe5q6RFDUD/0sI4ecHTjsychAmDLuM2C9K8uIOe5NSHSQIyw4EUD9QlvJx47uo7ELllRDFlKscDCkUsrIUbCTs7iKnzEIXmYZtjmJVOmKJI6t8Q3BOWjbcI49I7dKF1NJgB5gxfFASgL1QCF4b96eA1oRUMHAdEoiaJF7JmNXQpxGrAWUwvCgJOOS0/3y80KQR/tF/VH5ghQ9QasKLoC1euaeZ4bX964PNPW9n5Sizsjzc2fe2D5YPLn7u7ywP9/aL+zsHxa87yzu7yo3P62DSy2uCky7uIJ50YyHJRwo63WM2J1SymIm+AuZJ9lO7zIiEKHE/1egjcxebaKwDoZ8/Ny56QDZxjmhzD2+DGcJp+oL7vCT+kI34vB+T0TiXwh6rldqXWrlYXmkklsz6fDiPDZVGpdl4NOp02Yqlc+rneTgVmDfECLZJxHgb0LjOZXgNYUyJi6iFI74qVz4th8oQXiZiHX9MviS5dXWyclWZWb9bXq+E04vhdH5tWQmrPWN6kLqCZ3OwffxXLO5JGfNg02OOB7QwWk8UHUqaUEQ+UCTBK9prZcoRDBoLsBmHaMwrXLzGj+Bib3KhrrSiAim7LZgLEzFvtegQBA9FELnIS6Uum53zSrM/Xj3dKZ4ev5h7UK5pLObGSu4SRQp1T2tOyM/mMvxlGWkISqJPYsAZSHRdVhMpFOQj5H66/WBwKGDjp686rfo0Fn5jQ6ZJyu8MVGymy+FdOB1OscUShheg3Cn/qJn49F7l/sUDcqgMJu6jwQ5l2Ft/a6hk4EUUUj++ZCaunNRWlm5uO6vXu0eZcPri28LjVlfZZzOa9T0bua/v1jGO4bPmXI5PUr9KYE6i/NQuGwOMIzROJ3FK+sxcGA3LmiTdxDv6Jg+WrcFSzb/I0ikaqfU7/ByjE0mSM9G5PU3qMZm540lq+NTgqXa9GUpIuGIIsYEhHVWQLEWU7cg8sI0GblJEAMCxIRMjbfmv7AgZcLTsCSL8DyJs5wke8R9vllvWuAVhTrqqSUWmy3dG5JXRhl2qBwrjVAJlr1LEMCGKhUYiye+cjDnBTW++8mT5DeoqT8YQWZZBcy8Nz3jGGb/GJlwGnZtuF9LqwqIQK3EaRXXySrVAbYpsgQkMsnBwNMXbwgqurKuCixggn9WcJphM7FB2IHvG+aIRl1D6kSl7l4m6j0mVG4k4rTKVEXlxBUHSaOM/FKGbVn8LBYX1fpUE8+hZPgSLHwrBh3zwoeB/yBc/FDJirhQyow/B8od8/kMh9yG/jIdk4ZD8CvvTMH8/5LP4aYCHLn3I4wnsaJ/9LIgrZ+Fzf0ZcLsfvm/fYTemrSCRJi0IKnVeF1jPpA00ESi4guqoIDjuexdhIPgl6cot4NewDVTn/U5eW8NC3QLMGmXh8FUuelpkzCOIkgMoA8lbDXh1Y4pLKdPWlCAMt6+45p+E25vmcntf5zr93qhUwQTidjT50xsHwhIkYEANCYTyiSLYrEe0LQcmop29Zf6SZC3M5uJosK8Iam2lPRxxJDh5cvcpi9QJwBsh2pKuce2OBpA15XlOSvrKLihcSx9ghYoYje6ogpl92DGGcbBax6IDLRorkNIDsC5jZjCyqWP75MmiIZZpQKAyNePq+zEdQYSTLmzWMkJFQnyawPu6VNsmaRh6epml3xjk0wP6jq0cIf6Jqc+NHnhh0Wc8adM7xZgi/i3ce8KIO16gzcGw86PMXVMNqCy2JpqD5Rc2mC8lRVNZ13jHtkRo7rhSFcfWs2uyFSnc6GS4he36ZtPrKSH0n6lKgyexV7zVa9c6N4D/GHPakoKp0sAPnSISgAB23KztOQtOS7q4L9dRtu3LbuKwMOr0wDWxuC5c4ERqgstpZwReakK1Eihnogy/ikYSBiUNMgp7cpKU8B0hEWZhdFhzO3N9JIh9PpVnvDThPsiSJRemekXjfNCW1YhxSvpLT1e4kCcrWOmn86Pp3XeTpXcRH6ye588tuiluYPmtJBGyK7BJtcvGBnu50w7SwT4wjXBNCrXTMvF5u6ohCGpR2z2H+qhCxC1EEy6QwYh4prQiCTuj9GdYDaYZbKSxFPv2krWzpp1x2BqqzyVdJz+v5YQN7I2myxNpkfIwIW/4AOYc9r1NmabBptAWFWfij0oPBUQMzL0aIhxawiN3pVk9x2n8WwuiVIRdTct1cT/Nxjx2Lac/otGCOB5CG0JwMkb8wrsOjc2Va9xOoUaSZ/kKWiCAvmhJVhHrTsPhc1nC2ILTt+EQn2wVnFJrrn8O3YXlxaWFvgRkRh9geKF3pf9zuNW4Rv1Or9K5v0ZZbRp2xFIH4ROQ1rMFnF517+IYZXdBMLBJi+8uKDGSZCGtaYUTv0JrHn69MawcEENE+1Is0tHWIlk7ww/LE0fekKUYFdD/iZ0xRlIgZMLBxU+wUH565ZvbcnhF7bsTV5dLJPOWYMCjiksOEZcqwT6wspLBpPwpbJBjptIrPTQ+pypzg7LI4ZoahPwwDO8OZxAmKRy2/aB6ZNYCOWZVNRGWYzVGGrA75CBJLhdJ1D94NX8WXlBBSRlCxzkte2Y5BvzXaZ3zKsaV68NCt8+PS2kmT8CMlrcBECrNHn4StzHxs4Skjmb+pY+hubHl1e2d38dTNpcOjOsmE5uDIbkFWfK/g/a9C/uGnQPH3P+p+VmgrG/OqG4T1X1WaF/Go/lzOF7l42+ehTso4hWOMUONsxKRICsZ4MZHF7BblJdmMSVcWkFOjHBrcuPmZwhgVRPXIlj5HUz0V5mQfSONCOvpahXIowqsYWva0ncNaG5Bs38vOGB3yhKRTrthCQt2M2wQfymGtMnVxihU1PhFRsDud3fYGggszCnKtXYgFn2fMcuMcfKK09yLUWmLZMlat4XO4CZVCykgljiSJ3ULBvfAA/m/o1awYP7HXZyNlYP8OI+CgdzLrBaOhSOMEZ1RSDz7aIUozGClYjscwNUX6gayarD0q/eyIs70gLlefMJFJmyVFes+DOi32u5efBMPCY83hv3n4RVZ9XpiUO5Mct9FbqHxzjFkPp0HUHUKBAhUfjTz47vlYcAazPlJSS3thXmYkQ6jM1vGk+zHL47Oz1sGRDjL4Zegy86XYIgwFVyDiSTYMq53OdYPYGn49YGoLHD47oJaQUg6TwgB84pOMgjiqiXbwWhbp4JNKUWJXXDtH+U5TvzdB9aphFMsSJpIlRUcjewK4Aksis8FzXexUTHcRNCWSnIArYJdnAQEOQr2Xw0arclnvD6vM1/jdZytCdchWieR0mUsbAaEU6jvcD4a3SZmlsBIUuaIhgaZb9lFKWei3AmqmT5iUsswamzYqnq+A2TU/A3cbwK+FLIDmwUfCvzKIjMdfgyRXgfiolQHj9dM3vaYIHyXmWbPOblAGA81TZAbjlYbg20k5Kg+VRX3FSyPF6lGHNIxqkZajAqJasMm5ciCT/Yxn8HDRskWuiVBNTsQOdNMa4Tq0MhFB7PAFh0mo1mh23ev6w7BVy1k47sjdOHhW3bMkF+cliwgymtF6KoU6zIsvzva6fG+sy+Ob4wCSh6IUIxrXk+OVg7ZED/kCqGNQoSVEwYWZwxJbimwdPGIVvS2qusCbKUZaKL4YAu3NsHnXnRKb7bD50L4fXrAl6QpycUmKuMGiDfNUaWXzi0ABxpC1jHXNsPswuOq0kyLmS+4ufxK0jYKcvQvlM6MvMl2Gbh/fI7x8rFDQcrOOf+LzEV6HzIDyr7dhW245lS57bbXFq0azpjKC1hZBlOg518IwJj8toU9BRk89AUGyYJfmZZ8mE/qrhpcwCuu4yORgLbnFtSQPv1bwUw9+beKvi7jmJZBrGlekS/y4AL8+4nkSVKWjqowASF7RP6mQHwjEiXxNTFjMfi2/PmljJBJA5zs8W/BAwS2ATP2s4Dv3Be+YRVqqiblSZPOU55tMUaonPsazku3ftMaYHcHtDBCnC1OyKaIhpD83awTjZY2WggI6BhIV5TuQYnzXHEN45RuUMpIxRvDJtLm9m+PEk4JeZqiIZ0xeGXHg+eHBddX3rtgVdx7PD4EoZG1zgKwc7GYPx62V/vDkaKNxfDistjauzofV4IAZ3Udf74bVta99KHgN2AG1q2HNZyfXDw+uqkGzDw18rB/eswYcNZvHR5u5YbXdbEN5LDumBlWzGaiazZ0EG029SYatLi1YsUAg1msm8y+Wun+9pOnLJPGEvXKRQ8OpIKHy2kqHZtEX2sxzsAZHjFhMcZJDOKR/+BqgeYUfdNKV0Ye8/6FQ/OBnMFWcg3Qy/J7HtLEPCeNCwD6J2uHJFMS3Up9DawyTlfT/Rpm+4FmJSLL4vMh+86Z1zsMSZUVL8V9V9Y84/RkyXVil+/jwonSfmNy9eIkb27GM4w12Rm7KY8XFLaBL7N5iQYi46RL1KscSr+eIeB013P//aniEqKv5gpOqT8GoUdNOMQaByjSzE4cwJCIsDHJM+CiAy9aBBEEEIfqtuqzbq3crvfpu5bYuu+z1PZb8gy5DyYtXTSk8YzwHJDihPJ+JR+NXonoF/c1DOreEc0OomtKTnVUrbeiIxSqYVuSjJMGLEl0ZiCU1oRPK3LCbNCDt3nvoDuo1Ve6KPZl0CDn9wdBLQT+Cp/7hbfgrTIVD7nFh+NJ4SnW6ReyBzOxYZpGw+ZiohFC1Ofb1J7DZSVe7HeeolusjwPWMYTYMft50iNwmDL42mk1mGS3UaiQPIFcHvq6OSvZFMCo3DFPJ0hwVufphoA8iPoNS0ZkzpOqmoepEfS6VzC5ElF0hUu2TLIcD5EIehf/LFTovqcuR9hxh1nQi6BxRpgOZcVwE7vPbqSkKwVGYxowMJ50WgIPnO/SiR+qsIn2CYVhGhYM7XBxptiNyjhbr0QtYofji49QUcQdadnKhIPoiMV7RJ8P5pXLBcDdpR2KUuA+BDmHB0JaHhJoUxtzE/ZQ1al52oIyaYGtYg5/+uE1sgszqGsmJWKptOS5mBPYp4eC9S85xCI7Dc7BjGFJF4U5nv+O+XWjBqqIgDkdPqYQQgioUDtp1QUhNQw4OnacPXBbMKxI7Jed+mdQvz/EndCk2Pso6E60CBxgeGF8vRd8VHXnYkEQbqXjq/yrv8P+GfIh7/QmjQT1kpY/KoSkOTwy98PR8dFRqslr5rCHlYOoH2dUfWrhXjTmtqVZFX0piB4Vnr9d/RJs1q33drVSvpVUCqX19QsrSEKSU0zpI9g9auX5GI2AlZVehUISWoIzTDklliJvUSQ3owfqWuH8/VsS6hAGQdxOnpVCI+fQ/RjQ3S91OH0mdS+VfYHv8/YmCxpgr0GmIkQhABSF18Tgp1CRPF9gEI9CP7PXM74uWdo9LUEW8CYoztmphmv0NxXdVKr3e3VvYAVZEtkbsgoPAJjC7Q+eu3sNL4OGqFszLRVAqxrgNRGzKpXETjXZPxAvDKbOIi+katJ5OO00OxSd9IMjdw3D44hM/fRd7S0m/4jwfqXEx/Fk+umZjcoCjlKvrsoKa3C73NERlWpX7MyLvh3kLaV4Yq5NTnknyHrEjcbTgKnvDVqX2APVth3KGcEBNuBTvf9GaMi8X13n9D1xf9D7ic6A0XzL98hTPAMBKoikXGtAzkfuyHFw6E8ujSzbvEXHi5z0XQvkjh5DjtiJTYJlIsIs3eUyiRGzqvqgStRIXEJeI08WxjA6dkhVgPo2Lsk7A7krFqbZjbEqJMYkrRVaOvGB0tCGOkdoXEa+Qex27MLY1zZOrQ/aBts9hXi+g+q2Jd6q0Cz/Hui6ZeuFQAx0FpWEkkLsrHprHvtIqyvDqeYxFuw/nteHvOc+Yb8BKs5r3wjvIzD0nzGEjElxv2dx5PnlWtpT64nGGsoPNwaSPpPmSsX2x327Zegw0eLAYgKOpIB2Ii6fMhkBS84mlSpXRxS//KgmsB2maYh5b5qZHJve/VuWhkfzxfkN8HdRUvRKn8VLu0zgYnV5DqIbgmPEj0ECab82+nfoifuOrUXpuCFKOz6E+opsq9GtKj7/NEIJOFruXPnF3kv2BUDd9MSUsUE4OgEr/oQ3MbP1eVTtOY2AUKyxO/9992JYmdLLGkMDZKmYsdNVVzX2ZWY2natspUmlHJLc2zvHLPyvlKnAWnHpCsdqfX+x2qtdbIKdo758vuEc4AqLGmcwwmw1ANExduFf/56bRk2W6sntTHDSN+LknY8NRJ2MYTQ6ieNyOiwaJ2PajFB04189alW5E/ooKH2Ci0HpAIcIzPkUcBFAJU+1JtDxP0JO7Sq8GQLBBo4p6IgJaaZJeyQ+1+6BtS6TTpVBU98PuAVODD2dixc9G0Dk4PMpbmbNTE3NtBLP4209Iwj/HGfMlgb62POZJ/paSulIMLGL/I+vwNdpp/n7VB2Jnf934SgguaFruhHVB1Pcz4iVTBjy0RaFSsjRBJDWFhDkiYBxpcR5o4g8myapNdJWXycIbBDiX/o8/yV9DDTcN2gA8mdrmtVxWhjXNVxZ4yL/Fq4KVBHqAh4dlzY7EnIYYPEhjpDBNyHYzbS/3Ct5dWFqRAbvjY4gyE0amXtvmzM81rjbnJ7agJA1Y5hPz24INCCpfL6mzX6ref13sLFwuLxz9vK0v5DKd4Obb9HpHaEk5cJzSbCiSXVU0Srropm6AHLywjBCI9byC/C3DfysW5Ud58VsmJz/zIicEUm6Wve5ZubyKU2fEddWtco7bZ/lvOXVTcatsPto02YxM0bxt0gnbKBaM6M9fb6dv+r3p80Z7GjK1wlnJa6h5S0gJuEinwQCqTYvVGbbhWqOHXbq0vhNKIg14PQJXQKvP3ZVGDdF6cKtksTlZw+vBxTRAc7t+PzAXM5SACM2yEh95AdUfhjWOEXN1be0aKceOgKed8I6bMUCxOgqfq8woiOas/ofGYSyxHJpiVpnk5mgtDtCZUhtiGHp+hv1gwwNip7hgAl1G7RRnK19bJt7pGGdxQSj6iajAii+xQhOrC7P4UYrIHKiRGvbkk/Yr74eiqACPQM/+VDbKgpqBgOy/IVW1MWuBrwd8NXPeLd0p9ZF3wK2IB6XZUMl8JiM4YrVkxccvxuZjI2DfKWSOXg0VV63y8RWH6HWlPofpKhS/J+qjZ2m9kpWNfpiCA/Q1MyEWTaRADdNhjtcMECyyT+WQcJZWBMC3nyjiOHCtS3kkpUf1psRn5st+QQC+HkYUwbWXp+qFfwiDB0ZRHC49YtvhsJ7GVvDGxWhVJoTxlHNjm6VMVVlownGKfOM8EAoh9bGRUZ2mqP8cbD+mMMhfOhO6eXFOmf0SmyspBKQp/SAvGwRFKgMzRFflwzlqlvJIMu/JwNlOHd/+cvuy0Yblf6utPty5oR3hlxYrTAMxEGsyX3w8HlgJS/f393OI40YmyDI7uPydvbkTftesKgk679QeaALiUOKsZf0UM7bmLOAKxlFx3xDRG3bcXaM2uBKzhVdE91NX9cbl1SDycX/w0NS9SEAcNvqN80azMXiAReKqUavV2wJlHeVLy2eo5sJzZTpD04K1NmR4Fbo4rG3wZg0UX7rfbTYGjlxYUi4EaBkblUD8EumSvTJo1pRvrZByy3WuAI4FALnSIEqtQZhfBACy9oAlaPCINhG98FMGg2RYRcYWvUjQg6Iurww76LVh3OzOE436TBDzXvWiVRky1zpdNsado6XUiTgm7q2TE6Rg7oFdvaWRCCDFNVw0JQ+Kv7UosoT1wkVqkZDjczKwTHfXFjAjFLniid6hXHBxN1ouqDZl7IxSqGfV1HlGXixKjDzuBkkVbnBdOXKCwPbwujs4j7JhvJBWBIfHIsfyGbei95/nTfmiQeoBH8OpLX3kpWm7/bMUr7maWLYRpzEHQm4OcouyjxgxazBwZyDNFBB1q6FZbZ8PdE4B18+03qgvH5nWeQiwe75e5ic8yDxxmfuvU9pc+tltgpLGt9WNK1uKY73ZPT5sXTV++lc3J4c5F1okSbAnHgHkmyeSlwemmFVSJJzES3w3cVprsMW88nCGtYZ9HD9SmdJIFJlOkzafqXymdtbpDhotrddMFANHMT0z8p0puifNSqQcgH6i61SR1S4RaKr1wJ66dyunjWoBMmW1KoPqFX3J+y4wtDa0AN0sj9Fy5AFrR8kqqYOmba/uLu5os1lsk/x9sVF0Luw6GBMU8SZLK1E2xof4CrDxQjHEw0BWyraWvKwBgX8BfcJHntv7oKJD78uZjhXHkwTJfLXW8i760WWs4Q4yXN6L746yspft9y22RejEZDZ1Zpo57NV+s8JWIMGOyousMpPY4TdtSpaa2ipxa58ySNBsjdZfqYpq3oG5WGE/yp6DWJ7ZN2eY9j87E72x9aKuKwvuFXCg1UIDq5N5DZPol8vm2uI9KBW6tpGpNq5aWARgryAgpQU6oidtUNL6GRX3+ba2mam2dx6+rZ5A9cB1Uu5wPPxDb1E5Nuz7ttIXk+GNPLLl+zPRlMMz3Am+ZnXGlf6YA69beYDtI3oxU8UxLvco8q1iBlCtJpElpbmRq69DyiLzZa0D84DKlamLhamVzFTxlILCyJwXJs1YipP2I4+8/kWJc2Gn7X4Q+ffYoEJkZeFyi54/knH+yI1IrRp2JAXS0eJUAmCm2CvMw+hRS2X2lIQ+NGj/Zkl0Ggrr9Bgcs/KdWaPUvHEDVOJip6PGmVx90VzJEi7PtkPVlEqMIX6QjgisQqfkjgRuJLWjJEGh+rWMKbUNue+RdCGyiznbYUeSdGrEsG9zNnFQUcKUM+cuhX7RyegQ1ck5zcpjinvAPYUauomIMpvVT0UgUQIRVdZZxbht6ZO9k9XwWqzb7gc9dg96rEjvCHIxid8zGAPR9eKxNYr+JnQbT8sUCDYfuYAjI7yJHVN9C75c7Atzmh1xL67/UXfvntVoeeHdtBLVFzAe5pFDPhfxCGf0ek6Ao4ZtTsw1+sxON30lPys0HNnG3LhFAZGU05nUCktUjwLI/RMgj3aZK9+sDzpt5a6g62/+OVIDMzIRQz7UmNmWBlQRmjzZEe8S/URYgBNRST/OiBcQr7AOYM8TiXzEmHPbReC1gA6a+CaNakvdSu2R3BexFxcUMMoZDgjdEEw456LfqV6DUyVsd2GAweNdNG/6V8IyMQeXaUzo2bLQCJXEDiB7YyBiecgARxQIeG+MFyHgB3Ftnc3Fxk6vcug1t3c3/OP2jldtDa7O97+2SWIgxyyNTMOFEzLHvp6CzyMBPQXDxVv7i5cn50Z/wVlOVVTBWwecGZOaBSAGG/z4+2/2460y/+XrmT7vDNj7l7l8tvbrGNmYhJZRwfaUp8QJgnCY1amfA9dc3N/5vrW9d8b+wVsE0cNCO8WD7zyLD/cXWP8ae85f07hORjpvRtSLhe8bXX5BNlvr7dsw8W5n+cfW3vLZwtLSzruYIpxnK/d05RHp5ukFS2xFn3NVbgtGzrkv0c8mhRE1KwstQyEoQldMcDK2yKlEzEYS9LyKEFaup8lw6guIyRBeMfQtQZu8XxS1r24xU4VuMDGTerBilvTX0WXhZIIKsyBiPqFMKMkZ+x5HlTOonp308wrIEE0scheGngEp9f1sBJ8LNdoUnzRpeWupyRh27F+VqUe0aIvEk6xwLXEnTBjk051ejUarH5bZexI+mBemQlUmzusNCrKsQSR/5ktsxEFVGYZ4nfs9GjQJXS5DH/aBJ9WJHPD5mJoOSUyHFws1mOlwjhCDpZj6FmPSJhUHDPw9J6mB+eVl9NReSYke+DQS14trjG6iY7m8hy5ZikPsMWEOf8fglHFqtCXZGOwY7VrjQgbaAgKma0bdx4QsUMBS04/TPP42jiN4eFEBSC/npSrNP6cUjYuHsS/IACCNIMfbBjMoQF6LBNk2ScrXhG6yVIiwa5gT9bn5l80LHaUyyMokCJdlt+yA6Bm5MufMCTKjmPRHHq90Juk2lRnSaLfrvbW9H9/hSphb1QXCOfsqngNv3CRMRVjS14f12lCGOrHpVv2sJKP5zPYTbiGiskDgBVY2WtiGJjxMoaQsAlfWpEHvps6Hw6ywofJRLm2+Dn5ASxmZ6LXgQFlxcLtZvc2CFbyvqzk6ziMn0YyTSA7dPlV5sFney6EnGClxxmmXNNSE2SOKJwwjaQnUKvAhqagk7apXPdZoz8vBJpUOPfjD9+COgOnBf/L4T4Y+zBThn2xGCHnzMcKmfLFIhxfonywdPsNxM9hjcJiXAYrwNO2fePUk5orpS8+n+Zamv3Jhkk07/CM3o32TLVCBsKh0CxCxit9Qa9l1fLqX+oudHRSHQTaJT4CPkyuGSbV7YeAIcGUqZoSGQxKBlkYk7KOCWYKDSGvDc6EAnfP2GWbz4Yq2aiWIdmuI+B5KfiD7eCnLnq+f2uwA6Gelc9OuhWn6gjXIcEpJ7CDnSAiMi4rJ/VQi1cc8YmTcjwOoc0decqzw8ICFKIfJprhP9TKLCZOsXzN8raWER2u14AmpG+iaR8a2FvdScFLppPN2Zm9sJKhv5PQE5B3iXRwixd9oKAvGogEQrAkDsbJwNC/O1qI2FLQxYjYlcZgaB0WhJApEBYa9zRz2j3Pwg/ZTjnFUlYfOPbO+CQMlbIcDlNzRy1vhUknkizccYeT69/3Ajm8/Ce5cKSK00umI7vobyPCI5UysfHEB2ol3k1q5SB8x/IGgpXz2XFkMUmUja1KcLqQURjy0+4wikG4aZD277EqNtrKqp3LW/Y9llNUifxifekmciIciS04vj9YNj/7hnnD0qPnSBUQN5L0RJ0qOUkqnzICbXIh8LR0aiLbghI/WX7l6D0zAfKDp3opZyryhM1DXmpt/G6YQhh2OvoS0h02Fvz5PY0yZ0B9i8BOZfcEWoBaLj1uZbXv1KnN+eBemtlc3bk/WrnWMImvS96B29/1w8/a8vdM8b/+8ra7eeyd+86b6UPx9zsbJ8eFm5vvhyW21lRlsPgxrwcLg2C/e1FYPbmqLxbvztYN25TD3+N3HS1wdBz8HJ63iw/luJhhWFz28xslhLvM9+NqvHda65+0ft3SBld+1Ra91HmwMvu9Vb5MqyK3ZrnYoD1YPKxZjdDeCjooRmeMXjU49nuhaH43Rapbj8DVwviSIBdVxNDRUaZ5aP1NsSRHVfqOko3Yijwz2HjhhBC7guUsgUkMKNSRPQ9o05EvDlQxuiCa0hLupD/V0t0q9oJfqs7bwLAu/d94QlxuvJm4mtfVIfKtzWz+76UKWqV4zxOisYt9J2PcoVCQDRbADfPksY5ZkfPDdpazVUn6ehg/FJEEKrhzqja9f/OjUENOWanVqbEtEPEy6CheycHApwMGl+IdfK30sAQ773Hcy8HGp8Ff5V+eWhmGZ/cKeJ6yhW74LjgmuYjfdWmXAZmKvDkeUf30XMR4EyfWTKd5a3MyBFyTB46XMBj+/BCcnhH4qh09/iTPTpekSwPEq1SrbzoQxnxZZijSsMNPqVSTZ4a3OeaVNED5xPFvXuI6mDkZWb0UPB9N6GSfJ5goIylk7X6petTo1c52FX2op/TBNZ4d+cGFCeCCPudO8l4qS0u852zNi5jh0vELAz+EkIDEvNOzr4V0pLWwYocEVvoIZ4WWcCImXiCPr+WqdmiScVe0STeSFvnHBX2R/x8LfF5rtC993l4XZ/jqaeGUEjeysToyCxUvEqSyagJcl9XQEpBbYFxtpznNjFNKcM89AODkQ37oWIg+kPsHARvgTOvACOyzVYaT5b0QLgGT5rdLscamMSsloTXEMCwSCqKs+Hici/XKPY5PdtMFOeIed9SDafAz2vugylmOW4qvRPJQSZYgNc75zToTt4p6RUVrWhCXAyFeF/1qXKuZ2xXL8eZqrgfAXGLcrvZZ0mIdVnzSDXGRTeWKX2PT9vKVsz18w27VXksgaiuFA3Mtz6JiP2aJxRw+pC2MOmbST5HxTJ978wFeYAG1qU25BqIEx5wNi9TlkuxKORZimOnM2jrwCAD3cVe8BL3Eoc0URa0aFwhLXui3BVzVtcbMr3Txp0mqqMBKjEtHD8bWwPrLoF2d05KZe2SOsinMe6PGUTSFGTBLdUy48oa1A8ni+vojpkRdiy4qvvTNA3nG5ZeIZ/I9P3NnNggMKkm6xKTJ84/Cq2Du/h5ee5DE+MeazwsqDvJlOc0zJE94Mu7xItEFQc/BthPj5OC4pR+SmETyfU+/zzClMFT30eS8RYh/kHMIGha/YtZSCcS1CBj5F6gQKPLJ4ETV85EGeUerjwZzJHOsfPaWMm7rGcMT3dwnydeIZnhW6LfVFRTumBz3pFrkIkfLEDZ/LWDuazHS5k06wAuXRjSjCj6pwLXIXkvE2F4WAmdcQBX7GobeV3lntptWl/dp8wUljd8nqO3WKDDbUNdK+IDsU32j0cPvlIt872v+W3LrOElNpNjt3Z8T+iEQiImogrKqSxxdOSAG9fzfxKYQddkTDC94FP48ux+7waXq60WYW9TOn2e8tT8CfiEvvtjmdJNuW5QkN6LUEyc5IcxjkxuueFUTTniHMf+IFMUGySMbA7MacyBdmIlQHh/3fGOlxDjH85ovPCoE4LoDjgiyM8xX4LCu+yOMh5mdSmFZpU+m3TgMIUj1OVnAPjy+biA2xpoQlN9bJTkzfdUUazCtqRDfJ+H0jAk6JSyCmZPxT5qKj3iLWpkBIDH5+31r8drZ8pAWt9cp4zjxhPACVu3ITifjawZSc14H1cHsLVc8zFvBvRo3j6KoNp2fsIPfbaN+oaPbHz19CNh1+Kf5XLVF91z2rSkdWvwQ7kWOqfAETVJU9+Ii/lB1DLjUHVf/6MI9krFOyRFyv6DO9DdlPeSc24aOTdpFfTemfWq8bJv9EeWHqhJkQbJuZY2b1BNwL/BfWLZP4D9zdDWRzkiymrLqEty8Y8Hg2D4J5lP6kScBheqEMPWlrZsEoaDSqcWLH9sZKcXX7oHixc938scP+3XrYuThckrTismJT+FqIfgOBwI/irjNCxeKli7RTo12V/rCFI/FSrnc4ieqDdp/0+qCPVukkEmnoYXPnQo85lMDFWfInTzTsVtsDS06EL8vAvaGJoGOjMEtcHDnaRSTkRV9PX8Yl94QrW3gGo16/Z14eDnjU5tbT28lJJUQzaQgmuHmSygJhmBW5k3H34+752Gvyqjxa16QnFOYmWYNUfFOtDWF+ihcQykgG0plDSaOaBUobnN2ImU3n5kuV7+i5qcn3tAIJ5M04ceTPonL/NNNjZoxJaAnzJu4R+uxsG9/QssE0GpdtEAtxQarAzP87q0WE+PRY/alGfqG+0c6Ldph4UZj8AIm0/6dahbu757+SHvjXX2Gb6N5VvIp9ohGohuUeAgbYTp+Sc/qUCpYmjaEq+I8w2CaZ3dB8lRtXyty7Z5WmqkYLrG2AnHyVP2NeFLqZ6dE3bEGeQT3kaI4ampnkqDMMy8OK5knwVqN3Wm7XT8vVf07LzZvT8k3jtFzrnWqEh3CVMgafEhMCvSmZNeGLEXxBmpGu1CRyWOMotpKJhpSqy621X5aSVcWnKv34Hk4vr8yc/N45aff3e9fh9NXBj62Nys+yICkNEOzqdN8KpD7CxgsvoT6TiNWwzOGvFPWCiTzC6EdOQF6RieBU1UmeUmAAoVtzEs9VplcanhK5LBtWI02VU/+IJ2Ah3CDOou5E1OjvPg4tbayjE57NGRXCdmltNDYiwL+zBiK/qAaL2vKyGTQ1+YYHO+lIP+P5W4QuN15G4lD9gk/oSCCuaOQZ2Dlo4SbUEkmdQNTMAZEHJGr1i0YbpA3wvWkBCRHUhs0gO3r2MMzDPZmOqVQTHwmvEqjP44I47ZtmU0EphQVBfLUOB0csKalZs21cjjgp/I9Rik9zvjnMkEMfpamNndLE2JaTVYC60YtJyI8oUw0/Vnod4EeCCN7gii3th51ebRuxLf1Kt9tsEKCIzyTiRcZgr8bbSS77C9cXORoStowhaBfOhiV6TzMZki+8bFwMu+3L4e9uvXQ5HNwPlJShGByBKKdWRgGJGqt9Gl+rsAV2ha2g5Q3jHOTWTXPQOGu00AI7q1UGFd2+0q8tEpEwDohmZQZiM1gnkGTDqwPAaaPeAIYDBLN92jwTJhJMh7BDY+AN8kVSqCJCvmuyLNRYCNfhq5qEQEStSYzD+EyMLNzfTVsvDv3Ghkj3bIxZzFaIQeem2+VwNASZw4KYtpJT8KUh38z6PghHyUgUKFLGlY1gB8Vj5AS7kIWJSSnQ1jhiupWNYO33P4+V5vXBdPeo623sdLMbj37xW+Fu83YrTO2vby/4M1vnvYZ/f70xA8YBjFI+wSiZmoQ6BlcYlTMC+9HGOYyX0PCxIxYMdeSkxPSOkXYASwT+jYG6m4B2KI6A0n1iYMF9idsjONXSasWmkAN9Ig5yxmgEhi9yUKR/qJQoMGKECWzM7u761iYoCztXcNi/ylRli8YnjxzjV3FILX2JNxIC84D0R1OjWj3TfRbpWbDx3QQKVTpSp51JiiaIwMUkmV15EcEJR3YHYnLMFoulPVm6d46xC3Ru8r3x7psRKFb2vKA5ZQJLRdjrR71XvargorjKLnVXedAXFnYqrG1J8mDYFwS0pL6c5Dagx6v8xDGso9ooaWMeJgQ2hESv9NNgpZI07XI0WOGztAxLEtWfp36nPsFSoT4v2oe+CtROUBSyqUolI6Ltc6dARa44lNwzxCeT5lXGbRZiY5gVdj79W6bqyV7l7gbrBEUKFKwemV3XK68NqvLosOH1WuHdBxUCgflI+bpZXjjuBLMVSeMiGqbRlyJ78znVymVgrAcZUVh2dqoXpTKzukbE5TCW7S/V45EWRNGGeCLoIQ2JGh8qkKGnS8mn7GjW8LGy5GOF2TlpcScgjyPKPPJQ3iqLJbNj4SAjF4N7ntiFs7BeJ3atwpVnlOo/BNXQ++Bn+r3qh6D2wfcplPYhWAAY1fCDf0H4/A9cIlV+Wu002leN23qYbgEdD3LDpnk166L8jh2+DwoCC+1O+6HVueknP/gziQ/sY78w9P0kcPQEPpuiH/yquECe16pgbQkgIdgpH/wiT+NyVnerJAIJiF2QR2S2y2ydAa2c549CK7wMQTjmSLyboPR2ynWw8VmZvcjyKfAhIxMumAp/EX0KR33wX6vD7+LXi+EWCs2z80h4yI0rMQY9ccaY5rMsxRaA42xGE7LLWzBosd5IQIiMIubdxBkC9C3dcJlB94gqGUNxQkwz0mIyyxzRaNgXpfOqx0/8orhfF3ZyzsKhH4K1MyMi2MDlCQw0eTWLTEN9rpFqsNl2Z3CSyuyVM/mH3MBUOmKVKEVKsAgxzlcZdTUZJtFSw7WUxE88+UUu9K5R0bipM+WqDNY2sSSIZx0zpymi45ohVBUTYYtFJOvrMyfSsCsRfYdC1kuuCCWU4Qoji2BSUiv3lRQ2Cf2IgJ7I/ERDTfLnk+Xd1vOR4yqtisPts/3d5bO9teUfy7vCJ4KKsjBGhtEM/sF9JUILd17Kkaj8Hm7GtUYPld+1RECpzLYXQMzAvyLRTpuzkPyavutOnTc7l1Mc2aHIHz/y1jm2cNYaBFA065W2aBH8K4pgtTCItMSEpRtwM4SohHOO2evAtcWtcBNCWA2b7Ia9RZFazFaVy06bFrhivHrOn7dhFN1XjcC2JCEpyrwQ64ZpwAybfJ1O2QWNm0a+NQLKZACdNZLcnbQgPHm8WDAEFI9G4Bn9OpymCVxAMl9vhg1wifuZcJRPIVzm7u4uTF92OpfNOlJNhOnKTTjN/Pqw1K9obaXGQ5FksDD8BF35wV8ZhtOw5+M12KLcBXkXdg31Zb9x2W60/77pX84trPz8vbi5cuIXM/WcrTJBdLuNuoKgFZBU1wcMGg0xI9an5hwbs91OP0xo/h1ccW1vD2fvztnC6vLmnjDiyfrL4vZM9xUhQb4EiUItZLR2OH5KvE6RnwQZlHgWEVFpFyakyzQtXIfp6WqNmUD9q06PLcbMZtHg52gZTUjMOQzDMCEiLhhF+aKv1brtYnC0FDJ+hL7iOb4YTPguN1kfb+zuN8nWlg6pwDfIvVy/dbKERMJnGc4HJdYajTCJj/CSDA/jwi4RwLCqSeqEU3nQM5kylZY8FRh9WgYoKVOK+kbyg1kZioxaJ4VMIOjsDOSjScFnmf45XuItm/QkI8BzUjHX2LpCnbkKQ/Xa8EUnWWkxyUsJZUl9HeGVDIN7gaPSL0qFWwnOB8KlIHSJXnA+MJQrYNQiy4Orvq+lVGsd2hjF/oO8EzmHh1HIKNkxaokyj1FNBGTCsN5OjEZtvjg8UelDor7aMxVIhDG20msmpw2VfLCVL8kn1XSyFCZe/YVsqfPbPnEIjP9i1vhcrHqU/fN00MRrs8imasG/OR2LhubllNTiE8KGhch+SPFh428dHGDPsLz0WOw5atUa6s46IDa0cMDz7FYv58B6LiVb4DzIbE3gparCDs+O5DiNC7cYFQcQ03tN/WfkmcxmWtGbyINxWHdfEC3Pit3NCN3Nl9geWq+0zu56IMICJuAlWw1EpNvRNzL37bk6a8YWzosJVinnk1/uLzJD/moYVyV0hqEBGFIGrtE/7wzEcToBXbx0ucof2DFf0V+awHf0OFnLKku4aSeD80UJrXtXidGtjYuzSYbKXF7xI4pbpa3D1FFJKH+KmfMeb984Rux5mWzmJHh4ZiB7QmrERGeLscjyV/QcztuqDHUtFsS97OUcwYLSJ5QCD5hrPJTIgWSZE6fmtIKd8ir5rOAReLnAr15gr81ETH9yLERqccSb4Ak8TwRlpfWvGZC21bnKgljKhs2pp46fHrzOpD+pM0x7gSg0YV9oFSXsG9/4hnc6+1yjZacwj2beyxdHLYWAdqyYcMEjDa8MX8HBEctytaCoGwx302x3++DYur8XaSEPq532bb03OLu5oRPl2OL2iX1LenpyJdnehjyBGf5KTDZ154n8+QMB0XFj0F0KH8/DG8ltfTG8UViBwi7lW1KO6mRAEEmf9ahIrDDunkjs+JmRM75VQFpnLxcFzyLmkYpiUkO5O0N0SN1B1PeUCWF0SiyJuRFX5SDA8L+4UmCXBRSQRNn3I81V2L5nalVEiZwpjpsAHisdUj1sdi7Fr3Q99kGSIFTW1WgEGjlhR3GHqd5gZ4bNbEnkHaEl5WF2DL2ypB2LfDakv/ihEHzIBx8K/od88UMh86GQFdrMkzkd3RvqIpD89mgWZXyNS7gEtB1InVTtVQNfjG3+2Is7utZANBPDLwCjOb5c6MkfRTaSsrkyqkSXFwqOKO3aCY2cDBc8KjZRpCkF4i72ZmRwN4xobQ7q94Nw+nfltqJp7sUIbU5L/k529+kaM1q49ECN9GAouv0l0r9UolR8Xn4jVlWDtM6lvGAkWMnDlKCt0mXrakd7SB63yInpqj2PoHdiHyIWhe00qdOnGRTjYeekYlSjMA7+kTOnsAOpepiqG9LkKuZH4kv+lRmaLhAPMshsjFGaiaYUifdsKJRCBBcY7nyxnQOzTTh56SSnh2xPvJvFuEwaShEM7RpTnkaUQ4XP6d+oCZDWIGi6lpGGo3SE6wtEhTwz4+BTxcu+kEwVu0TnFPm5uHPxM1Pc3s00Vw71L1jHfFtcv91c+pH97u83vi8O1xszmR+Pl7cnu+v94ffHn49bi8XmeuNO4ygxQPax5h5RKYOnpQkhAqALgnbQAR9B/XHQexAD/K7RZntkmO60NV72SAQu/iWoBfIZNU1YQP6SzBqgRVQfLAzYuDq/GfDVlM19YeyV/kdx66pbgKyTrXRpZMPhRxVpYROGz4ElcdEXL7Fh5mTIZ+z1gdkUQPm3tPUjTABir9Jo13uNmh6f5I19dTf9mv4k6SdMvkBlCn6iyLmxGsoVRFuCSIkqrdSn9KUURajoeqq5fHvi6R6ra8dOmqxgqbW3TJ15USE+E6oHko6RQhF/adMrVkez9FMfM9qDl5mLpfx7PoxC5WpZZGfTopTNUP1KSk+Z9BFNDka69Co62DIk4R6Kdk9RohN2obdTU/BUUI0yNUWpjt3x9oZ4JkPCLH6DzMhKHfWiASzJlgnKfQwxO6J3KQgE+LiRGcJkhnsln4icDXiO6dDjrKMF4qNGKvmxiRvRytQ0igpdNsJSrX5RuWkOtMbmxjc2rJnHfjGV1ArIPO0HtlgfqQey/7EBdDZotOpnzUarMRhaFi3t/i471HfxCRiRBjGbon48t9NjiFg0l8zYWqgtqCDvQbcnAe0St+qDuVUoRPPNWkN0huMEh/im8Md8t1K9rlzC4NrodFrNSpjeZWNPXLsoEWQqoO9i7w5jCaycWgJmAtxi7qeg27MKDBqlAvIpIO1dkerlkSh8vpTUWBT6NgUUT6nAqzs7u6o0AY7d6jaaInQnOFx4VyBXs4c+0isoilVdCcd4uMtIRFzWgJrVUnI5FHBkDd1XQM5kBGw5ALoe82QTfKQ+XyqdNLGz+kWQ5/y//o4DqAWzNzwUimpDLRr7mTcHVMF85wLOJUh7zQRTWDD6yLeF5VwwiYSQmahVtFVXHCWcuCTB2giepQ3xaHcSgTa2WkSDz/pXNwOIR5zpoW4n8Y4ZZkTiHdUBkOWVEUKHgJ3RDN8oNZN9QrR+Rc3xfM+zyfxhdLUamCtJsQAT0DaVlKiKUy0VnbjrnoV3qbNaUybzdPkOzpTo07YD74uDBWflVIStV++egL+tlKaSIjk6X6QKI26e1WZPqJdeKUJ+e1lFFmU/q2ukXDY755QG08aQNlLfIrWteFVuJuu+jdTRaxBjmTZFJRNVpgnioZGxiCm8qFJ5lGFo2PZCqVChqI9wyPlq4Jm5cCRARrqIxN/NwWxSGHufwvQXbrfyvT7xNw855UazSbnjQ/1Ge9BB9CbAdIZAUMd+rQ+goOML2WE8eY+rfuLvywGhgzxudYQBbwpFa/RqXbW7PeeoiYNO/GZra6l5BeoXGqtPzmL1ce2yyAjsaatt0hoHoSpuehERqV+22LRkWsSa28+VtD/Hasbbj0GZXIRHNiEDfH9Sxsk9fYQGAX0VaWYHXFNLOb126iutRlykowmpHTHiXKwLCUXBB/VWFo+cScPqkMLQjdNXCV5G4B2ziFvGCeGo7tJi1NqZOupsUsxVBOmVgLFfRguM3kE6YK/gCiyP18h+NoL+LEMQ2fDaHBvLuSJo6SZHdtwVKX79mYjohZtYwxFljqyS0Ki77hT7qlJrNdpD/gRDTpoD1WMTAhdKhavhABZODRrqfmk6rlfxSyagiiOVnMwUCgX67qbdbLSvqcOskDP/yuKMg1bETtnIO1cJI+iwv94mptlAnWZLHLhHKewjPfcBrxQ6Y3+4AL/+6Dw2ms3KtHEIDyuwPkzo/vFQEM+n+QrHZvUwFF5Q92FwhfvZVJVrk0FcxBI8N/AyoUz+gMPcg8LWhFiy1II7FKMfqVanehdY9JDhRGAF4iH2IqXDLt6glRjiIJnf5gYd9xg4qMjPuCirnrssPIRk4PUkvJbzmcl3K0cDazVzATuhm+OwgGzCPnrSb8vhFCCqT6lm4oshk8F939CRn8AnS5BPDE8Ro3hetOCTXyjlC61Gp3tIHnhSKAlmhl7SPBJjTfg1T/B8anfadYrDT4c+DyDMoigFe5pp83H48+ZE5a61lEFSi3ZXsYrPGn99QhxChEJoVowi7TzOocF5GedLrtlI6y7ZTFlxbZ17w//0bO4LrpFS9UMyG8fXqU+u14048ZxmUMRgNB0Z8ZCDJV52qIMWCCML5k5p1ANYovJx9FBy0jvOkzf2qQDyKYAkkURMh1JtmKeFkUjZB2BTYnr6r7/+msNFkv1CJcSuvYFKE0ehzQJEBUHKMSK1ePi9rFVSUQ0jd5DYRMUYCYwTeoaSQypQef59Xuh2Ji54Rlk4uknNRFFx1Xp5kpm4M1w0+cPjvUI0QLY4qzAWnVx/mJJY2/xdbTXvajb/eqv7OKy0f7BN8qEK2w4FDunL7d93t1V/86Fy9DWzPdxeLD4eBxvd6trwe8CvtqRyEhHeH6uS/OM0m/YfT8VoR6suEM8R6ou4K2oUvjcq6bjKJLmjoWAT5N+IhpgJEsnr8scWEW8DrQ/GrbnHpN8zNs1MjMgSvKSevHzR79ZheR10qQHURtUgQXiEre11qmf4C3sc4V8/CZoFERJBAUceI0kIied/c5tnNkDtNlaBbuxZIk8vCA2hf6nvIh2HXEPQb+cmMKo/YHuhLjkdut2uvlOxM2HI0xeQWTjwNXcjwakLQmVVc1RDNGIcMeuyArLwNoGrUDIWtcBOS4rAJ+dMgeSuFRtQuWhRZhL6syKHk9XMFisyre34CgAor+GSz80Ku9WwijQxWh6DD/Cf2LBJLnCXkeIQJTVGvazCMh73uzwRudLATIsY1ThNQ87PGh1dspewXNuhxxtZLnQW70ldlFdDbXPu/49U8DyHgrq/PosvkX4zJGTDHOd0hhjHXNQAjozsbAw/cEpqXJO7vbG9yr7b5Q//WbzLrAXm1MLcIkLGhTzEDXPCg2BNsbG2xIdMESZaT8InGSBzcLsUBbfLKJRoophRyD6Tr1aaWnPmjcV+6HFaHfjx5I8mhSOlqIal3L12NtBh3qHhEWjczLMg5IBdy58fg0ewlqgaPCQcUcuSZL/BkBuxkcBPRzmeFrYy/iwzrzjJUZC+ivg/wWUaF+qSvCZZEcKEyL1DPzkFA/ytSi5CFcdBVmQs7DHTPeB9zIram2dSNLJiWiubYB3prPox1iA3ypKsZYyicakfFV99D1l7gWDEtcTg6uSHod/ydmpKDJt+hFdC2VRpddDU1Bf34dRhSa5VIHpuBmXx/jg7GFnrfV4gTBPCFZSQH6XvlSJGgruhrnpNObC1Mk9ZkWrfwUH5IhwejJDrRURCGiCvKj/flzOdU30aKiM+m8lY6DUZEibmZ9DES2gLCVxSN0HdzKfwRef8LKkXdeJejEPzTYwhOL7aggrHcJ6FGpsYbpSmbcbb1bkZsIVb9ngQDXnJU4J6u2bUe/IvtPSBvDpfafOZCM8En3SaajFJFtOoazZu68NWvz18qFx1OkPCGDCb7DoZDgn0Sfa2n5OKri8p1tOnrYuB67lwKEy3tPFjmvlOhNvSGEFLV2xT5J7jLE+N6RB/L5pTyFNlYkS3F908bPx7vmW62TlCkfrgUsOCUwQfMj/pzeAJHCsrAeN/cWzR/4gKfKD3A2wDGCNiFbHkctExQ16SLLiuOoW/sP/t0Lsc/6z5mLJlZqSO1tLk1EfMBIU7QN05gIp5Tb3DMyambChIBfNmPAi2byfOjZS5YT658wljzpa7t51Q1kOeUegtfwhkWoiIiUExQLnZ8E7N1HCk/pGsRM+oeNVTykoWBRdmkEbhhgOte0FOq3i3VrsgstrZBlteVeU5NBAlXwTUppHgLun0FlFzF4X9RM1bXiNlNPgj2mThwNDU+SPkweb5ZEjw2SI+h4din046Dk8rXj+SGJZ/w7d4F/yIGSby6tYltIO4Tet4VhzxtktAjNvSJdDQCs+NRxqMelRLgtaehHfgi7Vft4ilfg8tl9wNCGMjZDxuE/89rhwe2Z8OS0ZbG/BItN4vK5eVpvpTD9ypOI+YHnnB5Wim596LuyYUwYrnJFiR7VXJ4Q+yFttxpppifZ4dFQK+HzS2JlFFxlbTAA28vCHm83xtkP7uiroCehj1HZFpm9wUNUackS6jTIgi1yeHK9fD48Od5rCyenClY2fxy/u72mqxMTxf+5o59zc8OKhfXdu5HdZWVxrnqwfXx4cb/RM4vRrsXB237tkv7Wbj5GgzM6wfbjTZp+zYtY3ccevgkX1ycFUNmv0hu+tj/fC+Ozw/ajaPjzZzcFa72jq4YsfUHtjXGTzrJNhoRvC8JCTC9g6x0M8IPn99ouD6YZEwWc4sTktCJyDbK3s9flKUS4L9KGjJaI85Nag+E5zAhRbOu+QTaiqXydPkJ8Mx1jV0dtI050ItScmnDFaQPZG7Ixkm+B5XHnctjCtJd8idJKPveadRcWDWrPmVvSWhTXPCIdXQerZn9laYtLykm38jO1wxhKolkR5SnIifiPCuvZQCZZj5p30Z9c0c+ohc6cz8BhB7/NqcTMyGHSqq1NRs6MwpEvl5XtNu/ii14j2FFZcXdX0ZJlxfxJSkKaDjOFG6l9/GEq/TjEHXNWZjrh3pFqJTjWoOsRbT9mIU/NPISAOdjMg1QxaV+dJkllAaVVgWoWSvSUjaU2FQenLlTklLmajQ8zr4Mi5H5MgD2GyGxNahontZEZ3S9zEndAIevlXpniVVAbujGZNUXAb0umZAXONjlx6ZDubTySbtPVX/mncKxhd9vV4jNtqh16JZ2h60wJHjbqI5uUccC+nE4UZ9yHY6Sc6PTHQqAC4dLuGHKaY64ZqwbRJPjvpKtvlZyIpShUgZL+ZKNadCj63Swl7iSysgiEVwE20CfBl3+N2s+tWghOZDgDNhaGihC55wFk8ajJLi2gGmh7Ek3oowB6E4iM1Ud8o92mzYkFTDkZ5rTMOzWpSG950E4EfBA+PyFCKyAGPhyfS7XFmNubmovySBqRSTisF74LA3XKYxWC7X4EDLcSaaWRPQBPEkVjHlc1JLT/K8mCDCMzg3exD9WSrOKAdzIX9DAwFO80CSuEX6CkF8Ra45Vf71lj0Dt0FeID1l+sYY287mRuY7V8bOmKtpERIV8DcD5dpiiMROIt+XQ/gnfxrSlYsM7Sh5D6l4cFe7k/l/29lGWnuvEEl5ixbkiPAnduyxeTQ3J8bq33/HiPRIMZYxs3qSk40CKd7ff+uFr2pa2q2fMdO9Zj7xz3aqBMfNvAbv1Ce09HvOl67zGmHY4YxNPEiVYVRSVF7qVe3GKIUelN/mRpjtovca3k2TRIcgWZY0gzhU3H0WutK9yGTv5zXTS9kAImSub7Iv6IL4u+va3mZ+j1YYz8u8uruN5iKplQSHjT2duisHHetctJBYHwGx2gyPq0BRxlnUJEwQOzAfCHrmhGPJFaIA7bkkBSaFMRepmPbyIxXwNIEacgCOhF3O6xG0JHNJsuk49hauvkpHpfQsNEdqCSMZKf6x2DxWi+vk6OTqfPFq4aTV7J8cFNsnRzsXx37xprZ6cFNb+9FZb11lamsLeTNkUF/72fi2e10Y1hbXu9/2+syNv8qsN667W3fJuZRcwHgrsgYppr0Uy01Qhe5lFoN7Cqcy283GjZGJwp7IqN3PM6skVKVkQmIc2fqrmHg0WnB2gCcOSNh9PRHBCdJ2o9GjOe0AjXTK11L2F6a0gDIPozMfTDRitEWXX4Y9Uhj8gV8cb3V79SpzfngXpiAvkZrDlOtkVoVWtLgK0uxDEVACHEfYpcdXzvQdwBFxKUq/RlmyBWiRb1Gz6lfNos7ZFjVujfNit7XoM8RGXMjI1E9GwA31jLiWb5U0Tep7CMQ4BKglOqKsVd9kRjF5oTJGiPhKr1v6NHjUM4u8DT02rCu832YEk7/bhRHwSbvnUjLSyFpQvYA24Eop2gAuFD60kNEIlT42rKfQaYv7O9+3tvc4DHBjYWcysoRq4t12F/KCYLDVwrvZT4BkxN1YcQREIXE8G/2kNz6sVfm2IFN6GUeYNKeAgaEy9Yi+fiaS9QcNQpkzo0XZH81JZitPw3AqPsE5qRpXlsJ2Ab39yBLvEym7jOvhYXwlNsNsEsfhi+an5XswfB+TDdb4Rm+QdiPCHFlXT6gHIjDKCPWXqb+IzN6OpFJ/GXNMstDgDIMiEsq8vxeEsjIiIhGyYZtoeLIzCCmmuoCEdpwoDHDpmAaTMdIOExZrNt4HolW810c6SwEuzJS1TDOf11cHU+Kfo5yfQe/Z6zCx23uIuBsrKLu4u2Nd8CmYzPojgWVl3+S410jGZqvSTb70EqFeDSPgrDrkT0vWvrg5WQNDgyT2vkvRM2EhfC2Wbrx1yFmV76mwV69vmjUWFp5S4rIYCsAi1huRm+6j6qCKkhnMeq4rREGn8snQGvK92DQScwE7mbNTSE3PQc0Spqd8nhLv2xjeP1aDKiBNve+ZBO9scQsyGtVM4ixzzyl8QG5NRFBEVZnISlAQiNZLS2/NYQdBcikZPtEd4AaYxET+17JghvE8bdNUpE6mZBu1OEPbnA8LzKxF7kAs9jmd7RZAHf8CccsDHrLWqAYjGZ2CMuhf3BH8AnxwZZEJYlso7gk1iVbWIAypiO2Gdf207KS0lMsyrtG6xeWWb+T+UB6u4thGAZO5flBFg+mojMsTBuYG6E5EldQbuwRHrT84f6jUar2xMRZ5Xi5LyUoVvQhHczKeOct3ZHRveEgTmyy4uLyMifuA5nGkiwiPuEIYyJGPK+mf9USZKyqc/qsuoG1qJsetOktwTqZNlFSfA5hLvoJ489FXDmZdEScA6wVZpDTNY57T5FcqvOR5pVlpI5sOm3ZUaikXO5Q2EWXFxJINSRvYB8R8KyKCLx+rdw678OCq0Z/6koRMS70/mPqiEJ5UhgJbtB49RKgOWW8huEmnBO3TXB2dB9Qk3qSyT1E+geFNABLiTE+WYqoKAn31tu+h9G8s16rPi9jTvAgLua1q51Nf2GP2JNYBXtTS8vflvWW2eK/sbP0wvHptD5zJSPVjRZE9wV46kbllRhOWsye/ZFea4CX7Hk4C/SATLqzyprReSWyvmO3BsEx0QJIVAXofdwkudG81QjkrY2/MNzyY7Vmx39I6xkWjPDHrDRZxHqxE9HGOd1c+lKT6M0iq7+VnzPXe9vFR+VndRayY2Uk7zmG25KLTu6v0alCYP2hUz5DtW8j+eWDsiD/Gx6kdy9EMUtqby9E8G52DXqVKU0cjE76b+2K9Evwcat0mE+pBOUhNBXt45bxIGuRMJgecMU8vyCEY8Mp5EmIWumKWvp/rQTE5CDhvh/aUlL8j11DWhHoQcgl59Jrim9rJ5SaR80O4HHNAYS1pIOTHhAiBmCqak6Br4GgjBTxabzBwKtZjDtaOaFuV9UIfcSX7ffHLhslJKurI4RAGL4uANyO5BmDUyt6mBO2JOTqdZBYmP7c2nfQ6AMiG+yMaGdqhqkxC1pm4MNRSGg100pC4F4SpeD9wqHIUfK2LkokyT/sYtHm0A/S2q4UGGxeIwoqiokSzoAMzyD3vFSIhwPmSI2T2KjIC1gV2yIDzeMTu6/JoWeXLfSr3+iAli/4MKkw2ejr2l2mjQUY40Rm0RRYV/ojClz3DaNXZmXwCoXcylANbbtyA9KA78OcrCNk25dncdc8SQDVWqQ3J7BledDqDei+pMT/Buj2+4tLobo9gerXWSv/b6ok3PD6865y0D26GPADdkgCwgCBr56srv092r27YkZvNb6u55rC6en917O937CNFVaYsypQlmUlRLmQMjcgbRjsN6l9mk38Q+4QrIj9Am1sA74cGmgbhNPinuB9JU0fiL0gwTjuEZxQxveXldriaDnnp3IsaCUsBF4/0qNyGLeRETI20Jv+yBPRF/VOipYysMr64Eve6H9HJ+pgUO69jKzfTzFDU2L9ihgCKnquaRw1KZ2emDeIB3hATcG/XJbw1BKcveq2lvoxAjfiK50w39D/GZxyMALFzudA4fYV0D9zyLwFPcab6nI6ebMAFWwn7KhKXG8lMJDyCUG7x1QyhWoKs7dXMEEN7QbfzzB5IJcuqnJGbgKlQ4hzkOGYvrSWraKNhKjLZILBc+nQa8jQcUOT2oeRgZsTTcmLHF1xYtiJDoiyh0OTZOPXFJ09TuAcQBkvmg7g8tEWlBnOJ7bLQQ7w8wdPWRd5LgUDWuPjnHCJ7bvsAtaGbnTtwPDHoIPlIS0nHzqyfRYrSzrP07VrDZfBsFGUJsqH9IkwXiZO4Z6W7CXRmz9jf/ShKJPBjMCLP8/I5DHuLVoS3NBcZsArfFD/uZFGfHLskSQ6CS6IErUSJtZwaf3OhR8MO8smyVhi+FyGUJD9yUk42MfuSqsxYFfFMBkXBC5GVpzCrNav5E1miQtIHqUINQrcABxRxkuvUaDh05YDNixyU1ksQIk5QiBjnbMLZbXATV9yePSV/t1DYXv6bpkuW0mJzc9zM/QBcd2xzYMsaxcPZBAWFmARAto2Sb2YyAXBb0R/HkEkF4hhYKnURaJ9Kk+jpA+I3ghJVq1aQq9bzlK2iw1WsZCq0LLsPrSgnNjZ8X15vejwxJoDWsmYNBCNy/FKao1HObIkzjEpFgY70yLI3cO/qHJEFKhskmrRns8bwo7Q5Hep1e3p7Y71J4rEHrds4svgEBZv7RhEIbnfJkOJXCcCY4BpP1JFsHHO9yjl5aOnirNsNc4gFltzwbIW40RnzgRiPZxmmVXAMQkJfBN8f9MVbraiYePRCX9gBRcEv5AhsJaJwEfhKDS3Rub5Y0THrPs3TgiLvXsbeht2rqJeF6xyNEkuq1ZtzgIliPQ55nIy8Fjo4MyliWBemwpARUJMp5xliu8/N2ATfWTd5Ul8QCYY3mUxQxX/yRfonj/8UfPrLo39q9E/O0NClA/P62YXAuJZv/GXcp5Clf+ivoELfzRgnZI1/cvRPXbbBlN018xUzvieTMm95qBXZpQRZPFlNc6xXQXQ53W82qpKEwStkMOQgEHhEHa+Fe9gUEOd7YZojxsqnKXUKm8JhLUxfk+ABVZcA0no29OdoAw81GQBiuprhGtUgHC4jNyoPYzwHf0hK7mXtvdCdLZRLlUy683U78k3/Y3QHD12KAC+6DQ9Gi8zkeMivXIKRs94PPHcA/Dl5FP0JBPxjHJh3goNcA1/1jYjqvj8TtDs8sECMjiSMqOWVqBIlytjynBFlmDdIN+9l47OasgJTluH80vj9dH/endr8uAcJTUQZsX/YVnvRuLzpYT6TtTfVAUI6zHmm7jq9GparhGm+SyDDO0r9JGzXKoylwnYS4/Rt9qIk+ayKwNqqaGc/nMzVutsu9zTR3LwYQo7ctxX7IVeG72cqGRsMMcQt/kwOmTlSAa/6NAySYIA/BTySG3dJmUuLv6CmSSOuynbrgJc6YfAUf8nKvIciYzcQxvyxpWr188xTsUYzrXYZuWmpCHLeTUhsKcSD2q2QZo27gTxKrAOczoIMx7tK81o21OOzj80u4duMa70YFmMEG8tYj56yQ5vIMO8HMzp9il77EEd+rkqN6Yrwm1E7TaXReu2cZl5J046Xx7GuhuvJusD4C7Ke2UyF5zhIgOWNh4ioPJGSjfTm+MVkClK+Jq84UkMH7CbgpXsVxRWgFo1aeLoYssj7IHotOR3R7PvC/fcvAhmxVuk/tKtyecaZT1ycsDnT5PLDdPemD3InlD9fa0DyqM9mC1FsaCs0/lqbzPqsr7xsUAyGmWLWE2qjZIv5MkcYiN4JZTgUqRzh33aH/cKdBoocAAQJA9WY+PWJHmm6QooSeDh/djQ/MnlrZx4T5GGNzmayfMfk+RlmarBzz27QjE5osFj2hYnoDjXpCAPcLepHdLYojSxKMUAZZd08yMt9GsTlQB+JvRmp5lEIIyLtPqYm3Q4n6n9MvvRAtW2IUUZFcgFOWPSZ9DXY5IVJyi4RsZbICbHlHjLKJ0c6T6FuLM/c7T0u3P1YUtYxYp1EfHyxOo3x8Y3HmTDFA+SX7aRCHUT5Z4xOnS6HH6dFtJmo2iOcY6loPklU26REQMqoiYozorTrnOo5TxE+Me4hC13pXXsxASwZFUMmXiI5Y2dy69eddWGtZw/2aXoaucfMKK2o5tTehGFDIZW7X4ykp7iCIN+m2Rn8N4DviqiNQGUSQb904Nq83Md3ZeASrO3Vu5p0kAESu7S+s7y4t7VzfLa7vL2ws8B+BdkduDjGRyBer9CZuPpzaWRFtqoQopJpVBQM8kJEoZuJtzSWCvxQSA/gkE+4soJyraZ4UYSo+lk2WANh2qrcY5j2BuvKAdavLcvWmXLHSFhqMFqeCexcTxMXlMayayBqmmp8qnPIuDbdjZGpDUtNWiCqh5bWg04eDTeX2ulMUBBSUXYmKGKzi51YcpyApRghIoG6FDASVdG8okzTUCB68T2O1RI6tR//ho1LYS8dtCwuphWrCRR+5Iasxo5CBkgcVUu5MvWYmSrO0eqjncZ7akagS6KYZT1n55Y2WPrZbVLmceOq6u/7myvF65PDzduT1f3OerN7fNhSNXiahq6xecDVuUZ3amMRvSC+zJEoYUpZuDBWxBvGuJJnsnbAsgImkFhXdDMLXmoqntghcirJClN1R3H07JWpt1zkCzqPDc9l5jnAT/HjiN1BezPZjJCSVPZvKooO1iqwHGJOGlSC52HUR7bmSyzEglZ7P2PASeytBTXRMBlCuY4a3ULbKYyT0qU5ZsDp9oT0LjkrJamk0DQTXYJQqELGsIpfUEKrxUmhk/e7wijDKouxRtmI4rWZKGuBXgd8/gWvOeKIV5XmC/hGSGz6ID/gXsdJTkLn9rbLaUk8WUxHuOs0uCAyNDC86w67g8FVcrqkzzG5pHJiSKKSSHZvEmqTl4nnha+72wt7a2IbO9xe31wUf+Ad15cCDTpZqwwUyOOyHkrt2PA9UrWnq/2+frqcEdE1gFM1AqML7zA0JvMaSigV9ev5DiPYEO18n21LqmSdE4oXd2xReMMvum4QdyybFQAKbE+ePgkEkXk6PHyY1UINOTfmhyj7Z4p61xjlTEVVzkQmlQg6vDV4RBK9eqXZrQw4NaDa2dNJo28x5T+evy3Nl0u2swZSlvv5cnQIJihSIXYJXZ+Vl7goxJachXCa/qDBJ/Nx7ZQKcv57WVv7O5UgDE5SbmdAajjthWng1Q8yPqBZQTfNQKyLY793qPQAbqiLWZR41fA02/vv5rz2fe9vy4uzMtpZool91uDru3RJoutwNJsqLH1AHomGDitrO5mqrI+0V2fh/1PKVSC4zHS2e2ySYJFleIHV5YHVlYzxSVSCLiEmMa0m6RLah794TZIvXQFrc7MyOdY3CSPsGhEfFG0n1vscVWyipjmHz5Otny0MV5MyRqGPf7UWabTX71W4Z24+Bs4GM0Q8GzoifNCX3/HvJgyREyXLZwnQJkJ8QvEgbjEjjhl5/ok0UQZPFAnMEtnWvCpZnHeA5MqKUpBWuHkFGKEIvCpPZE1YHWIjtFI/POl3pyFfMInEjSbLhODKjp5/HyNFcUY3NedbzoRIOQWYcVhD44ILiEEH5D2lHTA8duT2UhEPrdLx1HFc9lKPJtjLUs6z1/GPkt0W1vLEWfWqXr0+a/Qr7LPb+tldo8aGEAbHcGzN8zxo0XbDn4dP2iOLTLVp7YKJSq12VuHY3iQgeUDOuTiScT3fWhYTcL8+s0W6lV6krUP2ZgLHRbLqIspQ8SRLFAVguCTPDLLmA+eMUUntmFuakeQWoeFo0LIqHOQWJhLJwxJg0f//p/fAxJIPa+T5n8ifha7EmSOK5wi3JwwZghliaC8WrVIyYKXu9yFwgAFdJyk1cTWORPJbVZAkQL3AjKfAei+ivFCSkBtFrFsTtTSrshD8PSQSXBRBUbyB4NZtvTc4u7kZAl6710xy1rfndW+TIHMLJa4azac2Y3meFA8I+y792xkid89aBIB2KoavEBz+CU+SFPsb8LHL4VUKSfUDvrTerHh3DhCofJuu9hViCI/LvMw4m6FXVW604VV1e53L06cCwldo3U1pQEAOtYp9wFYtN+xfVdjOlBRxHHZa4E8acFsYFZc84inTLNjVHNNoiASkKE6WElgXnvWnELnmh8G6oq+ESuMjIWNQ6dMUwoxwsbHE07JiWs4IaaRniI303VFyOgpLuRgd22NqrwVdzfHRZubkcKN73sj9Pvczw+rqymNt9aBROczeHPvFQVIDrvIVU7+ju2rMU/fJSf9Prbd2zDYygopicTD7QyPgRTYWzmlol+Xrqq2WkRSNCGlBcfbjrdoGhbOirbaEhJUra0oJlVCtGg2taLBcY6bytA3slIcoshxHPVuyLN3Idcu6dNKsa/IRH3tggtZothrJHap1QVbuJ17ipPEWPosCj4pVyyi0fHidnxgg/Z3uwzBKzCGCRQ58o5h7aGWhwzAXvp+ceKfWEPPRPcFEldDr+d+G7ys3gyu2c/KKbfiCMovsq+pNf9Bp1XtTXxo1ajovqCQ2Koyh0qvb63Sa/U+fOFvVMnxNk3zBuDpE5C4q7ME0301fM0RwMxG3VcQwME6YwutiJCS0JqOBcD+AXzrXjTo8EgIlmFHEn0HGrXjRKO84gn9HpFI/yGWlf8UGQkaV/EVNjxRfi/5HC/iYmhefP3/e3dthxuXizvLC3vKbvZ311VUgBkr9D7uXSS1Q+eR9Cp/YLsf86xA/AJ1LNh17lQHrdvwINkH+rcc/CfmeaoSd9OzI9tr22fLWdznQWg/9f5qNMwGVV7PVtNvzJMgY9eKjS1/sW+1LonxmMSRLbg66MbFqMY3+RW2Hp18sMnsU5+bzu4gixoxd/aUgm5YRNTYB15l+WelbJJbWd84WF/YWvm+tGuUHjvOycv3g856NlG4HAk086HhZTxi0JDnQN9cuGha6N+EMB5fohbWiiiAQQu0pLWIokJdI+B5kogx+r/Z6U9zhBZxcTHxcj2NQhIWCE3NG+MX0kwLBFiTdYI2ZFDzqOVG2Qu3MzwyvwMYbgHdZyEq6NPD+3a4q+/0L/lqnbTcN7yTOP46MPbRoM+T7SK1L4ndITW0BeK1Wv53uXwHkAiayrK/nSBj2zOC2qiUJcJenTzNgJMhiPD38yw0vJGD3ZmDQn7vYa/i2z+4yfVvpJUvTg1Z3WrcudHdnVhZWsllKe22y9KdzVtyb/VMFHpLKOYRYPfWpegY0HmcCfAaVWkzpyCJjzmgVqc8tWpGSDV8mb2D3EwUSGTbCPrOJ04REObec2UdhKjTqX6lAONSqxtm/nV5NnIO4Aw6InKLFygVlZ4fKjzXEkbWOaian6ipk2pzJ65rqEaPIirhry4yrTjzi6cJ4iFWpjGzjkTVeeWbKyXLMmAJFkgIRkEgY/ql0RsU7VWTkynOVBzl2qSQXlkmUVC/wZS+ZTIl28aZ4rlocDBlzcyOp57UmVBHJKbHV85SgHCdRm3l8YQ+eCbawtqeCZzbL62i0bQFnY9QGHYJtmqSA/6zaaMQkNo1TbsyJ6hzmAmd5zMCLZy2eKSi01Pn/z98YmjNZHRKbUJytyfB9eStzCtdzaGeQcIaqb5ZOIb+Zfm66NEe1MuqjJ4ggozuRFvTpIJLLmTd1OmHtJLHZAMhquLFc3B5uLC2w35a+bifVhPdzmShIJEGirDzKOxIdkBWs9+xG/Zvz3/XqQA8rLLQqj7jvfu9crsM8X29fdByqI0gPry0UlI6cNAdxhGlGpm94YzBFBG7LaxwwDZovNhi+s/B9RttVaGvBDedPCn5lrCchlA6c1HY4eAT6iki6vYIZA+Ir2au3w2G32h7QZ0k5GhKavDvceWpr2LzrTtU6d22Y2nAvzUURRBOSEUpqLUruqdQ97lLwebdO18Q/EGEZoko7bGJmqX1BWRALcv2TfHTcfjAthljzN1paYewZOcsvwG3YXS6niyyEEn8hb21vJgR3tqWqeEDWCsEOTbLWZDQzCNaijTV3DzU3ptNBdd4evKl2mp3e3F8Z/L8vYZlAbpQ6TjsBsn5OUNlH4RX8wYsCNhrddJzzYoJtXROq8m5iIhwXFIcj8IT+R0Mem3QqIt8qOwDc6FSpMaiju3sGbHMakpbKV8Uqk1JUOdYREZQsUnVnOXnVoDFACVfIcmQz2Sk2Ddk28zYV8lI7+T0/F3Z5jMhFtmLxqJwvKT+KPPDLT9Bb6wtjTdSSaK8o6X5HkQH199+4RLpyHXj7j+INskGsPiCO6JKFbNVPCSXGc4KS1OI68pXqTxIIgvdfvHdhO+B9D3+26oNKSGq6MPzelX8xe/Id+5XPavND6yxY5KZAZuU2/lzS5oPJub/zfU5bFuHYae2a01rL3vPWE7MhGM1Xg1YTj0QRQfhFVNL1P1J9nOgV5m2KX5sdFXaDv6969QuzpSE0Tv1J6Ht1Za5ZCL+BuKp1X1nWyC9v1FROfG60Lsu/9IS8eWW8IH9O3IczDnIgtSqoAUCwVwz3o7KNuOM7KqbCJemdHAdhX1Cwv13aWtw73l7GF9dq8lsj8DYrKL3xzpePVf7beT7Lf+t5AT8Bq3uYya1I/C66k++Y/728wn58/Qo/Vt4BIy08IT+JlnmYwriY8md691ftIgccDPwoWBNzmp1KEw1S5st7Q1i6hsQrPCRi7+HO8s/95d29ZFiG7ZougnS0uPu/vzw7O7s8C8Wqyr/3kIjp0zvc6dKXKAJKNRXd6fOgkL2eQjOAHw0rAYYnX92kNLQpLTUoBHMOvy7My5zyoV54zWoL6jb53km8n+was/PsHdy0sXFq4Ujwo0iU5dM76cbuLGY/fVpuV3sPXX4IjAGAXNYa/euzi169ftbvYhAKBnmjR7ubAjyEyTeTb6xj+ZVwcCDRXXdOBMKwywb3kxNPf01Msi/8MOXzw0mETQ4+NebLWmKFH1vkx34esAn5HrsUkN4w/CtvWMd8nmZf4LFFJNzzIGiK47AM7I5iLhHMUPsoQY6C+sDUPyZJnEleizZpT034ANY4mnCz/P64Y7E340Dhi+uyWy5MnRBM2YgfzmuPPlEWB0wQDbN+Dr+Xz7u8V2nXzsggpfE+qPQgd88+6ofl921+eMDne/i+Vr+o3EBB41WlxwylNDstzU6rVaHPalWfn5Dl1xe2Q4IXELJ1La1+5YOyiNRcIBnl4EHHLNps+HGaH5oXM1XNrXt4yHv+PSZzYRM+JVI8nsGzh0YxI4bR96Wz7Z3l71sLS9gD6elm4/yu07uu99L9Dj8WeS3ZdDgdzQFlDN5Vk+kI3z+x/72jg70MbwE+OvXr4e6WXmLLD4QXnsf1rdZhrllbvgT6s49WB90G3hOfFOJLGkae6EPkxIF1sDVQR2f4UdXOTVsYte3O4Ix93+s0avSak1PqIgHvFAyFve+EXKl4diRxijV5LDql7DnBDUiz158ImLPhy69pe8Jp/cKlz6OF7w1ELN7MayTZLzmd3zXPRxKP0GimOeRStnZhmPJDcaTkaKS8e8d7ig0UzkMvLznDR9yc+m5W7Q1FpG3IFrTN0FVy3pFGmJGqKSIJAlb7yvuOGTJEEMDaPNe+aZ21KtWeXCspFPXEj4PBAIhgi5bqxfvQu6b3jnYh/pRY2Y7DC2MINt0VQK7ZKJp84/pCjgmsEwe7UtKbvEv30u13k5w6890HD4yMD8G7SfiVWVHwK5p4bBdkn/niM/Y1GAofAn7hHPcU5vWyF/Y2o69iXiVM+LkwZApFHDKC704PWbl4gF4+ILGu2WdvovWwj+va1Bda31ZI5AEGBD9yho+Ek9WD1vHRQb+2UvSq/sHF8WH3qr64UFxf23moHe7zo4vcFtDWQblbp/hHYYi2E2s0/MY2mhCBz2LsYa1rkNGjy+JS9Asz0NjD8oM9bjhoaR4Yo/UBZW/5UT6/pGn6XIpfzrH7evxg8jNAPxF2Uwh1sv6uXNaHzDpoDc8bzX7rkhyXkkQ+oeuzzawkgPMP4Zfh9uFSabgdng+31zeTqnBkxOt/SmHYhn+H8GMafiRneTpJqO9SuIsQ+NxT4+KM7Oj6DzbkF2qUHz/lTc/y7teC6/fI9wwZbWawXNcfwuQTqtnPsY0B0w/h+wakHt43+DXErhe+v2He7Bl7cHJ/4FUudtpfO4N33DbASjcMnxukozI8wLy4j7DZ0y8YUFzYXALmJEGJ9ZIRO9FuVK8nKKEBHBsTBDOboLWULy1YLwaxl8/ocX85qOyw/4Fjt1uv9KpX9d5n8sXFIJvhduJzpn8DAsdhYuLy/HpicuJmcDE1Mz29vrq5tbPMr4TZEC9rdDrhPmkIplMlvrG9mZt7M+jd1MOk/j3cW+x8vTqzUevVASQI2XbTuHgTIrNUo3vVacOV2KGVypu5N1huwl8D1hm5Mgfam1jxmgH24ZunqDXDrvuGDYXzRq1Wb7+dmH0zeiNnI1bs5FlHCTS/04xmV2CjqiJK+pkznGa2EVz4DEwqWUzEr4meCC8C0kzriYuLiwm2E0KbJieuOq06e8lfMm86vTfiKDwklMcgoIFfFKctWHe3ld5Z7abVffnOwoNP7Cj+Gx1mVFLzoh42c7o3A/YGOucEJ23WK230ht7AEIJXA0FY3qascApws7+rN9nyXScxhF6uEPaVe1bEGgfIJhhRlgRbIcNMNmA/cnn44fM/vWyOn5fnWyARMkqffeH7zvLC0vHZzv7mGc5DYZn1iXD5f05xXeUXKfAt0Kk+8v6swIZQWkxlMOAnkUQ4NYkDYGF7e5nN6+H3rcVvZ8tHYJg1LviFZ/iFjcUcse7TbJyIfU3ujtamBiOepPTeWRYKQuRB2QbnyKvWkxbzFBrdSm+AE4ItKe/lJzCy8N8kX1cIa+658BxCpu0dz7BDPOgTa/AsD4WnMFyUwjxrepr9wx9BVMeJDKy8DkTIU/w4fneYfFn0cBqty6UKRqrmyB5kvif4V10P1wf+rZpjCLUOChQbmGDL++3t7Rz+xCmnBhgbX23uI+RIBiTvCuRCWzXKv3fQTuMTWNiMD1A+ctwBtIbpn3ADjLeGeBSLxt6+CAunn9vu9DAEwW3p8H0PGYIQYLazzw1h8Um/U72GmYp55zCxsHK2vkkzfRcG7O4emyg/6M/vZ3uL27waGyLe4tROu10n6tU+hicvwd6c6rcGoODMm5sTI0ULfOl+q8btggu5cKykZ5SZFA6RcR46QrN4ivCLZykM9g5/0N1xKwbAItJQrTCXcll2zzl78mt4Jh2fKLqdv6o3giZEfs6/gJ9jvlKvQGIPIOLA7UwEMRfZLJ2ThzCnZlL9IT6D4YTxB+3LtOufZKjVV6bs2pAiYoDBdpNbEeu4d3Nf5tkbuWW2FBGdM6O9f8s+xS8nYbMlZ2yeGTyNSrPxWJfeM121yNdm7dWKqYjXgDZQDg53cx6wKiKMFLJa0PFiFL8RgZAnD1imWGue2Ndjvg3fgwnBafzZmgshGro87s2sd2edzKDPuAuyN6ELRf8hiNHLZGQaVdXovmRt1UJ2UKPF+mEt/Egvlha2MHkqfWBhwyMuMKfXMDy3LbxbOknrQ0G2Hu1fcwoi2niO3pKMzbFF812ZfTE9d0qt4qtmniZxAOZOpbeo99Ss/goj3z7p8yOUqgs4a2liQCBuVrpCy7cLTePSI94AmMdQ6DjPx/fEX3/Vxd4N5inE91MiLDAxoVoOMy07Yzx6L89b0+3chQl/Mi9mDrjD7csbXJP46TM8XAAo1TMw75glExky7CvE96bxVzafxM1xM4ZwQTlgaxRFHBFVbpZ5v8hBhaV10HlHIR66PuF0INQH1KeV5hvaPOmfOd7XE7ynM+HpnBEF432Mx9KaQZMIV1WORpqYhZaK98tB69z6mB3N3rT5I9C1eKsw/I67ZUNFiNlePDkRlve3l5gVubm3vLk3pd4S4ldQ+Ymckd16m3l4bxbanfZD680yPPqbT5/erHzvsB7fgV93mc9Z3zGdlwKJlxbAVmygc/bu85X3hbXt8zT8yzZ0YWi8eXfcuem9Wd/+9EZ9puREyxM7yz+29pgJt7S0w8wh1iEu92DQualepeHqkLrijcDtmRMmhgbVNbf53zegdIz6na/GmB+kBW4a1DdTE5NEvMymswziUs3aXFpbnrQ5TkCPTNG+7xvtruZN2Wu863fO+PKfTr0gmJJOzc1NuG6OxjabnoLvYrt5uYv221G10mQvs9KjitSvlX5d8L5xSTa+lyDUAUuZ3/dRBY2MADRgzUBfv9/AFIkYOzPC2zaYilJzExjqZGNVJk2EbZdOzZdkKASXcx04JpPuz09LePdAkiW6Aec7jnwV34f+P0MfQbBI2GH5SaohOINs0iRPn01GjhKTHvPsXkZf0nD0UlehQ5l+5xBjBh+DDYmHbn1O2vXY+KkaM5G/fEYWpDf4PYzuN7DY4W/wC39AzNPP6Jkm1twMmDfVMzZLRu7nzsBTV85QdncS/vX4vxLvyVd5SsoXilGyURFDeXFIFHYB5vbVa1CL0K1c1h1ICCwjEQV9cIKpFS63l62LCyPZ2rhsd3psMcAw0Dma3DImT9l4FCx/Tz5jZXDF9gxHcOBNmH4zMT0B/8BI5QATBSKB09+hzQhfvRNpxnHqs8ZZg1b3jJ85+UZrjdjt8ANtSxMN0A/kD4VrmofBL3rz7BSw8gVEDX4/AyU1uBH+AXge+Uezc9loq68gHAh/1eF0dmSTraIV+GBQb3bOuo1+C/8SYT7ZihzfTWmBC983G+25F0fIG+3rd8T0179rDKpXEEGCK0Doh18fFjGS8LrrAre3SGlhbp5G5BsRamUrX8jTfLYRhnEYdgXKPk+801bLCfEoBYEGQDddbZIUAGNrL5uhkxNfIXLA1ts3b+d4aAvdkTdxx69s7W/+f8T9+ULbyJc+Dt8KcdNtHON9AUMImJ2ErVlCEkT7K9sCHGzLbdksifnnd2nvlb11ltokmZDunpn5TBNblkqlUtWpszznOZtTz+dbo7gsmLSBEeYWroscM8/JTYejaFfVlteobQcEbkG7a2ZhLXbZhEUUAGgIYpsWq3rUu3UHze8tb3gtVrvfvx6K4fx7OGj+PRTqC11CMX94O3ESpkBytcEvHLwwSO0g/ll5WRanlh9ucQkNgRBDp29mQt/F/6a1E7k1f8TtWyoWaPUbOyZiFNiBcu13QdtZmbE2KaWNZNMz4ENpNMedbruBBeqyaTXO+NMAdogGLxjQGQIhVVbEfXvOZZFqB4tPJTH/+e5FqTjG+LVMfAGr1an5mZjEe/PE8c1NizE/ZDng069Jndo+O9IW9wpkJ4hOggKJrZvVwfHoWmp64iDpb6DZXZDjUGt3rJLRfHtAXyj5G8RExQ5ZqlpNESH/I6MuiYuPPPBCiCjN0r9LSKcCo7l/6I0McgJVAB9TaiShidqVuHMg7SoLEb8nqDZk888nKJ1GAQgAb5rFMgXzz3qiUTUqsXCesYY7VbJ4YUkYnul5y0sthArKlBeWAi9DNCTwH7ASYBB+vPaW7HSYMQ4tU8efHQlz4iAnIlEwlGskQG50/UDMt00PK+h6u37XC9bHoxEq4ISf56uV2jjHRQcrkM8jViF8b6cnaJlPEokJyDzisUohWYjl77OEf2J5LlK95sWmuCs19vsb7sXsiu0JYhPrj/zj9jYOiYLTYNaUqkbHowOkhLxbPlPE5PXTGprGWU3G+9+f5LMGI3/QoCCXaF+qghaeLQ2Y/Mvs1aqGjVxml69SaeW9UM+jxOQlucZSdveLHF9JnIvtJ1OHaBulAAijYgDeD9p/E2KDmwVl5czf9IehY0f9g6ddoZUkuM0S+wK0l+oSdnB6h/QKGwlC6kS8VuLysjQvWReAQf0VD3uiFzgZ0NsS5GBPZCFgl5ADPVgxDNDE5tHG+YGwkhsnR0dnCZbcohNo5gGowHLwS9so2aFoltj/k1kZDiQBpHYMlL4SyC4VPlD+QOnqCUmJNkDSqEKd81sjb5QREtxze0mpMYnOgGyplrkvQum5dUcoatl/mrgR9jocSEg3qoqFCkE7wn0hZTS3wJiqsDj+AU7rdvpZQ6QNvAjPqphzpBRWKwAb5Hth/B5wBJDlZ4CcsglliDszV29XE8RfxlfVWEWcQrn8KsvkIW08AK4Z3GaRX3/+Wa4vBCYB3ihmh8YnnJcqaZK299SyJIig363lhOilAoHCei0w+KR7CNUkOkQ+opA6m7zMivGoZ76KIWmAXxAatuvuyOvnw56xuGu1V1X0qii98+g3mDnujoWhQJ6CbNoUpEAkoia5A8/nwCuGJ8waDkaSLXEnw4gv220KDUH8Ne8ph74ku2XtlDStUobPJvS4IO2Tl/S4Ts5ZuXrrEN4bfTkOuHQu//ptSUwqa7MECT5vskxKF/SyWa5CdAvkDzCn53LHe4c72bergXsPSGAxDcCB94fQJlbEUWfNbxLBjWgr6Tc3XKGonOJ36XETjSnqOKlOgV5E0GvRxql77c30xFMtvcsJ87kPx147v9XbUSGRpAGiEV0QbZFanGCI9DuH1DJHTf4qx0eUS6fXbKCmi14KIZE7gZ9ZXKzUMgUIC4oW12f2Z7ZmTsX/tmY2xd+9mUPxv62Zkxnxo7jB5t4noTyKD+3OPaqWjvbciBuiQVYrK2VChc+EYSV9UbFYqpGvSEgjaCrK/Zr6O+NnlBNEaonytqkfHH9JghxNsiYlj6HVJj7ArogigK5aZlGFQLgqY1Cc2Xs3MNoTSlEgFKUg3GZr6Pbabk+2Khq959YQzbGAbg3ZbUXfaT3U63ZCxI1LGR0yJTEMJ6ng1qJGKPyu7VB4Aw0XrCfvkWFqQjSy2u+r+Y7IvQL64l/zVnlRRh/v5R90iNR4iY4CNaIIWZY7UugH9SoVephfqMP+MfEQVHGobNNamTIt8jZeSr+TSWrCWlBjkl5B3uz/8VFxcM7NJEe3bv9u5skfi3GRT0nJrAVWKwg/hfoFeJjQS5WaYYvLAJ4k0OpaA8i4ODnRFxPcvxsPnKw4MpNBnebg84zRSDYBCg9dL+dSp/e4kinIOUOZrOUwK+9rZniYqRfmosr9+pUGlp2p7LzlfFnSacqc0Wnpk2j4/IoV6rBORiBQwGrG4qOgN4l3LaGWeMP375rvN/2+J5TP9zOEklMuRiHo37/L8XncMgLqsIDa6hLNNzmZrewz/CB+oc/MbLrqpFIT4HP7r2ZoShNvrKbUbKUf7ZFXizhCnMzMDJiqaq5ofl6EIlQWGN4FnlDpPkVHKD0f7xn0hSUtfdnFJ6fP10O/18CroshpVLrjPBDy91O/583grJnxW+Lle/pspWsgEBYZCQ2bdO4Sle0s8G7NFwviQFJ5IK7rmW0MzxefUz8KUO8cdnrFRpA0oEpf+RYSAhUSyO1+gL6hodCqhjaMXopGoZNjCMPJjoedZhdTexJa1CtUArmMdt3WndeeaT7NHO5UF2undw/SmfTOmpAlSitC+/B63O3qFZJQvl6V/YjdeNcUk37wHuOTwRJ+O9v6fFY/2aoLpeN95Cq6KCfPEStigNf3hXV76w477ott9Lw+zIYeK4pZsyV+ggIncvCTHw/hhQMgFmtZfSqa8VJxelFGkowwZDa58h4wbUJlwRx6uQ9j6nwO3jdS2L3NpSiwxuMRmzyqhIlRdIQNEkITA+idPXYxARLLd+dA2h/79nSY9nTjZO/4DK84rB9sIQLPvkjiM396UV7pHLGoEe425fbXdDr9LIlEh7NBSe7JbxAFQA/9gCHQcAF+bs+rkzPvwXjKvEcVh2KA+qe9TZ1p2gkawXgAepCEd4oOITyy+msgka+7fLEE0+unjho8hl3ywk8GBknKEMQeI5NkJMDAznXp8RPKfQcWYt/tUk1VPKkhQQnynKF34z2+dAJaCLyhh5yKlv3A/UMPIOQ5zuuZq6btcsiqxChxInklNj3457WXvHSa/tUKp4ueobZdiwMcFjTGi9l1AA8npgoa8CZyxsIcwufMe0ZWilkJKrcQ/UMh//Bqs6Py0pRxLsanNS5b/dz2FZVu8oEDfclQK/RMiMauKljEK8AGyeyPIvpEiHtD4g64OXRoAA1oLLfmvPFXoy1sb60Qr0r1EZ8TuYeBA/458QiOgym24mjOcXIherhcLiF+ED/3nvCXXAe+M8x4LtFoNA6Pzk639rfFp+R8gcxTM5sXbOm3OQeYgFnlLasS6XaFnWwSNwShPCU1RPZtkqP7aUaEwPaU5kh/AkL9CSPWn4gJ9icouJ9gbW6YmOm0jW9C/osOm1U+rSz4JGiB0r6HHY1SZVSIXNWdcNbCxAhJUAgJLy2j00pkx5zDv1NsXYxSiTUGmjy2JmdBm16jYackjko0rDy75PZt+AMqVjlXPxWXnTUu6ieHe4c783ljaa0kL4XCQ5rPldAIYTqBG/HWexTzC/0leJblqU/avmVrgVDly3LMouftmVRMIXthha28Jy1TrjasCn2p6gBLWmLat4FijNhNJfDmbUpMbdq/pXYXSXo1aWZISHFP0TtTeU0EGQ04bTkpN7Xlxm17/ScZvQD4C0gZgG3z3TAnKx+3jeSjki5BWTCJ8HENcDBY3V+49tctvpBrmnM4TNIyuWBKRTXxFqU335lFwbPCAHI5x8wQq6Og8iGLX55huH1Z3hkPJKdhr31L/2D3oPLKHBsoZZkaac6WAvI+CWk1y8w2K0SsUc7PPyeXUz9KeRZhnBEQN31lyIcV6XnrkP4kHdJ4QFE5McCzT+JFY6ETCvEpBxOzAsjDE4LMXYLnMThwxdaUZa4tQlmtXCaBn0AflReKPbHhfnMfVxCdz+2DkI7dlCUmFUbGfgOhIF5K7f5z5pvkPSshUd90O9SQK8WY+yUvWYQkka6QzVFzUiAA8hq65LyBe8HbD/HGiUtFbyGNM8CAfXjb1r2b5tThfpYZ9CakQewKlVY9hAYNqq+U6aUHlQrwd/CPWEKr5JkqYDwEb4LYn2Ik2iincMJr+y2v3ShXvcBtJhSulWfJcij2aLil2B6hRAFI/UwuR5Ze8vrGa3jNm0IpqRGz/M4tuEr0ZzUlYnoQ6Y/ekTBBAFkXZxuNFbGlFN2g1emgMFIvJ7sCP0txkV2R5nkSicfFqdnIhpPVr22RA6yghaCXgJWkNcNUIo6mRGKiA2QQzE5QBeHas3JScZvEeBQHqNCpj+nQ3m6kSrhNVWaUopLCKATPtuVKwl0tHgSnzhPKxbxuUaUXhT1VKJDfK2trmrfK4TCiQ+4U9oxhEgNkGTyL/yN30d8rKwnkPhb/P7r1MiMvGGX860zbh9A0wG8BibiS2Dw6Ormof9k7vTg6+Xi2d7a/xU1SGbCidNrj6zBzpNVE+fFinTD08torykmtacTrnPnji4UX7EZ4boeYKrnvKkNCi6v4zv+IbUWslaTU1y1cuTqoBeczSwXMkFiwJPKKXHF/8QdTX1MHlEjiYxNetO20XB6YMgGlLLbdO4/g6KBFAA3W8dHGGZ9U4TU0GPrXo0EbHKK6QFRIYWBNgRWHq3SYrZJXPrHVFmPSVo262v9II9FqgdgOARcdt+E5OimN+7OgUJGhXchWVeDfgBOy1S9rIUGHIHcKkOZW0lQ7CVge+VYYk89D3s54KN2Bj6JXDS8YN0bdsXvttR0W2MS2WopjV5NCX+2Ikj3S4J5R/C9YCf3ZSrdjRk5TMwy3aD41evWoU5iQUUUVRJKmSeXTShihH0lBN5AmfJTbKnBORLhqgzhtT4pJVzvuxCNlxH/KHuBWiqzUsUtQ/LLpDu9mTiHDFtpVuDrI2B1CxesWxIv5alhg1SoyU2gMWlLZuscn6EFLzBM/TmpZEZVFTuEGywrl/+BdXzda3Y5Oo8eAfPrsQvywQcfn9GhIP5eYssI0mFmZQdsdvB/oDH/wmiBbhX3tYSwRt4Nhly8m1zu+l2BsKs7qkZxZ2pCkNOw7sTHbeLqQVyPSvxdjYOj6GRcY8tfgpEx0TXTcbkOiZS7/+u0q/RsRWkESecNvXo8DALo1MF+f21mUS8PwjXQCGqgA8/4xmgb+sKF37Q2FbNMSQojrG8fKRMW4i3FyQwyK1EQ1uZaT/TZwJk/ure/rr03Ra+4UrteqzqCzSvY5quKKWE7ZXAryfdEF42SDcTDwWiOueGAO3FBC1l9/8fzUc23i62Ie0yyKeYwG9FxwHLa9x0YgBhm+jYediKr9e/B7IH//ve1kH3tdPZ/Y2XaOMQWyaWaFdpPTBlEgXrbU7KAJ2dSN1xd3a0hiO/OW084FsHsDvD3uKKYD/HwEQK5YacQNXFASYedYmcS+P6KsAOoEnQpHtS0zGnTNU8yLsonc6dbpaUNapNiFokwruHpWeamwdcTkpTrOI8hs/AsZqvw9S/9mCi+mq8I7fpZQVFKtZBKr6EVJIgOMgYBtfKvf3iAWA3LsD1TMGcLNP8kiHeh83FEY2S35c0imJ+aTkBkgHhIVoJEZg1BflUI0MLWjkZ6tZbnkARedcLLj1nVHSHM4C8lNICcIYNLgTwSxL/5B4jT5rjs9gHRkKaRF5+DvYlZ1+iBZA8BV8kH4jpBzvkAeh3kXFMRB7lRFphoYIzsYB7fn7GkMjBlGGYyki/PPp2IzGge7br/dZSnU1qn+QkAioOgICg9s1Dd2txrnx8ALtnXS2FzXA1Pl+HW8fR8VdOGsK3FCNi3zCTn7g1omRqpKhGMnZC3zrs6PyekzQhord4c+AT3j5MgU405ZaaEWrQw1feVSdKsylYxn2qamc5yhTok/86R9kd7K9OWpgaZNx3bI44aBETDCflx7ihcyERoS8QGR7kJmwfaUyDlzr9hbnFQu6CCOgxrX2WSiRzUNlXUCky84mMZODZt0Aqw2YBNIxGzVZhxd6oTi8DNuzMcb+1/3jhvAL9LA1J3G6d5XNu0of6W6EO3MdJ7s13cmjSjRGTh5xr9znxIKn8BKb0FYRpa+zsc0mCF8Hne7wCq5kaJIS2JZkU49Vit8MpmAAOeEVx6MqTymUAyznlBSBh7SurjDGysci6RPFtqZaKAwa8U+i5WpxvnJHp/DNy5JXwakm4BUWPfbTys73gg+bzBV7Rz+CjuwTvdW5xoCmtss60Q+ZVxOj6paPiWbPypau2K2EypegT9cwvEr56UCFp1fq1/Bm4j2KdYkiZUECOweZ8gzkz3YssEBtap2Pw69vw2NJ4mMLrkcgw7pJ7E5rIrPK7D1wNPKUQEgBZ4AO9AfYvwGK7hVCAEIX+AgALJWCJf1RwdA8J3BH0P8MPRABxBq8OCPsSu+j90/xDLHH67VDEUxXKjEJ91KtSKGP+cy41zNANPJ1tnM1snJkZhk18Br4lwtzfzg7JVL1F2unmfE4Mtjz+Iy/iwEzTN9WkFmkWXpCMJDKytMN/JDHxRfcC+x81vsNniL4KQxFmaUrlJi19Dxfv1s++jkwOGIwsXRyaYwtE5PdV6rSSE1y4mMaJk9DDKMJE9ySELtgA4DDaZeiPHOpHRVYN5KFVeI0JOuxbCzygYDnyg62QUWN/yrsNFAUcVf4VCPgCnZwQO/zAKxuoKZJ6FRzOyWRLBT0tF7n/yBAVNxP6nH1D/pnFJHpmutD917/40JgQL9iJbC+tHRgbUsCpj2Uqgs0nuY1kvYkKIdNI/qvlmprlYiWahnISjXTGIbC8HNjPyZwOu3ZwgPlk3oLbCQL0q4yv/GeL5m+JCbVtYC2NnbXnTaWNYNA/1ViPOvslpM52NwYdFy8rzCiQ69yWFN7J3Dc7ED7Wwdbp3U94GY4Hx9f28Dim/sbWwdnm7xbahKBlBGuzjI4PEcD4S66bGP1Kw2/0JhBBVcpAGKUIX8irfA5iSIOA0cCwt69BEhoPw4yJZS0b7YkmbnEvtcR2cTUtATT5CWqIrRmRE2Q0Uohf1sDheL0FqwOo8eeM4bDlN9X00ap0AOBPnNEDLwtct0bWp7NDRiJ75OkmNVS6D2Jereql6Q1PT6evcEgAtFrm7R1qCdNymfIjl9BIwDc+HfFV0AhL2ERToRCvTNjTdM0b2iF0BnhREjpkKD9iPtjShgahSsAx3L5YKlb6/SRCnjzJk3j2092j8beFTIU3J4USdFRvjpuS5PUkfoDP3I9qnx6RVxNti2VpOXGIBMQdIqtCEfEwk0ajE+XSOf8K/cVXp1LTT04e9cqjGFPmboJMqFVb5PgbIBDA/ZjJgmRUhCUZYTDNgf+Fk+PvwnLPTvI7cJZ66gvf6dRCBespJMGh4T0wOccAMDyalTH5N4UpJg3kIj9ZXfW5mHxsnwc1JRn4jHKHDCNPANCP0cyyLAxcKSaoy8Ya+hHGTISHAXeEGDbeh5ebAvCwwATgkIjMRNMBNy3Kcm5/T9iuy9/Zkz+S1qUpLwUVyIgZqFJUlwqwfU8GtrWtXGyOfkspmVGR6IGcdJ/lYP7hwxcd5+8IRmGHDbZYliRfsxGHQByZ7I5RjWN58pzAvdaKdxery/d9Y4PGpsHRyffVHRLM1uzMzGDVY7TaWeAwQrUr7LAanIqDFPv2JYNBTN5fiasksatBNaxkVbqtjoc+4Oyn2DCYc3QWG+O2xQFtUsJJ66K75S8sSaSnS9e+02jof+SOgDDXzHSXX+ogTl2CvVrrPB+5N1Ch5/7Ggj3Hqg0LoPXxgRC2F7/hVbq7SsdYEQNfb8bBjFhqjHq/0G3CboClyuj7QG3TRp2Zi2ZG5tyNsYGQoeHzUq/6wf4s+r+wERzpe6QSNTpPLPhRj5vLZ6441aD9FG/sO+45+11i2gAv7Fq4idSPH958cusNUskfRpiAOnTwe3nf6jrd8WJb2olpCQXnzs9j2xntOp1bPxsAkOm21/yJ6GAqYZIXAqK5ZrBv6DAgr8o6TKBUu0Mbod+g83twh4CLLppjC1nx4w3QPPrUgpC8r1b29y42CYa3b6OSo4ls5AvmvvCQmyJCf3SiSKixFQDpEI+zPFbaM7APlKOBzJxVFXqDKb7R5y5AaM75FzkwzRAofQRKfY9xQrHX7ku0seXskv1mw20PNeFJa80MGB3eotw/pPTza4C0nY6paA4jv3zb13qTLPMt1knhtGhacap3Fo0KWu0Eh+GOmbMag+r0Tn24biESlZbuCpYHrGNIE4+uCtRMNNbYc7jkKqYPkT3+DohCrL24WopJmv1xMYNjyqb3lUuVMgFwbjZrfTcsBucEf4IV4rVFWLfu2CbPqFV0QPSlk5+SqRheJShfiHDvCIW93ClBk+DciZGlNGQBZcoBn+rGwDVYjBMa0ZNp4xmwYQ6+xjlSb0yIcwza3XE//0kLENax6kHJPUe8n4zM2RLV6UZI3hobGF5ZuQtDRONIzA8NBSSnvb73afGgNMbI+TZ5h7UzWySElLyGRA5vcB955WtTTnthr1/X3nry0AtQuTmViVaE9lrYNyYhaiyM9f2Y6N4mdX0Z2j37CsuAT5sMxKWJGqPSE/eQyLAvdepSGGNpZwW7HbBsZAgX2YcpPCmx8G5c/82EanlvKN3jqycqFt+47hlmP6z8+LfEClKtXp+4Uqb+qgWdHNrPP3G4B7rtK/OeEaf058Hbo77wl2oCB8I9lO+F7h69seiQuAoP2sCX72BRnzDvcdvmP0Tczar0K7bcgfCd2jdUWZzqfHdsrsFTZ1tNKh6AIqztVKzIbDfBSt7tfO4OVpaJZylb9n+EmEKB26LVu9DhNT44pod3RZuWnPpxoFjwWUqZVXWApkvEky9Ra2wi9dD5jzVIuDj6u8fofUOmdi4BYJ2hWS+JTTrgU+nAR3TaV5EWAyUqk8DayuR9x0LZRNU+5fH5l3wntzuP5jzJ941fUVd3qFOHvNhdPFU6jzcpwxfAgeAwm8cf6ylJp5+U7lYf1mY2bM9B3hl/aaqGp1NXWYeL0IK/35Ks11lqVmtXt2dpwrONn8TDlfnjn0RzPbkNeYNLs83dRVC4XavvaHnjtFoLtBfN94A6bsMYhJBF73+swL7DvqviD6BW9HJXfiK6J/C/x+ZEt6qRbqfEggoNMTw1zwbQ7bo4hgSjbodr3HcaAyk19ScmIlCxWyL6rm1FzgESlJsBkqK6ene0eHEBXsDBrsa8vlUBrO7h/tnDoSz3qpRGBb1qRKfsn0Mm1lAcSVAeHJn9YOaG4UW4JCZltH+/ybWRck0No8JqCVIJR2LV14a8qFNv0lBG8j7+GVJ720yvGA3iymbTh6FmJvW10/eEGFgVa4XEQp/qysE2f8w1EdUH7tSMTMHpMZQ/Ni8PBXcPiLljFFVGUv3jG2x/b945eYrpj7Kz/ZEm/qfhDpUsiy+J96MHxXsgPiH6xp9fa3hlwrTlbqQvt7HznwhqmEJWB6Aia7fyLMGzxVo9Z0YHq8Go3j+inkW2xicO6Fd2ONMqz3HlPahQN8WjUPnyS5DKZc85P9XKUqxi6Tnz7zqwbFnhLRbBp+O+gVXqiZ66LnNaZGP1+hdhgig+sBTbk0Xy6Xzd6aqCvlYHaezQ/mLjFFYq3YDgjdm1Bs1/ahvPQY3C85LBZD7ovSMLKnTXlCfhVoN0AFjtGnQvE2+PDUkM3bWqT5ulNyOjeC2871FG/CZeTIVaSb0f33FU3AUPc6raFvLo2wnsDN80MSy9hSovnUGG4NtoZHd8Cv3x+xjYA5pqXK4tILRbTfGf/BJgzm6PrWzt6h+PciL2EydBzO2bs+8NtjFEtiOjaGnirxDeIMHFho257Q8a3+DUWnj/rG0XUCDeSMQxs+OtR/lwMmoWuSdEQ++pvM9T+6qm1cdTLWpmoOceryG48RvJ99+VqUfW48+Xs+TmrJhbDKj8G7yqNekNCjIbgIhnBBo7G5d0ISLeuEfYBTLNuNrnj1zjGoNUHbceoDbeVGZaG0PIfjWBcWlfHCgBVNx18KA+DMtPabfxZEgBNNQfNLW+lK1NpUQsUuThE+b7qeN/VmqKIby5BuBZKiVM3nw4OLIJ0FCzgrDOmlJUVbEpi0P8qcUnfUEhJYX1+4MjK+IZs3ukHbYYVpE47d9nNX6enb7E/Pib2VhbS0p6r5jNYU+eVr+DVgrlTBeg0xm1PITn7lljb1qsg4BS23bzhGY8zyf2S62kuH4oON6ffBP6RRRm/yhnhI7Ct+6eR/NWr8utCRbNFikF9dWIDi75a0YDCHlCG0Fw8gGKWd+KHjHnTk9w2/h6iet7lwUr18qJCJxoVMIj0lbivzTG0KUJaeUsJiPVDoyLNraFoKR1i7Dd0iVDsKjwP8Vvr9MPG+UrWGLbeSzuX4AyKl05u3bn8c3NIX49fQaVB7DJ5mHca4PvwmNg+6YmVl2iV1WJh7Yoa78AG54obqopjLyJyoB7f3nX4G0lBzR61RBriMHbQ15BUOBNXhdQB/OavzgK2Vgde4pRZS/f7Vz9iDA7f74OKGfcpKzSsu0ofW0a3qQFSoxSNrrICXmzIRLCH/spU6xae8MhISMp3UJELHfuU/jYamSTb/+McRZnHhwckZhM8aZyf17e29De4sxpzztRf4c+KXeNTSF/vSNywI8tJmCL/du0O+OcaNF00K2ZBXM+TIdTCfLkrQG+ndSzKCT82GRdFL18R05EXZrOx7EEf2fEFzc9yH9LQXz1Y7No0VMjxghtdrLcsERyNf2E5DqvJrG3aDweM/bjn+/NeLEx6OQjQjxtZjVtZeYQPbl5jLcHqg4qWAF9/aymJ4YZTQlWs8FPrOAdxzbfpaI5qpjF9YCqE9aa6V5RirsQ/dB81+EyvgLvVKsZVhVLJe4WBdkzRz8rSwssbPTBmvS4mjj+LgBv49Pf8IfnI+QWbps4Ezp3R4TiaSNA4rtdyV2CcTEuOAhBhFwD8p+IcChIR4+qYN02t8SPE+1bBdAS9qxTQspujv/6Yrpr5OzB1QaceAwkMtQ/HplOn4Z/k7XszqJjNc27+mzL7KZGD1M9xW3lXWTdxAQARsXDtDt9lEnBroQWmoogmK0RB+O/PcHl+IHiUogNPtUKLwyG+Or+eJXZj/GQBRKX7CAiEGP7Fmr1+ZQcztjDOXXFpaYup1sf7doWZoR4J2huYmHUdYheI/THeGe6qsOTAjZqgrLuxIbD0wxz6Ml+wrdInKytFnyB4TXZG3WEvycRViQb6QokbmbTrZEyd7KoZk87uN4UMSjzKw8FDvL4/yjav0yjRxOfUHJ4xgjVHMp32btuf/7zb3mqD2Sx2Ihgd+/chPHOa26RwHOA4hOOA0fs8FyVjHk4qmGsxH2X2cQYqggcs5yO+8ADh7RUw+R2MVpEYDC0JdQGsi0GE/JGcpVEpUdWJKYrAqZMLU4465PWC63hurOuaL56t1xCMFeQxim+6MOkxvgn+oZhuezh1FxJhQhI5vB5AyW4c8QCiigeAhyMlMkN+RypEIM35FqJeAANvZgsQFcUCIYaGtyyWGqNZSyfIwzUr06Ap1ncoXmTLfUO4j4t/avl9cB4bvfirO+id7eahFo7b81Ob5wTFhwCqbhW9iLvZd8PDBcARPPVBfxTYitKvwO2VbyIEMjKakd4B/qFpK8JaYYDEru+mjHkkEsEOvK7bpe6ylJfaYsTj05CEgCwhh5fkNdRo9AyLIsIIkg5yhwDIzqjMz7QuVZjVXLVDVvpd9TGOPcBNPqyK0+lc8JFYg2h/qXPms+J/VENGQ0GVgn4nP9ICqogyjOtcY9z2nhjEnJykyX+Uhd/LOu+0gJTERSibnZ5JNt3XHAW1mfByI398N3hvfZI1pPgTtcP0aSQFCN6LcXiQ3qm9Co47pN3fb+5v1Y1aD5+Qpmfdo/oh1es78zcjjfJkcs7+Bmb/IncwUa4l6C96jOLTZGYqD/vApMV9konSVJq/EU00lhcg8TzYNtoVcBFP5wL3u+F/9PJ1PlDllO1uTV2e0eiSBs1TiqKRmeaOgPYj/BgoBAytkYcHE4XnjN/CA3bDchMPtTuA2uzpTMLCB1FGRCGwFKD0ebtBzk4GsGTAzklP4/5M2nakcN6LWWYgbByT9cbKbeydbG2dHJ18ap1vH9ZO6+IivQHLVIylRzElONqnlrJiF+Wq5HKWEJVH46makTBsm9BMQokit8A132O70wEOWrge++L9bPg8r6uVN+vmfOAvQTImv6RM6K+SM/8u5xGoS1Xz+eYopEbNP/IP8oHgcCRX40H1KUad+V33ShkXB3FIiM8C2AWV5WThffJ5TzcWmgEmVR0VcxNni/Gec2HorALjj6vuJ85V3byIHqpamBRJwzhOeOqfq8mWFKanQ1di4r33zyJ3161e85jLuMiJyhGp2KiZqE8srbJOedeD2hdKF5SyIbIAvqHLizbksx9AJcHPALQLJfuETEG7uHe7wNUgdsVCKUi3GzJwp0TYd+4pP056FrIEzWftnipfz1xtfzqZN/oEDeRMSRgekmZ6GFFY+DHwn4WO7ygeYMt/C4rJVSeXQvYeKkDMAfJw5G4vNrzsDcENvaFtQxLwD5a0pJh2Qnxcj0pgnKv7r+i23izXDHQ3BpoMDkgjv5ThgahHx8vOQLZnZRgxtoVsjzw4kWZA0T0B3c2KHz1HVDT4JqRWF1FqWmS+nPaGjPJV4JxgMO/cuqn9GFguumIeOuG+HyQmQ76acV8Oz4+6Tkf14W9oHsoF1oSO08/nQ6CBbDZSefPcmk/lNe2zgBisJs7Ow/WTkVbCIKzXKRlFEtqCDS6JaWGaGEZTEMgJC98iqmgLcUkX6nwGfPZfEZPNMOZ/PrNc3MywHk6poFwz5VB5fVjAip+mcZTgRz+ObVyUnWBz0RSx2oSFSgrwKJLw6GfLnwp37sCBBZ3OYRJGSbcgiNPqADG6pBR3jBlqWQn/Kan4RfWkNM9xR6LjC/NI9APzo0am8dZ7/Lal5vyluUoKWDH851Vp1yqpPlV+6XO93qJCkXv9oCrSFY1JVFyqgrqPujKdXKhV9OptIyHRTrlk8YljNsvHgD++4I/61NtLHfaytpmYY0tiDrdq9OX0Swqn3WQgVT8ys4a7XHXjDpSWh3u31RZt95sCQek8R+WcAX9R7ojUvNPbB0G+x+E5gRiUKqZw7AJpUTK+EYC7sOJmPHzcT3BBhZixyuLf7oBBtcuHdU5m9huWvhKHQ0i5w65zwQQzD0SHa66yzmFpP8sP2x5iYz2NTJD6YspHFSdueBvbATiMer6s4MUkV46JGpMIyHwz8Cscph5POA8ElliEEOFOrcRdYJ/CV3Df0MZibseJmaPojSSJn6OvObDDoGFaJaVMViThmwWhNP2NSPSBTnxEWCNqgesrAt9j5u0MJncwma5sMss+ophRBTbkB5RhdvjAb06ctf9QBW/eov9/pe3w65ucvhPdT0SLsqOIf2lPhQ3RXLSIHSKVg5hbv9Qa+UOCyN/743slewysCe6Xeao29jsnIwC2QoYnZrh0JNBeLwevfk5Z2snVwdLbVqG9unpi8L82h/6AnAF8gNTvoOrGW1He2Ds/M63SlUMkLLfqXRuG0ASFUyCgKuGtE1leS9YHEqnD7bb+3Av84c/l5oPGQ/6fab1dW8I0l5OmqQAZoWrKUsWYjg5P5hHYwUtfCecr4KSI5COQG6XHePTo7qO8hhG7uolRysnuH20c4FewB5jLLi/9nA8yju03L3hhfpO4oVTjtL5S9HUcw/oKkN+A1b37pmmkAtwiCTEfbDO1XYdjmJHqtnbbOVZG5GPvRMYHdyuoSH35QvZxnx9JjGj2/yX5doyimSuahABPX2sk/Gzd91jtZkVhPhLD9wamOzzhhbgvsqevcvDcO/BD29RO4qhpAbEmnQq1i9YEbRWMOXmQcRU/UN375l3jUVfkE0MfUj/Kz6S6gz3Mxn0ydJ2SZ08Ux59MJ3NcKp+0bsWh8+08B+VfFpFEM4OiDcJzHfEX82d6WrpWiahUakUsUBCnQ91x4TRC2xD+TXn8S+jCywEgSIrmGfiFKwndA27BWsEhA+iPLJkfvbBCbAWpagKFX5LdGba/1r9pc1sP5Qv6I7jiqcY0TfZfXwfQjgPRoeMZQ3IoFIo+yNbdHqXutj6+vveGWQTPyyriuauHiGPBItvYm2zLfHHJIxZXpkX4xpFXtDQqZgpU+nnjQovV6MNY+Sv7Z4KUrcjnppcSD10RSRGwJlzKfYJbyCKZg4TBERACexo43qne7F2JZgNNyszMMbBwejI0xrlOvUrGUodsLzIsN1gG+uK1J2IThsy+6xH0vsha8FqD+LPbIu0636+Iay9SI0VUX1EoonpFbrD2nSgMVqYDyQu2lWhaGUSm3C712lHXyk81FnRdFP+toDJODw9YHdekm0awXtnQKVPjEaEebbi/vcex9i0k0cwKV7+yQrwMCIjxMINKBgwQ5lol4k2nUEQhFBobfCnJ6SsCsVLyy1jncJopeacmsDaSSnAHJA8Zq5hH/qMmPLzGAt+i2hFodc7w1ZGdbsaiMeFnmIXm6u7W/r3KsdMzzFgij27r+aQj5NZc8qH9unB839rc+be2fJuVWYwB/5hQ6gkOb3IcF6WMCaq4fxmKXyx3CDVTIz5nbujg+axzvn+/sHTZOxT9MFm9QHIJo84JWwx2NqBomNPlDnkA+OTbukDQG+DNg3XnH7hN6HGndsQeLjjWMtwivlA6qgLFcJTXW619TE9M7/DvIXY+PP9ZGF7vlBWoBOVKAlunCve3AhnhwB0IJ6C6HnftFOOB2uqUTPptwXGYxLQaWNAKGqRgapot2BNtxOpVW04daJp48rq3BuIvheNzF5FSia/eOzSu5cq+hPieV4ViSnEsn3nfJzQT7Z7rpfnNHwPGeBmqT9Gm/I6x/vqbETgaM64kTwG1nzHVcTVBGExJt0hmIF7oQLum0+Xr0xgkFZEkWGHheMUp3WjoIcJn/lckl5qFwnS48UETmkRry1hG7fAJYuCjwfkDDTtQhgH/J0v/QwXfdBSi0qrqg8JtWaA7cCm4XutP3HviGVQk3mJ4+ZaTFxMFHgNTCrDPMp74a0Rsbig/DeYvI04H9ZAts8huaNesXDhg1Tf/RMcpay19tUwyJNsDWPj7Z+1Q/24IJsFE/gAjW8fH+FkDQhd0nzO22mmloIPDVNYnlcmavZd0N8bEgRYX4XDQ+lwyFyw6twOUE5zLyHWeV+yq/sLCgJAHSUhSKmLaOkc7O6Pxk34lT7WNoSU62KNQn1lhj+2h/c+skeqLah+JxcLIfqjaI6Km6vUtvMEHSjPIKDX5JAzSi5AeB0Ov7F/WTLRooQ7igNaW/0jb/g2UsV8QVMu3FdNhpYIzQMSqfHeKMEB0e+XdTZua8E8oE/gUTQt8SrwznW/8HHIVFJDgoFeO03Fci6WNNZC6n+IsXocdaXmTl1YWvN33QrL1Mtwam3vDFW0yxUSJgaUXlnzTmfVmS3EWqqb+8Jw8Kny4Kw2Lhy3muMyp9+FZf6B2cnu5v9J6evvz9+ehD+fu3ykl5uNUt3i50Lobdsntx/fGumv/S+7a97T/y3bHGZQ2q4aAO/Ywaq9xelnm5pJaVWxQUnA7ys/7gfSWbVo7LlJPVn5fNfQdz7gHMRelJFiOhxIyeDb+4O04WvHRpJ6MOCOHZcje93hcY0resamKWuBEG2zvcPKl/2DvbBZELamE2jRnyFLhtNG/4skXbzQa8vlSc+IDUBhLwfLhQeRYbe5MvrckIFrxbhkaJF0ElCW59ISyy/AUd1+ILwZAbgLHKscsh44odksPR46GLiiLhKtRlF7sHG6dOdvSIGB+6OSYhV6k057YmhBXfPq8kj4vdjePvlcMvxU+fP24+dpt3heanT4uVs7MPx0fnef/D99vN1rd65c+nCreGDDlCCdAmSjqB5WrFhy3icgJvfvqgOfSP7vgiNNAqkWkanph7O5+eWr3a0+fSh25rp/bU3umOvz7dsDpQIeusGG7mpf07LkPsJcXglxqxUmn/MXVcbHofjG4oAyQmv8RipCpS/uVCTEBCvA4qM+o3ZF33cf+u7z/0G60eO1wxHbC6EDe4CbiFFFUvipaE9+EpuP/ELValczmehYZlXMGmU8khCAjcvf2byU3nevJtcJMy3azio+qiybpyoyCoqtk543PKsPM5/SpUgwMjv5LaU6HryL1veDIaTN/foVCy0Czsa7gcRrSGIhZEuVIvD57a1rthHgDm5G3OA+Wbuoo1JsENzlFz5hEU94FSZgDS6xulX/ESlDZAvo6P0MQhKIaGw9LR4EDW+MzlJvFxbr2m2GJb/+zi2DfxuqvpWTClCctX/GTv/zl2/BVXvABhilmj5lXc24JMUZga9A9v/6R2zRIDHiGJh16bt3lSA19DrGmjyP4ZJciwva1CCFOVKTNegLlIELmOleaJDzvbhfbO7XVrZ/t766le29vYe/pyts3XlngfiUnBghBX69YFF07QJNAfX0T6jh5f6VES/2IXoHTcHDulubQGPkqPZwBUC2l02o3CSlKKDb6Un5oaCAzHthU/xXyhQgl5+EbuTbAiY7gDsWDbnXvxNxi4farnjSfQP86lDAu2/DGVdsLDqUzBSaEDgcyTARnKs02//TSf+L1Ap/1eAkMcQi3iWFEd072qsn/HLHEl/ixU4VMB/myIP8VF+LQAP9ThUw3+bHFYCHF53BySF1eBPsO0Y+QSLhKYkAy1cNlKBTCFqnoGSk72dFGPXyQWuPHp05ITmI57Vcjl/j7pXOmbOv3ElHji8d7h9DbykUa4VzV2bFgF+2LmZVI8z1Iuh5hoG3CkS2ZhCxhphUbK+LENSndeq2SYsQPOznEfakrNJaDEKeCcs+Irh4Tl7j+XNH9Mhn+U5UCNxtEYpkKg7e6KUcHeSLZItrtJrpDdGkw7pzVQ57SnntM2CjwUF2TBhSm8c95w2EAIWy5wB56xsjFhY6EkC4lzGkbPbye1jpT8nP8My5YqjUw5J0/nSAAJJm5AnrXYWaRv9Op5Bbx+jfHoehEVbPChUonZH3qOP1u+L8qDgErJy+yJsM5dfiZ9fca0dYDmBitGcj0trLT+vu2NCN1+BlD/FWBAXFra3DpjR8jZl+Otxtbns63Dza1NcSlrE3SaaPQA47ybshG8h1C7oeA3WU68iKmEMIjKMMAZXkSYX4F9wR2Qk+DvL2iOFbH7u3dqR9Xl7tH1ukn8F7TSoJRxuzOUUwF1rEWuoaGobh3ikmgAusXjLKYEs0skrAp6SfrQAB4hc+GCgqhgE3bLVjkXfa4CXmFJOKhfpS8Lu3bsn/hRUGqVUV3UM/9bA9I7vP6YOgdi5/B8f1/27MPpuHkgfuWuuW3YXUeQ+SDe4lZ/NMSsmcaHrc9bG04KOkW5GmGAnZlbZbRp4l6LVKu4YmVyvnVfU9gTb/Ca0p7JUElNHTF5VVFP6icmXCxW7ZKbPViCYksr8q6I6TBYqka0taILIJGyPRQTXiGokr+9Q6OZr+KbFKQrdC0aMXM474TMod5APcXfjyxZTa/n34/8O8fA6S302DS0NAOqDUzF5X4FSaNhDUThmdicOZhpz898mblZ6sy4aqJzEEY6zJQfM47NJB7+Bp2A4+Sek93G1OpKXIWMl/vzK8841owi4R4pIIymttWhGAjAcTfLjExp+xia48lr1IeDyBUUpzOrv7U9+whdJWvCFBGzv4jO6wFD9KF7lMRU3zzaXG8c43EzvoRnZt4PQeAOG/ANiwib53zl5jHtDLjfdrfqm+/fne2d7W+939tc33OAqQQN+wyfivED7VqCPCzx63m/gwUK0Ru7IzRIkEqAkYJC2WL12AGERemfWpZbzahHmy16vYVovl9RYUIoQ/3wgwtbF7nybDWc16mqcDeaXR+LrNMm62JWlo29fYFkse1B3pfLC57WGzQoNHFr2nJvuPQsoP4MhLasA/jotbqM5Has1NCYc3jLSEK6neSZkPyHT1jclXOoYpqXJnoEKi4nXKs/6jbgfDWhagUJKJFIFE4vfOtKaYfDZ2inPL5qc/sDhMsKhPv/CIatFeNM8VWdBc9COYmySehUkicCwvyLUBRtZS0u0LX2y9w18RxvIcqUWHvxH11k5Ko7kfp5/IwIvgWxBRPZeUCtbm3VqrRhltlYW434Y35I1BdVzi0WOK9ajce/SK3m10LZ/fLJp+RWO8qdT4kO4OCUtlaFE7esgTOGqGL6NsIgsmn0NPPh69XAr9mH5yIn4pGCNTdk31HfBJ4eLT5En4xAi1btglg/6MuZz1PZVCNkbroSeJTLDUbqffxkZ0WfyvMWbWamcOKGiXnLxuDdzBSQn2eWvqpdQ3TRy30n1vT7ZMzPoE6BNS6WAdaEn77A5atDF2Nhio/txZhX9AntaXrtD2MvRjUgnjj6XfhA6Iy0o3Ce0QgzyotbdU+mLYv2/TJyRL/Av6Y+6XT3oDmaMp2D93JCRqQ5z3z37AASre7Fw1/+9e4qbW3hJUznqOpy14gWfbvPmWSn3vC+g8s744SUAx/m6nbXH3babqjJAgtKRl3MweIB0A/IUlDYx9ddZkGAFHo5kKBwisXrXOa1dV/CbAwiqR+23KG2BXuZ92Dvuv02HJbe/Y5O/Oi0g3EPB0o2VWJHAaZ0xWZbj4fd0z/34aG9IehhfCEK63LVZmKwVs6N7990PbV83qzEBVdk0pmWQdmfthRDHRulTChxbV7QXhxScCEc+Lz51SsM+QySlXE1VV6s3PJaP/fPaFDUY4iOQR3j5+TU5l6c6yXM8MCyBYamRMOIWlLwGv0QEhWEmb9xdLi9tyPpzWDaGHUYpgsRXOZyM2apYN/c7C+RUoOinSPuO7FK4BUuPz8/h+sBlzFskTKQeiuqkvwyu6mkryLBvorjo5MzoYqIKbeYT1jXgn/SOB9zJHZFN8XZTjaxZLs+EpBEd7q1vy1+XX4Gnfx1LfFT1jhfE7kfTZ5JipXvfe8KUaCZJukqyiIBgMYf/WYwmPZ3A62Q9FYbeS3es5ShPJIi5ZEA1rnbbRiFU+OUE/FEEn35rLUSeTHAMJ0Xa06FbmNZ4yXMHqmZvNs6bTWJqVmNTtBo+iPFKGA7XUIQGD43VivhG5YYExmZoR+O7raGC84czp1ATJ6HhwdxV/dpIDaTrNDNc9xCWa4ltRIjqo0wmlp3XruBZagcpeBIes6YSBBhXRoUdVDtAO6ZDfJ4LRx0pLzVpEG6yP2lrNwyY7+enJBxn4SDCh3Zur+PnEBe/ZA74WfoyRKmUhQBvhEPX7sdua2WUMePAcqqjURJVYGPb55iwdocM6XmgVHqL7Sqf9h8MXAQbmDZuvaso3wjujRD9Bp+/gX2lDvsVgauMfEnu4LJgeIPfnFm+042uZJ0sm67bfjj7sFFLJqG8zfQMlvidhdlTJxzSV9CJLGyZR56uDWCh6DEbdbP6jyVYjWocX/g6lqyP7MLTTFOBW9LCzGrDdvq3UWowzPxGt20OjGckByf8aE8p3al9sOj2N4j00vPYtl6iXTMRqnAIbtmcokyOWoYnrvpQNZlI7gdj9oA5ojUmJ8GGQn9CY8iucBOhNndjbz9DL/TTt8gS3kxQeenTIQlTD4B5qIoND67grNkDseMoi2rifn8o0ytnPJRx+PY4cU3KnI4aLnrjYLGt3FvoM7he0LcJ9QHsVyfVUPUTsnGgi0tGdj1+10kiUhvMFJD8hvypRTD1ok2iAHpuRiBSuS+32ZauU4C4gFdt3/jpFZWiCRUTMn4MwPnrT5XCW3TaH5Hug7fv8JYLKBhytShfPnSzOmWv3l0ktlAoNmM6JOQOEf9gyd4kgwld8iv3ApIYah+Z0y5Vtdzhw0ptGBcURLiRqMFJtYFVidFhf7m0cb5wdbhWePk6OiMb7bACo0zS0lBjXuiUnEMHHTjwe2CNMk5b/kqlTwcIGBdLfXE7634tQeW3z8yGadq4ra/Quzn3DW0EUvA1HXTaTV9v6eDnomnBAddNZIZfqErMXMCWDXWJNCTUaEYgm4M/VGhFEFicBH7lImBKmFWBRRqflx77A2FJgKOMj8z8P0uZGYvLeZz4jifixkLZUz4cUfCkkuPWqAH08m9Tt+jJvwebyaYrlCZAkUTWyPoQi3CQYyh1DO/DBDb/phKYlI7GL6thtP/7XGmkor0N5n9sbAg9MnsbAqLKnIzmPYO9taVMz34ay5wSj0AxKY7GKB1i3G3OTs6N/vNgwAg/s5MZXMa0239vOldu+MuAmL1TkJZ4Pya4SPffEGmzE7PewDHZ/ZHBYXd80uAJZKZIZdaCI0v5297SgGP2N2D+7rIOJ6tR6/VOBkDaRu6vDPDa4d4lFAMkBSQcw/jDaW8uYGvRCYt64mJ7/mEdMivKYTLC0bvshF7oq5mf5RonPB/uKH+AGifSmkrUYFNqr/Htx0Aoc3VSuKC4t/74G7hk+V2tWL7mzGhoNECf3PkYVRvU/PmF1zo2tutiGj4RkXOK4mDU8wOr/vzsSCVTlsjK0qIvK/gk526XS+4Bth0Y9ML7hoFR5lnnYCyyK7Fth7cCmMtEJOVG4A1WIMsWenMm+3whUU5LdhMchj1UrTwBfAD2Jp6DxD6hRHPBJuRO8x3JA9KifOw2es5ZXIvGpMbIw5dz9OFen5CSA8ysxOXe61QA4kXyGltfG6MkZtgtaOsmJZ5pMix1Oiv5Jet7++ggr19hv5IBAJ6FNEyRI+nkKEN1Mc4Csl3XWDgNy0aY3943a4B0XsQ+NzaIvsHxG7bgNQx4w2KWdPGcJrpzKYdnS/GHGe0Fa0ZZm3gAP8fe5FZaJEt8wSMmaYVykUCx32ErW8Oq3ilOCBlgWHykuKPy5HMJam2Qde/Sc5rPIfxOwQu8ZwgOc9XS1augw3xJrg7CoWgzAXohAYVh7dFPcEtaWG6n2A2XZuhSFIY7gvK9VMhvhxx19/eYIZzk/wo0jjjamYo6325C9FudkkrUeh+nbZ/PcOAXOiCyucZ3WIsQK6wKqxyaZ0gb9pd8AC72R84JaT4bftMJ1NCAD+m4UYwSTCQhhqnIWbzcp1DXxLRs0BYR84xRlXsvz1PKC1S9SKQPLpeh+N+Q5zUGje9EdLXDLpjqZHGBR1LCJEvQBbmS+oI/PLu3dbRWfZHEbfnKvx9FgemOGvN1IEU1HRlLHRCVn7Npldn4y6N72SVdcsrsFyEIqJFoMwoN/Ys0nr4ygW5SOHpXsZCvw4drQXvV74FCBGsyMBCQvRuXtOiGGVx946XUNBpywBrRM5JEQjzMaTV0PAynp/vV+MwAzPSEwTRKl0yt9VAQjnciYXySRci7nzBzjxlHKZMksagrJPNwW4LyEy6PZczD5+UZJlMNSmqcbG2MBW3M3k5Sm6UuKTkW7kLafxV8EJSS4x1E1fKYZ77LaHemmMIr9msYR2hdcUHhJfbISiEegOpgtC9xc4/c+E1hWkc+F3v5HiD4jukWM8OBy0hZOFA5j3ok2OkXAHzW5ccmXtja+qICQd7dq13B1y1c4n1J6hZdPokyZXWWrdTfgD0BreCFgK879vi+03v3uv6A12R4ONTb/9b/+5dTvzI51clbirCamPYfGSTxGodv0D4EvP6Uz+KWDBe2DjPYok09N6RTM6LcV5mxQOh3CWD9yBh8DLNzyQOxLbWv5nZAGo4FpAE0a4QkQyoB0TwEOPTSa4lsTdvjFImWjsJPfMGuBuWlna80foTBTStbe4ncazM+22PrDSp3CBmG8nwDaC/GdhDyhDp2Tf8aD/kCU23PcMoVPO0uFP7/mjmGkul41nUAwRxgxNSV1Ja2vFHvngHqAsBIvs9fnqXc3naIDYbFHo0RBOKzzMNTDEfAr9/MmjxivjBRZzSx+4w8LaQYZLawD0eyI6fvNFd56NQ/Id+1xzO+cjI4sxhJg+WZ/8J7VCJiPaFdcFrLNl8AnyeUD2SxhoTx0FHneHwWbvrZNtDfwB58eA04KbIMYaiUQJdiQAxfrvDuY6zRMFiU6SVs3rkPKTjfo+0binNCPSG7dMKmb2Doq4zyMoLJPTtxPv9zghrQ6cvvA5fiSJhoUqA566vGA5+Uxs63b5NrgBQUBvuN/dxZmVGqYbWEjCwnVOAFtIBoMLR/yiVkfuPXodiKZKl6ajMqTkypKXHYfh3czD8+/ZWGKBCoWgqq+upT7BwJymPlAoj2HR10LfrmvjBsJpAgGuKLYG6rf2INKRCimz6rRNIgJszHBpYDE59w8wIcbX0vk0pV8HWPt0XAwtAD/5TiRS/ZGIUpSmlg6Y4WaaCoF6IzsufxG6Ty62q/OP8cw4w3KlVOaqIvgaGME6AB/W50R1g+OtR9DODf9lJiLD/ZXaYcDkfcTbA0zklIb9shycQycF3KnAmakyGDkUC45N0+OqiVBJn9zstrx+EgcNBPN0Yzm+tM+G5QnWmUdMtGYPG9wMZBuRsf1kwJLPcBVYmlmQpU5iGLU4YuSHxHcq8VbCUBH99MIKPZD66QOC8KBPm+RpJ1cka17EQmO6N22+7TI/zhnFEfDryicPWYsBS7PnxQsDnxYn5Qvzm1y4LK5k/VUPjXfS/dtd/0o1U9I8eQLmaFtjxKQuGpa0qWqN9gIWf+ZvNDz3jpS7yEowQuNvAo9atjf4zozlHH0NM0/ZbZu2EIN0Q/dDk4JHXTntIAVFAag956Xx3MOh2CDuW6yE+X10VUzbNYxVedZG6hvhuSr52g/FKArM0eG8UB4ScSKicDfsw7APSSYhga/DY1jc2js4Pz0BTqn85rgO8bvt8f/8rVWLnk4uMgpMFV7GoLQghrteOGzwsRWOLF1/Phl7/5sbteneqDlmpRjUklhKbQ5Reh/49LElG6aXPfEnwaFtBNRXKCI8q5kPdMMBUwUSEGjXymkDUBxiRofvAzVS067ztC8uvL6sQow0ZjDo9UJIkbh/NUJ2bANzQBhJcNqGWid1i1PMGnmaxGXTacEbAPaJMM+7RuKm8ggWpUgur2KDlGkNkVrrF6QdToOriciGM08Y2YMQOIU/ucKexd8x3x2VYINokiL4SnMzhmvH4BsQH+RIWeR6scSUCY/NJYl+EmeRy7gBvIyoyqZFW1JauNnMqxPLTR+9pRTcmDyngSjmfl4bVtSITDcmpKVVnJO5L7C0ZzHfi1Ef5C+83+LKo6mLEtjIc6cl3bzaPNiDPcAbiX2/eJ9kpyd0ssAK8vIaWJe776HqB7zI/P2t94SvRMMkb2e+zaIQ5Km8EkVfCuIe0maQSbJ3rGXwTjWuw8RoQEMSvMBCodVqCmKBJenMt5ymFSeyuud+CGEWMHPExx8OpU0oBAxa5Ec14PL6q1wv9EOctWZK3MRXSXMheKiN8FVINxIBcrn8BofXmpF5Y2PzqXDESr5yvKPkYBDLGbfHRicNxdHR0WN6pymLKzFZADax+BBRdElXMSBeuEVEmSGdewxPeQDnGBjqO53gtq2FClsbQMoH3HGKAnlLSTSuMZQRmlksxnT12n47BpkkfH4OQPdrZOwQBf3J0wFfWWLWafiXlWqUhcYquIahj3NDoazrdLrKDmZeh7068F3JRid/apc719TX/WuSt3alLRvBIjpydIDeVE1v+X1Zszfl5sLieSVzxnaTNHQU3ppNuf9RpIsc60sVz5BPMxGepSCL0kJsqM2wR3F6fhBV33UGjlxLQ0sfeMPD7ahBz9XZ7Bj5oR1gZYYBQQusdVkx7jxt2fN5l4D4U5vGfojS3y4jmQ6CAaBEBMwiTl/cnlGSQ3hQ74Si3IbZlhJ1uEPjc7IbcBt413693CVd2OiDXhiLeK+YLfPYiTxqJ0d873JPkT3LXhvaD0ZPcv8tE0yvWxRlItDRs+e0OUARj+lZ64D716JNMzEpDEh78g1g0YqWi+WXeQpxNAbYyArsgX9qAqobeJougf8ZwyXcpSGwBbhc/Cz/BDxbmzZnT1mNYy1Z+fCPmsGKZF7KOHJB8q/iSJRgMWFgZ0VoQbhF7UUwE3CTvefCaDSTEuQKm5BxfT/EmyMeLKziZIF6tBu5KiZCAQuH0uvNt54IF0qJulKVjBSCdPfnyMGqL3jHaK1uMC8XjahuXRzXPBA++yq5ICA0xCEx3Abwwme6R3KDx4q5U2DBnXSBW4RBrrxGAgpKldFe1WIlGF3YvNR3i2FuMabEU8X/HpDVcUkiPGFKsyWeS1FNlI+6IjFSpjKjEu3dHH99rlqpvPhdfQp4PtUt1+t884iczt+l373LqYvWsiwx5GlAl4wbUXEwJVeePxxWSbGLcE8n34jNfQBxvzIUJQ8EANdR8voXYsRn+Y9MXxA7mLNf40WMX+E1SvcslySOCe46MU1HgG4B9QPh1TawDRqCdNKLf6sGd4wTO2w+ed+8Fv3WUo/c3brzAqopKZELDAYBMLQ4WlBEYVkXWUSxaievesmm0QXMDqHNl0KB5A/QMyrQpl6QfBt+3LJwc69RekR+hyreULeJVD8Xbpr+JefNSXokIJ1sQ038lqfxYTthTqqi6zCxgoR5fCGtX5tEao93Ic9sVBgWpIMI0vllUbuMegM1xFIuh6WKokCUNJpnd21TiHtStzHuxQ8f5M0L+T+Lwp/OwiZBmLV0NX7wgIe+6oKwdjGmY7thE27vvdL+j0avOJxcuarHQNYey1t7Xx2J3HFIFoHBPEYKwt2nwh0K/94Gh+kTs/ENO8plL5IQ2MOw85jjsX6aoP98Y92nxHnRASqIcdJ2MosLhkdMtyRBZyMn7AdmuQluSGUIcyOA9iShvIe4ESdroyuv07+aZAF1ITAa68R1prDgGwsOKpSyTendM8gFTNJjXwSlY94t7UGBFU/G2BHb2B9+moFxEc4YYjaliVoyeyHcqypQAw/NcegkE8BNRH+9JfAkfLv4cDzv+sAhFifAuWB8kHDEoI8oNgS9TUBk2EncK7l9lr07CcZLIk8YnV4ev4zlJPK2QHqX3e6jfcNtBKxPKt+o9Cr5xmkm0s+pHSMwkgAniS56ltebMIsBvT+wfN0N35NX7Qatz5vbFdevdscf9qbAuq73cYVQEI5te0hPYCyHRnQYPVBkBb1CIUfkcaA9RxS6ZMfTt46kn9J3O6ImvoxhUMUoaNPTQqEDWNB6NRpvla7TIQfIOPS+8xmn2gq4rBdYlcDAA0yLGFCGk2PHFwl8EwDD3RHpqFbuElGIgJs8HbddwbLbTGr2Cb1ZmtsFbXXkvJ0g6Lu5WRmwcsLedn+w5WekaA253b5iTOR7OqtcFMDF49MDiCFbcFiZ15UC9z/0GtaKpOcTBkaAePLTHvUFRPvTZp7//rNcP6uL/tuAPn48h6TKxoAQE4MJ4vio+kjRKPKoJP6Rn4WeoKD2grrxLl3+9x0CUmJxo6YDqSx+EaoofaE7supDThpFWbkzmBosXm1Zv7K/lq7QKcc7hT/Q3pXshwyxcGpzscOj46HY4BpY+gPRKYk++pqKjzkpzTTnKVam4aX/HlG3uhJAHEjyLH+fMuNNPK27QJdb4YX2gShhYxnMvll5xbnUpTHdFnAHz1idAiPA9FthS1+7H6zj8sNhPnGwy0J5GvhwWBBBxStLfd7eF9+/cmduhd72SBKSLk1qFVdbtIhfP+6OPkE/Supu5FTbyG4WBKFckFhQoiBW2MWt84WyfdGLecHGm0B8ZPSV0Id4C0VtVs4yLlEOHft9T2d6KF4a+nXP9Uf5uVyEtI3JrocbdnoJIcAxX41pI6ZNzlaeuPQ2rKjFJPk5BDIphLWPNZVmSg3azH2WQ/gX4q8QWYq3AwItbYBj0sGgHHOnk4KtxnwIfRLhytBZvaLoX84C7tuMDOKFhVWBWqtVAKlopiXxdlvJXJLxjbIBSAhAczKgH2W1L7opWdflRKraL52z45II7jyhO01yV2QrRlKsGguM1CalSQ4HSFddiB/WGMyszRnhjLRI9ONna3jrZOlEEQ5j+Ii6SDHhgQGWtPzl30JH8DEFDfFkp/EFsQiti2f4hDjTEXsf9V04weqp9z70Gp9PuMbnAQk8rMzf4bEiRnK2Dpeg2hZrc957gQCZ0UY0BTL+UftSEusI+A5jKCJyC3LE1zaGkdCJELQe3chExs3Ii0wKDzh3eyECHrcxySw9uZzRABvnZwTzfDPkvtMeD+p0UC08tdPQVWEuXPZYIiG7lRGNOFnYvbpEqEwKmjiwEgN6KYWYJupKIbGgLslJozhGG31uAqaA+zzrdvTss6L1hlYvt+e0nrrbH+6ZUdQknpWf2sZhR6/XGMP/Iv1Ni9qJRiYJT8bTJkYiyyKPVRXmvL1z3MMgwAbVVOJ1Du2XEQNWoDgfsLFHiRzFpxQzhhWSQPbC+9zgyCe+IuEyigjHASSBZvtsCg1I7fmtkgJXBdeD1GwEky0uOpvzjYj4vVviCvHaRnQ2N9f364UcHzN8furRXQpIEgp/vMoHbAGD2YBuDXQP+U2F4mlniycBHNKfGoia9ZZItQiFEVNhuZYXrvyeYJXRNWpWqcDV0AuTFZXLUGzRY0Z6PPUFp4SBwFTdBmXA/RWWxokzTBa+ZOU+9B7SdsBwBrw+zhiVulE7WIEy5NqUuAn/Aztnwez0hmpFEYUB5sW8vvCa7CPDUopymKlpokmBDPnPSst4DysqX0YRBAX9limyhB7ltmayPP6lL1/xmw+u3G5Ce2ud7q4rmu1TbUlck73X6kLnATNi8TfBVZSkwf3eUe0fxmLJLu8Qe7d9ZYCKMByLOKFdC+dkqFOa22w1iMUFKQSUNz9d3USZxW6oeUoyuuhYKeSqN1Ng6k8swMPCqN4rr21uN47OzXfS6nGx9Otk6bUD6Bzxa22957Ua56gVuU8H18M1bfvo1R2nCtjq7KCtsUHKnXPvgBWxCMC77IIZs4LbAFehk+9/ZX4/oGHDdLNsP82ufZZqrEqZScCIkBuqIrHna/cUMzHKj52AKPOvx7WADKSSDSKovtUc8hZWYaoFOtNa7BVwTWiHYcnHVN39YrViVY17RRnwtCe5uwQjPK2A34tXmCbWWeiOTzVMkOxus4JJARbHktOcjf6XIFh9XEiDDcc+So1RkX4MUI5k1i+QCr3dS7/OoJWIbIf0hzi2OXmW+QUl7GuFqSqhh+TYxEmuSnTZrbYjZh3OxfgRMnMqzrB0ORgz8ZCbVGc9n3pgAywuRARXtVYV4n9cfzaHt9IgpELmAQ2PJ2qJxP+yGeHahZ/nqdMiBkcNbkY4vLQ6Z/2JO5tfE/ANDbr/jgUvIRO6DOs8ME850G+aSM9ojwl78RLl23DuFDYoOUVkPUTk0RL0g+LvbIPDIXKjfKkQm1pc/GOnfzXP47gtyxtvI2ZU1s+wHXNLADZTQLHMvOjGVV2zGSDhmUcNyBkFGaMshtRTUyLvmv2udw71GsHUGlVJkmmFiHovep+DHjhj9kbRiawrC5/xgUN4MRcHAyQCVOnG7WVbQO/7RrlZy+dc8n/j/+//IV2ZsVKhzvCFy8dBPS3RUHJuTx1T2RQWxTIs/7Rkd06TodmOz1pYZ6grfp8D6pLWJRxzaqDmhEhD442GLFFe5Lw7YkTXbupaER4khS8eUKSErCGDCWiukWX/E2AiELQDq92HLOzzzADojtJj9fb5EggGFxtweug9wn72TDcDf2fon4tMOTnf4MgxEgQMWRBwhDZ+4Gt0sJYAcggKONRRB14Qt2+/3ZfBy5IfO5GYr7KPpSC8eF8QN4lICUMNo7XTvmsUvN1+KlSf3orXCDVUVBAn5OnoSQmF4BtkWAIEsTKThI8Ms0C7gVlANgDDty97lBECMnnr+OJC1xSoICKLAYi/AYeH6Ds407HhyEDzE13eo5Gtyh5BuIKSLypAWa3oqYsMkU6oXFZzY0IURQKkg0AgWMMFKs9vb25WtLWE4ZK24UDeYyXTdJAvNCuKMsEZGPYsO1Oedve3Fy4Wae5UOpWbwBcVlyY8SBROlP3Vaoze9GcAxZdLGqCCMCHav9zsuPJfQc4OZjaH38I5/p+2NEE1ohBIeqQDRMRzIuUTCWIia52FmZcbwfJxunOwdn6G5AlT+7Df5mZlbIWBRhJL61g0ayqMbSLTDozrGF1fZ16mSAsVkxbrXCnaF8zqgQux6WvPluHGAYvfui3fgHYJFsOtu3HknTHhXIbYsiHCAOey8EL+He3UCDHN4x0N/5Iu5oCcvEXX3gdEbEnLbZ+6N/pEs7UP549SrxEXH7kjoE7L7ZHnWNJ/SDztDWjqlJQQHktFIUgjBFYyIjiZCgkTl/aa0Zx8QDS4j6Q51iDiroBiFVQpWgnA1njVtGqJTSJbVd4a4UFo/5qRHmpL7FaGegBxA70WNKdaDmXF/aUnMbPIqvWwVIg0DbZ+RKOohvXIZcg3jpWpbQ2DTgl0HQWcRNig5ciDUmZYJFrEoE6wAsbDZ1ZOWJDogNmpgWf4x+GGWeyrIMuVkOJUyhPiukp/fyNNNYBcT0irRsDFFhm5W16w4yk3MPvFKsSJnjQpuQnxT47Ec5/Eyn6nVM9tu5horuzjkw5r52WmGe+DGH5n0Iqb4RrxTieuoKZsxoellZASeVjlfhATSpcLL5D38PaSYyp8TYdy9sdHF0lqHOK1jLEkiRBOrfhWLPRHvq8GSFsl+YTvppdLwz+ZYaQQvus7GUqxy2CB502oJdb+Lf+ljm69EaQUoprWwnsB9t9hkwtQRcefERC90ABYbscE50okdqybJ8bdpzw1HQAVhWLUF642bZQX1wpFr1y04c2DbzyWomI2jqw8bP2I5clnCDEQFa2eIzFqgvYdczvllKDu1pG1veoT5zT/9xb3zk+M/Cydfz7fOb9zdk1Fzs7yI/26sl5sXj+PWd26VKpiA+ptTIVrcn7PpKdy4SjZNySaTenro50uwv4W5loI4Em5AYHjBmVq46iYUiCuU0sy9RnkHJagjifhwlmNjqv7iA2nE0lL2/PQUzznIYHViWBgs1xP3Q4E8165B8ZdGh45v2TsutLiYjzQdm75W+am84uPcFZCbsPvD6k9DmuDgZgiMLekMHEgILajHbCGVklSUDBoqMUqGaWuyU+kzknz5Qgzsxu0Obt2mp0D1FKjldUkkWbgqFX0ZCcGIR9MaZI2PsQp5F5Srb9YpqvnGKiRh0ApWWaQ0uHS2+WtgTmK5ychzFadssvcEx258qDRmvAmjUX0GvPOOHNwae8NRwRoMvYbQ0Xqy6yaLRYX4xyA3VxXMmEteNx4GoXxWPrvA8JowluJzgHVpzPAurWW+TtIic+ouRfKtJUJZYmZVUtg/i9vwZwv+1OXXaiUxP73UIN+wJD0w6IlmHsY541Ur0CP5gOA0JD5wkR2wEP5dAcaIXBNLomkZIRcj4q2q7Jhz2mJCt53UH39gAfXxYKAcb+l5y98V8nw5qZYf3HKLsKYWyoxNNa1DoNQ1WFPT95RFMZSTEZ+5gXe8z7ynDCjQewNuuKqQM+Lkz5Wj7I9i5Xlrb6N+kjk9qx9u1k82M/XDs71Peyfnp5kzoYxlwJQS0iSAYak9GxMe4VPFSjEmydAAIP0V2Q5jV1qCifRCDrAwxl2v8Dg5GRdU/Wne1ZSkq8Lrkq14Uy5LALespVDah9TQgzeHbzDVo6RTPfiCmsLgxGhuDLlg+oPEcnzJUx6IlEIX267BCpGUgb9I2SHERg7efLffZmxbjP5GUu/OewrVdU22va5H1WJZTEiVPQiHFWTNyPAr4AEjIFgtjlTZfqPoroxOjEBSg1cQDVayN5UVOeP+Uhuw3GL4IhmTG4yF7tcCRhqH6EGRCz6bWBL/B7kOkMHrctJMhSBfIF82zk/2j47PMO0Ms87mneTF7omTJIpFlrME94Lo/fIvUJAZJGLTGMjsU9Tb1YdRAAAuTcgXqDKbMsU/4b8WoRzGPexR7RWp2yXevwOtDkq/ix+AZmklUcz/ToCDrvhSEV/eg5aSdWYvj/KNqx8VLPacFcfEBOdr+TYoH8AKlupJUHCySeLilTgAOpPIc4t2bIvtEKKdUzaMUcVcHwvseBZcOTW2ncWLAHKNKWRKKj2HFg9up0I/PXk8gVIq7sbdFvgkZbpd64EtiqosZRClbRk89GfEf+0Z/6HvtWduEarBOeB8MXrfipg3j5tV4hy8DkszSHIoNLuZzmAGDBQ+HYFb4l6hmAQabrhRyXl5si8FKzz2Ev7J5XLcTIkxMdKdjXtV13+gvarVaxvxPaEiEBqTAAqGi2Dw4GgeebF3GsjNnKoQW0GYF8SkWy7uY1D1LHgKkpSsYxTAZIwWGvKZ93QWFrLRkYCqdNMZdShSBBAAx3m9P+q0bl1oLZoTX6kqN3P2UyfoNLveimTCWe/0G7vFxhnmb2f3+n1veCbm8koCIvMzZly+UpVMDNd+tz3T6c/Av+CJPh03t/FjMJPMZN5vH+1vbp2wtYPoJ6zOF1Kh3O9DqthT4LzmCmGeynGcyy8QLYck/4K0ESGXaOAq6mtYMqUiOQLQ/pOFD1GeZn8YSXKS+3YVHMjTO8H3K3DY1SodmTjMbPh+N4NlisW4Bn93oSl2Q9x8V6i3s5PzLToYgFN3ZB7mGxB+A5zb5OoKnnqwv+Asiubys5PeKAWYtP0wVCbQkWhv8Wide6/oNgO/SzRwpgKMiCrc3JvvnezSknOJs0tFmgLnShzMvss1eaYhaAr0t95TuzMUehjn15k1PyGhUOym4vfEvK4HwUry2rDH1C10inTWI9qqiFTene7IX0q+P86fP601nNnS0MmVTvisKguVdz7ixWYYodxyRzPGGk3I3k7R40Llz17NZvt6YrlIs5xAjHpP2Fh+BZ8RNIj5aOjaiT0PerJF2eJExGg7VxCoVSsyFVQnNh+eEjM9melKotAEsxtRIoax81liG3Ak23iFEFsFKhuDP7vtNiyUlV+shRqb541w/aEEst0LeQecWPKolqgI10KbaboD8YUb/Sx7OJptzrel4pVLQs+aKW1vaaRWBZFaoDXIYvewE864YrkF4057BsQLGzOIqypiWJMBVdmljaemN5wxmyOQMayYttdoPq0k1rt5v10aysDUoiygxRJ5E3jVmVOFz6jyGddecOs/rCS0N5xDj4hAAh+IYaRvPmLWSYMQd7vekKOmCDsy7rd11+nnH60uy4pKBigkoTZw0Lx6UO7eeczncxCxpasYH6Ta9R7zgW6T4DgYQ1f2RjqqehMf9E/ytOcNMIIdweBmksAEgMlCiBbHUE7OeXv511vlkXqbM+3ol9K2jKQa6aqnkqO1iBYwk5nZds/cfXMwEa8DBDagRDVaK4m9Ynf8pfhY+LpzfnO8e5j/utO93+/xyciXUbS96aTmWmbFcuQHxaRtnY2pdrgeFJXdEJKjnDmgJhhY4Uyhujd1renoacHt+PqaEIfy1JTlxCRDIeh2WuZJ8/l5KM5jETPyw6IuJZYPcDanM8Ap5WRzrf4Qq4hkwACgKg39zs0tUA1k2OOHMBscJCoyvM546xlzvi2w0oflxGIJ5Vs1wH0C1pksgmQ0PYTbQpnMSDKDf2RMl2mDgKL3sO13TzG2RZu+2HKlMaos0WiuJ98LUXvVaSCC2VZDz4HZVt78UjC/FFfIZ1klHh4hznbd1kdvuDmz/jTjbg4792X+vcAu0VartRJPmh6r9jX4cjQI8rZHdDy6RaVvJZkFbS+5HHKxgdR4m+MGZO2M2Hc00DWrZBwY3o9E2GM5NG6H4vflkAoI2eleqyHTVYGHEz2SMPz4RQJs+Zrw4lfTVmpN1pmsN3EPpCM6koZjWnB8riwoM33Tg8KeQiMuU8rj87Lc6UIx2CmoUMMxXM3LgjKnG/UDkzjsE0AcUB6e7tY3jy7Eoa9CIeCrFlmJM1GejWZnpJD8Jud9lVAnZSqQ1heaixdEHDthR1pOnZrTStovUuu8lLI5coeQa4r9K1CZbTRJ0N0y0mXIWHjKBw3XWKNmeCNAyKEzZ6DEM6TvqDZlcrmF+7TaYAuAggOONmGjsqdKLDzg/BZ7sdCgdGI4ze6+99BgUDaUaWd0tkywMQtqe8N7wiczYGiV1Egsg5q1WMrkD0QAIV6P1xa3se7PnZO5IYjZunQo608mlmUtEkm+Qhr+utwbP0piKhMc0CYkdJbEtPGCnss2+WbSWwYqOch6MD3QczSCb8Gt2/YfkvNJt8nnVzisj7E1TKBVwDJGlVs5iCxu55Pi/9ndx5awLsZTLShMaNNtI7TYyY4eVQJ0wtXxdZMojbl80V2KylbfIGmVaaLk15KmD8RjjBvQ3TFiBaYjcknB+uHoudzF0MrQOJRkfeNs79OWOjH5BVmHkzsnR+fHjb1N9YOdrIfnyDTgmLsMHtoFSOTgXi2yii25NijpVyi9gyfLZVIlbA+8wxfKv0maHQvcbclG03hbXXaeEXwZWuX6BMJmrIkNzINqGsY7p04hvgenvVRkwZWR5g1Wkiibm2xRVp+S2aSklGvK9ePgqeXzuUV2MT0gSWIa1wVw3QLVbRqqVHVuxGvuV8uoEsHdR5IDqMocQAVOvZ+zyy1MyMNLVRcAGWJTZxuxmxDKhxWfaFMTdr/z8lbAmBhIQsINaBUDsoCMMVbSC2htM7N5MuxLqhZD1LCfxt3+G4zzEm1dGkmyDv0HPr0q4YGGsJWxv3fN96cEgkvzK2jzVRIDdyCOzTSfZk69x03/ZuZ43HX5DNQC8xLykXkPHvWhz1GeJMLtOlgxzJfZ4krlhT8Pg2YDT2pwLruF6zWft8Y3Moy5HeBBwGRY0oroo0mtoxgw6acgvQIpsGHNjX+mG5UkrHlO+g/ku5LFGSw+obi8BCtXXpIyqgzZAt8HkxKhihs7zeDCW7/n5ZIzTnaGxhM+JXME7sZKZTk7+S45zydym0UZ7A6ISSkQuxRuyCwl3Xa7A4PndmVeceL3AtCPzcCOtOI4ZkKj+OZgJBbxJfBt1v7191Jinq/n+5cYe0z+O83WYFE1oAiMW4Dih0tuqMyzG+rBiem9kjj0Rg/+8A6sd3b8Hu8es7egWlKVG1TMG6sx1u9Fz3eGY5BG4Atkq7yKiIcS5vgKpQodSGwj77i9nouM7k3L3VBFlAOgtYNQ2QIhGJeDp0DsGM6c2E7AfyUEiDBUHC4ZqYJfVDEapjoMPTe7yLNNSk2A1wK3ejT+JqFfsW4/K4lafoLtS45QjWfbtAyxpPNsJPSFkx+SuZyxenlJIl4BdNvNznDDrEkG2pYq2PEDCt/pE/hS9EwXlBptAJ40EbgZXEpBGZsVOFQmoTiNYJDbJ8c06rUjH35bkRiCCgMc4kYxJBkg/jI0eiRpZ0K88CtJenl865JELErjyBT78/mFSoU9zioWWzAtqjiLrxCp5oDZJ0n/LhnxIFTLZXsxhHfVvX7b/yyOdVgbKksLzZCsrVotuJV0QChP+dyq4R0VS1g8dWoVATXS4SiUn2TiiopUUyIAfKdGaNg299C9C+NtpLZxEUSHuVTgr2QR1BFsnswIcQD17XzhrHNQPis1dsuPeWj1gDI3eTd7+44VAMQBVBT2Px0rtEn2giIsNWMn23sSCnf/mlupyZCikjJIM3nm+1j0jOJM0IsoS1oVo/7onpFu0VmM5fqjtccyG5cUfdcpCokzZI98G9wixhHkX/AmYRhlmngeo6nciuRhMTmDhBF0jJ5wKtRyPwgGjcGDzRkVijNQY5KHRZY3w/cGr1SMAfzz3jHINff6TU7eqGJQHnKrJUcY5457zRbV/eGY5eyNAolJ5Vm0Suqds+TkHNv0cRRVXBXD+OAjUWTlJkRROkMkyVBcbILbqdqaFGwe2dOBi9PZnFB8+oLaO6Det1gEZ5BzDMuAJfG+f9fZhPJGGV6RGEgvL5p3OHDvxDYGQ5kBDE9+vgRdwmoWwdutR1yeauqguk+RoBN0fTkKbDTDB8Qc2FyH/kzTScRCFNpypy8kC0CsZhT/m0FPsXF0eAZVd/e3DnfOdtFNNz+zdbjxcetLY37mbOvguCFlH912ZmUGyuPxssSQOxgwlS+HhcpZ6aSxUC4CET4FfXV8vYrxdbuSFPgLYAo9GJ8z8jN+eMfXFtlYNnDNlMTIGarqXcNhJ4aMFI1El+FbYcY0mH277i4rNBiSr5TNjl6mnUt4YUV4YccQnNdk8h9b63CGc5WmM7iRsgRI/qRkHEpKqqToBEgRkRk9DaCb8KgZXkl4ljwXuBoe3SEQYhG+MwPeDPgRY9rwgc+Mqg3cuwrv4sbiYYW4ARYlk3XxmLZQZrDFiqF7WyBeTtTYHB4d1EGv2q1vfNw73BGfNo4OjuuHX/jiBWkvGtXETp2rMUDzLtfrm6i+kGjhK9BArtjLqH/m4/gbK5Ri9VXFUDpHQfPLvxxMNXy2M8N/e5MbB0OkHKHXkvmbmlmQlAnG7eztRaz9A0AiTbCiAEoAMb55wuNd/tFvBgNnkhR65zwMyiKOOsiLzs7WyeS0m7+/ZxFEBCXlGNjVwd7B1tLSOuolS0vh6m0t8Y5HLtVc0+HSKobli5Y6LOuS417FZ1GgDhP8Ov1vxhyUzGg5p+GsPIjZ6N+7w+C65TVvAP0G3ZPTLxvVFY35Zk69Z8eEspluBm0UsCSCmQCXWmXA5R3ptquOIgFycvMOeGCHj9+Cnttv+V63ffWjjEnXNDBylCXz57s3mQy+TYgm4Oy5HoqtBV/Ow8ODk02tAkWBfy0W3LDTcsnp5oO65mXgl07/Jhd0bvoZMWm4bWTTg6VABhKIvWHLGFPwngn7I5cCFfRZ3KXgZMUelQF/CkRn4+4wcPlneRNEiJXRNBX2RuZ9F31jKZDie8c8qYFVbBZLupsHmlSc7wzEiXEY6FjNqsxVQh0sRqptTyc1iHF/0GtUXhB8n3Jq/ZWUdJ3WC7U4l7lxcvNDpqE8EnKghDMxXuhnKHEDBP7aqlXesmA287PaOWwBIS4BKkTGVT4B3a5xP+7aTB8xmpYT5hmrIghhAfm5Z5ulhfKdEwvCxBFUh3g853llZMUHeMCrHwsGLLqKyAJV4NMuA4EIKFwb6cC9R/oiWhjp8bC7gn4pjLoo5xuBMbJdCM6rIm5VQhEgtWSU7w+cBQ5RgWS8v8ed+xXoN1uHGZif/BzS74C6psppgR9uoRjeaOX8bDuzKFVQLel2sFIyhPqHiBl4a/Gc8eJ823X7N2P3xsPmddodNsdPITkr+H78Cl5718wLXeEbSGeJbuVUyBS0pmIbmMJKX11UzKLc0hf31vfNs8V/6OTRo25IpqF3LSz2W3vYjd85DhSlcXmC2+B752tlfyhnb2Hak8meZX+UKrgCoH80/jOs7xjaEL57/Yb4h/fGWpsZ9287bW/dbz/pmArCQ2BL3fCvrz1vYzyAmqMz2/6wN7M+7rCllskYrxGHDX2gAPWxXe5U6alQs2jhhOiTIJyJoUenoIMGKEeK/9TEuULZCxndVRJrQqKJnxiQMxESvzdpdrpB7yZFoPZVtScLEXr17r3zxnnr/CG2vSVnknayKw3nwZm9+lGbrwmlw+nLDZeLjhbsNrDLb8Rvp+OmGM2JsB+CVEroRX0QyWK7FD/Beavc0gT+5OBPahn2rQLqdeFW7Z5lrL5Fe8bDSanTKkIeUyTVYa+Xyj6N2z6uHOhYRRIS/8tXw31DiDE4Uc0UDzFKz84PVG7zOFDUZQhi+tJBvAV1h5ZTE/k0EUxYzN3FzptK/Sg+6/armsnXsev6xtMNvKLambHx0TMiLKlYKoX8OP96BHFyE5A73PdXdLnn9QP3WwzgwNiEOeGbW4W7A14ExMBSjG4RBilVEXG1GFeBCy/Rh9brhxtH+0cH63t182YO6bxilAxV10nZs5uQUAtx+jyBCOOmsky2ner5vTR7SLMfo6rp/0gi4Ut7Ccz5upkHbZPWEPc+5AiR7VELz79ACEJIuwtG7tAgXYpJCzTHpKBHQ5yLSklDQqjjryjqKyBXb5D6l+MXP5tKoWmB+3Qt7Dx9taBwpiY2cyqHNYWz+GkRh16+F1fPNBVWB99yZ8CEPVWFkgk/EFfmIJZrg2CpioA1yFqULC3y8evdTtNtgrKHb4TPRgdytfqf7KOIlTf6OaXUtvXqIzsI1tEuVGn/uH4o2kPKvcY4KCxo7ejGq7M5SLZUbu5pxb5f0wPewUJX/vMJCRgb7v2iIexfpnoXbQBcpjOUT8gbHVmu4NgR3xB+MUGkdQofXYk/EEc/ivJVxK0JenjThYAXreoNT1w5GnZ6E7TfUloQWcx3cG0wbvbIB+2gFSm5I5B3gh68pozZfz/Z4kW5sTW8sP3MKcy11OrURS8qT8XKfA3vh5MLr1WU4wXRTPxtlyPnOcW4B1DbM47WAmEiIbfKEokv6y4OcTXrnH1d8BF7sOON9o4DV4vyUB9iXNXcG6pRZfkl30p/ZMNygymz+uVJZw0RCt7XMTXCJXfeU4y7gnuKscaFwn8zy55JhMiNEPJhhj6lBmCfAWo5iJH7oWdUEt9Jr8YJfe46bsPlWtjbo7aY8A4zdUDl/R7k/YIwfAtmhDzLPkPm4SgnTCij4j9bsbSHKSVab2ILiJMlBywI9BXJZFRV9Czh+omDQdebOUE2npmlGS5impDNSQ42nckfcGR0rnV/X1ydQFHH9mrKGD2DGxhAYdyQLo03pXLC6bg1HgZYIO9TZzgaU6E+rbxyO+jWK5rQ2umTP1S7IOy9exuzEuQK/Ldvbcr+ydoJJArDLlSJ3Srp7fLzLjJawwhqkIxqEPrfyK++6fpNtzvDmGgL8YznIhsvN4uRuFo5FsqFA+QCOg5hBrHSeMo5hLooOFMVPLwO58fVD7vlmPwvyW+gN1Qh/y+dNlRpXU39oNFj4YUAYMytwAwUQyvNqpAFvNR7Gd78J69VPldIPKMPgTz59H7pdqRFlIXq6oCTlvjBmCZykdMgX7YExdclVS9BG38LBBvG6mI6vZ9Mn//sOeM8GeHnLOSNBy399EGhydX3E+erfGcIMM7/zxjvpmcqpLM4v6Cy/MwVFXU6mT4nODfDZ/5z11OkKzx8uOnVKvFTXk+Ffz/hp1n/9IrLxit+QW1ZzIuHt3W7X1xKRRKY/3gp8bCVdbgwtrPOGj/VmviclU9IZqimVo5czKgPmiby5etT5A4JEqFQgskyv8pjTTtpyx22KasKP3qPyhxBaic9wq9zIhjzBHFR+X+v1/0fiz8kILbEYJWxgMbmCFzLVBiTWgy/pe36/imVaZhtsk7aIB2V25TZNNrlv6fqbCLu7O3BMHCyx34n8Psc0V8gBk2olynjQkZFQXgkA2/KLzUan1iHmC90sdMXnzreEL60PTBtjr3hWE6pnHx6RHFZPb1cUqAFb338EfrRqz/CIxw2j+DfYKu/6VFKzwLxWBLTqn4HWblv/D32xTM3EDUOuKAGZ5BEHWosVqF/q1g6aZiSjnd4Xco6k7gFhf3DtvnhTwdur8dw7QUE1FcKrM0DYaxotzZZSDkPq8QVxE95+df/Qx4/BCoY4MWFIvG/VVDBmSL+tQW/f7RzGjLolbbDVyS24DmIrTerVVyZps1JHMZVeNExO+8i14FXz7iMO03OxKqqP+1YIJu4df+KoDVKhVUSC6vKVRhuC3YPZp2dgP440VnIEySdnhCn6gRCsSl20c2ysDH5j1mGzvJYOkXjixpYuYKpmjBmDFlVqgx7OBt9XUrZhLmqKpX85ALsokSKaXG5ajqiV5EOFCcVyKj8c7iscThzcJUeBv5+5SeSJTKMkOr2Z0h+neBHqGcoPnzOnCK5mcOAq4Nhpt73m4AhEZJ6kdtC+UbUQ23v2hUmWmMceA33m/vI3CLquAwmm0SwWMtAkjdykwsaT6xYHYlIs7xQgZTqRcjWeF/OlzHrAvAf2/6432ayTc7neJa0IAyNlnUFjXunCTZujAx677C0wbPFO8sGkfNsTssVufM4j6towJWeU2lyTb7qLDM7SPdKfyrqbN15xNAhcf18owHYLnH4pLFb3z9rHG1vn26dQUbvvOmUZj+2eEJ2QGBWh8WTmnNAv6mPbr2+C3mO8LCcFbtAHJuyOHAMLsPY0GK4FYhsCef4Dr1C57EstvlSsXUNgcoiOcv5XgUZVAfivnm/GcPpnIo6MGadIAbi4fDMZwJ7SYCsfB1wDqZKqsCH0Q73R/JpGdsWLr3sqr2Us3D4HV+EkrFQjkEe0jjgklX1Q8WSxEkQA0Fn8STVOvQJT4Dkz0uBCISPC3n4W8nDgVv4WF2Ev+XF6BnkoZo1XkjIofQ8LdyrGJYNaYjJIouU2xBpsRrfYlW3iIshR+78cb/zd4cyZozPvM9glgkWT4xnYrh1H9mrINdpeuC27qRPgmo2U32/wGLPDe8ClgBeVSLbyhxVR+kcZ3UIf/qrSmaHC1L97AJbHpckd4YhjwEsJgVv2+23uZLSAlNsilnmttvbQ793Sim8Ui/lAILjpH4sQJyDNwq0DjhwIh2DNENnOE2PdppWr603Geqo9h9yD2oyPhOzRR8Acxdo7YDV7NJUDtyuF+RQV4PkMCiiQ7IJM+k3/G7Xk9UcGS2XeQ8P1/G67TN/mwu3wCWtVoOsD1D08FFYmweYAFx67w4bUIIVvK7Y+inQuFNOFvWeeD+LSwlVGjcNMBXxz5dx/w7ZJ9Kak38Bc2eKpUW9qA0T1sT8xznyFAFt8VkzNEh7quu5wwmlbU2IUjClRhrn9SppPFKlQSVHaj8RxQe0hgmli6owDs9vOb3/EmZ6UzToT/KpEX4zfjR9xKqQ2QJm9gBc+ca9cbt4EgmEZy4noOarUfKSL8XMHGAd4fnl4OSn6k8GqXEWvovfoC6QOeNM81XFL8IeeOz7wyDD0LjQ/HYeWPHjHqFNjXlutjiwODXw9TgPKRZYupCm84IdG9dZzK+iVCvQQeX+pWqvGWevqA4D4LdUNkJaP2ZM3iW9vUuBSvcqsJB7VqLlHejm/NyIEKtUpvpRSTeBifssbOH8/MKi4UBQ2ZwmJhImdDb+s/GRzCeTdiNJtBtect4pzGNVO9tniBlPAFMnxWQDec5kIdV0t1Rq+o3BbUfM/mHAVxAfMvoNgFvN8pGsXimlQM2J8Bo267U4rzRW1HMba0bb/VqtoUwoS66vPzEVHWefHBO7COnXLhTmdvsdF5WyESwptt3LNWm7iwF9n7QmkjlxhfiOzFxdN3nKzNX1mmmi8nrBJKqqnXBAOgl6uMCO54+n/Wsse1rL28fV6dwgEUmAXo/5FvbcQMVAdM7NfJev7coBSykdOZjzOihM5i0uBH69tC2EPlMhlSLVykjJBP3oTlyhVMY4MNHADzqPoJcOHsagqaytguYGrJa8OWEishDDHTcQ2x2pOML28YX13Q9WOSZAf1NhQau4WPVYbw79wbr/CA6dcSfoE02FhZNcwLSv4qJOtRPiv0OVS2LsQEeVbuL9IUIWCEJ4VVvw+FUHnuqHm9Yswl+lraFOJWGmfqvK33TqT8i+xV8LsoH4gkDs+pnDsxQEzUGg4JWeGPpi8uKTo16+2gonqoiV3277lAUQZ39r5RCm9hfIqtnu+j67fTCBDct9xDso1lY7Pc5cdwBrQdPEmCGgU7bh5v1r2p5W5QDzqp57g7sFPHe7w7GwCmFiDMKH2MCVitzp1gIjczGwIJ5OweaDXFt9rbaBORDhhl4ce1RabQ33UwfYu99u+q1xj3yMEIRg6PJeP8qo7piQcoAuc+lDDVfGGtzi9gCrx/sr0LLo9hhYaKV5gFl+oJwY6Ubg5zj1WsKc+G5loy9Qqh1yXw5z7w+GM3zWuxxnl/JpKN6AcVQnNBgumDh5vWpsUkrtdlS2xCtOttMwwufT5C7mn8cD44u8GJfPw1WMr1FvYZgAWCxUVQZga+g+dL3hptBXWyMroxGbDbEl6YdS00ytZfETnJ0rQNltMeJRd45y20GeigW8AEGuf2b6uL3jhlmyjfc8q3uhqAG4Gvk5S5wEYmdq6CHPP+upAqBuOU9h3uJcg4lMR0NSuqqRiLB5SznDikcsPDVQpdaYuBk3d+MF6feJgpp2/ClWfGBlitntx6WT5fQp1v2lysBPRWUwK1RowCxgTdeQR5Uat2alsb/A16tlZaGKIcaUWTGuocgE3fryr/fcIpK5mr+r1PfgrQw9YL4koBIwp+7ByfY6sFVng5E/cAylW8oIdpcFb+PO136JKjF653mmOHNYhODttmICcuY0KxDwj/0QNilM4RUx+5/n3RU4mk4gW2Mw6CKxJagkrdaK61zmnat5f6XVmh+uOJetlvjWWilWqvOD5WsgNMem0p2VwnLnnetku17/ZnS73BEqUlrdKd1qQ0sdcNyDwxXSJevC3vv9ClOKSmKYfx/Ka3FhgqM0SzXZYRAAU0gH59yR32SSfeTJASJ+0ddsmply+H/G6BD6cSGCcVpdIpVeKG86fP5AxfZev1bjL+SgpjG5/1oF324W/8B29HYiPqaszzrYSSGfRfAaL3L6G0oVWvslGDPInJklmoV2A2IlgX1rfnhEQEK16K2jbXh6G0Z0OUNv4Jl3sffGXOvqaKItjg1RR1v6u5y+Olw2CD7oRfJeL+93OfNwfBlA0efl1TjlSznh2WfgPLMbEytaTdvyMaG3WI0nYvqloJAd+3gBs6N9CqPHkRSz5uZoavTyolWcTKb7T+1UYteJ1UbXVplMRDZcVHNGGKFPPfFo8LMmKSjw6sDMYzsJ7dIJMB5afr4Rl+Unfqr/3TosvhzvHkMdjTp4/LmhogQZh6oVm08pufD3DrePGpJtADY19ZCpKYMZNcx+c/4Cy91ZdRyc1b8FHcuTCMfkAQtXGMZmF+IPh5Y+V7CE6pX2Nk1p1HmkwGqvCi0+QARzalW9qIdbwpTRYITTcmg+X/p5KNpQVfcXSjtBzUVrDb/fIoIJZ27VOpfuYk4ou6GwIlU1/GqxNxdiWz4X6giVxalsnEb9kOgIJo+L3Y3jgj++uClcbH76ctfqH34+O//UvTjPO+nz7+XOQadyeNDvjsWx8cX2XeVsY28BTtx/0sfxos8n3WanzBedbS3++Wl9eL71+OnPs+31g+0R95b2/lJIyqdBxL8FkuPvDtIcQ8gbj+SsLq+QRWQT0UqVN8yt71588fcuvhaavcO8e1Ebfy5+KrufD/Otp5vOxUW30Lzrfro4axW/3H2QJhpmboPkkdUDxH7s98SkFiZ0uP2kd/j36OJT975Y+HJx3auuO+k/awdf9uRCk/U7Pmx1P5yew393jwdn28HpWb7fLH64/npR4TMloSB0WHTw+9fPf/oftmu7J59Omnvf1st73TyfWeMzj7+XF49L635ro373Of+l9rlQ2zk9f9z+LGOrmD4MIKDq6c33h7P+oPqp8q0+XtzcHOxd7D7s3n/n8wrc4tfi4/2X3nawt3Hy6Tz/6fTowb/79Olw+7zD9yZmcah6cfh37fq6+32v9vlTbZg7zVc2Ttb3XfaqEa14BU/7dHE/OtvevnZz3Vb1046/eTrcWGAEwyI5IOC8Qe1TrzDcKbQucp1xyR+enO4NHiv5xQ77gxcl8dHXzx+emqUP163epwfxr3iplf7Hzdbg6CH/8U8+F9mVhTK+edI7HA93Hz/Xz9a/NffvTncGR3/2974zUeECkY5TPwvice7Pi9fVhYVcp+O5f/+5eXzb5PMW2Y8df/P64KjY7X3c6X5vFT/lP26cXJ/ffeIZQEzkYqrvP9WOznq3R0efzocHdyfjT+d3w7Nerdfu3158Kp6cfKLzMdkP6JI+9T8fLTqzhcbu+WK//8077p8vHosDXqP/LYAP8gIyG1WxC2d2WLsuVdrNstQrapIOHruwe3LaOq31Wt8+fDn5/vX2oLR93C6xpVqTxbGPN2sP7m79hlaPfq4/87Xj03x3++Lpw6hZPCnzZfAaKyF2kFWmxKRN8XL1dPBEn4FwSSZdn5YON7nkwkKtIv3rIBB2DdhzmH+CnCFAGUmEkoBtaoQMF1kqfJpoljxesn4qB/fnVi3TV+3rvEOEN56Eyq6HCh+S45/2Itx+UE/XP+h9KaF0XsqyQpIi6PC8pZEiQds1AZy7kCVEH9teF2V8ShpPkeqXhgqYJ2KG0G5C0fBI8DhQKSbEP25RqlxmloBPp0AESDtDz+s/+P4m/3Klfrno9D/bPhBKa7KcwcEC6D14DmqGfCKul6qdjt5zNI9LZpUO+cMbKvW2mCdYdJiyJz1FzWNfLkf8TIjIFPOXFI/sDwi7zyN05DXXzVEGxevPU1Pr0visfL0yxrGIuTbFSlxluykuIuuO0kVksmrYV0S7GKIB1X/cNnL5YXyVRxdqLZiq3cOg0Qkag+4Y8jwhEhRyu0ATrjR9o03Ytln9rhP0PPqRRwMd7mgw/piWj5C0CV0WyFWb+rHwHHd6NMkpCGMslavD5vc0o4WL+ZKMuETS91BRPxx3u5NtAOWkJFDQUJqNRTyXTUmoc8EDbCCbnaydoCjM8T0RWwFjscqwOiNQFasehaRESUkJi3XJ8kctYr5MAelzdBUMa5Lbec8rdU0VR7aypCbhAftJrQy7PoZsjYHoMclNKukEDij5HIz8bufGf/Cd7HAsx6sawU2cutdA4XGAktQQYIuYlLNQ41V3IWx4UgjjZpAOM5ePyjqszBPRnTykdKIXt74oTX8jvAIvjSMsv2bywmy3nGSh7J8pO+J/c5MpNqHGtthGUDAaDnyGaBV4MGrS9LedQJGFhOA/7Wap6op0hvX+E3Go3NiAAnmmPd3veZF6eNzdtdVwTbwpQpbxhrTRhOQ0+0A03kw6CWQQWMfh2R2ySNz/QHDGesgPnk07CNtaANjWPn5swccj/HgNH9fxYxE+1vFjIXTuKeK8SuQFZuSVOPx9Mpw0EAjm4gnX+LmYCm1es5ffh7JaS1xJRwfOhLKOjjXhC0QnvvByQIpXkgIxOiFgBpwSeN6dylhsp9UEbFnpWDwfLOv+zbXnX09lnCKvDAGjr4ee247chChgXx31YlQx37z5HWSwtCrFzVjiNhq3LkBdxU/I0zkX2v4xn2ihFOIGDu/QJLZt/yELuUiYhNstMdbAiF/GxhvMyF2ylXR0iEW+2rJkDVCUXVM11K+fv942N247Xz4fdg+/nVx/3fn0TdgV3Y8biC0qSmZ1W3N14lO3tP9EZmhQegbwdc4R/TzlMcEMA5lH6RrcaxneNX1ryB1ZgLZPxMt4upBMRHBzoFgOUmTQKNrDRaqVgO59yjIdB0M9j6h3KvVmMPRHfh97YRAz4atE9JZOKHEUSs0A0qLY/nluCSUkUFFCTm2RZMGQpawmFoEI/6v8e6j42nODTsd5FZeKMzsHRL4T+NNpTxCQJVkLpI8PY4Mgyhuf6iencOhSCoSCnCBqAGfnYCFOJLPIZPDQflVLxXBLMoRHmXZpfifKTFrEtBioBGtz2znxyqVZ7gLuhEGBDE0B9jpLKTe0BSZls2LBcZVRY3QmfLd/yjw0nRXDPDMt/mAGLAdjCNGUfwZOrN6qCp/CE8hDcnbT41BiDsibX3sC0z9q6Hzxj+LY6TdHH+nZMMHpIxS1X4WqL8ayI6y37EzK6G4hYr5KHmXi6RYGPZ1IvpbqSzk5oqcZyH5UuY8FUu917uPr1r7cMAvF0IZpuQBUPpKxZ8o0/6IcO+1yN/HxRWtC/2eShlOALNLr/3we80bOs/l/4AZIBIPxFYlICI3JEj8tbYdUSNbiJeD3M2u+H6arukyoHNv/o+0Ac3yKAKD/nyB3CAVsYlAfNig2nJQ7bRuZuugws83Y/83BDeUpy3w2HoeqfH08WL+UQeYo6iFZjNrGNZiDEOZrufrpqcdHJ2fKS0UTXr0+qpok1piuIBA3niF5Cubp7km+tXtQ3X+qPQi1LP/14sOg2akItSx/3+ptlyZ4wqZ/v19av22VTrpfet3xfu/wvnlae/ryuZWKYQ3iPqkE0jAommx6mLI6szjMkQLTO/WDgMuqC8WD8mGnkm/1P4lubHfc4ta41T+/h3P4ngR8rWhZMwuWWKPtj8ygGtQgu29cr5jcXLPXDSyUjMX2YGHKC01j/mHipizWFofAqouY/gRVBdV9e1ByEMoCOEasr9W/afqKF8lg/VssyfxQveVsA7k+pHWkv/p9L7PryOoFaapeABhaP5BU9IslVdsMknyc9gQbSuHm5Qe3d17XpZpPd+qKkgwjOrMHW6enYjXyG4IF8TB0Ackif5mX5R5QS/rt6kdFc1Ccnq9/2No4k5MMwPm2Rwde9Pre/v7e4c4E63oRDgSZkR3i2RTD7jb63o07dCecGJYi8j6eTyWiaq/axRNej8Am/6rxMgCqY78KWvJrk99Sq3qlm7L1rtPtuuiXFq/RyQ66ltJWqsg4O2ZGGhU0RD9FJ0Peg4KJrcMe2J4J9q0Vy1bKhSN5mHH71kl10NXxyIvuePxsrNgypseJNcDXzMfG30MVU52CUnYJHMwPjp41SMG0Xo1+ZJknFHW/wGGQnuyGDF7wVK9Ct5RT24LRP9Ob0zbfItt8P+mPPSF2vFF9OOq0ut76015bS5WSLGCv1+aZn1/1u4D8pPIFEN3aeGqWhpn2d75okTHasFYws/vEQy56h1XS0ydhew/HMJn2em6Pr6pJJodpTDNaTkIh2F5XeogtR1wMN5QiKI3xS6rpt4zlLjD13NIcWTucp3uwRDDiSlKLLMroPKkuzuzZ0bwSD/NOSXzZ3apvio1NrpmyDIC2/HtDq9LbmOqgvKAgVW4EswSeWMWKIQkcQWMZVpPuqZOts/OTw7OT+uHpNjLYE8OUfOopHsxQnnQQYiHHn+mxY39fIxUO1C2dayXVBUONMHQJqWpoYj1bI5dvxaBhN0lU4S+7DzERq7RgIy5or10Tz7kwX6g8h8kLUrI4wgt08v8lmbwGkf/DAaLnEX8td9NElQGemEiOiSoUTBcbVFUNenEvdZzNCrgUHKLVZy0FWQJFIW2LZeKyiYf+r4n+eG6vweVRqICpmv2jFhSE0VctFSvSVWADtkLfpKuSvc9zJLfhnplVp2174t/Y+4Q4SRzQnlA82/Bk/7BPZ71euyQ5vr52PRjLFGxFKbe1u38UvtxxhtoC1ou7rHL5JU2GWKgRhgzYyaT2ISa388t+WSv3kW9dUUnlVhgiPmbuyO05bYKJfxKe13Zc1MEZXcOEainF5BZdw6wxTf9/a+HCgVLe7Is5T2KUCYddLWLQO6PJUIeUpqwg2axVu6FtpbLgckLW2NOB+9CXtYkgUXvoqxK5i+UFGUn4RWpeoWQqtVLNXg60OprNGKq2+v3IKUXjlFZnZLZBQ1PCE6Z4014yY02XmmUxa8UHvKRglbxgKFvnSy8on88DRzHHoubGwZlMhrCdFri6JO9IDtW24VcFLyv8KrMIkNLAMK954sj9f+i1OgOQbfPKHTKvjPZ53ZOYjI9EKHPLJtqJoCEiWfpqkM0YG48FhRzjRbOrrFMnohgw9eDAnLiyeqipBriBEqRwmuWNUl64f+KFinv3MV7A8AJ8We/jGcJl8MJafJhvdXArrNFDzKs3d/Y3K/xNHpygERTTgJgT5J956WrDWg89rjmY5rSAU3liqfEzJrr5oAX5+nmavrythrxMdsh7CgurBpevSmf5z18Fzia9fxbZVyvFStRXS8LjP3IBYjIrghKi1IX/6xSTXHSPK3Bg6YFJvKvPCPxoOZWSRmY5nE+EEr0IQl8agEzzJWUYDwYqb9WaISF+mW93yhsWh8mumuC7ThnKkl1xRFjf7ZSecq+aqzgxgIjPGurUKjXxX80U1NL+o4IrpM1JjNCp3+pgFPnUa0F6OwhikjXQQ3zcIOhzN9DdUsVk/jkMHk5UUBE3LwwrGkkk1hY3/QJzm0Uro5BaZU+wvRE+0OsRrU+tYPCfeNO1VLPjCGIiV/LP4Aps+v4dDwqqjeDTE+tXWNQBI60sa/F/IpD4P9fsvysrskjJ0KAxzqkNwZAh9quDcgus2Nhiwfn3MaXCf/lQ6FwHCz8ORxOPaQxB5KzarTkx5Ybuw0SyuKcQQgkW0GpnZZJLWRg/KZEksIk5c01vkcFXYkYcufPKyRVJ+3Ms9wbkw/8rD8r/wEiE3k6sa2DOZgVepJTwvMmlpoDqtspQNIiX1DhTEuO/2YJfVfxio36yKc6DnK/J4fnB+tbJZOPTp+JqylZhsCYGhz04ib0yZW1NUcZVXFPqbpHagbYJqna+kLoXimerP4YV8Svrix8J1SCqgqLVR/tp0Cbc3N2XD/SLVsSvmA5TYub/xoh4gzIiUmT3hRHjkUGdCOoTXluOz7VVXvn6+7+1SKIdz05t6bUosfB1y0rL/Ne7lmlm8FhhjkyRNCTcSGghczFT2StgZEOMCvHkpuvfR96dy01Qqb7ifxSSn3Jn1IfJ4/9u9RaDg2t8f4JLx6fk/m9tMf/3DmIzLM9LNCrzecSIXapM4AGMkUo8cCRGyg8Jnawf1L8eHU7qm0frW5P61/OTLfaGxEZN1TzdO1aTnw5sHJ0fnp18gXQj+Yuc6tw/JJgqv1CxQny68Jq0HtLOBLYcqrgIp3AjuHnngWpR7/eGkWRQZ0FmMyL7GOan9GyF9FtSqz5KMGFil5SANN5GbPUSR4E2IibhT29Hz4eZ6EBUkbj8zcmsYAIU0Lw/Q5lm0fbixB2hDXrsPh2TqdKS0TvM2C5CpmsMxSlvFGDgBTfKmH15t1ydY/1T1gV+6WT9XMam7ilPi/qd9NriP23R9i6SAxCa4yGglFEu1/KILazQMuojQJ6VxbVfE7qxfrh/5x0ryghqaEd6Qf0Ozcaohs9jQBvl4svOA8Mf8oyVFS6FwZ+9Qq7flDN1h4xW7pHnsnJURH2KK5UaB60Hka6kwtQMjim6W8jGK13+hQXUKy9sj7G6iqlyYTr7YmUpWpN2z+THwWeUvBQQ7hHndPoNZMwKJp2eWE5BKqc9b52WD73me+BuWpUGk9MOvZCYx32FHwccOJNrVem7MK8aN102eGhe/lPkT5ZLZi5G31ogUtpCPJTQCSy+gkvjMCxZsTefeG53b1Bvt5VSR7uFWR3TcLnS6omqvOIfqSwcAnlNem8TcqVaTDG7SEnv+cUXMLBiqDY2JhsuSuF03/Tn4B2ythFu5ZNRS0psTQfyiZtsPQ6EOjDoDHEY0fMu1JPb3JPnDnOte6iszFG5V92anw8DJ+TtmlOOI72x6W1trJ1Ie5tA7B/rewqVxJziWZoMggdCsU9rxvAyiVWelqRIce4iR9vqXCF3u76xtX509HGyc3S0s7/FWitxDRA9ZkhfnFLKDvuPxaYnNzgu/njU9f27ieuLz9fXnZZXqlYmbttvIht1nL/tlaP0C3d5yVU3ZRRpABapgIQmdOTaNtnISKATWXor09JbafxmrsdV47bGWg2CvrkOaWFapHzcK1ArsBIcygGYg62R4ouBJj6OMfniXmjInRsIg9X3O+vuOlsSSOOAlGIxcp4KIztR/zz8CA6Y2CrHylcc2lgMHITNvmd0NkTDB4dItjt/yBn6BxYM4/fVFttA03/c79zcjtxg8MhyZ1GhF+PlDoGZFtV83TArV0zVbnj26GAnzx6aJkaxs7AE+gsqRNPY/XBmbzyfkqAz7LCjQvBQvUN2IySF9L4lV+yKYXY8qEklV6591Dx5i9/YFGitA6prpAuhk7jTsGkuLMa7QuSmcITrT0nlWWFIrRiFg7Bon2TjJ38HN6441+U2KTOoWOGDuZ7iz5BfFNpUWy3SuZSO8cKGaqHmgpY/6rhNt39Hdulr/K2LC8YSYt1qxth8oLyR+Ci2NrA3PUJELK06/+UGZ6zNaS23WvdFTNXNwD6XWk1Oa56fCr3IVXzBc9d+33uNKMa35Aqt4qfbkjwZllD8ySXr5JF/58XHa5yyLaYly4qRkr7RFXdpXBw39rCIIBN20PnIslKgsiNTAW4qyVelPBsTIzaxOj5ZWItKjjgW+VohIuY0q55dGIbkqSpxxvsxkr0sAPePPxhADnTwdkhE0IHi0aD+bfp9/DaiVXaDjzWCnWDoBcIQBfmLFCpNlwNFRBMjNhUXymU6uVYQdIKe6+Tqfb+Pn7ItCbpBohiQXcHoSejat543AioWuvXt0LvW38Rck03JVDoQ6LJRbq/MzxWXmi5GS4wTjD0MFwo41WCODxhaPjnLxak8N5BXpgyx/SecF01/xECiiC7DjFvyQqLqraqCV2FaydcUvLoQdwKE/rY7xH0YBOTx0L/voGs17QPxNUgeepnMakElrCEbFWgbg1ifMe+ESi68c0IGFf+SxmQ9PT118nn+OZTvFmsuIz9l+nj3WCa8qbO5rygyIFFojSumQGkCXYwB2rF5o9dWO0FDzEJ/PGzF284reNZAQ5WtkDaJPpzdlo84nMAN1Dncx5oEskSrMKQNyHzX798UxXYV26kpeWaMo68QvSVZxCrx2WGbrPYMzsbh04AgnSEWjmjD+jGnebadOIhpjahwKiXLRfxaYkyP1YSIsJ3Cofh6vdqi+jZuWYgpixB3rk5VAEiA8nWEYA+gEhgzs0ZVqYt5IaUMrtxxH0V8enfv9AykO3REHjutf9qC42xCzaosOyP9PfKuNGZB+bIs4GYtrwJI4Y3mFzk3Yrl57Umv8JQ4dFEaK2tJAljzmuaPibODUyWzcv5lYmUCg/zsXmYpBXtkCFSD5bLg7At4sViyRfPxsxFkFKk0ySJxWFR36uPRrROhGGGhIwaaBncqzZETaEZrLL8Rv+LhjxGTht6e+aEyOi92XHI9CImq5aQ9MKrWSDxLfOsWMiMw/wqqBEONhCleu5ia7eizvQVAOdIl6+FDpRku2lhvfD46gdleNFRqcTuNKLcYXPotLJkTNRzEYrY9fm21Ga2sGF846a1GtbgjJbC4OuLCpAah5wj4W9p1ct1XIxRiRIGKdD5c6sC/vsbidHzJAhMR6kvq/TYWp5SVlKo1PpWAFnElHmyOm3CMlzUCJ3CyUv63fDFQJXO/jotU5oRCloOdQvwZejkokaSNq3D8vjANVsy9x50QOaLqFLWywBY5YN9E8k2WtZJvOZ7E6trgr+r0Ow0p0+CWPfexQSloYm9tAHWd4VaPXM3ZXZGtkZ6FpT/Cm/LPTWEz3mkMr/qj2lABvddttvoPL0SoRUvDhbQ7IJ9uWi0xaTIwc+a8R8xDAgvGqEPUgotbtz0fuOid9CP8peS4bE4y+NaI9oarP4yH3e9ymXaHQn70zLShKevZPFHbuHGumhcsTIKJ2l4SuET0iCugki9fD0qJz/Ai1L6/85MVJfAjptDVT2YPOfEVAVn8g+v0q1gAxbJjpWCFxkelD1rDS5sOFgeIE8IF8idVmPNLSzypMUpOSjnlNR1eRF+LYcpzu13mxQuMlQEfr4Vm7MOfxmCgpbPKuSzEdLXMSXNafmVlvPrEa3poghg8NTWql1wK586+xAIYYSiOaqax8GznX6e9wT0eyIL9d0gCfna067COEOiwrlAaUquNVS6sZDhXdKLtVNlMI8HKvzzz3zwud5FCKLXY1RS32KM9lkt82h4kFvBNd4DcXXIHcrSTSQ67wT5YNNkHyT1RyktuUaoyGWgMhFBIQr5Re8IuSizLu1H7/a476uAE7Y/cPu7pSTMh2lTHy4jffq3GHEPMxvevyQF+VRG+tC538sNMVI5qvS8uhf8VkAw9IBLsoEfsbO9sf+s9CrpAKkFQpNDv9VytASHDTSFf04EOYyMxDhD1D62H0eMI2H0suh+zP7aeGJs3xjcn1hzw/D5BcXPcL5wIrRmsKtgyVqdnpCpqsl9w2EUu06kAgQYFh7FnkauiU81+fkX7H9tVdc/IOlKDRNsRwr9439bDIwk/yflpGKvoBJZaCSjcaaPEdhRYardDFyqbphBObZQavOkZryHhTUmmGpOb5vhwJ4vSgmrXrhplzGvENFOshZV+CWkHFviX5bRVf8u8bLqsDAMZ4pPuMKoBYAIhmfA0M2j6D81lfmjkmC9HEQ5Cj3bYUfskNADxz0GnNfQD/xqmudtC/JAT4mStEd0LlNU1vFnmjJ0z/ckxrmpI/m17150+Lmb1yi+OG0fHZ3tHh42PW1+ivjbTnyhxbeqPbryFnniDwJtb/7Dttkb+8Ek1zA9De0MFg86kM8XibV/cmB0CKTjkcb1D6C24SMHHJfqxxv+ijBWHZYlmKT2RNgbUoxX5y8j3u4F0OzhUjca9h8hHs+stKWhhN5gEnjts3U7GWB920uq1KcNYCLZJD1WwCSnZw2CCjU44n9ULQFGAShwpZrupzReQfVAW1a4hq0wBuNzPYf0zXtPCfND7QlI7GIQjdE8HcGbGha5nMm4XPdEwUvX9HUjEDgZeq3MN7vHRLbDIwBnDzui2B0fA5hkjuBcdmH4fwq7cHdg07DJop97jhjBQFMG7YtCtIR1NDZkRm27rru378r1OJSUu20TB+K7bTHUTdyXfCAXlYiXGQke9e6mw9APeLpRA7gmzNRgNXTENE8vNpcJyzMZl+eoUZchrnHEGleabDA5HBwwsDxsVf278btvrUzQuk9F1a1lRRYKbYtH2Svj9p54/Drb9Rz6JyrNGimm0wL4AYEUDRIJailgStWFSLiu/jelSw4cj9a5MDJyJZHhrKqFeh1pi3A+hnIcaldRWFMp2TCBiUkGzWl9mr9mLylWYq0YsvDBZjbrE1isMmTnwhr3AdC1begoV2rak9kZP1Ze9uf3OpyGs1oKvicEAuOjiBP4CXpQVeSqSMDmA2Gn6FIOF7+RajzLhR1oJWXVlSiuxrLrwyBpvPDS+MYpRaIijHEgxvvCXhlv9FrIN7LfwtkW6qTNnjT4SvRRM76A0ilRpLHtDCyuHipO5UDHc9Lod6jMHdKo4+atlST8rGb8c57HoTZysrrYdtW6QbqVQjqg0/4EdrEyjslHmWS83dbcUF6Hnwk5xnQQxCckbIJiOaQuCXdO9ww13XjIn0cae1htJOgN2REAOb4hs3nkqNmpxM9SIvcNmerRmTNSi2rj1WncbDCZWMzHu4YxVC6xq2VtAXExbuWVKDf1v6AyzP9g5aiBRAgs1LYaUiT1reZPYkwVlDFARUWwShjPZO4Y0gHYbEhFSqyn5KqpoZpOhQI0iThgrLqaQkJKSpMMg7mnd4bFBXbSwlPjo3rl3YjizjSw1AGzq/dH7d02YEDljf3KiASRuCxXRQsHae0N0a068Cq5UqF9ZFBgL1f5a5LRYLGE8SVOGXR7lxbKfL5afaV/5c/ew296u5d2LQvdz8XHQvOjmP24eFA+gkg+UEdrp9pu92tPXT7VCq/jp6XNxu9Ms8R1QM4RlEwaZxTzXkUQianGnxjD0jiyM1k/Pho1AzhX4MDkmeuOl6KVcHhPnK0Kv6DmQ2wFTtqNu4JCefbS9vbexJeaFeBDxN0YDN8uc/qS3A87AYKUAmReq8RwkaH0xpsy46enAjdRd5saKyrFnqUJxNtGUOABZNDmNyokJ6MemoS0cF77mFw4/djtfPuZWB59q+Qv35OunP4UeejJq/7m9wV0sRVQ6jE29/SBUYpc1ZarLXZCOZ3tjhGWxH+cDj8mKMARoDHikmHlvbuUJUJiy4ghUYlul9FKnkJgaiXBKmfeno/bReCT3F73zlzPvT8TrrHe7lhdHPgk/JsrkBXMsHsrd0ufvX0ohvYZy2GvhuGD6BWYyw0H4Ws+gGjBjJiwjCX01Py28qgsYpwwq211xW4600Dmd/jc97JLDFbvTOD/Z06dGNK6h+zAedlXsCmodl6YAdcLM5DXKc1+MZFVHrSJbGYszmlad2VV+UPxqeUIMD1182ggGZmRZH0fyqv+4sl5gAywWMKYpC8XRDgHMbbc14fWuWC+3LrzyDarVss42JV8SVZ53hUbp9v37DmABPzdkPamdszpdQjnZgOiUWbhW1Z805OGm9ZGV9+LngTuCFDz2pcyh4NNREnrYP/6IDCrOA4zex6rZo+HYg+0QRXcxTxVkxYjG7sMxxpQVyAGsdTSCA57oSOgGHkH5KO0Mr9FtrJHLI4eoRODYBVyX28aXaOisWLeewEHWPVdtXI8JgOQ3T7nXloMBiCApAMungEQtWRJ1o1ZzUJPouW3UTgLthcA8XPt0idXP5xdAl/Sa5ukVTtvVp390QfMFOOC9qhJMzo4LrysENDz6gatoeGtVYpCuojQn1LPej4betdDxbtf9UXAEI4/PLmfFHBVN+a3v3svyKVDqF0uR4PdOv2UDH1WVFXhob3TW6Xk+yOfIbSx2QJxCqjAw9xpN3WLeVt0H7tDtBU5YcVe540TxFIybAKILF3ePZfnmjqj+RNqG08SWP7yzLQLT+3o6ckdjdHGh7/Ut+l7fmr5XvZIIQeCPOhi04GdFh2MRIR0867FdcdreJurWugT9bEdGO8g1qXA/8kohA67CEJnw7mSszqEQWzQS3BcMTAFrs4xhiG05HLsgpYS9/FotsfhEdEVDJ1bfxnNXnTC7H+KkXh1eMyMl0df2YpiYK4utQo0ZevSFvES92ru8qV2uRcveoxZI4eG0iqHp+t3yT84Q5Zkj3KvkTrVsobRyOI7Qu1zbu8/1x4CrXpa+czaeMbEXuaeXgWrZUA81+IQPTOV/3GzlPrT8vYd6ffPP74Wd3T+LF7vV07u9v48e1r/uj/Lr6x/v1vc+Pn37XKmxQy5cEtjCg9rmPebdQtry1Pt/KazfNnufgr2tw6cvF4fDr5/3bvhakKkLqJUHt/5Q4pl7wtJvNIUcfGqIDd1syjEi8w8DRDhBMgWmczlZKaoxrbOyGBfUTsvy7iA8n07/3BeHLrwmZnyKVcbs5utP3BDlbi6EmclsRT3+wXmWlp43//RvPuy2u1869doe1GzNfzr9dPfp9OLpw96n7ZM/P+dvj88Lf3Y+nwWHG536nXtR+XtvM3+jweLiZajWIm8jBp27EK0393XrbLcOz7q1DjvR7tb+Pp+7IF155gb/CgfKXOv+XvmmFOXetACnGca5NMM4UZ0EUUezQ/8h0BXgKHNzAsku8mN/0oL/6KsSXFJlLfDTUQplGXcXDg77QcPA16+tWupAXMxqGgxn4AedR7o0xq+JW4F4d+AEzIPDcO6NrLJnCf9VDnWuSuEPN/ebjWDkDpXFE6ElZ+nPTwmyHDLff/vNCsel4/y4F5beQXmHxcJSQrrkO5jFghhSWF53+McqTjQr1C/R8sNg8iDLExqQUdyjJ3nJH4SnfutNvvl+r+vGnccdoUJtdh0CKqL2Ludo0x/mzTuovScepeeNbn0TFCDeyIgEtnGyNEcsG4CCZh6CpRSThCy1l4qDNqbmxIjHRWRScRZdz7/3IjcpCJus5Mji88VpDRJzoUEscE3jsMLXOWUeMpC8EJAB7IbzFkAamGE36uy4APu4YemgFDexO6EMNIJlmMtYtuPDTz1QzSE1RixqaPPUxWm1Krc4vhS9B6BAKIkqtCevgz5bz1VGNVXULpm32ISUlF3vs6OjEJiUB6z1Ygq//eDeicnvZgzri8pHiE79v0lykkix4xez4yL2+luzTuhfzu/Syf78O02MWq3GCEAigcT0z7xcqljA1a64yjcDYVKzwi1L/B/miRrfz4Xy2+uMMMhtsAhIyuiXdw++Wy3CmRJ+RQ4Q+jNMgawDjA/d1h+PeOwx/8we+1NMBSaIsjwrGl7tS/hO5LVj+lbF2lxkEhYgIdJbbXpuqlcF5a+oaMjQCYMJMLmrCHVa3v2+NsDEcfEu3kBT4g09v3Ek4Q3s2W+zctN4C+IgtTo3IJmG080ddQA3ey3sJcyKljGD44uorzHktSSIN3vZCsKQOD0FHEL97Oxkb/38bMu4XvTmQzA4HTxNgvHAG+KfFOfdGO5BTDLD57L1S1OlHLCJmqLcPOyT+XsLJn3aNGgNCUwAingAtkZ3/kc4L0f57zHBrVCWfp7f3uTGwTAnGs6Jceg6yr8DbwkD+Ri7aY0o80V0Gv0aPb897nrB0tIBflhauneHcAZlzQkpLF2485pCBDadi04f6nzOHp5t9Nqn3kB8PO93HukLd7DKKGqztuJD5koWGICEhoHb97qTh9teCjX+/2f9KIykSXM4HrFXANPjFvOsGhl/YnPXoAWxueLCmUjkQUqh9KNqGtUqt5IBtm6+NE4He+2tIYrprS5raVSt3H6yv5x3mAqEvR9WFlq1Gr5huAKkG/S9EIrqHrg3ndYEFd/JsScU3sDvTzrK0zHZ2Nt1sim13eaxXoN8stSq2jhE+8Q49IKr9E3YGuWlxVBHI1IzBelDyCp0akI403kY+UNFPmDa/GZzyySMKnkVJIi//gZ0h2DUfFKZsNNaxL6REmY5PsUYINKE6BEwiNRjER6yPuXapQd6Z24HNGgRG5pQYtQonr7Md5QGE5hITX+E0KJA5W2u+6N9cWRanvAbxIUaWiypyaJZklhhbId6E9Uw5kpKIO4u6qpndurFDbmFZGw95Kl48zO4wHS0gOHVUmfqWYZoDEf6DMWjodCqYZ2XwZNp2NRUXMU8MP+zE7RepkbD7BHK8EZrHIx802Vix4rxmBhcszwzj42xH6gBkWPBT1SVwe4YzPZrLNNE8urr5w9PzdKH61bv04P4Ny9szv5Hjkp+2D30PxdpX6lqwLljlGr/yj1Rxd8M7/fZ2XEcONHY/a1I4U+RywohDvuU0MxSPyriRazaCpoxOosMeEQ90xVXlSZBKiAhtutu3G2RDCDbAqDgQvHgWrufiLWYSES4OcroDRfnSE/De0KoX9H82fR/YjMcevea7s9mAVTRlwmFYgi6Jw1sGPjUj9J85fnyr2cKGpb/ATRUAlO4hrmDKfu8YVDl73IxNooDe2QjJnHBqr43hQTTmeakW06ZzePcMJsX07e0UL7j3hXY2DAhasLUsMF/4jwMzRbylIX/9ne9bldo/qekrjEthhQKIsDpJpHbcf309GLTYP17Z8hRzOSx02NOe1S2DZ1Mb2UwqPnEF1D+ZUTwGr5wUIMjwFYs7lys0HsU64B2gRj4sQQsOf8SFPQqX23wdsrevqyHizeygjIKVdpA66aTIcKE7EAOT1UqGZtHG+cHW4dnjZOjI6MW5XRy1mnBZBOr7PwbRTl6yyhBs0Wf57ySyk8NGo/BglyXU/z86EknuOjLelVkZW52hog6P7GaMkMpkWG/CvduMRI46wdgYH7qeA98Si0S2gJvItld6NA8c7IbTvYs+gM5B0p5quK8UPh5hBlDjrDbsC0iVC6kGr2kV1OaHnl2DOIoZvsTF1PwgCNWxiyOayWEWuSuFxj0ZPRcrMq0HVQxTUIe4IjBINqiQhmRwAU6FyNeyoQOPEDN8NQPraZA9ShcHBQahg8r4fRV+nWZfsxP+deZ8yUYLu1cok/y3btF0Wgaj7PiW1EZl6sJJ5t4nyDaqfSyBHLQN+eZ/seuzoJ4hD/re/XjzdqDu1u/qa9v3X0p1oJmicIHYjxKKuJpzm8l7dX3S3BhhN+b+fJ7ftM1q2KHEgteL/5oDl9ptc5YiKyRYt4KYDbhRlw3PJ3Y7vpQo5F2UEQOIhBnQFERZxZ4yfh61GijyrpSUkP7dNjrQYYj6V+G5tpzg/GdQsI6nK+qFSvKJKFiBrgxS5G6ZiwETv8oUlpMCnVGHSlek1nj3OKCjP5Ysxm9PQ57FE97I3i2+hgzVTZu3T7yCxo8YljS8tO91zwKuFkQSeVCWN6o8MnZ0Ovf3Lhd7w71v+uOy9fVJPjHKhYWVff1vJD+ePXu55INmL1n3uMombLefgjbpSr/UfKwtVMa806dlgRnclKeSs/DKEz1jt+a4iNMEWUxRElPt56xqat0aMNA2y9tV7syjlHuQCmPmSLlUhRoBluzovIBv6TYte784aAzeuQrQTKGHP5cUcQJe01ZK4ohbRI2M3v8s9LEVsljKd6kMCMEGFjBk9q49v2R5BcyfA7v1XbhtDEqK2umYt8POt99lsIlgtaFpXDoDb8uPKuMAXMj6OyNb3ZrN2cnG5XBQWe0cVbb7V18q40+7x72xvXx7f3u98P64cl3CtampXEUWMTgThBKsvtH/eMnJgpBscvHod2lW0cuAehPn+Iw9hLAbFeurhS+imZ6ZG5PQU4arChy69aOZdFfycRVb7fPkAIyDVxjHeLkyz1mQONrZ4TCCS7HrNu9ZhFQUsS3cc5NtJ1Y50bgi85+pFFr0BjqsTE7/4M7yxWAlUSxYj28HVAqTw4gC9a5cs0tSMIAo5tA/+dOvk9GUEJanHx+urV9vu9Iw0c6U1Eu6zAIYFWeN1y8KSAp5ftAK6iPObdZsLPFoSvHkR6Za7C82GmV8aQMIq38mU/jrhKLSfHFrm7unaw4szAD9rZPV9AzAhEZiNKQywyyZfrjHpZqyy8sLHA34PDtuOf28Yf28OER/19HACto+szCeS2/6w8ZtoQ8Tm2/7zlW7pR4RUJvNvqOYVVwbzCsiW/LrgkqHEfMaMJgX5mDWSWsCORhFBqY1/Vao6JQfyfXwq4IehDRAjq50eSmK9SNrhjbYOg9uMP25Ebs/XDS32O32xk9TW69oThBNNQejm8CsWrH3uTG63vDTkvIwNSkO27dPcFNoNFJ33sQKm53dDsY+jdDtzfxEXoFMdbOMBiNhm7bm9z6IzG5Wt4EOwJGaWriD2/cfqeFZ7VFC5PBeKguk796qeDWH0xGt/CL22kDA643mgw63W4wFqvqKZVyspeB1wfHOW2yqZzM30cBZNGx4qhNIWJ9AE/0A8A5uDZYFij+HENHL9B7XVS1xN/lmn77ibR0GSeT6jJVeLYiVeP+946srSpFu3v74N7BOuCrMBhWiERvItwieQV/Q9aZBkb81GEos8NENcme1/OHT3RCEuZeUkybg6TlEzX50l8BZ3MkEjspNOekE1PF0vQJFWaEGTZz4N977ZljsRjcvpi/3SetmRG/1qqTomohz5d//c77POJl0mu3bnC7xOCCqkQ3SbABxv0oLqfGvmiAXKVARDtBqo15lW/wjjbCrllW9mGAQfOGQX3IcJBeu9JoQfKO2ue1h55wsWo7QZXGIH+apYxZ+KiR/79LCQsKCyMK/3E3bBYr1S1oR2wVA95vUWsGQJqmuNEGI6ZLlSLRQ8sHDQ0w0y0Ju7Wr9BoAeYX1nAEqf7Ek4aub+Q5baYVICcIUPrXnFdYdSXVcnugfZOukGULOD1AEyHdJLxKswMI7KNhM7I+RJIDac2pFd5swK7Znpvasc1rMM4vaWsONr2RWbbLydFetFY/utNpLEUrtXSe/XrtDTDngEoxJdEDlKnfvDnMPDw+5ewgZBTk7JU+juMX8IXMNwMnQcGC4cwgW8kTGru0gZXbNmONSPmTaNGiN6UkS7H4NGBpAtHbEiZt1yvQitSMqlTb0CUxaq9q5VCx0E0l4UARfqPzTA3/ot1pu/8HvXutzWNemJC9L6K53vP691xdbByhiCKHvt9xDVCKl3o7BAEDzCZXjkZLls71Hdv9gslc5HNd1xKXORPqK0scKDsBOVr4Wc2QtK+ObS5ASHY/gmSWUjQg2XLSA/qtq1J5kJ9X6l9ONk3rp65fwDybaoZSnIrc2cGL30l27at1deqUrNbriNW4OnexpZ78PHrHdTnd/QgwN6V330Rc7s9fpr29ODvzLrdLVznd38nHvYm8/v1CqTupB4Aad/sYTIOTO/JEwctuNjWHncTwpPQ4Kw9LksfBYGHjfffHP42Tk3nWCzt0ERV/uMqhcXXYK3atD/Luxe9npFq4mvaFzgRRPN66Hrvpi+Xmy7t6NsUP9kdsDkhehyEx2Ti7z/tX95BHjy98nm+Kd47XF50P3kT9tut9TTpOHBOnlAV3ZHHKA2rnERNIrtO123O8u3mXjo3ciPpx5cDMZmsEsLDv4dHBZL1919vFk0YUN+HqyyadjGmokSUUx0aHL4qADQQWeQZAuD69jDbLcL/38FdqD6dQqvqcljD+OR+AfrizwPco8VSB54DOG1C//QkBLJf984mVWwUqA6uPiVdNda3BXhG/lJrkcz7gKeXjiOc7C+4A0gF6orDRnuIafCV12mT+6eujDDEKVn+MXmP9kxy8O9z6L7lKWKuFHMwo9Ki5YWDZ4FEPy9vKvWcwFhSzozy6O65OQty1apfAyuRFlI/w8xga2PzovGo2JkLC8Uo34qrSkUtLd0/al8DUQnUYcXNqRhpfsYeCo4OJtMgcO6UJFh29NZ17FKFxBpBuZ95D8aCa2mF55qxr5nCq5G6xOesFNsJoyEiXYMvkWgE+3r72y8kmtDFn5jHYtvZcgDTJ0MjVcqLQXuAdGoFS2nDEAlM1UwoRnYQUJ7Ugl30oob9sXI9MPGmKjbEAGtThFP8aVcRMxhC0CfMPlRAh9sAFHzBL3eE7mveFzhOYYx5Oid8+Ynhiuwp9fDXjLfie4BerocVdi0lkhUls+P32BIZiR+f80ugVm9CXW6TduO2IL7e93RhjlTBshS8w9iiHWsP01nI99KRQeoiQ11VYhkqaB7415LZr4uNHuNHdq39ynyoM7+VD8EDSLh8P9/ro/+dJ7vP9SDMatnduHyV5P/9Kqs1jCDKiFsr0NTxRBqmj+qNccepNHpDKdACbyM06ZIJhsoo95wx+2hembstIuRcMqfR/An9FBfC81BBRrhPrdEBas8fVQCAI0IQdO9sYbnQfeMFjv+jewvt/JuY/JX6I3jJrBCXHJ48Q9qZjv03JJB28vdg/EM26crqZ4Y/i4t78/WT85P5sIBWDjoxymqsx4jpHeUgGFroQdchoNII9NfavedvXTXbD18eFb/c+T3dz2zWN9e6u5fbFx+HFzl7uxwMlyL3jXj5EWEta+GD7Y0DYOTnsuljUA4+5CvK5j6o81YRelJqMWt1RDkbLUKC27ZgCCNPG3TPUtGDbASugKMsdGw5Hf9R+QzbKgwzhhVzmrvkLgDHw1eIoMmJpVvlJ5veVo1Jllq1R9ENAWteeoWc3tWRWzxJDUGIdsYJz91rgnzGxZ5dzQNv+gxICa8jbLMCusi/qhMPP5y+mX07OtA7wYz6ObLUiXeyh9CWepMAQd6YInzEGg3xxmE2Eo1p7ZRFu81edpJt49EgDdd4bj4GATSwe9ZV0c030Ww4wjsBpQk3p3lZYAXSZARlcmKidGbmUPvChYmOAMNJEfxefud0x0zDoc5VqQwPTIQwYrTFGx1fVGQ6h2A/Pz6KHvyUtRnOQtTTugsm4d802w9HhNwYV0tOACV4JiYq90h+0NTBmKhGhNNyNm0YjPJ48npNwC1H5rUkoNAy2IMIUHZW3zfQareT4rSc7C9vh4fzJwnwZuF+Tp6UZdTJR0Me+0fzBftTwdri/k2dbDfJ9yOJKeJja7GpXMaI0yEv79ycnydYsM++Y9lar/ZNTuuoGET22HSudhP9ISBI7SceuEjfSF2nIM9zZOX/V4x/WPe6dn9UO4+Mv6FgxU/ewMW6FGMG+lUvvJxgugaozGi+EqT8SunfLucdYZa4ITT+wZLVlAQUwHQgBCXN1KyIn1BkpjdlGylE6pMmJuysp9JJXWWrgo9YozrZZk2smOHkeGiIWPbqiicQzmBGtpAUAibVcukrLtaKMO3G5Lyvsj2WBDeAQq7wWTCSu8wEA1wOeUz9SuDGzYVLBkKGxs+EkkMAClR07VaWD8TawBhBtG47R+vMWdK8nENq3333baZMNWnlfVaCidvs0S28nicHWtAX/X7twjov1J5+JgNKHdCQZdpP2DN96HqIJjeL/DrwKcnQaeRIyqphpKM6zWuC0+EHp/5WvmpyurWQvQgDtVQ+wLFqurd71Hd3IsLIvJupieKQMxtao7ZcaW5IsCTzCnodY0Z9Y/gUKELY7/MmhtRLPkUhUff5YIzUNXYVS/uUE1cZMQmzUqjDn8CtvgJr3dYFWmmQSjTgv2G7cJCe9Bml0zwE/LzYPkLkc2gNyqBCGIjkLhXrCqPWHCekBNKb1VmM8EoWUNizs5RbWAclUVImqRcFcRCppQNAJGIG8tco41ZKXrQYtzUwtTkDitNi2T6jrtqqmUw0zS4yiYQUpnjVMt4DjJUmNsAPDroSXOmeBNf9SHmwBD5J331BAiX81tbabRQ5J8Q6gk1m5SQsURIqTA/krMkLJzn/YO148+S3fcfmfH65z4fDKCuayMjYFdnDl9i8QMaaqWleYMwKFsrTfEkcDdg5ukQMkClLhyecqGjPw4Ohjl+5iT7pCUk0rhM+MlayN/3Lpl8GZBHXfK4mvR/lqCYA+iRGG3e01pDyw5mJ7G94NRkUYwwMpTc+YJmPubdqp8YhtbFT2ozDsLtH0+Gz+LUQWCEqbvNzYRHjiq6bIA0Vm3C8whRriC5ipWB36h6JpRfBEsMSklaUPQsMmivCbWHOPiCyCH2mJePfMSiQGym0EPftofmIj4zMTJ0GAJ9vH86sLCghUejEuXwDBUs2GWSuChwSpn+ZBGs5SD2y3kn88YaQ371Ybfh3GeLKS8xxHp8eADnTx4zZRt9WH2lw3XkaIbYgnC7sp6YDGInR2HlLaEBhSF17tPWl+VgfW+f7qJ3kTHNMKpHlptIayRza1opWwa/bCj7EwyOcH0flBkYFZNdk61NpW2aWWEFWmaXjkk1eQJL9yGLUQqtVb4CWqoWQ+2xAzb9FpHcW6ckuRJfT7ePRm0dx67x93g5nzr9s+97UP/68VjsLf1oSL+C4Bn4Xxne9zeue22+gfch0WJGwl5mqZps7Mm2VocaNbYJ37nvR5SZy19TcIqRXsZnbqQgf9b39rZAx3/+Hx9f29DfPi49YW7WuNUPmsKJHlxnTKiQOygYkkT3hGm7sbxZOO4fri1n9KmELZXwHQ4gIA52dy+OLMw6aRubkeNE/E5n58cHaVQj5ZKNPjv+17QcclrhcRPx0L9f+LWotA1bf+gyaMDANSzD+MAC+emt4+GdORC5s5PPmBq/ATq7fRH/mRzOAabju+E0DUkM45JkNC4xoP6/t7hDqxYWE76OO7c3BSiZRcsHrSX03TWACOEb4tBzXBrxGxZUGc6KhXIF60XxDmLOZcVMgbHG/486HCuaagU5pU45wdAu770Cw+wAvOlzEUjfii1gyrdr0oaAcC1SOK7FSesm6JeEmEaWAWqAY5jFSjHrBJRxl5ghC9SPQuFyHUQfymEwRSUSQxcQPzJoqU35ziPgK2DDXZeJjMXxMFqxXlYzapaGtxZkK4gA94PKwuyQOXlX/ek8d8rZY0Cx7P3lBOligO/41YWZNzDyi8FA2Tyx29CfYN0ibr4WK3gx13xcaGIH/PiY3kRWESfJ8ZX8fEMLi2Hz9+CVqjBEzhBHJ3YjR/ByXTGPnysLqdS4HSdEV9K4nT4fAqXlvCcAtyRTt+B0wv48RA+Uj/kdfa58gQeAJSlpfAAGA0ewMeF8EAYDcq+YmLJZJ6+wc9iCFaJJ4WX37tOD7LZg2HLmEjAB7vU6QmpkeMuSRqQdy0PyGwMECsJpfP+Xd/Prz6AUPvoBW2xT5IWWqDkL+X4dpeKQF0umoKbJMRvTju9lODDkA0HhN/ApNcXU/KmQyRk4pelRAH/LYorvWt33B01hn7Xw2NRHvRnOC4aQ40xGPcSHNAFH8I8OhFKIL+ftWFQwDywwmIkE8QI4xZLzBFgb604f1cVnjJL78vKwVOY2NSLGp+1ikFtrkjuZguGW3qe4jcpYJLaYinG40SIzOd1//r6wH1Ej2gbTHaMGRNPkfTMoT1xdutlNoB2tm2FVAsFTa0QE07CB4A4kh1FmvqwnIFBz1xjduAPOyfdr73tQnP35FpoG3n3ojYG2iL4Kf910Nz59P3sYns82bvTX1KTvY2Tu68XX2/bF4/5z8Xtb+2d7n0TiJFg927vuBeP3Va+cNu8mWx8ML6leNUVyLUbScdc++epbeRKqKE3nrw0094aZRKXQite6C47rDj7TaH0T443UpPtoQcvZLsD9Y/TSpsg4G1ae4V/DZAN7+Lbl9q+u/ihvnVwfX/z+Wn3y8lWsXt/43f57dTUtmIVnNPPJInyxQNRdp35PF+GnXYeDDCt+BcwAS3k0RbPsQSDBqF9Tcj18AA1zkE1G9x2gttV2MCyNy43g2kZi6HRO3givoSNjQ0IQSAnhDuU0zylNxzMIisUI7qrkVIeU1dkrr5/slXf/NI4OT9siD1SmFulZw3fUn8QVB5xvgF9lXStcjU36gzmowFzA8PPJqIjO0ebklffgP0UMP+rWJ1SYSsGyGXBBtLh4j5od0Q8aXGkHZit8cPK39fNxNAThd1sqgMQ1MFcU4xXsc5ECtMKqlh463guJkTrlmS+cQ4uNvB6haIKfZ94qPlt9W/wHaSP+jQ/+LgwU2EX+935YfClgzsg7fwF07pNgYMsSF35ZdYBSDtefjLGoLuQcgBmtScshVBkGMe5PJnfR1s1ZURNCphpVrU0z/xjoYb8n0Y4yVL43uUUEgcwyqJxv78vBpxbJGVWrD7TMWeaVKROYR+vZV6MTEz1+kHnzu/6N09ClW5P9tq3bm8TWCRTqBs174QpXl67QbMVrknZANB2x5uAozelPb3cqwqHneKiQ2wKxim1mBAmLYAchnIcM/JYwOyxSjHskpD+UN7TUEs5GDrZw85oCNvazWXJu+pzE+QbDduE2ifw+ip7AKbg8k3FKU8ULbiHOpm85t8kU89JAnqbH0DzBlhKieYLIAIBaoPwuDwwyEsV3hPFaxBq0FVYB5TELgQ7Q6SgsKEmO25f2J39zxMqFOJgkZCJtmIn4Odc5I2DMuWYmz5GgL0iQGCWu1s2ULkxDU7xukk5RyxOZKrWavHRs1jfNANkDaf0FEYApygJQGGylMAqWjWCdsYOtGbE6co0Vpgfh1yU0rfkWK4laaoXKs/b55vH9R2OcKyK2yANzMk5N4TkxyVDo4vEL95BFQUMVndVzSPppydjgMqdmH69AibHIYnHKaPjQZfZ8f0boctQNAlCSJP60f5k3e20x+IgUASib0v8d347RshmW/r2yOOO6xpZ9Y3BG9O5a4ysAsmkSKZRVs1xnwgDH1+WWrOIK/eSpJeNiUu8mPAPBgCyAHVwjWLyfyo2+59+/jkHgKbljAmRmEFcyhIg0fQTioASl7ZWP1PpWfWzuu0qjx5ST1eJAuX0fOvzFi7+AUoqiOl3APLAHHJ8iayHbYJG51rkBddEa/COxBBkgOwzBVA+2rcK3EiV0+4M9osvp2dnWwd8TTpVAl86xNUs5xzns+WnyBOruo/M8MA4pvOQA7qndE6tJHJbjPsDt3XnKPU+7cidrvxMVYBxOr6EmT0+PgV1oK0QKYWSQkCEqwL9lWG/5G79S30/s3WWIfAD0cue3qKDG1m8uaWaXMrqdh7g4aReV3s+8dwusJs52b430odtbj1hD24M3dbqHePoCpjwZNeDsiPnlLMzlaGPW0ETuxgKHzC46MD326hLmSnVaZsFGdyyPR8nDInstKrPLZuxQCQRZzL3o8iBVIPF1hv6608jAFRkcT7tbYJjFtFLJi+/uBidnZBlj7xy5rxaSSYhYyW7InYq8dPDLZJzvtusn9Xfwz5QWOHpQ3YyEVJggcy35CIu0Fk4o0rF+YWKUQMGfwvSjQY012jgG3Dmsj8WIBVxocqUZqz+liUTAGZJjLuUkA64SJQTGUc5ejfdIfz4wXP7GiRUoGJDtQgYCxUh8NBAiHACHybIkBy1MchnYZ3KUTCVKgSzXBVVweAGRDkeprVlnWu0JewdSkd+s3m0cfbleAsVcbH8Lv/CVNT5BbAQQr7Eej94QJGVWv1CcXbw5DGN/kR8GHpAdpb+c+wFmJsFyi8Rz08OPYCgCl2mT+esu/07gELw0KHtu/CKoftPBu1/ZbROW/6o405O1k8nu6frG5Mzt38zhKHY6Iw6Qpr4OAHiR2hCzgu0SmGk5DAtcMBbaC/BjcOOIrlgAeLIwLp1Tl0pT+qpmyZ/gJAoQbqMMAQm5BTy5ZAa/xuL0PgS7ofn+/uMjf+N0HCwBaD42fQeKZ0/i3k3+mcGX0mdAtN40CUmhMHXQmGxkTo9X/+wtXFmK2SgFW9MPq1/mmx8+jQ5FprBPqydCcDtjtFWnOxgEa0v8Bc0gmQgq+ztQU1NjPKcHB3QgGRZQwOtMcSACNovutWlCpNCaCBtc7tb9c2tk1O7b7JKET0zPRcVWCqFzaI/sK5D1qDa5uwZvH4oPt8ioy7owtBH+K8n+utP3FRbRocRGyzTvRjlgutRXkFlHlf59LbZLdxFqOJSV0xEDgqfHlpwDd63qcQ30e9+E4t5+KQj3noLdwJdpG2W9fdsq5WZU/zg3uNg0rpvif/uJ0HQn9w3maI8hXY+grvmTI0fSpmVWQvBfCIEx+6eHezj6oOXgB80SRXbUuh9re9vA6f62Vb9gJuAXadigUmc7JLQc2DVrO/XNz6eYz7NWzyIa8n2FmECEaG6GoOh/4jFUiPAFt1/cZoM0OgB/eiNOneo4rm3btMdzRx4/W9uGxJw1uuHO6f1M2M5Uq5RMc4ZXaXUnRNPPjQVurVjm2hJ0PnlPFO1gg2OJUGduIAa9RvPkUY6dwXj/nnMpZHRA9FKw/0GrnALE1JILbNCV5HFhOQV4VAerIE8iyCrxjLfVDJIvxNtU9xkgm8kBW/vHF1kYOoPJei/+TQhuQbSBjaWXRdqF4A+xmIPgtG7c0A86KWG/C/fjIJG+RCK1hA9jvNYyk/yKfpYLU6a/HGhNnmSH8uTEX8sibYnJXl2e9KTxwsTiHDzcW/S18c7YvC0CoH5S2ULKn3gos6GM3zH84c3FAUnAm44tnW4d3S6R9dXoyqnEZQeOtmNISQFXi6MrkyNMTTtMY0GXesxSF4ysSA3k+gNGiGw9vWglyFbJAOpCCxSHJ4cc0yAPkXVlDENAC6o8yB3GtKuJqDeswbG+WTRpiQYNUv8EPxAiOoq5aeStomFS54RVgWUrWhhkHS2iAFjwj8YDe65I8pmR1nIDkOMsU0eBhkuiiGeAb7RYSsAl3Imq6kfZX5wwgoQasbMGvvBGzOKXvEELd+/6+h4BJV44KdWkNsY6xezwQjeFvJwg3QmYW54VSILJA5QZWDfFJ4KRBYXl8K7FU3AIZHlPH4OzveCoP41taRVbCJ6LRh5cWJPKxmSArOU0OtF/GwxySs+MwSgjBRGT4CohG7XfxDW9r7oUmDMtznNTjN6RBWVSysNHacvfoM6YphrBz/bvwIkLU01GmSDi9oFkZZ2H/Fax/QTBoOQnYmkOL3gpIWcDNLvuVW3BaVVuTkAdimmm5Ik8jHw1+DVEN3MoneDlS3Mo6rh5j9eBR/u4+CyW7iCJNqOmUQhe1/MG1Dx2XofZEa/cVmS14mvnRG0Y24pfCvcNZBCMzJlAr91Z2FPxu0BLnoNEyR1uiSry4Gox0wgYXJYoGdNsJi2K5wF4u12/WGHxosFPSZfATHdu9sCK+jrTxMyVGVkKx125ImfEb3TEEuPuJHSwgrgBhfZjRgnZ3d2wVeOOOHT1V1I6jUhQCFRW1sOszFuYG6zTEJHTX7z0i1f3dKAZ48DuwnMP4oGBB+kH94qR4GPvLm6CWk+p5YXZKEQ489Hz8L8VHpgINEQ4t+ZEzMShQEcaBCHQ8oqKv5WBzyCh84oVGVP6ZItF192OIi2xJ0QX+djZU+cN/Ff0YciVx4PDHHYRlmoOGBSkvT6e0dLS6diinsjYj2v1QjCq/Km9VKYSKLBFBscH3oLTrY0/i7UvPlC0TTQMPMqzC9IKk+N8Cj5Z842mjvcOtve3/s8Oa5/Oa7vT3YO6nv76Cn+Ut89OprUD+pfjw5TEsfgtA2attMvx7tii8lK7yFlbQGoLmwCcszEsgLXVqcj3Q2kw0/TYos4XHvdw72T88eDva3to7N85evZee3T5Dx/u3229XDz53nl+KzbPTwrfIIan58OzjbW18/uakcX54XjlJFXyKF2fhzyzdhRGYz8pKHv4ZSiCSE04tBfAJSifbiAcsrmTlmRG3bIsmT1uEjqsQGXpf2gUH2G5IqUhUpm/lkZPV6TbvaS4r+/o6VWUPutaAl0gyLhyInmHoVpUWKZ4eRLVVlNbM3IicSOsjicBaa+IcKHof+WBSRfIAok3qJWJBh5HQgfzpAFAqTn6S4INSELkyYAboEIdUF+ZSlYxVGpUKxKhrA480CoTJaTB+Knk3bHS0WzZeBXIdJpzVG8Dh38JN9Uil76zBsKdQxUMZo/q3LuIFTCUqpPURjDXkiXgmp90rrtRFRpzK5bnMKAg2QUjCkTHRzIFUG1N+LCoG13eAfkHixN6R6YfFeEirooGAdjUymLC6gZaqDMEFY4JDMvdx5nWYymh/ptqLonLzdHM/AEnOxguAt4FhNkaU2T48A7I10xQyVC2Esu8+nYO1CRAADKEgShCPSnw/HIOzbceuQGWZeH49XcedtwVwL6MpS8YiCSnuUC0rEiuugymbxaeR+yZCAQ8tjrDgctE3cdvTQhLsX1x4+G2w0oaz/1vZmFZxpC5mw0PtX3z4UCjqYDCynAsJVNhP7bDcD3odvW+wRcdkqtisn5eqnA8s9iyfw8KngYkXo8D7XQK0ZLWuMhOXlWSIYqTURvqBiWt9G4odwjGbWLtWZw6svdqeWD1ZG8Amq1ksIEL6mQNob4VqzBMbI3MT/FVqgxT6CWt5TqUAeAildlyKOE59Ery3AwC5BVlB6XktcouHUgqVxaWGnnVMqMNHN36BkJez4ErgoVWH1LVH5Mny9EAdJlpfca9YPG7t7m5tah7B1apDI+iBl7QiouJTCrCQdbmn78BCAzeqNOT1MPcB07vRAMgRFfxWcOKTYcrCopX4x4JyU4ZXKZvPnuBAlAriNIA5lt6Ae8xMBsiJ+KMoIpR1WCrm1OVk6/P2pejwOmPAzSg6F/3zG8jNtHx0fcClI6CLErzdZzZVWBvt5mA4mBHmITHI3geOtO/rADX7/DHzUvpIGBCYW4aszBUUaerAAwOZ6cTk4mGykkcy0/8+bYaum9MXwpEDfYhtYsOBIPtRcHzwDzeXTn3/gNVVDxyjDQKpSeAy53x+SHEQYGc9LMGQadrO+mMBcYtpAZjNUQGu4NlKg/JdYeWvuSYDaasvjW3rTk+62ZuKlpi+a2E4i9HvfaTMtpos5eJarsppj+W42z3b3TBiVEcWoBFXADlzJPfYoVzMpK2avksEFFGHPg9Pw2zVcTBXEhkYPgSdUiOPx42jZZ1us9XGr5dONk7/gMl6KR6GUm6zk2P9HLd4lU04GwA7sUaoUYhXq61sw16dgeMkkxUQJR1axpdVJg70+/oOOxi0B7w+bCBuT8r+9eitCQ8GExZYmfparMvzUvdWGpcEKcEJ5q5IoSOjun2KcaQFKldLJwaonpIFEoOYm8CO98c8xMJHZ5dzBzheV5DN0DIZla2+C0zQrzRTWQ191wgN9OwGO9OGnditdVEO8nhZbzBboywP3PvxSqTmqCzvAUG0izsO9ZFlIIx8VTooKVlQrGD8BeQCf8Rpk5hSK1cg0cUMrKKjATUZl+NH6Kq1pUNJYBRHrBFpjA9E/xXVfJuyiVfkzaLEFc9Cdq2GwjtJXLrVD9ZFEjSFx4bV4tM4lWCJV8MBdlCZfNl972969nX/KfiyfdL8Xa3aRVPC8ebtfuvl4c3n/dOfcnXz4fdg+/nVx/3fn0rSnOUn6NGjbw9fOH7tdC7enr53VISex+3Ghff85//XC29el68vWiTQWodra/fT296X8u1HZOzx+3J+Jg/3PpQ7e183j7pXjufyjUrk/uugcnn2rXUw1uzE8tQvTIWAoHJ2KiKp73y7/eSZb3FzCfRBti8DxQBhVU+2V9Fpan2wz8rtD3YTz9e2943fUf+KfbTrvt9SnnvnNzO+LDSHj70GmPbuUB4mJgeFxJrRppzcoHqyrBR4UhoE9isnn9e3pnBiSa5Re5WfL4Emzcob0eMO3M6bPCmVPBYnNxQIqDQimoBNfgITXtAjwOCzGUGXDj+Z3ByPcJkcyvMZHUxAIl5MRSNfZMIfQD1zaYa2GWO7ijImMKJQzWVH0yJzuna8L+YMohB7QX5+FRLScUwbPEzi3jqivi2X6YJz3zlirznhp33tO8M0tZ7+BTGeI7n+bdwBxccJ7eDT0MwRsobQe5ytYIDprXhAVYw/DaJ3HCzWA6RD4c9c/JcSTFch2KivQ7/W8uFqkVC6LbAVycZK3w3KF4WG8eOkjAULEH+vr3BwwwYWCE+Z+KeVJILFoVfHbIZw1W92VBwrlwzSh73+PdMKX2vytphwnBafrZI4fspI1XW+MFsy1wbPgyWGjfRhzAwBmy2l6Z+p983pJJAFmkOpiYkTQ7p4La9jJLGpFv0ybtCsGB4BKQUCa8wdY7yE8R1uu4mji5GtBrp7I+njkgBO3bEbteu8Jra54N1FnDmUc3IlIOnmhFlYCsfVHC6ih0o1YHGiuXZwsYrTnO7IIBsvHxsrR1dcJNITrCSqI4P4OYwAFaJJVA+rbgawfvwREfy8VVpGqVi2IKynIJxL1sl1+KKmChqUcv23uEm7TUREBnZUrxQUrZVHlBqzBf7wtsE6G0GnT487jX5osWU8UL6TnFGIeSUeLLFMQ8WhUJoxwP2mJdBGtibYO98HDrDwH/E0VhB5KMvEi1JrHA9myzC6GGNlB4tmVqhe2xqoHP4Vkq3+xnDMlq1L6RMH4uon0bSSqq+JA8vyC9YlWL+5SN1XV/FNejovKDyD2hiInLzCxjkT2oeD6qmpXnC5i5CNw9RY66E4iryR8pYL0q907HYKySzDJFzBCOsHAwB1D+mTkl0luPo6FLHEBpFSTRqaDCtrsVc2HY5UZR7i9WXp8DzwZ0EQs+TrWZIjEWNTMruCubTEyxkxkXk9CjDevdURXtTOlNQa4iZhxXrThkj8iXg5CrG+VKEx0d6MxoiBNQ7ggx88CNYW5EXJFOyw+PWuNxiLCHfSjiDWtpRhofv8dC0ULZ/vYGbXvRib+yUmRKkInNSQYwEvFeiw9ec+IT/DKLbpPqc45VvRShIyHYqGJr0ZaC4DYzDFzLVU7CQrKXAh5OrGe313EkYOWNvBqoiKQLDJSt37nySyVyL2jlRswJpcOjRvrYG845jjNJIemSeBBQbwpQEGIohpBE4Hvxctvvc23vPtcfd7vqJaOxtxChqjSUscsUK5kWcQoomKBtauKyeHGLYkGF5B3TB8tmKe6mQpIOAddF8ZbuRLP9iCnKbJ8ktxS9ix1jMnvQH/PchB1Lm1R+02YIjEQo7fXFA6TymcXYUKzcTM8DC5/KGOrt31C7jfAQoHAv8MSO/EApPPyl3moJycDsZnxvxGgIScL5JFHeKUMR4Z5oFwh3oxCuaS3vmt70W9bdqpyUYiQbQ4yY+3e6twMRO2CD0ZcsKGTHT6o+Cl2t27kXciwQ81NYs11vzZV2hu0A1xfdd4Q9J2QgjstQUTvHUEab7HFFLngJvA9vMhkQV+49+mqvCRwp+avnIBMiJdtaUkzQIdj20dmuUIov9g43jy5OOX5OsfOdo6Od/a3Jwd7GydHp0fYZhNWRkskaKdlW2mQyXUnsbB1undTPjk4A1iPrFSU0Rw3K1PXu2Pvkt11+MNxZKotGgtn0sTCTk184KxkFuU8MniS1vmG3ycn6a+GpFKRjJi9mYNu4xX2Q4eTrVjiWfdQsF86unuCDeT0iDiHGKlM12CL+gQaeJBdUkcV0JI1EChlHmclQJiS1Kp1JWrQUDbyJrKDyDiTXEtViRkHcbA3Fa3Kb7PgtFnUBnXAUS93d5MtkY1RWJGgY4bd/wtvIPMvEjG8E+qVDKybJ0rqh1kjhdEThSMyDkIqwZhq3Et9mJ9zTyNHAUoBqNZVWuW9h7IQZSWSZirnatqTZcYdN9ICkSZdOS+y7HOwyp3ZJlAxLpX3/zu957Y7L/kdK6NrrgwcVHvVC5yLKm1cku44jwU+e4sznMhc9VxWBgPUME/bkXlhLoHlwK1Vp3Rsi/umkA5MYFj+opqi1eCVhuowWrhBk2++0OO4zxNlqYwYuUNU4xUvL9avw73xjxK2BcItmxOGC+mSYanDP4aULTS3jC7DNNExLxlLce31U1rZWJWqcUptER8cjMCNBCxa/KYQgPBHqYVJ4RwAV8MM7PfjRqi1FylEuTS+b/Mqa1oaKy9Psjz+cIFzXJ2Y9cHiq1f3aGZgpyyE+h0h8BiVoMfPeI7tAriVsFn7LW4kQWlVgi03lGxtAwdPK1fGX88vK6RVa12Lc9/bRLgc0yuVHYccXildO2359mG8cS7RDMuGt3M9s+CCWVHM2xZ96F35ANofLwt5V/xsa2iPO0Cti3nHRJErVxBggaEd+2MqWeKS1SOToYopTd2rOFrp4KDMOyQKN1Ktf2M4wEY52tdb9fXEVc04iG9uqjEMp2xonA48ChUNKMVz6fLuj6+tOyytVK5OTg53JeqfbxWzsnvtdGBQsLl9IXtd1sPkk0FPME2ROEZVie/AC5CU2LFvDJRv3MjRqHm2bBTP2owEGkTLnJZq/quS5eO2GDKcU5vzCdDMdKjxk6sCYF8FwzzaH/gNlnxCaW6iHpNnrzPQXnDnqyQBM73cn6J+dPEEmfMp6SPVy46Yej5ouRABNS3Y2+WqFaT8MVidHRCWPwEn9uu1XTan4OAaTyANjWEBiheQQVpRjEteXpJsU9+XMuMnQ+3vcGWJWe6SgLmS6oMNndbg6afqjYOL2Rx34kDIK7K4uE68ojQEXq7HRlq/DzhrT1BxVotd5h350SGQAxDsLeKrVWosUdPrPn9DSbdJQhxuiF+xWWcybfrs3eH+btVrOi8gyiBQTKmIefJESoF4Z6cFG2UGIWhij8GVUSdiDA0S1JeYTbsIiWZyjC+fxXtkE78oJJ3vTa0saqsSXTD/ThoQHPGEtesLuUmcp4BOcfsKMe0ycWfmuFmViiHRehyswRdFXNDBIvnjHtfS0F58IcukzwRjMWGus3ye8aeBUYOS9QTQecW1FoxEK3yKfjmIxnMd9+tQDRYbQUetPA9u37bR/lJ9ZD3zH8WRm5+C8K+LA6QjdGLUzdeUeb6CoHIHmRTfHnH+kpA4GkKclKzUpw1js6eOuD4/8nrVamcX5FhYMHXqXg6t550cCAIjQm9WIsXwbrvENvy9s7NE6MiyotHuZ2KBdFpJUqIiJ/IW85fi/5YqCby/fXKXNghzuuN0BSIDNdajkhPOQdrL3fssd+j7HEbVPDCTM0O/C08tHiS+mGeUf0PCmk8mphDdVnw1LV6ZClTkGOYU53fZPxRGSKD50CL8zifdL0y5l6Yykw1IUKgY+KbadaOwgvvmp/toij11ZUijMKYmhBExoC4KRF8qqLPBjqAAsaR8mbipUmEKlazgPoo3MVXpNfXKysnRpVdb6TPG0qkoeEO4kQQOpLslcyx05uiZtFvOmaL+kwpzpzIk8APaNgRTu33gt767h8aatQmlzjH6BksTgrf/G5mdHUp3hbccqN6NQ0VFvtxc4WSg87lFGyAAKveXAVXcuBO634JofAfHuFvcKLzbMGkL18hMWHnWAi5gQ8emdodtsekMEx2z+gNj486T5BP0+O3Sym8J2OTn6wtVSimWCvAstUyKgjT04B5T1Ml6CFRX01sK2PppvWMRNzuI1FZTCJgCarRVBgO3rRuTkLOj5qjIJuHtUAFCZArh9TKgshoPGxixQhlhQIXhRxbZ732l/8z3HCKJLSAFeMic1ftnveBxjVeIYnQLFfWlfnTN5vMku49NKfBr3H21NIMP9CVs7YEgeQRkaPIw7JopdDoy0ARotuf1P564Kl2jX2I81mVIXI37sLmBj+C79h74EyGHDJoo+UltZS0UaAOIgAKuT8snEbLHzyaw5iXtOFWb3xaXYx9rHQ2Onm3yAq38Unntd2DknGAy+gcb6mEQsfkxFQ8bHxtcH3B3h1hsYKOrfqDAy9xZD9sVIhWXDgzUn9hnKjWHiFDO2XmQkyrMTaPD1lHnF8+UhbRuYaT2WCimmMIrgzZaIFkfhqZ2ck0M3adrJCaES5IJr75GqxeWcVewmTVGeB/ysaGXD0mItKn2ZJX39OWdh4llnYmUJdOgjSmadbHKNkifMaZ1w1upEZ6hOTkTPex4AelMKIvf8/gfXudI3dAo5nV7qYH7FMyEcKFR8TUFQ8Jtgoa5sC94iTEeHbFyS75wDQtAIRkZglc85ouTjZ4D6PekMCE2V5c3CjZkfOLejWJHkjs5s66ZD4UfsPASyWGVWIZKH0TW/LyraILXo/EKlokcdt84CgUKIERWLLpiKApVoI/qE5BSQPof2ChQWIlxLewL+Q9ReCXGxe3BloLyNwCVSO4QYVxtC93ZSDegIcRtIlw3MtIMtcgLw5VUZvIvzoIXdZxJcwf4zjbEw4A6BwUw6y6ZwnIZkZocxr6JJtYjEigbRInIspngh0RLNc73LaQBeIgycJiOZxs9Cf5ucbCUeINxVsU7FrMF2Mae7llLCmoKzptEGO7rzYk6UkANs0wuZcEX+TTGj+8a+oR53keWRYakiKktC96jjipeoPRmEMX1szFQkasFw/A5drOm2128JVYMJHMB5rIP0aDoKSU8/t4BV8sFVRW6KyDIB8XmLsnD4Hi0gSDt/e7izC1oarj2kAp2javZZrl+v3OUbQ5jswa2ep1SBNsKCFQkVzJk5aUXgdlXvm+by8WQHdP/JiSG8xTuIiWApv58ZoV5miaRdbWbhm1kWVqT+b0oMPhJQWikOYbfU2qrU3pGIt0L5SLFafkmjPURbxLarZAvbxiYDdLGqOCV/AiYRDWccoTivIBqKQCGSFAAxBj1MahiySkslbWta9Pzxm7h1tbr8x2+1BfEH+ObF3zx8LhXxQAEPFOWBagW/1/Av/lYo42c4XqthQ5PFauzP1AwwL9N2VCRUOJqXuvKaEGQQnPfHo8u/VjDCJbZ1cZq0m+U710iqaklGZZTMKD+nVsAjpKLHhRWA2CkBFgI/dTVQ9Q+sKV3KP/8htAEAmRb+4OmKX6xNHJknMHtotu9Did0wAhxBsJeFztX13WXZ1bEd3lq4FTSVCsTOIfQFczMSyrU7FEohUCk9uf//5t6ErW1k6xr9K5100lgYPMgDOEAcAmToTkIOkE53W7RfeQCceDqWzRDk/35rTzVIMkn3d9/v3uc5J21sWZZKVbv2sPZaXrMYPInsnQlkuK40vhJw+4MxQBoIBVWm/4A12hNIDB6EYDyyy3wNaRTAb4j2+4YenlOUyvNXtlJfSYiJ47rAyEUsAqabKmiYYQtZdL7E3PeQSK5/6N/gTkpZV3XSSr0GZWNEgJomH4oGms9G0WXM2egYOJ1jdsrtsyKu65l8Y6Ep4gwXX79JbUxkLMVC66+kLLMYaMlha7JcClZd0LOPBA8+zJYXP6ZcmWzy5NypRqHB2oGsaNBbdxha2CuI5jMturvSjKqvvDk7+4hChu3910cfzjS+VgSzHujFgV1cr7BlYOc77F/HXCnSud6FvR5vPUhWgaEvzR+wCIiw/9w/UytXOdE2wJSRWITTIFshlAVqqYbR3VhnpbCosKYFDBPiHAUdDhfDcTi8Q9VAFfMHBSGE4MvTZJvIrI72Cocjm0+dwVYI8l6mRGvU4FNhC0j5kMmWH9dzIkTSELPn5uZ5yRDnxLYDqDg1mrn5uYUKDwmOsSstqrAZ7HI76Kw/DLKSYVBzn/UvgoyUXoSNwUEhDHvd3jgojAbd2SSaXMwnSFO4OaXsSV8F0ixHWLxQcX8XlcuUJVQLuKD+wvvkm6kIdkImSgblULJyu/Q2nxOIH2Bi5jt6BqZm3+qTiiy1zfLk+sErisaMvS5vPr/sj2VXl61FXZ8yqv0ZbMbnLqUpfDgNZ1F/gBEJstVkw5m0QjeMF8ovbQRVbTyIhGO7ksnE+yJLgggTBub2inRGeh+iXG6ES9LnpOrgrluEk8fKFeJ4abS3JOA3n18AnO/tIST6clbtC8k3yhD6YMfbyEQWENi+yGQFASYsa7RIa7wVRGBc4VoFBoO8NEv4YHmeN6hfp7JjcUhtBG6mVMODSeHZ0Q960wr758Dn2wuYag7AT18mIPty9PXrFfzQx8kwjF+GhN/qf30zYZUMxjURuUUqt5FfkZjCWZBLBlQcc2nWeg44bNzBC4xK1h47HRKecmk4pt7SW7BbHfsX7f1RApOUzJjRou2gFBHpqe6ZpwELCZodZ9f9WTskOtnc+3AwfvYM+w9P//PuFD8ErVx5Vvo8/i/qtOrfwXQPx6CQaH9N1uPYUdyiLbj67PF00RkiKseKy2RlRxqH4qC60lj+f7/uDOIkXZ/mGDfqDy9gLE7Di/5HpshEcHvRnbXOtM6gl8jI8fNER7aQMtST7C5ccAeKwLbWe7AVoMVStbywMkqdeGAPXam2clM0hYfVPlbUsf4KYhYTh0pdgfg+KCmUZFD7bu+d5Da096nmEjqgP+J9HnLxAvM+BfH5ijII6IY+4IKi1EjObE54ldhDazAvFrdxxSr7uMkBI2LJEa5+nphShEbVbAczoyE76WJSvqSNgoeBzp/aKyvpLersqbOQ5senqWINgTsmURuh4DIsdluIZHQc1hi+t4rQvMgW3nzGGT4NwMqp3+3e9Khni5YKIg4gOUqAglk8m6ipGPf6wz40GRQI2RQDzZ+sjrgJ/xMGWju/nE81za+atibyatLA6y59H2aOWjl5za/lI7FHxWm42B9Pxq8pD5U/uBxsnvWH4/6cD8ctNF055ajfskJZIggWZQAfafF1qI8Z3un2RvrIkrGVrGWT6b3RfeUaWbmeiayENXAniXKrqo3kGS6d/Kfxb+MJqOFh+up0MJqiccGcrAuGQ3aMsp+UpH0quFhbyEvtKgmtEgqLYIuDve57mVLL5wiWTTNS9kDhBrOdwr4Y5reanRpoAWyJOOxN+ruik001S1rDkvjO2RgN9K7ycJylVYwSFohosN0533r29D1Cd2t5FrDSCU5Ko+G+MoZMWIRsL9IKCXWaFik0sDkoQUzyYv/k9e9wAkH9WjiHlSAH9MlOJt0Fnxz3iVqqw8UC8HzPZ1gB3rOhVLksSiw2ZXnOToz7l+EsjLmdwaANcips4PfacDI6XG0B4Gy2Tf2qHFToo1b367k6znykBnQ+GU7QdVLX6a9cv0g00VjBPQazAlkzmLEQCjnWuB+G2L5wcBUCahQien4E5ArTg1j38lriwUcmiXI9pXv0oFRqwSuIQGrfEUcVhj1w77GYlABWgPOaBC3Z4E402qQlAR8ZUFIzmXRtVBMJLZpxh4dqM/uM9mT5dgyaTQaf3iBjrIy3lfIhMIRmDdFDwM8mDUjU1ZQgp8JEL3ZQ00BO2j4+BbrBCvvwRDzgJ2VtGQIMp68z7I/0NhPcRg/1y5paXMCaNk7t1W5ykHs1tJ066JKQDD5arrhtICKEj8oS0W2obZH23aWQaFm3jMlGCMqKyADhIuBvN2nDkWALe/m3dWopIOoGIBk/ACQSbQk3zQDNJbrRiHh62TmHilvr3fC8Hw1BKFINmvoQxlCfiH8BDH3VT1oXLKN0Wn8P+O4tRnaaMRXSu666FZcMeSjt+J5d9d8jXhsSsXdnmISthucjPh2x+btmjkb8RRZg+rthXYwVDdr1tJzT+Q4E4S2tUIYAUG40q1qdU4e0hOlhMN1EteQS3VawcZ2S5hjavg/Hyt+cbT4Pez0uWtS2rCAG8frrJ7UtIoHa4bOAkdmCRoyroMc9W2dXA6gqf+53IiwJ5q9C+LvTB6LhvNd8w9g22iugv7ePlTvgUQykbFUhNeu6oQjPovRWXuXkFh9s0bpUCNjpgDEE03lSIIGEqokg4R/DYmXI4jJ44CDFJz+T/KOoRouvF/HEDojvcPPoGnuY2Q+TuVcXMqwnkwUG+xq1rWZNrzeJnKIUC/C0EioPwHEFG0UU5IM8n5hgTEifr/tHEceW2aiJooREuUqcy82VhXZl1IHrJ+y1QfDYEmJEyCFHZJfYjwaDzbswJSb52rZ572P8pTWMbdBobvdlTHUyi0W1Mqv+xS9R8abfgZxwVKQ6X9HUcirYBu77ZWzWM30Ter/KPUqnJzmBc0jeoU2UmU45wE/pgjPosaKRnt70+BUih3TxuVyDjBjXOMEbZpvzt+iPEKU+1vATuaUVNX2PHEe6WWwVR951CH4/DiFLEa/q3UfimdNuOAJvgqDzAkI6DYfhlRwiDQ78E2XZk3mZv1aPdzLBg6vL93dvyP3YLcLH/BVxdqxGm/3q+bv32Ep+WjsHKow30GZz3Ro8On/H53rTejQ472GbOVzY6yvk0YhqKJEAg7Z1hr1PUBUbGWuBndSuQNsbg/rQxiYoFLEruTw8/wD/dK/gX1mY1G3cSDXUUthwz1mJILitbsWvWTGi2o3fycuL+Jj5napQ+sQRt5+7pzyC5cN9z+romAoRpJpNmJa2LfZiF9GZLdh9E6YX3xDYpHo92+GkQklD137378ZhUJjO5KkevH7LEa81zOTyQP570ml7GcABh3c50HnqAs5NMB5qat8h4TeLGgRidCxa6stvWBrUGp+O7HhGdtp3tJyNAbM4FhPsY1wy6I977e6wH471W6SALtMe3R1nDu8PlZMzu1pAQV+NkIF18zeoqQ/Z9tW2NxiO78JgJSMq7kmU7NBZXC5d9mrKF3us5ZWxnGb+xFvs9WZ3YSg3Lpes25TT3MOJX33SVh5f0ewnP5ps5lM0LwDRhq/g7XPZr+zLvMm384TmswhtravFNmXEn+8yDgxlZVKaDAQzAV5oNTE3EJvW+lu9eU/AOLkjiciIvg0epBp0VNmGrq7uVTgzTG5q2/QD5kkzpdRcT4WuI8wXFgTUzXi23WJQJgde7QxtDsev0fgTHJKnWsvYCeVSh5sXAAFKX7KVj066/LQHV9RIV9krz6vLkNdi9rFF252cnz9u7vfA2KGOCV4zKTPxN9DlU5viaDK4C8fbNip47fG5XTpX109p3Jhq8Tk+A+b/yvWkhWTaylxCszfIKZ9CCsE0YJRv8c6bjF970dQ6ZFlDQXUTrEqZ+Iylg2WLTELdYmXDum0GvdknBdwFn3VPWC0YEgTeNLrTfJ/VnaSetYVHDJI65eb2JxcXyqbE4XyudsQYujjkaSHYcCuFYsdy3tli9nUQveFIF9S4/yLD8u4df7vOT858+0t4g2v/BnR8lgX+r32JjruPncuYh3lCC+3ayE/R6vkoHtf6kXbF+CNZrWTcIEdKSlBwHTcIB/WXBIQy7+u5QXxxapxbTE9kCwJnWx03Begxt0SFepahY/rCUS3JYXu3ugblcU6pV9Uqu4TxjS67JPJhiRAcKWFeKa/5yCIksHCNPpw1Y/tJ6GasKiz3u+1wONRXTUkf4EPSEKoKdidD3jKTgNL5ITuvgcSclGexG+JHwkHNyqP8GxirVtJEsbwQrV5AV3Cdq0hwqhUvitJWp55yqzvrfIXxr23FjUZcbWx5gTwElgNM5Mcq1PKcSqjkSY5HuqZo3VFmJqZaA7jDwrfIhCsQGaivPgAT2dH84QnETkDZLIs/DndH6hCK1lOZaGnZWE1KlvVlvuWK4EpyHIfLPrKC5T+hzFU2ylxpfn5zP3QvrjB4dp88A4ErFcOok+QkNlx9w0V0hZ4T30xVOz9pQpoKIEoDOwOSSmKDLX76C3TOM2pG3UBuhMK/T38xUJqkAT39yPqCFezprbrg3DsS0ctTCRWjgNJtWT32QHu1FfJq0+WCf6veHrkleYJHlCVNY7KgRkuEhECzGqWNSJOlJ7ZBT227FFerFW9D41obVnTJVUIorgPNJVVK+Ya3Mjs88lalIPsJcRLRxMxNZyLZfXbYMrp66eCjfobPGiMQXrDrqQXblBXbdL4D5WpJ8+0I1YL01v4Vg2nhe8VNazuVIzS1oH+Wmct8ukhiA7m4R0CQ8zTIp82xOvH+qD8bkFLoEbWmxJ/VikcVtHB2OYlBWVb9MbnA2BwPlrtopKpzp3Qm3u7z100jMJ3Y+6tS4UkVcmrLw1lQOIVI+T0E3u/baMmrS0DSzOAd4IJTXkG8DV1F7yAY72Iv0bDMFozksCHgGC3Go5A0U9TdRMoYKTNN78B8fb//sX369q8jxzZZPTZmy8XLFPRkrz+bBRZOCJObS+2iqJFTq6nHbxvbh5YrRTpYqWrhwRXtp6v7yVKW13c0ER3L+7C6vUloFewCsZ/SWJz2m5dxZzSNLwcX8XR8GQ+6E83LxgNyY3414atovFcF23xdUbNR2KPkqBps9VPX+hljSECOKKlG8imQVLQMHEB2WcThJdCM8pxNT+/AUyymW01QfBPIPOGaHzc512KNd3TYnTyoZXPSJsf+fgo5YL3H//Ct1thVTuw0VXTVJ52B8wbEBZwGwywvn6OuLaybim3/8f5d20nF5vYFiGEThTYaydJV4FSB1GmSk8lKDacUJnLsDI96QU8FTCE7bHoyEeGtmY2+8uGqTpCqgoPx5BrfgX+uIbnB97ols2v36MPhDpddHKqyl/1wbkIn084ePOFTbKdiWTVJds/lLvbHg2DTSbRQT2mlnEAcaCkA4XDHG6SkTlD4nk+VQ026oHA5mF8tOqlMtiRs0dNiiFf8b1XtPChNY0NAYt/f01icHVlPEEfSbWMnKQXgzedZnZkIBPGRwp/OWDYzw7xB82JffujSivq/cbItthRbIiGhlMnQvmjf4O0jKtQYox/YxdUbDjA7RzyUsIPCZ7xlYQ+qu9/B3nMzdtpm4a31E/g3f4av+cu+0y+dynsSmMJubkvUYBNSWrAMdVt/DiyLZ8EH4TmhE8CFmt3Af+70w7I4L3NFGEY0sTvUO6k2KZ/NdY1oQSGbTbgdNUa7Yeunc8SI71kbkbIKY9GfFRQVcsBgdGVZAbWa6EjIgXjgqQRjXF712nK3GD5XJ1dGQP3j3ZchUQUHlykp8n/rl3dh8Vwp8zSNe4PrGDLkcX8UA0Qp7sTRfDYZX3oyParSQJXUxqQMKoljOqC+ONFq0re1clYQN8J93egek+jG6Qpc/V2H98ioCwDMKH/e3HO8dQIs8F1hut7pMVAeYReCr0M4wfEMXn6aMrvMMeL/6aCvg/Elgbq0eeVz1hN5J3KUXk46d1eDr+FsYbmQ+7N+yJ9zZFQTRaUsfRuTrQ4I2FnTs12mtoVGHPVqbaSi13eLEOitlVJ8tErrDoHQsD++nF8ZL+How+9Ul1Gu+cmf7dOzk7cfXlPdJeDABEaxJH/Y26Pt9j9wnh2x+vWStWazFbKsN/BsMNn4LOpTS10v8LR8ahZEUPw27AstuyzQ1RJQgwEg79Vk1kEpjcBu0KEziRfGEVGap9Y2QCn6pvZ4MeqQZJTYMQ3+gfvM4oalK9Z658ETQjOj/4wFyMfCpHZwNRiHBabLyb+cjDq7wRPQ2+nPeNLWk6ltmpSfkJuv9fYd0QsfVc5PxB3AjkrEX8CiPeuHo/j0v4uwZ1t1PsspIzTzB7MwAoIxK3aQu8CsDBBeRgjGAlLOyXTu4mPNPLBl/z7u//nueP+w/enT28OEp841gXDzYn/zVWmzQfLDHjUrGS/ePhvLlrZfp8+mTlUq8eWCNdyWdr7JYh6jhZtLS31QaO6Zazie032DVQFMFfi0k2H8MeyOMKA8mMymFqbP00Osi4lPctPJ+K7d68joEjYEyRoE+2bl0ihJ8AQbdAizxickZWrkmN1/92q//f6wBipZ7bZVQhJFBjK0bHusKlRSYYsrQed6xWO1yRpW/h7/JEtJ00/yZaHNazjEsriNtDE/Z5O3mpSjwyb5GYlHCU9afkcv9ChuMx+V8FDxUMoppSMC6nudNtgLQmBO5mrcEDWjPgjnIaSGZbY20rVvB2IDOY1Zvz8+DO8Kzdv4/eJKRX9HI1odbOmpD7COREFapsNaPFbmzzbND4PYuKyS+pY45+rolaVe+DCZ/y47dV6+cOThxEDLFvJxIA/l2lKPCLbrGY1ZggUQy9cwvOn3JQHCz4sa/0jhIVmWTKTmWDydy92AG3ckTSGZiVIkXqq6kEsSQUrwp3k1/KTNWYl5N8x2COE+hATgK0gA6juqMDDDzgap0719A1ojL/cPfkODWFkeHH/4cHRwxm8eHh+fxPvX2DYN5ZZb6KcGMAUMohrcroajgoaRthrcMQfj9yBUnybEKLwcdDuTCfUXtl5/PDk9OE9xjenqhxXOrzzxyg9+VEaX7wMhprDKchbEH+Oim0tMpORM7gaILWDVaX/Za0pEGAUFbOgczO8uJt0FyYsUrxfDcX8WdgbDwXzQj4oMhoqKOSZ5oFSSld7CNPBlt8uZTD8OKnKlmHqorlAf1ouzhrAkpxvIVg9JoznsSmqNau5EI5V9KMR17CFS0eYgVH4zdHPHBwe/+6JKd06hQWO54nGI2ahikzWU3l1XqQAtn2O2D76FTKROP+6wEzWgnG61hCuEX2uCiY10D4/tJeIngdMfQ/1LLXF8SxmUujlYVJ79O8xWIM4Ftf6lmYNzlxPi9KWJtLVk9VrYTTNkIcvbli4kRqL0LUy/cIu2lNZx72x9fK02S2s1rWkmFbw8CktqIO8HPA82QcETPreee8z1B54+mrFMmWvpXCejZV3ijnn5zHqZzNxjmyCZDifFM9Zs6yZn4owMYwf+zngzE93I8+xvbmO3v7zD7+38qy/RfZAceHnb7vVMNT1gbaH96eStzg1qgZJitVSllW/mZk4tBmdh8hZ7L30k7qR39K+E4/eFyNVbXb12Iykgsg/fnqgt4BhioqOP+yiZIB9ZU1vWHHUFbqXgISn0OpgKPUWAjEVLuelpJ7kTYRLLgJcl57SBwdkdmCuwaWU792fBTlZk8huJRH4OmGm48LS0ku+5oKoVo+0NvCobCcmJ14i6GTLWbUTS20KnLf08cqUNjLHQ92mZB6UubhMeaTYjN1Gr506nyhSFt16wWroDiNQBLQN8Y66+CV8s+gro8yv/OVwM5+0UqwLg+aP3oagkUpchkM6nVFPXsMFEVmQNekwkqOGfw0BG+aA4lm3ldesvAoNfNL0LCupNSkJbrhU/EbNAJUhasZlj8x5o9El4nOkgWEDu2WTQ2/xj81V4NRsYnxn78Xyb8MbdAvX2/49yxPIQdPienoEZcqrl70xXY+1pbYiSj562Fa3HzjeH++e2slrRcKBC8/YE+e4QZ1S18stVu/KgSVjb0WB8ifQMLtQAVvXuc8omVDO2T54PkPA09CLPWRLscoKpxgmETrPreDTpDKJh6ElyHjo1douGwcgT2YYKticisMQlZck5hCj45+rOZQTn3rNpN3eUoDBy+rYf4Gx5kHyN1hArZ1mbOE1MvYCsMgS3uOpOnco24UDLD3SVP3B1qd3u+yzbS7525HDfgke7WhAPboUuk/S7Kw6RfWDHb0Ywd5mT0MrSG4DFg5lcq3HbMAkROSXvqAV3mMvmxKa12+UwixIkZtL+SZKuJtB6SMe1SF+qEC2ZsN7zzdN2mZb/AmhCjuQYkvdFCW265cQlR45ykLVJJsOoNS328K/Pzh5tII1iG5rt/bvIK2TISewhJnTi+cwD5AtUTXPKqL0tGvbnczWjLXcILnjzOWHh1bdBZXXz0zhadMAadPqyYayLdyTfUMv+YBHNJ6M31iL5Y/P3wWwRbZ7Ow/kigvaYA4CIO54ONVdC2mEFz3XqjlbAAhBoC7Gf2u+aXpPzr4h1RehyTKlYvYln8UH6DKXXPyUcCyrC00AcLfBakV2wQeJqmDjqUY4VAzVoe78bTRaAS0GqQojpIaPqZpVOP+6/PTn+rF4d7rebv7/9cHDw1uOz7BbnpI5TnBMYtAi8C5zdxW5Noo7YP3l9EBDcbo+QKyk6G4pOgnEAn47Cb2F31r/ZhFgwGD+Wp0EhL5Tsi1CE0rCCVd7+AW2cm/O7aR9CkHn/do7fdEnNstSWs5+BC0h9UbQPT12B3nPlmeAt6qOMUdXeo0uX54o68xBsceLdAgiFyv0b0+YE9YK3AM0l0AdXjIIkUgg7Nv2G7yQ/9dW7VBINks9ODMQN9HUpK2i8Dtc6qlFXa2seFNSFFLu49vozd7jU/kSpEU1pJiwnCZHO3INCFjmrvyqHa8MiTQkkRmaDhZAUdGzIbu1lWqw9Z2X7PGYNbiVlwbv3A6D3sDXv8Mbn/ZF6s98NZz2woeHVYNqfwV8kbV3oEHKkSgLSWw9YlkKTiI7JcQJ3sguJU+iii+IvkVd0hDB1rGZJ1yc/kpDnwcntTtdQR6TYK0t6tA+vETuBZMNu00nQZPCkvdIqyUk3ksmTfEoEzveXnp2qfzBR7fs29fGqQ+VyWaLObrx+Mgjydm9mRUxzRYYZqNGZCbOX3yDxQ9PjaZTMK/o9e7FbxJiCt6JqC1pMMlVpshkeMgo4a076gdHvU4vn8oWRLtg/+c+nt78ftw+P2u+OX2ukWUfmjfHpNSMZweZt+/3iYrrA0ASW9IY1iHzBjnHjS8Vd1SJcZepKNcSn/S8hNhGPNq/BxETmI1se4xS9f6jYhOPLBTmTuGq/hNchRxRrGXyE3CXdwOUE9ZXr/mxwMeiG8XWowp9wBsDQedjpD8O4G46UZQBymy/YqfucLx4RcvVU8iObBSgLe6ozQo/XsukgLeDkIy11QTV5rBPSV6fT4YmaafDS45ymnTmCWWi5DmY+svE9z74g2+vHi1FOB/wXP0Q6Jp/fwUY1msY8MjUL5P59wGVOqHnI2g3GFxNtuHcFL0zpZfFyUltszuHx2/VyGB56iAPTQlBZe3PgF4PK81SGAajY+WbqskUmiH9Suou0cIEpNZVNsh1AHc94KfCJ7Y477rp5fuzMrz7SeOoJ9lSbysqKF+SMKw/UsZCkgB3llUikLGjK8ZiBc7JNzLqd2aK3GGbY4BKIkYsRzlvdoAapTcCSHILOoEEbDHB9KcYDfZcyZGPEdOK1fv78eXN/Mb9ScwvpcMHfexlGSDs364fDkVwK23OYTlaNxSViaz5TW1CsDBVXeD6NoVg6mQ1a0bfzfi++EEyG3VOYGeprJ6RmygiJHvcECo1vs8FoS82dxz2Oegp+wL50fZr9049/nE7vrFOQtng5Ca94T/ri77Hwq0YMughEkxKDgOPxEBx/gAsI9UoVO8PLwNNoZLP4WW1kty81dPfSgzUmWuZJH8EmtdNSCbRa1zQWOvXDZflF1iTgS/cZ3GQ5D1eDXt/yc6sA59f0GtRLGawiAbdaLavlimTP3jyQH1o9nb6pufQqYy4R1W+Su88BIEB6sbYVdxuNuFPZ+hof3sbRYBR3y6VSbIhQ4n1YDlfhPD64w/gSv3jT7yBaI7KgtlVqiwdEhM7JobPsV7HELB2zumF2Me5H3XDqdnQ2NqrqKp9WDp5uVZ/WG/jvwdN67Wnl6GlVvXj1tL711C/BR/UjeAEHvMIXDXixVXu65cOfNXVMGd6kF+okPr2o4P/wSHUG9XX1W/4rvgUk56g0XM2u/Xk+bP130f0KcKI+8+MAL1Ue58KUNJGCJ7BjuviHHCUCCekGDiEJtHl7pZ2gvFuv1So19QKYOeytN8n2kdLpjBzGD9MdZEsIfq9BCDI7vWk87049FXPx7dez23YzPF2ToAtMEhsRnxtQM1bXsKF9zuhqcXExXBGfmokOgXulBM59IsuYGc4GLh+ssm0jrn4kCGfTDYRV6teHrfnJu8nXweHLA51lopoIvasNAKWFqzZt1A4KXDaMyrtTs0uk0iK3o8kDsSteGea5uwxDmBtL5b8cIugHehZfaMNomh0y1MusYhI5O3y/mBMu1VeGljpU780m00hd7AIkumYLtQuN+0MtE2PP1+TGZZHfW4499fk7LRWvwxFykHzudwwElSsrVZIfB5hUjpCnnjagHNYjelC6fbXvqg+C+a08x4H9cSoNY+0lFfH4ClIGzNqRpWXXd2hprJ2VOuzrqa4P3uY4E41vIfsOv2F+gU9T2UlyUb5fDA8GutfL6eitYrc6lhTN8anWfGupW20lGjaKwu5NbahNHs10cVpomzQbDnEoPbhC+GJrQiESFOCioC3hXvoLMrhVdJeOqY8u8Zvzq0G0+fzg5N2rjIYvdYKLUF1rDERz5nMJSfnL0Wg+hbgDlqLV263xJOUfuyxdMcf7BRFck7AgqfKy8yyJOH59IMyar5E4CQDYxBwfuU9GsORD5c4MhyEq2VNtAf+GjHixfzsfDjpC/I7ZLv79Le79g6kzAYk9uKe952xjrUZ+pPYJh/3bRaR5Q4EYetDtE69PYDX488nBgFZdfpSxsh3E2oO8qaTxbggihYm7is38KH/+QsDlcE3z0bRI84lnYx1mo/a8kF4MhmpKevXdcDxGN6yD6vWwv8TknWEIg8d4TZs4mX6dZMDBBdanljCOM6bWAzDcmyPlCYR8BmqjV2sdGN6Sm3L3ahYI1CH4W5Z/iqZHtpZ/x8kYIKnMrM91Ob4uTPeUU60RxUDgqspk0RAhD3HSBMPAnaDj3MOxw1z/H6+RkAm5mIA/iTnnlkhsmLcRo+wzUl87ONdWF3jOUhjXQerbHpfpgmQ7hbsvrghhWW0ouxljnb9lES9q0AlV4zJkgvgG0Ka6fG7cjJGHZow8NWOA9M9TImwq10qm/MUucqOUFqpomrTy+vcLYbzSsLM9TeaZX7GRq+mXuUOT7wmpw0odDZV61agFRMEMf2wDhK4Qw8utsn65XTIvK/plfQteesoxw4ipoKYBiJKWaxvVmgTl2FpfKSWIVDAZc3Q7PQSTAJaMD5YaRgZ9AMblmMmTKhTKu60fjZWH+DW09mBq+K7qqupTMhtEYx8UTlluDqWNcDTQQfWaoJj+0YCeHyNbT/4xqXgg3mwEkTXkARFY38w6UA20OlDLS5cby60vd/2bhbrLMV9eIxF849V9vJpG07v4i1BA5H/vz6AGqGYHanvBICvXpEI9p2JDsbEbBNas5hko0yvzeKdm3Zi1ULYoX6YW6cRrqtEGKxY1relCNGJR/s3b0zNwJXcMt93OwCg8VbHXu+ogzQ/7F2ix8l/7/SlmCfhSWQdNX6pwDyVYXY9Oj07OTj99AAPE4P78wcn+6RvUwaRaIp8Bu+KgGLyaPz3GNLRHNZ6mcC1pq+G0ctkib2C/Ym3EUtkIHfc1xXSspuqXkL6ayhB9iyBDlEHiHzwIB+G7r3KVKUG89BCVvOU7mMCfT1dLrcvTsw8Hshms27221Sr5MDW3DSkrBSJb5lPaurMyW2rH6oejNjfWdIcD4rh3IPSmfiVuFCn0+kZCtq/2y4Hx95vP3g3Gi9uYyo+R3STvdEhLRF0sPuTn6zhoNBjelGYjw2NZrYrLk920JtWZnwONvX93fHzI302TOBxcNZFOgUkOAy3wVR6ycqBMf+r8dZ2tHCTEkXnM8lbs0mZO+W0e9kVq/QY8GFpUka4RpzG+7g2u6ShC2IoWEpRX1Ed4yMVkQolAfTq3MRV3N6dH9Dsdomgh9thKcX8oAswq2BrKD9DIplSxLbiWIgjivFRgy6KLLGy0XizOSqU5FRJkMH3dNPKp/4ewXaUykNWSHenuj6HvYdyG53M7BQYM+HuAsrusoLvHpyeaNEvdBSzQIALRAwTuJnsRzfQzoucPNUBYsAGDANuR/ADik+yip+UdJGqQmYExiVdb8HsMtsuQcB0NnWVJfbhaBSGzhxPZoDKXmdB2wQgWZBiRVbkAXqZWuoNDzP6OXbK+G3ScfvwDHv70zl4wSLNbS/PamxQXnBXhqyYmstGs1jAfTMbRZNjnM2/xLEyww9kutNq/Y+VvyAa+rnZFa1Pk8xCf2oN96iWE+WWDz6WRk5ngC0wCD84O8flSK95o0msN7s4vWneDc+PUs23X0qPcHyfDcvT723ft04/7B0dQ+YSqXwzkLV6qMhY8UXv1u3cHx4dHQTKPUSfd61Te0ZbG5PSYFT/BeKJciDPhAkvuliHBrWkvSS0o1Tp/Y5lJW6XeyCbnr9bTjAMu3ml/jqrMzPrJzKg8iiToCR372aybxqpQjVnF6kSuEhQil9FFcLkvmkZIAj8vW0eVtra2uIpuNSMlfgRNvZ1KzTAofPUk3NnQS8VMtFQ6GO6PT7onMKsXlhqmLBiwYfZxKKkQUgslZ5D5x6sCVJXKNnZGOgpcSBc/GQ65mzpDI86UkLgX9QYbC5aG6nXz+cVU9gb4qzeIpkgmjr/w+u2rbeW/3QDJVIQpXBW2W5RZ1To5TKmunezWBuuyJOAs210OoGO3XPFtnflABjRT41+1GXhcFVcrQKpk7s/rlGbBpqDNCYoLEwsAAOeJxlrsHure5R0cTs1RwHSHHz7QLAF1QW7x4NWZ2CDbzj1jMwdO0wEnHpDf4zUiI4cYcqM4+9G3yYJxUvUt6Q56CIGN7njVqrChx0qFiPjfF2+DyIhkW8z70OV2pTbw+EbN8rWHeb206SDpnMrKpHwLU0PqmRXQ0lU3KrVE41dmrshP5YpMMrOlAnVsQPexAb0uj6mRzaqRBrCgPi/+jvB4WdV/g2egH+x2Y8C5gd83XozodZMz31x3/OWXQOrtOODTwewuVv8h9A188/37+M8/na95MFRL95vd6+t4qiHfdFfU2QyOwArbbG1mian0zsCurIikxQIMsKI5tfTo8Phg/ufHI6ggQ958RV12v9vt9yBqgY0JAhi1INUPxKinGzCUjt7vzzhTSQqomED+oaAPC3PJB4YEocw4snulDi/HvofTdYI+E8zMda/5x/7Zm6MP8Ox/18UFaoDOqB3anQlYHFFvF4Hs2UY8A27G/nF0sZgHIuf83D3xUeGxOHBXKkbhwhW2LFeqLmChsfwwGH8JC+R0HTq5Auw6tnw/13PqjnpBoX/b56heS92/6N+q59HV09i1uHzmGvtLD7lsq+JK5Tq0NtX+omnVfycPUWwBtu6WfTXWVhS1I0baRmk+5BFyQxr+AmRYg2if6aBBrQL+gwusHz0b94kFuCiJkFn/v8/GE97ysIPWzYB+DbshWLLILddtkSObUsKxvbWBVfilcpPgKiz4Bagt8q5s9vifIO8+lbwcqlAgwzO+aXCOpCpZSqVI7D22+ezo1mvOGcFpxpHlE1dTxCRSQYFdF06KhlreEjYyriVBk4iOxNba1FVsJC6I7w1bSLeo0aydhqb2P0jIqiZyo2RFrS1geYcxkkkk4Cx9ZjQvIKEDp77szy9sYB5eoJamE/lCAWalqZA5hc2Lfa+51xSH7btfVT89EpE4kbaoUrMkLIgAJIxcuELAtx9AEz3oJIBMwkBFlBUfQ0639dMmceVCMSyXaklDf61SsW/fTbXBax+7IQ3AU1dIEfe9nsZ94wgWgLxNLfMOpIBBswTXDp+Qigy11Xv/4zXrzsCLV4tma6NST4GV/Yzbe0bJX5uWOvMe+WKwKXLbyS+96d+COOcE8UFuTq1cgPDy2gpzxYptZ6QL1aGHk1GI2ttz8I9QG4h9MvW/VxDwzWLUqlVbHzKnK4+MBYSQHWQyGkTqrR4mnD2Jyt4sxpSTehl+uQn1JSDgEO2/Mid2bif43KRfjMKLfltFWP0YYABtmJC9wSw26CkPH1Tc6g1nN7eb58CL5gWxd78tswGNXlraLgfqvylYaRbBWJx5XALL4X7FY96GJtclKa5I5iEcvaHqtiAG+bBXtLAJ57eOjvZfQFiD6ZjOXWu+hYmYQvPtoZyiUZL4NthP1mWMkdTKjpQHpZY3DreTa7fQ3MulFcnI+FIj0JKmTVCId4AsAngLvfvKxjbCECw9SejEg5+D8hunLKXox6F9oywhpmljVhtjO/wS3ga6DQuQAnGZcxoru51bkiBSdod6UixvXH8NpCbIH9bfg+M/U3Y62ES655K0YrAppm43LK/0BtewfUJjOGZVIVgdhncwXuPJuI+m6vnu9PlpP5x1r9RU2DuHeafe2QVRAJ2NTXDWchh4S0kVmEISOe4GxfC5+mf6HGYY/AlJX/Uf07dVxZ43X4dxGaqa1jzu5Xe8pjTMgqN8n7RUFBj7NUpXLrNtmQydMvGyZXSVhS9XLDnVncBm+vXNvJAN1/SnOpdJ8w9/IbhRzyRf3MOnUjOKIjj/1Ozj2j1q8mxommlLtbBKqoVWUpD7E3wsvSnrNe13B2F8PQgvZ2E8Vq6CCoWG/evBHEQpByrMieLhYAq0avFUzZ5R2L2L52EvHIZqG1GGUC2tMb687Y9V/DiMQ7CKQ3XO6RViMpSL15dcABqFIpkIumadxXdS9sC7uAf/JCYLUzbCkyHtMPu4HGLvKc8E8Cdgjfz398i348Fp8MSgkYIn9jyapeoDtcaRITKkvm4pB2AnoV/O1v8DWiIHBgxn+3jYuAnf7F/+yjpNDV0d/Xj6duvt69/9Pz/fTP4cvfr219mfpT/8k+GffuPrX6eX8dsv0eVff/x11Tm4Gvz5x4fhhy8nF3+9/v1LRx3z20F8fPdy2Bu9in57/etV1//kf3ilvvb5w/Vfrz9NftVNzHW7uw+xvB0nqUCzClO02nhhBb26/b80sf7/Opv4fvgGHl4R2df+b+aTgKV2IRGAvp3twYBMC4kU5s8m0+EC08Dd8HrxTVDx8WE4+3rRv4M3WH9Du0XYW1ndWhGT/h4Jto/r575DSVjFLsMyiGnaoNLvOuPqqv96PRz+9nq4+NO/LasJe9Hxa1/iv/64mvYOrqad0Yeo9/lk+IffWPx542Ww02Y5mCo05vaPGlea0teTkHmmSL5G3Y3VrSzKDEJ5EGgwI+GJa2YK+2o7ms90oLDCxx/OEDkcrRvyNFDYAMBf08YhCUHszGqe8bJOUkmfJNUig//HvzJPUZWoq4ZtjPAwrdj3JppIHsiOfkWkkufJrl17BZQB1DDsMnyHf8HnKhkcOp9Mo3BerrwYDq77IIYVSOhew5a7etJXB/XFnBD/40/8/gbcV/wQqFgGl/iuWqKdTt+C9dWwCw5wSqNZ+7at5jR4CJubzzHN4mMtsM9N1bD7lnrhHYMMo5KlR0Vf4lNq6aQpImGVARh8DcG5eq1eLgLkdlQxwUyZK+mK3VWeDH+7nuG1iPoLd5JBztHNqcWuSJ+XzrExf9k9mES0kKlcPq4YOxevHiqG/kx2h9n4iXosIHjHvIfOu8k2Fl/UrO11qc5rsV8B3+JGpWanADi5L9lsm2KrRo1ZyuPUoKqXf9Jh70+CwuuTo6MP8W9XC+SmG/bj94OvfXhyH2fhPBxBDjsgbV5sQkbba1hHZfy3XeAPI+OU6/uHOvw9zgvM/r9ATYdZHy1sYTK71FMZTB8IeOwOxtPFnNUDUHxtuLA7nUxQo3zicHA+vuzDJJXsF42SfLloLpH6odQEAwY2MUQNQ1PWWHLBEO72kVEvQAwMt9Y78JpamZg9/MzAfjCi1kRY1TrJgl6+GtVn0vyYlAskrj2dvXFq/QLEfmHNI9OEp3t0g9zByUHFV3Ns0QETShqnmqZtBWJA09G/0JiIF2lZSvLTqQ0rsLUpgyAoS8HY57xOrUzWKZVUltR6BSbw97BN95zTkrqhofh7qJFWCgipEqIKqxO3HGh2esgKTC77MwBw9G9D+M/7X8s+/HcfPJ/o5WROpfGmMQ05tz/S0i+WmkNZjweGVqBcZAPLwIUqtsp+5VzaaB6g7LHK3amjClrZN5eWemosk/GRLYqexTVl84QFdiaUaZUyspp0YR8PX0HsS70LdOl8/9g7DYTLOZparybKe5h59qkwKYDhnM1NoQtgeFl5K8kqwaePLoo+rjeYEbiRz+xzGgIJKQMKFokYTx8BTvr/6BJGok5kbI9oRSCrCOxvFjbBTQhcdvxK2de1eq30bklj1rBlzcUjmIkKf4Dd+YgNKesWZo+/TJteWsnW5ZiEsq1FsIlck/lE6wpWDJVPenuONRqQsgQRljWHS+I8aSto8gNoVnY7Zx9UB5QlssBHY64Hbq2KT0STKFgXRN9TQRsxIBhbjU8xL+aan2Vyu8P+MLA60vaw26FwCr60D1DEgFAf+k3IgEAaxG57qGHfGGTjR9GlsNONpoMhMapIbYQqLvMRo7MCTFLwLU2igl0buex2+cyIxqmYNkUbu4Qr6OTt7+9PX8ukK5jPCzyHsL0Zi9yX/fGE5wP2X9Wd/qv3gDvkNg/c9M+uIAkIn1RPJgetifoU7uA1EKQ5LDE17M7asiNSNRmfCBjp9dtWsPkT17hafxd39Ue7xeRUJktDOEQ5uS+dA0EnR1rXIEoEFdGg0OsABdzttN0d9TRUuvX3L8+Rh7aF/+WSMiWCysgM0wfCGZYmIWMBg+UxH15V24wKpH//u5jMcRYSZsZrQiBycAosk3lRPahg4HMeY7mTTRPz79ZII3U7rZGa3M5KBqas9jF1wkzRR7jSX19/uO58KXfefj0aaHBiuarlTBPkAsojBO8hUjExNAuD18PFoQqmR1sZ5JboQ6pVg3gP5YWFWIPT/oHO0Zl/MvR1LPdjle+aXJLYu4aMH4QuZTCoujWL/IM607WyaecOPpI1ClVf+i76ZyGfFxsmUkQxcMu2NTmXjv9zT3R3eMsngGpNayAhCrVl9JlKsv3BOMBdcZMq9cWcm/CAzCDx5KYHVn1kUEFl490nhwllPJyAcb3Xm0ToKiALrWB+IZ+/PgJiJf7mFju3mYq9VuQAYz4MZ+E1uvjU/6+H/BP2yPAZsfAB7YlAMqu9rptpr4MtQBjnoGYnxatEVdRYfgYFl8CGD4l3i/VYhOAOwHvAjq51SGR//rhraPrQh7EdGFP8rbE07HZCngxI/4LxI9zf0qS42HqII/CaZgS0+ZVrFb97gXN+myXcrV4kmC0lvempTzJWqz6wJsuT/M37rG6jf3AZdKcVwlQ2nFJMjgHXY+lUG3tN4xDtcbiLM7GhM40Q9aq5CzaP7iJWHsx1fzb3APK5WHhNuqvYaELp4NQDFo4tKMbkmuDG4uus9V0RWCXa3a+tAeB5K8tP+7nTN9TVC71EPWVR66YEv87JBBKELSd1KnXFXRbYFmehSGqYVvlo0rs1WLqKb+nISGrpVSa1fKSFm4iauBtGAnFBm0BPFbbZm6l4xe3oanJDMZfG+2GDmTpELjaSg60Cd4365Hx4msukNe6Gg9uw3QnHX4lQR3w/w6jk5tDSTfWGNoqcBSYKBOcoBW7jhYR9byhcl6zzafw0ISw/Hh7Tj4zuov8On4GXgBWVHkWbhSawtMCP9DqwGvf6GK2rIYgWw7myg5EYVloyRD+vVe8kEW5V2eHGYljBjIl86DHGD3EuqdHie0UiQqc1N3cAgw54PXiQN8RTrB5BV1mj3uByMKfsZvnD5MYVWqpV6mIVd0f9OVbhVPgWbAIx3TWPHLkdkDpUMaMagitPLZ6mFTpYXYKMiOLy6WI23IN6msjF7fHHb8edye27weXVPIymt2NUYq5uNMRWSHonHa9YjGJMewNhyF5QOE+dWT1LOHnQbI5bQWHv3P2JbUHXBYGA5nWnJhO6xpDYiJWzLJXfQMdMUHiBxqyPnw+bnhiwOtE74SjQSWP4B/uC0WVaD/JPg79//h84GMo3Nktj09ZqeuC++fIxrQ5z4F+SBlGEBL0tQLhyE03iWW3LkzCABrdEzUcJTiFI8/8AC4wBpdIVk5ir0z+oVuqelqOfMGb6o1qQo3AsXTjKkCFeYgalfVTxGF9Wx9KvW6tKY80u6K3BjAQQNqe6lNF5jlmk2hLxBeuML6C3dovwFV4FpK8K6b5iOJ0WYftQ/8z6xffceb4KkuOgAORhhW9OSt3DyfW7ysurbuVk+OdouHg3Ur7waePuzz+6107YHGD6KIdJ1qWWCMhIavAzO+jG+5AGC7vKk7D54jPOkiJrK0uAYG/4DhdErapR9fY2DcgTJ7UcJ+ICjwKDgNJIYO8IyNNAxQfz0Eudu3ll1BqUz8fwT8IaYbsgNKeT/eSvkRdHIR+4iaeDd+O5ngI1rgRMQdUbnsILq04lltpl8+T+OGIj0KIlfL66ALQNQCADUAcePtLO3swXvSiOJvPpgFjLTJb9Zzoq40FAkUK3KMRByzNF/Vzr5xfFR0+D8lZ9R2ika8jdXR5oWDSV9sFFoyFGjCr+GUTyHLEo6KMYo/pKEXsl+1+BJ4g6RODdg8lotBgP5nfWQbw+AkblrAsJAp92W4CkT2Z9hM0nBwYXHieI1UpWC2gWOPROYG2QRkTbG/0s0axBKzURc9LdOiUeZW5igsaX2exovinqUYUQwALH232rcC7K8VRLJRIdl7KfCQGzi5JMWBAJA3gN+xqBTq9LUORsFR6UqNXCv1aOTtSLvhGCaVI6b0d3o9j8eXEnFdgadg/6aZhquhRZ2sl8+yGXQwwtpLRskqnD5uHxKeazfDmkIsG0FYTtn52B8pKaWkcfzoAP+c8JVLaivj5RUJhhgS9H4aNdqE063djeWC9xrVhXEC0eAI7brXwi3J9y5EwjD5+qwrPfvqfRAk+pj7WzwMLpbJUdKG9oMwLWuHGw9iyDskbgG6kKiKyMvK40mBe/Q6MAAHsLm+0NAp/VLKyFGhv+YezjMZkw7jche7g/+7IYu7ku6iKsUh1VjfPXMZLaM0BRYIiIg+Dd0idZa6/5TYPqrlrlwfC8h7uunoyIrk7xSK4L+z89qFRREasUOYbJV5LiKFYNg0Zrx3TypiCj1sFaCZjTG/VaUgfKOhgLir5N1Jv98y5yNbgNegjrVmNUFw6UoMaFLZ6ZVVdTJTWvt7mPaDdHZcbnLUCGBc9UELlOSYLJcHGrgrEp2szBcNjsz1LHYMVSEAmksbltkWJVk8zHEZNQMi2jenk1AQXZedcryk7C2UK9PRWoPQW/gGzuV2Fv4uoJJGwaIFJsBXf2MtLHsF6wEG3qikGdtBSqiVr+/oe/4g42IFGbB4Vv35QTi0Swn3FI1HGGHny9o+nBMVh5N7mMo8HlmDOrg3E8GcseQOE1gC818z/0+bgUlA4hdfyAMgA39XASUE2SbKa0vKsTUMPeTcy4ZrdF7LK02S7dQDS5CoHpkpDZsXzlRUfNlzv4B/0ZrGFb/hz2ePol1Bi1KGqiaFzWNRDEC9pXKnGOCvS4BdP5pv+vv1mhb0oNjwhFc0xA0jARUQbIdgNDc2fmVKQHTZshpAsK1ovn9z7XvcBDWCi75x0I/QopKpRJUUF33KOMOnqHXhNgGZ3J5KtIYSDJcrncWBbfVaq/BYWDkooPsAtXUj7YDlopUeWcJWStsNEZhj7EIfu9njsUOBKeULHogXCrke5f9sq32YEftAg6IWT35mh69CDfNLtPYllDYYvVdiRrYy/qit6yYI1RMiEPg+vkEuDsnEWwFgJmvUtVt46c4Jk8HQP1fPt0397CdU4o64vKris3UX1oR6Z2/ZYaPJ2gtNA8ZZ1D9QfA4pRpKTTR3FSWp+FcOZYhpVeQVGso2P0aNnb6QM6QmMSUENBPhP6k6Yy8OKsemaXi8A9OZPE+ll135jSPM1YDCFY3De4E3ppUgHNA0gxS5sq2xjfK+gLjzCj8NhnHCHuKwwlortzFo0F3NokmF/O4A0Ck8Nti1o8vkSIuvguvJhMZcwwg6uSZqHAgMuQD16Yng/tC0boY41igpIph6iEOkZCdHmwEBYlQAUjcRv2uJ4XNveB7pJOU6QDNEHEZfFqx89G0rTvDxbBig2bVKU52vgabHDLoVjN2y6hYyN9EYH9pS0LMz/RAOTDMBQFmjJSBt4GNOSYFIUCNR4CNytL9elAWnD8XZcyn/NN+Kg0D8BQVlPd/V8OKBy9d8rv4t/4gCueRLbkOfZY49nzWimTLqaO+ndhI38+sXINxv/nLYDqr5pKmEP7cjO9aL4LonNNRu+eizwKH8PeIWwzChU611KpWROiogRb0YYlfPER7llxRUE4OlBEgiad8L9qVYAHo8oFVUnBCRA92phasOgLAIsc3/FHfKFeWcp91uV7LO3bTRylwj3XxOeVuDG4B0+JNbxaDnl7mfl//ZdLpVnEO/mkFSVXiSEtB+ED3RV+16e9rpETq+0kEd2P5Mbz7GA4BPn1KGMjkA7J2gPUHdwCa3mXyqj+dvAscEhzmAa5hnyaUjvSFFBmLBz4ttVwRllJHNvnfFjPBtgUG5onNlnUHnUKQnUAIxPPEDDaT87wcAuAACEryJ8L7Mt86F6uPHY4w81/ewXjMifxFvTqaYQg1k34nzAJpqi7+MmYva8Z9yVuku+rWTsLBN1wMeKZR/Ovp6cfPH44OHzXBE9/lzXNbomYbJKFCuEu4cCQjqywRNUFFLJSPvtY21CZmq22TQ1XSCoM2slDQ1fz6GW5Rg/GgTfpV7LzoUgilwNL95GbGo/6ypte0o1dsMISGfqwnTYaT2bOfhYyS3EshYpvOIIsIoF+8QybjibcRfUFZxvIAvnAD73eArMrm59GPkYi2JaoFK0zPxDM+JSX5qw1ij0QiFkoQiK4sGRVIfQaScyH8BadcAqnGaY0bnQWm0p1h5+5k0SRnfZ/YChKn1E5a8jO2Sr4ug7AUJFjujq1FczNFDfeoDZuWgbPBqDoVSyRyLLd7k3FfElyWA+LL7WlrZBB/eTfjFripVQMtK2RaLj58/2ZfN+tGVqsyW1xsp9xyAstoegcCYRhkQ6shozHgqXYwIxJ8hp0VdPC+2Z8m5ud2aslx20SAmZCb83WaRfvIXQjsO8FmE9mo4fUMAzg+FcbxDWXckIJ6tBjOB0JEbTt07Bm1AbIVzvpBTm3S7d+PTkA/RZcja1sMuTRAxd0STzDyH6zU3dOnMNpPIbuslqP6i3dDKK2mMZbITWRQNFhngfaRMPpKdCfcnc6JMuy8hCT66QK1Di4WwyEyNqgwBMOwGfTwR7anpCzNE2NcHDdXjQSTyyP7F/8EWM+K2RW4dx/ORqi6WIVyt3ff3EeH3YpV0++WC2JlB0g94GIchTdgsBH5UQGoXjPx7Yr8pmRyuleDcXgRjmFSuUnvXZ2raRBMdFsMvfsoHq+d7zZ1AonzXR6Ay5dBgsfd7cUNbPeew7qSGTLxJcwZWFTPnG9lz64kb1rEWy/IgrIBVGlG7WaQQOXsoIGR5nR1giU2eToZMdLSq+hxfO6JkhEsxNbL7fO7KRr06EmNCQXxJWL9mGTQGt+69KMHT1girz3qQFlfeUtQtDxtYsH2su814xQyOH7dnyPBnw62oZkHQ40i83lRAew2nnpNOLIZ487QxIxaNI8BOKuz9diMhaQSu4GubgYr6nnqrYqfZKV3D4GND9qHHzxIe/1yEWCgGqWHBJCJjZ6atRwtZPyN7/cb8w81EhQn1FTQbxrXy6nw1amTCjywDtf1AixDTXkrePToER+IzlFJJ8cqCXR3sKrmwpdWx1Yi0Ku/GLVn0CTf785x4r963z49eveqTf6mEQVvTvfsXUeZY2FUV9/4uH/2RvxrPn+6Ban1jEYVV66wkecRkqApAzZhMhz2mZ8ghkCKz1eV5n8PZdvzewQfz62tbfAr/s9j9e/jjceP+e1C1n+o3UK+p6+5JvULhlntrdkM3ms7+u1MUCdwBp/nH+OpC+tNffTjYg7+W1SRGbXtbAT+xlpQXTM/XBf4l/4F59zWLyeaNNZ2XPhbm0XnVZCMP7pGMLV7+b5QiztHBhU8TP8ICIjBsJapvQ1GqobDtOTL3eJMgr5Dig3p/lpr8eNzGqDLb+oygeYBpchTqLoYXZoYRm1NOmDgTfVf2GZlcDC+qfjm56JuOGaRe90VsnZ4fPDp/dGHs/bJ8fEZ3Y/RcMmjiIu/a3TX1T35JOVyDyMIckvcPaNcST8431hTpmnNGTtYVyOo8X/nhwuPi4+RhRtPpH6po57Z153lcj5ZAE5UmhUsDpYNzA/y5/bJTw9O3n48w+M+7L8/WgPtjwrNL7mscf/mp78G0/2Z2myvESCpPgyqm8+Z+OhsIksDv4YJ/Z/U2gW7vPcYXSrlM82L8P4mgHoe/8RymY8hh//4J5bJfPz4+e4QxPKeU8vWT+jl7OFy+4lOBiP0+CdEhtHbxee7Rf5OZwZ/0Tfp6GjRGQ3mcrz8ZZ8V0WXWqeASn7PsLItZ8BxpcKoyMcfW/vrj17tO5deL7uj3G/XfUvi5Nl5Li0mt/Vn5/Y52hzo2jpWpp7ed2E00kSt7CaYNFKRt02T/s/Am0TJL1OLiLDwWnxyc1LU1fqHnhe21W657HTvRgPwD/Yx7rOL+9Bgv4ELZ1cfAQ7hMf4ZW4jGfgngpbAayVDjLLwr3PpQaN/xtKDiuOspGg+T+4WtPDDIXdOtlXbtA7aYbXTrlcoHuBVtLK5cbTQFg7Gl7zQj9ouFQHoLBqjWtuqxWjdfPly+luuOyE4vXIq1IZXiQ4vFPqUsDXcvrEH3+J3we2FwaTm7R3k9tLXbsboPWXCd0K5hvwEtJNr4NEvmBOvYJQeJkM4g+nx5zxkROod6RyChx1sRZ0FOrpIj77eTbniNeb1iXQLge0fM5FCAglSeonvpMTMnvILAnF2/EgYdsMAjTobzLTjPIUoOqY5sOulSsXvL28OVZ60j5vdX98/eQkjo+4xQsf6HBzl72uCcmkzv054kR2gywLQuH/njWaQcWL24d23FqJSsPJ1DT1wt0zCN01j+rYABu6D1wIg+GCCfgE5QZNGMT4pUxjdfF6Di+uRpRb8z/MH0ztW4rKx93Zot5fIBN3MCxOQs7MdEvqbNAZojDavamsB/Hh8zc57cwDZBw8e1p++D4fVZolEi44tFJ7h87OytdPBayGM4sPilN3cL+dDocUFnFwnSkUHmVzef4haPbfnchCZHyBhIAyBqsIl70ax+V/uRnaN/SaBKdWdduFzb1gNdVr/vbdRX8UuV9tzggInVLRqeoDCi9wM0HX1n7Dza8+NtJkk13sVgJdlcrOdObNPqG2HUBk4lpXxlrVEg3RSC6kT/HTFtGUEzTUuJiwQnXl85zz2mFeSsM1vk3He/Wk+Fu8oNgmcviZa6TXlQ1xTpkixlnMfDBiE06BADXcTyxheYe2WylbrdjOFbj5Jkl7WX8gyRVCYEvgwHAmdjLZ85Q5TfOJ8PJjWCSMyxKulVZpiCJ+Pmm8cPgKsvbpI1SoNec+ShvbSzlmAoe4/wVtJpU+uxxRVT9D2D7eIodjwowq76bc/6EP1qJk6ltb0NNQiZPgvyHumEYvOVe8dmjp79QMsPb8ZpeXj9s2knU1vfH5WyymP7+QU2TtnTdhL1eG3y//njRJm0nfs63eLDOmvC0trqip8PF5WActRezof6STVWa5U3JsKdTlEGheDYDazy+xNoASgFU+udQ28ACwcUASwbJfRKblCoOw96fyhcYjPtvAAdOvGz59wz1ZB+zQqgeS0kpb6a+RQ+tBvN1/DE+iE/jEy+FTvRMVxbsCpAqAzOFEyWxTWPEI92paDrtBFvRfM3uxLJpjJbuCR+4tBybCTAjmE4S0u+gwjdf5vDRbQOxmHlypo0P3D7s/uxAredSZDR4M06qNkDtKRI9DP41X6hCVunKb4O0o0XqgYARZ3vzhOiMDt1zSD4QPEVlTxPteg9xfrjVjHtz5r2g2lK+0f05JKjVMefnaNabQf5RsH6e13xo3HwN/bBxUMFtQtnbHecajQHbIUAe0J7nXIKzOnYq+dip9ATOdxMC01I4+4rdHVFTM95klB8KbmTjViwSUY8GKTBmQWaPmjvxQcbEbjrABttSF+hKNsyFgEVYSV0RGGpLvfhJBWzLaRH65eftClf1fvm5XKrtxAPP/FnZiS/5z3Gs3lAhov5Uza84iOJg08v8un280XN5htSGJDpks/k9F+q+92H0td/7yFxEIErP144pKgz9TDH0wyQ4/M/Jf07gRmM1hjHXf4M4YHG9cPwVopP5JP4dR4rI2OGaYHc+xTLgFF1R9ecbqemuB4fqn8NZUPiNVD3Amf0m7/NhqJLSjtBkathCnRqn6lZ4CRvD9/h4ovUEJQ95D1RIVcsKWswCXZvP4z7jeA22D8C+/SlGOqTOGX4JRZ3T2vj5irfcXO3RLaXYRb8HPHgsi25wFgybpPya+sYMMFFRv+3WD0i1hmZfL5rT9sO8+OoJP9DFpL/SbErfMn+ZqtJZPROW7qkGeDUMhC0wzR6t8ObcANmEnMUSGoil4apK65UMRpxBIwZ+n1gSak5ItmbnhZwCLa2PFDQ7rDSICEP/WcLvsmssiFJD0HJLq14RRURezsZFd3z9zAJONrMdz6reeDPl6HQ7ZBs7edvt7JYBM8q1Uok6tHVxQGh7MsqyZUvSVhxH4TzDS69BR1f2T75otvrT82SKCX5V/Bv3DIZMVcuW1rEfrJF0ut1HVNaPiJ5RiRlEvs9PL4+FgN78i1JPfH7lyK4gcGPUAQgC2I/NmQ0eEeBUHZutthu2nVYR7qtF9zdYjWiNbHREwLAaA9lPd2C+hB8++YRQCPWT/DsIqCAMsO260+s2JF4z0gEEsSA0mPRgUWm1c4dV7ffwC7/CP5ZhxIasMhReshQugl6h9A+aB03YnEsrWlSshZpBlCq3cUh8SympeexBQsrkwMLr8U2gdS856L4ESkcSWvbtmcSKxsLSIEISo9GgAi0h4OJK+cObkidqgfyzWylM4evJ8LIPhaQCgtzzkQAN8nbrVL2q+cqIHVILHBK/t3rxkcjxlOMVq/0Rn9+pcwawb1tu07qjDAAl0CayD9INaMo0XYXEMpdzUmyTctm5nRROxyCtTvrRwWTyTqISF2hVpyYo2L0sGgAKyr0mb48PMNojJQtdd5teYqSAjQ3zq9kinsLuEoNN8ZhE195AJern/SI5zbzmC2FqeuKKTCWJqVRo77BSyV7K/fcovSp4C96FsSuq7ERS6hpeNP+NtxFIqwI+zQhxmvFDjxTXCG00lPJqmnQg9lhBFvbtuBfOwlF4BxDRgzt4pp8d59WCMqNjSwh2PguWPdUMkRVoitDQit0K++dRFDYv+zEPmn15kiK+GcyvQGfnlqvONKPOwpkabP4ZtEplJNPu8gzMkFXjJhAaCBoU+Fs6X7R21BvjLS0xboNTZKm/yV0iEUrNrZNTWfgMnNx9vI1NWK/7aq87IaPP3yXJICBusgm+GD9gQDLR+qpZl0GHJjesnXM1s+gJFJLaZHJeiXyrwBBBlTzrUsR1q1oqwLKAeGcg4biSA7+0tzaSQ4SXXFgwzKrJCK1SAZzIKSNCCBkIg44OTseSvOBfRt8NDOt+r/cmHPeIQKp7OdjUGoxBgcHGTL0B/uIPDBtQPHUGY2oVgZ0lvIl7k5sxbJeY/YfBCJqDvbjoJToatNtCgnMlB0bhkKMC/pD1mv2akSd1QSDhs/Kz4B5sBXrUIfBHklTPhGSVO8/KKoy2hqVOIj1VBp+xXpY4psyUtzQFrf+FO/e5TxPk6Ta1wAyyxJ3Co+2iI6MW2Ixbvsj67xblqp5rm2bfGPpVjgYMmyT16rYZbDZfX6HbooIf8sjyo1Z5OICmdDWNyMSa5QxtkFi8iRkVyr9SdZsy1blfzhbAAjgjBPG1YPH4eGnixH0dbupKg4vPtIikcUKwPYVy7k9yTEqBE3XPk20tJU1usQMbZuBBTwJhuzCUc7qSvB8/l+UqauE/zD8YYom01nmkuwgzLgXpDB/8Ev2mRvhXSAdddhUery3x/mHr6E0iBHvx1iZgY90R9f21bemvqiCzSILZ7vRFiwZS1vRVrc7BqftDrPThFnz86hWzd1poMDUf6RAet1I8ubiQzQYGSZqgM7wKYolI7e8vkoGee3gGYsroYvJNpUHy6kdcVUmv+QpCG6TlADkTNY9fnN98pYVDaWvlYbLvhp0y4FleTBeWz/PkAuJSw6UDFqKOvubpYLQ/nwtJgSyigFSU7vikpABUh+TEfxeDmQkXDXVXUHw5UWa0Z6nNsqoX0lKUlr1JdzGC+KkgEoRqEYz7UTec9mP6j0cnfVo5eFpX/2s8rR89rb986peetoKnoL1omWX6CdZerG+JbVtJYbYyPaxe/fb5FKJNc++S7sVmmzoB45B/izML+ONM+qWeeFWymOfEygTV0XbzYj61zCS23tSc8POktoVVWX/5cTbBV9XlH/tgI9/Dsz7kD0/ftCpH5+/e6UcMpg3Ijm1OGzNjbV7ZgIM15ax2J5OvmRPc6gzY4x/IUOdJDuk/rnSLC8atH4TYe2x1MTp1EGqISekMZvPCJGng1XtOn9nKPj29FrSB1CHIvdW1ynhoG2PiVE34Kznhg4Fv5ZrGF88FPt/VtoCTM/NFxsUQktS1x7CgNqCRoGrI2Va4nhk8vOqqHp1bxaBHwROUmH5AxizrwZ4cvTo6OXIAMemqJSPeUoy+2NpitSY6Rp0U2RopuW4hbE4SeWXSsembodr2DjutJl/BFVBtpoVwFqJPdXlFTiGqiy4yKR4dvnqKOfNLTpddc+f7xW7D1JmTgQXgCOGpiUg8paQzkCvYDEWyZ0/Im/qCXk336/mcXDjmEU4BkZxnHK3rXhdogkLwU0yfeQYEFVntM4kYk68GMwa4j6WL78ks+gOrlUi7g6UkXs2jFV5MPg00xVibkPonX+yqz/vQQLRGTdYV3RMTRknTYTcCvTjPv2iyLq2TWV1ioiBPyelEwpKvg/hWbKOqx4lHxpeekxdpDIRDC9Mg+iKTYFsGiZaEhwYucjEz/9xIppb0qFcLctFVCDsipJSpPUFuC7tQnd0MIUjgx+ehkKT9efDx8yn7jl1nFSdo+My6d2aPOCCpN9n3tglQjT5nbzJt/3fRn90FD4fdzo21/t4AmcM8+JkDq46BGe+3rz8cnxy1Pxx9br97+0E9n8RzrEuZL1vZquJbQ2fT90vNBUuNXnNDilBo4tsXQySJwgtV6zutaJKnPFJlg6s0zu88lGxQl/eMvRjszSpXU6K9PE9KG9tW365hV1GzY244gWEt/i1LwxJxwZtjdt/I5jjB08pb6IEFFt9BsnaMbV5+PSXNaXtwTM/MSKy/tY0068SRGF5lslt/L3UtKOG/u9QNOYvcACJRdniFYDFRVlGf/IxlBL6k1TLvVEjnzkHPTasnunfq2LPm11L8pj84LllmKxnFPEBilclhdc8dW5QYHlzEkBseXHhiPNU5nK2tIixLktklqgOyuDHRWniWqQW16YyRwDY3kI3JZTrjnvbGTeaD3fFwNBhfTcjjpE22zKfEbQu8TE27TMty1J9dEjD/4Pj4t7dHGxK+693EWJW950l0R1DeA2WLGQhTfeszqxahlM2rMuD0HdDyjs1cWnY8HPZYzeH8hnsU9h4479FhqXHxGCZC17+vnc3zKfIl7kF/HzdBAhqQAI/8EY+cpt11O6/gChwVC/i54mv0/8jGJzKeG+yO9GegM/wQxi3h5q0JOWhazpAUxTgEAv6ncPMb17BQxJAtqqmyZk63pKC0Vo6hcg+KLNlbGnYelreJ8zrSUFc8pA09mZ5eGcrtdAofGRQhRqIettOHRD1uB3NxQGQFBeW2pNrZ3b01GxIvzU09+iSCyPsc6eVBIy8gb9vTcH5lrILNoABW/fDtydHB2fHJn8qd/Lh/sq9eunuA1PctJeRMq4iXQVNh1h9NAHwDbXI4ky1ok+sP2f5Kli/tDG/KvYHaTTQPR1NuqXWwRVq4xEq9l7m8QB2Q5e1/2/DHwQZTj9pcOuaZQ0vzqmfOV4EUOPUVld+yzRtrEzhap4t1GZjoGyUwIwcFKl5qmcXY8dFuzprCj+LbUUmySiwqG2pJce0LD7MgcpjibEv9CaxL3Pr7BZUzkJYKZ48BI0eaawOwa+iqG2CMdqVlzW6LV5nO8bBAgNW5zN8hqAlkzDKnplODjBPRsdOtRYbK6a4o3kQTdYJCD4WNUec5LjRF4TkuMLQ9gTG1wWr0uLdKROluUjKp2REw4aYarKaDCWc8XxJxuTKg5U3EbB1WhsTdOMA4QMZWvBq+1LKOTB2PZRUHUkAcJFqBtsSV31G/R6KOovAoeo/hOOwNwrFnHQ4KG1g0KGoX8y++Gp+rEw8tk1VLrfX3U62gZWbS/rg3m/xhIwK2sD8VE6QpnGrCOXBHMEs6qUwPzBzk5MkRVQBOFfIHqn/pj+K8F0lb97I4WcyLyrbNg0L3csBXWN1JagXRitA8Q6jy/CuWRRCrZgABW9TKCuGe5GXUlqJ2+cTd0exNx9nGCPuylZRdrzedhfKtbeNFRmBkh698kXXG3ayQo3wggZNIEOrd5HYx/jqe3HBDCd4F/xaqBDnVqE/TIeRdwh4DHyl33+p9u8WWsGWw2by8giOiOZ9jW7AGWEIBW+w1202oFezpvHJVg8HMfEgoqmh77aSfLPoku2/WyDPE1LyLKg2iWMAXhhaxngo51GY3GDNV8Ot9q0luQ6zEDpF3UiaD15D9rcOjV/uf3p21PyrHTn8R+/4zDv6j/eZo/1B5gce/6WP/2HwZdoaTzZeL+VwI2rewxdJ3chFv3r+c1bbM5JaE/hap7tnioknOqrUEV+3D3YH2bHHspHoiuLg9taHk9PL2tP3RFl7TApJ6Hlb8c4GvjAKSAFrBfJG0DfH8siNunOc3MH2x//Hj0YfDIH53fPBb++gP6WYiChmNEQyqPAzovldSHTOZtW7IYPz9YkPDEUgKG/VnfR/YG9C99je24EukbpeXYUyR00qDV9Iu6hw6YNnzhlJLHUReGWEPrXCZcq7UI25pW240zMaXN5kGO3m6hQ2hVdOw4dbbhIkGowXLw9/C5s2tRqJYdzq4BMfmeBwfYeF2H0lYPEJOa3cZwNOf1VyEvNarcHY5CRIpsa2ykSPqGErKxDPZePiN1McJA5yGCcs2HKAYXdB8RGLs6susZalcnLM/Px4hLsJ8muAJr2KZ8gjRb7xnkL6cM1hnagOf3AAY7ljZ1G+R3l/KmsdjNpnM5+PgCW/jTRaxRIRIJm9jkgGabwvJRS2XKlXp2bC03/kitmX/AI+vLcZWXqEt0KEB9ZdmHoY/IH9SkGV9kX+rIU1MwRNoi0ejPwvHvdP5DOGweBLhFdqBqCjaG0BOYX42eXmIwV5hB9ulWhIeFSwSjYfrJaTwUlGzBRWZ6JL8kpgEGeeaPc65lKtibFnOMk8WitRYHT5bYGfX2cBaaXPOZmanzDLMZZvNJSL0iACLjiXiWy8OyjpwaImIV3pg+PZFbtlM11uGEd8SirLQhLX78MfrGme5rkxJhyyKDLAvCJbO4NKhsVKu15QpniYT7VP6abzyCqd/z+lnywTmarAEvze5uNC/UxW0Z8aDl2zictUj+6e7HG4mgdR3PKqSJre7jAnFD3KHszrZl+NudunzBlU7ht7CvtEaE7fvR1eDu3AMMN5DUfHJG37a/BlQlXaM4Jx4FD6J+ojqeg7FO+xiO+2gRy/fxkdhdIfCWZASeB+O45eTefvtWHnqw2F/ZjF+SwbXRZFm4a31+HIEKXe2JQ67bgRNG0+GASEklyG6cPWI2/WaqKsoxOvQN55g2dtamra6KrbVUR2rWhLJTtywpdXCt7s/+CLJATYzHOYGkgaqGGTcalyet8rnv0AT1LgTTXe85hgKSgsEqfkzBHBB8x50G0G5CckIR/CJCnvkAOoo6gWn6qFh1IBvAl4UalM1IOgjYlt3p8c2zHLZvbRZF3SN/CWM0iicdWEL95qsgw7Pukdgb/qrNRqUx2o41BxHBBkWxBDIOkDAqnbbVYgWg17S0vOaicsgITfyxNOPcU8XDjFxkhHgO5SIVsdN94oIb0w2mlwrkd3LoRDFvTzaeycZdKHup2w19YCjwH5VpSxFnFQYjMtA/2BByikUEXPx1SqOYAyMJ0ngzOlEFoWsTF0Lb47/yNS2fgYzGz6d4uYSEf46rlYzhIs6p/coUZAPNoHsAvI/xetw5jVBp7XIeS6AElS8nSlq6+ZBk8AgP5ZUDqGf4nHBInBp+9njdm8xiNrqfzdTdAA/H58cfjw5Oj1FsHMCuGlzOK4TooTf3z36ff9d++jD/st3R4eIuHxCZ4aBaffHIMXe49+uiFuprMyo3xuE3eFk0SMkqfNAAi0B/e9q91EKxs9UnZmyPDfTNjk0bXCAVjq7QIEWmPz0FrZbYq91B7kUAJcM6cD8AglTZkh9q57ZNq5F6bZuDR4hjx22F8rGh92PWzaJONx/R7mSX6/CefRiOLjuwygl3EjJl4jnLB/zSZHmvOLoDNhDJJb7j0vsUlpMfx+jdTg9t5oCl/oSMcuLSHrbAOR5gZ9+PHE9PDXeldLGlvJz7tO+VC79HQ8zXMpzIJd/ifU8bDZm+qoNnWPoRlH8ZXoZT8eX8eWAgZOeLgeCmbBz2Qe20rWydAZzhusDSJz5FnEXAC73RNJXZlLB6gJXi7Z703M+30HanR3R73SnL6jkEJuZmcJJ9IXQLpI7o0x7SoIzhoyzlzoFTH6+B3LnAVo9GYEA3PopsVS+x+Lkc9lGjdL7qtWUQZfyAgZfXUCcvrCHrgl7E2vVRIqKtb0o2YfOjE/OjNUkYJSWt6oizWnBNMBwcSUMWVxPQFi3h/7seI7shbv8ZaKFQciEUPwm9i/sIk+/lZ01yFErnpqsoN3nbndV3JDyOYj0W38/kvo+lsaxUse0Y7TDWFbV34HD80Qlpt5A+jB9VI4PQyyMF9Rg/tZ5wROdmD402CJVBvxYhg8b4hvW8g00nat9Z/f4iMdoBj++2z97dXzyHvzETx9++3D8+UO8/+Hw5PjtYbz/8eO7o/jd2w+f/og/v/1wePz5VN17L6/C0RooSl1zGetJzhrRNau7tMQIQjT0uPbL8CZq/6jf0wv4b+6Hvq8b6WHmJoh7fW+FgPOT+dUg2nwOzsNTeQLOYyLyHnQNNWUEMPIQ0yINWZVx5VYLclC4moeYVdGJwBsxP08ZGYm3lUQgwkE43Js3083RpAMwTgNP5h0Feyh9JIM0DwkaZGeT9lTdRnlJHapkKqnL3jJ0bF7Fzq3P+7dzJPPREiO1pbAhWo6bspo+tewLG4T1vNRfdZDhlhK1b3NEIGbEXhYEIlnKovlJzcNkdu2eSaTLwU7qx0DRm3NpNTU6avejzrH8BtI1FmjWs7XGXk3A+UHXkElJu7rYvUGknp3a2zcRUh2FUzXsswU+vKbHO4+MPmbS1Ql1L9bbDy+P/whQ5ezw7QfQsc8BV994zsS1Zdisliev/gjKu8WprDTYRSxhg/d3p/8BxtHPfYAJom4RFO21nQvG/D2E12ynOrjRDj3cpEzNS6j/m93TTfgYcDRjaPlBY71JjQfwGv3JnPU9i7thh/U4i1QHgQZF8jXpW/Qugn42Zxfm3ZYIC6PoFxxT7AzGRbVOsYa92eUrAw+jAE48t1+pw0kE0Aqlm5zcs0ERW9hiik1MwQ8ILcIWS+KmtZLWa1X/gUJe2JwNmq1wcg7VvUFzmG+FCxXHhREpQXiW0wUzRgWgfAUYYqjFejabfAEZUivFb4dONZ+Ps/qFEACVP5zMDKhwCxsrLYFf4KcIdJGebi7JNc0qI9H8Tv0HGLykLFQTdj8rod6mygGYDPX7bfyuqR3YPqWpwFADJZiki1F7ftV3BD44Lei0C2dk7bmmOlU/GhG4pjCZXdL++r3vKt98Muy1EVDLW8UPfWFovsE3UndBNIchAEohVAY/4XcVb4aJx7bFHaoSEmqOTDf/kEDhY/Sa0qtHh+sG4VpX/ZlzXbr2RsRmKcII4RRM7iaPqELVs0QYwIsGyybSiz0kBH7u/im8SuuGVCLovP3w69EB8Ase/6anI3qSkDY3uIMy14BbsAlMyhWu7PU7vW69isu9InReVW2Kyu7eX69Z2wnVTuAH7KNB8CsHlgG02uvMubBFinEQgbjqxznt4Hvv+uPL+ZVuMys4fRKeVKvVWOYWV/1bNXL0JE8P+EnW7UySD0d6QJSrtiXOBfiwoUyigcAFQuhXuoJeoh1YmuSymaRZ1YZAVqzfh/1sJ0jlobAzEzzloGN5iVYwXrw9Ofp4fHJWPB2MF1ExS19uq671NDuSRcGY/d+cCiP2LRVDJvbV4O8m7Sl8T3Gw7hEKwH0Pp6PI1lpFMWKM2X36S0DakFXWbEMImbLY957t2ORA1c/FaODuFCN0whKGCjAxLYNZ5aw1eRxqLjA96OHR6dnJp4Oz/bP9k7enL/cPfvMEagL7DbmZmjQah01+oiw18PKe2vn4dyR4F/tOLafvEW8unaVWXM0wdAx81LDs8gRxrQ81gRI9q0F4i2PlkdOe9zQNtEBaGxoOrC49Fy06EEF74JpVNpQBYO5w5bXRXXpBFRAe5YzzBZofGkmy67PuFbi0DWV8XjxmokVvA43Kjoo4yiXhUsuvyPnBsqpQglY9UCY7zxHX13Yc1NVLtYJiQALA9ZbV0wBLIKOM+YdK3RmQFdFZ8h7Ex5ThgPXHt+NDBepF4nY40KhksMMZa5G4LbAWCXK6MvqxanScdETmTOZQDuE2KUT6Fvab+tDHHnRy1wNlfpQziJTfszsPIxjOv1Kb2BNikbe6fJ0225teM+43qdvW7rLlAifrSidsrOZw8pI8AZJHCe6lx8zsF1QSYxpc9SgDn6tiGefQNx3bQlllGQLcjRq+8/hbg9l5awytRv89bw0X5y3w23qz8746V3sy7gLdSU4k5TrqFr4ie4bXhLx5OJ4PNpteszOZx8rnUU5tHE4HMRbOAUpmYeGw+EurC/PbriCVeuPwzTt+bHF8dEvwliDyEHh5Bi2YyvVx1zc2x5a36tnxOERubYThBPfCtjxuD1TosOMMDmLs7jHLkJl7t6LcHjFkJsK/wN98roLm2d10jiaCQj4p9nIsVmEopu1+Yx9upZRC5nCyiMDj3nfKv7pqUKGSrBW8PIgLtJJSZeKTXHWoZ4O1dVERTl9J9BugnX81m4xkKdWRehd7JuVr1CeB/1bx35oUH2o40Q3DZK5kwUNSY4flV+gecgw6ph7wgXoaMIPpHEPEwN14NRIGzCF9g6cGejKdCxMiqUJqQ1yBCkSgefJ1CgXu1pFPleQq5pdYDMDD6MxjThMQwBKkV2Lb4NXRICixs59Y25LZUehScc/dCnzWVfYgzt9SJhjNAQAK6wlX266SYz90RricipT5SURqTULxBil2g5w0ro7u3lDO38Q0DGlc+UI81+AJoE6Px++Tp0B/UT4/mxxOZtaHCdNHB877WglFo/Ld46SsU0vkfCyFRSzJE4HIugUd5eEiMaJKRmppCuMEtLW1ZbIjx0J+WvVu866OsynIDnobzJMbq6WXzu9oZBRqdgue0KlYUSWN7k03dpGrmQQVtgY9tP6YAkTwYAXmR1m2cTWDsLUhw3hhnq3h5tnMrlWRKCfc/FbabHCVjeMadVLeJn284BfN3Cq3AOyP8gJ2uJTxyKx2t4R42Z+f9MOhpw0vQnMRRVJBo0AALt7dl/e+bPEVkXmhy9Jg/izbILyS91V1gYmMNdiInH1QUCVSmDqGijV2wagZH3LwO7ZYAuo48gsdKDbU5JBXkK8ut4Tjm5D1vE6sGVHRM4JzQjpziE3KFAV7QI9eABtc0Vz7eDF8hQQ28Fd1ZmU1Zuk8p+l8xi+riaTuIQMW7Zl9q4ZMj27XbIVDHUFNgCXjq6Oee99FfGY6y6mrNAHKfdrJ/JETpHb+f/BFqVQbZfQNDCfgMHQprI0P737deIXY50DQfHizKnOpwS10ONQrnpU4CnZ+Jy1aJSmvbQLfpvo2bblKcgVpjxhOLoHmpo1/RRSqkh0A+xeXPKkp0raWijUA1C3OAoAC9d6a0Qsrx8EMwZVJ27L2NUw2q5HMnJk8bG2jVjP4Grk26UfK6J0sm7CAsqqgbKz213Mrjb4taDNojkGiFnOiDU59qo++TAZjAF1cj6LLeDyZD7r9OLwJ7+LxoPs1Bp0dj+Y9EnWioanJLxBRtaWexw0wiJMAPTf2ibepRkXYfq399GOzlBsatCOCWsC2DczZCTRvAz5S7wLH+gbiAAW7Vk760BihYmqi342pQxQ9Yo+RM9U9RyCHykSfdwG/hBxjMSZnPWTAwyexwTYOixo7JJiqN1e7mdaEtNrrIsOpxqxGtOb6KWLHH1QdslSGv8MArq6U8X+JVY5iT4XkyveTb+FdNJYy9oYcRC6uJo3Ddpig9kPKYHo9+l2Qtjw6fmdtgeXUBxupXyZszqOgzGfJZXQCeulzUiSKpyvoM1ljd8mteCfhuDcZCRbWbn3AbKqZcHTDZANAQWAJBFlL8AHvoNTNI0GShXZa/ueAtPNIOc/gQ9Md4Zy9/1dEbM5i0QgR7MavOnzhh0SU3a+cs3bdTb8DSkNu78K2TlrnLhGI1mlzDOmtWq7uJZznONRUR9ScFklIi1yEtHPseGCxEru1aUxGCbegqR5plSn4t1hXVV1YdBehBQbXuk0YM89ASHOZYNFcolqJM2M+QnPrtkjjdoyhkgA7q63A2wOQndNJLXgyXOn27KnprElQ2+NtCcBdRa+J4K410xu+1aDEdzXhN+D+spjNQPcSKNo4EfSM/NWEFUN/GsLZZwK+owZJ5yCr0F28moz6RdcIFAl9g5I96jph2MLuHEvxFxPiivP0qjJZBKHfRn1A+JsJB7UV5bvEchqI1Kd3sr5rYXnng6D7YriIrsgHztwDrag+JzVSJ6pvUFRfpt2L5LgqFB0ixrBl0SL6gv9RBwFEHtIqGlCzovRqZdwDYCYJX5y3BuV3yn5Bc1bqsqL15JVxnwy2uZcxIfwDMp3oGMlh3atwRtg5q7UoKHweKLNzEwWbQQ9L2RWL7HtlO/F5C7kOLyZdOMVgCK7YOUgz1Cwixi3sWK87/L+fJ7Me6SVqUhUK6w6uwvFlf9aMRRQARs9DpVc7YUZqutVtgWM4uG6LEF6wcaFNBS88GamKISxhBi7hA8XXkCbvCX+OBfkKOilCfGdjtSag1UoUZPQywR0IpytY6nIc+J7NafQ141Meh1pG4vChPLi102tqCSM06a9iJZK8TZUETwYRGNGcbJ5F6Wzgu1QmeIfdfbF4ysDNRvpHtKeCPes14w0+h63qMJyHEN7HvQ6cro+J5Ag1hknrUR0zmXlNLf67hU3nqH4mpxFxm8E5riyQc2hFwZNbzRXrL/HTe24Gb3Xq58TD2aMPJ8dqPUbfbtV6tn8Ig7ZtN/Fs0WN8P/uYDNgs+U7JdYIAU2U3mkPbjIxXhVQ8MRPQUn+d78kLwLLs2PPS7CspKdIV7WhOLJi0VtQyh1dJs6DCiT3fxNxq96LkcFXjZxoaqW5nVVbEdl7ahadtcKYCDY+3P0QFNiSzrZ+0ssNBFHwmq9sn8mf1wMgF2Mb+dHBq7A5Orvzp3UnaXNZvQuk+2S4RnkNts+pB9MORChmHKB7sLHLLV+e8zmDMFBdIA655rolzTT19qutbToHtmXu8958eH/wWn54dHn86g/8cnZxQKTLsZTywvQv4gH4P40JMy/mrT4U9AZXkfuxcLe55S0+SFdvYrV5z8JavJkNq2RvR2PWQfYQ6jASlkqhjbmM7OtcJBHmp/X9DqedlL6Ecs6245WBWFDAd86w+XqZnopMRBiEPnnjzOdrSIGLtjDLtmASPX7GCKy5U4sEfAM4sky7NyPQ6kZnIOm9Y/8KNUgDBY0ftUFvJNGXu+xTwBPZA7wpuEFzFUXg56Lb/u5jM+8oznnaxdmRvCmnPPXNryCIZIkVggAYOptEwVOFCxP4d8D0Aq8Y603fBQkbn2cxCvP9yood+mxv9EcbgCAK6zLHafmQyBaWOoQw/kauSMfg7aBYJ8AHJvOKXSIs28rOHd9rSzcjXVhdXtUvcQke33f4Unm1yaNj1UH8aHs3zaKERpHvPMdbBix1Fl/RWUN58rp7Ye6Z+hptpYgfxMj9ddIaI6gZOBXyRGe8nuB0gwreaU3MtQ+kOC34jQ7g5b6f5AUMaTftAfoG+pHIgqP+GY4Xi0+gp3g4Pz5ZEuT+6ESTbTdReAEl2TAlvVGuMCTaJCobBG+QTiOfYf1BGgi9nm/sgfkA1BOhtsZty1kUNFyAKhyYv7dpuIyUBul+dT7z3WKnI9Bn3x3C6cdtSdJuUIAQ4n1MEYE5N7AEEcbgcXKhYNrhxOa1+ZPHHTqVLDxKyoHtcHpQSRoPl7P6/tA9870jVUrP5z/7X7JGDzpPd3K7p4z4WdEbESsJpSntXI9KC0nYGd0N29oMBGAy4IJZzAWR0r68NAsOkiVPNCGaea0xZopZvi15RkKuj1wZFtRmtOOwE+BBu+ht8f7pbINu8yG6ls1+V1NW4WW6NTBLYUYTEK7pOncU+wyVrLoTRwa6MKacYzj1NHIe360s6DnExseVCUqqwym5sTn6k4PzHMx163MvBMDqElMDF1NLQvW1kZEBHJzMY07/lWcAqTlCraTbXiGlKXRvWiSoX8Ct0N7qWbinSrK7PSrbTqiM4tSlzJKd+aDfWhUCyN7RVCz0iAQ3uqzBjtIPh6mHazi6sx+SKowDD+JrEN+G79VgqMeblVDy3q1x37cj7oEuVozHCW/WmXwfDIaOU6Eb9HBcJTeGuwSvJOkjiB2KLqLkXwzPAwhW5wiRqfd1Mg02WmNEt0JQcamQsaELmjO6i/w4HTQzzD19Cnw0Ql8fAOBh/3D89hZ7MGGS6vNaGeiSIoWr9zaBJQWo5z3vzubKeauEhiX07sjgcfkAnpFUqn7NUiER120iIUa9ksDWpq/0MyWGp5FAbMWqEOc3pjuHEaNrnttxHTRMBELslLewbyRW1uxdtdaGU6OqNpCBJCcCShSmRYc2ioTV1bW4oHDSnE6S32sjGKeo11gF8Q68Hz0Wddg4IMUbr2AV1fwmSphEhH7BXQY3AX3y/2NRRr/1I6jZZFLbZfrSpQxtA/WX/VxO3bIyd3C3SsPzC4tyJREQLVJV9XOywUVaK6nH2kVyEBsYnFjk3rZIIoALgGOaKfPMZdUGBwxONhmrHqiKJhhXopOIr3KCUo7ONS6WEOYRHm5vt+zKmtU4wSdLD2SoUnfLZ5iZPWF/zsqrZwD2fOUN69IiJ9sWkdHRmKPdQ5VL2Ld5rNEAE55kY0phybh7n6KiAWxGUl7+k1nkfcpeIMMIDvIxORsn2+YRHz+9oFyuooUeFuLI8eRYEy8ps2aVA9eFbCiRRue37MvVZsT0TEcx0sSwFv5S6I4PHHvwWi9CQM4udd3V6Bi6qruzigbWEHOrHqdDfxeIkBeISvfxWJREvV+3P9FDcjdkhjcpjckC3GW8jm4rVIHa2mH0dwPpUPwZXjBRQM8x+niyG/W9cdNv2Sd63tMopo7uTRUsJILOVW2tLWcCResRqNkd43NsP2LRCICJulEfEw8TpYdzTPVkOb2A06X4l88MeSX4DGzh6WlpmG9lNsID1CBQfbkSSCRowTMuzdGGaVHnAxLWHx8cnFJniXhh8l3wqiIxCQM7iBnWUvwnyhTTP+VSGfgWb+d+e7DtleG2zNm0T/QqyTXZYp4OLxDDRWn9fsNdOlaPyRr1Wq9QSXpgZTI4aFr1pPO9OPfDd7EBZwwLh7ypeeOfVcDLpSTYTeVf87dIKjFtWsthsaVjMb0Lil0BO6LxQSy2g1KQZhKF1ZcKbE1RNX5QvXrfknJk0h0qPBHsIfnaWLoGcCPG2QaagmNFxy8bGyfAQ6m2/11P/nt1N+zHLvSHhyd9BoHWDYSCCsS47biP1i19OcoTmk+6n480/S4utS81FXEyNcdyx5vSzrGLQSmERh9LCC5padQvyN3u6j1+weOXUL/n4iPg+G2Y+BA7PXuu41Mbh8Z1MSCJ76brOsaTI1XyFRTIcQANW7By0cfDp5N3xxzMk0Eb+bHxiuc9vBJABJ+XEYJU8WftaWlwSOJiMx30c6mcH4H1QuiXz0E/Q6bt/qa7lmUjEZx4HxHhTdcw6XkBgaQZuE9ENaDZaK2XNWSprO9JiwXncdgtaLeLXR2fqUbXW1LtrWCTJ2EXxw401PfxrOuo0lDd0Am9HGg/lb75AKlNs2+TeCW4UqTtqHPG6cHzTpNKPWL2r9YaSrCmpqUpnw6/Q2VJaHfjza+oXz9V9aZvsXIz1dXMx5itBgiXPlUOySMYpl8cjQgzw/rN/IfmyijMmYAqOH6H3QQ1ZTfLzD35rhcBxw9ILarIfitw5fnkrfY+PUrxEa7k2Dp23FmQXoVsy4NL4y+kTeRvxA4V8M/srXAOU9gvc4zayb1h/Hgi/u3ShyqO0crJ8o1Uu7HLlF9eXGiS1uGJcZSdH//l0pP5LAaM61S+/BE+iq3A4pODOSvglim7qSPxJ/iHULiuTe2oVcx/6LVjbdMY1Xd3V5J/eDt2Q3Eid2wKdFt42+UV6rQNVG71pBDdhj3qMjoFOd621+aS4qzfqCbEDHQ1aRMOETqL7iT/GJ/FpfOBluNFuN020ztKEgaZ14xhMkyntScbUEftIbh28cPMjUH3qWZ8Cx5Jmnagg60Rg8ytH6za6IsmR4zmriIVLkvpFG1q9SC8fBKxhGoNQmaylDqoMud2gyBq0yL+W1KO1iRHgJih1c0BbXmF/PBnfjSaLyO3HsDUltI40XRahc/ygYFpRWqhdyCQcZb5kq+NNb9itn1BDzVIjc1NtYgvgOIeYwwGludIYbVw6dPH/w3kl5GAqa4djlazYw1poO+oqzCNAFiB/u5yGZ2e27NiobRDiGhvWotwPSrHBuDf3XsBX0WeXu8hqJzFk1IvZ0LwntNSljUzCTvNzCMho4m+x+6NTJTZsMFjmV8YjeqCIuq6yorXu+3ua7Te2NOkh7tD/J3taLpvdO2Pk6Hgete9dIZ02INQXtOAjjVZTUIxmWHQ+IXNYHhkTrtertfsx64x2kzUhLIF/sgSKYBpqhxZkQ7HmSStFjARlKx5A3WzorhbDUCaIfYbbW22JYigqJTN9/MAzShb5INUdvF0liEVKDyyroUBfVlmES602AqkjyluVkgVKlMpBuSSCJFmCTiV3P5EH/yrTB9rb0yOydNR0mCuLrsKX4dJbREp1w/SRqoujGR04QMrVzZjb1arhJ4WP9fB5lKGG3BzMrBjbEZiGFPMXGfang9nZe12rE+fgIcvBRZYMJ1Dt7OxLEDFU3a1JfL/7WXpC3VDV6vLfsDJjFNMIMpHmHeEQreJTXlDLdNFIBte9Gk16HtkHPw4qXisorD1W+1ltmSgnca783E40Yc8UMrQSBskG+uimc7sg5c59QmJs/4uB0TUBp5EmhTNKsUu0dCc45Pq42O5ZfaUJEoRkJRj7ZSmXD55fmROoFQY+BL5hnqCaXlUSI4B3v88cH0jsUimraiutaNOJXiPQeFhWM/fD/V9eJoVjCwDA8JQ3tpeuY+BBZcU3dd8KjpTmIv2RH93b8zScY0k90ZqZNalTXzFHBlWcjLqoXYWidu++slFbWjwF6eItcnb5pX8Fqg38PS1wkfaQiM1WsJR6nuH3ciouMM7BubeHlBSTWQ+/pxZDsEkKLJxO9g3q0pEasBh18NdSd4eeZH07mQpcnZdP8+1mMT52HB2me9+pRNqe+72ua2cQRHBls7JMtuluE8VXsk032dXQ6s764bytntZggk3MDB7Vxe+chS4yZS6W3YUxk6Z2pBW0BEr4oXqZT7WlBhoHf5nUfbAlR/iWnjfllhDmAi1uqRsq1zL6BmoUEyQQRuVa1p7eMKRKlqebxG61c7JIc+CwwTqN+fbgNQr5lI1eRXZuIvWk0DfbSoju5nJMVvnQ9veouZco9qGVm8+nyrWIppMxwu17kkzfsa8pm59ltZbUI2n7uezbbT+erFOR2xYig/ugClScNVj0ZTjEgz1Kq38sLXqlus7WBfUgS1Z0u1bJiOd4oB4Qlwazca5u0lO7kNqoHfVBQ++eY1hQg7svA1fOLZNzRjw6/Y2HU1GpZ16VPOT/gj/u+N2rKB3YH19tff8vO+LMlefWtFMXj/wWLmSP33S2lmR1xlth73NApeTvtfrT82STlHq6dSh6YNuVh7vgDs1jYXbxgxQ5wnatngHGsZNxif3C+wWI8TmcxNDS3iAFkoUINA/dKDw+OdF8Y1icId2SluDs7osfKUdrmJrhRE+gRnI6ctXW6qFT8uSQcq+LwUsUwKorei4k9EGPDQzNQ85ABWikAAIR5IPNc4Q3IhnCfZ1doAzSqO0akUZZKFyavBaO4vx+i0AUDKAXRBecjWaIl1xSnrvjlK3ynlHw9ZAkNpoOYWoTKoumAQF6l8R+l1YHb61p8hQDUaioqCWMSPQH8V9lGLGyteqRQQY9iKxhoK6U7YedCMsorgkkDDdTdhM0hDYLXrQGWJcy/cfLNv51kk/YctGBg6gzQbC+laSnAnkqbLewgY4ae5lhupHRow2y1bhTxtFeFPBVat+iy+C4E/kIy3UKjrNHD3/odWpAVg1oqpPFVLut1KlG0GkFkqVBI/nUsJ+yy854+2byB65PwpbDy4TneEl8zi+/qB+r5NJvo2tjxyIVRIua0CTp1uivSP58bw9DFyNbYqoJq/DY6iu9oJmocqj7f8YtdklbTvyNDTeuyf0zKD6aqnWd1rT5n5mhGW+TiqgblDMltCtSqGgGTcdqQ/jjWtUlsU3ds+sKeN97P8vvqFd1Y0hqQfMKsdmGyg0wOb6hG6JDfQYtA0miuSHPtiqVpNcPbKTVZXLvdKYd5mkqjQdAF3hlbZx2R2fHJ7//59Pb32C8tzTBkNqmE14thmFWxBQU1NLlDd1R6hbHxm8sE9/ZI6CGpTVQ2Dn3sPue/k3+pMYVuRWdhCQQIQy0ucBsDOTSrDQagBUCZVaExfhBKiRkGG7P+83RFHL/CyPNQkhsYUWyIV2WIXObivCrZc70VOEUPz9VV2IDwo1ESl6nbVZhSO+FXkmtUu0OVRBER+FAXidcmKyy6iYVPcsXzkK86/YbUWDHQOPe/haOfgU27R377eRMrWqweInljtAx42Q/+2qU48d0v5X+Nzl/Iq9ssUSWOo2OcCqrvY66LrSt2m7JQnwH27eTcCm/A8uje/F4P3wQapixaBEZU61mI/fRI7SLZ2Q+V5hQ9HUS+bOlA/R3+0eDqjB0qW2Nt4ayNDBBn07wSIejwkrqbNwYAlfSvyjtUPTVlnrpuDK0gGsaVIJ0lJWSG8y1RHKrsULxMBskF6S4i6x9HWrWvQuQbUH5lkF3Ekf/HcZfpv0msjHFaoziL1E8v7X1+5JlK7sghfZJPdBzoSkEZK50SwUPFqxYR4K0aU3ix0vWr8oMU8QaX/BkT93j/Xlq6m9RbgdYD+BLWeV5hxqiEEQh3Ax64dWlDoPRfIEjlkOu5SAqABahjcFcbRnTW6+P4J0aEBLzO4BVwMMaKA9ObwKGvI38DPqtA4AyyHtekICZRetcKxFTxmSVSdxYGv7mJ+VUfYcDwVQvk3iVSKTpcJHAGjEoWxe7YqhwSEgP9+AWUuZw8Sc90AYBcRqfZCEg7HpV1fwkJRekxlrWV+mmTzSWJdMYIkdlpZxCa/ysVuHP5Nb+DMJQ5LXnefbu5qi46KFazHNtjMw96bGXyKFF5HqQrtixj99j4qLCxWwyOrgKZwdqAeyIS16htsrkgAX4MOD9W/yosgQG723D1ccU3gF5Bs/EFhWUFeiGRMyTw/jRow0SPAA1UjWr6hJY4Nt7BJvsFgP/OS9n+PjnovRq81hiuqmc0gnmR2eVxPGBf4xfA+AlPrEeuFQZGrqykScTQXBwDp/waonGrDfpLkaI8hC/IrCKv9AaOOtaUVrTzA3lDxeKmwRhbZKgmkam7Bat4nFWNhXpKSulWrIkmYccgMFF6HWTILTLCaNdDGT1w/Au8DntbY0+4W+fwSTcZt1SMq3tCTYyC97Z/YZxKanJNSg0ma80pO+WK0ajExjgCHprICWEq9PEngE0MlvBjHl+D0KW9AQSHweSGBZ3wVZd6l2uzVqFmLNv1VgAy8LZt5xS+dSpTHXpl5Neuwniph5pGqGjCaGHrojbp/qMSCsA7UMTeAzkPRl0xRamy5p9YBzWn1PnCkVmej6ZZKqfaHnWi2lLXB7LUcdNNsMTFOcL+x/sWZezJGXi4c10U9jH4uHd+Da+6M+7VwCK9QjeRKU80IcJQO2vhhUPnoPqHmHxuKqq1OSiRtN8H6qjyo721Q+qq1ZuYDy9m19xpwecWkpSZAgRRUC0qLUlR5za5aGaX83ULLJQdDmrEcuTonAbuvTYhsAiwKGGvxkv2P508lZKvCufI7atYIb5hZ4TBZ7s+CRbzA1VB6VzQw5V2TDn2wBZPGuNmgeMWa/tShrVkN4hdYJOeSIYLFe2MFiGpUs02e6KpUU719rd4LlSjiqiHBUiRDhTXmi6h8uPZ32FHjnVauk2tktiCdU3o/64J9ds1H14ND+Gd2Cp3yPakDfSvedSzuFEpSQV7V09TX0EX0y/ZzkjZWP5KB4t8EXsaf9T+yjykaP4bRyGxG/QSaNFJ7KGF34OcYS9PL/abNJrcY5dkE7ecPJsI7tn2an8v3gAtwP7gLO9OPa/suQgHEfh5r7KhUjQA/1O80LT9pEeasex3SciDi01MnHdjxwQHEyPkz7KIURJHLdVaoKxR68qM5wxLevmrO4UcCUdGXmKqBhzAkpLyiEV/b0MM2wITx0qdkBN3uTxP+rvH3GdJx3Cn7DfHAGI1yfEijVOuQx1IacopzcOPWPzri6r7DDWFVmN346z7D6DrP5pBk3TCModuNNOl9vkbszQYRaw4qcNHNc9LduQWf3MAaPYZThWESikNfpROOzHl5NJ76Y/VH9FN/3OSF1P3A3HYW8QjkG2PJorA0KHew4JKV8T0YqvAm5mJL30Zqo3iFTkUwb3w7eNpy94djesSgB65dQ/W0y0g4tZOOobpVX0TByXhBYCVCfJSlxRF5U8FRcOzD/sJp8iG/e3IgPjPEjU5SqnYMHWShehK7kl24m3hooOszHLKwwdBl++DmRSWZ9GIpBtZMexj0xMKtWUMqpG9Cy9dYrMMPlhognBbe3RqYJEXPnzed4xMrKQODng2/ZED+OW7lP+0WG8mI1+YCRxsCrgFtRYOm+V+XbqUlFGNyalpB96MmgXnMhbXSRkmFDNLfPM0izkUGWrCfCZuTyXma/skUM/EHAZD4YHsEpvphfd9gWIzM7a3Vm/N5hHbdpdUuvWmlSrv6VvyNTvVk5Z7iqkgnIRkm5NyMZGzWLQlOCyvPKzMvMTy2MCVgPdt8Ey5FO+SIwVeVRZ9szsYTxmmDL1tzNgTCuAOLY74Hac0E6V8JFSKnXqtAlRZs98wnuIBginwWqyRvVQgI7nbPR8twiiieo/AJMyARR2v1BOcUPatomFdzvDG3lk6TqbG0cXKauPczUtqC4YSi4XYwCNVeGf05ylifYmveVblSLeSPPyk5blftBsr7741UAhSJKCLyQLCwl9MQZxVbCzlhfffO66Pxtc3LVvpp6ed876cNWPaB7IZ2ZN6fvHBtPdIH+eN7ajL1xtzokt70zOy7s6Ee9u2XGinftUy2a2GLdvu8o18Owr4vhfXUZpabZLawt3ncKslW+73+p3FtNeOO/TT8k1OhcsfRONksH+IhUvaodZV81y91Rtw6LOyqG8mfY6CbcX30OKujaLU+VMVRDOBqz61pTOsQAorG+4IXy2lEDZI8wX/aFukS9MX5JmC5LT6tDOMVB5BixyUIX8juovJmFB6mDGkQYJZV3KlrviQGZosx5KzlHI0a6ttqOSXfUtrQYHkScHvjs+2D97e/wBtsTRXdhFQbegGA0ux4MxX1uZwHnsB6v7yvMtEQtwSuuiJQTVtX99T1KuKZOmlbk9ExSAdFpnopZuz4kKPMTOtYLipvV9fA5x4urr2eJh/7+8ep74KiKfTqIkcjDxSM2vr1Jjkr5Xe/0u+dcrPzR2VFquOes5UYFJbTHGubd5haztOHWczRcJpzQJl2QRZKkV3Nz4WRsvMoQaZ/Q91yMwPh17H9oSb6f6vNPZrOSFYxEz/z0ZuidkCEpCA2iAj+SFQAZrsphOJbAs47fK5/kkQxhfObpfQZH+U6BaaoHBth0Yx8+QGDibzNHKHoB3y/fYMOxjgZP7z/Ca08ZIOFRWxGWcK3vz4Ut3ZMo5wq/5qXJyF36ujd+N/rrr+OXD8PWru//4ja9xdzRc/HVXG3VHjfl//Kur7tcP13+9/jSJe29+nXZG3cVf41+vO58+TP7849fDjn/i0K+ZnAT5DrBtAC4dV05Mzx0WgL0ipBTSKJGmj932vjp6CNKJPHvhmxI5HMlai3aXrN61Mdwp07CkT8j5P/i1RNuL3glZGkpN/Ucv2Fw8gsKNSyMIhTJZAuGbk1L3cHL97mavudc0Y8aZRM86E1MF5hEJ0DTkJ3burFEifcZUCcQuVmijYe3vj5qsgrsqIaSLVhm5cruvlhhzodieAz6k61hWpa6uV6kAxenfFVnyZFgXUUD8EP4AB6+ioyIuPT3Yy0AWuKK9pkbJlwKJ5exrPIS//A5bi9cSXiNEDBAfr9bvy5pTuaaeWUYG4P+Mf0InDjg8+l7onppCpLBYs4pERN1NhTcAwSx31h7v7T4PNrGIU97O6JGOXHQB/IgOVFKJca/JVYDaxvJ/oVVZo/vUK6vPRwNC/lHfsmBCeLDIx6w6hur/6PnhxniJqdt/p/njKI8Em8dBZLi1CuxwWUFW6vHXpGvs/607Wrkh9fzesHfwctJ7c3LT/aaMYOXlVbdyMvxT7TvvRh+uO6eNuz//6F4bCySDn3Xd2rn8Z/sEbUhRO+qPbe9M4p5Htl3MucI5QbpWCBw3WCr0To5eHZ0cnZh6YJy1/itAktRGliRzJLOryoz246Dq6eiSlzS9u9EylN7JMGPS1bYT1r9Z+Ca7zjmORonqwrjkKTMCewclR7zgAZVVa2hTWY3v2gSKW7NqMisBZhlUb/puBMihzSqCMMQi6j/wuMwJRAnBarq08P3gw3R4uvydOBWyHnHCgW0ZaaMHaiy+yYq8aDIqUz6QM6CksrQEsFNitf1LKsf8StqVVhssLGSaahdWXatil2byPGrUZ5FRccZCN2I0XjR/RLHb6Wo0qMHxJaMEATMI+MHOiLJEbIGRPR3TTZgAsGdr1oL77qQMnMpq4BTtq1CSt+0rFe4by2QA4696w+Z/cmZfuZzyeE3ZLnGNcCP7diV8pUPKgG37h7Pbm60kVdWuM2UM4b4TEooDa+esDA1mWS/citwElk3ZjGU8H58vXnu4sqSrqwaO8nQ6P4tM7gRyKwYAWngJ9IjjwfhLCDP3rXKGh4OuWjj5u8liBii8cKac4/4GzEDUgsqrqH1iPr9BrQd0yNexSnMvuIKme+rTUdj9umG/uOv3+2F4dYXQAz5D1i2Qx2U7Ee5TyssjpSxEpWRJf+b1NkRgY37u5ZI29BXGGtsOouD5QVLcA4SpEWLPm83JLopbfiV+5ta7nQ8tFnM5XEsPaFCx+Rmb2d6y4TXLuus8SIP51q207HpGzkiizqw8SCohkCrEii31ZZzVACqPi3tudayBWYoNTxK+CGbxDTAyK1KyiN+TCZVd6VZdJrnfDcfJ7qg/D9WUhK9t9v+7GFwHJmSa9S+UW3slRaFKKROp3yjXRF0mg39ZRqnHM8ixQlkkEQ6PHI15Mh9fwdPuFEQvgPWZLOu6/+7kaP/wz/bJpw/t4Gbdsp1Bjx4lM1RhCQccUSm7Vs1NmigPEiRqLqqr7yFU2pAJNYjkvbGdvnmNMQ5tSYGaSdlbXcJ8pMYtr2h92xb0hFYTTot7WnIJnFxa8ldNJ6T1FQe1ZOrTtnosFfthWcy6yN1Istq+1t6m4boZ9OZX8VV/cHk1lxsqWe3PgVsubSAfvOFLfcA7Mq5RjPhG9jg48QLJ3gjAocilE9wGp7qYzvDra2jDspRuYMn+/XMwhmY0KrQru25ZXvyQ3BxiXlUfRzf9/ry8Bbbj81U4x8cKJ1UmXf17NeiRGwfwaBWno1EWd2JbwogfcXq1k6Wt1z9noLRzBku97aEdCm/OjZwhXC/hkbOcYfE0kkVKmHzwUP2STl5bxUD4yR01At6DqVK8wh0u8pGB3FNfEgCUmSMNq23Ujhqs6emQB7q4z1X9/nbH00NUD7iXJMVfyvygDFtjkEJeN5CV3q9moIpWIa8niznQ4nQWFxd9gNoL5jqpKl6yYde7z0kzorF8YES4k0/ceDOfbAIsnFBEtBKlWN+ofGT/ROe54GNrpWUCXcUjgO6nX/8XXKp8OpMEa4ov8KAb+YOe+L/rYZNrdRJr7mQlnno/VUpzGgu+yzVo14lAVUOcNJJDv9fxKD6GEGyP2qYdmTgbY4dm727Y36PIWNmpXn/MEzZps3HQe9NbM70IEw9a8bLvO3OcQIelf2LATbBoM6UmqTmdO85EYeRE5qaGmyIDhtL0Jxl8cVYDRc6S4GOF8P5sNEDnL2FJ0ssrxUfaJEJSKrfCLk/IECuyROJ9h9yGl2BOTcsHKUo38Fi779ZFvzV8CzL4AKzkwZFxIJyZnWY8x0RMI/Mw4mGxICbodsJfBlfiO3/y9WVfHOiUOhe2ikK0mgUqcaERqRmMztv2ttO9oAW5X2SAke21yqADjbWxyW2zLZSVjjDi9gLPiUn9YwtZ0QMo1mpOdImLMOTxM1SAZOh1q2mqdRV5zKrEY+YL3Jv3sMpDYBy0vWLTNTYwl3DiEZFxfq55DVKBvXJ+xalpgYsKHYRQyETFAeWObexoxoMNnWXMiQ6O7nqL93Az8Jqrkw7qp8ZWv4xAXnvcooUENksLf2+xUNsY34AU5B2XKCPlklkIMfuh3g1J5z6bHNLaHR7aHjKXp0X2RU06bm/1g1xMfGnE51+xFkHeM1t169H5etOBfj94gdH6L7/Y2ctM7ACV6lvtzfPrbk8sSXQzmFstFb6NfqqCeiMXl3WNraPCma/wTZYY5ycmqE8479HJyfFJ+/PH9v4BAGTUnanXv7cPDunVwaED1MulFVndoUJKfx+k062hWnV3m2377lJHSYu+6+LjaK+EjSbB3doh0oOEDTdOFta3GxMwAdy67E8XELfN2yYvITle5iOIKAkcrRvr7WVMa5IQqFd/bO7QVf3j6WNPDGdWMCaOgR/XzW4PCd7IHvGAVHBAYND/7XT5dPLun08UcAWrFSPWBdwJZCkLbCLRrhZwKpcRTMzfxE7fbRSNIAOo7vVeAiMPFU1yoBdDnd38JawZlsrPHmOuoU0aYzTgKnYFAv71Iv7iC3WpoiCb+BQ/Cp7cP27nNG090dVr9noqRPDmibT3sBYw9yY2iF/rcBL+2Ei8Xluj+cTnQZowoMzM2WdigD4+l1iZaIl6Npb2UQZNlENZiHDz2/7mX6XNRjG/1wbQ7ZI1InIbuG3gYC+b/MuwnaFy2BM0U+bK1ZeewZ72DKqEcAKYdUv6lI/iU6Bdr1VtaGqa4pQ/0KK4NAxJBwYjmNTY05jLI/C05oYQ3kQpwQ34h/3VtePf1mSYwcxvQZKO8jk/QTbnsZViUrf6GK4ePuxMZr3+bO9x6WkT3sMwQf9F4QL9SedGRvZyue7Md3mOucd07hiFH/JrsQMxw7Y+L58L/o6DX5Tb4/3wV2g+1DZ44VSJYaqG+mq56aAbxVeTecxNNnGuuxhF8EZnOLn5MukAS0N/HE1m/V58PQgvZ2EMzJaDKB72rwfKCMbq/6OwNxnG40WvH3f7w35nNpjfqUhxNp4048vwLp731SmacfTfxQBq2Moe9CP8b3yx6H6N54O5+nTYjzqDcAwOAlTPz/M4iR/vLP8f")));
$gX_FlexDBShe = unserialize(gzinflate(/*1574083581*/base64_decode("S7QysKquBQA=")));
$gXX_FlexDBShe = unserialize(gzinflate(/*1574083581*/base64_decode("S7QysKquBQA=")));
$g_ExceptFlex = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$g_AdwareSig = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$g_PhishingSig = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$g_JSVirSig = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$gX_JSVirSig = unserialize(gzinflate(/*1574083581*/base64_decode("nViNc9rKEf9XbKZxwYCEAAEWlv0SN29e3ry0M0k606nPzZzRCZQISZUOYx7wv3d3706ID3uSjhOE7nb39nZ/+wX3Ro63jrzOuPBGfa92XUzyKJOsaMY8mS74VPi/8yf+2awW+cS/5+0/37b/3WlffX1o2ntvLGgyK5tlrLi8ubaVrJvaOPIckN8febUgnSzmIpHMWuaRFEDH6vCxSEQx4Zl6v/9r7WHdaTnbN70J8naBd3BV5Z0K+T4W+L14t/rCp3/nc6H4ZoIH+GQNdt9hD8ziWSaS4G4WxQGrc9ZAgT0Q2AVloozVZ2ki2CYNgGET5VHBNt+jJIgF0vW10sWc5zJTlI8xn3x/FHm+Ypu5/A4LPOBsswSmdFmcERXyusDb63i1STrPeMw2IuYR8ociScSEbWZgmTRDygFq0/VqL1MMkQIMwOrhIpnIKE3gKq1H1lhHIavb8JIEORKOUGEgjMIcLWIVchXDYxkFcgam9eF/adxO9lx+HyPzFTC7LtxWgtfSwrgm/Drjq0LCreGlRQuJEGCh8jUNw0JI8jLCyOk4VV+BW1FjP1nE8ViZCbwSBO+fcHvn7DhVnqOX1u6icMsnnp+VYsTy7Pc79UJHIrIcMN9Mysyz7VBmzKINhM1wVIH0pVxlwi/PkOJZ2t8A3Gq/XAdCQrl514KnaQrAi0k0Amgw9Go1NFITPlCrv3HAM6hL8PwSzellDJs7WzymweoAkiQPgdaD6GMW2Ec8/yMky6MGH97lACtBkIazCL4OYmsEnvJ3knMRAiZFjuftRaRfhlb9cIsEIgPYF0L7/XOGV6ATEJNDMOr1ebt9gq3dvtFWtZq3ZZwjsX2amoQijHugtniCgNDoCsQkDcQ/P324gziB0Ekk7RA9onkIWFqCpZT64TUmGFS7vPn90YFNNJYsTfbA6p8B0cmUZF5pUABg79L0e6Tkfu08Hyrd+oll8kq3Y6L0gWQ+ATCUrm2HHqX/upQMe5UguT8l9R7vaq5AfF2dt+xpC2+n85xVZHGkI4k9EGFPH2DCLU4nnKLncQsM9YQ/RVMu05xZi0Lkb6egA/H1dSpCUbnIINUpMz5glrknEpfCDWRH7P4llYH8YP0VSvjSwpcG/q21xi8z+HAXUmSgc91hRUHbK4czK8zT+d2M53eAMYq7eKVNiVh0O+qmx/riX6NNWm1Z/dcyFyE1sSM0BxCwY43NPXYCaQWhECM7FLJ7FGIUI2FXlDSxHoWq/u3psksJx9G+53vb1D18dvXFEHXnJu+3HU1SXK6pDlK6dvqnjYi0u3agzJ0n0mYlY9q2ibvm636g8zEQenDxLBfTr3MuJzN1lV9YfcXx2mwDaXcaY91NCaI9jAEHC2vI40Jsx/vJbt90JGtvSd9/82N0+8LZZl88Fc1eTyPh5FWPU6410XtvwWuRSgr/gY8uCcP4cwZezSJbWlB1LGZZNtWzHkaeC0VHORjlVE19lNmb2oVvpcE4STFh8/85bKh7BINlfYYhf1Uho8FIZ7H9S6p/5isRYlz0QNWywGivgDCbXR40npc2dWyI6OFeaoVMgvG4Szj7C81XXxvk5D7i1HWwAStEHPq+D40Zu+14DjhQYcX6VuCzxaFH22teiB9BCxe54MEilr5zQYuInKthNfZyaF5XnyX2EUXTh6gl2dhBxkIXNIpd+D+JBc8/JFLkWEwPUgaZqIDkkE+w66PTCFqgQ8Xe6nmrDOdqY6sCe4MOU6X2VvuYMTSM9rBHPIQlVydRg4UdwCsVeCdOGQRx5AzRtdgS4Wk609DuqNydyXl8uHt1vKv6XWq7EQD97k+UBQKl6+jKh2FRCyGnF18LYLO/FehaIiEn9ndFFXpk+VEUBcxIrI4++TPNC+iUxdyDF6elG05P22WRBVw5EXcyDhoXnvYm7eexKS8uQWOE/SCX0HnPKr1ymphGGbDW8A6yi+mr/4hAjwQKBPHsOFrnANkxvn/EbCuoALl93RThNAHDhC9em7C4xmHj4oLTjGWezJpBXWKNEPqK+pOqbS61qhA5iEihBGJFA9V++/Lxj8pMon1oRsxKInme5RgaBbSHhfgC1Yc2SPpAg/pfv/36/pOjpakX2h/quP2RqbM6G73p3RH/SBvmBf6SflcmT00O1+zWZ38J0jnMd+z2huLNNdPWYYtWMYlmp5l678NmtzQ4ItYHA2pmcd5IF7I6IarBCXTKAEQiN5JhVIHPT2IK/T60daq+kjhHi1umyv7N83ODOZkuJjMYAnMd1E24SdHUuusceFFgapOR1OIwXlwQVzYr2ImYuykQrY+itNJ0kBCMhN5QQZNfXOA2hBFhEFW45Ig+C5cA/MRhaui3/y4EDOnWHOjwJwnaJDgOquXhsBM41XQ2jjvEk4R0BGLSBczMIM+AExjdqHc2JJuO7Cu1AsimJ99/fTQpYEAzv4slB3rQreqQx1D22HLd6251uRuYgR9HKlWHqsPARlm51Wmtse+nsrClEZWy8AAxOIAzLNWWmJak+fr7mOa3ugXftDAqksOO7k/QGd5Bl4gnowhAqobhQobtES4/8kIM+jhakRRHj8IvSTkgp9+GoN6DEtFei7xLYO9WHwJtkOXaafXdrclg662AHnKtc9UQsTYaImDlgsIl2yq+XX9CTry2YM/ylcesN/oJXf4p2+N4o6xPZxA6MaW0jDJgQvPNOl5bl0uN482zH6JSCpXn+Sb8xiZRl2L8nSaxSKZy1nbGZummQ/q7Oqh1a6Gj+aSztL995WnlN+W0gXbaTwjRv6+1nS6U6qqsoY5o/8X6XvYe7fJb60fX6AhqRzAULZmv5qsQzsDRi/audAViVgrFRIpMpktMONC00Q9yHd1X2PYkX2WSx2kK4Mzy1I6jR4jh7f8A")));
$g_SusDB = unserialize(gzinflate(/*1574083581*/base64_decode("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")));
$g_SusDBPrio = unserialize(gzinflate(/*1574083581*/base64_decode("S7QysKquBQA=")));
$g_Mnemo = @array_flip(unserialize(gzinflate(/*1574083581*/base64_decode("fP3JFp05jiYIvkuuSzqcAJBRq15WndO1yUWtObqZu5lJKSnC3aNfvsF7/4kEbprcF2YC/8sR+DDn/4AE6T/+fz//w8F//I//+f/9f7/8X//P//3F+GDCl/KP9uNr/6/8xxfzP/7Pn/8R/+N/FBgtJt/nv+70oNADmBRisBo9KvQGa6fa8UXvVno66C9iZ0fNI7b5r3b7eLyoeqw+5k6vT9qVKn35z+9/tB8XbXK1xtjHizYstNa8fz7/uMkrmFhsbcrqrH2szh30cRgoZbz+1eFK7778/tffvx4rDOeAPmxMuSjbYf25HefXsZfsWtL22mpnic5k211Uts/CfYIljBGM0aaA+4m0jHVEE7X9oy9//vHP1wKv5WEg20J7zdiblTx+mZP9+rf//mr914LhnhCa7l2JyiB3nFH78c9/f/3bH9/KtTfNmdSbc8rEnL0P9iQvoxBhry/ytJK7N3n99u0fX/tf9RqTbEqmDFC233ll+/mkeJnZafTP4zo3y6EPw6h76+C1t3PZ13TG6AVH0F6qw3ubrul42yhXUqdPJ/237xd9MM4n57pyL1zcrya/P4zVgUacdmJCH6MHUF6JN9oracm75hMqU/dW2UkIpfRmUPsBd91S/gE8rwNWa5xr2gD/nNF1f6DxbQPSZhQUxpBpxIKWlLP177Ndrk6tI/sQ328yruR4kS+vhmrh/W/qkKgP8T0xcwtFG5L0IVBGKCH619tczzm8j+7e3cfTCYV688Yqyw/nATL5tZbmMJSIGrm7yeEgt86a1rLG1IOXX/chGevGezIrfwnvs7vPobTRXQ/al+H+8nnvQnWDUs3Khgb8ou5MST3k4tWdoS8/+7ev5Y/6j5untJ5c99ojDlET5wYxjWo0+nTzuYveYg+1uPfO+4UeDt77z+9PloJ5dFAkL9hD8p4LZcZmnDm2cqN1m5RmflfRQ1FmDf7Lz+/5z69/5t//+OLPXQHeQzLaicJxouc0ILrBAho1UlgP3/eSwPSszeJgsK9ZXNBm9N6tymCBHnt9MXAWDqUYjavBeZb/+vXALuRsRX0+STn7Xkaj+pZXdpVxaOZtvGZiGasBNu2WoH3MJFyXFlxMbwm9sUtc+Ov1MEIDi03DUXiwy/zzx6/73ZkA3R4ven1ICDc/nms4f8LY1mw7OOx6cxGvm3uyjJaBWNgU5RogrdegVw+jH2J5vbgYxSWvBZxLGq2AomjiIFOVTSTz3kQMjI1urmhYSMVDxq5zJrve8jGoQk5dI3UrafehxVCyMmXy+5S9zcYPrxwjPaXe+S5rt7b4oN1YAkVu1+bBIDWFGVKU6NIjehObdjLxvYO3wM6p5IFNQ+bxuOLrUVKsnfUajVFEtzGKVIM7bsj20KJ/PbT7CG1OGTSUGo8N5Mt9C4eC0Q9oyp2OBwx4SB/ivRjj+Pi2HbRd1FSaSy5UjTZ++f7b7z9/e/CRCtgP7rBNOt2ncm3c8N4Zp7GdZJbdMIMglaIdd7Iv2TdZ/c26m/HN2KHsRnLXbly6I+916UmTZMkLagpYMt9VbSrhXuPJPhjJQc5WwwUJ5Ja44OaDNMpdSrg+x2QN1B60O532Q/Ss1/sMmuaW0otLMse7eHzJIbA6KOcA5uAe1zMMzBQKDXmCYNwiOFjoxlx91T66Qalsycc0lIWBgY3PIIMoYsCvTeB176+pdrDRUjDaV2n/amZJ0MlrtHGjHaZmazV1Eszjzp+MbgQovIymTNiud56VthybaVKygbW36PxWxvXxniOV+LYR7B9fjyN1iNUm5emBfTOiU+Hx2LxzgBplWL7J17yiS0E5YrsBJpcji+eu/jwuu4AOAGJXxC9YWh+EBzSJMaz20bjchQi599YU+QiH3eAW//xwevZZI7VfFrXZsubSqTuNdBOl0Vbvc1fUFjgNBJO736Y2TAwCtDt26Ps/evv93jM7bWeOlJvjHsrFU370XNPITf0FkuLGBpti8ooeBafG/yTHDG0UUlQXOHX+J3nmO9wdKGIEvJE8MycKVIxKbiV5RwgwNI0ZvFsU0/tmOYyF3m9gH+L1IS3lPnLVLoPf+J2B4aZhSCOFL6+TvSfC76YeV8ytpPjlt1/zHtzfNc3nYhVdBzytt5ylQOi5aA/Cx3W2TBg8s1Lllb1NAQ9bDKurLWsbvVkALmbDPI/5j7prYQOurCfXerLHjdR9edmnrtcWh8leUz4h7MC1VoZ+BTXhFzYFcbTSELO2v6zyLxa4RgZHPI543bRD3c/tz9//Wl4B4wBPI6qz3gUW303Pl8hrWx3XrabzTdZsBgxNcoa0suBWk+vRSXwEcJ3jJYNM9KXFoUBcALvKC8u/1Lt26WDDzYbvRKwpaaR+JbWOOVNG7VR2DT84tDicdtanhn9ezBfHcEm7bbDhMucMtqjeYdgklkMiiE1dVlxfaO9TdyuKyRcgSYWH7072KWuyEO0KM9IwlLMGSN4a+q3LGxZweEA4WCk9M5/68+eL/1yXvkZPyWmim1X5xYI8bB0+DZUUVilbPLOIjNr2sva+CuTI+Mm7oU34Emr1NoO4ZBydWsb6inB7Redtd4YKBavOJm3swiaXU9VkJqlSjUG4Ie0enzr8eTfyMJGxXNRI3Upq0NVqj0lspH7F9sP2PqGTRipMZdm2mtWvwsqLGUCn6uz7yuNKisclWg5xEH86Ne0QiY4Bv/912yV7YKlLVeM/b8vATWkjQ3fU9AxKmwJVmP2ESoqOD9Fsm2EhxvpWELcJxJUBIkMEVrbeE6CV8kYiE+CfxzdYjDh8e3L2b6/2A+PJNHDaNsSwPGvnq0eGf8qJxDe4/Nef9sf3h75se2opDmXfIm6CyeRSkika0I+0ThhZK4pO013iem4mt1b68TK2CSdtwmRqc85qJ7LZGUplLNkCKCeS7HIip2BgLTH6GhRDPqQNTZ4bbg2R9Ycnd5WnhwVhsexEviHWGm3ysFynECgVZzUZlfBlC7+mkCLzcVDVv7RutneQeiPSPpreH73eEwybe1HeCJrtjTBfrjZ0xZSIxm5XCLAnHD1KBoDTxpB//+PrMg0f+bEao7BkNBuLK96RZd1BI91AOjGTzTGT3DG+t68dO99oCq0QlapR4gqAkmmWpYL28xtSoG6QYY2CutHElalEkyFH5WjRrLiu2JQjgfZNu1r9sTjj6luz3ylXNOFqJ8KqRFig3X03xQLD/aHdF7sdlG/E0ggUsIqHEeKagIdmVU0GTyPEpcT23FgTUVgk2u2g7LDUgsKb8B3LcFNG34OjrFFu0iemMMJQ3jW6lSmF4VxoFiSLQWd1WNIjy4kYtZt1miIubmQLsNJTJSxB5xXtOZBH3xWsgU6ggsHCKivoFt0B6i4ZlLyJh3j167Vx+FriyP/oX//+v/6z/7ihAXWfq/faBXLb+wnz9URN10AXV9Q4IqWaoCtX2O2wgGlrqvm9eYs+joel4uEDjYnfUKKoEdudmDV9G9zh86GVeMUGp0IH/OpLjEm5JruR4vK6RGJNzSq2PfRh4yqu9eEVuzT6zbbXmzW2qPzPr6wqT/eijdqrDisDaq3Vlq3GqsLKgDL1RK0p8grD7s/qzY+QtascNqUyGduCH+pXN65CMSU3SMHCGDZNcQxrQ4saCwi0YDRfk6kGFE0VQ/yyBfvU2kJQrLDIev2v/vPX/UC9GS04xSaPhwv/fqD8lpn/KmZQBLtOIOQZqEBK3B6yWv+KU/h5EdfGUDIFTQKfiv21sT5kvgXaHpyK/SXXe/DZjKo8hUOxnxDr6+/1241IWMD5rGFEhNWRQA58HU6xyyAIRwJPJJzhRStzhbdG+ftf7V831Gn8n23QOCZs2mSDnpj5ZOXL+NAmL2UujUEsxTVyK8lrLpnhuHYzcDPNdI/VFqtxVtxOcGTHuEyLqEDcXpE3CUZoGr/B19N47HEyw9PhYVsPe9fab5nHcI6cYplETPoQ4Ifai8pXWHtfBAh4GBmidqdpcxnkkVmTUYEVvbj8fSKFfDFR0fyQNs/xVPs8avyX3kz9ZP+5O97moJ0HwW6pqjaGpuE6wk0oxpaLN1ZDoLTqfInld6OmoaVdV4/OdxfV7d9eRsdiTVPZ+q6qu878JEeNV8fN3Jz5KUd8GwD9Ruq+/Pbrz7cW8vWPaYu4dgIs6yhG2+C4vY0aqh8uazchvhDQfb0SEAPSrPDXt0r9hBHF8xXLReMoMW5wGEfpLmkwMybdyQFUDA7Nz45vxXrxYsXcc4hDE+LJbp6DVNoImrKVVj9mtK5YBCUwEZPXX3Izw6aRtEv3Vr8vTc47BmBZM0ngpnrbyDwQqwak0urPrKmRK5pXF9MdFv0CdtcuuwHDJw0mpLR6dUsqvRhlaWRWQDWN6cxflZtPp4//DvRr1ZJTAljJHCGW5dePf903mfGwT1aRj2R2bwuYTn2A4mgnVsDftJfcHVMRentm1oBSemvgbx2hlO8/+s+fD/MBlJ61iAoye0RFsREwq6SbPjEgJ1+SYgMjESHQDV/moSEyMhvviqZ0ltRvhrEeIKvkZ5zLxmRMHyVrnIPsZm9kNbbjm92uz5Xe4QHTHH4jjMCk5BVzErF6/hJjpyDx1QQHUSUNm6YHjgoewR3bHEAE2/iYvK+H2kErNV7Pm/flZukmQA6gWNnpmXZwK3C+lpQU2Ed2fVulsv5SusIM6NDXL6zFmjfh0A7ErcpKqVCdA0Wok3MLJXPa4cFpy9K0dMtMhrwWXkyspatsMVccrMJ37RfeB1N/+8etOjbfkz3sYduRu+1ZWTC19qAYI8ht9z80R9Vqagb5TXYHk3pMqKAM8ptNKlR+lFEJtSTvNuwCNeUwmsJryftVDYg4lULt7Pxqkor8T8n4WpTffv+9sz+///vrn/lv/a9f90Wuo2YWVYpIIb9ap1oZI3XTte+T+P5lAh6uJafFwJDfkUExxbUjt2n7gT1WNcQAnkiJx6VgzlDodaUWCpT4Nu5t838r98v8L0HqUjb09mTto9zHXaU4UhxBW/Wu8xtWpGowShg6hbDLMV5xbVZxkVO4AnQeIWO2jKwK39NEcMe3BYKQFDDLf7GdUmvMXLP2dsJ2oCkCKwlW42Wb7z/yJvtuFD8ywYooILPwNFGJqqa33/+Faa4w71QH74EmaneHvnG9N+rabsFmpI8YSgJU3AoEe+RgNdZh0PzIBNshMJzJUYXxtHv0QzWDaRWnKZ0e/QuH8js2WbNn0Knsn8zW9tSHD0r4Kx3KfvvnH483WEcwrWtfxk2tcd4N1w9nybpjePLGcxaxZkiBlLAywg8GxmJGy01dI25nNwxQ4gPUSGGLOk3B+la0m47b0U0bgBmaikunf/7SxXuovQTtStIGiXvICYbTXsWmjEcYSLVrU6X98nYGXK0qdmei/fIGNKYhaoCLNukbA+tSeWhvfdPGPQsRm6tKuWrjqWMG65WEP2JtfMJUVkGvdIsIrD9YjTiak/g2mLFyVpzG71gj/+fP3x7RGmgxO83RT3EDTy272Jsmk+KHi8uQYTo0qsLN3u7zBaaOaZd0h0qOKzVcP/AIsY5YxzgTPtcHHVEGRBprXQhR0bPpUPknb43nx9soJqmocvOkt8CaQxmavhhX9MuYloVG0fha2oBZhZi6I42xpj183DcKBZU8KEpP1fJSRfnWpRTe+7CqAw9t/xkRXRGBX4C2wkOFv+bCuv40ASt7nC5V43K2JJ6gq/p30xI/3SAyzH87eVfKuOnkw+DrnmqUfj6UR2Qn49nDhLHONZpXxOjXP8udUYq8rkCaQzzuGnDmKRj0ihIQjaI98WQDaxoKMIxCC46M7KexSJvFHinlXMeqTthul80yMq3BqaQ7054K7aiKWTba3abdPAsNUp5ntA9t64rpb9gtaKpI3EPku3UzPlI5uEP7fR6zTzb4qjlHot0OjpXT7vCIidk+fOkAt4l0WNN7VayC0W5QcT6gUYLXPpwu1nNdtVYKmKII8ei2k+vBOqN6fqLbQAqj6w6JlBSk6PagAsf8IXmN+0S3AUsKrUbSYiSjWw3lrgdq3hfJpyLrxpfx98bX6BJPRInwiUcA/hLhA8MUQkWVjrsbu4ZhK3TFYxrdCtxHMsCwVQEe8dSkL2kOtmWvhbbETZM2ZQZXaLmQ0W/OVegx+KKlIEa/nYOfEbvdKqg9+rDp5xhG7eOI7t1od5AEgW/j0GB79LgKIvSu4lG9Y98C2pT5ZvoZrL9NYGd52QxTGVRJ5BOPrPkH8mGdkIrRogPi2xn+9fuNffygYmtWrGjxUJmXRIBYO2WvoPC4+8QrvzSfipK3Gc8E+b//+Ue+l1iS8Z4U43A8wuVXCV6dp8JT1+ifL+mS+MylwhEuuh1MWA3rENHy5VDiWGI4OSB/+EprYq20p6xOJCkTz3EGBzclQZW13nei+ULvCiYWForKFuHK8HkYW1vipRbFvhch3OS335DxKVmlWkqExzZ+/fu3b/deVtOrMVokYYR1L4djLZo0nSHC+hxy7t3FobgzInwImWZh4gdmRXeJsKlkJTA6RC1CK+IaI5UGOZuthkRwDyg0zpfR1Qng9hzKNNm2apXlYdB9YjMc3B+O2n0IfHCjJdbvzRHbvw3BD+lFaMboKuTAjWGhcwO1fIyIm1bHmkDKmlUo4ipc+BG3mcKn7CBtYh7R0zCkQY09Fb2XNlzQwiEjbQCNQXYfoyiKQ2QFfKqJf3z958/b7AfGVgNJYw5vn/j6UoZ3JrakcR2Cdc9eWkBWcrcibaaIkHOYlj+NdBPzETuUbtRN29OxSokDSTETR1oPrVUcpWpOmjhz4Gd9isuKVRxr129EsLEX1sIvF9R70y7T6iyBwqJG2WPWx//79+9ff3379scDdTQaxx7v5P4mv77eWqhWC1WLcTW35wGsE9T35dwo79e0hSz4kEvTXKwxrgGe0zNT6ahItEreGIVhgHXmGtBoKChuvM4E1hJTUkJu4q5hB4Zw0L2GVtLq8fO2lxxIMafEtLE6Gxo0jJrOkw6B1f7oz8fakLJi7o1HEPvz/aVX2QLNDhjTXkulBNb9tEjNuGfA86s2U1XU9mFLGegUqGeNw6YPsQ3GNV9bUiyjifX29fow8EhotYkksyJoy2/V9qgEWTC8+CAdjKdOQ7Fe8a7qQ2rDwjhOYTVpj2vna8eMGBTwn8zK6nJqjYW6koGWzJ1VfF891/nda9w87c50yKZCjoq3MpkPpzNzbGwzinqTdhNBtczOj8idbd5WSQ8ezqdWiyIvk13Nir73Qd1o07YfDob575ix7Nq09yoI3WMIh/Kw3j+7azqZb3cNWZFXaUunD244LKRSbsjOsJIyDhxDK+WN7OYjv6LqnAs9o6KUpMNUMKkvnZpFUYhaHnHa4t9dDGUAKjI2vVLwH3mExvXsolVcFCyo1yO5gnwYMeehpcKkt2f9Zrtp9GCLdt6fnOrFtxl3qciV5NYIolkfA1xSFK/kjqjSrywUb0ZSY2adGZRb7aK81RmTZ6VBXeUKEwxmKIkUTSQdIe0LVHI1RFuikp2TroJ7/+oPzcXOOqBeYwt+A3m51WgaKo6/dJoQzkMsxXRG0Nq5+w9HgzHVWrU84eS34EdTW2VQoUQ0JS8S3iJrcVarjJC8YkFlIcNA70h83ciVY3TWQT0LEWzk7yf257dnPDG/4OCaEgqawvrGKBvE0wazvt3TB/8wTLb2yrFUomZSWHlkz8PbQ6fYJhwOS+pTazaDcU3PKnmQ5FQK+mEVDJnCo7jOlas3DMuwoonF3e9eZ90ezApgSbvfHRzfabVIYNr97naUxnqBum9bbhZjD3+EHm4XGswHoc8XL9Su4PsEuyk8VWDOpD0XOPwu33/7/vtf4z7yEU1PoFnPE/g1qJnXgtWTYhNIcD/GNYAMK6va6jUBePkI/3jm+KaZbuybutTtINtMnzRe48C7KYNYTjWr+H8TbOc4EGz1QZNIuwkD0gCwhwN65R1oNt6R+H7WpH4WtxMkl11Fr0lPdJsc9yVa9Vrs8fl84e3IVuNd+ChtdTl2meF2HxXPVsJNS54l1awnBUs8DBzP+BCGEpFaVNSshLfH81IOE8OwTNohbzaOqTqx5qTE5qXNxsHaZsPmlMqF6UjX//vPJbzZtRiooKYE0BaQZkerFrVJ0Hp4rRjmN4a0SXhtEjXXEErSOD6tad8ZC6MTFXTTBzuVCcDv64Ck66HQXWTugsc21AygiW/aCifOOoapakWWEm1PL7SEpR6l67YFnjb1M0/wqm5jCnhz2L/XeR/V/57CrZjWoGpF7FK0tww6PcHW9JiGlqmS9up/tVLp+M5X9Ns8VoVhjage1bRy1HfYDvWwfZyuf19jz+aoKr6isQiKVdtXk4o5vGXrJYhbGdqrEgoLaOZ/SqmZFOmD1mMwDFMVr3KKD5BzLoKhPvnjjW5HfOT3Sz9BYyhHvioGwZTM2yC+JKmZGZCavQa3jyCF9QjKyAWORK7tCNIW8WN6zWf5yJXhp11vi7am4qN2sFvaQIQcvO+aTE0fdh2I/7IFpVBhSpqG0JDVMaPU2UjviIbNZAboyOQgNYpgNptHDB1KrNLPypQru4uOJZDGGJly3eWB1VAs6jdXc2AFhDqs+s3NwEGugY1SpWJKlJoPxBB7UQrjMfmKLFzns0tBiiemjNtWOT8ZuEa5iqdZ97C2LrFCMHbz72Yq0eUq44GYdD0ptLGSVcygTLkJJpZ1HrqU/Uy5nlRDlqNJyYhnyvWkcJqMcpSmKKZcT4rCrIaXtbu32TtY5S3omjSVMuV6SDMUgAxKywhTbsZE5jRFC7Rlyj2msBnqxktxHMxm5CgtZROCVA2YcnPCs9bTg1W/uR0SoyufvYT5s2D/p3pGvVJWLEk8ZMUPI1KwRvHNMSVsZxDsOEqj75TrafnGd7o5aepkyg9cLjBEOwtjLLKdh7y53AyFuqwmlIwtIAsRMvWsZvIOGL9xK6KLR+zk9vHDFvIEPKxozBpsMkeSqXcfZGypljBkwDjTXoaqO/m0ONvDEU+y7uCz6cAisaL3LjgjcQmP2SNP2yiz5Lo28V1ktRFCDlVGxTPtbgthHbZlAu3g/c4hC2PnJHVoptwdXsj4wGTt+vuVRaYwZrqLxkw360eodfBd1F502DCcI4RUm3R9Mume7pxCLlV9S2ErWjIyi1+nSZ6gFAmeRoTslaxyJn9Y4q9qp7OIY0wyvojJNyDOunyIp1TZvqyU1S6tEp7p7W4hB/Plv8oahVIH750SqsfEm3Y7sNh85Cfs33V74AzmFlmUaHdnTyWoMxYzowSiTBrkPsdmeRIqyIBNIMVq+QXJAv1MiQ/wdIHo3KkaJSmR6WkNTSKsibpOuj+N1lOqSlgfk26CiXqtAUmWDwjmWT7gCqO1oWNUcsqZfJVOLaSGXqlbzpSHael5KUJ3pXX15U3rxFNXCXmEAahtBIbVAhXt6DYm6UOc3QuYST02wjI2SkkFCLgJpxnNWZVyqky5PSOT/eg+yh4uTKpA78i3h4pi92Hy7dwiVFYGo4xpC2YvQxCyA+sObX0VpmR1YUotF6qgcQpyW8XG1gFrlnFwTCqyfMerLrD0wDGt8uxgFhqgLENOmVymiEKwrdQjb2SjlkYJAMs8ThU0u1GiNdbs81EcbFvhHunXaiyzSJO2wvS6xouVhtXewupnUuYcZd48q0TZW6VMC1PbL0tjEV+YE4JiGGXSTZBFb0J1SlAgk25sExO+WkhppHsWDzjgJUu7JJOuLLO6Yr2vGugQVQSZb49WvHYv4/70gnetoKaYbMkPmGchdsVpz5RbLG2sDImtpsJs9QP5mFxH1CTGFpnBClmYRfCUJaXtpArBrLmlsYfk11eJWGM7sy9xJQ1f3rbC5T3UDrYkpR4oD9gCM6Yajy5oh7AHZoxODPhJU2bSVvUxWe+cU7d2PS6LDQGUslJMuR5XnRewgbJf1myqMZZuLWl4xxolNsCmjoOMgh+scZJ8RmBXpxSIZvLt4GzOrOc55eFas9XKLdUOPjZZtpRJd7TeB0uWrHFpa/ZHloqZmpq0kjLtpXw9wjsws7qrTniDJT0Vh7ap57GJN+YJZGORxsVg7Qe3VwFoLllpGeMhH8TcjDf3QbFM85Dt7bGy6GJXKmow6WrnsK9q5Bo7sZudowWMrSgJcUwJAt1Ss6yfyWovTIw7McMehrdBhnsx8Z4tXPnhG6U4H5Nu5zcahXFGQq233ipdXXJ1xhelGvFM+NiO29QyolKBkkm3KtXQHSIor9+6tSIM61Z8JTQWbN1mQ/TNhgCK0LYuSAw8Y7AsKE4KJt/bW2CLhYYCVe1eHAHabKNatcfh9vZSfAa+NMWOZ91uoQqJ16AIQbtFewQzHFCSbt0ZxLwnvdqeq1GUMivLFzqyvXSvnYJfjVS1p8H6myJWrN8VaiwQMiiw2noFR4bIalpUYsSYfK9ZWLBNC7vC+zwebeQeKIu1tzSadDYwNSleFlf4ljnFRcT021uzMO0IqN1Jn+4ozOugu/GhHnhvPZMg6k/W2XXEKE1HmNi+fSRP1Qypt2EVrw2TK8IOiJp3XobKM7l/xAw/eVtIiUbRLnQIX2aw2H2dHDNNr4B8exhInskUncA31UBrZX2FNHujalw77PFwwZYKMkKGKWXgr20JM0XtQoUkqKkzCPNWMWJakHbG4Qu5MVRqK92wyPjRo6ILW9j9ky3nRkolISbdqovzS/SoJIIx5aYR9FdrGVAMZnZvoxgY58ZDY/XrjYMtcvsd53RhgGBYFSXZwYsHHg/yKSXDtDN7TfTtFhXDtwixKSY8C5uxETHYPBTnhd1aKLKKHSGijJhjSnv69B+54p0xfVQdbXYL9SBIjjnCe6tppVzTo680kjw6C0EZ8cEjNIOYyX1EVCzcFnfo6ViolWQV/c7iDj1hVAOta0B5N62QCdOroeiiFrfDi423PigFrJh0w50pTGs8abKNLj56B1p1Rr5Og497oQhyNNsLK0q23VNaqmGAk0jjzvTIOb6CbTHGYFWZsheWSMPhiEeI5nqBHpEea7G1FgqhtxoLPfNaLlzk7CyAr+kjtNdDrSXaEqJKK2wq8z0dNXW2e09rcQIKLg6t3WWwcUecPuUZqq9MIO4+mtSplOA18bQVmHA9m3IWmMCV0p9BKUspxJTzNFtpn16VBVYrZvcvTSM6ujNMp/TXZ6nQxqLENRUmHUEdi5PLez4RcIoF1G4tGsKUq8lonGizrcTqGH4q4WlMuZ4dg7xhqWp8c7etOJtr0IyTNm3PLuBIw2ZNJ9yrQl5qwyz6PprGV5J4TwTVKdngTKoG2YScyFTtIm9RHjOGeYyh2E3sZjdxtsaOXaXcIt4C4+CiFDZmylWEdcO6mFdqn0034uqsTolFkleAgttiP0bhC+ydsna3xX70BIwfsyyNwJReRPzl4DoFUOe6pYP1VopRYgOZcnPi9FLrIOVROrNF4wTHzNrJuGOmJBkpkksP6JXiKUy+ySyXZsEkLxOVmDTpLnvGHTVZqwBv98lm4gKrAk0zgLi9agayCGe1V9Hmnd0yyFzlG4VdQXrO+l3xj4FRuoIf3Z4HEwAbqy3qXDf8yO9hvjN1rhvuB2jDoKZ1OrshjpDRoU0Kh3J2rWc/EKr1WREYbismCSHO+nWKHHRujwrOtRhwIDPVmdZeqeG3dtpswytDcfv2Xi0jB0Yc1iqmHXeUlVxFBQ2XSakCzeR7SbyGtpp6dFrfpg0Pl+h1jU0JKWr4xx02kzWzcubFGJTZaEweT43wmW2aMEKXyalMnm7yMwQxGcIzTWpbqN/PB/m5QkFF3Lpn+8krwqYwn9FSTZl8K1vlcuQd17is32KsWuPzOuxN2xSO6gF///63u0NNng0tSbunfguJm5W7yCrYwPmVJeZSKmsH2ivZK2gQiz3QNCW39YeYleUNKCU2mXIz+Y/ZnUcpJB7cFurBl8aFVhRk4rZOEqw4N2uUdrJMucG/wWAcQMFFLqwHBWMwQz1aTdNKGW6gWO+852p7y0bJC+MRGtjI2PjqKqWzmX7rrNt6ZhihxCy4QEcOfWWIcNF7bIW8uscr5HBAli+uAjnc3lqSd2MGFCuUsCUoJVt7Kdq6YIMcs01ED7IkCVO6y8x1d8ttvbiILmj0XqEvaFh2HuktG31Q6KFTKkeg3KocOHi3wfn66mJ3P5DOUtSidjCAyg9QAl9NUSdECn2GHiENRelnOSftgIV3PWKSlU+Y3B0N+J4xdtmCNdUq1lF3tYl80pdaRu1WExQYFPo6GM4ehXG3/UTQOgi6kvhxvtndPkBtOehzLD4eWvs2I3rM6A4Ob6mddcO3H4gH/axN/oAUlufatSuBSZtRmn2UzoSjdUZkzhk9AgaNtTA9ABq9VfY0YrGMcmT5FKY/zvh+s4MVeNBindxhqHjTX7FD1eUUQIkYdBS2b6dWuhtBloJiWthoqXhm5Pp3caMdoeURDu6xzZkeIIF1QKd5eNyehlIs70KzMjEn+KPa0JGYc1nabGTV9y32VwuTZ+65Ebs+eo+HPWwjTjtxTsVNRKEQM2fc8oMihh7zu+vvenj+nXr0Nf/8+eNuERQyue7eCUh+/TitibVf849H1Rfjx6AuG2YHv9dbuDK+KFQsby/jUs+Ztby3aHy7YfLvf/SHG9NDT7nLpldBNPO+ex8W/pksSzAE0eX1juQkW7JRlnMWlZ/DXjrfibGYfUxFTnoswLGCzkj262I9jo51T6NSJ0EdM4uIojS9mFlOp6q8os44bISjo8ZyX8GfMGI1AGZgjOYUjxj4ty/g69/6X3cnGeb3ISp5akwdnp8/AyBZFaHunYysnm6FK9jgpC6mj1hB1lRi6rD6lq79D6y2soKm/QCIaIYSybAUks4dpkZBPRjCMiSTtU+A9U9BzVCkB9e1pd5xeDef8LOb0dBmQl5QE6tAfDWlfZmp4csSDz6DuPjTss0rk55WwfVp5ZYpReVOeooyHqQ77I1kwVymToI62DITDWQAHPgo98QUMM4aaRpiaieoEy90Jjho1EFQY3bJaYH4TH3fk/NWNeaFLLlloV+mpuc9t+e2+DQqkMxjnSHZcqGjsGBVam4wdVocOA82E3FQUCZ0VA3aJ9TA9+60u5vk/epgTOhN2Z1gokhTYCzSRvXKzofbeHWbqbtBm4pyB4K7DX5XeV6kjD4p8w7u/va5ykK2U9fec/D3/bqL+bKeTU1mhTD1I/z+qhzB+lgf2vNnoSimYli/YHLprYVwZ/Jf1AxSeECWkoap3SVpXtO5qhq0Pow90Oc2JHwYwqeaQak2CGGGKbLqt9haOitjrN9Kax2Tb14sBp2FjMbswN5P9aprP63VyUvbHlPD6mWChLkdaXz7h+WmJwodPSgcA+7iNY9Nh2RMUm45OHlbIs1M76TccnAgvj2L3LC6KKMomPqWLPZyOLdB9UztWD/+uLjnOSKrlnRatTZqL3a7Ml+k1pUHBwHlxMH1apQIBqYm8e3kPWbwEocx9Rs6i1YczHe7IaUqOytrdh1ydY1oMxivS+jGQ9w65FwGq8xlxm1oQ7w+JMTRQEucBXiGq58s2PD5DnNwj4Vpz9bwT6Z9/gA5y2zSySAfODuuLz9QOxKC0swHYG/Mdv5CbjCGPRLX1jklUOdUUgmRSPuRdKPiV871VfaaNQPvm3bmD5D/GnJJH5uLqUqEAswW4ZqAMwn9DJqRP4KPgmnLjzRE4CN32o/oizesdboj93MfEZcRF8gw3hdVMOJd++v+/gDm0EdA0vp9G9QZMbc0hTT4jVYCUqg1Wjxi6Tdqye4aGPJA6relKKVOnmGPbEkGs0/1Gj1w/UKOPgSrjgnbmMv+H5OzPsrwY8AHW/XXrAo/C6ddJIfqjjby1ZfDv7iO2BoqXxyQ+QAlo6Ax9OupnaIBLVkoXUF7s/nwvrOs8GH1QXmks6mvNiM7y3WNJvsfwOztq43AElo9khy2EWD1VXtysXhtZ+Hep19/3vEw6GqfJfslO0NQIlcYgPqBh/Fr+wH96HypHgppGwXrRl3iCJGfHKmLSPqI2Qt5ZG1WqG9Ud6mgeZtp/bpsXC7U1/77q+3yydIwFW/RydbePFDnBpGZDTNIaRsBxIWjvc7lmiAFm/HIrVtf4aPL7cvQf4+ZLbXMGxlui6IlhOzUFi+JUxj9nwUq1kXRjUKfOz4ShuBVWUA6mw6l+XrmN66ygLY6yde0ujeEVbs6m3C+bnOYtU+Okq3bCP0i8FWDyY60EaTzIpcyhqFABozrhT7vDMyKB1nDALPTqrp0l2weRzjDdvzpiNd++XieVyabkskXBWXPxqs7A3MdG8UjImf7Bf9BNNjZxdAfpY23MXsw2hVhxlsF5DTxnFB9zbOZZBmHT2H7FfrwK415ZTybPCxrJyPBtB92ptbIyjlAhvQDIcAZ6T7kpOiBMtbtqizAzeEDXpdOVpda3ncTTum+TmyvSnpdYAa2ozvZXgjohiePKXXsocvyPDA7eOrLQOaVA72iqDz6eN63MEFmzKudhJWqBzTTc0DlnpP7dAt9GTWi0j8ByEv4Vhm/zTJJyi94+MBIK+Np15qi9s/2jfsvzNS6GqMCJ+lhUriYQa6VWlbew2zdqL2HMWuFmqCwj9nBUQ0rqtX6dpZYX38k6GoNzCKrVBTddfZxFIdc+EZQUQQuBf1m88XLzSvOc5i9G7VlU2C2ldVtDfJiB9PLLCyvUcsjc8woKb3L/yyeEaCwMoCFw/pmYo/v57kPi3LYVeA0ez/9y8oBhg8OlTHANVKKqQDtJSSvyQU70BwiYxvytj59/+3nb8vEfGmm+qFA3tnIcd801hbj6Ee20/qSHqUh15fEC2Te3xQRTh/wIqXMjztrTBNWgXzvVbS5KuVPgUDaORlVhYCo8SeQChSMWd7ubRJbwRutUfkLqC7Mx+sR77Ct4QESnzcr1BkISIqSSajDkGEsdUcKDCHUFZCcK2ulQXu0qCsgFJhRlarxKjLqWcTEykR9h0Bu+0X2437BYOzUSVGBiSRywcYMvWnKJpG04Y3YcndK+Uog0m+gzcXadOgGZh1xn8UBoa/GKJZ5Q00a/ySpxfeWqfusXVmSl3C2uZ6NcZWXHY3OP5C5f2pdsaFSlOwcaoNSlYaJ8OixeINs4kO2R57vuqNRv93eVVvsuwvstqOPFot/+/a955ufjdYjSz3tSUT94FInZt9Bk3ofsfxg4f0OR9mu64rl1+V4a1kD1y756ulZRhXWzUYJineAUtBZenSjhpFkQDFQAn1IgciyPykm00cnxC0GmT+WT4PPevpJIrZGxGAnajfrtjXerTgt2JKTIscYTH+4uj6USCAzXnjIjS22jBfs1gSvjvkEaysyCvddsTTOzoqqrsh4Ppum3Mho1vt1CtjZ5roOq/6GznJrS2EWJVVG2JXlnn6ElDzjYc1cHB9axp/5bw87QRzWW0XExg8WzcpaTM5OgdzRSpZbem14Zi2uh27jh5v+Or4g48Lg7FT4z+/La0oVsKeirdrpCDfbmLpvitiITrJEk4pvMBS7QHQ6DCkzF5VQEa+zaZ/GFmMr3cemKOzRyxkVEwblqCDo6HWzWIl9Wt60NYQP9kMI3Ywug+EghgfE+/5goJg6FkU0xfBBk/Ylz8odRpuVDlwY3pXaD6vONqukzcoGRpc9aAMe6PkxoMymWCqjjeD0ZTgbGaQHBTtHBTuXnHPyVuGas+fbLmEZCo6Cbw+o36hv1vQKnXkFh95tOQqDmkPO2nXccmmXa9hYKBErAsri8YOuwVAcLAXth1DCvOvN2jFSIO0Go641jowYCyo4KaLc5JFnmYmkQL242WYvT1AnVhyyjC6FZ7+3cbunZkafP1K39r1aT+ahNjlG0V6BS5F0ToXu1TNF4wsKHGYFYdSh6cmR5B6ljKxWHgE264oJ7iD2vz8cTuCMz4rBOz7w88z3v62EnnELKQbvSP+bW2gt+fQuJrQ6Io5eaCtoYRlZG6ly8omJ+dzvQm2DkV7VpN5RGvw0cp8eIcfTYRmg+NqiErpUGRMmGzVZf+SRnt+/wmLq8KUnJTLuaIt2RyaeuS3WdAwxa5cpgvojDRNjoP76kdVmcXRHO0e8f+iSZqaPgFFRL89WaecP3X5DlgRwlGfdVnNz6Re/uhh76dGrZqSY9KfRPDA7CNoTT1b1VMyuRja9xf6qe8wuane3zWViibCwkNJY4p6DegtzUxm4ahw+SXOVs9n4ZhRnYEyobm8M0OhsubxC3AOlzzP8+Vv/44+79CwmVq2johAdPdZeY+qfP2/wbas/wwL23Xoc4tvGcwm3URgGRsW1k8wnY1Wu2LxSnBeOpmtLUnSk2gY2JWY2mQ+BVTWWkKgr25XMFlhlr9BQBvchRMXxkMz9VF4dka6129xsgqqgmWTWd3JZA2vshlmq5MCz+9lR5m3mcFzRvKWxiBrKwzp7oF1LuR49jZaN0tkVjj5o95BzWqzSFkYBipEyWa//CkR+8qBJtmQ/BKrEyExvKDjr6HUm5+UQaiteUZvTI3z7VUjxdiAPtGcl6m1e+pnEXJ0FUpwERz+z14jvuf7jPnkswRN55XE925U9WRE2BlS+KPjnaCgmmDd0ZhP2bQNZRWlyC5tYzGmGBU6Hrj2XvRL3HbJi/SCjXWMX1aml3ChUo0DAdCVl3lO7HBMuE5ihiLyzQZm4ZcXBdFQ25Ye8FT90I1TXfADFcJu8/zgKpwc1VQUPpqNI1UtQ3vyyz7KEVXPxnS3Azp84QUVnZBnO8sjbDqSV81/6+iBLI2v3JhhVWLgyS0G/e2utCsTZEWwVGNc1SM2krlm2jt5g6q4NvgZOFeNnn7BzfheMqQbjkbK0/w58/J1WW+iqtS4F+jjKIkE5i/CtG757Xy5zB98cwqwNAasPKYa1G8gak941yOvupJkVpooo2H2fl1OsWnBRMzIkCCprY5HGz0cL50rwyTTG7DYVR4oBJ8GHWA2TsmtFC6RMaFRuWCFNO5G2yUcppVciDCN4e76d2QskJpWBvqspSaNrN5BJlZ247tcVxeuQplFGW/xDxXvBxTuovDlWwzTuuXlvrpQe32rORQknOFpZvea1GjnRRz/a2424XX8yH6+/86F3GtqTJvmkr+DkzCJxvIut76M+s0/DioazQYHNiYLO3Xm3cyQNos5GU9qRhlKsg6AxUErrj5yrMY7BPGWN3cTPO5eLY337nW2+YuH0UBj/uXLQRhXD0ZlyH3Rv3FS1/sh3aHQ3xYykWNFS/LBvFvuAoEVTp4ci+BpySZ5hzTh8cxvmjpesurmTMTmoXoH0ru2zBuFaawOhpgKkDxzTzkbHEJXUh5Q+cEyTHRSjpPJB+qSWQQizVYfGZXavy7VPgXFijkqUUxLxR5dxIfQwklXiWFL6FB5kqMPsYqqNSR/GkGE1MymmdTSfVK3cy+igOIrRPDSh6R+4VIHsGL0E6bTBo+vSbZe4Mt+cTfVsCA/rEHoUDLnVs+5tRLl2NA+16b2OK1aIVZScD4y6jdn369L+3SydkOW9RLPX17nQFlRbCkpfEpqHuvX+mRPPlFqYNb+NLPsYv425BPNMzyok9UA8eiu92cv3x7E03jWlmhiah7q1tOYs0zRoQMJTPHotCaZXa4r1TL3cfiTqrAgCkQmKGohHnyShoAyfPQtAyfTx6JckJUsFM3sFaSN0yY+d9bPm5CtGc2eobRK29gIdu/QboHkoaP+8xeSYbvzaZDlIPLojbT8QUu+2NSn28Wx4NPX/8SP/2Z81kRnOKwHUaJzuQ4weLAt9KSHR+HuzljMMLB9dPFws68Q+RsmNMZovSj4lGo/P3bqgCHXEULRH7z89+jxs60djzm0tD91nMV0ywwPKVrsrQTf0UmaVPTuJ32YJSnWLHTnEbLS1B/XxMpzq5kwlW9ceNtRir9whnKGzJMOy0YBRL72b8WuUJGxDA2tY+6r1spByQ9swWPW3K/evlEH+3bZn0S/RwOJpOa7MeTSeBzotqh0Nbnapyyrn+dHMxtnKDcDNMHWZ4rPDCu+f2ab3bm77mtr3334/4uFuN6Bl3UMJ7cajn4zYutmc1x84eR+xIv+LZZDnHzHa46QPEgk8tnRUAdyHWP09+x68pSSROBraN/pcSsrMA96BdwtyRfOA/O2ffz0ZGmNKYj1CXc2GXON122IfI0jbJJ4dYq4hdwcjLCyUtMdDuInXi6NRsSM27U1Hq0slR5Zy14Rl9Orp52p4wxRXNpqoB1J4GH0UrzHzCOpvEKPEeIIxt454r71+f9yUHke20lmHJn7Qplj/AgcyfByNAvMrOaJSpTqNZof5JxOjWQUsOO24d5h/PhHKA3J1Gmj7CPNp1sxRwp7QfAquYkCBMThpSkHzIc0SZqe5oDif0CQ9vqz0Gp01GkNJeqyDmZUNMasjdGnvWh2RjPQ7ojW6E83yNXF2KMLePoKyNpNQmbX5QIFs1qzW7itFGF1lrq7crNkVZXeIge22F6WJPFqjuH/520hno97lvK3dMksvr0AaFSHJGoV49Ph4/0K6xE5jBWWovxD1q57bfByo7ZHTw2U7X/Voo8LZj34Xkk2jJaLhFRFqRXrmdXhQZ36odGyhdZ9sgJ1iNmXIqjBoHWoIBxnT44iKnLbug+aQC6SBRnoD0LqkD5lZcRGNopvaTw4Ey6CbUAnuQes3/9lV4SYBdRcV3mC9Dght7qkWJQsZ7cNzsNilmalTf8OcVd5a/1A2Vo8uI2sGa0cq2fYzqEu1mlIISkocWq+LhMy3vp4BK9t+RR1wGW9SO3z/21pOlP5Saqbr5AJ2zBqh+Dfq3ge5fdBVQM45l1xVNCh7+hquQedpxk7kKWo7He5N23a6IA0KipkR7ZbrcRt1+e1XiNpF2zH+rTszsHNH0sb6ysBor8zHllnWyQg9PPtHbANYukcXNSRo4ZMwtb17F6RlDi18EKZYSvbwDtr16y1b8zyODhBLqmuwleJQ0N1sAKEbT4oNsxaUxgZgVyUvzmFnEUkFVRztIOTp8J0ZvaF0JKNFHRGSb7PRoXQko31oHitS9aUHV6N2B/DmtA+nY4hjFviSXkqcHRceAy5rfqUZGKiYtOyjpeXXV6eye7tmL1knixOhRTXokkxpLJw0gUl3V9IV7CSa1WSl1XTm4m+neMeJlTpy1e7zrtlc0RA2R3RKQjna1ZlxGQQq1F6PVoD7xMJ2Ja/LkhizetBkxkOveQZ2RM+IkiGJwpceas2RaHItpgYXelOMApZWSHkimVFCae4oMbotZvc13YnUqZMJCtC30egyAEyIsYB2/tHqQzKy0PRe0Z9s1EEly7/c/RGiA+uIo1nDy+Bw1cDKfXT/7ii53eLDs7L5bxhIOzp7TGxTWrMCrgJUHTqgkhWANuqR4hBaxpDV37iZ19/++HYnLPeCsSuOD7SfMshZWLMI8xrITztOPLlqn2EDvWtL+ZCr3XMJPSieTzyaHkiLSak+W6P8xtGoQBib+MbXcGacbCNWe97l72NxYsgoWvbRuOCa1ZVDOFN3srZbznx68NFTncGx2hj4ILec6ThqlbVg8WhT8LZK3dW+PSUyb+PvUtIB3SMk7YA8SwxQnV0emiKI3OlguXDSFQ1dGFxmpQsYOrNJyMvBMEb0XomCx0f/grvSZEjG9qRYTZ21z9d4Xa8yeIxmmXRWN/tXO2tNHqGC20/c1/7vjwq3w+ZATca9odtD0s4XH/yskKPpIG73kVzhytmBOVe+LmRTDc8fabPVMZIis53b0fGFinrrNUcFrzjn1XtfWP0cqau/8kmbpMY6W7OyhgBe1ftv3H5uWWWuffr71nvsH/3oLvvyyL22qFgDjur5m4vTlhZclA0C0J29Bds/HuXoLD+M0aVPGJ3fLJAXRo0sqVtQZKi7c/yPt3utmbWofPaP2sbcsWHr3obqHWUldBHdlrJz+awa63GgKZ5H/XrxRGaO/uzDpI0IKpfn/SpYlcaGs6XwF7FZNIN7c40S17hDf/j7z68///p9jH5zxldZlUaK+dkdGsRj0GUGysmEjNpL3NWAu7rIyGSUmmQ85IMaYAqxgouKg9Ph7Yf5/RGwXEOfdcgVjdM9YpsOZndKLdu6ayEqAMoh6tcyTherP6JN7DqEHsD+bbS/oAF2xhJKpD46/KA8xVESnL0PtiEfRIMLM8QiKgq0ow8QskfTS1B/RegD168wYzVJMW+4NbzpLqcBtg1rtAezaQOXp76MlFxUp7U5OS5j2Ois2JN2yz45OfrsnRySoj840p1PJQfmkUpYPLo9L/wSXTA7OkbFeujibvq9qhnw/Z+xItqYPSH3mlorsbl36sx2MaOsm3DdMgc+1reXfx8VPo7qYWB2VmMC8UOWdO+29ubVnfvw1Ead0AXf1qoV6awwfy+nZ+c4Jf6Sx9362ltruaMDQ+hd5QSf4H6aXYOtFuXjkp7rORvHVbQaUkr+CcYu39WgBM0qpieXluDoa06RBj81jW8mUN9m6awenDaEbcQS4XubNiy17t/FcbYrk2QG/6XooGsJowLD/aGEvEX5NS9vutfCSGd9S/1AWsNXx2btJ0j7CZsJzypL20/sAQLXNamMKUEbYo0+xPgGSFWxuPs9y+NCS4S9xCTLvvCQXTW6ZpZnU0mlICmPAV3IQuylDC3wyO84/AqGxqmtJEXN81ZXPt1IZqh2av9A7pu45PdLmbJiD/f2w8mQC6P2pPAX7z54KWKY2eNBecB+i4m6olYwj0ZNEX1+i4m6LO4QGvWsjlix4iWUOtmZfaCN+ODWsDD7catLB11Y2tBMa05RjhgML6/+2l/rA7mu+Lm9i+q0qOZcTVa8U353Nd0vmBU2UAp2oPc37Nss56Zi0iKv/CfNAixr0NXJvEEect37mwfP2q9n/vG68A/eHB9KNUULCfBbhspdk421FhoaW/EftgoCX9/gFPOJP/w/DN6PZ3X9jAmmYNG2N3ywF7aaB0tG7RDDh6gWP1LrpJ5I+OBhHcwlXNOsRz7oMRqG5QMrh4prwT8SU5aleNtsc5o3wodPTrYaUsvqEPiwetdHykm9wvBh9d0ADVTK7qDfMkwunXU2iTdVwboePoAplk/8HJ3iYfIPl8+aK9yx8r1UMJH/5PFJU9aVpl2XXdW7RIo1FAAU14p/BKg9MI4xLIOPJuPbzcc98veOPCFGwVlxrXhcjNKX/t1q4+uiidNdYbvU3Fmrbbw55CbpHu6b+scfpf7jsfoUmX8psTr+k8rGzKv5NJTQgkeDlL0oXDfelqE4vM4+RlsIbJ24MKImgelDCGCu/CvVazds19ku66TpjMGy4o7xD53tmNqVvzJY3CWrXUv6AFs6TDOrFl7vifQhY5DjZ6mYhR7tXF5tH+5nXGH2cdJGJHXE7EhPxmvPeNcmryGFQY4vGpqMWzjYtZJa+I0pDW/x0Q7m66uL4hUkw8i7WdCe2EOXXANN3TA2ec2e/+gjs+UKZIqVtOKr6D/pkRVmldWg2BL8Fm93vRc+yOGrLHSEPqYng7kyGAbLo7Ngxzqr9AHkZ58nntXeZNozGC5eyXpXJ9ROP30QLoZ5uGukocmkY8PUXetqTIVPpN8XhggAQyk6yUN29fmKPMsOiZLG9/cGZhfWS43/9i3DVj9L2PJqdmN4nbV6skzi5IGfYukwJbwE+TbmU9HbGGtkcKmoR8Fsl/PKSULXG71vzmqxCI/cl9mfbH0HhZ9VGUmRNmGvN3Cd0gh1pum/fsquQz6be7xnzd155S2EQ0vcLBDJjjST714D1lM6dMTV758ajtAUi1qwerwmgXfVaAEJwe5pXNfaWWsf+O7fu69dVgO8ohELazzRKawz7Brida3JUfRarHVwH6yjlDKzwSHbqeLZpml1N+baLA2lqhqGD7kmzZmI8Lam+nVSfnFsHXEMq+XO8BWNRTHePDo9SQ3The6HUqmCR30o1mViDqWhtq4P8XzZYi1NKVCC4dbkHqeJkUWHItKD/yDSw2guVfVoDr1sa7HhfGacrzGAYPSfKJBTU3WfsGtY10IsRWqowJmwa1iX17iFAlQUbhvCB1O3dcUVr7Tz4CF6vnSB1rs9XK4rqzjUpS18bwRbsCYlxiLAak65vDyzsevQ8vHO7l0bN+qZH2M74mq2AaoBdUD08eySadYBT9y/aldt4jivnTt82F4aLP2bUmWFh3y4jZmZESaluCAPWa0EZ1RRzD27oZSb5hEfhKw3Ydae1wQLbgaVCy7y3SrwVi82GbYVmqt/5J8P0zPLnTQ0bTFg0O8xskzGplQx4iEfbDfF8BMmq4DsgB9qC4/octTsr+ERuvczf+/3rs2QpKYk1fOQD1ostNhTVJLDMXzKMUroizVa9FqgNcjmKmRfTcXutS1+dJdddHgDYybrKlg20CdPjQnNnVU7t3ndqu8SvpxCwjG0d3yEiB0RdVeGimkxW6VYEg9YuZG7pF5PhrRqFzxE7wHSqq2O1Lv/MYKrZT6SrB18+sC+aTa/dlGTQ2lTlK9kGzs7RDnFqxMOFH9s15UYkSLLCKVqHQ/4xO+JFcuBig0yfEicYf0lG+vVH/lg7TGVZkF97RjT9k7OQwk0cxNAEV3wQP1LNcfRIY8CytOCvaXZmfA2K0u5UmTPbR7y9ARdPALcqE7bLdiKBp9nCLMSWNOSc8DqKUAs5wZFq+wv7AXC7mBdYzGhOuRTrophTnT4TPchH4wwloWQA43bwe4GumNmRq7ZKGgCrJ6DWQ1jryNUd8WdsEZjrZA9YydSCgkh7Pj7MvUwY0lJy/qFA3+/w+GfUMcjYW6aARY+uFwQ06i5KPY0cEsA+aWyBH7zIygsEtwSQH6FITYWWnRkjC2KFxzgs/749tfj3BF7VbpDztJXj2U/Ln2E6fnNCjaCILJhrsgEh3mA0rcKYcORlw3d1gG2KIwbwlrn7LZu95rPNiSP12ttPO7wVqWn8TENaeCa9B9qJ6WQYzaSa88hH/Ba6611b8RdnEM+WIQ7c3lnqsjHm0PSea8uLp9zS9mRsGswtdvtGpf22CHm9u7d+Cw1Osc8bXuj11/fHpoQw1s7ZMePOSyowpTlCaJSVX6OuPf4VRPw2mNDsUYUgapzyN1x4XStjpoqxsOJuR764fpbV16hRNuiCDOf9O93vvmUIdsZLl93DsQDguRAV3xn65BKFrVM56jF2P50Hjjw3sER8BbWQY+8x4vLeVY4QhEYbZJ/gMHDzXJRTXCHOeSRu3nKw+yLqyDrMzA5fEhYNQ5TeJdn3NaN63k/YgMHRtYeUGi/c9AHY2MpDmaPU23IlvN1ipIcWmo2iYgVHrI2PLnbvteK5azNuY14xJJ8+/WMccmzt49yuw4fw3bkPhdP3mvvY68ndbG57LPvR/bG+t6fxu+66HMz5++sX7+NeZaGXoqGgOHrAiKzZo75VFzJuJIZpCtzO6sy3vbsK9C+g41nB6y4jrll4lI9nTd5tlJTppZ2I86VjpgxdadkCfOY3cRy1QpKvYYgnXlzyIdCqwwf+RHLUlmzTc2HqJ3svPHnI9uGfLgCFkzv9h3nsr6yRM/o/NViOq2cNQsb7hy0qA8X9mDY2VBjlSkt2OO+mSmYlLWT3FWBq36+Mabld/jRshJ3lryaBWMO2H1l1dXYWN8SZkUetEPiO8Q1mWBlF8w5ZPWVX/H2zLB8kLEFcwQsj/8URaXN4KMjth3WEc8SQ5fuPxo2fBsuFyHM9OstfomWqyqHoxngKC8yD/tkZcgUUpOdHXnIgYd//rY5pnJt2TgR1j9HPLIAroyy2OywVZuT+5C6yjzMg5eRk3PIB+XUZT4QxVM8h3yolhsqjOGOqCi/DpE9cGpggYNFpOIwdfjkVGg+detkGUEes9gWl6KNNoeUiyxfY91ZKmRXaWqalViPTIh1IUpJwNQ6VldEstekTsv373S9MDMgRPgYj0h6BaMMrrF4FFrmHPHhFRYXYz8b4GxDPnDHbgp62yVIdyZt3PF6hxZ9T0FiL2eSUFAuLhytnV5qbdAjbeTnr/L7rT11W1sqMulpDtL1f1OmHKqiv5Z1zxIgR+TCBUFnFZ5SlG2ze2uma2aI4I9owAW1uq0IyHrJoEILWRhm5qhPz9g1k/tRgiCsQ+68hmdOIRbrlfdo72qC93yyg6kMyf21j0jb3/+qDxYRY6pve6pfv/8ItL0z2y8s3WoxRqZAzHEfPHY5kvMYRPO5OeTGLXsoXRsDUxNhXnPQBwUSqQwomrSzn/h9itBKkEHQPGQvVnIBUebevHMixnwOgaewv9w2BQCbl8KeB+wY7BxSrek+qb8RNUDRsc990XZrLyFy8QteRjeH8239jUcJkWcmXsPQqQqTzBwA6qTAMDICERM2ByywKF53qwVC2fZrDtBLCwVgJtGHtu4dq15724KvRsZoziEfjry5CKN7baseFS0exihKqcUuXSJzwIffiC1BI1LYqn1EHG7et9ky0hoFRzqL27FfSgRDTz5IdcgHkyfrKRWKrM8xh4C2fJyh4mSFF5EHPGLbeMApiHKapp+mMfuHE+WQKVcMFTPhkkCqAzzow7uKrxxJ6ZyfQ24x9Pefq4vHG34qx+y25TyiyJ4BlCHbGoPGiPYYsqvjXXQzDkIBebNsgLqYCm52fRUdRueQt8D/+e87EdrD8N3IxKpJvfmpLv/OeHV/0STW4ax59Q25fE5ltsF4B0x7WMnTJrHXDQ7YIOauPMln/vyCo2tzPSue7TnEaXeyzKo06V3Z09M6YC2B/qPXX2/8dmrEow50NYsYnTk23htxKxHeQ2kK+j5zzvmGbRZWywpU1E5nZpHf1qC/3VGaPlLGqEk790m3Cx7MGDIWdg7x669cNT6NL9mA1OzdM/d8mVi2LFacLJoxh+A65LoAPky7vsIv3SNGaRkybR5myAq9PMTpjdd6NqWWt0lvffzuoXrNjNzp6boizvzwbsiWKHOUXg8xwKv6uDD7zxFLEb4nywDoYPMQgbpzEO4c8JJLODw5WUZ3DtJDm6aXZGBTNG/nn5r3xWMywqyDqDyzR97E95vNYPS+nq6CdUpB75kwxgyEtsJhOUd8CH1C15lfH86FdRXhWTHlQnng03CyYvak/+CzG6aEOrz6E6D8RMgBZpNa7Sc+OC+oeGTuqogVfjxaWYdBJUMoIjRjDtAjvgqMaetQhL2DD11Lix+MKJw05fI91DPpG4Ph6XtT1oGqq24+kCthd53VHspxoSMzdeWjsNz6G7QyCJlR3EwhSopYclLmL5q+ZUDfXdNYzFH95/sf2+al5IaV8SNzxFo+5eZjMRQyIH0T7pmDuwW0tdYqiZLec8iiHVyyP7Uez/ak6298ircu3WLELF2E7pFJ+6xqYhNf9CNq3K/07yfWfjx6hVdGlEFT7t3D/nko95dYdam4o8nYyr+9MR/B2wyZ603Gms9RH4zGs90oz0/05JlD4OMPdWDGdwa1Lzvgb3l8K+yzqFv3Ip59Uu8+g/MXnPeMEY2IgeMxe3ztpexkl8dRGWJbyiol10SLbGO1IJp1zlEXfr8z2Fhyh5FFzaBJvViRHl5uvsApe6cwJr/rq3doPmCoGkT6mIfILyv0D7/yIZElmZz9ON7Wcu/9HZT5uPc1VVsxKDzCb3XGr5cViGKXjYh4BH4K9Q1pNMyKMPL47ETh7/NLA2T9H6b/9N6TpW5AxgDNIR8a8g1bejqTa3AdgppalCMYGxXHknumSrxQ3nVPSrMlyi4y1oVdM7j2N1uf3bvfsw/rkGfvcBG5PXpiVQhFXOYcCIuGsIZPzYbro2uiPxjSwSuOwE82K+bQYHXw2lqJhrS3Hx6Vfhe57GY6cZERyXOIXgw62cHYXTMnhE9xHn7EnjErRqFgSRP+yIASqlcHqKatxrqBQRmHzgPch+gpUyzLkSh93i74jyaeFtCh9Pe7M9p9TQ5p1bazeXxayT/1uQFXex/vEqSrFyuE1SqwHEosrmGSeaVz2AeDjTVh2t4UfTqED+c4656YKtPo5xA9nLGNwTyjKspkePSgW19Kii5XWYSVh8AG/C4938IgJyswzyEfLPq1M6RiWKWcDXyq/9Ursqop68HMMfdb+e1X/XmjqkipjhPybGMeeu63kv+6+VmuzVjQ7s0W+30zizGbPTVSlOPwqYZSG5EfctHOBknn5jVSrKdba5ExgZ4xYbdTq7sMUZExH+OeHevsxTvtLPe80utakrGjKoElLjxw/9+//+1+Zc1mM4L2Iw9j3d++/fp237HOWlKXRQ/nkPvCrACGpaJhfCOKp80xn0rg1J4ZKhUFW4RDLG85fyE7ClY7xRT15+JLchhltbw55PEo/32bXi0zpRBkbQvWfcwHXok1Z6ygXEj4BKk9dCpGRiXzkId9a2EWDXocg0RA6xyy4qRnxd7Ktw/dO4xuFeLwMHHJ7tg1hTi8ErDH43RhGRgm+qMJ4T4Cl62+QvwQsx2Hk26bG33ZqXOLqTRZsmJSfwoX76lC7gpIALe9yessZ1Urq4TGOXCo37HBWpBBmYI1h3wILmBdAlKVdZTnkA92iAGQfZN9MecQ3bZVJtNvWXHlg9fD1tDjne2b1hEPI89iFJgdqma9ZolfwC+o9zJd1FCdkTXQZsPHDw1VKPXS1Sf5SXxDLMjyOCuzeojvZ2XrwZDKRu0NP1KdXjnr14NMMytUsx4Dbjbq69xDqNO3ow35ZHhrfcYEWOXZr92H1nBx6rMossgvmKP02wK+sAyp2iaT11fTKdd4VCbchwR9SAzgzCCR6jOH6NlUqRUCMiJdd474JFj6MG0mzCoTOwxVf//59c/f/+rPUsp5jNgUQQFR53e++5lIrGi6EPWlsL5V4Cy4DeuI9JB3N3CrfCeLjLtzkKymS7gZ5+1J43efEvw9Q9CUk/ob4flQzvfOotYxcFc2Co2uqgEQmKOwxWrcQlHf+GZ3fjBskc0256Cb269JNeRdYnEnyvfNIKv0v5F1bQaeHvU9VkUEN6PQlk4XRowkEj/msEfHlT++P9WqkHxwSUH8uIuXi40zSnSDRAuNOeSpTr8iMK935lhzbbJe4hz0ScBExuJVC8vAh4ApS2GEWBnEWMVCgN78bzac3/Rs2ZyVO+R1m3pitceqsgwftXmfNjs3anGxKtoI+p1vXFsdG09LNpKdYz7kuxbGSAywFUaLD//O0/HMCGMwllNkPz4aEb4A+YlJmWsGf1YUX5RxRKUmAsXEXDkowg8fIuMRblDqLK1sFIsNfko/zS/jVVf8zUiqsyY1omiiYq1GehbtXm/KqJZ/6DCMb6M+m55bND62pLBmfEiZV9vxk6tlRnBnheVtOU+nyAOOzcTwqEQkoR4DEWsnk5P2C9Fqdh5iiRyARJmqOUD16PODCm50JawMH2rb5kSYoJq6LHw4B33QQSG7QqYrUAkfaa6vdp2XlAkM30ZQwCs+KpdOtfW+Lr3h0TZiO/i1c9urq/HFWBLUbt8AduPkaeWVK4bF2EvQjCq0F9m8YBnr+77KGj9zyH06D4A5Gms7TfPPk9Wz0rLjv6pvFLM66mj3z/97tRJbTGN2OlR+62HnX/irMbP+r2Iho73yxB1wUE1QmrTMIR9yHEwqjnmfctkofNCUkuWdzkdcy8LGCT7VHvOsU0bUtCuCD/p4Id7tU93dfubNAd/hmM90xjSwFMgyZcXxVn5Apq1YcKErrJnQaWwjVNYwRlBYMz3C017S4ppXZdg4NOlKuJ7+VZO7GIIaFWWfPmgLPQaMKSkGe3pW8X5w2AoGWIUWNZLnCPfhILNljSSgIsOJ9qpgd0xPneXvkpSVdFR4W1PjCx98OUT+Rv4QrdeM8pTDWqYGxWfS4G1/4lWXtztgRUj08GYz49tLdXkkItnaY45LH3arldSiLYpiTWkNNbmDU8xoISjGIUr/O8BsWClJGRV/aLy7tdx75n2fHbSlXIpHaNo7zeG/yn/+8Yj/A2bIqARwx0c15p/97p9jOvP+pIml+ODIL5PaldjncwWUHbHnkP1+3fc48651RZt55v4eY66UINfKKBpDfmbaLsab7uLwsuDmHLEWZH+wPcwlo5KIsefYXpK88rza28q/isv4UC1+67ktKaq+QQ0ke8jMYY9r83yVOMHf2f9snZvXtcY6qk+pypRWHgHyYVbefapDcb5Gv7Qyu2TkLAZXinYrH8B9U6u6d66RV4DCMzl35WGuM0x2Mq+Vh7yv5WTeoqVVQaOlKbqZoLtesqsFJjQWFYfncX2UQVpTeXd99k2x8cfwyaySYsaWSVH9PyX1EjJWhKD9CuxM/8onSKHmnBXpHZ/NDH/9+NcjN8L4Tkezi3XpR2WjufTrMfaWwBXt1uNWB/C5FmewWpLlNOawD9X5psfZ+CO5bb3DjziFO4CAYpt5FsovPJSd1SngSipRs9pF+mBPcJ5fVayKZSUe1XZeF/I1s2shEAoCKdE5kXZxdIXzTd+Lbwpye2Ybv8dc9YbCYIhotXsvso0vY1+aTQmP6kHrJh/1OX/9+HZLo9naN4+useKkBw2G1BtDWpnNyiPgyVouqJ8i5dY0XqQHgkDDhGeNmm1Oq5fq8ptSwgyy6op1aStJeZ97q2G66bUhHyQK9G6DiQpaTQ/H1jM2AUb2qKXpJfOhJwAh8y3QWGp6xKEv78Q77DNyQd6SZD7xrtBZX8GjmMY6xn4C68xThjtL7mxjPtY99oa1yazt8qfmCz7irBqnQPxk11Dnh1ZkSj/rM20z+2TcqnYkV7pyK5NdTByXtobFB6MZqpKzOsMzzFpL1QxJyX2I5epoayjqYfpPekFzgxm+Vex0yQf9KvvAcsVoiteZ+P+j//nt122ALr7Xmfav/ManTCqWWzyzrnDJJKT2xSVNr95mBUqnu6z8IyrHNHRe1ndh6jsJ8oS3ITJrjFW7v+GTEkFUoTstUzo9ShH+42e/Hz0DiZnHo8CCBHprh9FGx9ZE3/E5Qo15yp1Zaift7upqs+kpeNsVR1vaY0Qu81lpGWtR7BlpL4x+2fRsNMVV7R7ufWqvzcqdsYfRrjt9knApg8tDSwdKtCaXP4QvFRZ/2tuNHx5ixWyL1xJMU/zQ/yZ7i5c1Y12MKNx8JZgzh3JHsPZi1fLmIVN+/sq/fn6tf37/2x0rNHo2OZIULjyS9FdvfegVlDoc3livs7Bixpj1qMVt9uZDHg0aWxkaSseJF/UILgY2RhrNyr32xn2IZQAHzPWc5Efe7B3ur+MJpY/SpNI1y4aoSxmJmR6ANq+nM/9p2q0tt5pBGlC8OXJJXsapS6+H4ms22mZ9Kj3kS24UvXaIAfXr7yOLqCJ7E80hHwyNzdXE+F/6Tlmgbz6Qcy2ORmZNTfJKbx4m/cU21xMjKoZu2hB/iKEt68L0gKbJSuc85GNx+MycN4Pkfv5ZLWE5xoyGjFGCGbyolnA9LhsC66jKjtk9Fvku48BcvDZpaPT2MAUVDGtRYFenZ0qazb01d5fQib6vEWmMEbyMXOUR7xvW/yvfzHU0U62zsl4Jk6MkjznWWatVI1fiYoNJrO0cRim/kt/FN65rCxmjy8rzs/ZDcCdlRmlmSPMCD7mPesM2FW3P7bjr6yLs3Sv+frNl2FG8VGO9deHL99++b++VwAUzRC3MSQ4v8q2PYOks3ofM2/WzHsCk//n93w+7UA/ZZamGe8t48ZzM15//68Y0kFk/dEowDA/xryF/63+tur4dYcR3EZttV/3Nn+uPf39/vG0HYEpVlB9vP3UuqgaYIRw68rpVIShbFYBc6kNhtja8t3ap0VhdUXsMTnK692qx2FRbRquk/AJYedY1MwvAIlPhmXxpc3P1bBlpFqB839f1sA98+evbtz/msr/l9igpxfp/7aeLbvuh9PyhCwE6aNHUhXH8z//PFwPPJpivlV82ShNaILeEo75GhIhfXpEma7lBRjllkMXtF2a894aXLl2X8XjyB69xjxF0lDR4WuiKpRIsLJzmRZwOufrPH7//emaDzUbaWDZylvSJvryfxDQcXTp0wDrtFM9H9yJH+yjnd7VaKW6MtjpV3tTxmfx5mS9jHHR0TbfpQR6PuVwlKAsrsg3eHQEXyuTwyyo6sg3GN9qnEGbm5m+//vzj7aI4U9QhQIRcYF9fmB2AT/KbGkdvca1u+KZG86ZuP/7574vcDp/4z6IqvMhnjtMUF1//9t9fWZAxB75+gS8eq9+wLzTMjlvtx7fvFyXzWn7lb8O3XT8OX/7589utn9ceU3tnQW2fPPbuqmDHTID18rbdu0lJ85OvwJmTeBb3gLN2oVmI45fiKfzjIjWsMlxVPcNCmt537iU7ry+XWeJu7DeayXmXJXnPrvjS5aH4o7/yj2+/HjvMPHvYfKQsLIs8uno9F8nKbIvlaFu6LBL9l/dlPkmRL2Op73hVu04jXEQxppjDu+ThRgT3HRhYGx3uTLsQ4ZeDfZ20xTCzqIfxZV3N1XTo0Qwn5k7vMk/br8fri9RYj6yw+AcOovTlZ//25g/XbiZIvrwtTOstZGV43sLrlBAh9qb8Ntnb0Rio5PJOjduI7gOsHmbkrPalew/Jj1kVnhQiuu/laNjTIcSWzWN9+Y/fy9c/Ho9jxOKwvZXY7YvpPo5MLqS3bF+3I67b0VhYtKOx4Po1FjvX5vpRHfSkELmLiG9dJyhNntehKSyPhZHBbM4jhFeYvbCOOnLLK7exD/f++raglyi69WHoLLG0uxXvDY/VzhrgqBDdF7BlVsWT0VZ9b/QsSULOVUnEys71c7ODiXuXql4nn+x6Oau3tbzLhm9fu+9d8rkyjO4K0f3AsRMrHEmKn5nb+QooWp5PLJFYZ40KOSnkyZiRksbqjvTvlzy5D5vVhOSV5zSTQK+dbHy8Z292sxDZL29YeX2P1dIxvHwCM0P0eieuJNO9vAizodV1xhApjHfa4EZ0T79nn230UsLNvNLn8SXmgax+a+vE+7HDoHCmxOJCRAtsu+Smad0eIc/bV+/LmmYXC4+Sz8y+WtdCWH/o0KUUmU201i2OxJpDHQvSP0jdl1a+/lf+wf/L96oiS4B+VIBZP+23T2MsOAMbNxw6SRkofitXEZ97RCXLSpH2cdg+7gtgOxP03EKKAjoMMNaa9yIXRDJzX8ef+a97ebZDOxuLrJ+N4rPJzwaMUT732XzrBzz4fsdmTgP8si5nNqluWksjHsWyVlL7ZXVKsk6H0b9re66rcnx0T1xImUw0h19iWZXzYlUuDRaw7wo322fDCt+YNZQe3obPjRJW7BgSlhS8hITBbZAwFBZoZ4PKdaoKJCSsvh7JMStxFNCqhVaLAymJZnuxVyzRxnzAIGPvRf1/03sjcQnveezhLR/WqRxW0GWLmRkli8ot8xISEqscPYSgzINB/V/167J/LBSrPfuSr58OYv9YRvB9O+IeV2IQ84ghEzRIcv88vufBmsXf/vsuYDemqadr9PSit35qorcyYLpDODI+1nXGt/JSHqIGMEE8C3Gt1EmhhsJM7jBgLQs9okifC4VQ+I9TXkuQR9mohYprHaWD2F27sr5wU0I6E1WWFx52iD+rk/lhpWIyNfnXlX1JkAuoWsbVRl2lPE7WWYNvTXk74dD5+wOvRM8IaThlIqRMxFXyeSSF34W48fEcZp1OkirgTE4/7+vtlIwYclEuNxjJx0wmKk758tGE7Rmkl0IpNkWppJ39155qjQUGbyUp+wxvdjqJb+UyoB1O+3IQxBlsSU5R/2am+rpx1TH/bxqjPJybz70ohWVxb4u59yA+4pCevAycZ5yYpKY9u6uts2g5pVYOW9NyMWZdyxee/PPXLQemmyEVl+Q8DtV5mUeeRYJz16gfFbuvx0KTTa4+6IPay2/X0eM4ioissvud8Hh/1oYY6qGGrYSwEDYWcCWD8qAOz+7zQLohZ+q7/egqEPH9oG5M1HrzR9/5jTKuotOOarzLyqvD/dhm4QlrjJUfZS16gQ4Mh23PQ1kVWfEwBvEUVM5JUq6F0WnmHMmNfdcevUuxJ28HHnEo61fD9dUrDRBxzH5U8hawsv625TxlK7pmTVfeBaGknlW7WVNXJBQdpsz62+2wDbVFZ97FmbY9jusem8YMBd4W1ZWSdfNVujfyDqVZkindRulytpa8nGu8m8Lcl4zI+8Mwul6dCJtMsjiz/d9JXNsUNigXY4UekjQUhPjwSF13x2W+aW94sV6Hd620G3UzDylnAO46V8GdUujtSNZcv/n2UN7cIPUKZJVFpe0EkiMfyjsOZfukW25tBzdmZo1C6Jffzp1azkcE2LIeVvPX9UDGYmMWtvQwu9/dtvQLnzN6cMYotyXhymQiUTVIwjAeZo+8hbL4wY+2aZRxY1ysnDDI1SjTSslqt2mmWXFR4PDXLoU+cXgfspebwEreG4ssIsfm6X6o8onPnnkXjL/LhcQ00ElwNotsbBqoM8yX0SkfBjnrTKyu5ihN0qxlKLNulooHL82c8HbP3h19fLNjVHnN4F3C9vFuKY+DISzHMdv0LVecRqxpVMm4ZgmQ1RiLALPdu1yUdcqioqlYzxSYZcfs7TJ/VmwrlhQJffTve4pyT71JF8asCLIwWn7ihTdQMvBZCUSw+4Q0HAyJNs8qIIuJYUAdZ4z0SixV0WxNqxiUO2mTnIcpjUrr0mo8GwaeUQY3XDCWkfr7SOxC/DJBTg3tZieWoCTJcuHdEeXeYcaOOYFEeUsnwVtg9wSMCyW4mdVNlqv2soZnxS4Au11glFLIw5J3clDewmSrCJxscUZBTrOWySosewfmHk6hTJvHqsbSopHsH/wqUfpoDBeqNH7BYQ+4jvlR5MQOqsokWM9fJmFaRDyq0m2TCKvRv/lIcFiQ109uBxEMSyto0nwBXoLX2XTdnwk96+/TxfneT/9aXM38mpvEcODl8yhEoUVSLvxRy3hB0rOScQrKlxXVnnW8Nrt7KMS3an+59lkTw2AVWcCq/SoLyowjdl258SGsLChTYjY8lrI0B+Xpsf62XgtivTSiNHTOGjNC583Y+YIpkjTQF3HjgJqZFldl1pssp1o7uapcpLA9ECzd8U1WBBesDqnphmiQpfEU3ur5/fJjb60F5bazar44VXqJPSZlp44Kn4uq4grDzag8ZFili4HIz/gI1Vy29IgLeR3v+dnkZhGhIYE0AG5AGqLtlIPCnliBX+6Mj6H1M/tkncJhuXzaaqAXA0cQwPpd3HS7bErwjDIlW0cNSQXm1cGD1O7gUMf/+/ebuPQ0S7RKswewNj5n8fXvN7HJbjAPlprCWX1o1RTQx9iHNDsDbvKeRQaYQdp38T2JGZZ+G3x9p3ooV8uVeLu2H7BnJJMOTr0SrpirVQt5KLoS7Gp2B4J85Fqsn6T1NeSGZSZ4K4SrzOZ1l26bAgzJL88GGWlaSMo9pI13sZrCPJwU+ETaU0Ds2Iay+c8E5itxyHffGiloj2iZrnVpgGZxmmWX3iDgP/u/7pqB3ntmzFILAdq0EH4zyVqjoNMjg3qZLzOf4YdT2FdcDwxcK/F0DKyfveIgH+yTiGVzVE4trmwpeUacMSk84S5g/lCE+eHWbpRNi4eX+I+ebzOkHyVCqopgiG8v8Q0FcmVdTHuLU2tfbMIZ0zgSkDbKtFKW1GFq6JIyHZrK2Ydo9Bk6UpXHkNZDiGZU64/ojJVwezU+DoCqHOuhtd+RZ6yalCJ9xZDWg2LFlqHlkI4KSCCvVbC8H/3tud0++34GV64z6zzZk4I40mZdol6ytV7RCF56+L+f2kMKIZo4pPcRze6ojG22ks1S2uIRMf23frtVa47DdSs5B5o3j1kkWIyQXXhbIzZqbcuI+OWihK5oJEAyJfdytghaF0jbAqthfZ2itMrNQl/LDoeS82yIpcz3jVdf1/sKprZpduFVqK3CZ2zoLRwtrdZZzC5uz2fjsbFyUiT/wqPMQPnxn79u5YixD39YPh20q3kqdge5VWlDw00NL8mGgUeRt/X336f2vf+4QzGHb5XVGBm3gvYASgtUtdNTySx7/qu3C/lb5Rj5H/1rKd9/9J8/79N2PVEu8tmjXW2KZDH3rKDBI+r05uYec+mkbJvb9L8w+cjhnlnW56wSaFMZivvSpPlgVnp7re3xSvmRtnDW0VqJvfTzFR/yEYO3PH90R7ju/WGXsm1QpF0Cj4ShP7+1fz2Yhc/MqpUjZOVdBqeWXKc7/Y0IYSE/42G+f89PfIyRAYxEBLNS3BNqxmI7ixYpYWd9OAk0cpt1h5Vn4q20vTibyCBKqYX+iLG4Yqh7Jg+kLO8oZX6UbLjsw/zcEzUZBjVLzb3rB16TaLbMKOoX6bITh5v+drRdTNF7lrVZ2Ty/htF1F3oNKDVi9NLIZQvOSk0SpaJf31OovDSMSyO+g3Bt1npXxzH8ujU2F8zK5hhvjOLeQTfrfT5U+Md9NkguRcUGj2EV+6x8FGiKJQzfJaNuo+Q0qBQnnVYYNm5oEvimAHoMq9uwWnQNUSpKGKTlxRJjk+KkVwfPUhpPCZY6dhZ4Vp7CkZS788sam6fTp7x+/ig2tQT1uFad78ruwsoLYbTgOil7san6CRmFAkrDD4JkbDT1oBylpQE3DX5067Ih5Q3DZgUr1TBct8pjUHzqtjbHQkM64BGkSWs29Kh5aCu7TVp3lRUHpbxLUq0rw3VjGYDW7KpGuN3vYTuGoRwVrtI+0bDVVoVvbG5xRiUmjSItt7OU4/Jg++yMWZsihHETwsWxEns4oWghTF/GwxiFvTZA6QBE1qo3ByAxAz9ilddPslr9+CTWUClV6YObRSO3qEbA1n1Q9pzWrfTNxBisAhVo3coYTLTOSSvBLCX5/beHIEjVvmwPyjT38BAWGTn6ICNJWMPcSB2rkaY36chiMHfI8fFkQZ35b5bxELMg5VNDColVyFQU4BjXW8x6lM+OFJ66qdI1RvTGKNpJ3E/JAsScSFlTvKXxo84csIR9x22vr5O17lZeqV/3zvrgWFhIhe7MOVqoPVRTolc49lHGcv32TKLK2vOLqwmqUyNLSeqJuHnLp9uE8Z+ixaTN7ceQgVGGokzjpkyb+Ar5kToybso01NKZZYnsLCb0WtRYtibXKg1GmBQrIPmXJ1rGUOKhVz81ysy6YPdZQcxJBruxjHVYooKC01sKfP/xr4dxtrEm01XqQ8j++uuOcGbt3KTcFCUpveXA673fJXktXwYrze7E6vjCGRrDdpObNAeQsSslpZaq6/JCkHErpas1BUMiFY8pvXJ1pyP5aLe9fTes3+3W2hyUlDMyG79jjYiVLUUFZ0y+UpZpj61RW/8mjsyY5ZFQmjuJ1frvP397oCHPfKkpMptOtf5buQMHMkufgTJCclZqfVk4Jie9v81qbmJQIvfWWnnXmbSQO/pF2IX6fRLfHwwnjcpamRRlZMMeXspYB/3h2oCFFL680l3rb6zx3a+omh5zl1oyvdvD36zEDqx+SI5Om95tohsUmgS/xHr3awYPIT3Vx4BDijQSUfSz8EM6o+iX7XVWAllWH1nCobSO0tsPvrm3Z6dQvu0SzpLbTCfVMgYgaXmjQ51+HFvy09topPOB3Lq3w8w2v67KQzsc4Syvnl4NA95BaNIdRC6uQfp9xBBqVY7XrUYQrNnMsHn5Sb8pa77jCB0ltCG/saVUKEWnRGCR39iSYUWtVSOtg3Qo2i871+0vocRqoMJC/JbNMPPX+XIrzHZq2QsLIdttPx7vchH97uRj3MssLGiTpRuHXNvloyumS2hBh5d82YiQWWE+Oq+s5+XX8+oshd2RbrAShg2JtdnDKyqXMKwQgHE1tKMs80a4QgCWuTUdXWo3wvWt9MCoHrXLtynW0/w6+/EqhKti7bsv46ystZxS2E+p1eyHP7LiVtLdKmws8FUtMjuGwuHwuAxCLQLr0jLgisLm8Mi8dEoowRxtirSzLpZxbNLySVakl7fcWMb5o7Pe9sn1gApj35wVOwXBphlmF7xJUo2jTd1OmWo+TnLl6SCzEFzqgVqVyS1n5YEV87kIJUUJUQlWeMxP3jKXkt4ZglUAzQJC+egavhGub6ga5oxolOVvOjm03PyRzrwRbp5cB7yjiid3Vtx+ejx9yd5XRT8jLbCdX6ALWXG70uFLX0NE+aKE0yi7UoOkriZhjopKexTwvhxzLjXPfEzaZmhzpdtasq0ozQiEcWX7thO1o3j69sn1pJJHCE57TbSZ6JERMxgZKn6WCl95czKpJy+jAemIbX+qEIbVI2aQ0idF5AUx4zUavSlghUDOI8/cseak3+2sC/6sH2FeWXdKNjXReg6V8dKwQSqeROuDmYak4jUQRnv0NXOB1mgorGUzAPQyu/pZacCnqGWFsPTvdFSdW2YgrACJRQrErPDL6NfbhRUSta6w9bhpL4V8TQ4VRhBhW1Wa3a4UiRY3RAeuu2yUNxBp04fQlX60mtsot9cSssdTz9x+fONr1qPxTmFXW4g8I7Y6tRP523uIfB8WLIEi+NKWpDAtuNY3ZdfTFvKY4qzoU5TN3NztEbFFStKRQGlDcayzV1aaFdaf1vMpIdnuFN8EpS1Qy+XYYlQC6mh3ykdXeelKGQtK6/mYAMXbLsPP4hEbvzIH5306fWZpod7wdmPwGJqTWx93MwC/YUdRifiPZntEs5a8I5RYO+4mgFZ9ZwEtc/OikcabMQqrEEYy9GjWgzI2poZBqp3RrNwOImt2uclU4HgUPzt8dde5FtvjWWZ6PYTTL/IMB3CheEgSTUa766kujByjcg2i3Twjs2djs9L0ORsCbCrH4J1FJ7FP3Bz2rO4xmEXJTfgGb5lldUYKFuVg7TuO7vbTW6gVs1Tjot14GXZiusOWTQvl+wx++5VrfXqnurGz7Zy0x8SjNNTz0iQYto4u2XTcHPUVY4hndllYCK2SwWgqtNJQgobZ/kBkhWOIvB+KTTVupgLmlyGUJJlRdOuh2dGsNSAFdXSr+DG9Q9PyPaLDLf1sgDemKGfmNv5mSzI0FE4Y3cbfMDuTa5HyJx62hLusaYIcg3JOW0B9aI0F6lAW7reAuwC1paxwAb+qPqzc1d5IeVPTkPDqwnh903dW+7J0OUUfNlLXa2qtSkvkbA+x7mYcLncFf0avNE+LOY8SlfTW6I/KLWuxLPA2ZJnDE31cX2xgwcpqlZWvSgm2h1C6RyXzIO6WhEJ5ZjrK3w8bWiCb62x3Ln8/OJHdQtVTN0URBZs5IYyAQwuTjZs5gczsvKpEl8fNnBC6g1kEUyHcDXR8TqTgvqPdxc15eoy5aDclbK+ph8FqnWLTja8k+YX7F777QTGEz1YYi8F+zI4xMSgL2rzybVbehSx1xHjkxi+f5ZefHXVF/GwGBQYqKYSuYJW98twEed5ZRUrCekZQcwtdYyawIQXvkq2gPCZQUlNnb8xslVi5uFkTAHrLbkizWNysCYV1qEYKnI2bNcGD8egVG1/crAkOavfWKg8et2IwceC0ISv7Ps0JC0IuZVifFSCDuyuBGHUYUuJm45Yl76F3F5tyPXA9IhZ8o3ivnOVmQmBB1wZDKwkk8K4Zu+R+lG7iCEk5z82UYDsVl7zCoY/K669uLzdOtcP5rhwBbaGarJbXeJZLXTaW3J4lwhyiHYV617mS//Lj+4PjR1PHATW2b+6HFbONjL6qQrpnezPSGKx1SrdWPFPeX9War9MNCaxFWYJgtnBZv8yyjF9g0nbrVfjDPV5Vcd6Q8lg2O0IcDBGyU4TOnkefq/Hh8Exvn3xjBLh2lUGZXWtJH4RvDfUdePr7t78eEDgxRPQKnNpNCVAxAaGyBXED4DNxNWBVTizu0RydGpquGHTjFhlQE/F9LzJ8MyYtQrV11v+Hhui2xPeCI8SeNMJVALSBZA6dZiPcQujTrD2UpIs9Hq7+VyOd6w4O1tXcEfqx7FUSNVSirZRAYUZp5TG+z6BfdaqbPTnOSmFewZRpl9TW9TYbhgjSZM4wpjurLI9ckpxnMqsU6L5kyoq9L5kto5e1IzgiIxcNOBk1WtXkbE2NWVyWV8MdcVlsznwMSr7Jq92OIC+srjoAKZJY3dhC7aGx1qhUWExmPQhfc/FOSS9MW158YRjE6r1ksMmu3GWwRjJAKbc2m+6sgRMsB6wZUk1KdrOtsCwOraD0pM6mPKvFhDWVDoqynuzGVSrUyPdX+jGT3cIrQmrdofrNLbzC91GMj9qKNrXezUrNxmu/viUEpcSMxgbtm2kPGDHT8SVV2eS2MBgMxRAmZT/dHgZDs7a7UuNoNgTaqm70Ou1y0j6b3HZIA7sdyUs7SXJhg1aBGP07Ka2T20VwC8ylkxKCPktRry6S0f2Ark10Mw93ZjzFKrF5aXP1t5yd09Lc0q6ej+rnS1AINxZlek6oxE+mXT3vzZsCUj9JflMkTOZ1K86m2XTpKZnSbFJqFM0s+RWksqqOFp1GuIHUlhuLG4Ux+1WATHdYPe0hy9H4TdcDdC2DYu1Mm1cfsoeZACcJN108zZaVTeOZh1f/rmtWQhyk3IrNq59yM4x0pc6egozT9mQ7eLf0wTuI3xEYK4rG0lmAoZSgs1fVFr+UC/9TJYJJAZUvD8ACpcqA7VdHq3c90gc6irMSsfTgnQ2tZnvv++HxTFCxtqTd1Z8yq1SohHanzdXfmK871F7orp03vvxdibBIm6d/EJpy6n0r4Swm+PBipFcQvnICoghdij64iAosgLuP5ANFMGKPpct6MbO9ljyw2ce8p6qAFFan+7++//Hra/vx/RmXgyNCVRAV7ob3xjqgC1FWmk24a2C1NMqhyQSqNDPZWYur/b68tdOcnrwIuEUbRWMLVafIH9xL3Q6W6KFb5YQPlfnqtMpiv/aonPChMl/xhKax6HTSl562kPw8SptFRBTC1YzLWHaG7ipz3JzuNSTmjUqwd9oS3b23LWumvrQlunvG5VgUN3PaAvKxxNDDoXUvBzkT3deD7DN2zyqWpERb1T9+f5WM9IfPtmn/3NNxfGeuLS8HbSi3YWzu6DyzUe4gqrvA+6QtPm2SKpo+lNCNtCnRdrRoPSknuUXjl+FLraCwsrgln7Taajl6sSz7Hq8HdHGIkkfvSobD7PW2PgsaMbKcUERqhD2hvEX0QeGlmyc+1Ox9VOrFpS38vqY+UiwyUC/FI1Bv5rpev9+yTQmlHS2JinW1Dz8aKtcp7bktBgyrTVUj3Yt8d2oxQpRZzynt3M5VYqhLCqrYtPfUpiBTyjemTXunPN1QVToY0qG955+3qbfySy5Hbs/21fWoCMB3ZxXd5WzlcvEHS1idEtoxewGu+DkwQx5KPe+0OeUZkOeCUsUCY/akCjIFhxdyhin3QyqMI7EffQ7tQuq+tG9LreTBmkYN72f/zOxjWn/Qfs3tnw8PLisGFGTlXJjNDXnA6hJiCMbIVkRlMzFcxLfIc4V1BJEzx8QoiAnSPCBxF2B2ShRf9sTLIZETzcRREHfWpEOoogwUEydBXO3stuxFDimYGay/bvVkXSMcs1hp7U4LrFWWoyD6RiuOEKIJxZNwQYA5Y/qfLMQTP18fNeogqYmfpoEoIsqYGiT1MK20Lqu7MfXbD1n/9uP3G+z2Tr12FD0hmPxwRC7OG0gtxSpVcibfqrA5VyfmFeCJKdNuuDCzoLx4qGA2534LFaxvykN1uxuyzibyslQPzO6aayiS7bNhgjLN3RxArXdvmuD7TLkLsxZnXbIqMDfMVp1bXfoQOisBQtlg0q1mXk0GaAxhD2BKWj1sNWfsjoQsh6Pt5+OZheFMFhXJmTAdZZ2e/gBD5AxKuynrt1vUbHq5uZRF+d13QnwtTQC5U36XZdFVMCCT4Jl0Q4YZXXRBuU9+j74wDMaL9uPSc4LTtCgcsdOht5Xzcy21LJMkmHITZbnw2UcrUClTxkUTCDn6eGQdboSb9XOG9WFTDn4zIsxK3CZLawMTbvpo6lBrEDYwJtyMnzTts92JjDGm9Htc3eCb1ETkD5ytXJ/Z0805BnuoiDqlCr5l5c65pIg6JfuetVQXkwy9BROUkutQY/Gy9ggTbxmafdQRuiJAj6T73/98WBo8g2bjRP4g3/MjzWwtWM0qezLCccLUVlIj2RKd9MjNFnFbwkJnhq40mWBKaf+ZG9E7KpIZgiAmR1TAiUg7JgY5Y8Pa9lQXJDcCVGK3KFibs8a8gL7032v+8fXXz7tETrfkmh3KI9qTCGJn9ndAhPW7VwvIp6/cxmZqE1oG4/k9HYf42Q2hpjPhlhhtHSPsprw53IRXKxVrcEJpYsot4rZ36zAO5TbgVhbOzR4sMSqgDpUH53KC6kXkJxPLB1cH6wN8HRRi+eBMY3ZyNLHcJrwlug0W9ilboYjD7Ki8UKbumYeTIkFfBfwelHwPIZigXJbNroE5MakMCWTCLSQQSmlw5BAst+osez8tYE8GDdkYYVBi8l00BYb3fcgOJEy62yyiicZkK4LtmXTPjOqZNSIblNd1+v4XTW9W38y2KjCSkkJuM/GJOeHVBRN32162gO4IgN1I7W4GZGziQfrVmXQHEx1r9SEqd3FaMbYaAawbDBgg4iCYWETOZM+qXBLJhEwq7N2sINrD4r+R7ufmncWEVgEqcT83S236jLWv7jEbDJH5+UoHAZPuGYeNuYe0g4JJmx8ulGa9HcK7xZQ7XJheAZu1b264O+XRcyXR5A5my3B5tZCZfCNpNmby8wSuLO/mWrBZ2askrH0xxJndoJDuJzAqTVSoGAnSfgLOZyhWdhJj0j2+oLHqUGqTc7Will+ed8V3eVus2V8M/3q01mtfFfAbqNaG8slasz+CwOrPGDJoEI7W6PdZzdD2PuRWWSNSbyGw5tc10v0AZtoAlS7srbOiwSrranNAQdFmrTVf6o/vNz9uwYWUspTJ1m4ymVxnPbXKJzA7pa9x+DlHOJOvwkIZ3unyi/+kQLIjG4kfrF131UGv4J3UAuyW2E/Imq+XVVqZcAvKGwS9kxSHdqsA0GZpqOEkTLZb5b3BCkAimYAJ1u0hkyyEuiyNwoRbHRMys1CBsGAy4WrAjqnPGtKS89hd42+R0VVwUul7N6R/Rn7wQSZLwioJ1sFeXdLxNY7K9dgD+WN3tQcjcY2VgfzTE+IlVrJ7aXzGanHWBFMo98rf1hY7pMcS7Kbrz3pQKUmnOROu/ujGKC2RdkBbqIDFmMvhVFrfBSv68l30Yab3SxpbrBehkmnGksnoICbd0i3C9D7KCltMuOUeUWbpaLTVb8HECfgqySQlJnyj1K9bjoLtBbyieNuwGaVdq9QUzw1Tblg15GzO4skrodtecWy1ZOXNbXH5ma88HtWQt0nC/pRmlecgvHVM+VYUHmkBzNEh9iLtN3YLuccCLScUZVeY8IgVftaOYF4Xspc9lJg6Seo0fVdBeiTAwlbsuzEPz152Y2NKpfgLfzI4kxQ5BiL1i1kzY0RppLFbjj4fPtIYEvda2EFnN4Ylg6z3xaSrbJgVsYuSmcSE6yGMzJyrSBcwE26WkdY86xQa4SobfM+edS+p+1jcvWYpMNTFpNz9TadutnqWncqPb7X1mu8Jq8wOYMLt7lNzBrMIV2HCE5bWdHec9pDReFkKmslX1sOYmKaAUla/A505p+xlQXz+7+/7X3/Ufzwea611ZEXkbnHyrCO/QurlDEhU5omRuayRNnFLu0OMdf9GToYzMOkWAeAczX5+8uXRntFAjIpTUS7zVpMvEbQAQwQ+zMTm9dYXqmk4BQ8LXdonVmGP2u7r5r+T8b/+8e0fv9+F6xntVAcK790y8rvjNbWscKl3CMD7s9eZBpYTlpSjEnp0B/JGCRJk0i2kb1YkKrJsDBNuchowpaDYKGzcWRmGPnsbK2cat0gaDCylkvTuM+V6VNPoEWzSCHF7ULPOooy4ZMKtmFhrxZoG0pJihe5casjQnPKcN93ZRZ9K1U4onSd0mflLdXyaojIIk1rp53MdCoKTfj77juD/+v3H7/91dxpvjqiS7CXD5EedpSW4zuTB6q4i047ifUthO+sYWJz5F8sOJxGj7I2rskMUU27Rr4mXZ7BI67xN0lgIbRbyHiISjYnjw2R7VStvPcah+FPsnq5viOGyrAAAblbse1aXY/lSEsnOCUxptzp0qVNGmVDClG6jxFAAsoQgbqbqPykJpntBueJupuo/KadqmEqXLlQ3q/U9KVntIlsV27Kb1frWFYUZIi+92c7QfVnSyV5K7jGQKA/C1Eewzfz2XTDfl1liVJlF2mZRUu9GVgcBZ7ezYpkZEGV1Rabcziq0YHqXjZKYcjurYUq1XZaHYsrtrPipjJEUY5qz21mZCtGQ7HjGlPtZ+Ra9U6IP3OHzX/bUhMYnZiSfcUf1/YU60kixy3IuTK2cF85ujk0W1GLq7byCn6VRvVRFndvOK7jkZqKIeK5uMwFMhKDlNTDhdlyUWF8mmSTPlNtxNQosj5tGuR0XyyPEIkuqMOV2XMb6gllm9TLl9rSy65UZq0jYY8rzaf3+YMQEPNtEChfaM/qjY3ZpZP49U66hoMmO1INRjslvx9QbQolGShh39MhbLkriB2iUbDmmPs7q4taz62sNyqL8dlashLl4ROFulNtZUSzZhSjlkPPbCZAbLWZFaXabJcC14SPK1PPZfnsNSzR8qkPWMGTC7Yn4xLD1rFK2UO5l9O3MjkCZecSUe8AFGN5LkB47F45GL7+PC5HAYO0uDoVXhV0CeY8xklRrXRCRnrmaSFFhEWHLk2QYXMwhKjZK3GJCYy8uKJqQe5X8++ZWuEC2e6uoA25z5jNDaQ6qVJlc2FTWGPhMFHDt9pp/vrAONET2PRMqj8Sw/E61K3dqywYII9RRlJAXt6fpByw1KbjVCZuCq2BlRyQmXHF4CtGPKAO2mXDF4RhySFkWCmbCLTBmztGYonBS2OyZsyOZgaYtZ9NpSwLPd1HuO56Fl/52x4uPNFl0k5qNw12ttdUOC1l6pxxq1Wa7ZXhuZVtQJt+8A36QTUXxTbg9+WCmqQVQHIlOJB9MZR/t4faEhfRIBlnD/VJyqTnZSpXpt0zB4FNOXpa/YMq9sX1hVpmdInq25AJfGXxZRa1wtIX7VdbxWE4q139vozc7ghnZhYYJN3d9MCaeXZNXwv2dBKxDiepwm2mBVY7ou9d+eitQNlyyLEoVwq3aYgF0XbG8uc2mwFfO+FI0wt3yVhFSU4TdllYAnQDPiszLlROedsdvPhiQHnEnrAQ9tWRItslm0rDx5amdatgxbu6WiAN8topgOAr2L4psGTM9BqTZ320ZAwyfvHeK08Ntaflj1Jir4jlzu489pYhZcca7rXKftZ3xteJf5FuzNRp1vtomE5eZcisiUjJLzwTKJd7TBCJWOqpzbIRbXV/oOSclVNel7YRYx2den5RXnvBLa98euuvo/Hxl6gFT0heW70tAduZjxBJkgIdnhfwkvvKdyFKtyiP2Zl1WLoOyV6IRvZHBUcU3mHXRFGJpwuhMXE2V9g5vZOEm1uExZtnfGbyVXZK97dGFIrL6ZmdhJU2vZIblaCSz8HbL6MU2903ZtK3nXPV84ZSKH7Om1cZ+YqlZNsBhwq3LEkS+CFEjXHkp736HEiQv9buHuxcWYLIXFhNu6J3BCRxtFDbCLRbXw4AUJIvwe1R7yYN8EsnjTLiHa8y7l/CoP76SCrbbWx8uSG3MH43iluZvg4LtZ7e/Jy7wYSkgcTbOwubrsEWGLvgjhHe1bEYGiAEVLuzDozbaWaWFEVWwVZ3MUs/xVlH7LIsjPTE+PAM2Lz80P4cerchgBv/G6gf5uVSDnoaWEeMPxD5rEZ0L7R4IzhyBlfhu/HdOBOpgBctJq41/Vsv214HmWg5uvk2bHtO256YghVq6jN/zELUTMnxdrZO81Z/No//84wueXNATpBJkoK4/MO86FVYPjPfHrV3JvbbhJffqkoyU9KjNnFpiaXjU0lxuCx0V594Q+LxdzA2ib1V0B2J6us7znEsaM7ICFeKji/AkPr+ci2dVoEortI9euYfBh26sdqBJ20Uzsw7pHVqzrvMwyW9P1ObmsRnRDoTp4Ul/fp/vVuv+rXRt9Kh9P2GyFI5WSCs9PenPc2I1rQbvZACET1GbT6qYGNUPhT5p36fCW5+PRhJP+vDqrjMbUr5MBuf3h7XTiSPS0ZneafQBKFvKcj/Dq3CuoJ8Bl2NkbT5BozfWIKNHjR40+o7ZZz+0+aC6Xj8DrkCaEoMh5RWOSNUXWcQHgj3iT+6anwwwbJMMMlgrHlSvFJpPEheGM47u8piGmq0BCVuC9crz4KN37rQ+ruRBIafUfEMl9i4cOXUreXB89MPLyxisutnQDaWqbbbVmbVlkUSS5QWrsbwwQg4QpAQLNilHya8o06HEr+ROYzSzhUA2imU2uMPwcFLm6aDKSTSvYUr1yobkiFGJdNGEM9ftOh4bGuQuw8mDe+7fyXn7bCQ3FK9DOAPgLjmHruSQlK3z2q3y3pTeZfFzJj/24pwCDI85BmlXDUf51/kKrs86l89YiG0WUZkFsCRPvknWHKbFWO4zsd7hw5CsNgSdFaYyS11IERqC1egrmZngJ5rjzgg39dyjAxrHpq/0KutkTcOMUKWoC0FlnSXGzrqEtGqFoN5DnG2YDmvtRr+85nDtT6rpKH+70dOT/nxwLc9I1K7NJ6r7yWCdnKyMwvTq+daGlnGmxPbhGXF2CYopeI+yeBu1v6lPbNdzcmc2zToXUGFAGNHW0JW7A6SKuWRNH1FZK6p3k/n0VOSVu49W2/sUWY8jjwq9etdqxVaTEpcZUL9rviTW0xUxjct6L20Dah49KncNozb/CKWxMJXu1oBKS0HXgWryytfJaF8HOxXstz680S+7SdfsYySflZtMTnspBiARoVTdAnmNPsJglJGV20PL7l+qIasGsR6lqpbdOZqrvHbn4iMVYBpDla/rUtt6BhEanyXSZm9LJAca3yT1nfuKsRFop5W01fKTaDPvQ9JH9a2A5xVb7e1GlY+3lpmVO+U2RJWPO2stjKRAzqi+rRlAWo5uVBu9+rYQbXdFe1tR5eMsU6hl0L6v8mXPD70zA1Lo1fOiWpDfr8KrosqXp3XQV20/k3penjeIN1TafMPeBqXEXJOCD8FoaJXsbOiXpEEEjAYvTDcZwcpjBaMu05XYccgmJgCH9XF5hHa25xgohSdYqzFMx0CBihKdAgy1/9nLz9/6c+7O52itMhdnJLuc1XhGQPmg4EDCmw7avQ++K2gRnFdweZ/tatqQJU7gsLk9TUVoQ2dtQFp/wCW5i23UXvNhJFxm4rUbYF1CskkGB8KBRldTRLWl9R6khgBeU1cGq6nVdOV+BaN83YTaUjBSpMERcvACIKfIscZbe+aErR/XFDkWfgXOIi0rOShHlFwcfH3fYIgW8ncCzMsR/Nevb/cP8N/0s0LF8gOqudA7g80WadUB0CyjzMJSL0Wa6QDUg2p5Yktlc0B72db76RqWdlc4UtkXoDh6M6wbSXEJl498eR3YeqqI0gwI7+D7bTIZnYklK0xssRqeS2W9sloqUqGCw2r4VKi6fRWKVW4BarvO7KW45BQec+S4L5ZXhpM9ZifzkQBRWSeW2QkCpbYDuBjGLjMwK/+hF+XxqfZOBPPaGoU8KeR51kBUL/xZ9H5e+u9//e3W1jpDsgNxL1IJyL46GV5at2sDjiJ06y4ePfReHOyyX7c0w9DeTGP97HWeh6n2msks95KNxEtAqgkzG1tbQ+XtESpPldlv6HhUSlpndKQP/f7XkZV1SdZcamiKKQUoaVc+Qm4WlTt8tMJjtn5/m5XYcDQRW6qAQTwM2b//9ffpP7jkMKNh9CirnUCULwRCjTa/YyPXexAv+8R67XsIpoGShgdREwqFGZ/zTqqZEKPkNay9Nt59I+f+dne/duZ6rpH13d4VxpSsdg9i9ayzaDJBtay3OqO3g7I1ya9bcwM3mFlHUg2BZ2/7iwuPTq0easL6/dvPMy/bvZfGWcOamvL9R0T1xXB8qf0syfekRmPlO6RhfAJvFGpl7ikwKzCynxZTa1ZjxlaNzDsey5uF/PE+tmdOKaLpB1x9GtnQPuXODI27zBShjJHzuxjWNsbuYy6nZSJGuoc0XMe4fcxt6pq9Kop8Y3jYiZ/GbWMDNrLKZjntxRjXSkDFjYbuyfjPLqwxMURrWWov6JL2ClzHXs5apgu9V8W5geZIswKiX17Z+SrbKAXwCGVctvNpSl2vU4g1JqdoJqjC2FRa7bFKuIMLjL3ae1BrZRhp1McFxl7GikxI0UqQjKoVNoYEUIPEL+g13s+SZe6+lNMYNHRk82warSA7DJphepqDs6/SO4ILSr4qXTD+Tjlr5Nq+TyQ4E7EUcg3xeD9bFCjFXzCo3g7vSnIk03YRjORABZExtWKyQjiqJPz575//615qtAan71P5OsivN1/jwCorcCEonBZGnHmxUn9ENM+53HZ+P3xNygtHDZAEwN5ISSlF1Pax5enITlJKIBnJ92POBSNK6IJklcdBZHitSlwrklOmDtMF798RVCszIPrEDKqrhWxXHtQBpjYxgYURTJFSHaMm1WkGVkalPBhG7UGxztYTZqmkYtTEYjNp5goqH1cNMrOPUh0SSuGBF9aVTqtuslUR0UmBUq+4ZmgKo0nvQpubUcvVWSBH6oRklNc3sgu1GMkJyFiFz5gZ8YZR3mAy2hl1ZvDmNOeu5BoXQ+zMlg59ABfy967//ef3r7P2/SkyLYUWtMggMpp2TcMGsFk+ETIqui8zM0Ux1dOBQV7p19fHXWDt10heQIuP9/JjmDIrfMugGbLaXEJhPokgLyQ5o5BjmNWWs/J1px1ricblVOQVI9WD7HuvsVT5UskFZamRtaEwFIxJTntMpdbOj0myDXIaKHAUQkhO2xndGMo6ZNb23WsbOWu0haTEEZDXpHyvhnmSks9FXtvI1EqPVrEOkddsYZW5j0nvXK2VHLRTzd0yLFAqBRNo+46Nxfxp9l3IUdsZ17yxkJQ7o1qHDMQyZY58TOgkW6ozB4CiMnXUph5rDa5qK1XlMCPO4JuRBZeJHh7TS6HKJc8QQflx0g6pddtnDWSBISiqNugwTZOGQEjWo6U7M73f//p7r7++3VMajGCnRVcuICmbGWbS5gjKHU6apDTR2ZQVdfPsYf5qBnafKzDwVGo0xHdrsodTG1LzXsrIqDotaHj+Kyu5wNFrew+hsLNhnZWupWhR3fVQcu1DXoFoHy1FL0fgbARTlKcRnQaWmK1Py5B0uBx9pIUmV72zA+XbiO6RxHBd9pzIFSM5WNQZXrGZ7FHTnhby20b89deft90p+cjITbGIR5WJ5Z6xkgINZ2Pm337Vnz/fBe7/GPmGHTPgpeS3BFlujb/R2Dokh5mSe8CgdQh8GOL5mbAO1ZUh+GHIYG2BtZoXovBuGfK+F7/9+vOP2Xro99eW+etyh6nfKSfoFURXe2dV3Evz0tGXeadGFrcHiMKF+n2bzrjjaw3OdueN1KiOJsn79atthsE6qVFFLdwkutxCU8Dr0V53ozat1tSkF+zosbs59BnmjqhwqqgqSDYUw6heYSmovYQ+645YRcGPqEEuxhQZz8T458OZ3enejVBet+GLO11bJr5MgVKcH83SXhWo+v0yZ2ZlViJsX33LZj3k1+ftiXTIVqq2S3d1us0fT99p9z7HaKVZNd02hEl/Vm7wLNHHIGmASrdVoD9USN96SlSV79+es+d8gs3DtCM0aNnQUxc/LYenFbA43ymDlHNJu2vFtpyHwnYTqAY0Uy351OWjTYcH6mf968dDefAA5ZDTy+wPSPJa7FfWTq5I9I4mjCL6EUN6BrNcQWWsKxd00q2UaImfvvxEvvPlP3xcT7ZwNPQ5HD//vgZUa9y8u3I6keRmhtax9OMpLp9Pl2F0kRrBOqpnPPdy+ZPqRauUuy9i89EYJxlDAG98AcEx0RwNUBdqvgV8MWU8IhqD6kOJw3knXzqaw3A8HTMLIJh595i7ULLRaPABfMNMsgEfmsNuPLPgLiU4Bx+TdEGhOcssvEpCXPvIyCSOJtAAHl0TdiNLS9lkmRyDR++Ew5Zwi8NiRysCWKNZbKIXh+IvdwvK1wM85n7yy5jzTENXlhq0KGB+TNTrEH4WPGr03y/w9pxUX4PEyWjAymtTZrsxgyLrDs2hL/3sd9hAxjF8jaJOPprDsf8kBsfSwhkRQo/m4HyT+EKPbfjQgmAbeBSyf+PvKx6BtdNUZPMYPKvZL2g94+yNZ5QDUpU2KDkSDSHr0Rxu99fH70ZE0RcjOwChOWyE5duvi7UTi89+VJJe3+jK7m6tpLiuqPloVGOiSazOZi8iANDEB8Q6Jd8MW4AsY5LQPHjprQ30Dti6MvUYn+zlikkJrnt3GDaXexuTLjn4RB1G5VDtk91djJ1ajoo1H60aPdYn8wIrQBZaozHqFAc/ael0QXt4CFdQA6PGWGRsN5NHjffWWTWygFDc0NoFRFymKmSsrcRqM73Vvs/Mi7dnCGcEWuvl0RpG5t03IcTwqMG84RNM5HORfmK0mhgIxY8+ZBUztLrJb8zioDIHE4/KyBsz9Ym5EZCwheJRH/kgvy4x8w1sMh0JrVPBJAvr2apFoAe0Xgk1ypPjdwmW0IYrUu494jTljppMJRnLi/b2YD3nw1gyIxxgPj3p36VTXr+wjTE28rsdQhlB+3QdXYCvF5eVPFK0aB4ber5ZH3AQgkghQovqxQwMDcsYItUcj4qlL8/zebjJxmJCFiodWlyg/HkxY2KcHY/AFHzSH/6LiWh+r7eEKmnWmYwirB6Peof7M8x9QIWh8JzkFHacWVhm805of8YN4KwgOOfCGjVrOywTfv68HdClMgg9HNDLIh5uElYabi189q5EkE/AXWxznth55VyzMZ517Z5rdncGWn+aDf1A9F6yHmfVK5qY3hcZb4ezVtpc9Et5v+Wnj7V5K/VwPOqgbZ6bGV5ZUcTjoHNKjNvgjY5Z4eLuMjH9/lBGCpoMWcbto1u5wxX8Rc6YnIXJCN0V7Pr8fIRhXPbCs4nOG8k3ewkZg3LXzsJjP/+z/+uWQNXnmpGUY/KqKZBY0gSHIgoNnVfVRhZYBv1h+H7yNhcWRfCO7CDDjDlnYbrCs4bQe8TTszz4ARMkoeujQ9WEGFIGW6tEMA6DvA2vRo3jnTK0PEd39gD6F+vif3771v6499UWSDXLdHZ0pOiPjBkglncotrdP6oOhzGKnlxJ5ArHco61WJgWjS+pZxGFNC9Ifid48ZeR5k/ihzl6IUlny5ikjb7du84x+hY+cyb1CzjgvxYjaZECZTM/kvRJlzeTak5xljRgxSwHsjaIWzP4LOKRnCb21ysd96NWdgTiwkDuVtTE46Wcy8/L5U0n98e1ftx7GC/WMZoTDkMmdQt5jKaEaqRd6NYmTsXJ31Uvh6Fc71/W2GHc3JXkXz2oVq+xqGdAUI+H+o/zEUzSG2V4LgwgiwbP+xG79JzubnEj2dhagWKeDtvRsDye5XciPW3ZvJMsHxk9DGlk8KqXrfadYY5EYwKMq3xgpOxePruzLTA6v22MmeTJZg9q3F4FyWf48629HOfUFHfkjnHWaTC7m52O3WJXLHp924AvY8VX3VQZkM1PX6lW0zMTkhCUMz0ILJwy5rNhu1GFkVD6Gg8u05+SLN4x8D+dPWqjDRX3cgUs8x0xUj2yR586H47k+ZgOmGVdlSB6GRS+4jCxt6ntWCp7g1MfkZro/3zE5/QMpvKvOrSlD/JxSl6n/GFQPFouS4WAo+3kI9GU/afSIZojkZQw+aQsozIRZ7hVxvOFwr7z2/4EyZ6oLKk0dMQRFE4qj9BDfXVEW2R8ObvCvP//48b1+fYRy24JhBujJ7UEtksQE1uWKk0aaQA/jxYXUeG9qalJvCg/f99ff/jlbbt0VIJxrQVYywUBamI3n68n8PsglL7Fiz+j15iC7IpMeMCzWnbtmDwNCkhltGKIaMD59nXxiXi76aSt/L/o+uNFKfycOLxgpxIfbf2POJdKrUKtgWyHKcOKYcjPBSxU8xAWoXmbkiDVmGe2EIT436bKwdcb20YjMEMY/9oNVG32efYnFNoF52JJXiyIriKafSQphGRMe7OWqWuNMYS1cmxSuk7rOOrJYcijVcLBWuRqMvyCVd/PZ5djAPrh1+Yt/5/b62j6wtSLVF7B3o5kbCcfZoaU3yZDAKhzAM7ND6PJug3valC6G2nMdTnpvmVw1jDroHRJKhg1+sTvcDMCyAJSNH5neKQdWHPAJyDBLJvcKucFE1svYASZ/XocLirURcqlS5T3THtdc/hBzBNkpEM+sxw24wejTb6/sjGriqsxSvJUBqEwfVRNzc7Oqy+um2YX8fQvqj39///XtyDC6eOmMz7QKEILgtUkFKgBVVvdDCE/N+nJBs2ZHVobm4ZnNuO5/m40vXZdmAYCnmnAVO2iR0aeTHAhA05+caTlGUC4DaPrTbHJEdWjk2t3pPmFutHMea+Niq73yjl3ssx3kdtWY/Mwknt03bt6cWHCnXZ+e1EqIWCNrq6e9LNykPhx8jzywlkPofuyeIyY+HuDTDmAbI/KzBwssxGosB8Ns1p7sjn4mver6DJ3AyKpXk5607zPqDN7VXfGb9FHhxtlToRT2lBwmPzx8r9Jz//nXf/9+C97OPKRW2MMi5hjdM+SM7U5Y/ZgeVJ9WMjiSuGWT3ClXvkaTJ17d5Mkkv10xotZa6qN3YSqZgx7tVS6zJSV7htBt1I84gcsqmpOLKHwOkxrltweDkVHc7kma1PGmvtjxlObglNMFDYYxHo3WHQwWn+SHevlKmWAt8OIfBhj3i4iUOUAtQ9lrjy3VHZtP+keY2AObv5o+uL4nZM4BIB7i4Es2elFWi5pLsVMvhHFXvJn8SMeo3y+LX4uzwqXfcz0nrbt35hmyQH6WJtsR6hzg7wH1R67/uHl9yNgb7jBkjiHJo4yzPlHZfeGTWrkJIWeMoe+CeVIrEAfMmMWqd5MoUx/e1vdF+Ge7R3iCSKJe+xyh5DSGUavJItZlUj/w++ORRJYMLCF3KTsHqKZ9k9HHJOq/zZwXNZSpjt4qiUzkSa/ZbDIBuSwCUia5al6jamMyaceAk161xAQTmYsP7fuq0xWzMyM6KVWSriMHVoRK8zsIsTO+/LHci0Xxq2XYuFvMJvkdT8Ov5XJ3z84ip8LrF3qFXUaoBCCCEib1g11e6QE5zk4gUsAlVAWiNbMuIu1K2aTX/aK5B77Me3LDpNeUdWLdpNm0w+lJrpX2K6WFDribApg8PtHfNfmUqykiB4zJ01O23Ua24tGIzKJJrupupQw4LdArOSiTAUb2FouUy2dA2Pr1EPLAYpVjTVqkqOnMoYKQmc4YpS7MrOhFRqgZk1qJA+qpZN9ASCl3hqa9gz9vqVYyzj/K1xWsWHwsKcjr64xRgr0h5lGCiFOf1CSfxizFH6vRdkWJl5zfCChqzkxq7XIx8h02WYG3GX5pqoUxvZog8TaTK95MRwzJ4nGeuFDHhwR5sMg4RnCkbIxTY6itCVVUkprUS3Hd63plU20QyVyTXouRyXaUXMMeZMLkT3/mhfccDZfannM+qY9T+vbrDrJlAD1GEaLPnTFyu/UZsUI+Om+vcwnKO7I95BaP1F77pD71xafQc0jYhMx2BhSPc8qRob/w8U7qh9X2gtmM2yi+47WeMNudEXJH7Z7llEbMle+vEJPOHBGt48/F4MYwxZQq6o0wvV5nghlBZO77WoJfbkG67A1fBw+p3/769SM/bCyxQKhVXp8zImuDB7UUU4QDi8mtmjJkkVqyYzdsML1T1zHr5uKIQil01qkykKjx40oCDbmjk/fJAC9vYB2hkTBBTXrVZ2DNCDlKmcz0qoce2vzBJFnmGXu0OACMTwhVVC9l6qBiOYcp48jKYQUtlMUiv6Q6BI52Z6DSwniML20E4Wua1FFhmtRnWwyn7HxQgZnJZt5phf7QfbeTIui9JyNF2xkBtdG7YGbhJWWxx7P82x/fyu1FJssbk4Q+6OzD3fhU11whF2reI3rnALq5/vMJ+9FmUQqBdJ3FBelekn9GjVazO+8m/XF1vn378c/879ti8YoZPvw1y5TOekqsgs1B94hhc/WorPrQ9fafiGWweiIqtMwBKG9QmF2+pft5Ui/mkAvwpjpDv6R0sYfq8/PfPy8VkuGoC9ZJmW6j5okLLblaRbb4JNciGG1rrJmIdB4mT1pGaXMjdy/8XpNcyzsblb+drNDx3NnDdkfqPjRz+nHXz2thst5VbHjk/C0XIT0k0s9/366QCAO7hgOdeSCeu16WLQGPwrppoX7YNrbofZtT8SKffo5Rl1AZO5+ppc+r4OyZyXlHjLD8BVsiiGvsHoBqgQOzzMeg3bM5BxxhWf/+6+v449u3W+tvIxoybXcWzyG0r/kORYPYqhdWDue8kigyauX30uQhOzWjekY0tY4SXTmv+/YbgxRIwvUzBxyW3vrtznl12dcahOd6Umv5S2gCAkl44oIq1hk1oDV2d+VO+o+7GWePpTYkED46Hoq4MoqVQpQo3mm1XExJBosTGqJzqinWmeaSEWUpJ/nzOt8YMTtvm7DcOrckhV98GYb3eISz0EL+dEP/887i5LedGftIJcQdwmKGxX37/dcNfJC3rUdh5nOOrHp9QonUh0hUnwOOYIPZOstdvcBiCdYb5f5QUO6+m7HeVQrrowPdlkb8qk7qlZfy9NLf3BPqaD0r5FHj5ZVxg8sK0HDxjFB58c7LO2NYXTAoZaKLSoKXgcGq3wE4F9YTb3VuK7CaY+0tSFTodOxvK6tGQTgyJ70mjKztOXpR68u6MxBxlUXRspgO795aywLOyMIX8Fk0pOQbS3ZRJ2kOubJr/uq/LhwQ+S2mJooNTXo1yKaEUrpo4sPkS779HU8MPqMoXT3Jw3M2l0F29vNi/VahX4DnpT0iMDMRrsNJr1YLwAnopBXOHV27NphdbE2Q/B6FYGcW4GP7H0qFx9hphD0Je45YY9Gvqp9lZknjnmzMA5Yk+itzbvqZQt8jcif5Iyyn/fOvpX5mzilZRcX2h7Cr+Ue7tV+yno7sB28X6ocv7m/91x9//ePnQ8hYY3KRSr8/RNjfvv36dleVsnW21RSOUKZ+n8Mz6KcUGjjKnnbJxOEuNPEyhlzPxoTQc5Bw1S81166UDJYXrCtIQOYPzW6JZCFTYFgRgzSp1YJSvC2Gjiu33IgQVN5PPQZTujZ5VXyVkhia73Evk1wzQbU0YrRvE5Rfye88wFepgJ9//N6eRhSozTURm8sDVX8o4zeHQdTPmeSakx4g9+IUgedRMxr2nsdIItR/kmthyy7GAlb0zJrkapR8No3/aGwCtcljTSyurDYb0LYmJqPqplc87B/LbYZW2yARzjI9qtre0Gw3GKM07HlSjNi129xdlNjKkxaqEZ3PrYkCFpNckb91GDu9icrHNccBdd5HmdgyydWuDqxUeJIwxmswxsZW+tFTcpsKKescxCykSF87kyte0AQ5uy7ymSe1ltbSWMU0UdTUY/JolbnkxnsYosIQnhGGN/+GwJN5X96Vfx+xhS+htUPUmTnB2yOBldey/iuDvx5FKexJrXmz+M0nn8deIY3JkxJ6WmLpxVcJIb2qgNecTQkg5xKM2iCMtZoyRL39Sa5g5VnGnyrsVXEm9cPfMBHAFRxJ3SPBXg2bRxwQ4wXdb7wQCI6O1Sv1oU3/8e0fvz+dGbPksERTYXVPXOioga82KHNxyqFSABuH8lqDWsOsxhJbysq+qyp0m7nqIEol2lm0UNO4kVhFFCVfmRw0XA0RZ76rZMFnc6PN4kEQGxqprp6h02uWaodafZF8Se3FY+woJlepngf6kNdhfE1BhJC5QIoDjMV3zoGUmWtR3AyxXE1RuTCkCr7OTNKSl+w9aLlbwY5hRlM2Xa2CznhgdlKR7zosbO/cR2Zh3gQvkfEZjL1yydpDzdVKTSCodT49NopH2Pa60iOouizBtuTbQI3HqDXQfWqzlbVCnrQCfME515viMQ2LDunuQ8rjDCpZeNKTPz6zagZr2T5r1+BwUG3m/FqRCL32Cw+L0TMKPrGCU0lxjoDReEdj6WGN8rpBrQ+aLJRylgFbyTXe4QrGQCjdBbAz7VtDYoRevLw8oFb8ZGYQUslSKpydb64fuCLUGAmwyFcW7HSLV2BGT1Ianw1qFiEym/ZlEEWgmVotxDYwuuxBsrMz3nm1NlZLkEQRxEmtBbGbWWq1SV0NQnzezafByNBk03IEOP2uEZZutJcOKq8ngAK9SrS1dmK5yBuhqzIkj8lXU+9dUzvOYFpl90FTwEY0tbig7KdWqypEKMOB1B7PXizCcljRW0ApTZaWJtfmxxFDFWVLJ7VmPklj+OiaZFSwGPcuPO+BomyyOcnVwuo2Wf6/dJCdvSB2t+BsusbvR/m+GmhUI8bmqnLTnv0XnpYHk3vNTeM7SVMavO3OuqJsT9KcuGghO6/sJi5JyVf0my02B1HpcpJrUfKzR0TNohYlk6t9c3NhnEgKB0S1b24xDbrzci/Rgv7OWc/MsZJUwNA+n8lVBKW6zBxAvlpUDXujjBkBXMXdwYO/7vpOYJ3E9CrVAPROvhPX4wjFSA/Z2eNgvTW2swCSL/xsbjClwz0Rw1LJeOl+QP/hVqZiELPoCsIjwsPTdSdq5mgRJKQ7a/hvVRtn9b3DHLDM5jAPveOrfnuE4bfAcM9IaYIHe11WOzJgqk3ELzvEh+Rcg26Lm520/N58co7Ru7VRgiGraDE9nUG3t7WTFc3goyhTycRREW8MeKHHpDyqBandir6d+FjqvWcB90WMu5BLcYqWjOlhFL2YTS8mWFEc1LqzxPrr21dbYkvdtSQSLKbycX57b0gVZwM+CvJkz6rsy+zJhhGGqLs6qR9mhyWRnMWJSzVJ6UOq6wGbMfZsf7zMx96W1NvmUPhWdpHfO6n16qb8FxM4ysksxcqvFoJMO5KousTk/hnqfKOc3owr8t6Tf7yr//rt28+HFTuFFKMo4GtnJXPNZOVSzCFJJyCFy3VyJzUk9KYnmQ3jPhT9jsPkE5Cu5M98nqsOkWGO5kRjNSYn5ahaB5u8gnbpkaZ7e0yyH8bJ4GWm1pooRNtctEpM71nVesVbqbdeqvKqnh2drqDLzlK2o8QHlLSCAgarz2ff1+fHz4rZb6/uOfGItYQqlYxoLjPxym56MmGc6f7r948orOmDufPBS27RS/gRrR4kQjn3tndJm+QHuJmFWlazX2bRcKbxLCs4vW1zyGLqZhHJKr+yQ04pXuJcwpxExXWmVmv0W+yslL/DJ71byI/T+v2vmVS3iZ/YOhoa0nAVF3/PmdKTsbF6JboJTXLNWssCglUxUcJvkj/kz+WpshC7cTJo68xha+3nA7TkHnKS/DLCXXLh5n+hskJ+AKIngjrTlv75/evWw8CiZwVCfv4Ss3/cctbmNPtuiWxGF++2f/dFdtNupThFo2qsDz63OppCHtVgnlw6kshsnuQaTG/DtowktYyo51bMsmNB8UjHpJmtWrShnjG6T8GQzGOpX//sv/LdwIxm7z3YCyfMMY8Zff3twakoMSwAUUSIh9jnrG5O4h3ILlSTXNtRNwMqcpcPMamsBGGw7BEFDZhcN2Mz0qkUcK8cP+mfysbs2HorKJmxUVAiFpNaG6iPuTsKpj4zqV6X+aqpPAAaRinekhav1VpvI5JyxOFGal//+es/2x1wiakQwx0ptRIoUotxThygxJEnUD1E3sXe3x7sJYggPdrBnkEEtyl5INQotfP0wRLRWdaHJlrRvFoSvwasMX8s1fOQLTUnuQYGffGeVQwp69Lp5Zxp2vejNIwvrLTJJ1Lz/Jj3MFj2ymTUXh6sSbIwlXly7qzgvdlXZ8fLBih5RIpPa+CVwNkKVnvwt+W9n6G602e/tnRPNqMV9fTnEK372UizEZBUa1JEhWexTOG7INoATfInAz2BjC2zT22RORsprX0dl9CtbGBAlGFMKS0+0uvEUgDb4l6GZdLfSvHOWPLU/dAqXDGpDevS7MYk0xvdmXq28RT+dhpJrNs/S5f/H3sz5cwKkSyMMQep4RwsC7zzApd7o7Y/sZaxbXGCzTH5Uah5+Xj0rDJEkWHvjV2C6G/TacsFSbxJb7yS4EbJzVr2ImLInyXGVxGTHdpSBE7yZ4nuTcKUYpoXDg+mVqtnVNcD/4Iyc1C6vvlec+gkmLM/U5Zm34+HmY/ZcwHhafIGNZ9ajr4M2uvSMDU9Q862xJpsYxuiHPEcdOi6m0W8UWlJuTRLFaUr5pIlaYO3PWud0jNrew+h4VUYlwQD8uZpVzkF6oDeNfeIN2pTAEZkwae3rv5E8t66x4x4Fd/yM4QJY2Q1XIZKMao2X77/9l28repae5c4W9Z9lg4++NZaOZ1VcEhl6Yb6f/0///erGq29LC2zI9y9wYwkCuS65JLMMTPi4bL8fO1/1Ydxo1WT1mr+7xFIUT61UcmeIc7nSt7kR/elsxbpU2ZO60Pqa4+yY9DDy3Unc3tMeHaNxyd5SsubuyvcFDBNgHwT3LMi3KOGduNbexSReBRsCrPO1qpnu9JyVWrdh9PF8tKzr+aA1qIbuNt2TcDLdXn3Oqc2I4qN6F4e8Mps+bZZsmcQhh2yil2IVvPuth5YvMomqrPrrPI4C86Wf0fN2Oe2JLTHtpw7OMhiMW/74tKCy7xDB+6a3z4GtEM2kAm4mM4ufST1bMZb9i4fPhzSX8449T6b5NV3CcuNEBdChu4l2HfIm00LIb16oF+UDOBoehYUyrhSRhYs1bwjH7cfT+uPdzdqykEQwttMcxEGy9csvRXsjfB1c25TDh9QPlo1bIRu+2JCjO/o+Y3QrzvJKmwYSu8tWGrEX7J2tsY0Q9xxoLO517df14NIs2wKKDVhjdrgryU3pv7xfPj/811++zYtzzd06d+ls86bZN0/F+4W9Gs6SgpI5JMs8e0OU8arTvzDkGSqb6U4paY2qdDFt6loyB5LGAxqV34aP2wOsshxONT2V8/P8+W9GlBZ4cya1Jqd0M7Hd5YxWSezJN9cHKb60vPY0xxmmc+wbeglpvro2SrNHc7grVJXBbMWb4+w86Ve4ePI/vzPP379/l/5x52X0kOMOVvBLRGeRvKX6edKAIi1w1nteK17pmbrldkDFI5o7KXazmEbGH/eRpAAvvGUdvlvo1qNJbZeGDGInFB7Np87fYpX4lGnBv3Isl0rdt0OnKuOxWwNmoawztn4yGS/nb+zHrb3CjUq2W0WSvZRNNew9jQQbQkCs6izOcu7xSf90sJ9Tv9yKAXmNUrlA5tIYxIdc2ZIqFSzOLb+JZLP8t7Yip21Z56A6E192AKu6o9XFsuYKWJDSXBeWzmdLzK3MR0U0llytvtYTOUDZj/5KOO5Try5XRxeawhZWhusV7WP1GKY/5OfD2pbaBydMZLy+aCm6TOuzi50aW2wS/ukC4sPYyb4kZ+/Iy+ftpLsA3XZaWXSR43eBcN6umIssaDmrldHYHJQlotqUSTjGZ+erraVfuEgVzhdjd07xXJgn01hvzL0DHcX58YaF+smyo9E1BYNruRmjoSFNQlZPTMTe2KBIemdVcsL+QTM7JW8qbNTwm5mmEk+UXG9uiXW99I3fO4lKAk1Tremkuvp0Bi37NHDnvTzV/n9r/t9xVpcVdyF7ql+X2w2mTJjSRRqJaKBwanFM1pspVaqnzGO971o+wIarsKQfQUlBtqpUKkiJgpCsM9cStUnnWxL1iclUUDtQ2Cy6ZiU+iBe72fHYHraUJV8C6fEPkBk/B9ISQBSw8NbBs96p5YvpPdaTrNujN1lJ5PfxW6uiZda85kRtn5bM8T51CidzuslgewQJxOsfR2/7qCW3CJLLCctfT5q9ojE4KjYrgSrq9GvY0YHN2lXcxvYvIOtUuAXpUSUW/Vxm5aIVa0ddk16e9uLX0aqy3zaZk9GJQM9aD1l+H4Ekp0RJrVmq7cZczqLzS7zeZQ9+vr3b99uqDZK67WJas5ziFZshecyA+9keELw2nnZZkwvSsBBUBvrDlsZqMmYqBDuFlJrQK4ZLZzF8ZfpqBno056ahsL+wjNE4bIWecZqWdSJnrHK///G3ixZlhvZspxQBgWtKvBZUkNIqQGgfcGoIINC8mVEftTcS3GOu9t2t30pef9IUfNjhkahC9qhznkZ1N20gichS9mxO27dYr/T7slpT7D8vOfTte1siLd9+zTx3zI5beB7KiRu2o6RZ6ejr+u0p103s1FnTfdz8Fkn+uMmNOdhoEDC+nK6lj7Wnko6b+1JjjSvJOJ7fza8fvvWZ4flP66LQLP1+mlZR36brYFZTgo4STnMiam0eZpJuVuP4hNFzA6eXk7Xt/vtecisdfPYS3Px99o2OUMG8JnY1wXHWC5HEg2VhQ3kHNOEH/6mtz8g15XBu4fTNHjKJM0+C9vgvRaXWHRW5nQSo53YjQTuKgPD3Kqb0siWoj0mS9EV3cOWw4S8/HDfbSiTf/pjlVujoiNLw3BjyEEr0QU0L2UfZ9d6eIveFnC9h7G2ok4rqYKW33ssXaZT3PvZb/4tTPMJnd9b9YW0qUh8doB8C3d8tR7CGI9koxsyidf1BIBtTzcN9+LNQQKzV8cyHimPGgjv4b3+ZnzUXG0U3V3jydWz8AqymqmW7O9xBUKrgJ1igtHfU1olMVs1nQpOpI6QpCvN5Voqva3gHvcNb5G9GUsNQ52iVXatgWR+CItByFJOmjKZIFb1b/t57u7uJplkfX+b1xVmS2ZDNPL72KPoNe55tDZIIJkIcUXumk+9chJa/+xf+0aMdoyL34Os9Id2eZjZr1h2PXdtJBBLWAHmOWdc8bEv3obm6g3ykcXhshuS2BPKn9AQ08mGuL2RPizQN5/WPAFq8dYp70jT4u/LIFInCxal2VTOx5YySUJR6sO2Azjv0O6bRD3WxnnO1e7FRRIjqIGp9m47qhcW8xzIfk2mZIardzWjgXWC8NmPl1/l7ccx5+B1q5trbuFh+ryHDD8C63//1z9++uPP1cDvvXMcM7054R7PPMpO/f6v334DpT3GMivF380OpdUjTh/RWEg1VcUyT6+Yv3qI4H7FadLXJr/uhUbPXTP5bfA6XWusbV2y7kyhis2SsUeCaydPP9+1vD7CVk7Fu9ehLTMem+P+NpXUBJnb8O+ZUvceCMwC4KYvebZ0XwglEs1atpwegEya5eud9JDsSFhmvpqfXOgheSQaQksNN60rtd7uQfXl/W7tuYZPXv7Y/X4/Uh5m23yz3pf3rcitQ/URZ1xpEGRs0+7XKUXebhJfB6ba57aHr+BN/kpfe6s/Gl3qQcngvCUsvxZwLjuke6ORUAqLsYwz9UNmRBzzuZ431nmdzO9HQBSqg/LMjvvz9/88msi8zuRRZxOSh1wqDeM0w7ZuUnihvKW7vQLxowzXSPRUeUt3u+4Qo3eTmLb1VYr7rfF0jCfJ6X6IVJruFpw3/k/3U6F6mtSa/akCdlsJNbzlxb9aAZZT1MyTcLpArhz9POVQ8l13VFZ2YSZZqTxMlfAmXW725G5abRXflWR9XNn9vf3+vwBYi3fPdmnvcarfC/jLy3FhXzxeGhJF+naJ8nQTj6kz53VPLKuJKDEty4y3W+ffI83S/2xrS7tHQRwnLc2XHqdP3uNO5C049bGzzYD/6effr/QXmU6LsojNQu/XJeopJUHG5rmTfv/5z6vUe5VhOH/rqXbEIakPjW0VTe3Z+/ctXsrByfqqGDBP59N77RuTBjPlSVrV3r3JnYai43lW2yarye32PjpHHTQGWin2frO1o/PkZbqZbi7G2wWTSX8Pzfit/bowdHeKr+mmtOOzgPtjqz715DoNK+u+zVT8UUn23ma+V7KJLjKC9mInV79XWrSTAlJ4Xmrm1Fw2nXeXZlUx06qlrTsmRKxtDgZKi1nuZBmdkCud3osvVW5VZE9PyxcifCi9ZWdH9GwdCHXFh3MDuz1ZByz13Ecxw26QkRSiUm1fztz37bLg1BsgrOuHgcy9z5ZJv8IVXgtyyaij3C+DTZjVgklx2RNkLxVa7zGlfhykRJwWH4juQO4t5MDEHz3D//cv//z51//3WjOGofahZHdwP2RbZo/lQBQHlnmqrzV2XKm3buFHurwQ5PIK+Ki2IknQbX1z1L6U2DIrOH23kcRCVSYP6fs//fNf//Vf6NwQ79StWzC8PXVN1/9424YthB2fldvfPqMSP9fcKlPvt/FmvcCh+YzN73amzfAIbJA36ccE//ZRwKel5g2PbyvCe+YXOwHeez6UQnkTh8TRLxi52nP22f29sH18lv3/0LDDl5ZWvsVO2HxSs9j0cS7Sb2enyXs6z20nX+R2hx9/UPbfDIVuhvftNib6yEyFGYcbrd7jqz0rUJJbSnvd07OiT57EABuwryjxFtJj4vFLHKvI7F22n/OuSZ5F/D80iSt7zVuVmlO+4+unzzp7u5ENeTTRfsv7iqfK/uvd3x4xrTnKmG9+tK9w5Hg6zX5ZIT//+o9rqnxbc33buR6jl3MM7xGaBrary+y3HzZJ0CQvA7o2N5817Ny7+CNO8+VLmLGk9h7R+hSt7yGdXU971Yff9V3Ulu6bqB1mYcXH1feHqP8IKc2rhrXfSOsRju2v5rMf8XTV2yHyaCj3Fv18urk/XVv/+OOlWlsJvdRvD/Mzou77gRQuX9jr4u7idY1pPb64vD0Wy+uxd4Vjcyr9vc3y45EMj7x5LE7ceyxyf+S0h3w56nAX6rQJrl3vg3Z6GT4feddtu6rtRcf+jCb+Mb6cZq3jTfc8oswjK7Vox+N6FkP8kE9Ina+0fi15rHcGesiz4JK5hs1jJ3H4VViengyDiPdrooc4qzYR9uolt3tIfXy2Uvq4bhk6wujv3a6eD4Sb4VOn89V9+7Wf5+9DOsAN4xfZQHVG+4Qwy7hNmlnM4TVpb8EF01XTW2OSR0zpPh95y0bWXLJ7FlzT90ced6WS3jTdWsnP0iMZrERvg8I490HBk8GSu+vayzFXXblNnUkTj4KpmxJ3VCZOzgIvY+5QOhkhde+e8VdE2ZA9y65khJ79RB53vZeTto7e43312RMEMFqIbTxvy94HtHo2oC1pKXG+5bs9H0Cofi+W40rrIZPl5B/By69Yz5ed6Woaz4pk9e2RR3W039cv//pzYcn+GY5b0bE/80jwvTcU7322Xfd9e7w32Tl3btdaX6GN1L6/56366rP53dd2erzhtXhNEyb3gLk3TXis2jdNeOUuqqor8f6I94UPnEbT0Ok7y+09Deg5cOeRR2zwK9A3SDBzb92WgT1UXw/ZyfZ8oPW5amrp/kB4uIseDzy1VpDTufiho9++JPjMvyTsJulZWOfjkcofGTOuJoM9Eq4hXv9Z47qEMoUyxiCHeig8GKbMPlYk0/g0il6v9fwbaxpQyuMm8v0RmPmv1PZX7Ok+2fmLHei2lumxGetJZJ93ZRFSKmhtwPVCmj0/Usbe7RNxd/vkinXIOv0j1vj93cr1bn9f7VL1dY8sz5JTH49UvjVt5Y8gmu8Tkz0YG79dXkADut6/uzXF+PZAeNev3yfXK3Ej5lLSJJssJ8eHOqk/7XeU/CkNb4fde1Uve8NT/mncBzxr/vGAfyXR1kRsqGxKnb+hGHfXx4XH+yMl8M1jSLLmGOSviPs4ny5YObFemVjR4i7F/o5yw3R0eLbj1fdnLsv7BGO8DvFYzxOTPPEwkZ5PvLRNT2OUyj4luB+oDhl1aSNGp2RWudYOTlsAQlaN5B9opx6O//BhGb5/R3nfoC/vi5x2eptoTfX6A63ZY24S7naYPVL5WlHvW+yZaDQNP9gATadBStzsEXl/5Bowm8PGlpcG5Y90+5guj0aQb9tMo38zQ9+vZN1plvi43P58Lv7Fc0l2TyuQs0BB6f7jquBhBuMuNRPFrjnyI32IW7rlrbnH4xFROAve+ridWDrXF1GfKoVvzb1d6uW7PdvH39GXy+mn/qs93OAWJzb/bIv2/nfqa739j7fM9/M3an4vCfdMO74O9rdHYg4lhfeSUc9Hrq2DHjEzaMRHCpFwFnx9/2vrrCFxV7J1ir/G+SMeufjVGjnXCiy3d82cTsbEI07lnYjTtQ8+ouDbuc+dBBALZP9ec28DbEceuQ0o8oO38oa3uS2yLGv2fLnYIm52EJIVdppRv+HbC040G5/EO8+ECqfS1yMvOnF1Jve4+n6z6k975Pfhes6K83GlShaYEaznpkPSpPKsHveOTaBqUNF2X4PMxxX7+4vp54u9tExOsXa968D4bKV6U87VHomOmLT2SOHf4k9/jWf5uY9HfnjOaPLpinT8v5/XYOn05/rHHz/hHV8zE2LtUpmw/xROvpymDYsJh6fwK7apNaOB4phw/Pxl28dlPpNsPoTT7Z3TqSy+6WvkT+F6Wmj0Rw3ND2H5FO6mAEzXFCast9fYMvboVLjcfllFzNT2TLh+Cud5SuTRd663GTR0c69SsR/Ctxk0Tbr7lFeVCBT+msH++7/+/cf6vjG+pj3aInGB/YWvmUThHk6bZB+ZcPoUHsufEC+2VOttJvW4VEt8EQEKf2+4x6+/NNTqNgSP++uPX9fPV/HNYLNWOublU9jOPNOXylZgrU/hl6+m1DpdJmvb1N7nL5ded5VORuTkg30Iayh9zU427+kd9yGcVh++ZvrL+VM49DDH80bgQ1huU75jccKFb+PsfPUyOhW+jfPI3hnXki0WXb2NRpVYBx0NfxvnVqXtsMmuOU3uHsIvGj+9zkt9RX2h8Nc4//av3/77t+utS1yGBvO+UE9HPBNf//ntZzOJYEziiabN7GVuU1NO65y9yUI9rouPNw8jOzcWG21/G21JpZTWN3vvihvsFb/lzMiMjSiGGG4jvoo7jajYXAb/KbyTLmOcwYRv2+CUmpBON1i4bQPnxNkZTF/jpp9ONVeJImREwnNifsKZ9MlgecxAVkr4Pm2+gvde4nY+uuaIsjyXSZ/bzJ0ykkqOkONf+xxAGSlVpoaPh+3zM+saMdI9GW/zONTMuFHZ1MTbPGpc08hanoYVCn/flLzGxD8vZNSZARvXy7eNz3xNZ5v/br+va0OIvb6Lib3QbUaTQWgNk+37eNtqOXVn9jf95ZsWPJ3wTtYnE77NZTttv/Omwre5LKHbwTKY5o63uVwhnMOTfWByn+pBe9E4A3uNdNOCzY2i4CdH4duedH6ffsT0l+9H0zmZnhE0H8K3GczFJHdn+i/dZzDXsZyyDZyozWCs1U5oCPn1fBs+P11I4u+KPsf8ph6eS9tN49gSXwAAD1QPD/z8O0Ycy97fN3lvFls+7cP+/ucv35EhP32FDL1y1nddXa8QUnzoRYDXKtBY1zMXMLxJ15el/ipwXfu0QXrF9l3C+a1u7DOENIU0ND0O2rcxyuHNVXDVPTMz7Jn6//5AdMy3MESdq77fP/YkkD4/9gXHe51O3C9kv6TlGpqPGGs7BFZS9eyZ8PHMi1t3iuqvnu74TPzBM0NCmSnk+/I4KU3URViWTi3dkUe0/OBqRFNJWgt5pAi/HphhraJXq3N85HU9cK2+lXuvVzmGS1r960LxKtPidqtTyPSdovZP6Ze/JtoRGFcg0tXfJvt0i1+bvkmtN+k0fVs5hfvAFB/5wKQkerpJf+3Ot61TzJq4dufP+/f2y9XS2M3RG/R+wsfybX/uOYKZ+YVIq7+PkOloGUI23Im4AKX0WkIp2IE46/2BCiv1H1cMlHH63sm9/J/wAFxH2l/6uvZ71TU3pV331dXu9ZScApCfc7F91bUePWxxYOXE0N0GFiZc63iYgvXtsUdy+PcD53Net547ble/K/+8aVgJD8eZfcofv/68Nzjam9kcQ66KMvhQ/Xzo8pfsNMZVbRIeekQQkb8Udp05+coe8p8Pvbxn9tCJwWYPhR+93imcNR+RLp8PxR+93uzB1/U9rZ8P5R895PvQobvcpynoa/Q+pnb5nSQud1umcq4uyDI1FExhu07e7FpwX28HbfRO45M13f2SwdhS2F8ptZ/ohn3bDJJCvm2G1/JeplEfFvfHU8X98Klw0kLCeF05Pp/yHq+b3x6pGnrb5Xbcnapi7mOkX0UkVznvdzu6TiWv/PHMaxmc1In0raNwsO0ZiA37eCgkF3b5prz3h05k7cdDr2ZHOmzpfIc3vz8UHhfijy2O6qT7Yk8N+Rzv81D84Xif1HUf+00z2lP1LS7icnL1neZ4ZatdD0TB0LX8GoBlp8B3NGl8+wOxXGv6v/785VvZ/fnLVb7GJ+07JH9/t1gTezf7/BV8v50LJxqR7p+5Z55r3a4DzgOZPbBqOzMXyQO50gd6OQ1gC3lA6Ctt8d2OK/YNQo8qm7xsh+1NaZy6K+nFn2gDJDNdJNyA4UQRvP2FVyiM2vZyod5XluiPV5apk51rIX9GSoRFfKmZuJOXeSOH0zkq4la5bJMcalt9ksGSmvBTrjPNt+oaWe7qHA7W6+Q0u7vkSubPGJ49YCwT9y63U/04ga8JN3380ehhJZFnd/Ty/lh913svo6Z048/vK7sY3x6BeMT7KSOS+ux7s+cu2+arm8PX33udaN71MBMZbE2vu7P1n6vERIwxp7IXeUAS46Isp8H2d3DTx5spgtTnF+luzjZmJs+V8BcjobWNXL9rfXw+F//iuRrd9uVuEJzn8qdSv87qEk69CPbH5K/+mBfbfeV+Ipod8eL7d5jTYlbOjEStFe8ZX+7Se5nrZhKcjkXC9pHUPparr8IX8IC87Kg3dXBSzsr6DvR7VyJFww+VyD6XivJQPW+7qdTrS76+/u0gWG3s/V1n9G2so3uL9vuElml2uj0XbhMbXXyzWU5C/sv5Mafo/O4jEfP7Q9cf+7LnP4wDg8Vhtu99vZ+O0X+x3v0pYbdnuk1XdLmyjVW85uUf7uK3B/wjovrzUn2vZFuq3xbdyY7625dB+Qwyex30y6uZYu02VfFZ0BOWBTQfi80sjXbfu9FnNgavCFWJ02eiK+KzYOcjfPaP8fvPv11KaQdvqOvv2yk692zBcMJh/1e/7jmisex2r4yw88j//L++7suel4JfBvbr+thO5Byv0h5P+Zw9iab3ba60HrFfAaUhcfp1fhkmzWdtxOunzQR9rOuPmvBmtet4Mz6/pOMjDOM/v/18K//bS7a3qa/otddD6ZG/+feHrQbRLiOO6fbH+Bw1/6iK+10y6VWvzI5OP0e9/YUaIZkBo5CSLcWYqtw+uz4r25z3eSalrJg0q078/ac2u5bUGdYnDgwzKOfc71XO/p//+VWv+ZFs9vu/fr1OQts2ZhT1py5D8Udl0NMS7r9//a2NK8nRtdDM+Piu8Z6vp7w7ua5XmEBoM3xHT31KhWuJ+bCjftc4+JSKV2BAd8Pt74vJT6n0kpq1+903/a18SdliH48Wzp9SckltW52PFk6fUnq5Xu0LTXAzqQIX2/5kB9DfqpdPwq8Z5mJvr+7yQq05svtOxvuUusZ+LHVhNjb2eo29mV1qqBWY1DX2ofRXV+dPKRz7lY0o6HtdY69bV9rCxkuvsQ+7i/hvn+yn1DX2cgIvtLFVqNfYV5Fu5EVH9Rp7O6Zar46tr3KNfQh2ZszMRrV4/Iu1rsa+sVxjn776udW3FiBPqfi3/d+/DlM2v/3+HQD/OmbN2O2i9Kevqagn4Ll1NmHlmooeRx0zsEEuApu4FN+VLd1yTYVv0qXTLVWuqYilj3Ojz6SuqbAd3GZS9l7VXcdH6icVgC23CtugupAeLVg/pa6p8MM7WZn+1rUNtLev+hpMKsFf7KeqiGdS19ibNrC35791jX1WV2acbOnWa+x7HVnKRymUh1T5Xki/XPZkMePJDiD6m9ccnAu/+PDSvEuZ3OW2zsXAPBP1cTKzX5smthaiI3PgHagiKdmF7+CiT6lrDmwqtT/qg35KXXNwdtbRhkwqgyIty7VG5uCUqL9ytEKw8SJKxjuFt1ebkklWkHfX+je4PDUcyS7xDsZ+hx4WmyHD1ktK7FCImb2Xx2PAjRy+QyI+pa6xD+20NuRS19gff0FulUrB2JvlvLJnY+9B93QzyXdi68tf699QWIdj6tZ7GPu1Y52OrUJ/jb1pK788MyBO3v7rt2zDx0eA9IdUcJdD6DSkcd+G/qfUNap2Tmt71Bz+lAKtYkb+eJTm+5S6RlVSeZm/n1LXqO4+2gidzVC4RnUbUdsUUSnQKu2UF99shgKMqumw5hwdiWtUbQFmlcL+YnQwXuJzLmw/RljRYZ7Gx2yG4jX2emqi7cB2RwSjUotZ85u9fYTT1E4ZY3S2a+M19s4vV7ew8YpgVErK81Fn91PqGvs0ZEilKydeY99j0ypUr0YwbHZs6RHj9CGVrrGvW12LiY19usY+SS6hsdPUp2vsJW/b2Yut6HSN/QrZxTDY3k6gTYy4kwibx3SN/dJDklR/JYE14cLKjp0w6Rr7LGP5MulvXWPvZzbrmmqmBJbMaQkXlb19BoO+GJM/2hF+Sl1jb7vMOC2zUyGDJt9tjU1HIl9jbwfoNHOdzXa+xr6EYMeasvHK19ifaG2zP9h+zDD2YotVN7FbT6WM11+MXoYy6/ZchLzW/Unoq4OOKuicaJMdNvtGgXW/5nK+s72NIJtMZS6GlR5AtuhoNvtspwHI2g523S36F6+x152iD1QzAcjWsbZvjo0XgOypNxcX1RMAssmZKnn0N/mUusbekGuXUNmaAJDNZWyTYzMEIFvV7ea51DX2Iik5l9h7Acj2GYvkys5HA9nXVa8bbT9qbn1KXWMftYUpDN88gKztDudCoX8RLJgSXZ+OSiHIqq2IzlYOgGzJZrg/giw+pcB6DMsm27O3Lxc9jZqK2p9kUqDvfY+nmgKTusa+7W1/UdhaLXDWzqmpsEsEb+T68lH1ubR2dnYAuY45T+QVfS8gV9vYZljTb4QLHNtDzwzxTynQOfaNNdFzG8h179pLonq1gp2TtMRHUNSn1DX2frbZHlW1PqWAXNMqkoT+xWvsS1Ezfjt7eyDXbRCuNmJMKsN+PJWjEpshJNfYsykKNkNArsG07+AWXwUb80QircHmEYhV544pMToJQKxFuivCNHkAYm0xn1x7ojGDEetrrZYmObCRCMCidhqX2pXMYwAWbccHECbZ28HBtVgvRR7lmz+lrlGtrpXp2Y1NABY1K960byajGoBFe5pt6yQzFJBFhya/IxsJYFEzmIL2yN4eWHTkfip2sHkEFu2S5YRnMalr7I33nFl89C/CXYwOdbOw8QIWzd+FfIheDcCi0eiktUClkJpi6IPd6wRgUfu+PWcju9aOHrw3GaU0NvYBVrQZF72xszYAsZ746ErvJ0JAavKndiUbeyBWm8SyU6VvDxZM32bhK1tfQKxDcjcup78F2uT0q4uLrRwg1l1dDZHOIxCr2ikU02B/MeL9l32k34QCAhKrE8O0xsbrjVh1jdGYzgFiDX4EW9Tk7AhArF+3FVSvBiDWstUbt1EpOEWrWe6bfyNYj2Uss37peL2dom4s/vZguavoqJW9FxCrHRw5J3YPEIBYJabsH6VMPqWusd9bTp1Mpn2BWJNTzY7dywUg1hRntf1I/yLcFmQ91XrYmgBiPXERo7A7hQDEmuw/7A8S+ysAsQYb0iKevhfonJXNQqZSQKzabRU6diMSkFhLrmb/shlCYpW8dh9MfwGxtmjEuhm7ByDW0MzMSZFKXWMfzeAMhVF5AGINp8HbpBog422BqctNT3cg1lSjmD3BVg4Qaz7ZJonqaCDWkrI7sbJMCs5a7T6MxLQcEOsJpB4uMo0JxBrTqR6ibE0AsZqVE4Myl3AAYu0nmCcxSzQAsZ7QttIWfXvQ93OO+ahA/CkFxFqHqR1qDQGxllqNM5kjNACxRhvR2SLbj0CsXYrxHHMlBiDW09/KZputHGBRp2WtUdnuQBaNWkKmOgdYdKuzA8az2QYW7d3PsyeZFDhVR142GvS3QJOXVINSu7DgKWpoEqn+AhY1vO9Z6NmBLNq12Yyzt0cWzWb1DOYcD+XNk6dLEls5wKIrip76RUwKNPkaUTq1o4FFT/5q9+w+J6AXNebTp5GtwoLaZMryga1CYFEv4fSHZlZHxbF3umNgsw0sapbvONXWmBRqctPR+tEs6CGVbt7KueqeZdD3g4AOTXae0pkCJlVv0+7pOkNvqo6xMtXVwKQjujhks9UITDpl7NoiWWcRmLQdG7IwGojApNpLbI6FkETwoprV3Zewb4zoRR2+HnuNSV3rvy2zLAqz6SKQq/FHaZOFfUQgVz3x1V3pe8HY23HoC7M1I5DrqEZcmTFDBHI1m7tnuiYikKuG2UNlvoqI5Cqh++rJHo9AriXG7hI7aSKQa7O/aGY+Gy8g122MMibjyAjkWuwwmpVZHxHINbpVdklkrUYg121kPiezGCKQa1b7wMEsrAjkelo2G1SzvwjkquJqG57NNpBrOW5UZbZmBHKNrq2mk61VIFe37N13J9okArlmv7bxLX0v0Pu1t+CYFRmBXFUle5lsfQG5lq8Gz+wuJgK5Jh9PJ1g6qtfYO/vIldgpH9HXOnQtiUznALmqGUW7M46MQK5z7NMPnq0JIFfvJbfErKKIvtbpm4EFm20g1yRu98Z/C/weZhi40pnOAXJdK+oozJMXgVyNH3aa7JYlArka5OdIwyIjkGvw+TQBYqsQyNUUQJ7UhxLR1ypxuMXsigjkumtcp2E6k7ru3s+dVIrM0xKBXGWnLJvFK0QgV/HFzU41OZCrM/NqJBb6GYFcc7NJLIlwZEwYPBlXdcyfHN/IdY4TIMakrrGP29ddqCYHcpVihDuUvT2Qq21+b4com20g1yYiJVD9heSax8w6mI4Gcs1uepOkI3GN/SzaSmGeqQjkupKZMJ15ISKQq5rdGgrzVkYg175OB2ZhKwfIVcdZEp2NKvpaZc3cmW86ArnOJH7ETKUgcDXUbacqm20g1zxTMTRioyp4azCNux3TvkCus69pGoCtQsEbejs93GKngrzdluXQqP2FQcOn1Uijs6141o4qNKIkKq77acMX6W/BWWvzszzVTMC3yY8T/81mCPjWhemFRhlF4NuYRjUbgP7WNfbqWtuTBaVH9LW6mVahsw18awayWSYs+DsC32rqJ26HndvAtz5vkwlsFRZc0SW1R7WdTym4/z11aD3zokYk177UOWrLAbnmde7x2V1MBHJdtZgRQu1VINdwUuhqJuwXjVw/2W9XW46Tvh8QrBp2Gvyx1WgE++JIg07ZLBokoje1q3F1YOMGBJtP22MJbF9WvAdWO4KpTgRy1R5scX60/X1IyW1E0nKp10L/MtiJc50IZDpu4Nmz0S2T+RITsqmOWiaLyEnApj2bSUBjuZJDW6VIoz6oBGy6c3JpsvunBGwa+8kL5G+fYb0dfwOL5UrApsOOnFmYtzcBmw43Wx2ZnNAJ2NSt01qIRYYmYFN/yvZmZb8FbFp6MQQf7LeATcW7mnYg2iUBmyYzaGKcZOUkj569KaKLjQSwqevLiF/ZbAObhpV1Fua1T8Cmdqaa1ck8tAnY1JRZ657dHiRgUzOzalJmoydgUy+j2L5kawLY1Bu9hsZO6IRsqnLUHrFVErCpVtdNis02elVtC7vI0noSsGnNzQ4TplUSelUNxiO1CRKwaa7Z29+kvwX3AnUttxKVAs9eK6NNdtObgE1zNeT37CYiAZu6OFYOhf1FYNOwRgqd7m1gU6m6V2bJPwnjgJ0GU2BMywGbJreHLKrlIsbkaTFDhP4WrHvJJ+WSjgR4l0xDF2H2awI2ba2mleh+BDat0U3jdPZewKam4EbbneloYNPTh/K0imJS19hnb7bWFrbugU2jO/XsPVur4FUtq+eknWkTYNNoaL3cJDZUAjaNzeUt/LdA5/i+cmPWRwI2Pb1HQ2P+mQRsuvLIdnCzb0Q21VyNpNh4AZtOmWpAxjRTftM5zuwiNhLApnuXbNY3e3tg072MszOLCknApkF3Kb0T2y7luyUj3pv5wXJvEjBqLa1ux6g4AaP64fpMla1ZYFT7imbTwMYNvatFXMvM05GAUfPpdlNZBF8CRk0jZI2M3xIwqivNjElqYQGjlnqYhd0QJqBPs4vD6SrLpECjZxv4znJJEtCnjfuWxaI6E9Cn2cVxd3oCIn0OH+coVAqsyKMu+mR/EejTTH0Nm/msE9Dn8c+c9hBMCmKUtM/lWbRDQu+q+NozizRJGOlbbehjZKsQ6HMbmfXEvDgJ6NPPPTUzD18C+mwyDdhZZkcC+uzbzOTKUmkT0Kcu+ymue8C7aqbTiIuue2BU3RpWZbF5CRjVJtF0tWO6xxgVKiLbOcliuhIwqlluTiq7RUjAqFFq70ptJ2DUEoud4OzOO4F31Qg5+MIIO0GOqmbTX4Fat+BdtXXrEvWWJGBT018xOcbhCdnU9GWJlX0jsOmpKJIji1NOyKbTrOvW2NiDV7UFM5QTPY/Aq+qMfwwb6NuDZ88me3jKWECuW05FChbhnoFcRZKfNHMoA7n6PUPPjHgykGvMc/XGqC67K7vA7OTkBsslyUCuBuf6LAj+KYW3jaZ6N8sIyA4j3NMqkXnHM5Brm0lmZVlbGcj1NCQtnUWSZyBXM7AMNxkXZY9xMkOTsjvv/Jab6tPJdGNSkNmxTTN55qnKQK5pSzJqoFJwaxD6dsJuZzPGA49a/GS5zxnI1VRXC5ut6OzxXkxkNpavnIFc1Sg/Ug9tBnKtZiWXyk6+DOS6ptt9NTYSGA+c5unxyOYRyTVqN1uF/kWIxV57d8ci0jKQ6zRV6Ok9YgZyNau5HdRnUgJ/0azHxDJYM2awnlrGncVVZIwHLjZFhVlWGTNYXe9b2P1hBnI1+6vtyrKaM8YD+5NmwUg/A7k2ZyqA3i3keOmcccrDrMXGC8hVpK2aGRdlINdzYWNGLf0tuC2rrR/Dg0lBbJ6XERI73TOQa3JZR2ZZuhnI1U6umBo7rTKQaw7dkI3lDWSMBy7LoJjdQWYg19jiiJXVz8hArs1LX4nlbWYg15qqzSO7jc0Jz9olvbBiMxnI9VxIdsdsuQzk6qVEV1gOewZyjbpiDYwCMpBry7udxidEKuOtwbkFY1k1GchVSzerlvlBMpCrGQldU2N7KOMdfPItTzaPQK61iKEri7XJQK5u9TJpTGoGr+qpfhlp/EIGYrWROK1B6F+EW+LmW0ybvj3EpNo08myfLOjZa2YpsNvFjF7V4s20Y/dgGb2qNsSjsZiWDMRqtoUd4/QbgVi71GI2ATsVwKvaZLlJ4w8zcK0ZcjV5dn+SgWtXqzXRKMUMXBsPSW8W75GBa3uzvSIssipjBqvX5Ru7ecjAtbNlM9LoqALXDjeMAuipAFzrR0tus+oaGUsx2dnoaJZIBq5NezsXOxsJ4NrZR9/KbokzcG06XLiY9zID164TMlHZzVsGrp19minM7vEzcu1q3QmLTszAtV8NuSOL3MvAtScGpSyWuZ2Ba0/UXqHxCxmjhvupZMMimDL4XrPfJ0GdzTZGDZ8cSs9uUDNwbTFNkoTdbGXgWol2WgrdtZjB6mIrifnQM/hcTdWbxdfYNwLXNh90V8dOUeDakqrZq6x+QAauNVOumSZnqxC4NtQTmkBtcuBav7QIrbiSgWvTaZBDI9wzcO1OrRqxsvWFXBt8ipvdf2Xg2nM8TnpDL8C1wZgvDGYzibv83du2v40MlYKxnxKe7cc/peCW2Kst6EzGS4BrxUZrJXaXLMC11awqW2BkTQjUXKrnekWZjhbMc42xlMToV4BrS5AwMovUFuBa18WE2E4T4Npz63vK5TIpsO+H7bXN/FeC0cI2DDUyv5oA18Zdh2ssDkWAa7ux3FQWISfAtS2Y6btY3KEA19Zeay6M0wSjhW2f8ehEAa6daZzkAioFXkE1eyKyWzIBru3GMDOxwoECXBtcK4vWtBPg2lq058KqkQhwrZ1JLnlmRwtwrXOlPbvNfErB2Jc+0qZrFaOFh65Ab+YF81xbM8OQ3cIKRgtL6gbvbD8C145oNpuyWHoBrp3Ht6uFfSNwbZbZT8E6JgVZ86M611kEpoBHtp2bbb5rgWsNE4f9Rfr2YN+f2+nK8lwlXjqntXCi7+hfhMi9MUpqzMYU4NphsD08XV8YLTxGyoN5fgS4VoLhhLJsWAGu3aYOAq0zIsC1zkYhjcX2I3CtGA05mpMtwLU9++I7PWGAa23/n2rZTMsh16oY1zLPugDXzt3UN8bIAlw7VvO9s1t+Aa6dY+4cWNaWANeeCsW5s1v+02b+FTd93CuTvn2+1v3YMRsTsffK132ON6N8LJZDJcC1q4wSJ7MnBD2yYZ/al/QvYvTTNsDgv4U1JUo57VmZFLCV60sTq20kGe37WkdgVXgEuDbGatYci1kX9MR6zdLo3gauXU0NQ1h2p7xVZho1TOZtFuDaL30gLGNIkGu1+0i9gYL+Wtk+dHarKMC10k1NJGa5i3Htq9Z1bzsslk8qxrUvqZbjyRAjUsi11bUm7M5KkGtjMDONxQUIcK0Z23PmxVYOlhiWvopnJC0YLWxzaPTOxh64Np8Y/8oiSgS41nbdTkp3GmbDnuIUlcXpCHBtdrLTZFk+Alzbl5mYg91aC3DtCsY9ynwBUi6do6dzC70bsiMMoiy2ISQdL+DadJpbDha7JcC1ZgHUtgZbE8C1ZgrNUplPWoBrpRtRjMA0JnBtq9lUK7u1FuDafLr4bGoNlWvdG3ekqPTtgWvnUTqZxRkKcO2OI5aUmJ0DXBtnCm4Ke6+K2SHL7DR2UyMYS7x7l8LuowW4troQm1ILBrjWGM2s3812B3DtNHU5hVVlEODavE4AJouXFuTaUz0ms5qEClybfPKqzN+h4K91W0tvTJMrcG07RRKE2V8KXLuybbXAoqkV/bVFzChnNpMC17rch4Eh2Y8K/lrXpG9lEUEKXLuXHQqFrUJ949oYpbEYPAWuXTGeMqFsJDALVqfTxNaqYhasb2osymYIuLZVNexjMUgKXKv59IJh8cgKXDv7bH4x/aXAtVXldLKiv4W1tO3oCIxFFbhWYp+2i8jeVsyCXT63zOrsKnBtkLB3ZHpCwxUjsmoW71lEkCLXNlMTheloBa6dwftFK94pcO2S7udmsT4KXBvarqdELJOC2KjDANTrpgF1TlVNzMZU4FoXR7a1w1Y0cK1ZWfNEcDIp8FvVXm3Xst8Cru11lr2ZVauYBWu7MQjdj+Cvjc7vQJtKKHDtOskU1I5W4NriZh2N+fAVuLYbgrnCKjwo+GvDktUCy87VN65Nzaab7Q7gWp1i64vdIClwrfacHW2loMC13nZHbawOlwLXahunUB0biTd/bT1ufDZDwLW99WoHDPtG4NoZysj0lkyBa00tmaHAvJQKXNuinsr89LfAb2WWkE9CpeAuzY4rVVYBSYFru89JN/M+KHDtjjYWEok9oeivNeCLhVlpCp7Y0AxZM7txUyDWXrdpOZaRoG+1hEtPhUuBNjH0XZvFWSkQa/Q92CTR90Ji3TFMFv2h6In10lpgmTgKxGpWqTvNsJkU5BZrMeuEVRdQINbQjUUHtScwv9XU+lwsflWBWNMaPU9Wf06BWNc0Tmysl4OiJ7aXbZYO/UbwgtuQnqJkTApyRpyeex/6jXBLlv00MGfrHoh1T7MIIrXSsJZw1FMbiukcbIqTg8TCfDoKxFrNelmOeWIViHXYMpqZkaFifutpFjjo3kZibUaPnVodQKwmZCcf3Y9IrH3bmmDeB8UI41n7pnm3isRqh+UcjDsUPLEj2Wk7WEUABU+s8y0VGk2qQKx5uC2LrtU3YpU5aV1PBWKVtKr3rMqmArG2dbp2MZ+hYv2mqtsPRk0KxDprstXKvG4KnliZbhbOQ0CsIQyz+di9rwKxrhi80mx5xfpNLbrTBodJgQWT2kib2phArDW0cy/AZgiI1RCzhUV3B9Ztkm7HB/PEKhCr96fBFvNJKxBrrctWBLVzgFhj8mbDsErbBT2xdezRWExNAWK1c8j7zPLBChBrHWIMxtiqALG6mnMdrEJHAWJd1caL3nUUrDjs/IimaZkU3pLN3DyrFFWAWL1ZObuw2vEFiDVOM3xpHY+CdZt8y26xaNKCxNpKOXqHSaE30EcbMPZeQKxr9ZMRw0YCiHXmc83PPHgFiLXsY4qyCPYCxDoNTspkMeAFiFX2DjkzK60AsYa1a6QxzQUjjENpebGM44LE2ksyhmRjj3Wbdmlls3iAEvCmJpn9pURPFCDWsPqaid1GFYwwHqczNMvAKUispwK5pysaiHXagVw6824VIFabwlg3u6stGGF8xquzs7YAsdoydUK5tgCxSg22a5kFU5BYT50rz/ixALFKdWEGdodcMDf2zGJh9/cFiNUUgLfBZ2//5omtp9YjW18Rxz66slhufwFijaNMoT7pAsTa7EjVwPixALGesv110d2BFYfzss9ktxgFiNWdFJzBokkLemLX0BHZuV0wN3YbLWzmoyjoiT3uB8cs0QLEmqVlowD6W9AbSmSFyiJ5CxDrLEa2g7FCwbpNJeyt7O6xALGufW6GmDewYI8ctZPbsyjEAsQazDpunuWKFCDWvxgviDDux5NSmaes5Pd1n6Wz9QVc68tpp0x1IXCtreZTL4NKwQ3llN0mixsqwLWpLjsT+Ehgw9HZDlQwKbyZd717uiaAa5eR6OqezaNgNk8KmeaeFsyJNQ0njuV3FODa0X3elTFfwZzYKTkri6IuwLVljGW7g34jZJSUE6bPIusLcK2d4Qb8dIaAa82SNJXJMqnKW4Txco3Wxy/Atfn0k6b1twpwrVuhmF3IRgK49ty2nThEJgWRN220GhllFuDav3p7WPf+3FDQ8xG4tuhIUVk+X8HMWTl+SmqTK94peOmNxWIUbPbqa8iOMUwBrk2au6e9Vgs2ezXe7sKixApwramStIVlEBasSyzalmc9TApwbRlLbOmzUcUeOW3uQvPKyxvX9mUkQP8i1KHwpWfqTyvGtc+uiGEUW4N012KEcTb6FlZ/qwDXygklddSOBq7thj1TWR3ngly7z4FDqQm41pTvSD4ymwm41vm5cqb6CyOMd9BGq1UU4NoRW7RDhr493KX5kmedbOyBa9fIcY/J1gRwbXDTncP2LlXRE1ttdxR2P1GxHrHOrZ5FbVbg2j3NBmjsPrq6t7O27Mr8QxUjjEdMqTGrtr7VfEo5DBYtU4Fr52n8qozdq8P7nGEHKYtCrMC13aBPIqvyXDHCOBRni5qswgpcu0ZKI7OIoApcW/JufjILpgLXqjTjJtY9oqIndiUXN7OQK3CtnXprNromMHP2hFgElhFX0RNb3Z6VRblWzJzVGOpgd0MVuLbnXttk3poKXOvcMnVGRwIjjDWZwmQ38zVgpGWzI5Ld1VbgWh9CX4FVv6rAteJHOPVjmBTcIZvCn4WdjxW41od57H369lh3KJaemGexAtfGU9k0su5wFXu/VuOhzBi5AtfOOUNXuoeAa4dBjCxWS7VihPFXUTM69sC1bu02BqtbUIFrk9H2adPApCBjfK2klZ0wFbg2jlOTnGU1VOBaraeXHqubWYFr9/BuRDrbwLVRWy6NVYKtEc/aYEufeWIrRhjvmoOwfusV6xGbMTcSO2EqdtKJs8XKmK8C1x6eGI1FM1TgWkkhVM+iZSpwbTYt3mmv+5reIi27X+zepGLv1xnm6VHPpCCyPpnFNxgZVuBaO9BspVLNlPE+R6Uu1rmjYs2nvstpSMqkwM7pkiutd10xJ9YUxVCWLVaBWE8A+6Z91isQa5NwTgX69jCq9fhOme1bgVjNDi47sSixil1dZx570HUPxNqaHbWeeWsqdnU1w2o1ViGkArHKPPW1GQ9VINYQYmxrMf0FxOqWSKH3ABWIVU/+TaTnEBDr6G2ezAAmBZq8lzVolFgFYk1q9hDtYVKxR04L3ric/ZZiDKUcK43NEBBrWLolsmiZCsR6vHfGTWx9AbGWrlkDy5GqQKxm628d9HwEYjWrwbfN8h8rEqupX1+Zd76iJ9YsgDIY6VQgVtmhbXpnVYFY1U3xjRFrBWIts0dRRmAVc2I1qM8sxq0CsaYk+5SQZlJYb0jqDCwbsQKxBjuR86b2KhCrs0PBjlsqBflpqbfNVzQQqzcNHWmcQgVPbLMhPkmXTApiKM1CW5OOKnbSqXZsd+YDq0Csp1N26CyLrQKxLi8qNA65Yq2naOcN7YNSMXY4LB891avoiRW3R2YZ0BU9sc65oVQXArGezsJBNxtVINZpetXTaNKKxCpf8b73sfcOPbFa5vDE22xSQKyn2kkmt3cmhXVXRHwkFrJJYRyfQVMgdqFJgSc2nZbgJJPdpMCCsa3RJ4mZN6m3OL4TEXDXOSaFeZleKss7MSmwHpu9VyO31iaFVf21nYtNIgXEWm035k7yH03qyldoI7mxiT1xWlXifU4x7cRGFYh1tblPMD+TAq9IOZHW5Ab8tImHeyazyQfxxJ6G5tc37hNfTM6O03r7+q1ox1UjFvJpEn1puWznuyNet9POGNZX8FtIXtNpz3v9xTBcF2J1nEaycG6vKax/8ml5CrpwdedJ7Mppznnt7WjbdtEZQmKd9swkWQ2n4SFYj74PRzyepzXftaJDEZdJlNhpNQc7rZWxSbWR0xQNGGa25omdc9p2Xatw1HUK9xEprPVkeOICyZM+rZCuv1iPliaEf9J3rpEotiiWMC0HxKo9mplIGPk0QrneK0iy0WezHTHyZkkMpDvDaS4Bsx3T8sTDctogwLk9fWM5saes/7Uf17l45uOFtZ6MtAaJVj4F1MHaVrWzj30jEKvtlDhZjsEpjHzNUMxm6FBdiNmusTaNpD7AKTJ7fWNvBtb8L8r/ya4FFi3Bxj7R3ZGurL/TY3XYwcSkILbATc2BUNMp7nedyCHZaxI7+pShg9PKdHQkWbinYBpYj6EmT/J0TmkvuM/ZcXqScXmKUF2W1ZnrRvU99sYxpMiFeKROYR+Qqt1NUmPjlKC5/uI+fE+iUk6xlGtUSwie5YGdsh5IYMGsE7a+gFjPdVJrxBI9pRKuUTWJICQj7iT1Xxoz+XZ6ozIp8PM5swxZn+KTKH3p1ZBbiiQD+qT0XtrktNBi0R8n+fT6i3NKDeQ+56RJXvvx5Hgn4lc4CX3XN5p9b+xBxwvjV+2HWB/skyQFI6FxBnJnddJ5wLIyE4B5T0/iyaWj206d1Yg/KRLAyGbJZVK17wTzX2uinRLoxAd2gtPREhXJxHN9wqivVXi62XgSjXUCfq+/mPQUL2KaHHu/zux9JL6AE0R5/ZbRdtjED3PC/eC9irNhZX8RY4enGQEseu2EUF37UQxrHamje4J94HQ/F7p07IFYt8/ZsJyte6ziVGQY57LZBmJ1q52EJToS2M14nXY89L3Qvx3FtA47FYBY5+jBsImdCkCsVedYka4JINawp3YlPdrONT+wqEFMJCx6LqSvtw9utkVuwM/VKVhDIZxsNyYFcR3Bn7avVAqsx5ZjYPcT53rlWjlVmtkKbA8BsWqWKJnUxzzIeq3VUbqZ92TlePSxjpXrJJlBBwOulXPufCLJyzwG67WHzBBe1J7wGDtsuFo3s2o9EKs//dIm6apxDrVrtkWjj8SvcNTv9V4+zbJIxMZRFEDlcWVKFB6INZcyxiB322fwr7FPtbhAugocsesb+1CzAthvYV+d1ZJPjPA9EOtfScFZO8366sz29UisJ5mPefDsH2Yau9A78QXYP4yp6bOxzkf2T/+P3gtuC06Nw0Zire0fVHEaJc9OMkpsuMC/3WMxS4ectR6ItY40jFDYisYqTlJzHsz29ZjtGkNpzM9nUmDn+BYNTthIALGOUedk/lqTAp1jWrw5dsJ4IFbTqvvcgDEpuJnfvaknXYxNCux7sxLcJlkztgghbjv6NoXZ5B6I1cswdCdxfOfMv1ZhPxU5SRUUkwJ9b0paN90d2FcnmxWwPNu1WJ1YbIYiu1Pwb311xIvQNRFR55gKoJaVx9jh1Vdl2XUmBVW5bQd1Fm9iWxv0ffTzhLwwKewBOE5xGTb24GNtqXUbMvoXYey934Oe2x776sw1pidZkiaF1Wy8GHqw9QVc66uxTif1FEwKbEy/Ugzk3tekwM7Z2WiO8bbH6sQxpb2I390UJuQGSh9zM6Lw2PO1hTg7iU03KagUumRqYTaAB64to5ohSvwdJgX6Pq/aBrO/PHpix2xeSH0Ak8KcWFfs5GY7DbjW2FF2IxkSJgVekVPBy5GozZPHBOu+flUdJFIYOywycmf3vh6rODVdozBG9pgTG6ZopScMcO3ptdsHu+30wLX7ZL5NZol64FqzmdZxPjEp0PfRnes0KgWeWDvTZJJ6QyYFt8NlpGBHIZMC+/6UOBMSzWCHO+icvuIOJDLCpKBKa7Bvyfy3MCdWVmVxyCYFXLu2Hd3sFsMD18Yxy26kQptJwc282do2smxUgWuPU0YCqbhvUqhzYs1Kz1rs+aqptsRHAvuolTU28ZSZyYQdMMwodOxuyAPXSkl7RBIHY1LgFal1Ja5XgWtjtX+e/0XsPrK6WSeEhzxwrdk4YVNG9sC1cbQgm/TcMCmsDO0b7VhrUmDnHBcx64lgUhDPNE4eG4nsMkMU1n1zqbI6WyaF95ia2mL3hR65ttiid+zm1KMn1nRO3cQvalLgFTkFdDqJljEp9AbmlOidggeunbYejvuZSYE3MPo+I6nIaVKYE1sMj0gtMZOC+5zZ3bEV7lIBuLYmMxUGszEDcK2MaQYMs5kCVifuEm1DkpEIwLXTTtDK8jJNCrtN5eo8qf5jUqBz3LmEYRQQgGuX334FEqF3QhMvvbpCrJvdUAb0xI4dIr1nCsC1veVT8pWNKnCtjUQ1DiH7MWBObNius9h0k8KqfVOF5WWaFNwpnMuoziyYgFxrirxU5msKWJ04Grx7tmsDcO0p4bRdoN8IeZmtzNJI1RiTeov+WHmz0yp4vEtbudE794CeWL9bLmwPBfTEplQ7qyxhUuA7Oc35aMxDAK71bZv5yvZ2wJzY0OxYY/dyAbhW5OwhEpdmUjD2qrkPdrsSgGtTLHVXEl1kUnCfYyhspxodCbTvS5ROKpga4GPU09hb6E7DfrG2N3oknb5MCiLOSnEGfewbsV+saQnb60wzAdfageZdJ7mUJgX5CtFeypOseJN6y087HkhyWgXsurNTr41ZjwG41s1zBc5OvoDViW3wx2L8GNATK0n6YsQakGuDzraYFykA1w4brdpIfKFJQTV6dyoOMnYPwLVm4pxW4/Tt8Q55iBnlTH8ljNvWUROJ7DIpiHItNq5KqqqZFPZmt/nydK0C1+7lmtskYtaHjHfI3k5a0iPOpGDd9+Ymq45nUqDv++pGBfS3IPojzGBvxmYb+8XWnCSyKIsAXLvM7h2OVHEyKThrS8yTdfkwKdD3s56y7/S3sLOj/eemuzZjBILZooPEnNrRCvZ9cT1n0hnNpCBuOy2DKBaVEoBr8z6FJpi/NmC2ayh7NHYjEtATa8u7TlLpxaQgAye3sIXuDiRWM5GNnOjbX6Ma8ph1sJvAgFWc2hqZRSGaagBPrGaXBukYYlJwO9xLsDVGfwtW9LCDllWZNimsZiOGtVTnALHutVcPVEcDsfqVHc3UMylY0WP0Vuk5BCza1tqnhjyTgtz5U/l20TUBLLpNX56MaiaFsQXZLDmqJ4BFbTG3QO/JA1YUdtmvSK0h9LHGWGNifpgALFq88Wolkc8mBT5Wb3YH6/1kUngHk+vw7BYjAIvGHQftEGVScOfepimXyN4eWTSOVVjer0lhTlndzZP+tSaF974n2Y1UoTMp8DXZebYdyQ42KbhzP7bkZHfuob75t2uvLJohIIvm7tpmHrwALLrm6U1N6omaFOSxzlzzIPmPJoUReqcAGdNMEVg0d9OFrE/saTZy/ZZub5hG9lAEFnVlGUkzj2dEFjXbtzhSCc2k8B5Azchl9BuBRbd4GzGmVyOwaN9dZyfV1U0K4pl6qlFJLX2Tgq7Tzp/UUzqqMPbbFKYn+UM+AosWEUMFNo8RWDS7neZm9/cRWPQ0YZiNdIA1KcjAcafgEdNyEVjU1VMegEXxRGTRmlJg/ZpOJD0wci9+shuRCCxqNo4tCXYvF4FF7b9G8yxaOQKLbmOhtUmtFB+RRcVM98zuASL6WG3qe2K3ihHrM+01eiGVcUwK7gE0pC2d/kXQOWI2U6erMGBswV51kBqNJgUsmnasymzyiCxaJDR6exexPtPpzFGYJyNip5xURpqkNoOPwKJuG/FF5t+OwKLlkHSmYw8sahZbOH3pmBTcPeZUNj2RI7Domrp1JzZD2ClnZ8NtZq9GzGPNJVW+vuKbn++UaaTvBSwqMRmhsN0BLLokmWKl+xE7wO5Sl5KKFyYFPCTJUI3UezQprCisPXTmkYrAosXl4YWOKtZnqictk/nTIrDoWrP5wiKfI7Bo2NuFxO4UInaAzdOlxCItI9ZnMu07GuPHCCzq3PTdsRukCCxajNJkMqs2IoumtENn918RfazzNNhifquIdYdPQRJHOoaYFEbDu6iJZeBErDsshgqNecEjdsrZxkOF2UwRWHQbBfjBotcisOiIy9n4M20CLOpOFc1B9xCwqLZjAdAZwg6wMZywWrbugUWdGXyZ7w7wsdpuXENInQeTgvztmXVm+o1ArL3HcfoeMikYe2kGnSxONAKxtq6+bmb7RuwAayqgREYBEYg1piXnmo9Iofd0u7PZ2GwDi4pP2aw2dioAixpNnFovbLaBRZdTtz27TY/IojYKcZKuKCYFFozrdXdH/yJok7Rz9aSGi0lBxMbYJ1advhfklB0/RmT3mBGIVZcR6wzsvTCPtQwzYuj6wt6uZiJP1mvGpGBFp1O/lO4hIFa1qe+Z1LU2KdAmbhQ709iKBmINY5ZFs58iEOs6tfQ7ywyKGBXsc6yFjxd4Mpz0uagdjRWFTw+ZTXotmxRSk9ZY2Y1uBGI9Q78SXTlArMWsqpKp5Q7EWgwWQmHxXxErL40mEllsQQRi9WUFbcxzHYFY226ikVp82APHTndtLJI3ArHu01SXeh8SEGsrrQbHYjsTEOtIdj5vlhuYMCq49JWExVklJNZQiq/Me5qAWKs/NgepemVSQKxL/WadME0KogZimq0w2yQBsRYxLp8sGj5hD5xaZm/sTEsYFbyCX5NFPicgVrMt+irsZj4BsS5D6TbpXwRiDafNmmOesoQVhecoQ78z9cKHVPrb+s8aP/22fv/nK9frlAWbnfkFE2a0xmnkyvw2Cf2ozquwDuGnCxPcG5jpV0mNe5OCu8rRW6uMERNmtG4vNbGbigTsWnc0M5LdtCasweSb2z+QAl9ePt28WORcAnbdKmtGUt3SpDCj1U7qwHK0E3bDqeq1Ms9HestoPdVQmNcpYQ0mH5o0Fo+YsBtOPwkCzOJJwK5G8XU70kfJJ2BXb8pn0bjlFPGeWO1/UE0G7GqHvmuDxQ8kYNedZlDWhcCkQPtIm0XZuZWwtnCMPVGLJyG7ittlsviUBOxqg3WiPJnGwC6voZnyZ+dWAnY9kQjRkbqCPqWrC5SYnTwSu0tKwK55FeMDxgMJ2LWqk0LjnxLWFg4rzbGYJgN29c2Oh0nPGmBXM8599cxyTciuUZdkdhuTgF177mk4xmIJ2LV4v9Nm3oqE3XBOXTBl8SkJ2NX2UGubdA8yKaxnLslGls0Qsmv0205KYm0mYNcT3uEX6dpgUnhXaWdgppoc2DXHFZOwO8GE7NpPYh+zxBLGB+81ThFMJgVj35Isx2KgE7Crdlk1s8yrBOyaXQ2d9XYwKcy9jN27zeYR2PU0/FqOnuDYM8cNZ2caW9FYqam2EzTKvhHYVWzw62bRpwnY9dxT5kK6EJgUWjxVptCzFvNe495eWQ2ThPHB3iyQzmoAJOzyaopJaGRAAsLNOw71jF2T4h19PpkqTMsB4TrbG6YF6G9B/l/wu3RSP8akwNKfZXrPPGsJCLfP6lugVgfGB8/mTuAik8J6h2YvFlaJIgHh5jpyqfR0B8LtRjOmK9hOw7xXOVmCLEItAeGe8mqmddhaBcL9CvGKLDIgAeGurr0M5qVLWKmpFDVjme1tIFzV0+6S5VUnrNRkvzMK8xSlgmetiNnL9O2xo3o03Cd9Inx66/LaxQvzOiUg3FPU1SsdVezyurKZPZR5kHCLczOxuKyE8cHllLinlkJFv2A8tgkb+3p1sxfXWhYWa5wqrvu0cyD9W00KIjyqnR183aNPdppYj2S2MxDumBIbq3doUnDWHsSl93gZ44NT2mbMkffKmPfazZKmOUIZCDdpT2mx2c5IuOM0u2MzlJFwbUOJJ7XtTnb6tR9TNNudkXcGwg2unXJO9BvRH57VzmQmhYQ7NRqlEkshY95r3zaxTPtmINxYl5+DZZhnrC2sZTgaMZrBJ7tOscPCNGYGru2mmspksS4ZuNYg7aAhHQms47/UTnj6FyFWb8lumfn8s8fY7NZcZp7bDFxrkGMnDNPkGbhWt5nRfEVjbeF1qm6yulwZuHa2OFZgLJqBa9s89eEY82XgWm3GrJX0ZDIpiAM5yT+ZdKYwKfQLNr828x3kgPrebO3IbMyMPlmD2q7shi5jz5x4wphZ1lsGrp3BxeVZBGQGrh1j5EyjTzNwbas70L5AJoVn7bmMZzHQGbu81mbrmfnWM3CtmTmnzxj9RtA5P7QBMnCtHXtzB+bBysC1LURZLbPfAq5tyWYys0zuDFzb+imcxk6YnK5u9uuU1a8spz0j10ptMbFs9Qxce7o7OZoJmYFrp6FKDCwXOqfrrE09ZN38t0DnnK4Hjdn3Gbg2a1meVYg1KchB631X6ofIwLU+DVv67JY7Y23hJkE38wJn4FrTUjqEMXIGro31MBOr1JSBa+WketI7+gxcO8xCK4tqJuBar7pmZzFLGbh21D02177AtStUiYPOEHCthLZ+MF7AtbvPfJoCMCnIOc67hcAynDJw7fS92fizU9S49rkKwwiqrEfHaT0Ct3dm4w5W/ypjFPGcbXuWb5SxF6yuIDQjLL/VczIwrPy3gGuNkJdQSwG59lTFdixDMxvXPvuQiFvDeJSd7tgz5xQN36yKUQaujTZBZlOw/ajIVlNXpxYfcG0dJ+Se8VAGrlUn1Sm1OrAC8a5mbDN/ZQZinaYYt7Cst4w+2XjYl2VVZiDWVnuKwiLec7nqfBqDmEXB7h4zZrSOWWznsW/Ebjh11JMrzKTS5RUxDWCowNYXdsNx6/ja2WxjN5ztjNxZHdkMxNps6djw07eHiD6z5WZltRMzRhHryeBguTgZiLWHkSuN1MlArHaczdKofQ/EKlpdoPHUGbvhJDEQYNnqGYh17R5TZpE6GYg1GUkHz6o1ZvDJxmG2kecjAaeoRucL1V8VqiK6FHjkbzZifTFyc3MUFvsvGEUsRdxm99ECxLq2js66lZgUVObrxmCD+RUEWLRLcY2uQsH+rWbg58VmSIBFy+lqlkm9aZNS3GmzVBbFIsCio5iROVhGvrz1uWnLzAAy2wIs6ro/neeoFOSN+a4xkn7wJgXapJs+mEybCLCoTU8KgVnI4i9tUrQW19laNaK/zlrX52J1sE0K677FHSOp/25S2OcmbR9YxKh4jBc7kU2s6qYAiyZNJ/OWzSOwaNgnqZJFLQqw6FerAnojIsCiepwUwk53QR9rkNIzyxsTYNH5FbHEalcLsKg7gXqB3Q0JsKid9KWXQP8i1kOxvRb4eIG/o8sJgWJ7KGBstoqy/mFegEVbmsv0HPutiFFlIbTG7rYFWFSnTFOrbN0Di/p9+kmwTCJBFpW5p9A9BCxaYq48G08inqKnawOL8hRg0XQiabtj8xgvTS7D28iTnuReIPI3juVCZzFxgh1sUvN5s9h/Qe9p0e4G86cJUKaeO7PObHLByN81VxmsAp4AZZ6Lxx5ZlSzBzqxRxmhcCkZ1fcVTs1UIlNmaN9uX7kegTDNVzTJhFowAP/Zk615YhTLBesDLnarUTOcAP2pavSWWoSnAj2ZVuRFYxKgAP/ZdxsysUoAAP840NLvJ1tdb3SRTOo7dfwnwo84+jcDY7shvUXguDFbxQdAvakPRhMVPCNYD3s6fFFMmhdXFi2xlFa4F/KL7lBMZLNdegB/rbOc+jK0J4Mddw860UqYAP0oY0herpCPAj+PwWKCnKPLjNEyjUTwC/FhTDfbf7NwGv2hI0yCM5RIKdrCZNqs0PliAH03d2M6m5yPwY19ijM9u+QX4MczQPPU+CPDjOOdLpzsN+HFvo+REzyHsuTpO/QU620CZfqj2xKptCNZNyrPYvLIVDZS5clq2LNjYY92koM0rq4AnBe9N8nKOVWGTct2blF6brTAqddmFI7l4coWZ1GUXlnP/7dkdjGDdpFJKZr2PTApsclfiYv3NTQop02nlZ0dB35yLsdC/CJSpNeSRqL7HesCm7mmvTpPCuqi1TFqhX4AydaVqhxqVAh46bRsm80gJUOZ01RYio3IBykzVqed7Gygz69h2cDNdiHWTDvzSaAbByN/TDquybAMFylwpxpbZHlKgTLMK56b16BQos/Zshzs7hxT8oslefk9Gmereoq5PYWoqhfXC3KyD+bcVWNTUZdrC4kQVWXScsAHm71D0i5YQa2O3BQosWpuvp1YxkQIWzQblvjILRoFFd0y+dZb1plg3yTTTqRXApOCePHhTcnS80C86ZIfN4uUUc1XNQuuV7W0FFm1aTGMyylTMVT2292I3p4r1gI21Z2LWtmLdpBJb3+zmVIFFDdHyZv2nTQp7DcdpuMDeK+Dtyp5zMttXsR5wSbaLmM5RYNEdYo+B1YdUYNG5defFPLGKftFTUkzYKarAotm7aSYr0eQacOxXcrR2tWIHm2n6V9ntsCKL9lPYinm31FjUP/WEaaUy2LmtGO/bvJ+J1RVUZFGNye3FZhtY1LDWlAuLjVJj0ed7RTXbKrCqKQosun13azLKVGDRZnaCo/ETCn7RVIy3qZdSwS9qmyyaFcI0E/Zc1ZlP33smBf4hW4NlsNs7xXrAdrxnxyxkBWL1vTi/WCVWBWINZmAa7NC3h5q0qvYxLLdEgVjLiFvozalirqqZoVKZzaRArLNWE2NRA4rE6nW1wTyxmtGTMerYLMZN0S9aQuqV6q/3ukmjTxY1oMi10vKcrKKI5st6jEF79szO0QyRXbLa2p6teyDWVdq5iKXvBafokJET1QBArNlsCUcz8hWINWmJrjPPj6LHM85cI/PpKBCrN6swCYuiViDWePqrFxbBrljp19BkOXbro0CsyQh50r4tih1shp1Xi8UXKhCrVyOdyix3BWJdS+Ys7MZN0eNZtYxCtRxW+tVtRy/9Ruxgs30ZNG5IkVi1+xTZ/ZcCsZ4aB2tQPQHEOswEaImxlSre6J7sLFYbXYFY8wkJnCyWX5FYi+2CxOLJFf2iM5VU6bp/62AjcjoJEyms9LtH0sai9BWI1ffeZbGYGgViXSXGuFh2imIHm7lcoJ5rLRhNmsQMK3IHo+AXrVtOqSk2EuXSOdryWJPVDFUg1nLqKgiLB1Ag1tDF2wqj3wiejDmmb5GN/Vsk78gS6V8EYq3LYJuvQiBW0ze7N1ZZQYFYYxx7RBa1qVhdyZa0b+yGUoFYg6xUaG8HffOLSq6B9BE3KbAec991U8sdiNW71U/yKJOCyK7kT2Aw2UMFe64GiYP1UzYp7ORhyn53MhIF/aI7G6exO4UCxFq8SyEz/3ZxcFOzsq+0akoBYk12Dh1aYFKQNePcaWdP1lcBYj1Zzn6z9VWAWG3cXSpspxUg1uib87Oyt8dI3rHFDAo2qkCsY/hu1hyxYAoQa47DjjSWZ1iAWPcYzcztr0EO8U3K9vb+71/HT7/88bLNT/dSkW//wqd0vUmPaDav/44H+5C2vf4pPaPWMb/vUz6l/U06GBPW/b1fP6XDTbqaAgvt2+b7lI43aUMLQ7HIvtJ0waf0KajsWqbS+SZtZ/Vw5ZunPqXlJp0MSmNI9Lf1Jl3aiej/5oVP6ftcmn1YtiYy89XdZ2ecLPf6fXvzKX3NzitaSOYJgydfWd19dlabJda+mXS8/XaZO/b6XdftU/o+O611dWvTN7mPt+S2R/3ef5/S9xHU4Ufx33GrH9L+PoKn7Eh/1JH9lL6v79FHMHSl0oGM9zLDtJB1Uv19fdtJnXVWNvP+PoJGVM2giuzL6sn6PjHhJdAxuY+3HW6pjUXfhKzvaLaxfmcLfkqT9V3Pjd2mv33XVd41W1WBjXe4z+UJHNDl2KoK97n0oduk0RUb7ruhz5DtMGS7Idzn0izD2bJnXxnuc+l87qqN6MEa7nNpYCumZok+qeE+lyozzOKp9H0us+F1Dt9Zt5/S97ms09SgOqZ9wn0uz3sk58nZUNP9vZ2OKo9MiU/p+3vbNstlZXKm1UTeOybbOXSnpft7hzSiC5GNSb6vwZzXLL2xN8n3NWjEU2prbEzyfQ2ONpJ/2Kaf0vc1OHpdNTKroOb7Ggx9nERQtosz0Sd2vDTHpdlcrjTjt83zKU30SW/jlPRi0ve5dLWdoE46O/e5TO5EnDamB+U+l7usKpXuHSFng+a+gmMaQu5zWcwIMxXBtI/eTxInukJWNvN6P4tdDLF3egLqfS5zjT0/qnl/ShPbR851XmL7UvX2JlLn6IXqWCUndy9pzMn0oN7ncot33St770LsWJFcEj1Jyt1SsmOh9sCl73P5V9L3fRnFTp1e2VyW+76U3Vp9ZHp/SpOzoYe5G13f5T6X3Rc9vdqY9H1feplZHLU4CJOMkl1dmb03oYy1Tv91uucJZdR5iPM7aiEkkDbbLP/tl3/++6ff/v7H368FXtq5lbwvlOCi3Kfe59I7UbKntOF96oMv41EY9VOaHCWmwXIlB09w6T4oErexlLurTZMm6ue0MSmLSt+X7IzBgNbdt5pJ35dsm66VsNiYpPuSPb1hVhb62/cl61bdMc/7gRkcUfeaWzermn0lUfetROel37dDcETdr7naGIn9NlH3pxapL/F+OARH1H3Mdv6VcDeVwmkSf1PJZadjuDHp+3jbD7vl2n1jhtM0/rYGndl4jxa6n9J3FdG25hH0blKH00T+Jj3S1kc7qk9pchjHvu1sYHtHmWFlx2iIbA3qfXZm7xIWHRNyvDYjzJwWWyd6n50DU6XRr1SyG7LZ6gzUgiOH8epma05iUgfHDmNTgn22u/ETnDIFbqZF1ru6D44cxiu1WZmpFBw5jG0/2ZLd7CvZYezcnD6xryz3uSzRRrAW+tv3nbb3idn0bMWSw3iPZFuz3Q+e4MhhXPeXKcJGkBzGbp3zP5OvPA2mzzF1iv5dB70Nd/5OIH8rTxdOudvvQ+1nONWm+G32z9c+jg7lTwDWkf+9/frHv375d/t9vRb6Dlkqq+wTTvTKS8lqq3kzFXF8seen//jfcMuRXQjlocAFpE33fr/4+K39uv55aQkjTDN/v3dyeX+g/u1b+Kff1x/rz2sJpNTFfXs73z72kNnX3/j3H39f//znT/81xqUxXMydldyKzqDwZQSlUJ/JqkHfpOz8+frYn/9cv7TfXgmPdmwuSedc/v/+fw=="))));
$g_DeMapper = unserialize(base64_decode("YTo1OntzOjEwOiJ3aXphcmQucGhwIjtzOjM3OiJjbGFzcyBXZWxjb21lU3RlcCBleHRlbmRzIENXaXphcmRTdGVwIjtzOjE3OiJ1cGRhdGVfY2xpZW50LnBocCI7czozNzoieyBDVXBkYXRlQ2xpZW50OjpBZGRNZXNzYWdlMkxvZygiZXhlYyI7czoxMToiaW5jbHVkZS5waHAiO3M6NDg6IkdMT0JBTFNbIlVTRVIiXS0+SXNBdXRob3JpemVkKCkgJiYgJGFyQXV0aFJlc3VsdCI7czo5OiJzdGFydC5waHAiO3M6NjA6IkJYX1JPT1QuJy9tb2R1bGVzL21haW4vY2xhc3Nlcy9nZW5lcmFsL3VwZGF0ZV9kYl91cGRhdGVyLnBocCI7czoxMDoiaGVscGVyLnBocCI7czo1ODoiSlBsdWdpbkhlbHBlcjo6Z2V0UGx1Z2luKCJzeXN0ZW0iLCJvbmVjbGlja2NoZWNrb3V0X3ZtMyIpOyI7fQ=="));
$db_meta_info = unserialize(base64_decode("YTozOntzOjEwOiJidWlsZC1kYXRlIjtzOjEwOiIxNTc0MDgzNTEzIjtzOjc6InZlcnNpb24iO3M6MTM6IjIwMTkxMTE4LTE0MTIiO3M6MTI6InJlbGVhc2UtdHlwZSI7czoxMDoicHJvZHVjdGlvbiI7fQ=="));

//END_SIG
////////////////////////////////////////////////////////////////////////////
if (!isCli() && !isset($_SERVER['HTTP_USER_AGENT'])) {
    echo "#####################################################\n";
    echo "# Error: cannot run on php-cgi. Requires php as cli #\n";
    echo "#                                                   #\n";
    echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
    echo "#####################################################\n";
    exit;
}


if (version_compare(phpversion(), '5.3.1', '<')) {
    echo "#####################################################\n";
    echo "# Warning: PHP Version < 5.3.1                      #\n";
    echo "# Some function might not work properly             #\n";
    echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
    echo "#####################################################\n";
    exit;
}

if (!(function_exists("file_put_contents") && is_callable("file_put_contents"))) {
    echo "#####################################################\n";
    echo "file_put_contents() is disabled. Cannot proceed.\n";
    echo "#####################################################\n";
    exit;
}

define('AI_VERSION', '4.4.2');

////////////////////////////////////////////////////////////////////////////

$l_Res = '';

$g_SpecificExt = false;

$g_UpdatedJsonLog      = 0;
$g_FileInfo            = array();
$g_Iframer             = array();
$g_PHPCodeInside       = array();
$g_Base64              = array();
$g_HeuristicDetected   = array();
$g_HeuristicType       = array();
$g_UnixExec            = array();
$g_UnsafeFilesFound    = array();
$g_HiddenFiles         = array();

$g_RegExpStat = array();


if (!isCli()) {
    $defaults['site_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/';
}

define('CRC32_LIMIT', pow(2, 31) - 1);
define('CRC32_DIFF', CRC32_LIMIT * 2 - 2);

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
srand(time());

set_time_limit(0);
ini_set('max_execution_time', '900000');
ini_set('realpath_cache_size', '16M');
ini_set('realpath_cache_ttl', '1200');
ini_set('pcre.backtrack_limit', '1000000');
ini_set('pcre.recursion_limit', '200000');
ini_set('pcre.jit', '1');

if (!function_exists('stripos')) {
    function stripos($par_Str, $par_Entry, $Offset = 0) {
        return strpos(strtolower($par_Str), strtolower($par_Entry), $Offset);
    }
}

/**
 * Print file
 */
function printFile() {
    die("Not Supported");

    $l_FileName = $_GET['fn'];
    $l_CRC      = isset($_GET['c']) ? (int) $_GET['c'] : 0;
    $l_Content  = file_get_contents($l_FileName);
    $l_FileCRC  = realCRC($l_Content);
    if ($l_FileCRC != $l_CRC) {
        echo 'Доступ запрещен.';
        exit;
    }

    echo '<pre>' . htmlspecialchars($l_Content) . '</pre>';
}

/**
 *
 */
function realCRC($str_in, $full = false) {
    $in = crc32($full ? normal($str_in) : $str_in);
    return ($in > CRC32_LIMIT) ? ($in - CRC32_DIFF) : $in;
}


/**
 * Determine php script is called from the command line interface
 * @return bool
 */
function isCli() {
    return php_sapi_name() == 'cli';
}

function myCheckSum($str) {
    return hash('crc32b', $str);
}

function generatePassword($length = 9) {

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);

    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
        $length = $maxlength;
    }

    // set up a counter for how many characters are in the password so far
    $i = 0;

    // add random characters to $password until $length is reached
    while ($i < $length) {

        // pick a random character from the possible ones
        $char = substr($possible, mt_rand(0, $maxlength - 1), 1);

        // have we already used this character in $password?
        if (!strstr($password, $char)) {
            // no, so it's OK to add it onto the end of whatever we've already got...
            $password .= $char;
            // ... and increase the counter by one
            $i++;
        }

    }

    // done!
    return $password;

}

/**
 * Print to console
 * @param mixed $text
 * @param bool $add_lb Add line break
 * @return void
 */
function stdOut($text, $add_lb = true) {
    if (!isCli())
        return;

    if (is_bool($text)) {
        $text = $text ? 'true' : 'false';
    } else if (is_null($text)) {
        $text = 'null';
    }
    if (!is_scalar($text)) {
        $text = print_r($text, true);
    }

    if ((!BOOL_RESULT) && (!JSON_STDOUT)) {
        @fwrite(STDOUT, $text . ($add_lb ? "\n" : ''));
    }
}

/**
 * Print progress
 * @param int $num Current file
 */
function printProgress($num, &$par_File, $vars) {
    global $g_Base64, $g_Iframer, $g_UpdatedJsonLog, $g_AddPrefix, $g_NoPrefix;

    $total_files  = $vars->foundTotalFiles;
    $elapsed_time = microtime(true) - START_TIME;
    $percent      = number_format($total_files ? $num * 100 / $total_files : 0, 1);
    $stat         = '';
    if ($elapsed_time >= 1) {
        $elapsed_seconds = round($elapsed_time, 0);
        $fs              = floor($num / $elapsed_seconds);
        $left_files      = $total_files - $num;
        if ($fs > 0) {
            $left_time = ($left_files / $fs); //ceil($left_files / $fs);
            $stat      = ' [Avg: ' . round($fs, 2) . ' files/s' . ($left_time > 0 ? ' Left: ' . seconds2Human($left_time) : '') . '] [Mlw:' . (count($vars->criticalPHP) + count($g_Base64) + count($vars->warningPHP)) . '|' . (count($vars->criticalJS) + count($g_Iframer) + count($vars->phishing)) . ']';
        }
    }

    $l_FN = $g_AddPrefix . str_replace($g_NoPrefix, '', $par_File);
    $l_FN = substr($par_File, -60);

    $text = "$percent% [$l_FN] $num of {$total_files}. " . $stat;
    $text = str_pad($text, 160, ' ', STR_PAD_RIGHT);
    stdOut(str_repeat(chr(8), 160) . $text, false);


    $data = array(
        'self' => __FILE__,
        'started' => AIBOLIT_START_TIME,
        'updated' => time(),
        'progress' => $percent,
        'time_elapsed' => $elapsed_seconds,
        'time_left' => round($left_time),
        'files_left' => $left_files,
        'files_total' => $total_files,
        'current_file' => substr($g_AddPrefix . str_replace($g_NoPrefix, '', $par_File), -160)
    );

    if (function_exists('aibolit_onProgressUpdate')) {
        aibolit_onProgressUpdate($data);
    }

    if (defined('PROGRESS_LOG_FILE') && (time() - $g_UpdatedJsonLog > 1)) {
        if (function_exists('json_encode')) {
            file_put_contents(PROGRESS_LOG_FILE, json_encode($data));
        } else {
            file_put_contents(PROGRESS_LOG_FILE, serialize($data));
        }

        $g_UpdatedJsonLog = time();
    }

    if (defined('SHARED_MEMORY')) {
        shmop_write(SHARED_MEMORY, str_repeat("\0", shmop_size(SHARED_MEMORY)), 0);
        if (function_exists('json_encode')) {
            shmop_write(SHARED_MEMORY, json_encode($data), 0);
        } else {
            shmop_write(SHARED_MEMORY, serialize($data), 0);
        }
    }
}

/**
 * Seconds to human readable
 * @param int $seconds
 * @return string
 */
function seconds2Human($seconds) {
    $r        = '';
    $_seconds = floor($seconds);
    $ms       = $seconds - $_seconds;
    $seconds  = $_seconds;
    if ($hours = floor($seconds / 3600)) {
        $r .= $hours . (isCli() ? ' h ' : ' час ');
        $seconds = $seconds % 3600;
    }

    if ($minutes = floor($seconds / 60)) {
        $r .= $minutes . (isCli() ? ' m ' : ' мин ');
        $seconds = $seconds % 60;
    }

    if ($minutes < 3)
        $r .= ' ' . $seconds + ($ms > 0 ? round($ms) : 0) . (isCli() ? ' s' : ' сек');

    return $r;
}

if (isCli()) {

    $cli_options = array(
        'y' => 'deobfuscate',
        'c:' => 'avdb:',
        'm:' => 'memory:',
        's:' => 'size:',
        'a' => 'all',
        'd:' => 'delay:',
        'l:' => 'list:',
        'r:' => 'report:',
        'f' => 'fast',
        'j:' => 'file:',
        'p:' => 'path:',
        'q' => 'quite',
        'e:' => 'cms:',
        'x:' => 'mode:',
        'k:' => 'skip:',
        'i:' => 'idb:',
        'n' => 'sc',
        'o:' => 'json_report:',
        't:' => 'php_report:',
        'z:' => 'progress:',
        'g:' => 'handler:',
        'b' => 'smart',
        'u:' => 'username:',
        'h' => 'help'
    );

    $cli_longopts = array(
        'deobfuscate',
        'avdb:',
        'cmd:',
        'noprefix:',
        'addprefix:',
        'scan:',
        'one-pass',
        'smart',
        'quarantine',
        'with-2check',
        'skip-cache',
        'username:',
        'imake',
        'icheck',
        'no-html',
        'json-stdout',
        'listing:',
        'encode-b64-fn',
        'cloud-assist:',
        'cloudscan-size:',
        'with-suspicious',
        'rapid-account-scan:',
        'rapid-account-scan-type:',
        'extended-report',
        'factory-config:',
        'shared-mem-progress:',
        'create-shared-mem',
        'max-size-scan-bytes:',
        'input-fn-b64-encoded',
        'use-heuristics',
        'use-heuristics-suspicious',
        'resident',
        'detached:'
    );

    $cli_longopts = array_merge($cli_longopts, array_values($cli_options));

    $options = getopt(implode('', array_keys($cli_options)), $cli_longopts);

    if (isset($options['h']) OR isset($options['help'])) {
        $memory_limit = ini_get('memory_limit');
        echo <<<HELP
Revisium AI-Bolit - an Intelligent Malware File Scanner for Websites.

Usage: php {$_SERVER['PHP_SELF']} [OPTIONS] [PATH]
Current default path is: {$defaults['path']}

  -j, --file=FILE                       Full path to single file to check
  -p, --path=PATH                       Directory path to scan, by default the file directory is used
                                        Current path: {$defaults['path']}
  -p, --listing=FILE                    Scan files from the listing. E.g. --listing=/tmp/myfilelist.txt
                                            Use --listing=stdin to get listing from stdin stream
      --extended-report                 To expand the report
  -x, --mode=INT                        Set scan mode. 0 - for basic, 1 - for expert and 2 for paranoic.
  -k, --skip=jpg,...                    Skip specific extensions. E.g. --skip=jpg,gif,png,xls,pdf
      --scan=php,...                    Scan only specific extensions. E.g. --scan=php,htaccess,js
      --cloud-assist=TOKEN              Enable cloud assisted scanning. Disabled by default.
      --with-suspicious                 Detect suspicious files. Disabled by default.
      --rapid-account-scan=<dir>        Enable rapid account scan. Use <dir> for base db dir. Need to set only root permissions(700)
      --rapid-account-scan-type=<type>  Type rapid account scan. <type> = NONE|ALL|SUSPICIOUS, def:SUSPICIOUS
      --use-heuristics                  Enable heuristic algorithms and mark found files as malicious.
      --use-heuristics-suspicious       Enable heuristic algorithms and mark found files as suspicious.
  -r, --report=PATH
  -o, --json_report=FILE                Full path to create json-file with a list of found malware
  -l, --list=FILE                       Full path to create plain text file with a list of found malware
      --no-html                         Disable HTML report
      --encode-b64-fn                   Encode file names in a report with base64 (for internal usage)
      --input-fn-b64-encoded            Base64 encoded input filenames in listing or stdin
      --smart                           Enable smart mode (skip cache files and optimize scanning)
  -m, --memory=SIZE                     Maximum amount of memory a script may consume. Current value: $memory_limit
                                        Can take shorthand byte values (1M, 1G...)
  -s, --size=SIZE                       Scan files are smaller than SIZE with signatures. 0 - All files. Current value: {$defaults['max_size_to_scan']}
      --max-size-scan-bytes=SIZE        Scan first <bytes> for large(can set by --size) files with signatures.
      --cloudscan-size                  Scan files are smaller than SIZE with cloud assisted scan. 0 - All files. Current value: {$defaults['max_size_to_cloudscan']}
  -d, --delay=INT                       Delay in milliseconds when scanning files to reduce load on the file system (Default: 1)
  -a, --all                             Scan all files (by default scan. js,. php,. html,. htaccess)
      --one-pass                        Do not calculate remaining time
      --quarantine                      Archive all malware from report
      --with-2check                     Create or use AI-BOLIT-DOUBLECHECK.php file
      --imake
      --icheck
      --idb=file                        Integrity Check database file

  -z, --progress=FILE                   Runtime progress of scanning, saved to the file, full path required. 
      --shared-mem-progress=<ID>        Runtime progress of scanning, saved to the shared memory <ID>.
      --create-shared-mem               Need to create shared memory segment <ID> for --shared-mem-progress. 
  -u, --username=<username>             Run scanner with specific user id and group id, e.g. --username=www-data
  -g, --hander=FILE                     External php handler for different events, full path to php file required.
      --cmd="command [args...]"         Run command after scanning

      --help                            Display this help and exit

* Mandatory arguments listed below are required for both full and short way of usage.

HELP;
        exit;
    }

    $l_FastCli = false;

    if ((isset($options['memory']) AND !empty($options['memory']) AND ($memory = $options['memory'])) OR (isset($options['m']) AND !empty($options['m']) AND ($memory = $options['m']))) {
        $memory = getBytes($memory);
        if ($memory > 0) {
            $defaults['memory_limit'] = $memory;
            ini_set('memory_limit', $memory);
        }
    }


    $avdb = '';
    if ((isset($options['avdb']) AND !empty($options['avdb']) AND ($avdb = $options['avdb'])) OR (isset($options['c']) AND !empty($options['c']) AND ($avdb = $options['c']))) {
        if (file_exists($avdb)) {
            $defaults['avdb'] = $avdb;
        }
    }

    if ((isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false) OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false)) {
        define('SCAN_FILE', $file);
    }


    if (isset($options['deobfuscate']) OR isset($options['y'])) {
        define('AI_DEOBFUSCATE', true);
    }

    if ((isset($options['list']) AND !empty($options['list']) AND ($file = $options['list']) !== false) OR (isset($options['l']) AND !empty($options['l']) AND ($file = $options['l']) !== false)) {

        define('PLAIN_FILE', $file);
    }

    if ((isset($options['listing']) AND !empty($options['listing']) AND ($listing = $options['listing']) !== false)) {

        if (file_exists($listing) && is_file($listing) && is_readable($listing)) {
            define('LISTING_FILE', $listing);
        }

        if ($listing == 'stdin') {
            define('LISTING_FILE', $listing);
        }
    }

    if ((isset($options['json_report']) AND !empty($options['json_report']) AND ($file = $options['json_report']) !== false) OR (isset($options['o']) AND !empty($options['o']) AND ($file = $options['o']) !== false)) {
        define('JSON_FILE', $file);

        if (!function_exists('json_encode')) {
            die('json_encode function is not available. Enable json extension in php.ini');
        }
    }

    if ((isset($options['php_report']) AND !empty($options['php_report']) AND ($file = $options['php_report']) !== false) OR (isset($options['t']) AND !empty($options['t']) AND ($file = $options['t']) !== false)) {
        define('PHP_FILE', $file);
    }

    if (isset($options['smart']) OR isset($options['b'])) {
        define('SMART_SCAN', 1);
    }

    if ((isset($options['handler']) AND !empty($options['handler']) AND ($file = $options['handler']) !== false) OR (isset($options['g']) AND !empty($options['g']) AND ($file = $options['g']) !== false)) {
        if (file_exists($file)) {
            define('AIBOLIT_EXTERNAL_HANDLER', $file);
        }
    }

    if ((isset($options['progress']) AND !empty($options['progress']) AND ($file = $options['progress']) !== false) OR (isset($options['z']) AND !empty($options['z']) AND ($file = $options['z']) !== false)) {
        define('PROGRESS_LOG_FILE', $file);
    }

    if (isset($options['create-shared-mem'])) {
        define('CREATE_SHARED_MEMORY', true);
    } else {
        define('CREATE_SHARED_MEMORY', false);
    }

    if (isset($options['shared-mem-progress']) AND !empty($options['shared-mem-progress']) AND ($sh_mem = $options['shared-mem-progress']) !== false) {
        if (CREATE_SHARED_MEMORY) {
            @$shid = shmop_open(intval($sh_mem), "n", 0666, 5000);
        } else {
            @$shid = shmop_open(intval($sh_mem), "w", 0, 0);
        }
        if (!empty($shid)) {
            define('SHARED_MEMORY', $shid);
        } else {
            die('Error with shared-memory.');
        }
    }

    if ((isset($options['size']) AND !empty($options['size']) AND ($size = $options['size']) !== false) OR (isset($options['s']) AND !empty($options['s']) AND ($size = $options['s']) !== false)) {
        $size                         = getBytes($size);
        $defaults['max_size_to_scan'] = $size > 0 ? $size : 0;
    }

    if (isset($options['cloudscan-size']) AND !empty($options['cloudscan-size']) AND ($cloudscan_size = $options['cloudscan-size']) !== false) {
        $cloudscan_size                         = getBytes($cloudscan_size);
        $defaults['max_size_to_cloudscan'] = $cloudscan_size > 0 ? $cloudscan_size : 0;
    }

    if (isset($options['max-size-scan-bytes']) && !empty($options['max-size-scan-bytes'])) {
        define('MAX_SIZE_SCAN_BYTES', getBytes($options['max-size-scan-bytes']));
    } else {
        define('MAX_SIZE_SCAN_BYTES', 0);
    }

    if ((isset($options['username']) AND !empty($options['username']) AND ($username = $options['username']) !== false) OR (isset($options['u']) AND !empty($options['u']) AND ($username = $options['u']) !== false)) {

        if (!empty($username) && ($info = posix_getpwnam($username)) !== false) {
            posix_setgid($info['gid']);
            posix_setuid($info['uid']);
            $defaults['userid']  = $info['uid'];
            $defaults['groupid'] = $info['gid'];
        } else {
            echo ('Invalid username');
            exit(-1);
        }
    }

    if ((isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false) OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false) AND (isset($options['q']))) {
        $BOOL_RESULT = true;
    }

    if (isset($options['json-stdout'])) {
        define('JSON_STDOUT', true);
    } else {
        define('JSON_STDOUT', false);
    }

    if (isset($options['f'])) {
        $l_FastCli = true;
    }

    if (isset($options['q']) || isset($options['quite'])) {
        $BOOL_RESULT = true;
    }

    if (isset($options['x'])) {
        define('AI_EXPERT', $options['x']);
    } else if (isset($options['mode'])) {
        define('AI_EXPERT', $options['mode']);
    } else {
        define('AI_EXPERT', AI_EXPERT_MODE);
    }

    if (AI_EXPERT < 2) {
        $g_SpecificExt              = true;
        $defaults['scan_all_files'] = false;
    } else {
        $defaults['scan_all_files'] = true;
    }

    define('BOOL_RESULT', $BOOL_RESULT);

    if ((isset($options['delay']) AND !empty($options['delay']) AND ($delay = $options['delay']) !== false) OR (isset($options['d']) AND !empty($options['d']) AND ($delay = $options['d']) !== false)) {
        $delay = (int) $delay;
        if (!($delay < 0)) {
            $defaults['scan_delay'] = $delay;
        }
    }

    if ((isset($options['skip']) AND !empty($options['skip']) AND ($ext_list = $options['skip']) !== false) OR (isset($options['k']) AND !empty($options['k']) AND ($ext_list = $options['k']) !== false)) {
        $defaults['skip_ext'] = $ext_list;
    }

    if (isset($options['n']) OR isset($options['skip-cache'])) {
        $defaults['skip_cache'] = true;
    }

    if (isset($options['scan'])) {
        $ext_list = strtolower(trim($options['scan'], " ,\t\n\r\0\x0B"));
        if ($ext_list != '') {
            $l_FastCli        = true;
            $g_SensitiveFiles = explode(",", $ext_list);
            for ($i = 0; $i < count($g_SensitiveFiles); $i++) {
                if ($g_SensitiveFiles[$i] == '.') {
                    $g_SensitiveFiles[$i] = '';
                }
            }

            $g_SpecificExt = true;
        }
    }
    
    if (isset($options['cloud-assist'])) {
        define('CLOUD_ASSIST_TOKEN', $options['cloud-assist']);
    }
    

    if (isset($options['rapid-account-scan'])) {
        define('RAPID_ACCOUNT_SCAN', $options['rapid-account-scan']);
    }
    
    if (defined('RAPID_ACCOUNT_SCAN')) {
        if (isset($options['rapid-account-scan-type'])) {
            define('RAPID_ACCOUNT_SCAN_TYPE', $options['rapid-account-scan-type']);
        }
        else {
            define('RAPID_ACCOUNT_SCAN_TYPE', 'SUSPICIOUS');
        }
    }

    if (isset($options['with-suspicious'])) {
        define('AI_EXTRA_WARN', true);
    }

    if (isset($options['extended-report'])) {
        define('EXTENDED_REPORT', true);
    }

    if (isset($options['all']) OR isset($options['a'])) {
        $defaults['scan_all_files'] = true;
        $g_SpecificExt              = false;
    }

    if (isset($options['cms'])) {
        define('CMS', $options['cms']);
    } else if (isset($options['e'])) {
        define('CMS', $options['e']);
    }


    if (!defined('SMART_SCAN')) {
        define('SMART_SCAN', 0);
    }

    if (!defined('AI_DEOBFUSCATE')) {
        define('AI_DEOBFUSCATE', false);
    }

    if (!defined('AI_EXTRA_WARN')) {
        define('AI_EXTRA_WARN', false);
    }


    $l_SpecifiedPath = false;
    if ((isset($options['path']) AND !empty($options['path']) AND ($path = $options['path']) !== false) OR (isset($options['p']) AND !empty($options['p']) AND ($path = $options['p']) !== false)) {
        $defaults['path'] = $path;
        $l_SpecifiedPath  = true;
    }

    if (isset($options['noprefix']) AND !empty($options['noprefix']) AND ($g_NoPrefix = $options['noprefix']) !== false) {
    } else {
        $g_NoPrefix = '';
    }

    if (isset($options['addprefix']) AND !empty($options['addprefix']) AND ($g_AddPrefix = $options['addprefix']) !== false) {
    } else {
        $g_AddPrefix = '';
    }

    if (isset($options['use-heuristics'])) {
        define('USE_HEURISTICS', true);
    }

    if (isset($options['use-heuristics-suspicious'])) {
        define('USE_HEURISTICS_SUSPICIOUS', true);
    }

    if (defined('USE_HEURISTICS') && defined('USE_HEURISTICS_SUSPICIOUS')) {
        die('You can not use --use-heuristic and --use-heuristic-suspicious the same time.');
    }

    $l_SuffixReport = str_replace('/var/www', '', $defaults['path']);
    $l_SuffixReport = str_replace('/home', '', $l_SuffixReport);
    $l_SuffixReport = preg_replace('~[/\\\.\s]~', '_', $l_SuffixReport);
    $l_SuffixReport .= "-" . rand(1, 999999);

    if ((isset($options['report']) AND ($report = $options['report']) !== false) OR (isset($options['r']) AND ($report = $options['r']) !== false)) {
        $report = str_replace('@PATH@', $l_SuffixReport, $report);
        $report = str_replace('@RND@', rand(1, 999999), $report);
        $report = str_replace('@DATE@', date('d-m-Y-h-i'), $report);
        define('REPORT', $report);
        define('NEED_REPORT', true);
    }

    if (isset($options['no-html'])) {
        define('REPORT', 'no@email.com');
    }

    defined('ENCODE_FILENAMES_WITH_BASE64') || define('ENCODE_FILENAMES_WITH_BASE64', isset($options['encode-b64-fn']));
    
    defined('INPUT_FILENAMES_BASE64_ENCODED') || define('INPUT_FILENAMES_BASE64_ENCODED', isset($options['input-fn-b64-encoded']));
    
    if ((isset($options['idb']) AND ($ireport = $options['idb']) !== false)) {
        $ireport = str_replace('@PATH@', $l_SuffixReport, $ireport);
        $ireport = str_replace('@RND@', rand(1, 999999), $ireport);
        $ireport = str_replace('@DATE@', date('d-m-Y-h-i'), $ireport);
        define('INTEGRITY_DB_FILE', $ireport);
    }


    defined('REPORT') OR define('REPORT', 'AI-BOLIT-REPORT-' . $l_SuffixReport . '-' . date('d-m-Y_H-i') . '.html');

    defined('INTEGRITY_DB_FILE') OR define('INTEGRITY_DB_FILE', 'AINTEGRITY-' . $l_SuffixReport . '-' . date('d-m-Y_H-i'));

    $last_arg = max(1, sizeof($_SERVER['argv']) - 1);
    if (isset($_SERVER['argv'][$last_arg])) {
        $path = $_SERVER['argv'][$last_arg];
        if (substr($path, 0, 1) != '-' AND (substr($_SERVER['argv'][$last_arg - 1], 0, 1) != '-' OR array_key_exists(substr($_SERVER['argv'][$last_arg - 1], -1), $cli_options))) {
            $defaults['path'] = $path;
        }
    }

    define('ONE_PASS', isset($options['one-pass']));

    define('IMAKE', isset($options['imake']));
    define('ICHECK', isset($options['icheck']));

    if (IMAKE && ICHECK)
        die('One of the following options must be used --imake or --icheck.');

    // BEGIN of configuring the factory
    $factoryConfig = [
        RapidAccountScan::class => RapidAccountScan::class,
        RapidScanStorage::class => RapidScanStorage::class,
        DbFolderSpecification::class => DbFolderSpecification::class,
        CriticalFileSpecification::class => CriticalFileSpecification::class,
        CloudAssistedRequest::class => CloudAssistedRequest::class,
        JSONReport::class => JSONReport::class,
        DetachedMode::class => DetachedMode::class,
        ResidentMode::class => ResidentMode::class,
    ];

    if (isset($options['factory-config'])) {
        $optionalFactoryConfig = require($options['factory-config']);
        $factoryConfig = array_merge($factoryConfig, $optionalFactoryConfig);
    }

    Factory::configure($factoryConfig);
    // END of configuring the factory

} else {
    define('AI_EXPERT', AI_EXPERT_MODE);
    define('ONE_PASS', true);
}

if (ONE_PASS && defined('CLOUD_ASSIST_TOKEN')) {
    die('Both parameters(one-pass and cloud-assist) not supported');
}

if (defined('RAPID_ACCOUNT_SCAN') && !defined('CLOUD_ASSIST_TOKEN')) { 
    die('CloudScan should be enabled');
}

if (defined('CREATE_SHARED_MEMORY') && CREATE_SHARED_MEMORY == true && !defined('SHARED_MEMORY')) {
    die('shared-mem-progress should be enabled and ID specified.');
}

if (defined('RAPID_ACCOUNT_SCAN')) {
    @mkdir(RAPID_ACCOUNT_SCAN, 0700, true);
    $specification = Factory::instance()->create(DbFolderSpecification::class);
    if (!$specification->satisfiedBy(RAPID_ACCOUNT_SCAN)) {
        @unlink(RAPID_ACCOUNT_SCAN);
        die('Rapid DB folder error! Please check the folder.');
    }
}

if (defined('RAPID_ACCOUNT_SCAN_TYPE') && !in_array(RAPID_ACCOUNT_SCAN_TYPE, array('NONE', 'ALL', 'SUSPICIOUS'))) {
    die('Wrong Rapid account scan type');
}

if (defined('RAPID_ACCOUNT_SCAN') && !extension_loaded('leveldb')) { 
    die('LevelDB extension needed for Rapid DB');
}

$vars->blackFiles = [];

if (isset($defaults['avdb']) && file_exists($defaults['avdb'])) {
    $avdb = explode("\n", gzinflate(base64_decode(str_rot13(strrev(trim(file_get_contents($defaults['avdb'])))))));

    $g_DBShe       = explode("\n", base64_decode($avdb[0]));
    $gX_DBShe      = explode("\n", base64_decode($avdb[1]));
    $g_FlexDBShe   = explode("\n", base64_decode($avdb[2]));
    $gX_FlexDBShe  = explode("\n", base64_decode($avdb[3]));
    $gXX_FlexDBShe = explode("\n", base64_decode($avdb[4]));
    $g_ExceptFlex  = explode("\n", base64_decode($avdb[5]));
    $g_AdwareSig   = explode("\n", base64_decode($avdb[6]));
    $g_PhishingSig = explode("\n", base64_decode($avdb[7]));
    $g_JSVirSig    = explode("\n", base64_decode($avdb[8]));
    $gX_JSVirSig   = explode("\n", base64_decode($avdb[9]));
    $g_SusDB       = explode("\n", base64_decode($avdb[10]));
    $g_SusDBPrio   = explode("\n", base64_decode($avdb[11]));
    $g_DeMapper    = array_combine(explode("\n", base64_decode($avdb[12])), explode("\n", base64_decode($avdb[13])));
    $g_Mnemo    = @array_flip(@array_combine(explode("\n", base64_decode($avdb[14])), explode("\n", base64_decode($avdb[15]))));

    // get meta information
    $avdb_meta_info = json_decode(base64_decode($avdb[16]), true);
    $db_meta_info['build-date'] = $avdb_meta_info ? $avdb_meta_info['build-date'] : 'n/a';
    $db_meta_info['version'] = $avdb_meta_info ? $avdb_meta_info['version'] : 'n/a';
    $db_meta_info['release-type'] = $avdb_meta_info ? $avdb_meta_info['release-type'] : 'n/a';

    if (count($g_DBShe) <= 1) {
        $g_DBShe = array();
    }

    if (count($gX_DBShe) <= 1) {
        $gX_DBShe = array();
    }

    if (count($g_FlexDBShe) <= 1) {
        $g_FlexDBShe = array();
    }

    if (count($gX_FlexDBShe) <= 1) {
        $gX_FlexDBShe = array();
    }

    if (count($gXX_FlexDBShe) <= 1) {
        $gXX_FlexDBShe = array();
    }

    if (count($g_ExceptFlex) <= 1) {
        $g_ExceptFlex = array();
    }

    if (count($g_AdwareSig) <= 1) {
        $g_AdwareSig = array();
    }

    if (count($g_PhishingSig) <= 1) {
        $g_PhishingSig = array();
    }

    if (count($gX_JSVirSig) <= 1) {
        $gX_JSVirSig = array();
    }

    if (count($g_JSVirSig) <= 1) {
        $g_JSVirSig = array();
    }

    if (count($g_SusDB) <= 1) {
        $g_SusDB = array();
    }

    if (count($g_SusDBPrio) <= 1) {
        $g_SusDBPrio = array();
    }
    $db_location = 'external';
    stdOut('Loaded external signatures from ' . $defaults['avdb']);
}

// use only basic signature subset
if (AI_EXPERT < 2) {
    $gX_FlexDBShe  = array();
    $gXX_FlexDBShe = array();
    $gX_JSVirSig   = array();
}

if (isset($defaults['userid'])) {
    stdOut('Running from ' . $defaults['userid'] . ':' . $defaults['groupid']);
}

$sign_count = count($g_JSVirSig) + count($gX_JSVirSig) + count($g_DBShe) + count($gX_DBShe) + count($gX_DBShe) + count($g_FlexDBShe) + count($gX_FlexDBShe) + count($gXX_FlexDBShe);

if (AI_EXTRA_WARN) {
    $sign_count += count($g_SusDB);
}

stdOut('Malware signatures: ' . $sign_count);

if ($g_SpecificExt) {
    stdOut("Scan specific extensions: " . implode(',', $g_SensitiveFiles));
}

// Black list database
try {
    $file = dirname(__FILE__) . '/AIBOLIT-BINMALWARE.db';
    if (isset($defaults['avdb'])) {
        $file = dirname($defaults['avdb']) . '/AIBOLIT-BINMALWARE.db';
    }
    $vars->blacklist = FileHashMemoryDb::open($file);
    stdOut("Binary malware signatures: " . ceil($vars->blacklist->count()));
} catch (Exception $e) {
    $vars->blacklist = null;
}

if (!DEBUG_PERFORMANCE) {
    OptimizeSignatures();
} else {
    stdOut("Debug Performance Scan");
}

$g_DBShe  = array_map('strtolower', $g_DBShe);
$gX_DBShe = array_map('strtolower', $gX_DBShe);

if (!defined('PLAIN_FILE')) {
    define('PLAIN_FILE', '');
}

// Init
define('MAX_ALLOWED_PHP_HTML_IN_DIR', 600);
define('BASE64_LENGTH', 69);
define('MAX_PREVIEW_LEN', 120);
define('MAX_EXT_LINKS', 1001);

if (defined('AIBOLIT_EXTERNAL_HANDLER')) {
    include_once(AIBOLIT_EXTERNAL_HANDLER);
    stdOut("\nLoaded external handler: " . AIBOLIT_EXTERNAL_HANDLER . "\n");
    if (function_exists("aibolit_onStart")) {
        aibolit_onStart();
    }
}

// Perform full scan when running from command line
if (isset($_GET['full'])) {
    $defaults['scan_all_files'] = 1;
}

if ($l_FastCli) {
    $defaults['scan_all_files'] = 0;
}

if (!isCli()) {
    define('ICHECK', isset($_GET['icheck']));
    define('IMAKE', isset($_GET['imake']));

    define('INTEGRITY_DB_FILE', 'ai-integrity-db');
}

define('SCAN_ALL_FILES', (bool) $defaults['scan_all_files']);
define('SCAN_DELAY', (int) $defaults['scan_delay']);
define('MAX_SIZE_TO_SCAN', getBytes($defaults['max_size_to_scan']));
define('MAX_SIZE_TO_CLOUDSCAN', getBytes($defaults['max_size_to_cloudscan']));

if ($defaults['memory_limit'] AND ($defaults['memory_limit'] = getBytes($defaults['memory_limit'])) > 0) {
    ini_set('memory_limit', $defaults['memory_limit']);
    stdOut("Changed memory limit to " . $defaults['memory_limit']);
}

define('ROOT_PATH', realpath($defaults['path']));

if (!ROOT_PATH) {
    if (isCli()) {
        die(stdOut("Directory '{$defaults['path']}' not found!"));
    }
} elseif (!is_readable(ROOT_PATH)) {
    if (isCli()) {
        die2(stdOut("Cannot read directory '" . ROOT_PATH . "'!"));
    }
}

define('CURRENT_DIR', getcwd());
chdir(ROOT_PATH);

if (isCli() AND REPORT !== '' AND !getEmails(REPORT)) {
    $report      = str_replace('\\', '/', REPORT);
    $abs         = strpos($report, '/') === 0 ? DIR_SEPARATOR : '';
    $report      = array_values(array_filter(explode('/', $report)));
    $report_file = array_pop($report);
    $report_path = realpath($abs . implode(DIR_SEPARATOR, $report));

    define('REPORT_FILE', $report_file);
    define('REPORT_PATH', $report_path);

    if (REPORT_FILE AND REPORT_PATH AND is_file(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE)) {
        @unlink(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE);
    }
}

if (defined('REPORT_PATH')) {
    $l_ReportDirName = REPORT_PATH;
}

$path                       = $defaults['path'];
$report_mask                = $defaults['report_mask'];
$extended_report            = defined('EXTENDED_REPORT') && EXTENDED_REPORT;
$rapid_account_scan_report  = defined('RAPID_ACCOUNT_SCAN');

$reportFactory = function () use ($g_Mnemo, $path, $db_location, $db_meta_info, $report_mask, $extended_report, $rapid_account_scan_report) {
    return Factory::instance()->create(JSONReport::class, [$g_Mnemo, $path, $db_location, $db_meta_info['version'], $report_mask, $extended_report, $rapid_account_scan_report, AI_VERSION, AI_HOSTER, AI_EXTRA_WARN]);
};

if (isset($options['detached'])) {
    Factory::instance()->create(DetachedMode::class, [$options['detached'], $vars, LISTING_FILE, START_TIME, $reportFactory, INPUT_FILENAMES_BASE64_ENCODED]);
    exit(0);
}

if (isset($options['resident'])) {
    Factory::instance()->create(ResidentMode::class, [$reportFactory, $vars->blacklist]);
    exit(0);
}

define('QUEUE_FILENAME', ($l_ReportDirName != '' ? $l_ReportDirName . '/' : '') . 'AI-BOLIT-QUEUE-' . md5($defaults['path']) . '-' . rand(1000, 9999) . '.txt');

if (function_exists('phpinfo')) {
    ob_start();
    phpinfo();
    $l_PhpInfo = ob_get_contents();
    ob_end_clean();

    $l_PhpInfo = str_replace('border: 1px', '', $l_PhpInfo);
    preg_match('|<body>(.*)</body>|smi', $l_PhpInfo, $l_PhpInfoBody);
}

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MODE@@", AI_EXPERT . '/' . SMART_SCAN, $l_Template);

if (AI_EXPERT == 0) {
    $l_Result .= '<div class="rep">' . AI_STR_057 . '</div>';
}

$l_Template = str_replace('@@HEAD_TITLE@@', AI_STR_051 . $g_AddPrefix . str_replace($g_NoPrefix, '', ROOT_PATH), $l_Template);

define('QCR_INDEX_FILENAME', 'fn');
define('QCR_INDEX_TYPE', 'type');
define('QCR_INDEX_WRITABLE', 'wr');
define('QCR_SVALUE_FILE', '1');
define('QCR_SVALUE_FOLDER', '0');

/**
 * Extract emails from the string
 * @param string $email
 * @return array of strings with emails or false on error
 */
function getEmails($email) {
    $email = preg_split('~[,\s;]~', $email, -1, PREG_SPLIT_NO_EMPTY);
    $r     = array();
    for ($i = 0, $size = sizeof($email); $i < $size; $i++) {
        if (function_exists('filter_var')) {
            if (filter_var($email[$i], FILTER_VALIDATE_EMAIL)) {
                $r[] = $email[$i];
            }
        } else {
            // for PHP4
            if (strpos($email[$i], '@') !== false) {
                $r[] = $email[$i];
            }
        }
    }
    return empty($r) ? false : $r;
}

/**
 * Get bytes from shorthand byte values (1M, 1G...)
 * @param int|string $val
 * @return int
 */
function getBytes($val) {
    $val  = trim($val);
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        case 't':
            $val *= 1024;
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return intval($val);
}

/**
 * Format bytes to human readable
 * @param int $bites
 * @return string
 */
function bytes2Human($bites) {
    if ($bites < 1024) {
        return $bites . ' b';
    } elseif (($kb = $bites / 1024) < 1024) {
        return number_format($kb, 2) . ' Kb';
    } elseif (($mb = $kb / 1024) < 1024) {
        return number_format($mb, 2) . ' Mb';
    } elseif (($gb = $mb / 1024) < 1024) {
        return number_format($gb, 2) . ' Gb';
    } else {
        return number_format($gb / 1024, 2) . 'Tb';
    }
}

///////////////////////////////////////////////////////////////////////////
function needIgnore($par_FN, $par_CRC) {
    global $g_IgnoreList;

    for ($i = 0; $i < count($g_IgnoreList); $i++) {
        if (strpos($par_FN, $g_IgnoreList[$i][0]) !== false) {
            if ($par_CRC == $g_IgnoreList[$i][1]) {
                return true;
            }
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
function makeSafeFn($par_Str, $replace_path = false) {
    global $g_AddPrefix, $g_NoPrefix;
    if ($replace_path) {
        $lines = explode("\n", $par_Str);
        array_walk($lines, function(&$n) {
            global $g_AddPrefix, $g_NoPrefix;
            $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n);
        });

        $par_Str = implode("\n", $lines);
    }

    return htmlspecialchars($par_Str, ENT_SUBSTITUTE | ENT_QUOTES);
}

function replacePathArray($par_Arr) {
    global $g_AddPrefix, $g_NoPrefix;
    array_walk($par_Arr, function(&$n) {
        global $g_AddPrefix, $g_NoPrefix;
        $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n);
    });

    return $par_Arr;
}

///////////////////////////////////////////////////////////////////////////
function printList($par_List, $vars, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
    global $g_NoPrefix, $g_AddPrefix;

    $i = 0;

    if ($par_TableName == null) {
        $par_TableName = 'table_' . rand(1000000, 9000000);
    }

    $l_Result = '';
    $l_Result .= "<div class=\"flist\"><table cellspacing=1 cellpadding=4 border=0 id=\"" . $par_TableName . "\">";

    $l_Result .= "<thead><tr class=\"tbgh" . ($i % 2) . "\">";
    $l_Result .= "<th width=70%>" . AI_STR_004 . "</th>";
    $l_Result .= "<th>" . AI_STR_005 . "</th>";
    $l_Result .= "<th>" . AI_STR_006 . "</th>";
    $l_Result .= "<th width=90>" . AI_STR_007 . "</th>";
    $l_Result .= "<th width=0 class=\"hidd\">CRC32</th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";
    $l_Result .= "<th width=0 class=\"hidd\"></th>";

    $l_Result .= "</tr></thead><tbody>";

    for ($i = 0; $i < count($par_List); $i++) {
        if ($par_SigId != null) {
            $l_SigId = 'id_' . $par_SigId[$i];
        } else {
            $l_SigId = 'id_z' . rand(1000000, 9000000);
        }

        $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
            if (needIgnore($vars->structure['n'][$par_List[$i]], $vars->structure['crc'][$l_Pos])) {
                continue;
            }
        }

        $l_Creat = $vars->structure['c'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $vars->structure['c'][$l_Pos]) : '-';
        $l_Modif = $vars->structure['m'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $vars->structure['m'][$l_Pos]) : '-';
        $l_Size  = $vars->structure['s'][$l_Pos] > 0 ? bytes2Human($vars->structure['s'][$l_Pos]) : '-';

        if ($par_Details != null) {
            $l_WithMarker = preg_replace('|__AI_MARKER__|smi', '<span class="marker">&nbsp;</span>', $par_Details[$i]);
            $l_WithMarker = preg_replace('|__AI_LINE1__|smi', '<span class="line_no">', $l_WithMarker);
            $l_WithMarker = preg_replace('|__AI_LINE2__|smi', '</span>', $l_WithMarker);

            $l_Body = '<div class="details">';

            if ($par_SigId != null) {
                $l_Body .= '<a href="#" onclick="return hsig(\'' . $l_SigId . '\')">[x]</a> ';
            }

            $l_Body .= $l_WithMarker . '</div>';
        } else {
            $l_Body = '';
        }

        $l_Result .= '<tr class="tbg' . ($i % 2) . '" o="' . $l_SigId . '">';

        if (is_file($vars->structure['n'][$l_Pos])) {
            $l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$l_Pos])) . '</a></div>' . $l_Body . '</td>';
        } else {
            $l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$par_List[$i]])) . '</a></div></td>';
        }

        $l_Result .= '<td align=center><div class="ctd">' . $l_Creat . '</div></td>';
        $l_Result .= '<td align=center><div class="ctd">' . $l_Modif . '</div></td>';
        $l_Result .= '<td align=center><div class="ctd">' . $l_Size . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $vars->structure['crc'][$l_Pos] . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . 'x' . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $vars->structure['m'][$l_Pos] . '</div></td>';
        $l_Result .= '<td class="hidd"><div class="hidd">' . $l_SigId . '</div></td>';
        $l_Result .= '</tr>';

    }

    $l_Result .= "</tbody></table></div><div class=clear style=\"margin: 20px 0 0 0\"></div>";

    return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function printPlainList($par_List, $vars, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
    global $g_NoPrefix, $g_AddPrefix;

    $l_Result = "";

    $l_Src = array(
        '&quot;',
        '&lt;',
        '&gt;',
        '&amp;',
        '&#039;'
    );
    $l_Dst = array(
        '"',
        '<',
        '>',
        '&',
        '\''
    );

    for ($i = 0; $i < count($par_List); $i++) {
        $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
            if (needIgnore($vars->structure['n'][$par_List[$i]], $vars->structure['crc'][$l_Pos])) {
                continue;
            }
        }


        if ($par_Details != null) {

            $l_Body = preg_replace('|(L\d+).+__AI_MARKER__|smi', '$1: ...', $par_Details[$i]);
            $l_Body = preg_replace('/[^\x20-\x7F]/', '.', $l_Body);
            $l_Body = str_replace($l_Src, $l_Dst, $l_Body);

        } else {
            $l_Body = '';
        }

        if (is_file($vars->structure['n'][$l_Pos])) {
            $l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$l_Pos]) . "\t\t\t" . $l_Body . "\n";
        } else {
            $l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $vars->structure['n'][$par_List[$i]]) . "\n";
        }

    }

    return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function extractValue(&$par_Str, $par_Name) {
    if (preg_match('|<tr><td class="e">\s*' . $par_Name . '\s*</td><td class="v">(.+?)</td>|sm', $par_Str, $l_Result)) {
        return str_replace('no value', '', strip_tags($l_Result[1]));
    }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ExtractInfo($par_Str) {
    $l_PhpInfoSystem    = extractValue($par_Str, 'System');
    $l_PhpPHPAPI        = extractValue($par_Str, 'Server API');
    $l_AllowUrlFOpen    = extractValue($par_Str, 'allow_url_fopen');
    $l_AllowUrlInclude  = extractValue($par_Str, 'allow_url_include');
    $l_DisabledFunction = extractValue($par_Str, 'disable_functions');
    $l_DisplayErrors    = extractValue($par_Str, 'display_errors');
    $l_ErrorReporting   = extractValue($par_Str, 'error_reporting');
    $l_ExposePHP        = extractValue($par_Str, 'expose_php');
    $l_LogErrors        = extractValue($par_Str, 'log_errors');
    $l_MQGPC            = extractValue($par_Str, 'magic_quotes_gpc');
    $l_MQRT             = extractValue($par_Str, 'magic_quotes_runtime');
    $l_OpenBaseDir      = extractValue($par_Str, 'open_basedir');
    $l_RegisterGlobals  = extractValue($par_Str, 'register_globals');
    $l_SafeMode         = extractValue($par_Str, 'safe_mode');

    $l_DisabledFunction = ($l_DisabledFunction == '' ? '-?-' : $l_DisabledFunction);
    $l_OpenBaseDir      = ($l_OpenBaseDir == '' ? '-?-' : $l_OpenBaseDir);

    $l_Result = '<div class="title">' . AI_STR_008 . ': ' . phpversion() . '</div>';
    $l_Result .= 'System Version: <span class="php_ok">' . $l_PhpInfoSystem . '</span><br/>';
    $l_Result .= 'PHP API: <span class="php_ok">' . $l_PhpPHPAPI . '</span><br/>';
    $l_Result .= 'allow_url_fopen: <span class="php_' . ($l_AllowUrlFOpen == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlFOpen . '</span><br/>';
    $l_Result .= 'allow_url_include: <span class="php_' . ($l_AllowUrlInclude == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlInclude . '</span><br/>';
    $l_Result .= 'disable_functions: <span class="php_' . ($l_DisabledFunction == '-?-' ? 'bad' : 'ok') . '">' . $l_DisabledFunction . '</span><br/>';
    $l_Result .= 'display_errors: <span class="php_' . ($l_DisplayErrors == 'On' ? 'ok' : 'bad') . '">' . $l_DisplayErrors . '</span><br/>';
    $l_Result .= 'error_reporting: <span class="php_ok">' . $l_ErrorReporting . '</span><br/>';
    $l_Result .= 'expose_php: <span class="php_' . ($l_ExposePHP == 'On' ? 'bad' : 'ok') . '">' . $l_ExposePHP . '</span><br/>';
    $l_Result .= 'log_errors: <span class="php_' . ($l_LogErrors == 'On' ? 'ok' : 'bad') . '">' . $l_LogErrors . '</span><br/>';
    $l_Result .= 'magic_quotes_gpc: <span class="php_' . ($l_MQGPC == 'On' ? 'ok' : 'bad') . '">' . $l_MQGPC . '</span><br/>';
    $l_Result .= 'magic_quotes_runtime: <span class="php_' . ($l_MQRT == 'On' ? 'bad' : 'ok') . '">' . $l_MQRT . '</span><br/>';
    $l_Result .= 'register_globals: <span class="php_' . ($l_RegisterGlobals == 'On' ? 'bad' : 'ok') . '">' . $l_RegisterGlobals . '</span><br/>';
    $l_Result .= 'open_basedir: <span class="php_' . ($l_OpenBaseDir == '-?-' ? 'bad' : 'ok') . '">' . $l_OpenBaseDir . '</span><br/>';

    if (phpversion() < '5.3.0') {
        $l_Result .= 'safe_mode (PHP < 5.3.0): <span class="php_' . ($l_SafeMode == 'On' ? 'ok' : 'bad') . '">' . $l_SafeMode . '</span><br/>';
    }

    return $l_Result . '<p>';
}

///////////////////////////////////////////////////////////////////////////
function addSlash($dir) {
    return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

///////////////////////////////////////////////////////////////////////////
function QCR_Debug($par_Str = "") {
    if (!DEBUG_MODE) {
        return;
    }

    $l_MemInfo = ' ';
    if (function_exists('memory_get_usage')) {
        $l_MemInfo .= ' curmem=' . bytes2Human(memory_get_usage());
    }

    if (function_exists('memory_get_peak_usage')) {
        $l_MemInfo .= ' maxmem=' . bytes2Human(memory_get_peak_usage());
    }

    stdOut("\n" . date('H:i:s') . ': ' . $par_Str . $l_MemInfo . "\n");
}


///////////////////////////////////////////////////////////////////////////
function QCR_ScanDirectories($l_RootDir, $vars) {
    global $defaults, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, $g_UnsafeFilesFound, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SensitiveFiles, $g_SuspiciousFiles, $g_ShortListExt, $l_SkipSample;

    static $l_Buffer = '';

    $l_DirCounter          = 0;
    $l_DoorwayFilesCounter = 0;
    $l_SourceDirIndex      = $vars->counter - 1;

    $l_SkipSample = array();

    QCR_Debug('Scan ' . $l_RootDir);

    $l_QuotedSeparator = quotemeta(DIR_SEPARATOR);
    $l_DIRH = @opendir($l_RootDir);
    if ($l_DIRH === false) {
        return;
    }
    while (($l_FileName = readdir($l_DIRH)) !== false) {
            
        if ($l_FileName == '.' || $l_FileName == '..') {
            continue;
        }
        $l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;
        $l_Type = filetype($l_FileName);
            
        if ($l_Type == "link") {
            $vars->symLinks[] = $l_FileName;
            continue;
        } 
        elseif ($l_Type != "file" && $l_Type != "dir") {
            continue;
        }

        $l_Ext   = strtolower(pathinfo($l_FileName, PATHINFO_EXTENSION));
        $l_IsDir = is_dir($l_FileName);
            
        // which files should be scanned
        $l_NeedToScan = SCAN_ALL_FILES || (in_array($l_Ext, $g_SensitiveFiles));

        if (in_array(strtolower($l_Ext), $g_IgnoredExt)) {
            $l_NeedToScan = false;
        }

        // if folder in ignore list
        $l_Skip = false;
        for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
            if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
                if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                    $l_SkipSample[] = $g_DirIgnoreList[$dr];
                } 
                else {
                    $l_Skip       = true;
                    $l_NeedToScan = false;
                }
            }
        }

        if ($l_IsDir) {
            // skip on ignore
            if ($l_Skip) {
                $vars->skippedFolders[] = $l_FileName;
                continue;
            }

            $l_BaseName = basename($l_FileName);

            if (ONE_PASS) {
                $vars->structure['n'][$vars->counter] = $l_FileName . DIR_SEPARATOR;
            } 
            else {
                $l_Buffer .= FilepathEscaper::encodeFilepathByBase64($l_FileName . DIR_SEPARATOR) . "\n";
            }

            $l_DirCounter++;

            if ($l_DirCounter > MAX_ALLOWED_PHP_HTML_IN_DIR) {
                $vars->doorway[]  = $l_SourceDirIndex;
                $l_DirCounter = -655360;
            }

            $vars->counter++;
            $vars->foundTotalDirs++;

            QCR_ScanDirectories($l_FileName, $vars);
        } 
        elseif ($l_NeedToScan) {
            $vars->foundTotalFiles++;
            if (in_array($l_Ext, $g_ShortListExt)) {
                $l_DoorwayFilesCounter++;

                if ($l_DoorwayFilesCounter > MAX_ALLOWED_PHP_HTML_IN_DIR) {
                    $vars->doorway[]           = $l_SourceDirIndex;
                    $l_DoorwayFilesCounter = -655360;
                }
            }

            if (ONE_PASS) {
                QCR_ScanFile($l_FileName, $vars, null, $vars->counter++);
            } 
            else {
                $l_Buffer .= FilepathEscaper::encodeFilepathByBase64($l_FileName) . "\n";
            }

            $vars->counter++;
        }

        if (strlen($l_Buffer) > 32000) {
            file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
            $l_Buffer = '';
        }

    }

    closedir($l_DIRH);

    if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
        file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
        $l_Buffer = '';
    }

}


///////////////////////////////////////////////////////////////////////////
function getFragment($par_Content, $par_Pos) {
//echo "\n *********** --------------------------------------------------------\n";

    $l_MaxChars = MAX_PREVIEW_LEN;

    $par_Content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '~', $par_Content);

    $l_MaxLen   = strlen($par_Content);
    $l_RightPos = min($par_Pos + $l_MaxChars, $l_MaxLen);
    $l_MinPos   = max(0, $par_Pos - $l_MaxChars);

    $l_FoundStart = substr($par_Content, 0, $par_Pos);
    $l_FoundStart = str_replace("\r", '', $l_FoundStart);
    $l_LineNo     = strlen($l_FoundStart) - strlen(str_replace("\n", '', $l_FoundStart)) + 1;

//echo "\nMinPos=" . $l_MinPos . " Pos=" . $par_Pos . " l_RightPos=" . $l_RightPos . "\n";
//var_dump($par_Content);
//echo "\n-----------------------------------------------------\n";


    $l_Res = '__AI_LINE1__' . $l_LineNo . "__AI_LINE2__  " . ($l_MinPos > 0 ? '…' : '') . substr($par_Content, $l_MinPos, $par_Pos - $l_MinPos) . '__AI_MARKER__' . substr($par_Content, $par_Pos, $l_RightPos - $par_Pos - 1);

    $l_Res = makeSafeFn(UnwrapObfu($l_Res));

    $l_Res = str_replace('~', ' ', $l_Res);

    $l_Res = preg_replace('~[\s\t]+~', ' ', $l_Res);

    $l_Res = str_replace('' . '?php', '' . '?php ', $l_Res);

//echo "\nFinal:\n";
//var_dump($l_Res);
//echo "\n-----------------------------------------------------\n";
    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function escapedHexToHex($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr(hexdec($escaped[1]));
}
function escapedOctDec($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr(octdec($escaped[1]));
}
function escapedDec($escaped) {
    $GLOBALS['g_EncObfu']++;
    return chr($escaped[1]);
}

///////////////////////////////////////////////////////////////////////////
if (!defined('T_ML_COMMENT')) {
    define('T_ML_COMMENT', T_COMMENT);
} else {
    define('T_DOC_COMMENT', T_ML_COMMENT);
}

function UnwrapObfu($par_Content) {
    $GLOBALS['g_EncObfu'] = 0;

    $search      = array(
        ' ;',
        ' =',
        ' ,',
        ' .',
        ' (',
        ' )',
        ' {',
        ' }',
        '; ',
        '= ',
        ', ',
        '. ',
        '( ',
        '( ',
        '{ ',
        '} ',
        ' !',
        ' >',
        ' <',
        ' _',
        '_ ',
        '< ',
        '> ',
        ' $',
        ' %',
        '% ',
        '# ',
        ' #',
        '^ ',
        ' ^',
        ' &',
        '& ',
        ' ?',
        '? '
    );
    $replace     = array(
        ';',
        '=',
        ',',
        '.',
        '(',
        ')',
        '{',
        '}',
        ';',
        '=',
        ',',
        '.',
        '(',
        ')',
        '{',
        '}',
        '!',
        '>',
        '<',
        '_',
        '_',
        '<',
        '>',
        '$',
        '%',
        '%',
        '#',
        '#',
        '^',
        '^',
        '&',
        '&',
        '?',
        '?'
    );
    $par_Content = str_replace('@', '', $par_Content);
    $par_Content = preg_replace('~\s+~smi', ' ', $par_Content);
    $par_Content = str_replace($search, $replace, $par_Content);
    $par_Content = preg_replace_callback('~\bchr\(\s*([0-9a-fA-FxX]+)\s*\)~', function($m) {
        return "'" . chr(intval($m[1], 0)) . "'";
    }, $par_Content);

    $par_Content = preg_replace_callback('/\\\\x([a-fA-F0-9]{1,2})/i', 'escapedHexToHex', $par_Content);
    $par_Content = preg_replace_callback('/\\\\([0-9]{1,3})/i', 'escapedOctDec', $par_Content);

    $par_Content = preg_replace('/[\'"]\s*?\.+\s*?[\'"]/smi', '', $par_Content);
    $par_Content = preg_replace('/[\'"]\s*?\++\s*?[\'"]/smi', '', $par_Content);

    $content = str_replace('<?$', '<?php$', $content);
    $content = str_replace('<?php', '<?php ', $content);

    return $par_Content;
}

///////////////////////////////////////////////////////////////////////////
// Unicode BOM is U+FEFF, but after encoded, it will look like this.
define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));

function detect_utf_encoding($text) {
    $first2 = substr($text, 0, 2);
    $first3 = substr($text, 0, 3);
    $first4 = substr($text, 0, 3);

    if ($first3 == UTF8_BOM)
        return 'UTF-8';
    elseif ($first4 == UTF32_BIG_ENDIAN_BOM)
        return 'UTF-32BE';
    elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM)
        return 'UTF-32LE';
    elseif ($first2 == UTF16_BIG_ENDIAN_BOM)
        return 'UTF-16BE';
    elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM)
        return 'UTF-16LE';

    return false;
}

///////////////////////////////////////////////////////////////////////////
function QCR_SearchPHP($src) {
    if (preg_match("/(<\?php[\w\s]{5,})/smi", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
        return $l_Found[0][1];
    }

    if (preg_match("/(<script[^>]*language\s*=\s*)('|\"|)php('|\"|)([^>]*>)/i", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
        return $l_Found[0][1];
    }

    return false;
}


///////////////////////////////////////////////////////////////////////////
function knowUrl($par_URL) {
    global $g_UrlIgnoreList;

    for ($jk = 0; $jk < count($g_UrlIgnoreList); $jk++) {
        if (stripos($par_URL, $g_UrlIgnoreList[$jk]) !== false) {
            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////

function makeSummary($par_Str, $par_Number, $par_Style) {
    return '<tr><td class="' . $par_Style . '" width=400>' . $par_Str . '</td><td class="' . $par_Style . '">' . $par_Number . '</td></tr>';
}

///////////////////////////////////////////////////////////////////////////

function CheckVulnerability($par_Filename, $par_Index, $par_Content, $vars) {
    global $g_CmsListDetector;


    $l_Vuln = array();

    $par_Filename = strtolower($par_Filename);

    if ((strpos($par_Filename, 'libraries/joomla/session/session.php') !== false) && (strpos($par_Content, '&& filter_var($_SERVER[\'HTTP_X_FORWARDED_FOR') === false)) {
        $l_Vuln['id']   = 'RCE : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if ((strpos($par_Filename, 'administrator/components/com_media/helpers/media.php') !== false) && (strpos($par_Content, '$format == \'\' || $format == false ||') === false)) {
        if ($g_CmsListDetector->isCms(CmsVersionDetector::CMS_JOOMLA, '1.5')) {
            $l_Vuln['id']   = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'joomla/filesystem/file.php') !== false) && (strpos($par_Content, '$file = rtrim($file, \'.\');') === false)) {
        if ($g_CmsListDetector->isCms(CmsVersionDetector::CMS_JOOMLA, '1.5')) {
            $l_Vuln['id']   = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'editor/filemanager/upload/test.html') !== false) || (stripos($par_Filename, 'editor/filemanager/browser/default/connectors/php/') !== false) || (stripos($par_Filename, 'editor/filemanager/connectors/uploadtest.html') !== false) || (strpos($par_Filename, 'editor/filemanager/browser/default/connectors/test.html') !== false)) {
        $l_Vuln['id']   = 'AFU : FCKEDITOR : http://www.exploit-db.com/exploits/17644/ & /exploit/249';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if ((strpos($par_Filename, 'inc_php/image_view.class.php') !== false) || (strpos($par_Filename, '/inc_php/framework/image_view.class.php') !== false)) {
        if (strpos($par_Content, 'showImageByID') === false) {
            $l_Vuln['id']   = 'AFU : REVSLIDER : http://www.exploit-db.com/exploits/35385/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'elfinder/php/connector.php') !== false) || (strpos($par_Filename, 'elfinder/elfinder.') !== false)) {
        $l_Vuln['id']   = 'AFU : elFinder';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;
        return true;
    }

    if (strpos($par_Filename, 'includes/database/database.inc') !== false) {
        if (strpos($par_Content, 'foreach ($data as $i => $value)') !== false) {
            $l_Vuln['id']   = 'SQLI : DRUPAL : CVE-2014-3704';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'engine/classes/min/index.php') !== false) {
        if (strpos($par_Content, 'tr_replace(chr(0)') === false) {
            $l_Vuln['id']   = 'AFD : MINIFY : CVE-2013-6619';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if ((strpos($par_Filename, 'timthumb.php') !== false) || (strpos($par_Filename, 'thumb.php') !== false) || (strpos($par_Filename, 'cache.php') !== false) || (strpos($par_Filename, '_img.php') !== false)) {
        if (strpos($par_Content, 'code.google.com/p/timthumb') !== false && strpos($par_Content, '2.8.14') === false) {
            $l_Vuln['id']   = 'RCE : TIMTHUMB : CVE-2011-4106,CVE-2014-4663';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'components/com_rsform/helpers/rsform.php') !== false) {
        if (preg_match('~define\s*\(\s*\'_rsform_version\'\s*,\s*\'([^\']+)\'\s*\)\s*;~msi', $par_Content, $version)) {
            $version = $version[1];
            if (version_compare($version, '1.5.2') !== 1) {
                $l_Vuln['id']   = 'RCE : RSFORM : rsform.php, LINE 1605';
                $l_Vuln['ndx']  = $par_Index;
                $vars->vulnerable[] = $l_Vuln;
                return true;
            }
        }
        return false;
    }


    if (strpos($par_Filename, 'fancybox-for-wordpress/fancybox.php') !== false) {
        if (strpos($par_Content, '\'reset\' == $_REQUEST[\'action\']') !== false) {
            $l_Vuln['id']   = 'CODE INJECTION : FANCYBOX';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'cherry-plugin/admin/import-export/upload.php') !== false) {
        if (strpos($par_Content, 'verify nonce') === false) {
            $l_Vuln['id']   = 'AFU : Cherry Plugin';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'tiny_mce/plugins/tinybrowser/tinybrowser.php') !== false) {
        $l_Vuln['id']   = 'AFU : TINYMCE : http://www.exploit-db.com/exploits/9296/';
        $l_Vuln['ndx']  = $par_Index;
        $vars->vulnerable[] = $l_Vuln;

        return true;
    }

    if (strpos($par_Filename, '/bx_1c_import.php') !== false) {
        if (strpos($par_Content, '$_GET[\'action\']=="getfiles"') !== false) {
            $l_Vuln['id']   = 'AFD : https://habrahabr.ru/company/dsec/blog/326166/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;

            return true;
        }
    }

    if (strpos($par_Filename, 'scripts/setup.php') !== false) {
        if (strpos($par_Content, 'PMA_Config') !== false) {
            $l_Vuln['id']   = 'CODE INJECTION : PHPMYADMIN : http://1337day.com/exploit/5334';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, '/uploadify.php') !== false) {
        if (strpos($par_Content, 'move_uploaded_file($tempFile,$targetFile') !== false) {
            $l_Vuln['id']   = 'AFU : UPLOADIFY : CVE: 2012-1153';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'com_adsmanager/controller.php') !== false) {
        if (strpos($par_Content, 'move_uploaded_file($file[\'tmp_name\'], $tempPath.\'/\'.basename($file[') !== false) {
            $l_Vuln['id']   = 'AFU : https://revisium.com/ru/blog/adsmanager_afu.html';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'wp-content/plugins/wp-mobile-detector/resize.php') !== false) {
        if (strpos($par_Content, 'file_put_contents($path, file_get_contents($_REQUEST[\'src\']));') !== false) {
            $l_Vuln['id']   = 'AFU : https://www.pluginvulnerabilities.com/2016/05/31/aribitrary-file-upload-vulnerability-in-wp-mobile-detector/';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }


    if (strpos($par_Filename, 'core/lib/drupal.php') !== false) {
        $version = '';
        if (preg_match('|VERSION\s*=\s*\'(8\.\d+\.\d+)\'|smi', $par_Content, $tmp_ver)) {
            $version = $tmp_ver[1];
        }

        if (($version !== '') && (version_compare($version, '8.5.1', '<'))) {
            $l_Vuln['id']   = 'Drupageddon 2 : SA-CORE-2018–002';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }


        return false;
    }

    if (strpos($par_Filename, 'changelog.txt') !== false) {
        $version = '';
        if (preg_match('|Drupal\s+(7\.\d+),|smi', $par_Content, $tmp_ver)) {
            $version = $tmp_ver[1];
        }

        if (($version !== '') && (version_compare($version, '7.58', '<'))) {
            $l_Vuln['id']   = 'Drupageddon 2 : SA-CORE-2018–002';
            $l_Vuln['ndx']  = $par_Index;
            $vars->vulnerable[] = $l_Vuln;
            return true;
        }

        return false;
    }

    if (strpos($par_Filename, 'phpmailer.php') !== false) {
        if (strpos($par_Content, 'PHPMailer') !== false) {
            $l_Found = preg_match('~Version:\s*(\d+)\.(\d+)\.(\d+)~', $par_Content, $l_Match);

            if ($l_Found) {
                $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];

                if ($l_Version < 2520) {
                    $l_Found = false;
                }
            }

            if (!$l_Found) {

                $l_Found = preg_match('~Version\s*=\s*\'(\d+)\.*(\d+)\.(\d+)~i', $par_Content, $l_Match);
                if ($l_Found) {
                    $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];
                    if ($l_Version < 5220) {
                        $l_Found = false;
                    }
                }
            }


            if (!$l_Found) {
                $l_Vuln['id']   = 'RCE : CVE-2016-10045, CVE-2016-10031';
                $l_Vuln['ndx']  = $par_Index;
                $vars->vulnerable[] = $l_Vuln;
                return true;
            }
        }

        return false;
    }
}

///////////////////////////////////////////////////////////////////////////
function CloudAssitedFilter($files_list, &$vars)
{
    $black_files = [];
    $white_files = [];
    try {
        $car                = Factory::instance()->create(CloudAssistedRequest::class, [CLOUD_ASSIST_TOKEN]);
        $cloud_assist_files = new CloudAssistedFiles($car, $files_list);
        $white_files        = $cloud_assist_files->getWhiteList();
        $black_files        = $cloud_assist_files->getBlackList();
        unset($cloud_assist_files);
    }
    catch (\Exception $e) {
        QCR_Debug($e->getMessage());
    }
    $vars->blackFiles = array_merge($vars->blackFiles, $black_files);
    return array_diff($files_list, array_keys($black_files), array_keys($white_files));
}

///////////////////////////////////////////////////////////////////////////
function QCR_GoScan($s_file, $vars, $callback = null, $base64_encoded = true, $skip_first_line = false)
{
    QCR_Debug('QCR_GoScan ');
    try {
        $i = 0;
        if (defined('CLOUD_ASSIST_TOKEN')) {
            $files_for_scan = [];
            foreach ($s_file as $index => $filepath_encoded) {
                if ($skip_first_line && $index == 0) {
                    $i = 1;
                    continue;
                }
                $filepath = $base64_encoded ? FilepathEscaper::decodeFilepathByBase64($filepath_encoded) : $filepath_encoded;
                $filepath = trim($filepath);
                if (substr($filepath, -1) == DIR_SEPARATOR) {
                    QCR_ScanFile($filepath, $vars, $callback, $i++);
                    continue;
                }
                $filesize = filesize($filepath);
                if (isFileTooBigForCloudscan($filesize)) {
                    QCR_ScanFile($filepath, $vars, $callback, $i++);
                    continue;
                }
                $files_for_scan[] = $filepath;
            }

            if (defined('RAPID_ACCOUNT_SCAN')) {
                $storage = Factory::instance()->create(RapidScanStorage::class, [RAPID_ACCOUNT_SCAN]);
                /** @var RapidAccountScan $scanner */
                $scanner = Factory::instance()->create(RapidAccountScan::class, [$storage, &$vars, $i]);
                $scanner->scan($files_for_scan, $vars, constant('RapidAccountScan::RESCAN_' . RAPID_ACCOUNT_SCAN_TYPE));
                if ($scanner->getStrError()) {
                    QCR_Debug('Rapid scan log: ' . $scanner->getStrError());
                }
                $vars->rescanCount += $scanner->getRescanCount();
            } else {
                $scan_bufer_files = function ($files_list, &$i) use ($callback, $vars) {
                    $files_to_scan = CloudAssitedFilter($files_list, $vars);
                    foreach ($files_to_scan as $filepath) {
                        QCR_ScanFile($filepath, $vars, $callback, $i++);
                    }
                };
                $files_bufer = [];
                foreach ($files_for_scan as $l_Filename) {
                    $files_bufer[] = $l_Filename;
                    if (count($files_bufer) >= CLOUD_ASSIST_LIMIT) {
                        $scan_bufer_files($files_bufer, $i);
                        $files_bufer = [];
                    }
                }
                if (count($files_bufer)) {
                    $scan_bufer_files($files_bufer, $i);
                }
                unset($files_bufer);
            }
        } else {
            foreach ($s_file as $index => $l_Filename) {
                if ($skip_first_line && $index == 0) {
                    $i = 1;
                    continue;
                }
                $l_Filename = $base64_encoded ? FilepathEscaper::decodeFilepathByBase64($l_Filename) : $l_Filename;
                $l_Filename = trim($l_Filename);
                QCR_ScanFile($l_Filename, $vars, $callback, $i++);
            }
        }
    } catch (Exception $e) {
        QCR_Debug($e->getMessage());
    }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ScanFile($l_Filename, $vars, $callback = null, $i = 0, $show_progress = true)
{
    static $_files_and_ignored = 0;
    
    $return = array(RapidScanStorageRecord::RX_GOOD, '', '');

    $g_Content = '';
    $vars->crc = 0;

    $l_CriticalDetected = false;
    $l_Stat             = stat($l_Filename);

    if (substr($l_Filename, -1) == DIR_SEPARATOR) {
        // FOLDER
        $vars->structure['n'][$i] = $l_Filename;
        $vars->totalFolder++;
        printProgress($_files_and_ignored, $l_Filename, $vars);

        return null;
    }

    QCR_Debug('Scan file ' . $l_Filename);
    if ($show_progress) {
        printProgress(++$_files_and_ignored, $l_Filename, $vars);
    }

    $fd = @fopen($l_Filename, 'r');
    $firstFourBytes = @fread($fd, 4);
    @fclose($fd);

    if ($firstFourBytes === chr(127) . 'ELF') {
        if(defined('USE_HEURISTICS') || defined('USE_HEURISTICS_SUSPICIOUS')) {
            $vars->crc = sha1_file($l_Filename);
            AddResult($l_Filename, $i, $vars, $g_Content);

            if (defined('USE_HEURISTICS')) {
                $vars->criticalPHP[] = $i;
                $vars->criticalPHPFragment[] = 'SMW-HEUR-ELF';
                $vars->criticalPHPSig[] = 'SMW-HEUR-ELF';
            }

            if (defined('USE_HEURISTICS_SUSPICIOUS')) {
                $vars->warningPHP[] = $i;
                $vars->warningPHPFragment[] = 'SMW-HEUR-ELF';
                $vars->warningPHPSig[] = 'SMW-HEUR-ELF';
            }

            $return = array(RapidScanStorageRecord::HEURISTIC, 'SMW-HEUR-ELF', 'SMW-HEUR-ELF');

            return $return;
        }

        return null;
    }

    // FILE
    $is_too_big = isFileTooBigForScanWithSignatures($l_Stat['size']);
    $hash = sha1_file($l_Filename);
    if (check_binmalware($hash, $vars)) {
        $vars->totalFiles++;

        $vars->crc = $hash;

        AddResult($l_Filename, $i, $vars, $g_Content);

        $vars->criticalPHP[] = $i;
        $vars->criticalPHPFragment[] = "BIN-" . $vars->crc;
        $vars->criticalPHPSig[] = "bin_" . $vars->crc;
        $return = array(RapidScanStorageRecord::RX_MALWARE, "bin_" . $vars->crc, "BIN-" . $vars->crc);
    } elseif (!MAX_SIZE_SCAN_BYTES && $is_too_big) {
        $vars->bigFiles[] = $i;

        if (function_exists('aibolit_onBigFile')) {
            aibolit_onBigFile($l_Filename);
        }

        AddResult($l_Filename, $i, $vars, $g_Content);

        /** @var CriticalFileSpecification $criticalFileSpecification */
        $criticalFileSpecification = Factory::instance()->create(CriticalFileSpecification::class);
        if ((!AI_HOSTER) && $criticalFileSpecification->satisfiedBy($l_Filename)) {
            $vars->criticalPHP[]         = $i;
            $vars->criticalPHPFragment[] = "BIG FILE. SKIPPED.";
            $vars->criticalPHPSig[]      = "big_1";
        }
    } else {
        $vars->totalFiles++;

        $l_TSStartScan = microtime(true);

        $l_Ext = strtolower(pathinfo($l_Filename, PATHINFO_EXTENSION));
        $l_Content = '';

        if (filetype($l_Filename) == 'file') {
            if ($is_too_big && MAX_SIZE_SCAN_BYTES) {
                $handle     = @fopen($l_Filename, 'r');
                $l_Content  = @fread($handle, MAX_SIZE_SCAN_BYTES);
                @fclose($handle);
            } else {
                $l_Content  = @file_get_contents($l_Filename);
            }
            $l_Unwrapped = @php_strip_whitespace($l_Filename);
            $g_Content = $l_Content;
        }

        if (($l_Content == '' || $l_Unwrapped == '') && $l_Stat['size'] > 0) {
            $vars->notRead[] = $i;
            if (function_exists('aibolit_onReadError')) {
                aibolit_onReadError($l_Filename, 'io');
            }
            $return = array(RapidScanStorageRecord::CONFLICT, 'notread','');
            AddResult('[io] ' . $l_Filename, $i, $vars, $g_Content);
            return $return;
        }

        // ignore itself
        if (strpos($l_Content, '85ed2fc1e1e5b7d2cd405f96ca358030') !== false) {
            return false;
        }

        $vars->crc = _hash_($l_Unwrapped);

        $l_UnicodeContent = detect_utf_encoding($l_Content);
        //$l_Unwrapped = $l_Content;

        // check vulnerability in files
        $l_CriticalDetected = CheckVulnerability($l_Filename, $i, $l_Content, $vars);

        if ($l_UnicodeContent !== false) {
            if (function_exists('iconv')) {
                $l_Unwrapped = iconv($l_UnicodeContent, "CP1251//IGNORE", $l_Unwrapped);
            } else {
                $vars->notRead[] = $i;
                if (function_exists('aibolit_onReadError')) {
                    aibolit_onReadError($l_Filename, 'ec');
                }
                $return = array(RapidScanStorageRecord::CONFLICT, 'no_iconv', '');
                AddResult('[ec] ' . $l_Filename, $i, $vars, $g_Content);
            }
        }

        // critical
        $g_SkipNextCheck = false;

        if ((!AI_HOSTER) || AI_DEOBFUSCATE) {
            $l_DeobfObj = new Deobfuscator($l_Unwrapped);
            $l_DeobfType = $l_DeobfObj->getObfuscateType($l_Unwrapped);
        }

        if ($l_DeobfType != '') {
            $hangs = 0;
            while($l_DeobfObj->getObfuscateType($l_Unwrapped)!=='' && $hangs < 10) {
                $l_Unwrapped = $l_DeobfObj->deobfuscate();
                $l_DeobfObj = new Deobfuscator($l_Unwrapped);
                $hangs++;
            }
            $g_SkipNextCheck = checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType);
        } else {
            if (DEBUG_MODE) {
                stdOut("\n...... NOT OBFUSCATED\n");
            }
        }

        $l_Unwrapped = UnwrapObfu($l_Unwrapped);

        if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Unwrapped, $l_Pos, $l_SigId)) {
            if ($l_Ext == 'js') {
                $vars->criticalJS[]         = $i;
                $vars->criticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->criticalJSSig[]      = $l_SigId;
            } else {
                $vars->criticalPHP[]         = $i;
                $vars->criticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->criticalPHPSig[]      = $l_SigId;
            }
            $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
            $g_SkipNextCheck = true;
        } else {
            if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Content, $l_Pos, $l_SigId)) {
                if ($l_Ext == 'js') {
                    $vars->criticalJS[]         = $i;
                    $vars->criticalJSFragment[] = getFragment($l_Content, $l_Pos);
                    $vars->criticalJSSig[]      = $l_SigId;
                } else {
                    $vars->criticalPHP[]         = $i;
                    $vars->criticalPHPFragment[] = getFragment($l_Content, $l_Pos);
                    $vars->criticalPHPSig[]      = $l_SigId;
                }
                $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Content, $l_Pos));
                $g_SkipNextCheck = true;
            }
        }

        $l_TypeDe = 0;

        // critical JS
        if (!$g_SkipNextCheck) {
            $l_Pos = CriticalJS($l_Filename, $i, $l_Unwrapped, $l_SigId);
            if ($l_Pos !== false) {
                if ($l_Ext == 'js') {
                    $vars->criticalJS[]         = $i;
                    $vars->criticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
                    $vars->criticalJSSig[]      = $l_SigId;
                } else {
                    $vars->criticalPHP[]         = $i;
                    $vars->criticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                    $vars->criticalPHPSig[]      = $l_SigId;
                }
                $return = array(RapidScanStorageRecord::RX_MALWARE, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
                $g_SkipNextCheck = true;
            }
        }

        // warnings (suspicious)
        if (!$g_SkipNextCheck) {
            $l_Pos = WarningPHP($l_Filename, $i, $l_Unwrapped, $l_SigId);
            if ($l_Pos !== false) {
                $vars->warningPHP[]         = $i;
                $vars->warningPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $vars->warningPHPSig[]      = $l_SigId;

                $return = array(RapidScanStorageRecord::RX_SUSPICIOUS, $l_SigId, getFragment($l_Unwrapped, $l_Pos)) ;
                $g_SkipNextCheck = true;
            }
        }

        // phishing
        if (!$g_SkipNextCheck) {
            $l_Pos = Phishing($l_Filename, $i, $l_Unwrapped, $l_SigId, $vars);
            if ($l_Pos === false) {
                $l_Pos = Phishing($l_Filename, $i, $l_Content, $l_SigId, $vars);
            }

            if ($l_Pos !== false) {
                $vars->phishing[]            = $i;
                $vars->phishingFragment[]    = getFragment($l_Unwrapped, $l_Pos);
                $vars->phishingSigFragment[] = $l_SigId;

                $return = array(RapidScanStorageRecord::RX_SUSPICIOUS, $l_SigId, getFragment($l_Unwrapped, $l_Pos));
                $g_SkipNextCheck         = true;
            }
        }

        if (!$g_SkipNextCheck) {
            // warnings
            $l_Pos = '';

            // adware
            if (Adware($l_Filename, $l_Unwrapped, $l_Pos)) {
                $vars->adwareList[]         = $i;
                $vars->adwareListFragment[] = getFragment($l_Unwrapped, $l_Pos);
                $l_CriticalDetected     = true;
            }

            // articles
            if (stripos($l_Filename, 'article_index')) {
                $vars->adwareList[]     = $i;
                $l_CriticalDetected = true;
            }
        }
    } // end of if (!$g_SkipNextCheck) {

    //printProgress(++$_files_and_ignored, $l_Filename);
    delayWithCallback(SCAN_DELAY, $callback);
    $l_TSEndScan = microtime(true);
    if ($l_TSEndScan - $l_TSStartScan >= 0.5) {
        delayWithCallback(SCAN_DELAY, $callback);
    }

    if ($g_SkipNextCheck || $l_CriticalDetected) {
        AddResult($l_Filename, $i, $vars, $g_Content);
    }

    unset($l_Unwrapped);
    unset($l_Content);

    return $return;
}

function callCallback($callback)
{
    if ($callback !== null) {
        call_user_func($callback);
    }
}

function delayWithCallback($delay, $callback)
{
    $delay = $delay * 1000;
    callCallback($callback);
    while ($delay > 500000) {
        $delay -= 500000;
        usleep(500000);
        callCallback($callback);
    }
    usleep($delay);
    callCallback($callback);
}

function AddResult($l_Filename, $i, $vars, $g_Content = '')
{
    $l_Stat                 = stat($l_Filename);
    if (!isFileTooBigForScanWithSignatures($l_Stat['size']) && $g_Content == '') {
        $g_Content = file_get_contents($l_Filename);
    }
    $vars->structure['n'][$i]   = $l_Filename;
    $vars->structure['s'][$i]   = $l_Stat['size'];
    $vars->structure['c'][$i]   = $l_Stat['ctime'];
    $vars->structure['m'][$i]   = $l_Stat['mtime'];
    $vars->structure['e'][$i]   = time();
    $vars->structure['crc'][$i] = $vars->crc;

    if ($g_Content !== '') {
        $vars->structure['sha256'][$i] = hash('sha256', $g_Content);
        $g_Content = '';
    }
}

///////////////////////////////////////////////////////////////////////////
function WarningPHP($l_FN, $l_Index, $l_Content, &$l_SigId) {
    global $g_SusDB, $g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;

    if (AI_EXTRA_WARN) {
        foreach ($g_SusDB as $l_Item) {
            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);
                    return $l_Pos;
                }
            }
        }
    }
    return false;

}

///////////////////////////////////////////////////////////////////////////
function Adware($l_FN, $l_Content, &$l_Pos) {
    global $g_AdwareSig;

    $l_Res = false;

    foreach ($g_AdwareSig as $l_Item) {
        $offset = 0;
        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos = $l_Found[0][1];
                return true;
            }

            $offset = $l_Found[0][1] + 1;
        }
    }

    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CheckException(&$l_Content, &$l_Found) {
    global $g_ExceptFlex, $gX_FlexDBShe, $gXX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;
    $l_FoundStrPlus = substr($l_Content, max($l_Found[0][1] - 10, 0), 70);

    foreach ($g_ExceptFlex as $l_ExceptItem) {
        if (@preg_match('~' . $l_ExceptItem . '~smi', $l_FoundStrPlus, $l_Detected)) {
            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
function Phishing($l_FN, $l_Index, $l_Content, &$l_SigId, $vars) {
    global $g_PhishFiles, $g_PhishEntries, $g_PhishingSig;

    $l_Res = false;

    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        foreach ($g_PhishFiles as $l_Ext) {
            if (strpos($l_FN, $l_Ext) !== false) {
                $l_SkipCheck = false;
                break;
            }
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_PhishEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }

    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped phs file, not critical.\n";
        }

        return false;
    }

    foreach ($g_PhishingSig as $l_Item) {
        $offset = 0;
        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "Phis: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return $l_Pos;
            }
            $offset = $l_Found[0][1] + 1;

        }
    }

    return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CriticalJS($l_FN, $l_Index, $l_Content, &$l_SigId) {
    global $g_JSVirSig, $gX_JSVirSig, $g_VirusFiles, $g_VirusEntries, $g_RegExpStat;

    $l_Res = false;

    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        foreach ($g_VirusFiles as $l_Ext) {
            if (strpos($l_FN, $l_Ext) !== false) {
                $l_SkipCheck = false;
                break;
            }
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_VirusEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }

    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped js file, not critical.\n";
        }

        return false;
    }


    foreach ($g_JSVirSig as $l_Item) {
        $offset = 0;
        if (DEBUG_PERFORMANCE) {
            $stat_start = microtime(true);
        }

        while (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {

            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "JS: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return $l_Pos;
            }

            $offset = $l_Found[0][1] + 1;

        }

        if (DEBUG_PERFORMANCE) {
            $stat_stop = microtime(true);
            $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
        }

    }

    if (AI_EXPERT > 1) {
        foreach ($gX_JSVirSig as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    //$l_SigId = myCheckSum($l_Item);
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "JS PARA: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return $l_Pos;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    return $l_Res;
}

////////////////////////////////////////////////////////////////////////////
define('SUSP_MTIME', 1); // suspicious mtime (greater than ctime)
define('SUSP_PERM', 2); // suspicious permissions 
define('SUSP_PHP_IN_UPLOAD', 3); // suspicious .php file in upload or image folder 

function get_descr_heur($type) {
    switch ($type) {
        case SUSP_MTIME:
            return AI_STR_077;
        case SUSP_PERM:
            return AI_STR_078;
        case SUSP_PHP_IN_UPLOAD:
            return AI_STR_079;
    }

    return "---";
}

///////////////////////////////////////////////////////////////////////////
function CriticalPHP($l_FN, $l_Index, $l_Content, &$l_Pos, &$l_SigId) {
    global $g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment, $g_CriticalEntries, $g_RegExpStat;

    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;

    if ($l_SkipCheck) {
        /** @var CriticalFileSpecification $criticalFileSpecification */
        $criticalFileSpecification = Factory::instance()->create(CriticalFileSpecification::class);

        if ($criticalFileSpecification->satisfiedBy($l_FN) && (strpos($l_FN, '.js') === false)) {
            $l_SkipCheck = false;
        }
    }

    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_CriticalEntries . '~smiS', $l_Content, $l_Found)) {
        $l_SkipCheck = false;
    }


    // if not critical - skip it 
    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
            echo "Skipped file, not critical.\n";
        }

        return false;
    }

    foreach ($g_FlexDBShe as $l_Item) {
        $offset = 0;

        if (DEBUG_PERFORMANCE) {
            $stat_start = microtime(true);
        }

        while (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
            if (!CheckException($l_Content, $l_Found)) {
                $l_Pos   = $l_Found[0][1];
                //$l_SigId = myCheckSum($l_Item);
                $l_SigId = getSigId($l_Found);

                if (DEBUG_MODE) {
                    echo "CRIT 1: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return true;
            }

            $offset = $l_Found[0][1] + 1;

        }

        if (DEBUG_PERFORMANCE) {
            $stat_stop = microtime(true);
            $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
        }

    }

    if (AI_EXPERT > 0) {
        foreach ($gX_FlexDBShe as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "CRIT 3: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return true;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    if (AI_EXPERT > 1) {
        foreach ($gXX_FlexDBShe as $l_Item) {
            if (DEBUG_PERFORMANCE) {
                $stat_start = microtime(true);
            }

            if (preg_match('~' . $l_Item . '~smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
                if (!CheckException($l_Content, $l_Found)) {
                    $l_Pos   = $l_Found[0][1];
                    $l_SigId = getSigId($l_Found);

                    if (DEBUG_MODE) {
                        echo "CRIT 2: $l_FN matched [$l_Item] in $l_Pos\n";
                    }

                    return true;
                }
            }

            if (DEBUG_PERFORMANCE) {
                $stat_stop = microtime(true);
                $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
            }

        }
    }

    $l_Content_lo = strtolower($l_Content);

    foreach ($g_DBShe as $l_Item) {
        $l_Pos = strpos($l_Content_lo, $l_Item);
        if ($l_Pos !== false) {
            $l_SigId = myCheckSum($l_Item);

            if (DEBUG_MODE) {
                echo "CRIT 4: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    if (AI_EXPERT > 0) {
        foreach ($gX_DBShe as $l_Item) {
            $l_Pos = strpos($l_Content_lo, $l_Item);
            if ($l_Pos !== false) {
                $l_SigId = myCheckSum($l_Item);

                if (DEBUG_MODE) {
                    echo "CRIT 5: $l_FN matched [$l_Item] in $l_Pos\n";
                }

                return true;
            }
        }
    }

    if (AI_HOSTER)
        return false;

    if (AI_EXPERT > 0) {
        if ((strpos($l_Content, 'GIF89') === 0) && (strpos($l_FN, '.php') !== false)) {
            $l_Pos = 0;

            if (DEBUG_MODE) {
                echo "CRIT 6: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    // detect uploaders / droppers
    if (AI_EXPERT > 1) {
        $l_Found = null;
        if ((filesize($l_FN) < 2048) && (strpos($l_FN, '.ph') !== false) && ((($l_Pos = strpos($l_Content, 'multipart/form-data')) > 0) || (($l_Pos = strpos($l_Content, '$_FILE[') > 0)) || (($l_Pos = strpos($l_Content, 'move_uploaded_file')) > 0) || (preg_match('|\bcopy\s*\(|smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)))) {
            if ($l_Found != null) {
                $l_Pos = $l_Found[0][1];
            }
            if (DEBUG_MODE) {
                echo "CRIT 7: $l_FN matched [$l_Item] in $l_Pos\n";
            }

            return true;
        }
    }

    return false;
}

///////////////////////////////////////////////////////////////////////////
if (!isCli()) {
    header('Content-type: text/html; charset=utf-8');
}

if (!isCli()) {

    $l_PassOK = false;
    if (strlen(PASS) > 8) {
        $l_PassOK = true;
    }

    if ($l_PassOK && preg_match('|[0-9]|', PASS, $l_Found) && preg_match('|[A-Z]|', PASS, $l_Found) && preg_match('|[a-z]|', PASS, $l_Found)) {
        $l_PassOK = true;
    }

    if (!$l_PassOK) {
        echo sprintf(AI_STR_009, generatePassword());
        exit;
    }

    if (isset($_GET['fn']) && ($_GET['ph'] == crc32(PASS))) {
        printFile();
        exit;
    }

    if ($_GET['p'] != PASS) {
        $generated_pass = generatePassword();
        echo sprintf(AI_STR_010, $generated_pass, $generated_pass);
        exit;
    }
}

if (!is_readable(ROOT_PATH)) {
    echo AI_STR_011;
    exit;
}

if (isCli()) {
    if (defined('REPORT_PATH') AND REPORT_PATH) {
        if (!is_writable(REPORT_PATH)) {
            die2("\nCannot write report. Report dir " . REPORT_PATH . " is not writable.");
        }

        else if (!REPORT_FILE) {
            die2("\nCannot write report. Report filename is empty.");
        }

        else if (($file = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE) AND is_file($file) AND !is_writable($file)) {
            die2("\nCannot write report. Report file '$file' exists but is not writable.");
        }
    }
}


// detect version CMS
$g_KnownCMS        = array();
$tmp_cms           = array();
$g_CmsListDetector = new CmsVersionDetector(ROOT_PATH);
$l_CmsDetectedNum  = $g_CmsListDetector->getCmsNumber();
for ($tt = 0; $tt < $l_CmsDetectedNum; $tt++) {
    $vars->CMS[]                                              = $g_CmsListDetector->getCmsName($tt) . ' v' . makeSafeFn($g_CmsListDetector->getCmsVersion($tt));
    $tmp_cms[strtolower($g_CmsListDetector->getCmsName($tt))] = 1;
}

if (count($tmp_cms) > 0) {
    $g_KnownCMS = array_keys($tmp_cms);
    $len        = count($g_KnownCMS);
    for ($i = 0; $i < $len; $i++) {
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_WORDPRESS))
            $g_KnownCMS[] = 'wp';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_WEBASYST))
            $g_KnownCMS[] = 'shopscript';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_IPB))
            $g_KnownCMS[] = 'ipb';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_DLE))
            $g_KnownCMS[] = 'dle';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_INSTANTCMS))
            $g_KnownCMS[] = 'instantcms';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_SHOPSCRIPT))
            $g_KnownCMS[] = 'shopscript';
        if ($g_KnownCMS[$i] == strtolower(CmsVersionDetector::CMS_DRUPAL))
            $g_KnownCMS[] = 'drupal';
    }
}


$g_DirIgnoreList = array();
$g_IgnoreList    = array();
$g_UrlIgnoreList = array();
$g_KnownList     = array();

$l_IgnoreFilename    = $g_AiBolitAbsolutePath . '/.aignore';
$l_DirIgnoreFilename = $g_AiBolitAbsolutePath . '/.adirignore';
$l_UrlIgnoreFilename = $g_AiBolitAbsolutePath . '/.aurlignore';

if (file_exists($l_IgnoreFilename)) {
    $l_IgnoreListRaw = file($l_IgnoreFilename);
    for ($i = 0; $i < count($l_IgnoreListRaw); $i++) {
        $g_IgnoreList[] = explode("\t", trim($l_IgnoreListRaw[$i]));
    }
    unset($l_IgnoreListRaw);
}

if (file_exists($l_DirIgnoreFilename)) {
    $g_DirIgnoreList = file($l_DirIgnoreFilename);

    for ($i = 0; $i < count($g_DirIgnoreList); $i++) {
        $g_DirIgnoreList[$i] = trim($g_DirIgnoreList[$i]);
    }
}

if (file_exists($l_UrlIgnoreFilename)) {
    $g_UrlIgnoreList = file($l_UrlIgnoreFilename);

    for ($i = 0; $i < count($g_UrlIgnoreList); $i++) {
        $g_UrlIgnoreList[$i] = trim($g_UrlIgnoreList[$i]);
    }
}


$l_SkipMask = array(
    '/template_\w{32}.css',
    '/cache/templates/.{1,150}\.tpl\.php',
    '/system/cache/templates_c/\w{1,40}\.php',
    '/assets/cache/rss/\w{1,60}',
    '/cache/minify/minify_\w{32}',
    '/cache/page/\w{32}\.php',
    '/cache/object/\w{1,10}/\w{1,10}/\w{1,10}/\w{32}\.php',
    '/cache/wp-cache-\d{32}\.php',
    '/cache/page/\w{32}\.php_expire',
    '/cache/page/\w{32}-cache-page-\w{32}\.php',
    '\w{32}-cache-com_content-\w{32}\.php',
    '\w{32}-cache-mod_custom-\w{32}\.php',
    '\w{32}-cache-mod_templates-\w{32}\.php',
    '\w{32}-cache-_system-\w{32}\.php',
    '/cache/twig/\w{1,32}/\d+/\w{1,100}\.php',
    '/autoptimize/js/autoptimize_\w{32}\.js',
    '/bitrix/cache/\w{32}\.php',
    '/bitrix/cache/.{1,200}/\w{32}\.php',
    '/bitrix/cache/iblock_find/',
    '/bitrix/managed_cache/MYSQL/user_option/[^/]+/',
    '/bitrix/cache/s1/bitrix/catalog\.section/',
    '/bitrix/cache/s1/bitrix/catalog\.element/',
    '/bitrix/cache/s1/bitrix/menu/',
    '/catalog.element/[^/]+/[^/]+/\w{32}\.php',
    '/bitrix/managed\_cache/.{1,150}/\.\w{32}\.php',
    '/core/cache/mgr/smarty/default/.{1,100}\.tpl\.php',
    '/core/cache/resource/web/resources/[0-9]{1,50}\.cache\.php',
    '/smarty/compiled/SC/.{1,100}/%%.{1,200}\.php',
    '/smarty/.{1,150}\.tpl\.php',
    '/smarty/compile/.{1,150}\.tpl\.cache\.php',
    '/files/templates_c/.{1,150}\.html\.php',
    '/uploads/javascript_global/.{1,150}\.js',
    '/assets/cache/rss/\w{32}',
    'сore/cache/resource/web/resources/\d+\.cache\.php',
    '/assets/cache/docid_\d+_\w{32}\.pageCache\.php',
    '/t3-assets/dev/t3/.{1,150}-cache-\w{1,20}-.{1,150}\.php',
    '/t3-assets/js/js-\w{1,30}\.js',
    '/temp/cache/SC/.{1,100}/\.cache\..{1,100}\.php',
    '/tmp/sess\_\w{32}$',
    '/assets/cache/docid\_.{1,100}\.pageCache\.php',
    '/stat/usage\_\w{1,100}\.html',
    '/stat/site\_\w{1,100}\.html',
    '/gallery/item/list/\w{1,100}\.cache\.php',
    '/core/cache/registry/.{1,100}/ext-.{1,100}\.php',
    '/core/cache/resource/shk\_/\w{1,50}\.cache\.php',
    '/cache/\w{1,40}/\w+-cache-\w+-\w{32,40}\.php',
    '/webstat/awstats.{1,150}\.txt',
    '/awstats/awstats.{1,150}\.txt',
    '/awstats/.{1,80}\.pl',
    '/awstats/.{1,80}\.html',
    '/inc/min/styles_\w+\.min\.css',
    '/inc/min/styles_\w+\.min\.js',
    '/logs/error\_log\.',
    '/logs/xferlog\.',
    '/logs/access_log\.',
    '/logs/cron\.',
    '/logs/exceptions/.{1,200}\.log$',
    '/hyper-cache/[^/]{1,50}/[^/]{1,50}/[^/]{1,50}/index\.html',
    '/mail/new/[^,]+,S=[^,]+,W=',
    '/mail/new/[^,]=,S=',
    '/application/logs/\d+/\d+/\d+\.php',
    '/sites/default/files/js/js_\w{32}\.js',
    '/yt-assets/\w{32}\.css',
    '/wp-content/cache/object/\w{1,5}/\w{1,5}/\w{32}\.php',
    '/catalog\.section/\w{1,5}/\w{1,5}/\w{32}\.php',
    '/simpla/design/compiled/[\w\.]{40,60}\.php',
    '/compile/\w{2}/\w{2}/\w{2}/[\w.]{40,80}\.php',
    '/sys-temp/static-cache/[^/]{1,60}/userCache/[\w\./]{40,100}\.php',
    '/session/sess_\w{32}',
    '/webstat/awstats\.[\w\./]{3,100}\.html',
    '/stat/webalizer\.current',
    '/stat/usage_\d+\.html'
);

$l_SkipSample = array();

if (SMART_SCAN) {
    $g_DirIgnoreList = array_merge($g_DirIgnoreList, $l_SkipMask);
}

QCR_Debug();

// Load custom signatures
if (file_exists($g_AiBolitAbsolutePath . "/ai-bolit.sig")) {
    try {
        $s_file = new SplFileObject($g_AiBolitAbsolutePath . "/ai-bolit.sig");
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        foreach ($s_file as $line) {
            $g_FlexDBShe[] = preg_replace('#\G(?:[^~\\\\]+|\\\\.)*+\K~#', '\\~', $line); // escaping ~
        }

        stdOut("Loaded " . $s_file->key() . " signatures from ai-bolit.sig");
        $s_file = null; // file handler is closed
    }
    catch (Exception $e) {
        QCR_Debug("Import ai-bolit.sig " . $e->getMessage());
    }
}

QCR_Debug();

$defaults['skip_ext'] = strtolower(trim($defaults['skip_ext']));
if ($defaults['skip_ext'] != '') {
    $g_IgnoredExt = explode(',', $defaults['skip_ext']);
    for ($i = 0; $i < count($g_IgnoredExt); $i++) {
        $g_IgnoredExt[$i] = trim($g_IgnoredExt[$i]);
    }

    QCR_Debug('Skip files with extensions: ' . implode(',', $g_IgnoredExt));
    stdOut('Skip extensions: ' . implode(',', $g_IgnoredExt));
}

// scan single file
/**
 * @param Variables $vars
 * @param array $g_IgnoredExt
 * @param array $g_DirIgnoreList
 */
function processIntegrity(Variables $vars, array $g_IgnoredExt, array $g_DirIgnoreList)
{
    global $g_IntegrityDB;
// INTEGRITY CHECK
    IMAKE and unlink(INTEGRITY_DB_FILE);
    ICHECK and load_integrity_db();
    QCR_IntegrityCheck(ROOT_PATH, $vars);
    stdOut("Found $vars->foundTotalFiles files in $vars->foundTotalDirs directories.");
    if (IMAKE) {
        exit(0);
    }
    if (ICHECK) {
        $i = $vars->counter;
        $vars->crc = 0;
        $changes = array();
        $ref =& $g_IntegrityDB;
        foreach ($g_IntegrityDB as $l_FileName => $type) {
            unset($g_IntegrityDB[$l_FileName]);
            $l_Ext2 = substr(strstr(basename($l_FileName), '.'), 1);
            if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                continue;
            }
            for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
                if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName,
                        $l_Found)) {
                    continue 2;
                }
            }
            $type = in_array($type, array(
                'added',
                'modified'
            )) ? $type : 'deleted';
            $type .= substr($l_FileName, -1) == '/' ? 'Dirs' : 'Files';
            $changes[$type][] = ++$i;
            AddResult($l_FileName, $i, $vars);
        }
        $vars->foundTotalFiles = count($changes['addedFiles']) + count($changes['modifiedFiles']);
        stdOut("Found changes " . count($changes['modifiedFiles']) . " files and added " . count($changes['addedFiles']) . " files.");
    }
}

if (isset($_GET['2check'])) {
    $options['with-2check'] = 1;
}

$use_doublecheck = isset($options['with-2check']) && file_exists(DOUBLECHECK_FILE);
$use_listingfile = defined('LISTING_FILE');

$listing = false;

if ($use_doublecheck) {
    $listing = DOUBLECHECK_FILE;
} elseif ($use_listingfile) {
    $listing = LISTING_FILE;
}
$base64_encoded = INPUT_FILENAMES_BASE64_ENCODED;

try {
    if (defined('SCAN_FILE')) {
        // scan single file
        $filepath = INPUT_FILENAMES_BASE64_ENCODED ? FilepathEscaper::decodeFilepathByBase64(SCAN_FILE) : SCAN_FILE;
        stdOut("Start scanning file '" . $filepath . "'.");
        if (file_exists($filepath) && is_file($filepath) && is_readable($filepath)) {
            $s_file[] = $filepath;
            $base64_encoded = false;
        } else {
            stdOut("Error:" . $filepath . " either is not a file or readable");
        }
    } elseif ($listing) {
        //scan listing
        if ($listing == 'stdin') {
            $lines = explode("\n", getStdin());
        } else {
            $lines = new SplFileObject($listing);
            $lines->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        }
        if (is_array($lines)) {
            $vars->foundTotalFiles = count($lines);
        } else if ($lines instanceof SplFileObject) {
            $lines->seek($lines->getSize());
            $vars->foundTotalFiles = $lines->key();
            $lines->seek(0);
        }

        $s_file = $lines;
        stdOut("Start scanning the list from '" . $listing . "'.\n");
    } else {
        //scan by path
        $base64_encoded = true;
        file_exists(QUEUE_FILENAME) && unlink(QUEUE_FILENAME);
        QCR_ScanDirectories(ROOT_PATH, $vars);
        stdOut("Found $vars->foundTotalFiles files in $vars->foundTotalDirs directories.");
        stdOut("Start scanning '" . ROOT_PATH . "'.\n");
        if (ICHECK || IMAKE) {
            processIntegrity($vars);
        }

        QCR_Debug();
        stdOut(str_repeat(' ', 160), false);
        $s_file = new SplFileObject(QUEUE_FILENAME);
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
    }

    QCR_GoScan($s_file, $vars, null, $base64_encoded, $use_doublecheck);
    unset($s_file);
    @unlink(QUEUE_FILENAME);
    $vars->foundTotalDirs  = $vars->totalFolder;

    if (defined('PROGRESS_LOG_FILE') && file_exists(PROGRESS_LOG_FILE)) {
        @unlink(PROGRESS_LOG_FILE);
    }
    if (CREATE_SHARED_MEMORY) {
        shmop_delete(SHARED_MEMORY);
    }
    if (defined('SHARED_MEMORY')) {
        shmop_close(SHARED_MEMORY);
    }
} catch (Exception $e) {
    QCR_Debug($e->getMessage());
}
QCR_Debug();

if (true) {
    $g_HeuristicDetected = array();
    $g_Iframer           = array();
    $g_Base64            = array();
}
/**
 * @param Variables $vars
 * @return array
 */
function whitelisting(Variables $vars)
{
// whitelist

    $snum = 0;
    $list = check_whitelist($vars->structure['crc'], $snum);
    $keys = array(
        'criticalPHP',
        'criticalJS',
        'g_Iframer',
        'g_Base64',
        'phishing',
        'adwareList',
        'g_Redirect',
        'warningPHP'
    );

    foreach ($keys as $p) {
        if (empty($vars->{$p})) {
            continue;
        }
        $p_Fragment = $p . 'Fragment';
        $p_Sig      = $p . 'Sig';
        
        if ($p == 'g_Redirect') {
            $p_Fragment = $p . 'PHPFragment';
        }
        elseif ($p == 'g_Phishing') {
            $p_Sig = $p . 'SigFragment';
        }

        $count = count($vars->{$p});
        for ($i = 0; $i < $count; $i++) {
            $id = $vars->{$p}[$i];
            if ($vars->structure['crc'][$id] !== 0 && in_array($vars->structure['crc'][$id], $list)) {
                unset($vars->{$p}[$i]);
                unset($vars->{$p_Sig}[$i]);
                unset($vars->{$p_Fragment}[$i]);
            }
        }

        $vars->{$p}             = array_values($vars->{$p});
        $vars->{$p_Fragment}    = array_values($vars->{$p_Fragment});
        if (!empty($vars->{$p_Sig})) {
            $vars->{$p_Sig} = array_values($vars->{$p_Sig});
        }
    }
    return array($snum, $i);
}

whitelisting($vars);


////////////////////////////////////////////////////////////////////////////
if (AI_HOSTER) {
    $g_IframerFragment       = array();
    $g_Iframer               = array();
    $vars->redirect          = array();
    $vars->doorway           = array();
    $g_EmptyLink             = array();
    $g_HeuristicType         = array();
    $g_HeuristicDetected     = array();
    $vars->adwareList            = array();
    $vars->phishing              = array();
    $g_PHPCodeInside         = array();
    $g_PHPCodeInsideFragment = array();
    $vars->bigFiles              = array();
    $vars->redirectPHPFragment  = array();
    $g_EmptyLinkSrc          = array();
    $g_Base64Fragment        = array();
    $g_UnixExec              = array();
    $vars->phishingSigFragment   = array();
    $vars->phishingFragment      = array();
    $g_PhishingSig           = array();
    $g_IframerFragment       = array();
    $vars->CMS                  = array();
    $vars->adwareListFragment    = array();
}

if (BOOL_RESULT && (!defined('NEED_REPORT'))) {
    if ((count($vars->criticalPHP) > 0) OR (count($vars->criticalJS) > 0) OR (count($g_PhishingSig) > 0)) {
        exit(2);
    } else {
        exit(0);
    }
}
////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@SERVICE_INFO@@", htmlspecialchars("[" . $int_enc . "][" . $snum . "]"), $l_Template);

$l_Template = str_replace("@@PATH_URL@@", (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $g_AddPrefix . str_replace($g_NoPrefix, '', addSlash(ROOT_PATH))), $l_Template);

$time_taken = seconds2Human(microtime(true) - START_TIME);

$l_Template = str_replace("@@SCANNED@@", sprintf(AI_STR_013, $vars->totalFolder, $vars->totalFiles), $l_Template);

$l_ShowOffer = false;

stdOut("\nBuilding report [ mode = " . AI_EXPERT . " ]\n");

//stdOut("\nLoaded signatures: " . count($g_FlexDBShe) . " / " . count($g_JSVirSig) . "\n");

////////////////////////////////////////////////////////////////////////////
// save 
if (!(ICHECK || IMAKE)) {
    if (isset($options['with-2check']) || isset($options['quarantine'])) {
        if ((count($vars->criticalPHP) > 0) OR (count($vars->criticalJS) > 0) OR (count($g_Base64) > 0) OR (count($g_Iframer) > 0) OR (count($g_UnixExec))) {
            if (!file_exists(DOUBLECHECK_FILE)) {
                if ($l_FH = fopen(DOUBLECHECK_FILE, 'w')) {
                    fputs($l_FH, '<?php die("Forbidden"); ?>' . "\n");

                    $l_CurrPath = dirname(__FILE__);

                    if (!isset($vars->criticalPHP)) {
                        $vars->criticalPHP = array();
                    }
                    if (!isset($vars->criticalJS)) {
                        $vars->criticalJS = array();
                    }
                    if (!isset($g_Iframer)) {
                        $g_Iframer = array();
                    }
                    if (!isset($g_Base64)) {
                        $g_Base64 = array();
                    }
                    if (!isset($vars->phishing)) {
                        $vars->phishing = array();
                    }
                    if (!isset($vars->adwareList)) {
                        $vars->adwareList = array();
                    }
                    if (!isset($vars->redirect)) {
                        $vars->redirect = array();
                    }

                    $tmpIndex = array_merge($vars->criticalPHP, $vars->criticalJS, $vars->phishing, $g_Base64, $g_Iframer, $vars->adwareList, $vars->redirect);
                    $tmpIndex = array_values(array_unique($tmpIndex));

                    for ($i = 0; $i < count($tmpIndex); $i++) {
                        $tmpIndex[$i] = str_replace($l_CurrPath, '.', $vars->structure['n'][$tmpIndex[$i]]);
                    }

                    for ($i = 0; $i < count($g_UnixExec); $i++) {
                        $tmpIndex[] = str_replace($l_CurrPath, '.', $g_UnixExec[$i]);
                    }

                    $tmpIndex = array_values(array_unique($tmpIndex));

                    for ($i = 0; $i < count($tmpIndex); $i++) {
                        fputs($l_FH, $tmpIndex[$i] . "\n");
                    }

                    fclose($l_FH);
                } else {
                    stdOut("Error! Cannot create " . DOUBLECHECK_FILE);
                }
            } else {
                stdOut(DOUBLECHECK_FILE . ' already exists.');
                if (AI_STR_044 != '') {
                    $l_Result .= '<div class="rep">' . AI_STR_044 . '</div>';
                }
            }
        }
    }
}
////////////////////////////////////////////////////////////////////////////

$l_Summary = '<div class="title">' . AI_STR_074 . '</div>';
$l_Summary .= '<table cellspacing=0 border=0>';

if (count($vars->redirect) > 0) {
    $l_Summary .= makeSummary(AI_STR_059, count($vars->redirect), 'crit');
}

if (count($vars->criticalPHP) > 0) {
    $l_Summary .= makeSummary(AI_STR_060, count($vars->criticalPHP), "crit");
}

if (count($vars->criticalJS) > 0) {
    $l_Summary .= makeSummary(AI_STR_061, count($vars->criticalJS), "crit");
}

if (count($vars->phishing) > 0) {
    $l_Summary .= makeSummary(AI_STR_062, count($vars->phishing), "crit");
}

if (count($vars->notRead) > 0) {
    $l_Summary .= makeSummary(AI_STR_066, count($vars->notRead), "crit");
}

if (count($vars->warningPHP) > 0) {
    $l_Summary .= makeSummary(AI_STR_068, count($vars->warningPHP), "warn");
}

if (count($vars->bigFiles) > 0) {
    $l_Summary .= makeSummary(AI_STR_065, count($vars->bigFiles), "warn");
}

if (count($vars->symLinks) > 0) {
    $l_Summary .= makeSummary(AI_STR_069, count($vars->symLinks), "warn");
}

$l_Summary .= "</table>";

$l_ArraySummary                      = array();
$l_ArraySummary["redirect"]          = count($vars->redirect);
$l_ArraySummary["critical_php"]      = count($vars->criticalPHP);
$l_ArraySummary["critical_js"]       = count($vars->criticalJS);
$l_ArraySummary["phishing"]          = count($vars->phishing);
$l_ArraySummary["unix_exec"]         = 0; // count($g_UnixExec);
$l_ArraySummary["iframes"]           = 0; // count($g_Iframer);
$l_ArraySummary["not_read"]          = count($vars->notRead);
$l_ArraySummary["base64"]            = 0; // count($g_Base64);
$l_ArraySummary["heuristics"]        = 0; // count($g_HeuristicDetected);
$l_ArraySummary["symlinks"]          = count($vars->symLinks);
$l_ArraySummary["big_files_skipped"] = count($vars->bigFiles);
$l_ArraySummary["suspicious"]        = count($vars->warningPHP);

if (function_exists('json_encode')) {
    $l_Summary .= "<!--[json]" . json_encode($l_ArraySummary) . "[/json]-->";
}

$l_Summary .= "<div class=details style=\"margin: 20px 20px 20px 0\">" . AI_STR_080 . "</div>\n";

$l_Template = str_replace("@@SUMMARY@@", $l_Summary, $l_Template);

$l_Result .= AI_STR_015;

$l_Template = str_replace("@@VERSION@@", AI_VERSION, $l_Template);

////////////////////////////////////////////////////////////////////////////



if (function_exists("gethostname") && is_callable("gethostname")) {
    $l_HostName = gethostname();
} else {
    $l_HostName = '???';
}

$l_PlainResult = "# Malware list detected by AI-Bolit (https://revisium.com/ai/) on " . date("d/m/Y H:i:s", time()) . " " . $l_HostName . "\n\n";


$scan_time = round(microtime(true) - START_TIME, 1);
$json_report = $reportFactory();
$json_report->addVars($vars, $scan_time);

if (!AI_HOSTER) {
    stdOut("Building list of vulnerable scripts " . count($vars->vulnerable));

    if (count($vars->vulnerable) > 0) {
        $l_Result .= '<div class="note_vir">' . AI_STR_081 . ' (' . count($vars->vulnerable) . ')</div><div class="crit">';
        foreach ($vars->vulnerable as $l_Item) {
            $l_Result .= '<li>' . makeSafeFn($vars->structure['n'][$l_Item['ndx']], true) . ' - ' . $l_Item['id'] . '</li>';
            $l_PlainResult .= '[VULNERABILITY] ' . replacePathArray($vars->structure['n'][$l_Item['ndx']]) . ' - ' . $l_Item['id'] . "\n";
        }

        $l_Result .= '</div><p>' . PHP_EOL;
        $l_PlainResult .= "\n";
    }
}


stdOut("Building list of shells " . count($vars->criticalPHP));

if (count($vars->criticalPHP) > 0) {
    $vars->criticalPHP              = array_slice($vars->criticalPHP, 0, 15000);
    $l_Result .= '<div class="note_vir">' . AI_STR_016 . ' (' . count($vars->criticalPHP) . ')</div><div class="crit">';
    $l_Result .= printList($vars->criticalPHP, $vars, $vars->criticalPHPFragment, true, $vars->criticalPHPSig, 'table_crit');
    $l_PlainResult .= '[SERVER MALWARE]' . "\n" . printPlainList($vars->criticalPHP, $vars,  $vars->criticalPHPFragment, true, $vars->criticalPHPSig, 'table_crit') . "\n";
    $l_Result .= '</div>' . PHP_EOL;

    $l_ShowOffer = true;
} else {
    $l_Result .= '<div class="ok"><b>' . AI_STR_017 . '</b></div>';
}

stdOut("Building list of js " . count($vars->criticalJS));

if (count($vars->criticalJS) > 0) {
    $vars->criticalJS              = array_slice($vars->criticalJS, 0, 15000);
    $l_Result .= '<div class="note_vir">' . AI_STR_018 . ' (' . count($vars->criticalJS) . ')</div><div class="crit">';
    $l_Result .= printList($vars->criticalJS, $vars, $vars->criticalJSFragment, true, $vars->criticalJSSig, 'table_vir');
    $l_PlainResult .= '[CLIENT MALWARE / JS]' . "\n" . printPlainList($vars->criticalJS, $vars,  $vars->criticalJSFragment, true, $vars->criticalJSSig, 'table_vir') . "\n";
    $l_Result .= "</div>" . PHP_EOL;

    $l_ShowOffer = true;
}

stdOut("Building list of unread files " . count($vars->notRead));

if (count($vars->notRead) > 0) {
    $vars->notRead               = array_slice($vars->notRead, 0, AIBOLIT_MAX_NUMBER);
    $l_Result .= '<div class="note_vir">' . AI_STR_030 . ' (' . count($vars->notRead) . ')</div><div class="crit">';
    $l_Result .= printList($vars->notRead, $vars);
    $l_Result .= "</div><div class=\"spacer\"></div>" . PHP_EOL;
    $l_PlainResult .= '[SCAN ERROR / SKIPPED]' . "\n" . printPlainList($vars->notRead, $vars) . "\n\n";
}

if (!AI_HOSTER) {
    stdOut("Building list of phishing pages " . count($vars->phishing));

    if (count($vars->phishing) > 0) {
        $l_Result .= '<div class="note_vir">' . AI_STR_058 . ' (' . count($vars->phishing) . ')</div><div class="crit">';
        $l_Result .= printList($vars->phishing, $vars, $vars->phishingFragment, true, $vars->phishingSigFragment, 'table_vir');
        $l_PlainResult .= '[PHISHING]' . "\n" . printPlainList($vars->phishing, $vars,  $vars->phishingFragment, true, $vars->phishingSigFragment, 'table_vir') . "\n";
        $l_Result .= "</div>" . PHP_EOL;

        $l_ShowOffer = true;
    }

    stdOut('Building list of redirects ' . count($vars->redirect));
    if (count($vars->redirect) > 0) {
        $l_ShowOffer             = true;
        $l_Result .= '<div class="note_vir">' . AI_STR_027 . ' (' . count($vars->redirect) . ')</div><div class="crit">';
        $l_Result .= printList($vars->redirect, $vars, $vars->redirectPHPFragment, true);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of symlinks " . count($vars->symLinks));

    if (count($vars->symLinks) > 0) {
        $vars->symLinks               = array_slice($vars->symLinks, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_vir">' . AI_STR_022 . ' (' . count($vars->symLinks) . ')</div><div class="crit">';
        $l_Result .= nl2br(makeSafeFn(implode("\n", $vars->symLinks), true));
        $l_Result .= "</div><div class=\"spacer\"></div>";
    }

}

if (AI_EXTRA_WARN) {
    $l_WarningsNum = count($vars->warningPHP);
    if ($l_WarningsNum > 0) {
        $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_026 . "</div>";
    }

    stdOut("Building list of suspicious files " . count($vars->warningPHP));

    if ((count($vars->warningPHP) > 0) && JSONReport::checkMask($defaults['report_mask'], JSONReport::REPORT_MASK_FULL)) {
        $vars->warningPHP              = array_slice($vars->warningPHP, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_warn">' . AI_STR_035 . ' (' . count($vars->warningPHP) . ')</div><div class="warn">';
        $l_Result .= printList($vars->warningPHP, $vars, $vars->warningPHPFragment, true, $vars->warningPHPSig, 'table_warn');
        $l_PlainResult .= '[SUSPICIOUS]' . "\n" . printPlainList($vars->warningPHP, $vars,  $vars->warningPHPFragment, true, $vars->warningPHPSig, 'table_warn') . "\n";
        $l_Result .= '</div>' . PHP_EOL;
    }
}
////////////////////////////////////
if (!AI_HOSTER) {
    $l_WarningsNum = count($g_HeuristicDetected) + count($g_HiddenFiles) + count($vars->bigFiles) + count($g_PHPCodeInside) + count($vars->adwareList) + count($g_EmptyLink) + count($vars->doorway) + count($vars->warningPHP) + count($vars->skippedFolders);

    if ($l_WarningsNum > 0) {
        $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_026 . "</div>";
    }

    stdOut("Building list of adware " . count($vars->adwareList));

    if (count($vars->adwareList) > 0) {
        $l_Result .= '<div class="note_warn">' . AI_STR_029 . '</div><div class="warn">';
        $l_Result .= printList($vars->adwareList, $vars, $vars->adwareListFragment, true);
        $l_PlainResult .= '[ADWARE]' . "\n" . printPlainList($vars->adwareList, $vars,  $vars->adwareListFragment, true) . "\n";
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of bigfiles " . count($vars->bigFiles));
    $max_size_to_scan = getBytes(MAX_SIZE_TO_SCAN);
    $max_size_to_scan = $max_size_to_scan > 0 ? $max_size_to_scan : getBytes('1m');

    if (count($vars->bigFiles) > 0) {
        $vars->bigFiles               = array_slice($vars->bigFiles, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= "<div class=\"note_warn\">" . sprintf(AI_STR_038, bytes2Human($max_size_to_scan)) . '</div><div class="warn">';
        $l_Result .= printList($vars->bigFiles, $vars);
        $l_Result .= "</div>";
        $l_PlainResult .= '[BIG FILES / SKIPPED]' . "\n" . printPlainList($vars->bigFiles, $vars) . "\n\n";
    }

    stdOut("Building list of doorways " . count($vars->doorway));

    if ((count($vars->doorway) > 0) && JSONReport::checkMask($defaults['report_mask'], JSONReport::REPORT_MASK_DOORWAYS)) {
        $vars->doorway              = array_slice($vars->doorway, 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_warn">' . AI_STR_034 . '</div><div class="warn">';
        $l_Result .= printList($vars->doorway, $vars);
        $l_Result .= "</div>" . PHP_EOL;

    }

    if (count($vars->CMS) > 0) {
        $l_Result .= "<div class=\"note_warn\">" . AI_STR_037 . "<br/>";
        $l_Result .= nl2br(makeSafeFn(implode("\n", $vars->CMS)));
        $l_Result .= "</div>";
    }
}

if (ICHECK) {
    $l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_087 . "</div>";

    stdOut("Building list of added files " . count($changes['addedFiles']));
    if (count($changes['addedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_082 . ' (' . count($changes['addedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['addedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of modified files " . count($changes['modifiedFiles']));
    if (count($changes['modifiedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_083 . ' (' . count($changes['modifiedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['modifiedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted files " . count($changes['deletedFiles']));
    if (count($changes['deletedFiles']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_084 . ' (' . count($changes['deletedFiles']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['deletedFiles'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of added dirs " . count($changes['addedDirs']));
    if (count($changes['addedDirs']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_085 . ' (' . count($changes['addedDirs']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['addedDirs'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted dirs " . count($changes['deletedDirs']));
    if (count($changes['deletedDirs']) > 0) {
        $l_Result .= '<div class="note_int">' . AI_STR_086 . ' (' . count($changes['deletedDirs']) . ')</div><div class="intitem">';
        $l_Result .= printList($changes['deletedDirs'], $vars);
        $l_Result .= "</div>" . PHP_EOL;
    }
}

if (!isCli()) {
    $l_Result .= QCR_ExtractInfo($l_PhpInfoBody[1]);
}


if (function_exists('memory_get_peak_usage')) {
    $l_Template = str_replace("@@MEMORY@@", AI_STR_043 . bytes2Human(memory_get_peak_usage()), $l_Template);
}

$l_Template = str_replace('@@WARN_QUICK@@', ((SCAN_ALL_FILES || $g_SpecificExt) ? '' : AI_STR_045), $l_Template);

if ($l_ShowOffer) {
    $l_Template = str_replace('@@OFFER@@', $l_Offer, $l_Template);
} else {
    $l_Template = str_replace('@@OFFER@@', AI_STR_002, $l_Template);
}

$l_Template = str_replace('@@OFFER2@@', $l_Offer2, $l_Template);

$l_Template = str_replace('@@CAUTION@@', AI_STR_003, $l_Template);

$l_Template = str_replace('@@CREDITS@@', AI_STR_075, $l_Template);

$l_Template = str_replace('@@FOOTER@@', AI_STR_076, $l_Template);

$l_Template = str_replace('@@STAT@@', sprintf(AI_STR_012, $time_taken, date('d-m-Y в H:i:s', floor(START_TIME)), date('d-m-Y в H:i:s')), $l_Template);

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MAIN_CONTENT@@", $l_Result, $l_Template);

if (!isCli()) {
    echo $l_Template;
    exit;
}

if (!defined('REPORT') OR REPORT === '') {
    die2('Report not written.');
}

// write plain text result
if (PLAIN_FILE != '') {

    $l_PlainResult = preg_replace('|__AI_LINE1__|smi', '[', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_LINE2__|smi', '] ', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_MARKER__|smi', ' %> ', $l_PlainResult);

    if ($l_FH = fopen(PLAIN_FILE, "w")) {
        fputs($l_FH, $l_PlainResult);
        fclose($l_FH);
    }
}

// write json result
if (defined('JSON_FILE')) {
    $res = $json_report->write(JSON_FILE);
    if (JSON_STDOUT) {
        echo $res;
    }
}

// write serialized result
if (defined('PHP_FILE')) {
    $json_report->writePHPSerialized(PHP_FILE);
}

$emails = getEmails(REPORT);

if (!$emails) {
    if ($l_FH = fopen($file, "w")) {
        fputs($l_FH, $l_Template);
        fclose($l_FH);
        stdOut("\nReport written to '$file'.");
    } else {
        stdOut("\nCannot create '$file'.");
    }
} else {
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . ($defaults['email_from'] ? $defaults['email_from'] : 'AI-Bolit@myhost')
    );

    for ($i = 0, $size = sizeof($emails); $i < $size; $i++) {
        //$res = @mail($emails[$i], 'AI-Bolit Report ' . date("d/m/Y H:i", time()), $l_Result, implode("\r\n", $headers));
    }

    if ($res) {
        stdOut("\nReport sended to " . implode(', ', $emails));
    }
}

$time_taken = microtime(true) - START_TIME;
$time_taken = round($time_taken, 5);

stdOut("Scanning complete! Time taken: " . seconds2Human($time_taken));

if (DEBUG_PERFORMANCE) {
    $keys = array_keys($g_RegExpStat);
    for ($i = 0; $i < count($keys); $i++) {
        $g_RegExpStat[$keys[$i]] = round($g_RegExpStat[$keys[$i]] * 1000000);
    }

    arsort($g_RegExpStat);

    foreach ($g_RegExpStat as $r => $v) {
        echo $v . "\t\t" . $r . "\n";
    }

    die();
}

stdOut("\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
stdOut("Attention! DO NOT LEAVE either ai-bolit.php or AI-BOLIT-REPORT-<xxxx>-<yy>.html \nfile on server. COPY it locally then REMOVE from server. ");
stdOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");

if (isset($options['quarantine'])) {
    Quarantine();
}

if (isset($options['cmd'])) {
    stdOut("Run \"{$options['cmd']}\" ");
    system($options['cmd']);
}

QCR_Debug();

# exit with code

$l_EC1 = count($vars->criticalPHP);
$l_EC2 = count($vars->criticalJS) + count($vars->phishing) + count($vars->warningPHP);
$code  = 0;

if ($l_EC1 > 0) {
    $code = 2;
} else {
    if ($l_EC2 > 0) {
        $code = 1;
    }
}

$stat = array(
    'php_malware'   => count($vars->criticalPHP),
    'cloudhash'     => count($vars->blackFiles),
    'js_malware'    => count($vars->criticalJS),
    'phishing'      => count($vars->phishing)
);

if (function_exists('aibolit_onComplete')) {
    aibolit_onComplete($code, $stat);
}

stdOut('Exit code ' . $code);
exit($code);

############################################# END ###############################################

function Quarantine() {
    if (!file_exists(DOUBLECHECK_FILE)) {
        return;
    }

    $g_QuarantinePass = 'aibolit';

    $archive  = "AI-QUARANTINE-" . rand(100000, 999999) . ".zip";
    $infoFile = substr($archive, 0, -3) . "txt";
    $report   = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE;


    foreach (file(DOUBLECHECK_FILE) as $file) {
        $file = trim($file);
        if (!is_file($file))
            continue;

        $lStat = stat($file);

        // skip files over 300KB
        if ($lStat['size'] > 300 * 1024)
            continue;

        // http://www.askapache.com/security/chmod-stat.html
        $p    = $lStat['mode'];
        $perm = '-';
        $perm .= (($p & 0x0100) ? 'r' : '-') . (($p & 0x0080) ? 'w' : '-');
        $perm .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x') : (($p & 0x0800) ? 'S' : '-'));
        $perm .= (($p & 0x0020) ? 'r' : '-') . (($p & 0x0010) ? 'w' : '-');
        $perm .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x') : (($p & 0x0400) ? 'S' : '-'));
        $perm .= (($p & 0x0004) ? 'r' : '-') . (($p & 0x0002) ? 'w' : '-');
        $perm .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x') : (($p & 0x0200) ? 'T' : '-'));

        $owner = (function_exists('posix_getpwuid')) ? @posix_getpwuid($lStat['uid']) : array(
            'name' => $lStat['uid']
        );
        $group = (function_exists('posix_getgrgid')) ? @posix_getgrgid($lStat['gid']) : array(
            'name' => $lStat['uid']
        );

        $inf['permission'][] = $perm;
        $inf['owner'][]      = $owner['name'];
        $inf['group'][]      = $group['name'];
        $inf['size'][]       = $lStat['size'] > 0 ? bytes2Human($lStat['size']) : '-';
        $inf['ctime'][]      = $lStat['ctime'] > 0 ? date("d/m/Y H:i:s", $lStat['ctime']) : '-';
        $inf['mtime'][]      = $lStat['mtime'] > 0 ? date("d/m/Y H:i:s", $lStat['mtime']) : '-';
        $files[]             = strpos($file, './') === 0 ? substr($file, 2) : $file;
    }

    // get config files for cleaning
    $configFilesRegex = 'config(uration|\.in[ic])?\.php$|dbconn\.php$';
    $configFiles      = preg_grep("~$configFilesRegex~", $files);

    // get columns width
    $width = array();
    foreach (array_keys($inf) as $k) {
        $width[$k] = strlen($k);
        for ($i = 0; $i < count($inf[$k]); ++$i) {
            $len = strlen($inf[$k][$i]);
            if ($len > $width[$k])
                $width[$k] = $len;
        }
    }

    // headings of columns
    $info = '';
    foreach (array_keys($inf) as $k) {
        $info .= str_pad($k, $width[$k], ' ', STR_PAD_LEFT) . ' ';
    }
    $info .= "name\n";

    for ($i = 0; $i < count($files); ++$i) {
        foreach (array_keys($inf) as $k) {
            $info .= str_pad($inf[$k][$i], $width[$k], ' ', STR_PAD_LEFT) . ' ';
        }
        $info .= $files[$i] . "\n";
    }
    unset($inf, $width);

    exec("zip -v 2>&1", $output, $code);

    if ($code == 0) {
        $filter = '';
        if ($configFiles && exec("grep -V 2>&1", $output, $code) && $code == 0) {
            $filter = "|grep -v -E '$configFilesRegex'";
        }

        exec("cat AI-BOLIT-DOUBLECHECK.php $filter |zip -@ --password $g_QuarantinePass $archive", $output, $code);
        if ($code == 0) {
            file_put_contents($infoFile, $info);
            $m = array();
            if (!empty($filter)) {
                foreach ($configFiles as $file) {
                    $tmp  = file_get_contents($file);
                    // remove  passwords
                    $tmp  = preg_replace('~^.*?pass.*~im', '', $tmp);
                    // new file name
                    $file = preg_replace('~.*/~', '', $file) . '-' . rand(100000, 999999);
                    file_put_contents($file, $tmp);
                    $m[] = $file;
                }
            }

            exec("zip -j --password $g_QuarantinePass $archive $infoFile $report " . DOUBLECHECK_FILE . ' ' . implode(' ', $m));
            stdOut("\nCreate archive '" . realpath($archive) . "'");
            stdOut("This archive have password '$g_QuarantinePass'");
            foreach ($m as $file)
                unlink($file);
            unlink($infoFile);
            return;
        }
    }

    $zip = new ZipArchive;

    if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::OVERWRITE) === false) {
        stdOut("Cannot create '$archive'.");
        return;
    }

    foreach ($files as $file) {
        if (in_array($file, $configFiles)) {
            $tmp = file_get_contents($file);
            // remove  passwords
            $tmp = preg_replace('~^.*?pass.*~im', '', $tmp);
            $zip->addFromString($file, $tmp);
        } else {
            $zip->addFile($file);
        }
    }
    $zip->addFile(DOUBLECHECK_FILE, DOUBLECHECK_FILE);
    $zip->addFile($report, REPORT_FILE);
    $zip->addFromString($infoFile, $info);
    $zip->close();

    stdOut("\nCreate archive '" . realpath($archive) . "'.");
    stdOut("This archive has no password!");
}



///////////////////////////////////////////////////////////////////////////
function QCR_IntegrityCheck($l_RootDir, $vars) {
    global $defaults, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, $g_UnsafeFilesFound, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SuspiciousFiles, $l_SkipSample;
    global $g_IntegrityDB, $g_ICheck;
    static $l_Buffer = '';

    $l_DirCounter          = 0;
    $l_DoorwayFilesCounter = 0;
    $l_SourceDirIndex      = $vars->g_counter - 1;

    QCR_Debug('Check ' . $l_RootDir);

    if ($l_DIRH = @opendir($l_RootDir)) {
        while (($l_FileName = readdir($l_DIRH)) !== false) {
            if ($l_FileName == '.' || $l_FileName == '..')
                continue;

            $l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;

            $l_Type  = filetype($l_FileName);
            $l_IsDir = ($l_Type == "dir");
            if ($l_Type == "link") {
                $vars->symLinks[] = $l_FileName;
                continue;
            } else if ($l_Type != "file" && (!$l_IsDir)) {
                $g_UnixExec[] = $l_FileName;
                continue;
            }

            $l_Ext = substr($l_FileName, strrpos($l_FileName, '.') + 1);

            $l_NeedToScan = true;
            $l_Ext2       = substr(strstr(basename($l_FileName), '.'), 1);
            if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                $l_NeedToScan = false;
            }

            // if folder in ignore list
            $l_Skip = false;
            for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
                if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
                    if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                        $l_SkipSample[] = $g_DirIgnoreList[$dr];
                    } else {
                        $l_Skip       = true;
                        $l_NeedToScan = false;
                    }
                }
            }

            if (getRelativePath($l_FileName) == "./" . INTEGRITY_DB_FILE)
                $l_NeedToScan = false;

            if ($l_IsDir) {
                // skip on ignore
                if ($l_Skip) {
                    $vars->skippedFolders[] = $l_FileName;
                    continue;
                }

                $l_BaseName = basename($l_FileName);

                $l_DirCounter++;

                $vars->counter++;
                $vars->foundTotalDirs++;

                QCR_IntegrityCheck($l_FileName, $vars);

            } else {
                if ($l_NeedToScan) {
                    $vars->foundTotalFiles++;
                    $vars->counter++;
                }
            }

            if (!$l_NeedToScan)
                continue;

            if (IMAKE) {
                write_integrity_db_file($l_FileName);
                continue;
            }

            // ICHECK
            // skip if known and not modified.
            if (icheck($l_FileName))
                continue;

            $l_Buffer .= getRelativePath($l_FileName);
            $l_Buffer .= $l_IsDir ? DIR_SEPARATOR . "\n" : "\n";

            if (strlen($l_Buffer) > 32000) {
                file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
                $l_Buffer = '';
            }

        }

        closedir($l_DIRH);
    }

    if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
        file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
        $l_Buffer = '';
    }

    if (($l_RootDir == ROOT_PATH)) {
        write_integrity_db_file();
    }

}


function getRelativePath($l_FileName) {
    return "./" . substr($l_FileName, strlen(ROOT_PATH) + 1) . (is_dir($l_FileName) ? DIR_SEPARATOR : '');
}

/**
 *
 * @return true if known and not modified
 */
function icheck($l_FileName) {
    global $g_IntegrityDB, $g_ICheck;
    static $l_Buffer = '';
    static $l_status = array('modified' => 'modified', 'added' => 'added');

    $l_RelativePath = getRelativePath($l_FileName);
    $l_known        = isset($g_IntegrityDB[$l_RelativePath]);

    if (is_dir($l_FileName)) {
        if ($l_known) {
            unset($g_IntegrityDB[$l_RelativePath]);
        } else {
            $g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
        }
        return $l_known;
    }

    if ($l_known == false) {
        $g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
        return false;
    }

    $hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';

    if ($g_IntegrityDB[$l_RelativePath] != $hash) {
        $g_IntegrityDB[$l_RelativePath] =& $l_status['modified'];
        return false;
    }

    unset($g_IntegrityDB[$l_RelativePath]);
    return true;
}

function write_integrity_db_file($l_FileName = '') {
    static $l_Buffer = '';

    if (empty($l_FileName)) {
        empty($l_Buffer) or file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
        $l_Buffer = '';
        return;
    }

    $l_RelativePath = getRelativePath($l_FileName);

    $hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';

    $l_Buffer .= "$l_RelativePath|$hash\n";

    if (strlen($l_Buffer) > 32000) {
        file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
        $l_Buffer = '';
    }
}

function load_integrity_db() {
    global $g_IntegrityDB;
    file_exists(INTEGRITY_DB_FILE) or die2('Not found ' . INTEGRITY_DB_FILE);

    $s_file = new SplFileObject('compress.zlib://' . INTEGRITY_DB_FILE);
    $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

    foreach ($s_file as $line) {
        $i = strrpos($line, '|');
        if (!$i)
            continue;
        $g_IntegrityDB[substr($line, 0, $i)] = substr($line, $i + 1);
    }

    $s_file = null;
}


function getStdin()
{
    $stdin  = '';
    $f      = @fopen('php://stdin', 'r');
    while($line = fgets($f))
    {
        $stdin .= $line;
    }
    fclose($f);
    return $stdin;
}

function OptimizeSignatures() {
    global $g_DBShe, $g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe;
    global $g_JSVirSig, $gX_JSVirSig;
    global $g_AdwareSig;
    global $g_PhishingSig;
    global $g_ExceptFlex, $g_SusDBPrio, $g_SusDB;

    (AI_EXPERT == 2) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe));
    (AI_EXPERT == 1) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe));
    $gX_FlexDBShe = $gXX_FlexDBShe = array();

    (AI_EXPERT == 2) && ($g_JSVirSig = array_merge($g_JSVirSig, $gX_JSVirSig));
    $gX_JSVirSig = array();

    $count = count($g_FlexDBShe);

    for ($i = 0; $i < $count; $i++) {
        if ($g_FlexDBShe[$i] == '[a-zA-Z0-9_]+?\(\s*[a-zA-Z0-9_]+?=\s*\)')
            $g_FlexDBShe[$i] = '\((?<=[a-zA-Z0-9_].)\s*[a-zA-Z0-9_]++=\s*\)';
        if ($g_FlexDBShe[$i] == '([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e')
            $g_FlexDBShe[$i] = '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e';
        if ($g_FlexDBShe[$i] == '$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.')
            $g_FlexDBShe[$i] = '\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.';

        $g_FlexDBShe[$i] = str_replace('http://.+?/.+?\.php\?a', 'http://[^?\s]++(?<=\.php)\?a', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~\[a-zA-Z0-9_\]\+\K\?~', '+', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~^\\\\[d]\+&@~', '&@(?<=\d..)', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = str_replace('\s*[\'"]{0,1}.+?[\'"]{0,1}\s*', '.+?', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = str_replace('[\'"]{0,1}.+?[\'"]{0,1}', '.+?', $g_FlexDBShe[$i]);

        $g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
        $g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
    }

    optSig($g_FlexDBShe);

    optSig($g_JSVirSig);
    optSig($g_AdwareSig);
    optSig($g_PhishingSig);
    optSig($g_SusDB);
    //optSig($g_SusDBPrio);
    //optSig($g_ExceptFlex);

    // convert exception rules
    $cnt = count($g_ExceptFlex);
    for ($i = 0; $i < $cnt; $i++) {
        $g_ExceptFlex[$i] = trim(UnwrapObfu($g_ExceptFlex[$i]));
        if (!strlen($g_ExceptFlex[$i]))
            unset($g_ExceptFlex[$i]);
    }

    $g_ExceptFlex = array_values($g_ExceptFlex);
}

function optSig(&$sigs) {
    $sigs = array_unique($sigs);

    // Add SigId
    foreach ($sigs as &$s) {
        $s .= '(?<X' . myCheckSum($s) . '>)';
    }
    unset($s);

    $fix = array(
        '([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e' => '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e',
        'http://.+?/.+?\.php\?a' => 'http://[^?\s]++(?<=\.php)\?a',
        '\s*[\'"]{0,1}.+?[\'"]{0,1}\s*' => '.+?',
        '[\'"]{0,1}.+?[\'"]{0,1}' => '.+?'
    );

    $sigs = str_replace(array_keys($fix), array_values($fix), $sigs);

    $fix = array(
        '~^\\\\[d]\+&@~' => '&@(?<=\d..)',
        '~^((\[\'"\]|\\\\s|@)(\{0,1\}\.?|[?*]))+~' => ''
    );

    $sigs = preg_replace(array_keys($fix), array_values($fix), $sigs);

    optSigCheck($sigs);

    $tmp = array();
    foreach ($sigs as $i => $s) {
        if (!preg_match('~^(?>(?!\.[*+]|\\\\\d)(?:\\\\.|\[.+?\]|.))+$~', $s)) {
            unset($sigs[$i]);
            $tmp[] = $s;
        }
    }

    usort($sigs, 'strcasecmp');
    $txt = implode("\n", $sigs);

    for ($i = 24; $i >= 1; ($i > 4) ? $i -= 4 : --$i) {
        $txt = preg_replace_callback('#^((?>(?:\\\\.|\\[.+?\\]|[^(\n]|\((?:\\\\.|[^)(\n])++\))(?:[*?+]\+?|\{\d+(?:,\d*)?\}[+?]?|)){' . $i . ',})[^\n]*+(?:\\n\\1(?![{?*+]).+)+#im', 'optMergePrefixes', $txt);
    }

    $sigs = array_merge(explode("\n", $txt), $tmp);

    optSigCheck($sigs);
}

function optMergePrefixes($m) {
    $limit = 8000;

    $prefix     = $m[1];
    $prefix_len = strlen($prefix);

    $len = $prefix_len;
    $r   = array();

    $suffixes = array();
    foreach (explode("\n", $m[0]) as $line) {

        if (strlen($line) > $limit) {
            $r[] = $line;
            continue;
        }

        $s = substr($line, $prefix_len);
        $len += strlen($s);
        if ($len > $limit) {
            if (count($suffixes) == 1) {
                $r[] = $prefix . $suffixes[0];
            } else {
                $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
            }
            $suffixes = array();
            $len      = $prefix_len + strlen($s);
        }
        $suffixes[] = $s;
    }

    if (!empty($suffixes)) {
        if (count($suffixes) == 1) {
            $r[] = $prefix . $suffixes[0];
        } else {
            $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
        }
    }

    return implode("\n", $r);
}

function optMergePrefixes_Old($m) {
    $prefix     = $m[1];
    $prefix_len = strlen($prefix);

    $suffixes = array();
    foreach (explode("\n", $m[0]) as $line) {
        $suffixes[] = substr($line, $prefix_len);
    }

    return $prefix . '(?:' . implode('|', $suffixes) . ')';
}

/*
 * Checking errors in pattern
 */
function optSigCheck(&$sigs) {
    $result = true;

    foreach ($sigs as $k => $sig) {
        if (trim($sig) == "") {
            if (DEBUG_MODE) {
                echo ("************>>>>> EMPTY\n     pattern: " . $sig . "\n");
            }
            unset($sigs[$k]);
            $result = false;
        }

        if (@preg_match('~' . $sig . '~smiS', '') === false) {
            $error = error_get_last();
            if (DEBUG_MODE) {
                echo ("************>>>>> " . $error['message'] . "\n     pattern: " . $sig . "\n");
            }
            unset($sigs[$k]);
            $result = false;
        }
    }

    return $result;
}

function _hash_($text) {
    static $r;

    if (empty($r)) {
        for ($i = 0; $i < 256; $i++) {
            if ($i < 33 OR $i > 127)
                $r[chr($i)] = '';
        }
    }

    return sha1(strtr($text, $r));
}

function check_whitelist($list, &$snum) {
    global $defaults;

    if (empty($list)) {
        return array();
    }

    $file = dirname(__FILE__) . '/AIBOLIT-WHITELIST.db';
    if (isset($defaults['avdb'])) {
        $file = dirname($defaults['avdb']) . '/AIBOLIT-WHITELIST.db';
    }

    try {
        $db = FileHashMemoryDb::open($file);
    } catch (Exception $e) {
        stdOut("\nAn error occurred while loading the white list database from " . $file . "\n");
        return array();
    }

    $snum = $db->count();
    stdOut("\nLoaded " . ceil($snum) . " known files from " . $file . "\n");

    return $db->find($list);
}

function check_binmalware($hash, $vars) {
    if (isset($vars->blacklist)) {
        return count($vars->blacklist->find(array($hash))) > 0;
    }

    return false;
}

function getSigId($l_Found) {
    foreach ($l_Found as $key => &$v) {
        if (is_string($key) AND $v[1] != -1 AND strlen($key) == 9) {
            return substr($key, 1);
        }
    }

    return null;
}

function die2($str) {
    if (function_exists('aibolit_onFatalError')) {
        aibolit_onFatalError($str);
    }
    die($str);
}

function checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType) {
    global $g_DeMapper;

    if ($l_DeobfType != '') {
        if (DEBUG_MODE) {
            stdOut("\n-----------------------------------------------------------------------------\n");
            stdOut("[DEBUG]" . $l_Filename . "\n");
            var_dump(getFragment($l_Unwrapped, $l_Pos));
            stdOut("\n...... $l_DeobfType ...........\n");
            var_dump($l_Unwrapped);
            stdOut("\n");
        }

        switch ($l_DeobfType) {
            case '_GLOBALS_':
                foreach ($g_DeMapper as $fkey => $fvalue) {
                    if (DEBUG_MODE) {
                        stdOut("[$fkey] => [$fvalue]\n");
                    }

                    if ((strpos($l_Filename, $fkey) !== false) && (strpos($l_Unwrapped, $fvalue) !== false)) {
                        if (DEBUG_MODE) {
                            stdOut("\n[DEBUG] *** SKIP: False Positive\n");
                        }

                        return true;
                    }
                }
                break;
        }


        return false;
    }
}

function convertToUTF8($text)
{
    if (function_exists('mb_convert_encoding')) {
        $text = @mb_convert_encoding($text, 'utf-8', 'auto');
        $text = @mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    return $text;
}

function isFileTooBigForScanWithSignatures($filesize)
{
    return (MAX_SIZE_TO_SCAN > 0 && $filesize > MAX_SIZE_TO_SCAN) || ($filesize < 0);
}

function isFileTooBigForCloudscan($filesize)
{
    return (MAX_SIZE_TO_CLOUDSCAN > 0 && $filesize > MAX_SIZE_TO_CLOUDSCAN) || ($filesize < 0);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// The following instructions should be written the same pattern,
/// because they are replaced by file content while building a release.
/// See the release_aibolit_ru.sh file for details.


class Variables
{
    public $structure = array();
    public $totalFolder = 0;
    public $totalFiles = 0;
    public $adwareList = array();
    public $criticalPHP = array();
    public $phishing = array();
    public $CMS = array();
    public $redirect = array();
    public $redirectPHPFragment = array();
    public $criticalJS = array();
    public $criticalJSFragment = array();
    public $blackFiles = array();
    public $notRead = array();
    public $bigFiles = array();
    public $criticalPHPSig = array();
    public $criticalPHPFragment = array();
    public $phishingSigFragment = array();
    public $phishingFragment = array();
    public $criticalJSSig = array();
    public $adwareListFragment = array();
    public $warningPHPSig = array();
    public $warningPHPFragment = array();
    public $warningPHP = array();
    public $blacklist = array();
    public $vulnerable = array();
    public $crc = 0;

    public $counter = 0;
    public $foundTotalDirs = 0;
    public $foundTotalFiles = 0;
    public $doorway = array();
    public $symLinks = array();
    public $skippedFolders = array();

    public $rescanCount = 0;
}


class CmsVersionDetector
{
    const CMS_BITRIX = 'Bitrix';
    const CMS_WORDPRESS = 'WordPress';
    const CMS_JOOMLA = 'Joomla';
    const CMS_DLE = 'Data Life Engine';
    const CMS_IPB = 'Invision Power Board';
    const CMS_WEBASYST = 'WebAsyst';
    const CMS_OSCOMMERCE = 'OsCommerce';
    const CMS_DRUPAL = 'Drupal';
    const CMS_MODX = 'MODX';
    const CMS_INSTANTCMS = 'Instant CMS';
    const CMS_PHPBB = 'PhpBB';
    const CMS_VBULLETIN = 'vBulletin';
    const CMS_SHOPSCRIPT = 'PHP ShopScript Premium';
    
    const CMS_VERSION_UNDEFINED = '0.0';

    private $root_path;
    private $versions;
    private $types;

    public function __construct($root_path = '.') {
        $this->root_path = $root_path;
        $this->versions  = array();
        $this->types     = array();

        $version = '';

        $dir_list   = $this->getDirList($root_path);
        $dir_list[] = $root_path;

        foreach ($dir_list as $dir) {
            if ($this->checkBitrix($dir, $version)) {
                $this->addCms(self::CMS_BITRIX, $version);
            }

            if ($this->checkWordpress($dir, $version)) {
                $this->addCms(self::CMS_WORDPRESS, $version);
            }

            if ($this->checkJoomla($dir, $version)) {
                $this->addCms(self::CMS_JOOMLA, $version);
            }

            if ($this->checkDle($dir, $version)) {
                $this->addCms(self::CMS_DLE, $version);
            }

            if ($this->checkIpb($dir, $version)) {
                $this->addCms(self::CMS_IPB, $version);
            }

            if ($this->checkWebAsyst($dir, $version)) {
                $this->addCms(self::CMS_WEBASYST, $version);
            }

            if ($this->checkOsCommerce($dir, $version)) {
                $this->addCms(self::CMS_OSCOMMERCE, $version);
            }

            if ($this->checkDrupal($dir, $version)) {
                $this->addCms(self::CMS_DRUPAL, $version);
            }

            if ($this->checkMODX($dir, $version)) {
                $this->addCms(self::CMS_MODX, $version);
            }

            if ($this->checkInstantCms($dir, $version)) {
                $this->addCms(self::CMS_INSTANTCMS, $version);
            }

            if ($this->checkPhpBb($dir, $version)) {
                $this->addCms(self::CMS_PHPBB, $version);
            }

            if ($this->checkVBulletin($dir, $version)) {
                $this->addCms(self::CMS_VBULLETIN, $version);
            }

            if ($this->checkPhpShopScript($dir, $version)) {
                $this->addCms(self::CMS_SHOPSCRIPT, $version);
            }

        }
    }

    function getDirList($target) {
        $remove      = array(
            '.',
            '..'
        );
        $directories = array_diff(scandir($target), $remove);

        $res = array();

        foreach ($directories as $value) {
            if (is_dir($target . '/' . $value)) {
                $res[] = $target . '/' . $value;
            }
        }

        return $res;
    }

    function isCms($name, $version) {
        for ($i = 0; $i < count($this->types); $i++) {
            if ((strpos($this->types[$i], $name) !== false) && (strpos($this->versions[$i], $version) !== false)) {
                return true;
            }
        }

        return false;
    }

    function getCmsList() {
        return $this->types;
    }

    function getCmsVersions() {
        return $this->versions;
    }

    function getCmsNumber() {
        return count($this->types);
    }

    function getCmsName($index = 0) {
        return $this->types[$index];
    }

    function getCmsVersion($index = 0) {
        return $this->versions[$index];
    }

    private function addCms($type, $version) {
        $this->types[]    = $type;
        $this->versions[] = $version;
    }

    private function checkBitrix($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/bitrix')) {
            $res = true;

            $tmp_content = @file_get_contents($this->root_path . '/bitrix/modules/main/classes/general/version.php');
            if (preg_match('|define\("SM_VERSION","(.+?)"\)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkWordpress($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/wp-admin')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/wp-includes/version.php');
            if (preg_match('|\$wp_version\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }
        }

        return $res;
    }

    private function checkJoomla($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/libraries/joomla')) {
            $res = true;

            // for 1.5.x
            $tmp_content = @file_get_contents($dir . '/libraries/joomla/version.php');
            if (preg_match('|var\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|var\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }

            // for 1.7.x
            $tmp_content = @file_get_contents($dir . '/includes/version.php');
            if (preg_match('|public\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|public\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }


            // for 2.5.x and 3.x
            $tmp_content = @file_get_contents($dir . '/libraries/cms/version/version.php');

            if (preg_match('|const\s+RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];

                if (preg_match('|const\s+DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                    $version .= '.' . $tmp_ver[1];
                }
            }

        }

        return $res;
    }

    private function checkDle($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/engine/engine.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/engine/data/config.php');
            if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

            $tmp_content = @file_get_contents($dir . '/install.php');
            if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkIpb($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/ips_kernel')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/ips_kernel/class_xml.php');
            if (preg_match('|IP.Board\s+v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkWebAsyst($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/wbs/installer')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/license.txt');
            if (preg_match('|v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkOsCommerce($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/includes/version.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/includes/version.php');
            if (preg_match('|([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkDrupal($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/sites/all')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/CHANGELOG.txt');
            if (preg_match('|Drupal\s+([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        if (file_exists($dir . '/core/lib/Drupal.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/core/lib/Drupal.php');
            if (preg_match('|VERSION\s*=\s*\'(\d+\.\d+\.\d+)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        if (file_exists($dir . 'modules/system/system.info')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . 'modules/system/system.info');
            if (preg_match('|version\s*=\s*"\d+\.\d+"|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkMODX($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/manager/assets')) {
            $res = true;

            // no way to pick up version
        }

        return $res;
    }

    private function checkInstantCms($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/plugins/p_usertab')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/index.php');
            if (preg_match('|InstantCMS\s+v([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkPhpBb($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/includes/acp')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/config.php');
            if (preg_match('|phpBB\s+([0-9\.x]+)|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }

    private function checkVBulletin($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;
        if (file_exists($dir . '/core/includes/md5_sums_vbulletin.php')) {
            $res = true;
            require_once($dir . '/core/includes/md5_sums_vbulletin.php');
            $version = $md5_sum_versions['vb5_connect'];
        } else if (file_exists($dir . '/includes/md5_sums_vbulletin.php')) {
            $res = true;
            require_once($dir . '/includes/md5_sums_vbulletin.php');
            $version = $md5_sum_versions['vbulletin'];
        }
        return $res;
    }

    private function checkPhpShopScript($dir, &$version) {
        $version = self::CMS_VERSION_UNDEFINED;
        $res     = false;

        if (file_exists($dir . '/install/consts.php')) {
            $res = true;

            $tmp_content = @file_get_contents($dir . '/install/consts.php');
            if (preg_match('|STRING_VERSION\',\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version = $tmp_ver[1];
            }

        }

        return $res;
    }
}


class CloudAssistedRequest
{
    const API_URL = 'https://api.imunify360.com/api/hashes/check';

    private $timeout    = 60;
    private $server_id  = '';

    public function __construct($server_id, $timeout = 60) 
    {
        $this->server_id    = $server_id;
        $this->timeout      = $timeout;
    }

    public function checkFilesByHash($list_of_hashes = array())
    {
        if (empty($list_of_hashes)) {
            return array(
                array(), 
                array(),
                'white' => array(),
                'black' => array(),
            );
        }

        $result = $this->request($list_of_hashes);

        $white  = isset($result['white']) ? $result['white'] : [];
        $black  = isset($result['black']) ? $result['black'] : [];

        return [
            $white,
            $black,
            'white' => $white,
            'black' => $black,
        ];
    }

    private function request($list_of_hashes)
    {
        $url = self::API_URL . '?server_id=' . urlencode($this->server_id) . '&indexed=1';

        $data = array(
            'hashes' => $list_of_hashes,
        );

        $json_hashes = json_encode($data);

        $info = [];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL            , $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , 'GET');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
            curl_setopt($ch, CURLOPT_TIMEOUT        , $this->timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , $this->timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
            curl_setopt($ch, CURLOPT_HTTPHEADER     , array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS     , $json_hashes);
            $response_data  = curl_exec($ch);
            $info           = curl_getinfo($ch);
            $errno          = curl_errno($ch);
            curl_close($ch);
        }
        catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $http_code      = isset($info['http_code']) ? $info['http_code'] : 0;
        if ($http_code !== 200) {
            if ($errno == 28) {
                throw new Exception('Reuqest timeout! Return code: ' . $http_code . ' Curl error num: ' . $errno);
            }
            throw new Exception('Invalid response from the Cloud Assisted server! Return code: ' . $http_code . ' Curl error num: ' . $errno);
        }
        $result = json_decode($response_data, true);
        if (is_null($result)) {
            throw new Exception('Invalid json format in the response!');
        }
        if (isset($result['error'])) {
            throw new Exception('API server returned error!');
        }
        if (!isset($result['result'])) {
            throw new Exception('API server returned error! Cannot find field "result".');
        }

        return $result['result'];
    }
}

class JSONReport
{
    const REPORT_MASK_DOORWAYS  = 1<<2;
    const REPORT_MASK_SUSP      = 1<<3;
    const REPORT_MASK_FULL      = self::REPORT_MASK_DOORWAYS | self::REPORT_MASK_SUSP;
    
    private $raw_report = array();
    private $extended_report;
    private $rapid_account_scan;
    private $ai_extra_warn;
    private $ai_hoster;
    private $report_mask;
    public $noPrefix;
    public $addPrefix;
    public $mnemo;
    
    public function __construct($mnemo, $path, $db_location, $db_meta_info_version, $report_mask, $extended_report, $rapid_account_scan, $ai_version, $ai_hoster, $ai_extra_warn)
    {
        $this->mnemo = $mnemo;
        $this->ai_extra_warn = $ai_extra_warn;
        $this->extended_report = $extended_report;
        $this->rapid_account_scan = $rapid_account_scan;
        $this->ai_hoster = $ai_hoster;
        $this->report_mask = $report_mask;

        $this->raw_report = [];
        $this->raw_report['summary'] = array(
            'scan_path'     => $path,
            'report_time'   => time(),
            'ai_version'    => $ai_version,
            'db_location'   => $db_location,
            'db_version'    => $db_meta_info_version,
        );
    }

    public function addVars($vars, $scan_time)
    {
        $summary_counters                       = array();
        $summary_counters['redirect']           = count($vars->redirect);
        $summary_counters['critical_php']       = count($vars->criticalPHP);
        $summary_counters['critical_js']        = count($vars->criticalJS);
        $summary_counters['phishing']           = count($vars->phishing);
        $summary_counters['unix_exec']          = 0; // count($g_UnixExec);
        $summary_counters['iframes']            = 0; // count($g_Iframer);
        $summary_counters['not_read']           = count($vars->notRead);
        $summary_counters['base64']             = 0; // count($g_Base64);
        $summary_counters['heuristics']         = 0; // count($g_HeuristicDetected);
        $summary_counters['symlinks']           = count($vars->symLinks);
        $summary_counters['big_files_skipped']  = count($vars->bigFiles);
        $summary_counters['suspicious']         = count($vars->warningPHP);

        $this->raw_report['summary']['counters'] = $summary_counters;
        $this->raw_report['summary']['total_files'] = $vars->foundTotalFiles;
        $this->raw_report['summary']['scan_time'] = $scan_time;

        if ($this->extended_report && $this->rapid_account_scan) {
            $this->raw_report['summary']['counters']['rescan_count'] = $vars->rescanCount;
        }

        $this->raw_report['vulners'] = $this->getRawJsonVuln($vars->vulnerable, $vars);

        if (count($vars->criticalPHP) > 0) {
            $this->raw_report['php_malware'] = $this->getRawJson($vars->criticalPHP, $vars, $vars->criticalPHPFragment, $vars->criticalPHPSig);
        }

        if (count($vars->blackFiles) > 0) {
            $this->raw_report['cloudhash'] = $this->getRawBlackData($vars->blackFiles);
        }

        if (count($vars->criticalJS) > 0) {
            $this->raw_report['js_malware'] = $this->getRawJson($vars->criticalJS, $vars, $vars->criticalJSFragment, $vars->criticalJSSig);
        }

        if (count($vars->notRead) > 0) {
            $this->raw_report['not_read'] = $vars->notRead;
        }

        if ($this->ai_hoster) {
            if (count($vars->phishing) > 0) {
                $this->raw_report['phishing'] = $this->getRawJson($vars->phishing, $vars, $vars->phishingFragment, $vars->phishingSigFragment);
            }
            if (count($vars->redirect) > 0) {
                $this->raw_report['redirect'] = $this->getRawJson($vars->redirect, $vars, $vars->redirectPHPFragment);
            }
            if (count($vars->symLinks) > 0) {
                $this->raw_report['sym_links'] = $vars->symLinks;
            }
        }
        else {
            if (count($vars->adwareList) > 0) {
                $this->raw_report['adware'] = $this->getRawJson($vars->adwareList, $vars, $vars->adwareListFragment);
            }
            if (count($vars->bigFiles) > 0) {
                $this->raw_report['big_files'] = $this->getRawJson($vars->bigFiles, $vars);
            }
            if ((count($vars->doorway) > 0) && JSONReport::checkMask($this->report_mask, JSONReport::REPORT_MASK_DOORWAYS)) {
                $this->raw_report['doorway'] = $this->getRawJson($vars->doorway, $vars);
            }
            if (count($vars->CMS) > 0) {
                $this->raw_report['cms'] = $vars->CMS;
            }
        }

        if ($this->ai_extra_warn) {
            if ((count($vars->warningPHP) > 0) && JSONReport::checkMask($this->report_mask, JSONReport::REPORT_MASK_FULL)) {
                $this->raw_report['suspicious'] = $this->getRawJson($vars->warningPHP, $vars, $vars->warningPHPFragment, $vars->warningPHPSig);
            }
        }
    }
    
    public static function checkMask($mask, $need)
    {
        return (($mask & $need) == $need);
    }
    
    public function write($filepath)
    {
        $res = @json_encode($this->raw_report);
        if ($l_FH = fopen($filepath, 'w')) {
            fputs($l_FH, $res);
            fclose($l_FH);
        }
        return $res;
    }
    
    public function writePHPSerialized($filepath)
    {
        if ($l_FH = fopen($filepath, 'w')) {
            fputs($l_FH, serialize($this->raw_report));
            fclose($l_FH);
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    
    private function getRawJsonVuln($par_List, $vars) 
    {
        $results = array();
        $l_Src   = array(
            '&quot;',
            '&lt;',
            '&gt;',
            '&amp;',
            '&#039;',
            '<' . '?php.'
        );
        $l_Dst   = array(
            '"',
            '<',
            '>',
            '&',
            '\'',
            '<' . '?php '
        );

        for ($i = 0; $i < count($par_List); $i++) {
            $l_Pos      = $par_List[$i]['ndx'];

            $fn = $this->addPrefix . str_replace($this->noPrefix, '', $vars->structure['n'][$l_Pos]);
            if (ENCODE_FILENAMES_WITH_BASE64) {
                $res['fn'] = base64_encode($fn);
            } else {
                $res['fn']  = convertToUTF8($fn);
            }

            $res['sig'] = $par_List[$i]['id'];

            $res['ct']    = $vars->structure['c'][$l_Pos];
            $res['mt']    = $vars->structure['m'][$l_Pos];
            $res['et']    = $vars->structure['e'][$l_Pos];
            $res['sz']    = $vars->structure['s'][$l_Pos];
            $res['sigid'] = 'vuln_' . md5($vars->structure['n'][$l_Pos] . $par_List[$i]['id']);

            $results[] = $res;
        }

        return $results;
    }

    private function getRawJson($par_List, $vars, $par_Details = null, $par_SigId = null) 
    {
        global $g_NoPrefix, $g_AddPrefix;
        $results = array();
        $l_Src   = array(
            '&quot;',
            '&lt;',
            '&gt;',
            '&amp;',
            '&#039;',
            '<' . '?php.'
        );
        $l_Dst   = array(
            '"',
            '<',
            '>',
            '&',
            '\'',
            '<' . '?php '
        );

        for ($i = 0; $i < count($par_List); $i++) {
            if ($par_SigId != null) {
                $l_SigId = 'id_' . $par_SigId[$i];
            } else {
                $l_SigId = 'id_n' . rand(1000000, 9000000);
            }

            $l_Pos     = $par_List[$i];

            $fn = $this->addPrefix . str_replace($this->noPrefix, '', $vars->structure['n'][$l_Pos]);
            if (ENCODE_FILENAMES_WITH_BASE64) {
                $res['fn'] = base64_encode($fn);
            } else {
                $res['fn']  = convertToUTF8($fn);
            }

            if ($par_Details != null) {
                $res['sig'] = preg_replace('|(L\d+).+__AI_MARKER__|smi', '[$1]: ...', $par_Details[$i]);
                $res['sig'] = preg_replace('/[^\x20-\x7F]/', '.', $res['sig']);
                $res['sig'] = preg_replace('/__AI_LINE1__(\d+)__AI_LINE2__/', '[$1] ', $res['sig']);
                $res['sig'] = preg_replace('/__AI_MARKER__/', ' @!!!>', $res['sig']);
                $res['sig'] = str_replace($l_Src, $l_Dst, $res['sig']);
            }

            $res['sig'] = convertToUTF8($res['sig']);

            $res['ct']    = $vars->structure['c'][$l_Pos];
            $res['mt']    = $vars->structure['m'][$l_Pos];
            $res['sz']    = $vars->structure['s'][$l_Pos];
            $res['et']    = $vars->structure['e'][$l_Pos];
            $res['hash']  = $vars->structure['crc'][$l_Pos];
            $res['sigid'] = $l_SigId;
            if (isset($vars->structure['sha256'][$l_Pos])) {
                $res['sha256'] = $vars->structure['sha256'][$l_Pos];
            } else {
                $res['sha256'] = '';
            }


            if (isset($par_SigId) && isset($this->mnemo[$par_SigId[$i]])) {
                $res['sn'] = $this->mnemo[$par_SigId[$i]];
            } else {
                $res['sn'] = '';
            }

            $results[] = $res;
        }

        return $results;
    }

    private function getRawBlackData($black_list)
    {
        $result = array();
        foreach ($black_list as $filename => $hash)
        {
            try {
                $stat = stat($filename);
                $sz   = $stat['size'];
                $ct   = $stat['ctime'];
                $mt   = $stat['mtime'];
            }
            catch (Exception $e) {
                continue;
            }

            $result[] = array(
                'fn'    => $filename,
                'sig'   => '',
                'ct'    => $ct,
                'mt'    => $mt,
                'et'    => $hash['ts'],
                'sz'    => $sz,
                'hash'  => $hash['h'],
                'sigid' => crc32($filename),
                'sn'    => 'cld',
            );
        }
        return $result;
    }
}


class CloudAssistedFiles
{
    private $white = [];
    private $black = [];

    public function __construct(CloudAssistedRequest $car, $file_list)
    {
        $list_of_hash       = [];
        $list_of_filepath   = [];
        foreach ($file_list as $filepath)
        {
            if (!file_exists($filepath) || !is_readable($filepath) || is_dir($filepath)) {
                continue;
            }
            try {
                $list_of_hash[]     = hash('sha256', file_get_contents($filepath));
                $list_of_filepath[] = $filepath;
            }
            catch (Exception $e) {
                
            }
        }
        unset($file_list);
        
        try {
            list($white_raw, $black_raw) = $car->checkFilesByHash($list_of_hash);
        }
        catch (Exception $e) {
            throw $e;
        }
        
        $this->white = $this->getListOfFile($white_raw, $list_of_hash, $list_of_filepath);
        $this->black = $this->getListOfFile($black_raw, $list_of_hash, $list_of_filepath);
        
        unset($white_raw);
        unset($black_raw);
        unset($list_of_hash);
        unset($list_of_filepath);
    }
    
    public function getWhiteList()
    {
        return $this->white;
    }

    public function getBlackList()
    {
        return $this->black;
    }
    
    // =========================================================================
    
    private function getListOfFile($data_raw, $list_of_hash, $list_of_filepath)
    {
        $result = [];
        foreach ($data_raw as $index)
        {
            if (!isset($list_of_hash[$index])) {
                continue;
            }
            $result[$list_of_filepath[$index]]['h'] = $list_of_hash[$index];
            $result[$list_of_filepath[$index]]['ts'] = time();
        }
        return $result;
    }    
}


class DetachedMode
{
    protected $workdir;
    protected $scan_id;
    protected $pid_file;
    protected $report_file;
    protected $done_file;
    protected $vars;
    protected $start_time;
    protected $json_report;
    protected $sock_file;

    public function __construct($scan_id, $vars, $listing, $start_time, $json_report, $use_base64, $basedir = '/var/imunify360/aibolit/run', $sock_file = '/var/run/defence360agent/generic_sensor.sock.2')
    {
        $this->scan_id = $scan_id;
        $this->vars = $vars;
        $this->setWorkDir($basedir, $scan_id);
        $this->pid_file = $this->workdir . '/pid';
        $this->report_file = $this->workdir . '/report.json';
        $this->done_file = $this->workdir . '/done';
        $this->start_time = $start_time;
        $this->json_report = $json_report;
        $this->setSocketFile($sock_file);

        $this->checkSpecs($this->workdir, $listing);

        file_put_contents($this->pid_file, strval(getmypid()));

        $this->scan($listing, $use_base64);
        $this->writeReport();
        $this->complete();
    }

    protected function scan($listing, $use_base64)
    {
        $s_file = new SplFileObject($listing);
        $s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        if (function_exists('QCR_GoScan')) {
            QCR_GoScan($s_file, $this->vars, $use_base64, false);
            whitelisting($this->vars);
        }
        unset($s_file);
    }

    protected function checkSpecs($workdir, $listing)
    {
        if (!file_exists($workdir) && !mkdir($workdir)) {
            die('Error! Cannot create workdir ' . $workdir . ' for detached scan.');
        } elseif (file_exists($workdir) && !is_writable($workdir)) {
            die('Error! Workdir ' . $workdir . ' is not writable.');
        } elseif (!file_exists($listing) || !is_readable($listing)) {
            die('Error! Listing file ' . $listing . ' not exists or not readable');
        }
    }

    protected function writeReport()
    {
        $scan_time = round(microtime(true) - $this->start_time, 1);
        $json_report = $this->json_report->call($this);
        $json_report->addVars($this->vars, $scan_time);
        $json_report->write($this->report_file);
    }

    protected function complete()
    {
        @touch($this->done_file);
        $complete = array(
            'method' => 'MALWARE_SCAN_COMPLETE',
            'scan_id' => $this->scan_id,
        );
        $json_complete = json_encode($complete) . "\n";
        $socket = fsockopen('unix://' . $this->sock_file);
        stream_set_blocking($socket, false);
        fwrite($socket, $json_complete);
        fclose($socket);
    }

    protected function setWorkDir($dir, $scan_id)
    {
        $this->workdir = $dir . '/' . $scan_id;
    }

    protected function setSocketFile($sock)
    {
        $this->sock_file = $sock;
    }
}


/**
 * Class ResidentMode used to stay aibolit alive in memory and wait for a job.
 */
class ResidentMode
{
    /**
     * parent dir for all resident aibolit related
     * @var string
     */
    protected $resident_dir;
    /**
     * directory for all jobs to be processed by aibolit
     * @var string
     */
    protected $resident_in_dir;
    /**
     * directory with all the malicious files reports to be processed by imunify
     * @var string
     */
    protected $resident_out_dir;
    /**
     * resident aibolit pid
     * @var string
     */
    protected $aibolit_pid;
    /**
     * file lock used to make sure we start only one aibolit
     * @var string
     */
    protected $aibolit_start_lock;
    /**
     * status file used to make sure aibolit didn't get stuck
     * @var string
     */
    protected $aibolit_status_file;
    /**
     * number of seconds while aibolit will stay alive, while not receiving any work
     * @var int
     */
    protected $stay_alive;
    /**
     * maximum number of seconds without updating ABOLIT_STATUS_FILE,
     * used to track if AIBOLIT is stuck, should be killed
     * @var int
     */
    protected $stuck_timeout;
    /**
     * number of seconds scripts would wait for aibolit to finish / send signal
     * @var int
     */
    protected $upload_timeout;
    /**
     * max number of files to pick
     * @var int
     */
    protected $max_files_per_notify_scan;
    /**
     * timestamp of last scan
     * @var int
     */
    protected $last_scan_time;
    /**
     * time to sleep between lifecycle iterations in microseconds
     */
    protected $sleep_time;

    protected $scannedNotify = 0;

    protected $report;

    protected $resident_in_dir_notify;
    protected $resident_in_dir_upload;
    protected $blacklist;
    protected $watchdog_socket;
    protected $activation_socket;
    protected $systemd = false;
    protected $interval = 0;
    protected $lastKeepAlive = 0;

    /**
     * ResidentMode constructor.
     * @param $options
     */
    public function __construct(
        Closure $report,
        $blacklist = null,
        $resident_dir = '/var/imunify360/aibolit/resident',
        $stay_alive = 30,
        $stuck_timeout = 5,
        $upload_timeout = 10,
        $max_files_per_notify_scan = 500,
        $sleep_time = 100000
    ) {
        $this->setResidentDir($resident_dir);
        $this->resident_in_dir = $this->resident_dir . '/in';
        $this->resident_in_dir_upload = $this->resident_in_dir . '/upload-jobs';
        $this->resident_in_dir_notify = $this->resident_in_dir . '/notify-jobs';
        $this->resident_out_dir = $this->resident_dir . '/out';
        $this->aibolit_pid = $this->resident_dir . '/aibolit.pid';
        $this->aibolit_start_lock = $this->resident_dir . '/start.lock';
        $this->aibolit_status_file = $this->resident_dir . '/aibolit.status';
        $this->stay_alive = $stay_alive;
        $this->stuck_timeout = $stuck_timeout;
        $this->upload_timeout = $upload_timeout;
        /** @var int $max_files_per_notify_scan */
        if (!empty($max_files_per_notify_scan)) {
            $this->max_files_per_notify_scan = $max_files_per_notify_scan;
        }
        $this->sleep_time = $sleep_time;
        $this->report = $report;
        $this->blacklist = $blacklist;

        umask(0);
        if (!file_exists($this->resident_dir)) {
            mkdir($this->resident_dir, 0777, true);
        }
        if (!file_exists($this->resident_in_dir)) {
            mkdir($this->resident_in_dir, 0755);
        }
        if (!file_exists($this->resident_out_dir)) {
            mkdir($this->resident_out_dir, 0755);
        }
        if (!file_exists($this->resident_in_dir_notify)) {
            mkdir($this->resident_in_dir_notify, 0700);
        }
        if (!file_exists($this->resident_in_dir_upload)) {
            mkdir($this->resident_in_dir_upload, 01777);
        }

        $this->checkSpecs();

        $addr = getenv('NOTIFY_SOCKET');
        if ($addr[0] == '@') {
            $addr = "\0";
        }

        if ($addr) {
            $this->systemd = true;
        }

        if ($this->systemd) {
            $this->watchdog_socket = fsockopen('udg://' . $addr);
            stream_set_blocking($this->watchdog_socket, false);

            $this->activation_socket = fopen('php://fd/3', 'r');
            if ($this->activation_socket === false) {
                die("Something went wrong with activation socket.");
            }
            stream_set_blocking($this->activation_socket, false);

            if (getenv('WATCHDOG_USEC') !== false) {
                $this->interval = intval(getenv('WATCHDOG_USEC'));
            } else {
                $this->interval = 1000000;
            }
        }
        $this->lifeCycle();
    }

    protected function isRootWriteable($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $owner_id = (int)fileowner($folder);
        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid($owner_id);
            if (!isset($owner['name']) || $owner['name'] !== 'root') {
                return false;
            }
        } elseif ($owner_id != 0) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0100)                           // owner r
            && ($perms & 0x0080)                        // owner w
            && ($perms & 0x0040) && !($perms & 0x0800)  // owner x
            && !($perms & 0x0010)                       // group without w
            && !($perms & 0x0002)                       // other without w
        ) {
            return true;
        }
        return false;
    }

    protected function isWorldWriteable($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0004)                           // other r
            && ($perms & 0x0002)                        // other w
            && ($perms & 0x0200)                        // sticky bit
        ) {
            return true;
        }
        return false;
    }

    protected function checkSpecs()
    {
        if (!extension_loaded('posix')) {
            die('Error! For resident scan need posix extension.');
        } elseif (!$this->isRootWriteable($this->resident_in_dir_notify)) {
            die('Error! Notify in dir ' . $this->resident_in_dir_notify . ' must be root writeable.');
        } elseif (!$this->isWorldWriteable($this->resident_in_dir_upload)) {
            die('Error! Upload in dir ' . $this->resident_in_dir_upload . ' must be world writeable.');
        }
    }

    protected function setResidentDir($dir)
    {
        $this->resident_dir = $dir;
    }

    protected function writeReport($vars, $scan_time, $type, $file)
    {
        $report = $this->report->call($this);
        $malware = (count($vars->criticalPHP) > 0)
            || (count($vars->criticalJS) > 0)
            || (count($vars->blackFiles) > 0)
            || (count($vars->warningPHP) > 0);

        if ($type == 'upload') {
            $pid = intval(basename($file, '.upload_job'));
            if ($malware) {
                posix_kill($pid, SIGUSR1);
            } else {
                posix_kill($pid, SIGUSR2);
            }
        } elseif ($type == 'notify' && $malware) {
            $filename = basename($file, '.notify_job');
            $report->addVars($vars, $scan_time);
            $report->write($this->resident_out_dir . '/' . $filename . '.report.tmp');
            @rename($this->resident_out_dir . '/' . $filename . '.report.tmp', $this->resident_out_dir . '/' . $filename . '.report');
            unset($report);
        }
    }

    protected function isJobFileExists($pattern)
    {
        if (count(glob($this->resident_in_dir . $pattern)) > 0) {
            return true;
        }
        return false;
    }

    protected function isUploadJob()
    {
        if ($this->isJobFileExists('/upload-jobs/*.upload_job')) {
            return true;
        }
        return false;
    }

    protected function scanJob($job_file, $type)
    {
        $start_time = microtime(true);

        $vars = new Variables();
        $vars->blacklist = $this->blacklist;

        $files_to_scan = array();
        $count = 0;

        $job = json_decode(file_get_contents($job_file));

        if ($type == 'notify') {
            $files_to_scan = $job->files;
            $count = count($files_to_scan);

            if ($count > $this->max_files_per_notify_scan) {
                // TODO: show a warning: too many files to scan, the job was skipped
                return true;
            }

            if ($this->scannedNotify + $count > $this->max_files_per_notify_scan) {
                $this->scannedNotify = 0;
                unset($vars);
                unset($files_to_scan);
                return false;
            } else {
                $this->scannedNotify += $count;
            }
        } elseif ($type == 'upload') {
            $files_to_scan = $job->files;
            $count = count($files_to_scan);

            if ($count > 1) {
                // TODO: show a warning: too many files to scan, the job was skipped
                return true;
            }
        }

        $vars->foundTotalFiles = $count;

        if (function_exists('QCR_GoScan')) {
            if ($this->systemd) {
                QCR_GoScan($files_to_scan, $vars, array($this, 'keepAlive'), true, false);
            } else {
                QCR_GoScan($files_to_scan, $vars, null, true, false);
            }

            whitelisting($vars);
        }

        $scan_time = round(microtime(true) - $start_time, 1);
        $this->writeReport($vars, $scan_time, $type, $job_file);

        unset($vars);
        unset($files_to_scan);

        if (defined('PROGRESS_LOG_FILE') && file_exists(PROGRESS_LOG_FILE)) {
            @unlink(PROGRESS_LOG_FILE);
        }

        if (defined('CREATE_SHARED_MEMORY') && CREATE_SHARED_MEMORY) {
            shmop_delete(SHARED_MEMORY);
        }

        if (defined('SHARED_MEMORY')) {
            shmop_close(SHARED_MEMORY);
        }

        return true;
    }

    protected function isNotifyJob()
    {
        if ($this->isJobFileExists('/notify-jobs/*.notify_job')) {
            return true;
        }
        return false;
    }

    protected function scanUploadJob()
    {
        $files = glob($this->resident_in_dir_upload . '/*.upload_job');
        $this->scanJob($files[0], 'upload');
        unlink($files[0]);
    }

    protected function scanNotifyJob()
    {
        $files = glob($this->resident_in_dir_notify . '/*.notify_job');
        foreach ($files as $job) {
            $res = $this->scanJob($job, 'notify');
            if ($res) {
                unlink($job);
            } else {
                break;
            }
        }
    }

    public function keepAlive()
    {
        if (intval((microtime(true) - $this->lastKeepAlive) * 1000000) > $this->interval / 2) {
            while (fread($this->activation_socket, 1024)) {
                // do nothing but read all dat from the socket
            }
            fwrite($this->watchdog_socket, 'WATCHDOG=1');
            $this->lastKeepAlive = microtime(true);
        }
    }

    protected function lifeCycle()
    {
        $this->last_scan_time = time();
        while (true) {
            if ($this->systemd) {
                $this->keepAlive();
            }
            while ($this->isUploadJob()) {
                $this->last_scan_time = time();
                $this->scanUploadJob();
            }

            while ($this->isNotifyJob() && !$this->isUploadJob()) {
                $this->last_scan_time = time();
                $this->scanNotifyJob();
            }
            if ($this->last_scan_time + $this->stay_alive < time()) {
                break;
            }
            touch($this->aibolit_status_file);
            usleep($this->sleep_time); // 1\10 of second by default
        }
        if ($this->systemd) {
            fclose($this->watchdog_socket);
            fclose($this->activation_socket);
        }
        unlink($this->aibolit_status_file);
    }
}


/**
 * Class FileHashMemoryDb.
 *
 * Implements operations to load the file hash database into memory and work with it.
 */
class FileHashMemoryDb
{
    const HEADER_SIZE = 1024;
    const ROW_SIZE = 20;

    /**
     * @var int
     */
    private $count;
    /**
     * @var array
     */
    private $header;
    /**
     * @var resource
     */
    private $fp;
    /**
     * @var array
     */
    private $data;

    /**
     * Creates a new DB file and open it.
     *
     * @param $filepath
     * @return FileHashMemoryDb
     * @throws Exception
     */
    public static function create($filepath)
    {
        if (file_exists($filepath)) {
            throw new Exception('File \'' . $filepath . '\' already exists.');
        }

        $value = pack('V', 0);
        $header = array_fill(0, 256, $value);
        file_put_contents($filepath, implode($header));

        return new self($filepath);
    }

    /**
     * Opens a particular DB file.
     *
     * @param $filepath
     * @return FileHashMemoryDb
     * @throws Exception
     */
    public static function open($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception('File \'' . $filepath . '\' does not exist.');
        }

        return new self($filepath);
    }

    /**
     * FileHashMemoryDb constructor.
     *
     * @param mixed $filepath
     * @throws Exception
     */
    private function __construct($filepath)
    {
        $this->fp = fopen($filepath, 'rb');

        if (false === $this->fp) {
            throw new Exception('File \'' . $filepath . '\' can not be opened.');
        }

        try {
            $this->header = unpack('V256', fread($this->fp, self::HEADER_SIZE));
            $this->count = (int) (max(0, filesize($filepath) - self::HEADER_SIZE) / self::ROW_SIZE);
            foreach ($this->header as $chunk_id => $chunk_size) {
                if ($chunk_size > 0) {
                    $str = fread($this->fp, $chunk_size);
                } else {
                    $str = '';
                }
                $this->data[$chunk_id] = $str;
            }
        } catch (Exception $e) {
            throw new Exception('File \'' . $filepath . '\' is not a valid DB file. An original error: \'' . $e->getMessage() . '\'');
        }
    }

    /**
     * Calculates and returns number of hashes stored in a loaded database.
     *
     * @return int number of hashes stored in a DB
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Find hashes in a DB.
     *
     * @param array $list of hashes to find in a DB
     * @return array list of hashes from the $list parameter that are found in a DB
     */
    public function find($list)
    {
        sort($list);

        $hash = reset($list);

        $found = array();

        foreach ($this->header as $chunk_id => $chunk_size) {
            if ($chunk_size > 0) {
                $str = $this->data[$chunk_id];

                do {
                    $raw = pack("H*", $hash);
                    $id  = ord($raw[0]) + 1;

                    if ($chunk_id == $id AND $this->binarySearch($str, $raw)) {
                        $found[] = $hash;
                    }

                } while ($chunk_id >= $id AND $hash = next($list));

                if ($hash === false) {
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Searches $item in the $str using an implementation of the binary search algorithm.
     *
     * @param $str
     * @param $item
     * @return bool
     */
    private function binarySearch($str, $item) {
        $item_size = strlen($item);
        if ($item_size == 0) {
            return false;
        }

        $first = 0;

        $last = floor(strlen($str) / $item_size);

        while ($first < $last) {
            $mid = $first + (($last - $first) >> 1);
            $b   = substr($str, $mid * $item_size, $item_size);
            if (strcmp($item, $b) <= 0) {
                $last = $mid;
            } else {
                $first = $mid + 1;
            }
        }

        $b = substr($str, $last * $item_size, $item_size);
        if ($b == $item) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * FileHashDB destructor.
     */
    public function __destruct()
    {
        fclose($this->fp);
    }
}

class FilepathEscaper
{
    public static function encodeFilepath($filepath)
    {
        return str_replace(array('\\', "\n", "\r"), array('\\\\', '\\n', '\\r'), $filepath);
    }
    
    public static function decodeFilepath($filepath)
    {
        return preg_replace_callback('~(\\\\+)(.)~', function ($matches) {
            $count = strlen($matches[1]);
            if ($count % 2 === 0) {
                return str_repeat('\\', $count/2) . $matches[2];
            }
            return str_repeat('\\', floor($count/2)) . stripcslashes('\\' . $matches[2]);
        }, $filepath);
    }
    
    public static function encodeFilepathByBase64($filepath)
    {
        return base64_encode($filepath);
    }
    
    public static function decodeFilepathByBase64($filepath_base64)
    {
        return base64_decode($filepath_base64);
    }
}


/**
 * Class RapidScanStorageRecord.
 *
 * Implements db record for RapidScan
 */
class RapidScanStorageRecord
{
    const WHITE = 1; // white listed file in cloud db
    const BLACK = 6; // black listed file in cloud db
    const DUAL_USE = 2; // dual used listed file in cloud db
    const UNKNOWN = 3; // unknown file --> file not listed in cloud db
    const HEURISTIC = 4; //detected as heuristic
    const CONFLICT = 5; // we have filename hashing conflict for this file
    const NEWFILE = 0; // this is a new file (or content changed)
    const RX_MALWARE = 7; // detected as malware by rx scan
    const RX_SUSPICIOUS = 8; // detected as suspicious by rx scan
    const RX_GOOD = 9; // detected as good by rx scan

    /**
     * @var string;
     */
    private $filename;
    /**
     * @var int
     */
    private $key;
    /**
     * @var int
     */
    private $updated_ts;
    /**
     * @var int
     */
    private $verdict;
    /**
     * @var string
     */
    private $sha2;
    /**
     * @var string
     */
    private $signature = '';
    /**
     * @var string
     */
    private $snippet = '';

    /**
     * RapidScanStorageRecord constructor.
     * @param $key
     * @param $updated_ts
     * @param int $verdict
     * @param $sha2
     * @param string $signature
     */
    private function __construct($key, $updated_ts, $verdict, $sha2, $signature, $filename, $snippet)
    {
        $this->filename = $filename;
        $this->key = $key;
        $this->updated_ts = $updated_ts;
        $this->verdict = $verdict;
        $this->sha2 = $sha2;
        $this->snippet = $snippet;
        if ($signature!=='') {
            $this->signature = self::padTo10Bytes($signature);
        }
    }

    /**
     * Create db storage record from file
     * @param $filename
     * @param string $signature
     * @param int $verdict
     * @return RapidScanStorageRecord
     * @throws Exception
     */
    public static function fromFile($filename, $signature = '', $verdict = self::UNKNOWN, $snippet = '')
    {
        if (!file_exists($filename)) {
            throw new Exception('File \'' . $filename . '\' doesn\'t exists.');
        }

        $key = intval(strval(self::fileNameHash($filename)) . strval(fileinode($filename)));
        $updated_ts = max(filemtime($filename), filectime($filename));
        $sha2 = '';
        if (!$verdict) {
            $verdict = self::NEWFILE;
        }
        if ($signature!=='') {
            $signature = self::padTo10Bytes($signature);
        }
        return new self($key, $updated_ts, $verdict, $sha2, $signature, $filename, $snippet);
    }

    /**
     * @param $array
     * @return RapidScanStorageRecord
     */
    public static function fromArray($array)
    {
        $key = $array['key'];
        $updated_ts = $array['updated_ts'];
        $sha2 = hex2bin($array['sha2']);
        $verdict = $array['verdict'];
        $signature = $array['signature'];
        return new self($key, $updated_ts, $verdict, $sha2, $signature, '', '');
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array['key'] = $this->key;
        $array['updated_ts'] = $this->updated_ts;
        $array['verdict'] = $this->verdict;
        $array['sha2'] = bin2hex($this->sha2);
        $array['signature'] = $this->signature;
        return $array;
    }

    /**
     * @return array
     */
    public function calcSha2()
    {
        $this->sha2 = hash('sha256', file_get_contents($this->filename), true);
    }

    /**
     * @param $verdict
     */
    public function setVerdict($verdict)
    {
        $this->verdict = $verdict;
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param $signature
     */
    public function setSignature($signature)
    {
        if ($signature!=='') {
            $this->signature = self::padTo10Bytes($signature);
        }
    }

    /**
     * Unpack bytestring $value to RapidScanStorageRecord
     * @param $hash
     * @param $value
     * @return RapidScanStorageRecord
     */
    public static function unpack($hash, $value)
    {
        // pack format
        // 8 bytes timestamp
        // 1 byte verdict
        // 32 bytes sha2
        // 10 bytes signature (only for BLACK, DUAL_USE, RX_MALWARE, RX_SUSPICIOUS)
        // note - we will hold bloomfilter for file later on for those that are WHITE
        // it will be used to detect installed apps

        $signature = '';
        $timestamp = unpack("l", substr($value, 0, 8));
        $updated_ts = array_pop($timestamp);
        $verdict = $value[8];
        $verdict = intval(bin2hex($verdict));
        $sha2 = substr($value, 9, 32);
        if (in_array($verdict, array(self::BLACK, self::DUAL_USE, self::RX_MALWARE, self::RX_SUSPICIOUS))) {
            $signature = substr($value, 41, 10);  # 10 bytes signature string
        }
        if (strlen($value) > 51) {
            $snippet = substr($value, 51);
        } else {
            $snippet = '';
        }
        return new self($hash, $updated_ts, $verdict, $sha2, $signature, '', $snippet);
    }

    /**
     * Pack RapidScanStorageRecord to bytestring to save in db
     * @return string
     */
    public function pack()
    {
        $signature = '';
        if (strlen($this->signature) > 0) {
            $signature = $this->signature;
        }
        return (($this->updated_ts < 0) ? str_pad(pack("l", $this->updated_ts), 8, "\xff") : str_pad(pack("l", $this->updated_ts), 8, "\x00")) . pack("c", $this->verdict) . $this->sha2 . $signature . $this->snippet;
    }

    /**
     * Hash function for create hash of full filename to store in db as key
     * @param $str
     * @return int
     */
    private static function fileNameHash($str)
    {
        for ($i = 0, $h = 5381, $len = strlen($str); $i < $len; $i++) {
            $h = (($h << 5) + $h + ord($str[$i])) & 0x7FFFFFFF;
        }
        return $h;
    }

    /**
     * Convert string to utf-8 and fitting/padding it to 10 bytes
     * @param $str
     * @return string
     */
    private static function padTo10Bytes($str)
    {
        # convert string to bytes in UTF8, and add 0 bytes to pad it to 10
        # cut to 10 bytes of necessary
        $str = utf8_encode($str);
        $len = strlen($str);
        if ($len < 10) {
            $str = str_pad($str, 10, "\x00");
        } elseif ($len > 10) {
            $str = substr($str, 0, 10);
        }
        return $str;
    }

    /**
     * @return int
     */
    public function getUpdatedTs()
    {
        return $this->updated_ts;
    }

    /**
     * @return int
     */
    public function getVerdict()
    {
        return $this->verdict;
    }

    /**
     * @return string
     */
    public function getSha2()
    {
        return $this->sha2;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @param $filename
     */
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    }
}


/**
 * Interface RapidScanStorage implements class to work with RapidScan db
 * @package Aibolit\Lib\Scantrack
 */
class RapidScanStorage
{
    /**
     * @var string
     */
    protected $old_dir;
    /**
     * @var string
     */
    protected $new_dir;
    /**
     * @var resource
     */
    protected $new_db;
    /**
     * @var resource
     */
    protected $old_db;
    /**
     * @var resource
     */
    private $wb;
    /**
     * @var int
     */
    public $batch_count;

    /**
     * RapidScanStorage constructor.
     * @param $base - folder where db located
     */
    public function __construct($base)
    {
        if(!is_dir($base)) mkdir($base);
        $this->old_dir = $base . '/current';
        $this->new_dir = $base . '/new';
        $options = array('create_if_missing' => true, 'compression'=> LEVELDB_NO_COMPRESSION);

        $this->new_db = new LevelDB($this->new_dir, $options);
        $this->old_db = new LevelDB($this->old_dir, $options);

        $this->wb = NULL;  // will be use to track writing to batch
        $this->batch_count = 0;
    }

    /**
     * @param RapidScanStorageRecord $record
     * @return bool
     */
    public function put(RapidScanStorageRecord $record)
    {
        $this->startBatch();
        $this->batch_count++;
        $value = $this->wb->put($record->getKey(), $record->pack());
        return $value;
    }

    /**
     * @param $hash
     * @return bool|RapidScanStorageRecord
     */
    public function getNew($hash)
    {
        $value = $this->new_db->get($hash);
        if($value) {
            $return = RapidScanStorageRecord::unpack($hash, $value);
            return $return;
        }
        return false;
    }

    /**
     * @param $hash
     * @return bool|RapidScanStorageRecord
     */
    public function getOld($hash)
    {
        $value = $this->old_db->get($hash);
        if($value) {
            $return = RapidScanStorageRecord::unpack($hash, $value);
            return $return;
        }
        return false;
    }

    /**
     * @param $hash
     * @return bool
     */
    public function delete($hash)
    {
        $return = $this->new_db->delete($hash);
        return $return;
    }

    /**
     * Close db, remove old db, move new to a new space
     */
    public function finish()
    {
        $this->old_db->close();
        $this->flushBatch();
        $this->new_db->close();

        self::rmtree($this->old_dir);
        rename($this->new_dir, $this->old_dir);
    }

    /**
     * Start batch operations
     */
    private function startBatch()
    {
        if(!$this->wb) {
            $this->wb = new LevelDBWriteBatch();
            $this->batch_count = 0;
        }
    }

    /**
     *  write all data in a batch, reset batch
     */
    public function flushBatch()
    {
        if ($this->wb) {
            $this->new_db->write($this->wb);
            $this->batch_count = 0;
            $this->wb = NULL;
        }
    }
    /**
     * Helper function to remove folder tree
     * @param $path
     */
    public static function rmTree($path)
    {
        if (is_dir($path)) {
            foreach (scandir($path) as $name) {
                if (in_array($name, array('.', '..'))) {
                    continue;
                }
                $subpath = $path.DIRECTORY_SEPARATOR . $name;
                self::rmTree($subpath);
            }
            rmdir($path);
        } else {
            unlink($path);
        }
    }
}

/**
 * This is actual class that does account level scan
 * and remembers the state of scan
 * Class RapidAccountScan
 * @package Aibolit\Lib\Scantrack
 */
class RapidAccountScan
{
    const RESCAN_ALL = 0; // mode of operation --> rescan all files that are not white/black/dual_use using cloud scanner & regex scanner
    const RESCAN_NONE = 1; // don't re-scan any files that we already scanned
    const RESCAN_SUSPICIOUS = 2; // only re-scan suspicious files using only regex scanner

    const MAX_BATCH = 1000; // max files to write in a db batch write
    const MAX_TO_SCAN = 1000; // max files to scan using cloud/rx scanner at a time

    private $db;
    private $vars = null;
    private $scanlist;
    private $collisions;
    private $processedFiles;
    private $rescan_count = 0;
    private $counter = 0;
    private $str_error = false;

    /**
     * RapidAccountScan constructor.
     * @param RapidScanStorage $rapidScanStorage
     */
    public function __construct($rapidScanStorage, &$vars, $counter = 0)
    {
        $this->db = $rapidScanStorage;
        $this->vars = $vars;
        $this->scanlist = array();
        $this->collisions = array();
        $this->processedFiles = 0;
        $this->counter = $counter;
    }

    /**
     * Get str error
     */
    public function getStrError()
    {
        return $this->str_error;
    }

    /**
     * Get count of rescan(regexp) files
     */
    public function getRescanCount()
    {
        return $this->rescan_count;
    }

    /**
     * placeholder for actual regex scan
     * return RX_GOOD, RX_MALWARE, RX_SUSPICIOUS and signature from regex scaner
     * if we got one
     */
    private function regexScan($filename, $i, $vars)
    {
        $this->rescan_count++;
        printProgress(++$this->processedFiles, $filename, $vars);
        $return = QCR_ScanFile($filename, $vars, null, $i, false);
        return $return;
    }

    /**
     * we will have batch of new files that we will scan
     * here we will write them into db once we scanned them
     * we need to check that there is no conflicts/collisions
     * in names, for that we check for data in db if such filename_hash
     * already exists, but we also keep set of filename_hashes of given
     * batch, to rule out conflicts in current batch as well
     */
    private function writeNew()
    {
        $this->collisions = array();
        foreach ($this->scanlist as $fileinfo) {
            if (in_array($fileinfo->getKey(), $this->collisions) || $this->db->getNew($fileinfo->getKey())) {
                $fileinfo->setVerdict(RapidScanStorageRecord::CONFLICT);
            }
            $this->collisions [] = $fileinfo->getKey();
            $this->db->put($fileinfo);
        }
    }

    /**
     * given a batch do cloudscan
     * @throws \Exception
     */
    private function doCloudScan()
    {
        if (count($this->scanlist) <= 0) {
            return;
        }

        $index_table = array();
        $blackfiles = array();

        $sha_list = array();

        foreach ($this->scanlist as $i => $fileinfo) {
            $sha_list[] = bin2hex($fileinfo->getSha2());
            $index_table[] = $i;
            $fileinfo->setVerdict(RapidScanStorageRecord::UNKNOWN);
        }

        $ca = Factory::instance()->create(CloudAssistedRequest::class, [CLOUD_ASSIST_TOKEN]);

        $white_raw = array();
        $black_raw = array();
        try {
            list($white_raw, $black_raw) = $ca->checkFilesByHash($sha_list);
        } catch (\Exception $e) {
            $this->str_error = $e->getMessage();
        }

        $dual = array_intersect($white_raw, $black_raw);

        foreach ($white_raw as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::WHITE);
        }

        foreach ($black_raw as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::BLACK);
            $this->scanlist[$index_table[$index]]->setSignature('BLACK'); //later on we will get sig info from cloud
            $blackfiles[$this->scanlist[$index_table[$index]]->getFilename()] = $sha_list[$index];
        }

        foreach ($dual as $index) {
            $this->scanlist[$index_table[$index]]->setVerdict(RapidScanStorageRecord::DUAL_USE);
            $this->scanlist[$index_table[$index]]->setSignature('DUAL'); //later on we will get sig info from cloud
        }

        // we can now update verdicts in batch for those that we know
        //add entries to report, when needed

        $this->vars->blackFiles = array_merge($this->vars->blackFiles, $blackfiles);

        unset($white_raw);
        unset($black_raw);
        unset($dual);
        unset($sha_list);
        unset($index_table);
    }

    /**
     * regex scan a single file, add entry to report if needed
     * @param $fileInfo
     * @param $i
     */
    private function _regexScan($fileInfo, $i, $vars)
    {
        $regex_res = $this->regexScan($fileInfo->getFilename(), $i, $vars);
        if (!is_array($regex_res)) {
            return;
        }
        list($result, $sigId, $snippet) = $regex_res;
        $fileInfo->setVerdict($result);
        if ($result !== RapidScanStorageRecord::RX_GOOD) {
            $fileInfo->setSignature($sigId);
            $fileInfo->setSnippet($snippet);
        }
    }

    /**
     * regex scan batch of files.
     */
    private function doRegexScan($vars)
    {
        foreach ($this->scanlist as $i => $fileinfo) {
            if (!in_array($fileinfo->getVerdict(), array(
                RapidScanStorageRecord::WHITE,
                RapidScanStorageRecord::BLACK,
                RapidScanStorageRecord::DUAL_USE
            ))
            ) {
                $this->_regexScan($fileinfo, $i, $vars);
            }
        }
    }

    private function processScanList($vars)
    {
        $this->doCloudScan();
        $this->doRegexScan($vars);
        $this->writeNew();
        $this->scanlist = array();
    }

    private function scanFile($filename, $rescan, $i, $vars)
    {
        global $g_Mnemo;

        if (!file_exists($filename)) {
            return false;
        }
        $file = RapidScanStorageRecord::fromFile($filename);

        $old_value = $this->db->getOld($file->getKey());
        $old_mtime = 0;
        if ($old_value) {
            $old_mtime = $old_value->getUpdatedTs();
            if ($file->getUpdatedTs() == $old_mtime) {
                $file = $old_value;
                $file->setFilename($filename);
            }
        }

        if ($file->getVerdict() == RapidScanStorageRecord::UNKNOWN
            || $file->getVerdict() == RapidScanStorageRecord::CONFLICT
            || $file->getUpdatedTs() !== $old_mtime
        ) {
            // these files has changed or we know nothing about them, lets re-calculate sha2
            // and do full scan
            $file->calcSha2();
            $file->setVerdict(RapidScanStorageRecord::NEWFILE);
            $this->scanlist[$i] = $file;
        } elseif ($file->getVerdict() == RapidScanStorageRecord::BLACK
            || $file->getVerdict() == RapidScanStorageRecord::DUAL_USE
        ) {
            //these files hasn't changed, but need to be reported as they are on one of the lists
            $this->vars->blackFiles[$filename] = bin2hex($file->getSha2());
            $this->db->put($file);
        } elseif (($rescan == self::RESCAN_SUSPICIOUS || $rescan == self::RESCAN_NONE)
            && $file->getVerdict() == RapidScanStorageRecord::RX_MALWARE
        ) {
            //this files were detected as rx malware before, let's report them

            $sigId = trim($file->getSignature(), "\0");

            if (isset($sigId) && isset($g_Mnemo[$sigId])) {
                $sigName = $g_Mnemo[$sigId];
                $snippet = $file->getSnippet();
                if (strpos($sigName, 'SUS') !== false && AI_EXTRA_WARN) {
                    $vars->warningPHP[] = $i;
                    $vars->warningPHPFragment[] = $snippet;
                    $vars->warningPHPSig[] = $sigId;
                } elseif (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'js') {
                    $vars->criticalJS[] = $i;
                    $vars->criticalJSFragment[] = $snippet;
                    $vars->criticalJSSig[] = $sigId;
                } else {
                    $vars->criticalPHP[] = $i;
                    $vars->criticalPHPFragment[] = $snippet;
                    $vars->criticalPHPSig[] = $sigId;
                }
                AddResult($filename, $i, $vars);
                $this->db->put($file);
            } else {
                $this->scanlist[$i] = $file;
            }
        } elseif ((
                $rescan == self::RESCAN_ALL
                && in_array($file->getVerdict(), array(
                    RapidScanStorageRecord::RX_SUSPICIOUS,
                    RapidScanStorageRecord::RX_GOOD,
                    RapidScanStorageRecord::RX_MALWARE
                ))
            )
            || (
                $rescan == self::RESCAN_SUSPICIOUS
                && $file->getVerdict() == RapidScanStorageRecord::RX_SUSPICIOUS
            )
        ) {
            //rescan all mode, all none white/black/dual listed files need to be re-scanned fully

            $this->scanlist[$i] = $file;
        } else {
            //in theory -- we should have only white files here...
            $this->db->put($file);
        }

        if (count($this->scanlist) >= self::MAX_TO_SCAN) {
            // our scan list is big enough
            // let's flush db, and scan the list
            $this->db->flushBatch();
            $this->processScanList($vars);
        }

        if ($this->db->batch_count >= self::MAX_BATCH) {
            //we have added many entries to db, time to flush it
            $this->db->flushBatch();
            $this->processScanList($vars);
        }
    }

    public function scan($files, $vars, $rescan = self::RESCAN_SUSPICIOUS)
    {
        $i = 0;
        foreach ($files as $filepath) {
            $counter = $this->counter + $i;
            $vars->totalFiles++;
            $this->processedFiles = $counter - $vars->totalFolder - count($this->scanlist);
            printProgress($this->processedFiles, $filepath, $vars);
            $this->scanFile($filepath, $rescan, $counter, $vars);
            $i++;
        }

        //let's flush db again
        $this->db->flushBatch();

        //process whatever is left in our scan list
        if (count($this->scanlist) > 0) {
            $this->processScanList($vars);
        }

        // whitelist

        $snum = 0;
        $list = check_whitelist($vars->structure['crc'], $snum);
        $keys = array(
            'criticalPHP',
            'criticalJS',
            'g_Iframer',
            'g_Base64',
            'phishing',
            'adwareList',
            'g_Redirect',
            'warningPHP'
        );
        foreach ($keys as $p) {
            if (empty($vars->{$p})) {
                continue;
            }
            $p_Fragment = $p . 'Fragment';
            $p_Sig      = $p . 'Sig';
            if ($p == 'g_Redirect') {
                $p_Fragment = $p . 'PHPFragment';
            }
            if ($p == 'g_Phishing') {
                $p_Sig = $p . 'SigFragment';
            }

            $count = count($vars->{$p});
            for ($i = 0; $i < $count; $i++) {
                $id = $vars->{$p}[$i];
                if ($vars->structure['crc'][$id] !== 0 && in_array($vars->structure['crc'][$id], $list)) {
                    $rec = RapidScanStorageRecord::fromFile($vars->structure['n'][$id]);
                    $rec->calcSha2();
                    $rec->setVerdict(RapidScanStorageRecord::RX_GOOD);
                    $this->db->put($rec);
                    unset($vars->{$p}[$i]);
                    unset($vars->{$p_Sig}[$i]);
                    unset($vars->{$p_Fragment}[$i]);
                }
            }

            $vars->{$p}             = array_values($vars->{$p});
            $vars->{$p_Fragment}    = array_values($vars->{$p_Fragment});
            if (!empty($vars->{$p_Sig})) {
                $vars->{$p_Sig} = array_values($vars->{$p_Sig});
            }

            //close databases and rename new into 'current'
            $this->db->finish();
        }
    }
}

/**
 * DbFolderSpecification class file.
 */

/**
 * Class DbFolderSpecification.
 *
 * It can be use for checking requirements for a folder that is used for storing a RapidScan DB.
 */
class DbFolderSpecification
{
    /**
     * Check whether a particular folder satisfies requirements.
     *
     * @param string $folder
     * @return bool
     */
    public function satisfiedBy($folder)
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            return false;
        }

        $owner_id = (int)fileowner($folder);
        if (function_exists('posix_getpwuid')) {
            $owner = posix_getpwuid($owner_id);
            if (!isset($owner['name']) || $owner['name'] !== 'root') {
                return false;
            }
        }
        elseif ($owner_id != 0) {
            return false;
        }

        $perms = fileperms($folder);
        if (($perms & 0x0100)                           // owner r
            && ($perms & 0x0080)                        // owner w
            && ($perms & 0x0040) && !($perms & 0x0800)  // owner x
            && !($perms & 0x0020)                       // group without r
            && !($perms & 0x0010)                       // group without w
            && (!($perms & 0x0008) || ($perms & 0x0400))// group without x
            && !($perms & 0x0004)                       // other without r
            && !($perms & 0x0002)                       // other without w
            && (!($perms & 0x0001) || ($perms & 0x0200))// other without x
        ) {
            return true;
        }
        return false;
    }
}
/**
 * CriticalFileSpecification class file.
 */

/**
 * Class CriticalFileSpecification.
 */
class CriticalFileSpecification
{
    /**
     * @var array list of extension
     */
    private static $extensions = array(
        'php',
        'htaccess',
        'cgi',
        'pl',
        'o',
        'so',
        'py',
        'sh',
        'phtml',
        'php3',
        'php4',
        'php5',
        'php6',
        'php7',
        'pht',
        'shtml',
        'susp',
        'suspected',
        'infected',
        'vir',
        'ico',
        'js',
        'json',
        'com',
        ''
    );

    /**
     * Check whether a particular file with specified path is critical.
     *
     * @param string $path
     * @return bool
     */
    public function satisfiedBy($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, self::$extensions);
    }
}
class Helpers
{
    public static function format($source)
    {
        $t_count = 0;
        $in_object = false;
        $in_at = false;
        $in_php = false;
        $in_for = false;
        $in_comp = false;
        $in_quote = false;
        $in_var = false;

        if (!defined('T_ML_COMMENT')) {
            define('T_ML_COMMENT', T_COMMENT);
        }

        $result = '';
        @$tokens = token_get_all($source);
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $token = trim($token);
                if ($token == '{') {
                    if ($in_for) {
                        $in_for = false;
                    }
                    if (!$in_quote && !$in_var) {
                        $t_count++;
                        $result = rtrim($result) . ' ' . $token . "\n" . str_repeat('    ', $t_count);
                    } else {
                        $result = rtrim($result) . $token;
                    }
                } elseif ($token == '$') {
                    $in_var = true;
                    $result = $result . $token;
                } elseif ($token == '}') {
                    if (!$in_quote && !$in_var) {
                        $new_line = true;
                        $t_count--;
                        if ($t_count < 0) {
                            $t_count = 0;
                        }
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) .
                            $token . "\n" . @str_repeat('    ', $t_count);
                    } else {
                        $result = rtrim($result) . $token;
                    }
                    if ($in_var) {
                        $in_var = false;
                    }
                } elseif ($token == ';') {
                    if ($in_comp) {
                        $in_comp = false;
                    }
                    if ($in_for) {
                        $result .= $token . ' ';
                    } else {
                        $result .= $token . "\n" . str_repeat('    ', $t_count);
                    }
                } elseif ($token == ':') {
                    if ($in_comp) {
                        $result .= ' ' . $token . ' ';
                    } else {
                        $result .= $token . "\n" . str_repeat('    ', $t_count);
                    }
                } elseif ($token == '(') {
                    $result .= ' ' . $token;
                } elseif ($token == ')') {
                    $result .= $token;
                } elseif ($token == '@') {
                    $in_at = true;
                    $result .= $token;
                } elseif ($token == '.') {
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '=') {
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '?') {
                    $in_comp = true;
                    $result .= ' ' . $token . ' ';
                } elseif ($token == '"') {
                    if ($in_quote) {
                        $in_quote = false;
                    } else {
                        $in_quote = true;
                    }
                    $result .= $token;
                } else {
                    $result .= $token;
                }
            } else {
                list($id, $text) = $token;
                switch ($id) {
                    case T_OPEN_TAG:
                    case T_OPEN_TAG_WITH_ECHO:
                        $in_php = true;
                        $result .= trim($text) . "\n";
                        break;
                    case T_CLOSE_TAG:
                        $in_php = false;
                        $result .= trim($text);
                        break;
                    case T_FOR:
                        $in_for = true;
                        $result .= trim($text);
                        break;
                    case T_OBJECT_OPERATOR:
                        $result .= trim($text);
                        $in_object = true;
                        break;

                    case T_ENCAPSED_AND_WHITESPACE:
                    case T_WHITESPACE:
                        $result .= trim($text);
                        break;
                    case T_RETURN:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_ELSE:
                    case T_ELSEIF:
                        $result = rtrim($result) . ' ' . trim($text) . ' ';
                        break;
                    case T_CASE:
                    case T_DEFAULT:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count - 1) . trim($text) . ' ';
                        break;
                    case T_FUNCTION:
                    case T_CLASS:
                        $result .= "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_AND_EQUAL:
                    case T_AS:
                    case T_BOOLEAN_AND:
                    case T_BOOLEAN_OR:
                    case T_CONCAT_EQUAL:
                    case T_DIV_EQUAL:
                    case T_DOUBLE_ARROW:
                    case T_IS_EQUAL:
                    case T_IS_GREATER_OR_EQUAL:
                    case T_IS_IDENTICAL:
                    case T_IS_NOT_EQUAL:
                    case T_IS_NOT_IDENTICAL:
                    case T_LOGICAL_AND:
                    case T_LOGICAL_OR:
                    case T_LOGICAL_XOR:
                    case T_MINUS_EQUAL:
                    case T_MOD_EQUAL:
                    case T_MUL_EQUAL:
                    case T_OR_EQUAL:
                    case T_PLUS_EQUAL:
                    case T_SL:
                    case T_SL_EQUAL:
                    case T_SR:
                    case T_SR_EQUAL:
                    case T_START_HEREDOC:
                    case T_XOR_EQUAL:
                        $result = rtrim($result) . ' ' . trim($text) . ' ';
                        break;
                    case T_COMMENT:
                        $result = rtrim($result) . "\n" . str_repeat('    ', $t_count) . trim($text) . ' ';
                        break;
                    case T_ML_COMMENT:
                        $result = rtrim($result) . "\n";
                        $lines = explode("\n", $text);
                        foreach ($lines as $line) {
                            $result .= str_repeat('    ', $t_count) . trim($line);
                        }
                        $result .= "\n";
                        break;
                    case T_INLINE_HTML:
                        $result .= $text;
                        break;
                    default:
                        $result .= trim($text);
                        break;
                }
            }
        }
        return $result;
    }

    public static function replaceCreateFunction($str)
    {
        $hangs = 20;
        while (strpos($str, 'create_function') !== false && $hangs--) {
            $start_pos = strpos($str, 'create_function');
            $end_pos = 0;
            $brackets = 0;
            $started = false;
            $opened = 0;
            $closed = 0;
            for ($i = $start_pos; $i < strlen($str); $i++) {
                if ($str[$i] == '(') {
                    $started = true;
                    $brackets++;
                    $opened++;
                } else if ($str[$i] == ')') {
                    $closed++;
                    $brackets--;
                }
                if ($brackets == 0 && $started) {
                    $end_pos = $i + 1;
                    break;
                }
            }

            $cr_func = substr($str, $start_pos, $end_pos - $start_pos);
            $func = implode('function(', explode('create_function(\'', $cr_func, 2));
            //$func = substr_replace('create_function(\'', 'function(', $cr_func);
            //$func = str_replace('\',\'', ') {', $func);
            $func = implode(') {', explode('\',\'', $func, 2));
            $func = substr($func, 0, -2) . '}';
            $str = str_replace($cr_func, $func, $str);
        }
        return $str;
    }

    public static function calc($expr)
    {
        if (is_array($expr)) {
            $expr = $expr[0];
        }
        preg_match('~(chr|min|max|round)?\(([^\)]+)\)~msi', $expr, $expr_arr);
        if (@$expr_arr[1] == 'min' || @$expr_arr[1] == 'max') {
            return $expr_arr[1](explode(',', $expr_arr[2]));
        } elseif (@$expr_arr[1] == 'chr') {
            if ($expr_arr[2][0] === '(') {
                $expr_arr[2] = substr($expr_arr[2], 1);
            }
            $expr_arr[2] = self::calc($expr_arr[2]);
            return $expr_arr[1](intval($expr_arr[2]));
        } elseif (@$expr_arr[1] == 'round') {
            $expr_arr[2] = self::calc($expr_arr[2]);
            return $expr_arr[1]($expr_arr[2]);
        } else {
            preg_match_all('~([\d\.a-fx]+)([\*\/\-\+\^\|\&])?~', $expr, $expr_arr);
            foreach ($expr_arr[1] as &$expr_arg) {
                if (strpos($expr_arg, "0x")!==false) {
                    $expr = str_replace($expr_arg, hexdec($expr_arg), $expr);
                    $expr_arg = hexdec($expr_arg);
                }
            }
            if (in_array('*', $expr_arr[2]) !== false) {
                $pos = array_search('*', $expr_arr[2]);
                $res = $expr_arr[1][$pos] * $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '*' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '*' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('/', $expr_arr[2]) !== false) {
                $pos = array_search('/', $expr_arr[2]);
                $res = $expr_arr[1][$pos] / $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '/' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '/' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('-', $expr_arr[2]) !== false) {
                $pos = array_search('-', $expr_arr[2]);
                $res = $expr_arr[1][$pos] - $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '-' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '-' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('+', $expr_arr[2]) !== false) {
                $pos = array_search('+', $expr_arr[2]);
                $res = $expr_arr[1][$pos] + $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '+' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '+' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('^', $expr_arr[2]) !== false) {
                $pos = array_search('^', $expr_arr[2]);
                $res = $expr_arr[1][$pos] ^ $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '^' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '^' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('|', $expr_arr[2]) !== false) {
                $pos = array_search('|', $expr_arr[2]);
                $res = $expr_arr[1][$pos] | $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '|' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '|' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } elseif (in_array('&', $expr_arr[2]) !== false) {
                $pos = array_search('&', $expr_arr[2]);
                $res = $expr_arr[1][$pos] & $expr_arr[1][$pos + 1];
                $pos_subst = strpos($expr, $expr_arr[1][$pos] . '&' . $expr_arr[1][$pos + 1]);
                $expr = substr_replace($expr, $res, $pos_subst, strlen($expr_arr[1][$pos] . '&' . $expr_arr[1][$pos + 1]));
                $expr = self::calc($expr);
            } else {
                return $expr;
            }

            return $expr;
        }
    }

    public static function getEvalCode($string)
    {
        preg_match("/eval\(([^\)]+)\)/msi", $string, $matches);
        return (empty($matches)) ? '' : end($matches);
    }

    public static function getTextInsideQuotes($string)
    {
        if (preg_match_all('/("(.*)")/msi', $string, $matches)) {
            return @end(end($matches));
        } elseif (preg_match_all('/\((\'(.*)\')/msi', $string, $matches)) {
            return @end(end($matches));
        } else {
            return '';
        }
    }

    public static function getNeedles($string)
    {
        preg_match_all("/'(.*?)'/msi", $string, $matches);

        return (empty($matches)) ? array() : $matches[1];
    }

    public static function getHexValues($string)
    {
        preg_match_all('/0x[a-fA-F0-9]{1,8}/msi', $string, $matches);
        return (empty($matches)) ? array() : $matches[0];
    }

    public static function formatPHP($string)
    {
        $string = str_replace('<?php', '', $string);
        $string = str_replace('?>', '', $string);
        $string = str_replace(PHP_EOL, "", $string);
        $string = str_replace(";", ";\n", $string);
        $string = str_replace("}", "}\n", $string);
        return $string;
    }

    public static function fnEscapedHexToHex($escaped)
    {
        return chr(hexdec($escaped[1]));
    }

    public static function fnEscapedOctDec($escaped)
    {
        return chr(octdec($escaped[1]));
    }

    public static function fnEscapedDec($escaped)
    {
        return chr($escaped[1]);
    }

    //from sample_16
    public static function someDecoder($str)
    {
        $str = base64_decode($str);
        $TC9A16C47DA8EEE87 = 0;
        $TA7FB8B0A1C0E2E9E = 0;
        $T17D35BB9DF7A47E4 = 0;
        $T65CE9F6823D588A7 = (ord($str[1]) << 8) + ord($str[2]);
        $i = 3;
        $T77605D5F26DD5248 = 0;
        $block = 16;
        $T7C7E72B89B83E235 = "";
        $T43D5686285035C13 = "";
        $len = strlen($str);

        $T6BBC58A3B5B11DC4 = 0;

        for (; $i < $len;) {
            if ($block == 0) {
                $T65CE9F6823D588A7 = (ord($str[$i++]) << 8);
                $T65CE9F6823D588A7 += ord($str[$i++]);
                $block = 16;
            }
            if ($T65CE9F6823D588A7 & 0x8000) {
                $TC9A16C47DA8EEE87 = (ord($str[$i++]) << 4);
                $TC9A16C47DA8EEE87 += (ord($str[$i]) >> 4);
                if ($TC9A16C47DA8EEE87) {
                    $TA7FB8B0A1C0E2E9E = (ord($str[$i++]) & 0x0F) + 3;
                    for ($T17D35BB9DF7A47E4 = 0; $T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E; $T17D35BB9DF7A47E4++) {
                        $T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4] =
                            $T7C7E72B89B83E235[$T77605D5F26DD5248 - $TC9A16C47DA8EEE87 + $T17D35BB9DF7A47E4];
                    }
                    $T77605D5F26DD5248 += $TA7FB8B0A1C0E2E9E;
                } else {
                    $TA7FB8B0A1C0E2E9E = (ord($str[$i++]) << 8);
                    $TA7FB8B0A1C0E2E9E += ord($str[$i++]) + 16;
                    for ($T17D35BB9DF7A47E4 = 0; $T17D35BB9DF7A47E4 < $TA7FB8B0A1C0E2E9E;
                         $T7C7E72B89B83E235[$T77605D5F26DD5248 + $T17D35BB9DF7A47E4++] = $str[$i]) {
                    }
                    $i++;
                    $T77605D5F26DD5248 += $TA7FB8B0A1C0E2E9E;
                }
            } else {
                $T7C7E72B89B83E235[$T77605D5F26DD5248++] = $str[$i++];
            }
            $T65CE9F6823D588A7 <<= 1;
            $block--;
            if ($i == $len) {
                $T43D5686285035C13 = $T7C7E72B89B83E235;
                if (is_array($T43D5686285035C13)) {
                    $T43D5686285035C13 = implode($T43D5686285035C13);
                }
                $T43D5686285035C13 = "?" . ">" . $T43D5686285035C13;
                return $T43D5686285035C13;
            }
        }
    }
    //

    public static function someDecoder2($WWAcmoxRAZq, $sBtUiFZaz)   //sample_05
    {
        $JYekrRTYM = str_rot13(gzinflate(str_rot13(base64_decode('y8svKCwqLiktK6+orFdZV0FWWljPyMzKzsmNNzQyNjE1M7ewNAAA'))));
        if ($WWAcmoxRAZq == 'asedferg456789034689gd') {
            $cEerbvwKPI = $JYekrRTYM[18] . $JYekrRTYM[19] . $JYekrRTYM[17] . $JYekrRTYM[17] . $JYekrRTYM[4] . $JYekrRTYM[21];
            return $cEerbvwKPI($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zfcxdrtgyu678954ftyuip') {
            $JWTDeUKphI = $JYekrRTYM[1] . $JYekrRTYM[0] . $JYekrRTYM[18] . $JYekrRTYM[4] . $JYekrRTYM[32] .
                $JYekrRTYM[30] . $JYekrRTYM[26] . $JYekrRTYM[3] . $JYekrRTYM[4] . $JYekrRTYM[2] . $JYekrRTYM[14] .
                $JYekrRTYM[3] . $JYekrRTYM[4];
            return $JWTDeUKphI($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'gyurt456cdfewqzswexcd7890df') {
            $rezmMBMev = $JYekrRTYM[6] . $JYekrRTYM[25] . $JYekrRTYM[8] . $JYekrRTYM[13] . $JYekrRTYM[5] . $JYekrRTYM[11] . $JYekrRTYM[0] . $JYekrRTYM[19] . $JYekrRTYM[4];
            return $rezmMBMev($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zcdfer45dferrttuihvs4321890mj') {
            $WbbQXOQbH = $JYekrRTYM[18] . $JYekrRTYM[19] . $JYekrRTYM[17] . $JYekrRTYM[26] . $JYekrRTYM[17] . $JYekrRTYM[14] . $JYekrRTYM[19] . $JYekrRTYM[27] . $JYekrRTYM[29];
            return $WbbQXOQbH($sBtUiFZaz);
        } elseif ($WWAcmoxRAZq == 'zsedrtre4565fbghgrtyrssdxv456') {
            $jPnPLPZcMHgH = $JYekrRTYM[2] . $JYekrRTYM[14] . $JYekrRTYM[13] . $JYekrRTYM[21] . $JYekrRTYM[4] . $JYekrRTYM[17] . $JYekrRTYM[19] . $JYekrRTYM[26] . $JYekrRTYM[20] . $JYekrRTYM[20] . $JYekrRTYM[3] . $JYekrRTYM[4] . $JYekrRTYM[2] . $JYekrRTYM[14] . $JYekrRTYM[3] . $JYekrRTYM[4];
            return $jPnPLPZcMHgH($sBtUiFZaz);
        }
    }

    public static function stripsquoteslashes($str)
    {
        $res = '';
        for ($i = 0; $i < strlen($str); $i++) {
            if (isset($str[$i+1]) && ($str[$i] == '\\' && ($str[$i+1] == '\\' || $str[$i+1] == '\''))) {
                continue;
            } else {
                $res .= $str[$i];
            }
        }
        return $res;
    }

    ///////////////////////////////////////////////////////////////////////////
}




///////////////////////////////////////////////////////////////////////////

function parseArgs($argv){
    array_shift($argv); $o = array();
    foreach ($argv as $a){
        if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
            if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
            else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
        else if (substr($a,0,1) == '-'){
            if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
            else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
        else { $o[] = $a; } }
    return $o;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////
// cli handler
if (!defined('AIBOLIT_START_TIME') && !defined('PROCU_CLEAN_DB') && @strpos(__FILE__, @$argv[0])!==false) {
    //echo "\n" . $argv[1] . "\n";

    set_time_limit(0);
    ini_set('max_execution_time', '900000');
    ini_set('realpath_cache_size', '16M');
    ini_set('realpath_cache_ttl', '1200');
    ini_set('pcre.backtrack_limit', '1000000');
    ini_set('pcre.recursion_limit', '12500');
    ini_set('pcre.jit', '1');
    $options = parseArgs($argv);
    $str = php_strip_whitespace($options[0]);
    $d = new Deobfuscator($str);
    $start = microtime(true);
    $hangs = 0;
    while ($d->getObfuscateType($str)!=='' && $hangs < 15) {
        $str = $d->deobfuscate();
        $d = new Deobfuscator($str);
        $hangs++;
    }
    $code = $str;
    if (isset($options['prettyprint'])) {
        $code = Helpers::format($code);
    }
    echo $code;
    echo "\n";
    //echo 'Execution time: ' . round(microtime(true) - $start, 4) . ' sec.';
}

class Deobfuscator
{
    private $signatures = array(
        array(
            'full' =>'~for\((\$\w{1,40})=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi',
            'fast' => '~for\((\$\w{1,40})=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi',
            'id' => 'parenthesesString'),

        array(
            'full' =>'~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi',
            'fast' => '~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi',
            'id' => 'xorFName'),

        array(
            'full' =>
                '~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi',
            'fast' => '~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi',
            'id' => 'phpMess'),

        array(
            'full' =>
                '~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"[^\"]+\",\"[^\"]+\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"[^\"]+\",\"[^\"]+\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi',
            'id' => 'pregReplaceSample05'),


        array(
            'full' => '~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi',
            'id' => 'pregReplaceB64'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'([^\']+)\';\s*\1\s*=\s*gzinflate\s*\(base64_decode\s*\(\1\)\);\s*\1\s*=\s*str_replace\s*\(\"__FILE__\",\"\'\$\w+\'\",\1\);\s*eval\s*\(\1\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\'([^\']+)\';\s*\1\s*=\s*gzinflate\s*\(base64_decode\s*\(\1\)\);\s*\1\s*=\s*str_replace\s*\(\"__FILE__\",\"\'\$\w+\'\",\1\);\s*eval\s*\(\1\);~msi',
            'id' => 'GBE'),

        array(
            'full' => '~(\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\])\s*=\s*\s*array\s*\(\s*base64_decode\s*\(.+?((.+?\1\[\d+\]).+?)+[^;]+;(\s*include\(\$_\d+\);)?}?((.+?___\d+\(\d+\))+[^;]+;)?~msi',
            'fast' => '~\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\]\s*=\s*\s*array\s*\(\s*base64_decode\s*\(~msi',
            'id' => 'Bitrix'),

        array(
            'full' => '~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi',
            'fast' => '~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi',
            'id' => 'B64inHTML'),

        array(
            'full' => '~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(\$(GLOBALS\[\')?[O0]*(\'\])?=(\d+);)?\s*(\$(GLOBALS\[\')?[O0]*(\'\])?\.?=(\$(GLOBALS\[\')?[O0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi',
            'fast' => '~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(?:\$(GLOBALS\[\')?[O0]*(?:\'\])?=\d+;)?\s*(?:\$(?:GLOBALS\[\')?[O0]*(?:\'\])?\.?=(?:\$(?:GLOBALS\[\')?[O0]*(?:\'\])?(?:[\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi',
            'id' => 'LockIt'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\([^\)]+\)+\s*;~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\(~msi',
            'id' => 'FOPO'),

        array(
            'full' => '~\$_F=__FILE__;\$_X=\'([^\']+\');eval\([^\)]+\)+;~msi',
            'fast' => '~\$_F=__FILE__;\$_X=\'([^\']+\');eval\(~ms',
            'id' => 'ByteRun'),

        array(
            'full' => '~(\$\w{1,40}=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi',
            'fast' => '~(\$\w{1,40}=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi',
            'id' => 'Urldecode'),

        array(
            'full' => '~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$[0O]+\.?=(\$[0O]+\{\d+\}\s*[\.;]?\s*)+)+((\$[O0]+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$O0\(\)\*\d,\s]+);|(eval\(\$[0O]+\([\'"]([^\'"]+)[\'"]\)+;))~msi',
            'fast' => '~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$[0O]+\.?=(\$[0O]+\{\d+\}\s*[\.;]?\s*)+)+((\$[O0]+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$O0\(\)\*\d,\s]+);|(eval\(\$[0O]+\([\'"]([^\'"]+)[\'"]\)+;))~msi',
            'id'   => 'UrlDecode2',
        ),

        array(
            'full' => '~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\([^\)]+\)+;~msi',
            'fast' => '~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\(~msi',
            'id' => 'cobra'),

        array(
            'full' => '~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\([^\)]+\)+;~msi',
            'fast' => '~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\(~msi',
            'id' => 'strtrFread'),

        array(
            'full' => '~if\s*\(\!extension_loaded\(\'IonCube_loader\'\)\).+pack\(\"H\*\",\s*\$__ln\(\"/\[A-Z,\\\\r,\\\\n\]/\",\s*\"\",\s*substr\(\$__lp,\s*([0-9a-fx]+\-[0-9a-fx]+)\)\)\)[^\?]+\?\>\s*[0-9a-z\r\n]+~msi',
            'fast' => '~IonCube_loader~ms',
            'id' => 'FakeIonCube'),

        array(
            'full' => '~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi',
            'fast' => '~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi',
            'id' => 'strtrBase64'),

        array(
            'full' => '~\$\w+\s*=\s*array\((\'[^\']+\',?)+\);\s*.+?(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\2\[[a-fx\d]+\])\(\);(.+?\2)+.+}~msi',
            'fast' => '~(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\1\[[a-fx\d]+\])\(\);~msi',
            'id' => 'explodeSubst'),

        array(
            'full' => '~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+(.+\3)[^}]+}~msi',
            'fast' => '~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+~msi',
            'id' => 'subst'),

        array(
            'full' => '~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+?eval\(\1\(\"[^\"]+\"\)\);~msi',
            'fast' => '~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+?eval\(\1\(\"[^\"]+\"\)\);~msi',
            'id' => 'decoder'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi',
            'id' => 'GBZ'),

        array(
            'full' => '~\$\w+\s*=\s*\d+;\s*\$GLOBALS\[\'[^\']+\'\]\s*=\s*Array\(\);\s*global \$\w+;(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?).+?exit\(\);\}\}~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?)~msi',
            'id' => 'globalsArray'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;[^)]+\)+;\s*\$\w+\(\);~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;~msi',
            'id' => 'xoredVar'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*\'([^\']*)\';\s*(\$\w{1,40})\s*=\s*explode\s*\((chr\s*\(\s*\(\d+\-\d+\)\)),substr\s*\(\1,\s*\((\d+\-\d+)\),\s*\(\s*(\d+\-\d+)\)\)\);\s*(\$\w{1,40})\s*=\s*\3\[\d+\]\s*\(\3\[\s*\(\d+\-\d+\)\]\);\s*(\$\w{1,40})\s*=\s*\3\[\d+\]\s*\(\3\[\s*\(\d+\-\d+\)\]\);\s*if\s*\(!function_exists\s*\(\'([^\']*)\'\)\)\s*\{\s*function\s*\9\s*\(.+\1\s*=\s*\$\w+[+\-\*]\d+;~msi',
            'fast' => '~(\$\w{1,40})\s=\s\'([^\']*)\';\s(\$\w{1,40})=explode\((chr\(\(\d+\-\d+\)\)),substr\(\1,\((\d+\-\d+)\),\((\d+\-\d+)\)\)\);\s(\$\w{1,40})\s=\s\3\[\d+\]\(\3\[\(\d+\-\d+\)\]\);\s(\$\w{1,40})\s=\s\3\[\d+\]\(\3\[\(\d+\-\d+\)\]\);\sif\s\(!function_exists\(\'([^\']*)\'\)\)\s\{\sfunction\s*\9\(~msi',
            'id' => 'arrayOffsets'),

        array(
            'full' => '~(\$\w{1,50}\s*=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"([^\"]+)\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\s*\{\s*function\s*[^\}]+\}\s*return\s*\$\w+;\}[^}]+}~msi',
            'fast' => '~(\$\w{1,50}=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"[^\"]+\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\{\s*function ~msi',
            'id' => 'obfB64'),

        array(
            'full' => '~if\(\!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\).+\$REXISTHEDOG4FBI=\'([^\']+)\';\$\w+=\'[^\']+\';\s*eval\(\w+\(\'([^\']+)\',\$REXISTHEDOG4FBI\)\);~msi',
            'fast' => '~if\(!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\)\{\$fld1=dirname\(\$fld\);\$fld=\$fld1\.\'/scopbin\';clearstatcache\(\);if\(!is_dir\(\$fld\)\)return findsysfolder\(\$fld1\);else return \$fld;\}\}require_once\(findsysfolder\(__FILE__\)\.\'/911006\.php\'\);~msi',
            'id' => 'sourceCop'),

        array(
            'full' => '~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"][^\'"]*[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\9\([\'"][^\'"]*[\'"],)+\s*[\'"][^\'"]*[\'"]\s*\)+;~msi',
            'fast' => '~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"][^\'"]*[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\9\([\'"][^\'"]*[\'"],)+\s*[\'"][^\'"]*[\'"]\s*\)+;~msi',
            'id' => 'webshellObf',

        ),

        array(
            'full' => '~(\$\w{1,40})=\'([^\'\\\\]|.*?)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';\s*(\$\w{1,40})\(\6,\$\w{1,40}\.\"([^\"]+)\"\.\$\w{1,40}\.\4\);~msi',
            'fast' => '~(\$\w{1,40})=\'([^\\\\\']|.*?)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';~msi',
            'id' => 'substCreateFunc'
        ),

        array(
            'full' => '~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi',
            'fast' => '~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi',
            'id' => 'createFunc'
        ),

        array(
            'full' => '~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis',
            'fast' => '~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis',
            'id' => 'forEach'
        ),

        array(
            'full' => '~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"][^"\']+[\'"]\)+;~msi',
            'fast' => '~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"][^"\']+[\'"]\)+;~msi',
            'id' => 'PHPMyLicense',
        ),

        array(
            'full' => '~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);[\w\+\=]+~msi',
            'fast' => '~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);~msi',
            'id' => 'zeura'),

        array(
            'full' => '~((\$\w{1,40})\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi',
            'fast' => '~((\$\w{1,40})\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi',
            'id' => 'evalVar'),

        array(
            'full' => '~function\s*(\w{1,40})\((\$\w{1,40})\)\{(\$\w{1,40})=\'base64_decode\';(\$\w{1,40})=\'gzinflate\';return\s*\4\(\3\(\2\)\);\}\$\w{1,40}=\'[^\']*\';\$\w{1,40}=\'[^\']*\';eval\(\1\(\'([^\']*)\'\)\);~msi',
            'fast' => '~function\s*(\w{1,40})\((\$\w{1,40})\)\{(\$\w{1,40})=\'base64_decode\';(\$\w{1,40})=\'gzinflate\';return\s*\4\(\3\(\2\)\);\}\$\w{1,40}=\'[^\']*\';\$\w{1,40}=\'[^\']*\';eval\(\1\(\'([^\']*)\'\)\);~msi',
            'id' => 'evalFunc'),

        array(
            'full' => '~function\s*(\w{1,40})\s*\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*"\\\\x62\\\\x61\\\\x73\\\\x65\\\\x36\\\\x34\\\\x5f\\\\x64\\\\x65\\\\x63\\\\x6f\\\\x64\\\\x65";\s*(\$\w{1,40})\s*=\s*"\\\\x67\\\\x7a\\\\x69\\\\x6e\\\\x66\\\\x6c\\\\x61\\\\x74\\\\x65";\s*return\s*\4\s*\(\3\s*\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\s*\(\1\s*\(\"([^\"]*)\"\)\);~msi',
            'fast' => '~function\s*(\w{1,40})\s*\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*"\\\\x62\\\\x61\\\\x73\\\\x65\\\\x36\\\\x34\\\\x5f\\\\x64\\\\x65\\\\x63\\\\x6f\\\\x64\\\\x65";\s*(\$\w{1,40})\s*=\s*"\\\\x67\\\\x7a\\\\x69\\\\x6e\\\\x66\\\\x6c\\\\x61\\\\x74\\\\x65";\s*return\s*\4\s*\(\3\s*\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\s*\(\1\s*\(\"([^\"]*)\"\)\);~msi',
            'id' => 'evalFunc'),

        array(
            'full' => '~preg_replace\(["\']/\.\*?/[^\)]+\)+;(["\'],["\'][^"\']+["\']\)+;)?~msi',
            'fast' => '~preg_replace\(["\']/\.\*?/~msi',
            'id' => 'eval'),

        array(
            'full' => '~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi',
            'fast' => '~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi',
            'id' => 'evalInject'

        ),

        array(
            'full' => '~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi',
            'fast' => '~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi',
            'id' => 'createFuncConcat'

        ),

        array(
            'full' => '~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi',
            'fast' => '~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi',
            'id' => 'evalWrapVar'

        ),

        array(
            'full' => '~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi',
            'fast' => '~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi',
            'id' => 'escapes'
        ),

        array(
            'full' => '~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi',
            'fast' => '~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi',
            'id' => 'assert',
        ),

        array(
            'full' => '~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]}=[\'"]([^\'"]+)[\'"];eval.{10,50}?\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\}\)+;~msi',
            'fast' => '~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]}=[\'"]([^\'"]+)[\'"];eval.{10,50}?\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\}\)+;~msi',
            'id' => 'evalVarVar',
        ),

        array(
            'full' => '~(\$\w+)=[\'"][^"\']+[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\2\([\'"][^\'"]+[\'"]\)+;~msi',
            'fast' => '~(\$\w+)=[\'"][^"\']+[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\2\([\'"][^\'"]+[\'"]\)+;~msi',
            'id' => 'edoced_46esab',
        ),

        array(
            'full' => '~@?(eval|(\$\w+)\s*=\s*create_function)\s*\((\'\',)?\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+(\s*\2\(\);)?~msi',
            'fast' => '~@?(eval|\$\w+\s*=\s*create_function)\s*\((\'\',)?\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|eval\s*\(|urldecode\s*\(|rawurldecode\s*\()+~msi',
            'id' => 'eval'
        ),

        array(
            'full' => '~eval\s*/\*[\w\s\.:,]+\*/\s*\([^\)]+\)+;~msi',
            'fast' => '~eval\s*/\*[\w\s\.:,]+\*/\s*\(~msi',
            'id' => 'eval'
        ),

        array(
            'full' => '~eval\("\\\\145\\\\166\\\\141\\\\154\\\\050\\\\142\\\\141\\\\163[^\)]+\)+;~msi',
            'fast' => '~eval\("\\\\145\\\\166\\\\141\\\\154\\\\050\\\\142\\\\141\\\\163~msi',
            'id' => 'evalHex'
        ),

        array(
            'full' => '~eval\s*\("\\\\x?\d+[^\)]+\)+;(?:[\'"]\)+;)?~msi',
            'fast' => '~eval\s*\("\\\\x?\d+~msi',
            'id' => 'evalHex'
        ),

        array(
            'full' => '~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi',
            'fast' => '~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi',
            'id' => 'seolyzer'
        ),

        array(
            'full' => '~(\$\w+)="((?:[^"]|(?<=\\\\)")*)";(\s*\$GLOBALS\[\'\w+\'\]\s*=\s*(?:\${)?(\1\[\d+\]}?\.?)+;\s*)+(.{0,400}\s*\1\[\d+\]\.?)+;\s*}~msi',
            'fast' => '~(\$\w+)="((?:[^"]|(?<=\\\\)")*)";(\s*\$GLOBALS\[\'\w+\'\]\s*=\s*(?:\${)?(\1\[\d+\]}?\.?)+;\s*)+(.{0,400}\s*\1\[\d+\]\.?)+;\s*}~msi',
            'id' => 'subst2',
        ),

        array(
            'full' => '~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi',
            'fast' => '~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi',
            'id' => 'strreplace',
        ),

        array(
            'full' => '~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi',
            'fast' => '~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi',
            'id' => 'echo',
        ),

    );

    private $full_source;
    private $prev_step;
    private $cur;
    private $obfuscated;
    private $max_level;
    private $max_time;
    private $run_time;
    private $fragments;

    public function __construct($text, $max_level = 30, $max_time = 5)
    {
        $this->text = $text;
        $this->full_source = $text;
        $this->max_level = $max_level;
        $this->max_time = $max_time;
        $this->fragments = array();
    }

    public function getObfuscateType($str)
    {
        foreach ($this->signatures as $signature) {
            if (preg_match($signature['fast'], $str)) {
                return $signature['id'];
            }
        }
        return '';
    }

    private function getObfuscateFragment($str)
    {
        foreach ($this->signatures as $signature) {
            if (preg_match($signature['full'], $str, $matches)) {
                return $matches[0];
            }
        }
        return '';
    }

    public function getFragments()
    {
        $this->grabFragments();
        if (count($this->fragments)>0) {
            return $this->fragments;
        }
        return false;
    }

    private function grabFragments()
    {
        if ($this->cur == null) {
            $this->cur = $this->text;
        }
        $str = $this->cur;
        while ($sign = current($this->signatures)) {
            $regex = $sign['full'];
            if (preg_match($regex, $str, $matches)) {
                $this->fragments[$matches[0]] = $matches[0];
                $str = str_replace($matches[0], '', $str);
            } else {
                next($this->signatures);
            }
        }
    }

    private function deobfuscateFragments()
    {
        $prev_step = '';
        if (count($this->fragments) > 0) {
            $i=0;
            foreach ($this->fragments as $frag => $value) {
                $type = $this->getObfuscateType($value);
                while ($type!=='' && $i < 15) {
                    $find = $this->getObfuscateFragment($value);
                    $func = 'deobfuscate' . ucfirst($type);
                    $temp = @$this->$func($find);

                    $value = str_replace($find, $temp, $value);
                    $this->fragments[$frag] = $value;
                    $type = $this->getObfuscateType($value);
                    if ($prev_step == $value) {
                        break;
                    } else {
                        $prev_step = $value;
                    }
                    $i++;
                }
            }
        }
    }

    public function deobfuscate()
    {
        $prev_step = '';
        $deobfuscated = '';
        $this->run_time = microtime(true);
        $this->cur = $this->text;
        $this->grabFragments();
        $this->deobfuscateFragments();
        $deobfuscated = $this->cur;
        if (count($this->fragments)>0) {
            foreach ($this->fragments as $fragment => $text) {
                $deobfuscated = str_replace($fragment, $text, $deobfuscated);
            }
        }
        $deobfuscated = preg_replace_callback('~"[\w\\\\\s=;_<>&/\.-]+"~msi', function ($matches) {
            return preg_match('~\\\\x[2-7][0-9a-f]|\\\\1[0-2][0-9]|\\\\[3-9][0-9]|\\\\0[0-4][0-9]|\\\\1[0-7][0-9]~msi', $matches[0]) ? stripcslashes($matches[0]) : $matches[0];
        }, $deobfuscated);

        preg_match_all('~(global\s*(\$[\w_]+);)\2\s*=\s*"[^"]+";~msi', $deobfuscated, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $deobfuscated = str_replace($match[0], '', $deobfuscated);
            $deobfuscated = str_replace($match[1], '', $deobfuscated);
        }

        return $deobfuscated;
    }

    private function deobfuscateStrreplace($str)
    {
        preg_match('~(\$\w+\s*=\s*"[^"]+";\s*)+(\$\w+\s*=\s*\$?\w+\("\w+"\s*,\s*""\s*,\s*"\w+"\);\s*)+\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\("\w+",\s*"",(\s*\$\w+\.?)+\)+;\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;

        $str_replace = '';
        $base64_decode = '';
        $layer = '';

        preg_match_all('~(\$\w+)\s*=\s*\"([^"]+)\"\s*;~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $i => $match) {
            $vars[$match[1]] = $match[2];
        }

        $res = preg_replace_callback('~(\$\w+)\s*=\s*str_replace\("(\w+)",\s*"",\s*"(\w+)"\)~msi',
            function ($matches) use (&$vars, &$str_replace) {
                $vars[$matches[1]] = str_replace($matches[2], "", $matches[3]);
                if ($vars[$matches[1]] == 'str_replace') {
                    $str_replace = $matches[1];
                }
                $tmp = $matches[1] . ' = "' . $vars[$matches[1]] . '"';
                return $tmp;
            }, $res);

        $res = preg_replace_callback('~(\$\w+)\s*=\s*\\' . $str_replace . '\("(\w+)",\s*"",\s*"(\w+)"\)~msi',
            function ($matches) use (&$vars, &$base64_decode) {
                $vars[$matches[1]] = str_replace($matches[2], "", $matches[3]);
                if ($vars[$matches[1]] == 'base64_decode') {
                    $base64_decode = $matches[1];
                }
                $tmp = $matches[1] . ' = "' . $vars[$matches[1]] . '"';
                return $tmp;
            }, $res);

        $res = preg_replace_callback('~\\' . $base64_decode . '\(\\' . $str_replace . '\("(\w+)",\s*"",\s*([\$\w\.]+)\)~msi',
            function ($matches) use (&$vars, &$layer) {
                $tmp = explode('.', $matches[2]);
                foreach ($tmp as &$item) {
                    $item = $vars[$item];
                }
                $tmp = implode('', $tmp);
                $layer = base64_decode(str_replace($matches[1], "", $tmp));
                return $matches[0];
            }, $res);

        $res = $layer;
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSeolyzer($str)
    {
        preg_match('~\$\w+=\'printf\';(\s*\$\w+\s*=\s*\'[^\']+\'\s*;)+\s*(\$\w+\s*=\s*\$\w+\([^\)]+\);\s*)+(\$\w+\s*=\s*\'[^\']+\';\s*)?(\s*(\$\w+\s*=\s*)?\$\w+\([^)]*\)+;\s*)+(echo\s*\$\w+;)?~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $base64_decode = '';
        $layer = '';
        $gzuncompress = '';
        preg_match_all('~(\$\w+)\s*=\s*\'([^\']+)\'\s*;~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $i => $match) {
            $vars[$match[1]] = $match[2];
            if ($match[2] == 'base64_decode') {
                $base64_decode = $match[1];
            }
        }

        $res = preg_replace_callback('~\s*=\s*\\' . $base64_decode . '\((\$\w+)\)~msi', function ($matches) use (&$vars, &$gzuncompress, &$layer) {
            if (isset($vars[$matches[1]])) {
                $tmp = base64_decode($vars[$matches[1]]);
                if ($tmp == 'gzuncompress') {
                    $gzuncompress = $matches[1];
                }
                $vars[$matches[1]] = $tmp;
                $tmp = " = '{$tmp}'";
            } else {
                $tmp = $matches[1];
            }
            return $tmp;
        }, $res);

        if ($gzuncompress !== '') {
            $res = preg_replace_callback('~\\' . $gzuncompress . '\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi',
                function ($matches) use (&$vars, $gzuncompress, &$layer) {
                    if (isset($vars[$matches[1]])) {
                        $tmp = gzuncompress(base64_decode($vars[$matches[1]]));
                        $layer = $matches[1];
                        $vars[$matches[1]] = $tmp;
                        $tmp = "'{$tmp}'";
                    } else {
                        $tmp = $matches[1];
                    }
                    return $tmp;
                }, $res);
            $res = $vars[$layer];
        } else if (preg_match('~\$\w+\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi', $res)) {
            $res = preg_replace_callback('~\$\w+\(\s*\\' . $base64_decode . '\((\$\w+)\)~msi',
                function ($matches) use (&$vars, &$layer) {
                    if (isset($vars[$matches[1]])) {
                        $tmp = base64_decode($vars[$matches[1]]);
                        $layer = $matches[1];
                        $vars[$matches[1]] = $tmp;
                        $tmp = "'{$tmp}'";
                    } else {
                        $tmp = $matches[1];
                    }
                    return $tmp;
                }, $res);
            $res = $vars[$layer];
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateCreateFunc($str)
    {
        preg_match('~(\$\w+)=[create_function".]+;\s*\1=\1\(\'(\$\w+)\',[\'.eval\("\?>".gzinflate\(base64_decode]+\2\)+;\'\);\s*\1\(\'([^\']+)\'\);~msi', $str, $matches);
        $find = $matches[0];
        $res = ' ?>' . gzinflate(base64_decode($matches[3]));
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateCreateFuncConcat($str)
    {
        preg_match('~((\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));\s*)+\$\w+\s*=\s*\$\w+\(\'\',(\s*\$\w+\s*\(\s*)+\'[^\']+\'\)+;\s*\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $res = preg_replace_callback('~(?|(\$\w+)\s*=\s*(([base64_decode\'\.\s]+)|([eval\'\.\s]+)|([create_function\'\.\s]+)|([stripslashes\'\.\s]+)|([gzinflate\'\.\s]+)|([strrev\'\.\s]+)|([str_rot13\'\.\s]+)|([gzuncompress\'\.\s]+)|([urldecode\'\.\s]+)([rawurldecode\'\.\s]+));)~', function($matches) use (&$vars) {
            $tmp = str_replace("' . '", '', $matches[0]);
            $tmp = str_replace("'.'", '', $tmp);
            $value = str_replace("' . '", '', $matches[2]);
            $value = str_replace("'.'", '', $value);
            $vars[$matches[1]] = substr($value, 1, -1);
            return $tmp;
        }, $res);

        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . " = '" . $var . "';", '', $res);
            $res = str_replace($var . ' = "";', '', $res);
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalWrapVar($str)
    {
        preg_match('~((\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));\s*)+\s*@?eval\([^)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $vars = array();
        $res = preg_replace_callback('~(?|(\$\w+)\s*=\s*(([base64_decode"\'\.\s]+)|([eval"\'\.\s]+)|([create_function"\'\.\s]+)|([stripslashes"\'\.\s]+)|([gzinflate"\'\.\s]+)|([strrev"\'\.\s]+)|([str_rot13"\'\.\s]+)|([gzuncompress"\'\.\s]+)|([urldecode"\'\.\s]+)([rawurldecode"\'\.\s]+));)~msi', function($matches) use (&$vars) {
            $tmp = str_replace("' . '", '', $matches[0]);
            $tmp = str_replace("'.'", '', $tmp);
            $value = str_replace("' . '", '', $matches[2]);
            $value = str_replace("'.'", '', $value);
            $vars[$matches[1]] = substr($value, 1, -1);
            return $tmp;
        }, $res);
        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . '="' . $var . '";', '', $res);
            $res = str_replace($var . ' = "' . $var . '";', '', $res);
        }

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateForEach($str)
    {
        preg_match('~(?(DEFINE)(?\'foreach\'(?:/\*\w+\*/)?\s*foreach\(\[[\d,]+\]\s*as\s*\$\w+\)\s*\{\s*\$\w+\s*\.=\s*\$\w+\[\$\w+\];\s*\}\s*(?:/\*\w+\*/)?\s*))(\$\w+)\s*=\s*"([^"]+)";\s*\$\w+\s*=\s*"";(?P>foreach)if\(isset\(\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\]\)+\{\s*\$\w+\s*=\s*\$_REQUEST\s*(?:/\*\w+\*/)?\["\$\w+"\];(?:\s*\$\w+\s*=\s*"";\s*)+(?P>foreach)+\$\w+\s*=\s*\$\w+\([create_function\'\.]+\);\s*\$\w+\s*=\s*\$\w+\("",\s*\$\w+\(\$\w+\)\);\s*\$\w+\(\);~mis', $str, $matches);
        $find = $matches[0];
        $alph = $matches[3];
        $vars = array();
        $res = $str;

        preg_replace('~\s*/\*\w+\*/\s*~msi', '', $res);

        $res = preg_replace_callback('~foreach\(\[([\d,]+)\]\s*as\s*\$\w+\)\s*\{\s*(\$\w+)\s*\.=\s*\$\w+\[\$\w+\];\s*\}~mis', function($matches) use ($alph, &$vars) {
            $chars = explode(',', $matches[1]);
            $value = '';
            foreach ($chars as $char) {
                $value .= $alph[$char];
            }
            $vars[$matches[2]] = $value;
            return "{$matches[2]} = '{$value}';";
        }, $res);

        foreach($vars as $key => $var) {
            $res = str_replace($key, $var, $res);
            $res = str_replace($var . " = '" . $var . "';", '', $res);
            $res = str_replace($var . ' = "";', '', $res);
        }

        preg_match('~(\$\w+)\s*=\s*strrev\([create_function\.\']+\);~ms', $res, $matches);
        $res = str_replace($matches[0], '', $res);
        $res = str_replace($matches[1], 'create_function', $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubst2($str)
    {
        preg_match('~(\$\w+)="([^"])+(.{0,70}\1.{0,400})+;\s*}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        preg_match('~(\$\w+)="(.+?)";~msi', $str, $matches);
        $alph = stripcslashes($matches[2]);
        $var = $matches[1];
        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($var . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($var . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        preg_match_all('~(\$GLOBALS\[\'\w{1,40}\'\])\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);

        foreach ($matches as $index => $var) {
            $res = str_replace($var[1], $var[2], $res);
            $res = str_replace($var[2] . " = '" . $var[2] . "';", '', $res);
        }

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateAssert($str)
    {
        preg_match('~(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*(\$\w+)\s*=(?:\s*(?:(?:["\'][a-z0-9][\'"])|(?:chr\s*\(\d+\))|(?:[\'"]\\\\x[0-9a-f]+[\'"]))\s*?\.?)+;\s*@?\1\s*\(@?\2\s*\([\'"]([^\'"]+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = base64_decode($matches[3]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateUrlDecode2($str)
    {
        preg_match('~(\$[\w{1,40}]+)=urldecode\(?[\'"]([\w+%=-]+)[\'"]\);(\s*\$[0O]+\.?=(\$[0O]+\{\d+\}\s*[\.;]?\s*)+)+((\$[O0]+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$O0\(\)\*\d,\s]+);|(eval\(\$[0O]+\([\'"]([^\'"]+)[\'"]\)+;))~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        if (isset($matches[9])) {
            $res = base64_decode($matches[9]);
        }
        preg_match('~\$[O0]+=["\']([^\'"]+)[\'"];\s*eval\(\'\?>\'\.[\$O0\(\)\*\d,\s]+;~msi', $res, $matches);
        $res = base64_decode(strtr(substr($matches[1], 52*2), substr($matches[1], 52, 52), substr($matches[1], 0, 52)));
        $res = str_replace($find, ' ?>' . $res, $str);
        return $res;
    }

    private function deobfuscatePHPMyLicense($str)
    {
        preg_match('~\$\w+\s*=\s*base64_decode\s*\([\'"][^\'"]+[\'"]\);\s*if\s*\(!function_exists\s*\("rotencode"\)\).{0,1000}eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"]([^"\']+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $hang = 10;
        while(preg_match('~eval\s*\(\$\w+\s*\(base64_decode\s*\([\'"]([^"\']+)[\'"]\)+;~msi', $res, $matches) && $hang--) {
            $res = gzinflate(base64_decode($matches[1]));
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEdoced_46esab($str)
    {
        preg_match('~(\$\w+)=[\'"]([^"\']+)[\'"];(\$\w+)=strrev\(\'edoced_46esab\'\);eval\(\3\([\'"]([^\'"]+)[\'"]\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $decoder = base64_decode($matches[4]);
        preg_match('~(\$\w+)=base64_decode\(\$\w+\);\1=strtr\(\1,[\'"]([^\'"]+)[\'"],[\'"]([^\'"]+)[\'"]\);~msi', $decoder, $matches2);
        $res = base64_decode($matches[2]);
        $res = strtr($res, $matches2[2], $matches2[3]);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEvalVarVar($str)
    {
        preg_match('~\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\'](\w+)[\'"];\$\{"GLOBALS"\}\[[\'"](\w+)[\'"]\]=["\']\2[\'"];(\${\$\{"GLOBALS"\}\[[\'"]\3[\'"]\]})=[\'"]([^\'"]+)[\'"];eval.{10,50}?(\$\{\$\{"GLOBALS"\}\[[\'"]\1[\'"]\]\})\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $res = str_replace($matches[4], '$' . $matches[2], $str);
        $res = str_replace($matches[6], '$' . $matches[2], $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateEscapes($str)
    {
        preg_match('~\$\{"(.{1,20}?(\\\\x[0-9a-f]{2})+)+.?";@?eval\s*\(\s*([\'"?>.]+)?@?\s*(base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+\(?\$\{\$\{"[^\)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $res = stripcslashes($str);
        $res = str_replace($find, $res, $str);
        return $res;
    }


    private function deobfuscateparenthesesString($str)
    {
        preg_match('~for\((\$\w+)=\d+,(\$\w+)=\'([^\$]+)\',(\$\w+)=\'\';@?ord\(\2\[\1\]\);\1\+\+\)\{if\(\1<\d+\)\{(\$\w+)\[\2\[\1\]\]=\1;\}else\{\$\w+\.\=@?chr\(\(\5\[\2\[\1\]\]<<\d+\)\+\(\5\[\2\[\+\+\1\]\]\)\);\}\}\s*.{0,500}eval\(\4\);(if\(isset\(\$_(GET|REQUEST|POST|COOKIE)\[[\'"][^\'"]+[\'"]\]\)\)\{[^}]+;\})?~msi', $str, $matches);
        $find = $matches[0];
        $res = '';
        $temp = array();
        $matches[3] = stripcslashes($matches[3]);
        for($i=0; $i < strlen($matches[3]); $i++)
        {
            if($i < 16) $temp[$matches[3][$i]] = $i;
            else $res .= @chr(($temp[$matches[3][$i]]<<4) + ($temp[$matches[3][++$i]]));
        }

        if(!isset($matches[6])) {
            //$xor_key = 'SjJVkE6rkRYj';
            $xor_key = $res^"\n//adjust sy"; //\n//adjust system variables";
            $res = $res ^ substr(str_repeat($xor_key, (strlen($res) / strlen($xor_key)) + 1), 0, strlen($res));
        }
        if(substr($res,0,12)=="\n//adjust sy") {
            $res = str_replace($find, $res, $str);
            return $res;
        } else return $str;
    }

    private function deobfuscateEvalInject($str)
    {
        $res = $str;
        preg_match('~(\$\w{1,40})\s*=\s*[\'"]([^\'"]*)[\'"]\s*;\s*(\$\w{1,40}\s*=\s*(strtolower|strtoupper)\s*\((\s*\1[\[\{]\s*\d+\s*[\]\}]\s*\.?\s*)+\);\s*)+\s*if\s*\(\s*isset\s*\(\s*\$\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*\{\s*eval\s*\(\s*\$\w{1,40}\s*\(\s*\$\s*\{\s*\$\w{1,40}\s*\}\s*\[\s*[\'"][^\'"]*[\'"]\s*\]\s*\)\s*\)\s*;\s*\}\s*~msi', $str, $matches);
        $find = $matches[0];
        $alph = $matches[2];

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }

        $res = str_replace("''", '', $res);
        $res = str_replace("' '", '', $res);

        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateWebshellObf($str)
    {
        $res = $str;
        preg_match('~function\s*(\w{1,40})\s*\(\s*(\$\w{1,40})\s*,\s*(\$\w{1,40})\s*\)\s*\{\s*(\$\w{1,40})\s*=\s*str_rot13\s*\(\s*gzinflate\s*\(\s*str_rot13\s*\(\s*base64_decode\s*\(\s*[\'"]([^\'"]*)[\'"]\s*\)\s*\)\s*\)\s*\)\s*;\s*(if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*(\$\w{1,40})\s*=(\$\w+[\{\[]\d+[\}\]]\.?)+;return\s*(\$\w+)\(\3\);\s*\}\s*else\s*)+\s*if\s*\(\s*\$\w+\s*==[\'"][^\'"]*[\'"]\s*\)\s*\{\s*return\s*eval\(\3\);\s*\}\s*\};\s*(\$\w{1,40})\s*=\s*[\'"][^\'"]*[\'"];(\s*\10\([\'"][^\'"]*[\'"],)+\s*[\'"]([^\'"]*)[\'"]\s*\)+;~msi',$str, $matches);
        $find = $matches[0];

        $alph = str_rot13(gzinflate(str_rot13(base64_decode($matches[5]))));

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[4] . '{' . $i . '}.', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[4] . '{' . $i . '}', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        $res = base64_decode(gzinflate(str_rot13(convert_uudecode(gzinflate(base64_decode(strrev($matches[12])))))));
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateXorFName($str)
    {
        preg_match('~(\$\w+)\s*=\s*basename\s*\(trim\s*\(preg_replace\s*\(rawurldecode\s*\([\'"][%0-9a-f\.]+["\']\),\s*\'\',\s*__FILE__\)\)\);\s*(\$\w+)\s*=\s*["\']([^\'"]+)["\'];\s*eval\s*\(rawurldecode\s*\(\2\)\s*\^\s*substr\s*\(str_repeat\s*\(\1,\s*\(strlen\s*\(\2\)/strlen\s*\(\1\)\)\s*\+\s*1\),\s*0,\s*strlen\s*\(\2\)\)\);~msi', $str, $matches);
        $find = $matches[0];
        $xored = rawurldecode($matches[3]);
        $xor_key = $xored ^ 'if (!defined(';
        $php = $xored ^ substr(str_repeat($xor_key, (strlen($matches[3]) / strlen($xor_key)) + 1), 0, strlen($matches[3]));
        preg_match('~\$\w{1,40}\s*=\s*((\'[^\']+\'\s*\.?\s*)+);\s*\$\w+\s*=\s*Array\(((\'\w\'=>\'\w\',?\s*)+)\);~msi', $php, $matches);
        $matches[1] = str_replace(array(" ", "\r", "\n", "\t", "'.'"), '', $matches[1]);
        $matches[3] = str_replace(array(" ", "'", ">"), '', $matches[3]);
        $temp = explode(',', $matches[3]);
        $array = array();
        foreach ($temp as $value) {
            $temp = explode("=", $value);
            $array[$temp[0]] = $temp[1];
        }
        $res = '';
        for ($i=0; $i < strlen($matches[1]); $i++) {
            $res .= isset($array[$matches[1][$i]]) ? $array[$matches[1][$i]] : $matches[1][$i];
        }
        $res = substr(rawurldecode($res), 1, -2);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubstCreateFunc($str)
    {
        preg_match('~(\$\w{1,40})=\'(([^\'\\\\]|\\\\.)*)\';\s*((\$\w{1,40})=(\1\[\d+].?)+;\s*)+(\$\w{1,40})=\'\';\s*(\$\w{1,40})\(\7,\$\w{1,40}\.\"([^\"]+)\"\.\$\w{1,40}\.\5\);~msi', $str, $matches);
        $find = $matches[0];
        $php = base64_decode($matches[9]);
        preg_match('~(\$\w{1,40})=(\$\w{1,40})\("([^\']+)"\)~msi', $php, $matches);
        $matches[3] = base64_decode($matches[3]);
        $php = '';
        for ($i = 1; $i < strlen($matches[3]); $i++) {
            if ($i % 2) {
                $php .= substr($matches[3], $i, 1);
            }
        }
        $php = str_replace($find, $php, $str);
        return $php;
    }

    private function deobfuscateZeura($str)
    {
        preg_match('~(\$\w{1,40})=file\(__FILE__\);if\(!function_exists\(\"([^\"]*)\"\)\)\{function\s*\2\((\$\w{1,40}),(\$\w{1,40})=\d+\)\{(\$\w{1,40})=implode\(\"[^\"]*\",\3\);(\$\w{1,40})=array\((\d+),(\d+),(\d+)\);if\(\4==0\)\s*(\$\w{1,40})=substr\(\5,\6\[\d+\],\6\[\d+\]\);elseif\(\4==1\)\s*\10=substr\(\5,\6\[\d+\]\+\6\[\d+\],\6\[\d+\]\);else\s*\10=trim\(substr\(\5,\6\[\d+\]\+\6\[\d+\]\+\6\[\d+\]\)\);return\s*\(\10\);\}\}eval\(\w{1,40}\(\2\(\1,2\),\2\(\1,1\)\)\);__halt_compiler\(\);[\w\+\=]+~msi', $str, $matches);
        $offset = intval($matches[8]) + intval($matches[9]);
        $obfPHP = explode('__halt_compiler();', $str);
        $obfPHP = end($obfPHP);
        $php = gzinflate(base64_decode(substr($obfPHP, $offset)));
        $php = str_replace($matches[0], $php, $str);
        return $php;
    }

    private function deobfuscateSourceCop($str)
    {
        preg_match('~if\(\!function_exists\(\'findsysfolder\'\)\){function findsysfolder\(\$fld\).+\$REXISTHEDOG4FBI=\'([^\']+)\';\$\w+=\'[^\']+\';\s*eval\(\w+\(\'([^\']+)\',\$REXISTHEDOG4FBI\)\);~msi', $str, $matches);
        $key = $matches[2];
        $obfPHP = $matches[1];
        $res = '';
        $index = 0;
        $len = strlen($key);
        $temp = hexdec('&H' . substr($obfPHP, 0, 2));
        for ($i = 2; $i < strlen($obfPHP); $i += 2) {
            $bytes = hexdec(trim(substr($obfPHP, $i, 2)));
            $index = (($index < $len) ? $index + 1 : 1);
            $decoded = $bytes ^ ord(substr($key, $index - 1, 1));
            if ($decoded <= $temp) {
                $decoded = 255 + $decoded - $temp;
            } else {
                $decoded = $decoded - $temp;
            }
            $res = $res . chr($decoded);
            $temp = $bytes;
        }
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateGlobalsSubst($str)
    {
        $vars = array();
        preg_match_all('~\$(\w{1,40})=\'([^\']+)\';~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = $match[2];
        }
        foreach ($vars as $var => $value) {
            $str = str_replace('$GLOBALS[\'' . $var .'\']', $value, $str);
        }
        return $str;
    }

    private function deobfuscateGlobalsArray($str)
    {
        $res = $str;
        preg_match('~\$\w+\s*=\s*\d+;\s*\$GLOBALS\[\'[^\']+\'\]\s*=\s*Array\(\);\s*global \$\w+;(\$\w{1,40})\s*=\s*\$GLOBALS;\$\{"\\\\x[a-z0-9\\\\]+"\}\[(\'\w+\')\]\s*=\s*\"(([^\"\\\\]|\\\\.)*)\";\1\[(\1\[\2\]\[\d+\].?).+?exit\(\);\}\}~msi', $str, $matches);
        $alph = stripcslashes($matches[3]);

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] .'[' . $matches[2] . ']' . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] .'[' . $matches[2] . ']' . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        preg_match_all('~\\' . $matches[1] . '\[(\'\w+\')]\s*=\s*\'(\w+)\';~msi', $res, $funcs);

        $vars = $funcs[1];
        $func = $funcs[2];

        foreach ($vars as $index => $var) {
            $res = str_replace($matches[1] . '[' . $var . ']', $func[$index], $res);
        }

        foreach ($func as $remove) {
            $res = str_replace($remove . " = '" . $remove . "';", '', $res);
        }
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateObfB64($str)
    {
        preg_match('~(\$\w{1,50}\s*=\s*array\((\'\d+\',?)+\);)+\$\w{1,40}=\"([^\"]+)\";if\s*\(!function_exists\(\"\w{1,50}\"\)\)\s*\{\s*function\s*[^\}]+\}\s*return\s*\$\w+;\}[^}]+}~msi', $str, $matches);
        $res = base64_decode($matches[3]);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateArrayOffsets($str)
    {
        $vars = array();
        preg_match('~(\$\w{1,40})\s*=\s*\'([^\']*)\';\s*(\$\w{1,40})\s*=\s*explode\s*\((chr\s*\(\s*\(\d+\-\d+\)\)),substr\s*\(\1,\s*\((\d+\-\d+)\),\s*\(\s*(\d+\-\d+)\)\)\);.+\1\s*=\s*\$\w+[+\-\*]\d+;~msi', $str, $matches);

        $find = $matches[0];
        $obfPHP = $matches[2];
        $matches[4] = Helpers::calc($matches[4]);
        $matches[5] = intval(Helpers::calc($matches[5]));
        $matches[6] = intval(Helpers::calc($matches[6]));

        $func = explode($matches[4], strtolower(substr($obfPHP, $matches[5], $matches[6])));
        $func[1] = strrev($func[1]);
        $func[2] = strrev($func[2]);

        preg_match('~\$\w{1,40}\s=\sexplode\((chr\(\(\d+\-\d+\)\)),\'([^\']+)\'\);~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $offsets = explode($matches[1], $matches[2]);

        $res = '';
        for ($i = 0; $i < (sizeof($offsets) / 2); $i++) {
            $res .= substr($obfPHP, $offsets[$i * 2], $offsets[($i * 2) + 1]);
        }

        preg_match('~return\s*\$\w{1,40}\((chr\(\(\d+\-\d+\)\)),(chr\(\(\d+\-\d+\)\)),\$\w{1,40}\);~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $matches[2] = Helpers::calc($matches[2]);

        $res = Helpers::stripsquoteslashes(str_replace($matches[1], $matches[2], $res));
        $res = "<?php\n" . $res . "?>";

        preg_match('~(\$\w{1,40})\s=\simplode\(array_map\(\"[^\"]+\",str_split\(\"(([^\"\\\\]++|\\\\.)*)\"\)\)\);(\$\w{1,40})\s=\s\$\w{1,40}\(\"\",\s\1\);\s\4\(\);~msi', $res, $matches);

        $matches[2] = stripcslashes($matches[2]);
        for ($i=0; $i < strlen($matches[2]); $i++) {
            $matches[2][$i] = chr(ord($matches[2][$i])-1);
        }

        $res = str_replace($matches[0], $matches[2], $res);

        preg_match_all('~(\$\w{1,40})\s*=\s*\"(([^\"\\\\]++|\\\\.)*)\";~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = stripcslashes($match[2]);
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = Helpers::stripsquoteslashes($match[2]);
        }

        preg_match('~(\$\w{1,40})\s*=\s*\"\\\\x73\\\\164\\\\x72\\\\137\\\\x72\\\\145\\\\x70\\\\154\\\\x61\\\\143\\\\x65";\s(\$\w{1,40})\s=\s\'(([^\'\\\\]++|\\\\.)*)\';\seval\(\1\(\"(([^\"\\\\]++|\\\\.)*)\",\s\"(([^\"\\\\]++|\\\\.)*)\",\s\2\)\);~msi', $res, $matches);

        $matches[7] = stripcslashes($matches[7]);
        $matches[3] = Helpers::stripsquoteslashes(str_replace($matches[5], $matches[7], $matches[3]));


        $res = str_replace($matches[0], $matches[3], $res);

        preg_match_all('~(\$\w{1,40})\s*=\s*\"(([^\"\\\\]++|\\\\.)*)\";~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = stripcslashes($match[2]);
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'(([^\'\\\\]++|\\\\.)*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $vars[$match[1]] = Helpers::stripsquoteslashes($match[2]);
        }

        preg_match('~\$\w{1,40}\s=\sarray\(((\'(([^\'\\\\]++|\\\\.)*)\',?(\.(\$\w{1,40})\.)?)+)\);~msi', $res, $matches);

        foreach ($vars as $var => $value) {
            $matches[1] = str_replace("'." . $var . ".'", $value, $matches[1]);
        }

        $array2 = explode("','", substr($matches[1], 1, -1));
        preg_match('~eval\(\$\w{1,40}\(array\((((\"[^\"]\"+),?+)+)\),\s(\$\w{1,40}),\s(\$\w{1,40})\)\);~msi', $res, $matches);

        $array1 = explode('","', substr($matches[1], 1, -1));

        $temp = array_keys($vars);
        $temp = $temp[9];

        $arr = explode('|', $vars[$temp]);
        $off=0;
        $funcs=array();

        for ($i = 0; $i<sizeof($arr); $i++) {
            if ($i == 0) {
                $off = 0;
            } else {
                $off = $arr[$i - 1] + $off;
            }
            $len = $arr[$i];
            $temp = array_keys($vars);
            $temp = $temp[7];

            $funcs[]= substr($vars[$temp], $off, $len);
        }

        for ($i = 0; $i < 5; $i++) {
            if ($i % 2 == 0) {
                $funcs[$i] = strrev($funcs[$i]);
                $g = substr($funcs[$i], strpos($funcs[$i], "9") + 1);
                $g = stripcslashes($g);
                $v = explode(":", substr($funcs[$i], 0, strpos($funcs[$i], "9")));
                for ($j = 0; $j < sizeof($v); $j++) {
                    $q = explode("|", $v[$j]);
                    $g = str_replace($q[0], $q[1], $g);
                }
                $funcs[$i] = $g;
            } else {
                $h = explode("|", strrev($funcs[$i]));
                $d = explode("*", $h[0]);
                $b = $h[1];
                for ($j = 0; $j < sizeof($d); $j++) {
                    $b = str_replace($j, $d[$j], $b);
                }
                $funcs[$i] = $b;
            }
        }
        $temp = array_keys($vars);
        $temp = $temp[8];
        $funcs[] = str_replace('9', ' ', strrev($vars[$temp]));
        $funcs = implode("\n", $funcs);
        preg_match('~\$\w{1,40}\s=\s\'.+?eval\([^;]+;~msi', $res, $matches);
        $res = str_replace($matches[0], $funcs, $res);
        $res = stripcslashes($res);
        $res = str_replace('}//}}', '}}', $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateXoredVar($str)
    {
        $res = $str;
        preg_match('~(\$\w{1,40})\s*=\s*\'(\\\\.|[^\']){0,100}\';\s*\$\w+\s*=\s*\'(\\\\.|[^\']){0,100}\'\^\1;[^)]+\)+;\s*\$\w+\(\);~msi', $str, $matches);
        $find = $matches[0];
        preg_match_all('~(\$\w{1,40})\s*=\s*\'((\\\\.|[^\'])*)\';~msi', $str, $matches, PREG_SET_ORDER);
        $vars = array();
        foreach ($matches as $match) {
            $vars[$match[1]]=$match[2];
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*\'((\\\\.|[^\'])*)\'\^(\$\w+);~msi', $str, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[4]])) {
                $vars[$match[1]]=$match[2]^$vars[$match[4]];
                $res = str_replace($match[0], $match[1] . "='" . $vars[$match[1]] . "';", $res);
            }
        }

        preg_match_all('~(\$\w{1,40})\s*=\s*(\$\w+)\^\'((\\\\.|[^\'])*)\';~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[2]])) {
                $vars[$match[1]]=$match[4]^$vars[$match[2]];
                $res = str_replace($match[0], $match[1] . "='" . $vars[$match[1]] . "';", $res);
            }
        }
        preg_match_all('~\'((\\\\.|[^\'])*)\'\^(\$\w+)~msi', $res, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($vars[$match[3]])) {
                $res = str_replace($match[0], "'" . addcslashes($match[1]^$vars[$match[3]], '\\\'') . "'", $res);
            }
        }
        foreach ($vars as $var => $value) {
            $res = str_replace($var, $value, $res);
            $res = str_replace($value . "='" . $value . "';", '', $res);
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscatePhpMess($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'[^\']+\'\);(\$\w+)=base64_decode\(\'([^\']+)\'\);eval\(\1\(gzuncompress\(\2\(\3\)\)\)\);~msi', $str, $matches);
        $res = base64_decode(gzuncompress(base64_decode(base64_decode($matches[4]))));
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscatePregReplaceSample05($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})\s*=\s*\"([^\"]+)\";\s*\$\w+\s*=\s*\$\w+\(\1,\"([^\"]+)\",\"([^\"]+)\"\);\s*\$\w+\(\"[^\"]+\",\"[^\"]+\",\"\.\"\);~msi', $str, $matches);
        $res = strtr($matches[2], $matches[3], $matches[4]);
        $res = base64_decode($res);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscatePregReplaceB64($str)
    {
        $res = '';
        preg_match('~(\$\w{1,40})\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\w+\(\'.+?\'\);\s*(\$\w+)\s*=\s*\"([^\"]+)\";\s*(\$\w+)\s*=\s*.+?;\s*\2\(\5,\"[^\']+\'\3\'[^\"]+\",\"\.\"\);~msi', $str, $matches);
        $find = $matches[0];
        $res = str_replace($find, base64_decode($matches[4]), $str);
        $res = stripcslashes($res);
        preg_match('~eval\(\${\$\{"GLOBALS"\}\[\"\w+\"\]}\(\${\$\{"GLOBALS"\}\[\"\w+\"]}\(\"([^\"]+)\"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match('~eval\(\$\w+\(\$\w+\("([^"]+)"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match('~eval\(\$\w+\(\$\w+\("([^"]+)"\)\)\);~msi', $res, $matches);
        $res = gzuncompress(base64_decode($matches[1]));
        preg_match_all('~\$(\w+)\s*(\.)?=\s*("[^"]*"|\$\w+);~msi', $res, $matches, PREG_SET_ORDER);
        $var = $matches[0][1];
        $vars = array();
        foreach ($matches as $match) {
            if($match[2]!=='.') {
                $vars[$match[1]] = substr($match[3], 1, -1);
            }
            else {
                $vars[$match[1]] .= $vars[substr($match[3], 1)];
            }
        }
        $res = str_replace("srrKePJUwrMZ", "=", $vars[$var]);
        $res = gzuncompress(base64_decode($res));
        preg_match_all('~function\s*(\w+)\(\$\w+,\$\w+\)\{.+?}\s*};\s*eval\(((\1\(\'(\w+)\',)+)\s*"([\w/\+]+)"\)\)\)\)\)\)\)\);~msi', $res, $matches);
        $decode = array_reverse(explode("',", str_replace($matches[1][0] . "('", '', $matches[2][0])));
        array_shift($decode);
        $arg = $matches[5][0];
        foreach ($decode as $val) {
            $arg = Helpers::someDecoder2($val, $arg);
        }
        $res = $arg;
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateDecoder($str)
    {
        preg_match('~if\(!function_exists\(\"(\w+)\"\)\){function \1\(.+eval\(\1\(\"([^\"]+)\"\)\);~msi', $str, $matches);
        $res = Helpers::someDecoder($matches[2]);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateGBE($str)
    {
        preg_match('~(\$\w{1,40})=\'([^\']+)\';\1=gzinflate\(base64_decode\(\1\)\);\1=str_replace\(\"__FILE__\",\"\'\$\w+\'\",\1\);eval\(\1\);~msi', $str, $matches);
        $res = str_replace($matches[0], gzinflate(base64_decode($matches[2])), $str);
        return $res;
    }

    private function deobfuscateGBZ($str)
    {
        preg_match('~(\$\w{1,40})\s*=\s*\"riny\(\"\.(\$\w+)\(\"base64_decode\"\);\s*(\$\w+)\s*=\s*\2\(\1\.\'\("([^"]+)"\)\);\'\);\s*\$\w+\(\3\);~msi', $str, $matches);
        $res = str_replace($matches[0], base64_decode(str_rot13($matches[4])), $str);
        return $res;
    }

    private function deobfuscateBitrix($str)
    {
        preg_match('~(\$GLOBALS\[\s*[\'"]_+\w{1,60}[\'"]\s*\])\s*=\s*\s*array\s*\(\s*base64_decode\s*\(.+?((.+?\1\[\d+\]).+?)+[^;]+;(\s*include\(\$_\d+\);)?}?((.+?___\d+\(\d+\))+[^;]+;)?~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $funclist = array();
        $strlist = array();
        $res = preg_replace("|[\"']\s*\.\s*['\"]|smi", '', $res);
        $hangs = 0;
        while (preg_match('~(?:min|max|round)?\(\s*\d+[\.\,\|\s\|+\|\-\|\*\|\/]([\d\s\.\,\+\-\*\/]+)?\)~msi', $res) && $hangs < 15) {
            $res = preg_replace_callback('~(?:min|max|round)?\(\s*\d+[\.\,\|\s\|+\|\-\|\*\|\/]([\d\s\.\,\+\-\*\/]+)?\)~msi', array("Helpers","calc"), $res);
            $hangs++;
        }

        $res = preg_replace_callback(
            '|base64_decode\(["\'](.*?)["\']\)|smi',
            function ($matches) {
                return '"' . base64_decode($matches[1]) . '"';
            },
            $res
        );

        if (preg_match_all('|\$GLOBALS\[[\'"](.+?)[\'"]\]\s*=\s*Array\((.+?)\);|smi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $varname = $found[1];
                $funclist[$varname] = explode(',', $found[2]);
                $funclist[$varname] = array_map(function ($value) {
                    return trim($value, "'\"");
                }, $funclist[$varname]);

                $res = preg_replace_callback(
                    '|\$GLOBALS\[[\'"]' . $varname . '[\'"]\]\[(\d+)\]|smi',
                    function ($matches) use ($varname, $funclist) {
                        return str_replace(array('"',"'"), '', $funclist[$varname][$matches[1]]);
                    },
                    $res
                );
                $res = str_replace($found[0], '', $res);
            }
        }

        if (preg_match_all('~function\s*(\w{1,60})\(\$\w+\){\$\w{1,60}\s*=\s*Array\((.{1,30000}?)\);\s*return\s*base64_decode[^}]+}~msi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $strlist = explode(',', $found[2]);
                $res = preg_replace_callback(
                    '|' . $found[1] . '\((\d+)\)|smi',
                    function ($matches) use ($strlist) {
                        return "'" . base64_decode($strlist[$matches[1]]) . "'";
                    },
                    $res
                );
                $res = str_replace($found[0], '', $res);
            }
        }

        if (preg_match_all('~\s*function\s*(_+(.{1,60}?))\(\$[_0-9]+\)\s*\{\s*static\s*\$([_0-9]+)\s*=\s*(true|false);.{1,30000}?\$\3\s*=\s*array\((.*?)\);\s*return\s*base64_decode\(\$\3~smi', $res, $founds, PREG_SET_ORDER)) {
            foreach ($founds as $found) {
                $strlist = explode('",', $found[5]);
                $strlist = implode("',", $strlist);
                $strlist = explode("',", $strlist);
                $res = preg_replace_callback(
                    '|' . $found[1] . '\((\d+(\.\d+)?)\)|sm',
                    function ($matches) use ($strlist) {
                        return $strlist[$matches[1]] . '"';
                    },
                    $res
                );
            }
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateLockIt($str)
    {
        preg_match('~\$[O0]*=urldecode\(\'[%a-f0-9]+\'\);(\$(GLOBALS\[\')?[O0]*(\'\])?=(\d+);)?\s*(\$(GLOBALS\[\')?[O0]*(\'\])?\.?=(\$(GLOBALS\[\')?[O0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+\?\>[\s\w\~\=\/\+\\\\\^\{]+~msi', $str, $matches);
        $find = $matches[0];
        $obfPHP        = $str;
        $phpcode       = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($obfPHP)));
        $hexvalues     = Helpers::getHexValues($phpcode);
        $tmp_point     = Helpers::getHexValues($obfPHP);

        if (isset($tmp_point[0]) && $tmp_point[0]!=='') {
            $pointer1 = hexdec($tmp_point[0]);
        }
        if (isset($matches[4]) && $matches[4]!=='') {
            $pointer1 = $matches[4];
        }

        $needles       = Helpers::getNeedles($phpcode);
        if ($needles[2]=='__FILE__') {
            $needle        = $needles[0];
            $before_needle = $needles[1];
            preg_match('~\$_F=__FILE__;\$_X=\'([^\']+)\';\s*eval\s*\(\s*\$?\w{1,60}\s*\(\s*[\'"][^\'"]+[\'"]\s*\)\s*\)\s*;~msi', $str, $matches);
            $res = base64_decode($matches[1]);
            $phpcode = strtr($res, $needle, $before_needle);
        } else {
            $needle        = $needles[count($needles) - 2];
            $before_needle = end($needles);
            if (preg_match('~\$\w{1,40}\s*=\s*__FILE__;\s*\$\w{1,40}\s*=\s*([\da-fx]+);\s*eval\s*\(\$?\w+\s*\([\'"][^\'"]+[\'"]\)\);\s*return\s*;\s*\?>(.+)~msi', $str, $matches)) {
                $pointer1 = $matches[1];
                if (strpos($pointer1, '0x')!==false) {
                    $pointer1 = hexdec($pointer1);
                }
            }
            $temp = strtr($obfPHP, $needle, $before_needle);
            $end = 8;
            for ($i = strlen($temp) - 1; $i > strlen($temp) - 15; $i--) {
                if ($temp[$i] == '=') {
                    $end = strlen($temp) - 1 - $i;
                }
            }
            $phpcode = base64_decode(substr($temp, strlen($temp) - $pointer1 - $end, $pointer1));
        }
        $phpcode = str_replace($find, $phpcode, $str);
        return $phpcode;
    }

    private function deobfuscateB64inHTML($str)
    {
        $obfPHP        = $str;
        $phpcode       = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($obfPHP)));
        $needles       = Helpers::getNeedles($phpcode);
        $needle        = $needles[count($needles) - 2];
        $before_needle = end($needles);
        if (preg_match('~\$\w{1,40}\s*=\s*(__FILE__|__LINE__);\s*\$\w{1,40}\s*=\s*(\d+);\s*eval(\s*\()+\$?\w+\s*\([\'"][^\'"]+[\'"](\s*\))+;\s*return\s*;\s*\?>(.+)~msi', $str, $matches)) {
            $pointer1 = $matches[2];
        }
        $temp = strtr($obfPHP, $needle, $before_needle);
        $end = 8;
        for ($i = strlen($temp) - 1; $i > strlen($temp) - 15; $i--) {
            if ($temp[$i] == '=') {
                $end = strlen($temp) - 1 - $i;
            }
        }

        $phpcode = base64_decode(substr($temp, strlen($temp) - $pointer1 - ($end-1), $pointer1));
        $phpcode = str_replace($matches[0], $phpcode, $str);
        return $phpcode;
    }

    private function deobfuscateStrtrFread($str)
    {
        preg_match('~\$[O0]+=\(base64_decode\(strtr\(fread\(\$[O0]+,(\d+)\),\'([^\']+)\',\'([^\']+)\'\)\)\);eval\([^\)]+\)+;~msi', $str, $layer2);
        $str = explode('?>', $str);
        $str = end($str);
        $res = substr($str, $layer2[1], strlen($str));
        $res = base64_decode(strtr($res, $layer2[2], $layer2[3]));
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateStrtrBase64($str)
    {
        preg_match('~(\$\w{1,40})="([\w\]\[\<\&\*\_+=/]{300,})";\$\w+=\$\w+\(\1,"([\w\]\[\<\&\*\_+=/]+)","([\w\]\[\<\&\*\_+=/]+)"\);~msi', $str, $matches);
        $str = strtr($matches[2], $matches[3], $matches[4]);
        $res = base64_decode($str);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateByteRun($str)
    {
        preg_match('~\$_F=__FILE__;\$_X=\'([^\']+)\';\s*eval\s*\(\s*\$?\w{1,60}\s*\(\s*[\'"][^\'"]+[\'"]\s*\)\s*\)\s*;~msi', $str, $matches);
        $res = base64_decode($matches[1]);
        $res = strtr($res, '123456aouie', 'aouie123456');
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateExplodeSubst($str)
    {
        preg_match('~\$\w+\s*=\s*array\((\'[^\']+\',?)+\);\s*.+?(\$_\w{1,40}\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\2\[[a-fx\d]+\])\(\);(.+?\2)+.+}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        preg_match_all('~function ([\w_]+)\(~msi', $res, $funcs);
        preg_match('~(\$_\w+\[\w+\])\s*=\s*explode\(\'([^\']+)\',\s*\'([^\']+)\'\);.+?(\1\[[a-fx\d]+\])\(\);~msi', $res, $matches);
        $subst_array = explode($matches[2], $matches[3]);
        $subst_var = $matches[1];
        $res = preg_replace_callback('~((\$_GET\[[O0]+\])|(\$[O0]+))\[([a-fx\d]+)\]~msi', function ($matches) use ($subst_array, $funcs) {
            if (function_exists($subst_array[hexdec($matches[4])]) || in_array($subst_array[hexdec($matches[4])], $funcs[1])) {
                return $subst_array[hexdec($matches[4])];
            } else {
                return "'" . $subst_array[hexdec($matches[4])] . "'";
            }
        }, $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateSubst($str)
    {
        preg_match('~(\$[\w{1,40}]+)\s*=\s*\'([\w+%=\-\#\\\\\'\*]+)\';(\$[\w+]+)\s*=\s*Array\(\);(\3\[\]\s*=\s*(\1\[\d+\]\.?)+;+)+(.+\3)[^}]+}~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $alph = stripcslashes($matches[2]);
        $funcs = $matches[4];

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[1] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[1] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);
        $var = $matches[3];

        preg_match_all('~\\' . $var . '\[\]\s*=\s*\'([\w\*\-\#]+)\'~msi', $res, $matches);

        for ($i = 0; $i <= count($matches[1]); $i++) {
            if (@function_exists($matches[1][$i])) {
                $res = str_replace($var . '[' . $i . ']', $matches[1][$i], $res);
            } else {
                $res = @str_replace($var . '[' . $i . ']', "'" . $matches[1][$i] . "'", $res);
            }
        }
        $res = str_replace($find, $res, $str);
        return $res;
    }

    private function deobfuscateUrldecode($str)
    {
        preg_match('~(\$\w+=\'[^\']+\';\s*)+(\$[\w{1,40}]+)=(urldecode|base64_decode){0,1}\(?[\'"]([\w+%=-]+)[\'"]\)?;(\$[\w+]+=(\$(\w+\[\')?[O_0]*(\'\])?([\{\[]\d+[\}\]])?\.?)+;)+[^\?]+(\?\>[\w\~\=\/\+]+|.+\\\\x[^;]+;)~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = stripcslashes($res);
        if ($matches[3] == "urldecode") {
            $alph = urldecode($matches[4]);
            $res = str_replace('urldecode(\'' . $matches[4] . '\')', "'" . $alph . "'", $res);
        } elseif ($matches[3] == 'base64_decode') {
            $alph = base64_decode($matches[4]);
            $res = str_replace('base64_decode(\'' . $matches[4] . '\')', "'" . $alph . "'", $res);
        } else {
            $alph = $matches[4];
        }

        for ($i = 0; $i < strlen($alph); $i++) {
            $res = str_replace($matches[2] . '[' . $i . '].', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '[' . $i . ']', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '{' . $i . '}.', "'" . $alph[$i] . "'", $res);
            $res = str_replace($matches[2] . '{' . $i . '}', "'" . $alph[$i] . "'", $res);
        }
        $res = str_replace("''", '', $res);

        preg_match_all('~\$(\w+)\s*=\s*\'([\w\*\-\#]+)\'~msi', $res, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i++) {
            if (@function_exists($matches[$i][2])) {
                $res = str_replace('$' . $matches[$i][1], $matches[$i][2], $res);
                $res = str_replace('${"GLOBALS"}["' . $matches[$i][1] . '"]', $matches[$i][2], $res);
            } else {
                $res = str_replace('$' . $matches[$i][1], "'" . $matches[$i][2] . "'", $res);
                $res = str_replace('${"GLOBALS"}["' . $matches[$i][1] . '"]', "'" . $matches[$i][2] . "'", $res);
            }
            $res = str_replace("'" . $matches[$i][2] . "'='" . $matches[$i][2] . "';", '', $res);
            $res = str_replace($matches[$i][2] . "='" . $matches[$i][2] . "';", '', $res);
            $res = str_replace($matches[$i][2] . "=" . $matches[$i][2] . ';', '', $res);
        }
        $res = Helpers::replaceCreateFunction($res);
        preg_match('~\$([0_O]+)\s*=\s*function\s*\((\$\w+)\)\s*\{\s*\$[O_0]+\s*=\s*substr\s*\(\2,(\d+),(\d+)\);\s*\$[O_0]+\s*=\s*substr\s*\(\2,([\d-]+)\);\s*\$[O_0]+\s*=\s*substr\s*\(\2,(\d+),strlen\s*\(\2\)-(\d+)\);\s*return\s*gzinflate\s*\(base64_decode\s*\(\$[O_0]+\s*\.\s*\$[O_0]+\s*\.\s*\$[O_0]+\)+;~msi', $res, $matches);
        $res = preg_replace_callback('~\$\{"GLOBALS"}\["' . $matches[1] . '"\]\s*\(\'([^\']+)\'\)~msi', function ($calls) use ($matches) {
            $temp1 = substr($calls[1], $matches[3], $matches[4]);
            $temp2 = substr($calls[1], $matches[5]);
            $temp3 = substr($calls[1], $matches[6],strlen($calls[1]) - $matches[7]);
            return "'" . gzinflate(base64_decode($temp1 . $temp3 . $temp2)) . "'";
        }, $res);
        $res = str_replace($find, $res, $str);
        return $res;
    }

    public function unwrapFuncs($string, $level = 0)
    {
        $close_tag = false;
        $res = '';

        if (trim($string) == '') {
            return '';
        }
        if ($level > 100) {
            return '';
        }

        if ((($string[0] == '\'') || ($string[0] == '"')) && (substr($string, 1, 2) != '?>')) {
            if($string[0] == '"' && preg_match('~\\\\x\d+~', $string)) {
                return stripcslashes($string);
            } else {
                return substr($string, 1, -2);
            }
        } elseif ($string[0] == '$') {
            preg_match('~\$\w{1,40}~', $string, $string);
            $string = $string[0];
            $matches = array();
            if (!@preg_match_all('~\\' . $string . '\s*=\s*(("([^;"\\\]+)(\\\)?)+");~msi', $this->full_source, $matches)) {
                @preg_match_all('~\\' . $string . '\s*=\s*((\'([^;\'\\\]+)(\\\)?)+\');~msi', $this->full_source, $matches);
                $str = @$matches[1][0];
            } else {
                $str = $matches[1][0];
            }
            return substr($str, 1, -1);
        } else {
            $pos      = strpos($string, '(');
            $function = substr($string, 0, $pos);
            $arg      = $this->unwrapFuncs(substr($string, $pos + 1), $level + 1);
            if (strpos($function, '?>') !== false) {
                $function = str_replace("'?>'.", "", $function);
                $function = str_replace('"?>".', "", $function);
                $function = str_replace("'?>' .", "", $function);
                $function = str_replace('"?>" .', "", $function);
                $close_tag = true;
            }
            $function = str_replace(array('@',' '), '', $function);
            if (strtolower($function) == 'base64_decode') {
                $res = @base64_decode($arg);
            } elseif (strtolower($function) == 'gzinflate') {
                $res = @gzinflate($arg);
            } elseif (strtolower($function) == 'gzuncompress') {
                $res = @gzuncompress($arg);
            } elseif (strtolower($function) == 'strrev') {
                $res = @strrev($arg);
            } elseif (strtolower($function) == 'str_rot13') {
                $res = @str_rot13($arg);
            } elseif (strtolower($function) == 'urldecode') {
                $res = @urldecode($arg);
            } elseif (strtolower($function) == 'rawurldecode') {
                $res = @rawurldecode($arg);
            } elseif (strtolower($function) == 'stripslashes') {
                $res = @stripslashes($arg);
            } else {
                $res = $arg;
            }
            if ($close_tag) {
                $res = "?> " . $res;
                $close_tag = false;
            }
            return $res;
        }
    }

    private function deobfuscateEvalFunc($str)
    {
        $res = $str;
        $res = stripcslashes($res);
        preg_match('~function\s*(\w{1,40})\((\$\w{1,40})\)\s*\{\s*(\$\w{1,40})\s*=\s*\"base64_decode\";\s*(\$\w{1,40})\s*=\s*\"gzinflate\";\s*return\s*\4\(\3\(\2\)\);\s*\}\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*\$\w{1,40}\s*=\s*\"[^\"]*\";\s*eval\(\1\(\"([^\"]*)\"\)\);~msi', $res, $matches);
        $res = gzinflate(base64_decode($matches[5]));
        $res = str_replace($str, $res, $str);
        return $res;
    }

    private function deobfuscateEvalHex($str)
    {
        preg_match('~eval\s*\("(\\\\x?\d+[^"]+)"\);~msi', $str, $matches);
        $res = stripcslashes($matches[1]);
        $res = str_replace($matches[1], $res, $res);
        $res = str_replace($matches[0], $res, $str);
        return $res;
    }

    private function deobfuscateEvalVar($str)
    {
        preg_match('~((\$\w+)\s*=\s*[\'"]([^\'"]+)[\'"];)\s*.{0,10}?@?eval\s*\((base64_decode\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\()+(\({0,1}\2\){0,1})\)+;~msi', $str, $matches);
        $string = str_replace($matches[1], '', $matches[0]);
        $text = "'" . addcslashes(stripcslashes($matches[3]), "\\'") . "'";
        $string = str_replace($matches[5], $text, $string);
        $res = str_replace($matches[0], $string, $str);
        return $res;
    }

    private function deobfuscateEval($str)
    {
        $res = $str;
        if (preg_match('~(preg_replace\(["\']/\.\*?/[^"\']+\"\s*,\s*)[^\),]+(?:\)+;[\'"])?(,\s*["\'][^"\']+["\'])\)+;~msi', $res, $matches)) {
            $res = str_replace($matches[1], 'eval(', $res);
            $res = str_replace($matches[2], '', $res);
            return $res;
        }

        if (preg_match('~((\$\w+)\s*=\s*create_function\(\'\',\s*)[^\)]+\)+;\s*(\2\(\);)~msi', $res, $matches)) {
            $res = str_replace($matches[1], 'eval(', $res);
            $res = str_replace($matches[3], '', $res);
            return $res;
        }

        if (preg_match('~eval\s*/\*[\w\s\.:,]+\*/\s*\(~msi', $res, $matches)) {
            $res = str_replace($matches[0], 'eval(', $res);
            return $res;
        }

        preg_match('~@?eval\s*\(\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi', $res, $matches);
        $string = $matches[0];
        if (preg_match('~\$_(POST|GET|REQUEST|COOKIE)~ms', $res)) {
            return $res;
        }
        $string = substr($string, 5, strlen($string) - 7);
        $res = $this->unwrapFuncs($string);
        $res = str_replace($str, $res, $str);

        return $res;
    }

    private function deobfuscateEcho($str)
    {
        $res = $str;

        preg_match('~@?echo\s*([\'"?>.\s]+)?@?\s*(base64_decode\s*\(|stripslashes\s*\(|gzinflate\s*\(|strrev\s*\(|str_rot13\s*\(|gzuncompress\s*\(|urldecode\s*\(|rawurldecode\s*\(|eval\s*\()+.*?[^\'")]+((\s*\.?[\'"]([^\'";]+\s*)+)?\s*[\'"\);]+)+~msi', $res, $matches);
        $string = $matches[0];
        if (preg_match('~\$_(POST|GET|REQUEST|COOKIE)~ms', $res)) {
            return $res;
        }
        $string = substr($string, 5, strlen($string) - 7);
        $res = $this->unwrapFuncs($string);
        $res = str_replace($str, $res, $str);

        return $res;
    }

    private function deobfuscateFOPO($str)
    {
        preg_match('~(\$\w{1,40})\s*=\s*\"(\\\\142|\\\\x62)[0-9a-fx\\\\]+";\s*@?eval\s*\(\1\s*\([^\)]+\)+\s*;~msi', $str, $matches);
        $phpcode = Helpers::formatPHP($str);
        $phpcode = base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode)));
        if (strpos($phpcode, 'eval') !== false) {
            preg_match_all('~\$\w+\(\$\w+\(\$\w+\("[^"]+"\)+~msi', $phpcode, $matches2);
            @$phpcode = gzinflate(base64_decode(str_rot13(Helpers::getTextInsideQuotes(end(end($matches2))))));
            $old = '';
            $hangs = 0;
            while (($old != $phpcode) && (strlen(strstr($phpcode, 'eval($')) > 0) && $hangs < 30) {
                $old = $phpcode;
                $funcs = explode(';', $phpcode);
                if (count($funcs) == 5) {
                    $phpcode = gzinflate(base64_decode(str_rot13(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode)))));
                } elseif (count($funcs) == 4) {
                    $phpcode = gzinflate(base64_decode(Helpers::getTextInsideQuotes(Helpers::getEvalCode($phpcode))));
                }
                $hangs++;
            }
        }
        $res = str_replace($matches[0], substr($phpcode, 2), $str);
        return $res;
    }

    private function deobfuscateFakeIonCube($str)
    {
        $subst_value = 0;
        preg_match('~if\s*\(\!extension_loaded\(\'IonCube_loader\'\)\).+pack\(\"H\*\",\s*\$__ln\(\"/\[A-Z,\\\\r,\\\\n\]/\",\s*\"\",\s*substr\(\$__lp,\s*([0-9a-fx]+\-[0-9a-fx]+)\)\)\)[^\?]+\?\>\s*[0-9a-z\r\n]+~msi', $str, $matches);
        $matches[1] = Helpers::calc($matches[1]);
        $subst_value = intval($matches[1])-21;
        $code = @pack("H*", preg_replace("/[A-Z,\r,\n]/", "", substr($str, $subst_value)));
        $res = str_replace($matches[0], $code, $str);
        return $res;
    }

    private function deobfuscateCobra($str)
    {
        preg_match('~explode\(\"\*\*\*\",\s*\$\w+\);\s*eval\(eval\(\"return strrev\(base64_decode\([^\)]+\)+;~msi', $str, $matches);
        $find = $matches[0];
        $res = $str;
        $res = preg_replace_callback(
            '~eval\(\"return strrev\(base64_decode\(\'([^\']+)\'\)\);\"\)~msi',
            function ($matches) {
                return strrev(base64_decode($matches[1]));
            },
            $res
        );

        $res = preg_replace_callback(
            '~eval\(gzinflate\(base64_decode\(\.\"\'([^\']+)\'\)\)\)\;~msi',
            function ($matches) {
                return gzinflate(base64_decode($matches[1]));
            },
            $res
        );

        preg_match('~(\$\w{1,40})\s*=\s*\"([^\"]+)\"\;\s*\1\s*=\s*explode\(\"([^\"]+)\",\s*\s*\1\);~msi', $res, $matches);
        $var = $matches[1];
        $decrypt = base64_decode(current(explode($matches[3], $matches[2])));
        $decrypt = preg_replace_callback(
            '~eval\(\"return strrev\(base64_decode\(\'([^\']+)\'\)\);\"\)~msi',
            function ($matches) {
                return strrev(base64_decode($matches[1]));
            },
            $decrypt
        );

        $decrypt = preg_replace_callback(
            '~eval\(gzinflate\(base64_decode\(\.\"\'([^\']+)\'\)\)\)\;~msi',
            function ($matches) {
                return gzinflate(base64_decode($matches[1]));
            },
            $decrypt
        );

        preg_match('~if\(\!function_exists\(\"(\w+)\"\)\)\s*\{\s*function\s*\1\(\$string\)\s*\{\s*\$string\s*=\s*base64_decode\(\$string\)\;\s*\$key\s*=\s*\"(\w+)\"\;~msi', $decrypt, $matches);

        $decrypt_func = $matches[1];
        $xor_key = $matches[2];

        $res = preg_replace_callback(
            '~\\' . $var . '\s*=\s*.*?eval\(' . $decrypt_func . '\(\"([^\"]+)\"\)\)\;\"\)\;~msi',
            function ($matches) use ($xor_key) {
                $string = base64_decode($matches[1]);
                $key = $xor_key;
                $xor = "";
                for ($i = 0; $i < strlen($string);) {
                    for ($j = 0; $j < strlen($key); $j++,$i++) {
                        if (isset($string{$i})) {
                            $xor .= $string{$i} ^ $key{$j};
                        }
                    }
                }
                return $xor;
            },
            $res
        );
        $res = str_replace($find, $res, $str);
        return $res;
    }
}


/**
 * Class Factory.
 */
class Factory
{
    /**
     * @var Factory
     */
    private static $instance;
    /**
     * @var array
     */
    private static $config;

    /**
     * Factory constructor.
     *
     * @throws Exception
     */
    private function __construct()
    {

    }

    /**
     * Instantiate and return a factory.
     *
     * @return Factory
     * @throws Exception
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Configure a factory.
     *
     * This method can be called only once.
     *
     * @param array $config
     * @throws Exception
     */
    public static function configure($config = [])
    {
        if (self::isConfigured()) {
            throw new Exception('The Factory::configure() method can be called only once.');
        }

        self::$config = $config;
    }

    /**
     * Return whether a factory is configured or not.
     *
     * @return bool
     */
    public static function isConfigured()
    {
        return self::$config !== null;
    }

    /**
     * Creates and returns an instance of a particular class.
     *
     * @param string $class
     *
     * @param array $constructorArgs
     * @return mixed
     * @throws Exception
     */
    public function create($class, $constructorArgs = [])
    {
        if (!isset(self::$config[$class])) {
            throw new Exception("The factory is not contains configuration for '{$class}'.");
        }

        if (is_callable(self::$config[$class])) {
            return call_user_func(self::$config[$class], $constructorArgs);
        } else {
            return new self::$config[$class](...$constructorArgs);
        }
    }
}
