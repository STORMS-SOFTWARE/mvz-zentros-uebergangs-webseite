<?php
/*
 * required fields:
 * - "message"
 *
 * Note: fields prefixed with "_" are automatically ignored for the mail content generation
 */

/* @var $blade \eftec\bladeone\BladeOne */
/* @var $_ \STORMS\webframe\Core\WebFrame */
/* @var $cfg \STORMS\webframe\Modules\MailFormsConfig */
/* @var $config STORMS\webframe\Modules\MailForms\MailerConfig */
/* @var $current_base String containing the base path to the current module */

//use STORMS\webframe\Modules\MailLog;

if(! $cfg instanceof \STORMS\webframe\Modules\MailFormsConfig )
    die('NO DIRECT ACCESS');

$success_callback = null;

$visitor_mail_addr = null; // the mail address of the person who filled out the contact form
foreach(['email', 'mail', 'e-mail'] as $key) {
    if(isset($_POST[$key]) && !empty($_POST[$key])) {
        $visitor_mail_addr = filter_var($_POST[$key], FILTER_SANITIZE_EMAIL);
        //unset($_POST[$key]); // delete the original field so we won't have it within the mail html twice (through auto mail content gen.)
        break;
    }
}

$config_name = filter_var($_POST['config'], FILTER_SANITIZE_STRING);
$config = $cfg->formConfigs($config_name, $success_callback, $visitor_mail_addr); // Note: if nothing is returned by 'formConfigs', $config is null
if($config===null)
    throw new Exception("Mail-Config '$config_name' not found. The formConfigs method may return false in order to just skip the handler.");

if($cfg->bypassHandler() || $config === false)
    return;

// simple bot prot (using timespan between creation of the config object and the time the post was received)
$request_quicker_then_expected = !(time() > ($_SESSION['last_load'] + $config->getMinimumDelay()));

//s($config);

$mail_transport = $config->getTransport(); // should contain an PHPMailer or SwiftMailer object

$reCAPTCHA_success = true;
if(Config::RECAPTCHA_ENABLED && $config->usesRecaptcha()) {
    //if(!_isDev()) { // just do not check on dev
        $reCAPTCHA_response = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.Config::RECAPTCHA_SECRET_KEY.'&response='.$_POST['g-recaptcha-response']),true);
        if(!$reCAPTCHA_response['success'])
            $reCAPTCHA_success = false;
    //}
}

if(!isset($_POST['reqs'])) // this will force that reqs is at least passed to the handler
    $allRequiredFieldsSets = false;
else
    $allRequiredFieldsSets = true;
define('REQS', explode(',',$_POST['reqs']));
$missingFields = [];
foreach (REQS as $req_field) {
    $req_field = str_replace('[]', '', $req_field);
    if(is_array($_POST[$req_field]??''))
        $val = implode(', ', $_POST[$req_field]);
    else
        $val = trim(strip_tags($_POST[$req_field]??''));
    if($val==='' && strpos($val, '_') !== 0) { // && ($_FILES[$req_field]['size'] ?? 0) === 0
        $allRequiredFieldsSets = false;
        $missingFields[] = $req_field;
    }
}

/*
 * file attachments feature
 */
if ($_FILES && $config->getAttachmentsAllowed()) {

    if (!is_string($_FILES[array_key_first($_FILES)]['name'])){ // Check if there is only one file
        // transform the file array to be more accessible
        foreach (array_keys($_FILES) as $key) {
            $files = $_FILES[$key];
            $fileCount = count($files['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                $fileArr = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                    'full_path' => $files['full_path'][$i],
                ];
                $_FILES[$key][$i] = $fileArr;
            }
            foreach (array_keys($fileArr) as $key_to_remove) {
                unset($_FILES[$key][$key_to_remove]);
            }
        }
        // attach all the files
        foreach($_FILES as $key => $files) {
            foreach($files as $file) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $file['name'])) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadfile))
                    $mail_transport->addAttachment($uploadfile, $file['name']);
            }
        }
    }else{
        // attach the file
        foreach($_FILES as $file) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $file['name'])) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadfile))
                $mail_transport->addAttachment($uploadfile, $file['name']);
        }
    }
}
elseif($_FILES && !$config->getAttachmentsAllowed())
    throw new Exception('File attachments are not allowed for this form.');

$msg = strip_tags(filter_var($_POST['message'] ?? $_POST['msg'] ?? $_POST['nachricht'] ?? '', FILTER_SANITIZE_STRING), '<br><p><b><u><strong><i>');

$mail_body = $blade->runChild(
    ($config->getMailTemplate() ?? ($current_base . '/views/mail-template.blade.php')), compact('_', 'config', 'blade', 'msg', 'visitor_mail_addr')
);

$mail_transport->Body = $mail_body;

if(!$request_quicker_then_expected && $reCAPTCHA_success) {
    $success = $mail_transport->send();

    if($success && is_callable($success_callback))
        $success_callback_ret = $success_callback($visitor_mail_addr);
}
else
    $success = false;

if($config->getLogMail()) {
    // $mail = null, $name = null, $content = null, $success = false
    //MailLog::appendLogEntry(implode(', ', array_keys($mail_transport->getAllRecipientAddresses())), '?', $mail_body, $success);
}

?>

@section('status_html')
    @if($request_quicker_then_expected)
        <div class="alert alert-danger">
            {{-- ... Die Datei wurde zu schnell angepostet. BOT PROT... --}}
            @if(_isDev())
                [DEV-NOTE: BOT PROTECTION] &mdash;
            @endif
            🤖 Der Server kann Ihre Anfrage aktuell nicht bearbeiten.
        </div>
    @elseif(!$allRequiredFieldsSets)
        <div class="alert alert-danger">
            Es wurden nicht alle Pflicht-Felder ausgefüllt. Bitte komplettieren Sie Ihre Eingaben.
        </div>
        @if(_isDev())
            <h5>[DEV] FOLGENDE FELDER MÜSSEN GEFÜLLT SEIN:</h5>
            <?php
            // generate debug table
            ob_start();
            echo "key | ".str_repeat('&nbsp;', 10)." | val \n";
            echo "-   | - \n";
            foreach(array_intersect_key(REQS, array_keys($_POST)) as $key)
                echo '$_POST["'.$key.'"] | '.str_repeat('&nbsp;', 4).'=>'.str_repeat('&nbsp;', 4).' | ' . ((isset($_POST[$key]) && !empty($_POST[$key]) ? $_POST[$key] : '**!!!!!NICHT GESETZT!!!!!**') . "\n");
            echo _md(ob_get_clean()); ?>
        @endif
    @elseif($reCAPTCHA_success)
        @if(_isDev())
            <div style="background-color: white; padding: 60px; border: 1px solid black; color: black;">
                {!!$mail_body!!}
            </div>
            <?php
            d($_POST);
            d('receiver:', $mail_transport->getAllRecipientAddresses());
            d('mail subject:', $mail_transport->Subject);
            ?>
        @endif

        @if(isset($success))
            <div class="alert alert-{{$success ? 'success' : 'danger'}}">
                {!! $success ? $config->getMessageSuccess() : $config->getMessageError() !!}
            </div>
        {{--
        @elseif(HANDLER_USES_FILE_ATTACHMENTS)
            {!! $failed_files_markup !!}
        --}}
        @else
            <div class="alert alert-info">
                PHP Mailer nicht verwendet.
            </div>
        @endif
    @else
        <div class="alert alert-danger">
            @if(Config::getProp('RECAPTCHA_VERSION') === 2)
                Bitte bestätigen Sie das Häkchen bei "Ich bin kein Roboter".
            @else
                🤖 Es ist ein Fehler aufgetreten.
            @endif
        </div>
    @endif
@endsection

@json([
    'status' => [
        'html' => $_->blade->yieldContent('status_html'),
        'success' => $success ?? false
    ]
])
