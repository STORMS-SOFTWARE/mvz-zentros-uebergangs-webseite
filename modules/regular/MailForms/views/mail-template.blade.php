<?php
/* @var $config STORMS\webframe\Modules\MailForms\MailerConfig */

$mail_label_map = array_merge($config->getTplLabelMap(), [
    // generic map
]);
$mail_value_map = array_merge([
    '_FORM_CB__YES' => '&#10004;',
    '_FORM_CB__NO' => '&#10007;',
], $config->getTplValueMap());
$omitted_fields = array_merge($config->getHiddenFormFields(), []);

function generateTable($data) { // TODO put this somewhere else
    $html = '<table>';
    $html .= '<tr>';
    foreach (array_keys($data) as $header) {
        $html .= '<th>' . $header . '</th>';
    }
    $html .= '</tr>';
    foreach ($data[array_key_first($data)] as $rowKey => $rowValue) {
        $html .= '<tr>';
        foreach ($data as $columnKey => $columnValues) {
            $html .= '<td>' . $columnValues[$rowKey] . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
}
?>

<style>
    blockquote {
        border-left: 4px solid grey;
        padding: 10px 0px 10px 15px;
        margin-left: 0px;
        color: #727272;
    }
    table {
        width: 100%;
        border: 1px solid black;
    }
    table th {
        text-transform: uppercase;
        background-color: #F5F5F5;
    }
    table td {
        border: 1px solid grey;
    }
    a {
        color: #96d20a;
    }
    .muted {
        color: #a9a9a9;
    }

    {!! $config->default_mail_template_chunks['style'] ?? '' !!}
    
</style>

{!! $config->default_mail_template_chunks['before-MailBodyIntro'] ?? '' !!}
{!!_md($config->getMailBodyIntro())!!}
{!! $config->default_mail_template_chunks['after-MailBodyIntro'] ?? '' !!}
<br/>
{!! $config->default_mail_template_chunks['before-PostFieldGeneration'] ?? '' !!}
@foreach($_POST as $label => $val)
    @if( !in_array(strtolower($label), ['message', 'msg', 'g-recaptcha-response', 'email', 'mail', 'e-mail', 'reqs', 'config']) && !in_array($label, ($omitted_fields??[])) && strpos($label, '_') !== 0 )
        {!! _isDev() ? "<small class=\"muted\">[$label]</small>&nbsp;" : '' !!}
        {{-- label: --}}
        @if(array_key_exists($label, $mail_label_map))
            <b>{{$mail_label_map[$label]}}:</b>
        @else
            <b>{{str_replace('_', ' ', $label)}}:</b>
        @endif
        {{-- value: --}}
        {!!
            strlen(trim($val=implode(', ', array_filter( ((array)$val), function($v){ return $v !== '-'; })))) === 0
                ? '<small><i class="muted">nicht angegeben</i></small>'
                : str_replace(array_keys($mail_value_map), array_values($mail_value_map), $val)
        !!}
        <br/>
    @endif
@endforeach
{!! $config->default_mail_template_chunks['after-PostFieldGeneration'] ?? '' !!}
{{-- generate tables (if needed) --}}
@if(($tdc = $config->getTableDataConfig()) !== null)
    @foreach($tdc as $group)
        <?php
        $table_map_propagated = [];
        foreach($group as $label => $key)
            $table_map_propagated[$label] = $_POST[$key] ?? [];
        echo generateTable($table_map_propagated);
        ?>
    @endforeach
@endif
@if($visitor_mail_addr)
    <b>E-Mail Adresse:</b> <a href="mailto:{{$visitor_mail_addr}}">{{$visitor_mail_addr}}</a>
@endif
<hr/>
@if(count(array_intersect($config->getHiddenFormFields(), ['msg', 'message', 'nachricht'])) === 0)
    @if($hasMessage = (isset($msg) && !empty($msg)))
        <h5>Nachricht/Anmerkung:</h5>
        <blockquote>
            {!!nl2br($msg)!!}
        </blockquote>
    @else
        @if(!$config->getHideEmptyMessageNote())
            <i>{{$config->getEmptyMessageNote()}}</i>
        @endif
    @endif
    @if($hasMessage || !$config->getHideEmptyMessageNote())
        <hr/>
    @endif
@endif
@if($cmf = $config->getCustomMailFooter())
    {!! $cmf !!}
@elseif($config->getAppendDefaultMailFooter())
    <i>
        Dies ist eine automatisch generierte Nachricht.
        <small class="muted">
            Sie haben Fragen? Wir helfen Ihnen gerne! <a href="http://storms-media.de">STORMS|MEDIA</a>
        </small>
    </i>
    <hr/>
@endif
