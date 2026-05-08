<?php
$default_opacity = 0.02;
?>

<style>
    #bs-grid-helper {
        position: fixed;
        bottom: 50px;
        left: 50px;
        z-index: 9999999;
        background: #fff;
        opacity: {{$_SESSION['bs-grid-helper--default-opacity']??$default_opacity}};
        cursor: pointer;
        transition: all 0.5s;
    }
    #bs-grid-helper:hover {
        opacity: 0.8;
    }

    #bs-grid-helper #bs-grid-helper--usage-info {
        position: absolute;
        right: -4px;
        top: -10px;
        background-color: white;
        text-align: center;
        border-radius: 100%;
        line-height: 0;
    }

    span.bs-vp-span {
        width: 100%;
        font-size: 30px;
        line-height: 40px;
        text-align: center;
        padding: 0 8px;
    }

    #bs-grid-helper > div > div:first-child {
        position: absolute;
        top: -10px;
        font-size: 12px;
        background-color: white;
        line-height: normal;
        font-weight: bold;
    }
    #bs-grid-helper > div  {
        position: relative;
        z-index: 10;
        background-color: rgba(255,255,255, 0.8);
    }

    #grid-helper--bs-3 {
        border: 3px solid darkred;
    }
    #grid-helper--bs-4 {
        border: 3px solid greenyellow;
    }

    a.bs-grid-helper-set-opa {
        font-size: 12px;
        line-height: normal;
        display: block;
    }

    #grid-helper--qm {
        width: 15px;
        height: 15px;
    }

    #grid-helper--pin {
        position: absolute;
        display: none;
    }
</style>
@section('bs-grid-helper-usage-info')
    <a href="javascript:;" id="bs-grid-helper--usage-info" title="LEFT click: temp. HIDE layer ;;; MIDDLE click: temp. KEEP layer VISIBLE">
        <img id="grid-helper--qm" src="{{$current_base}}/assets/question-mark-circle.svg" alt="">
    </a>
@endsection
<div id="bs-grid-helper">
    <img id="grid-helper--pin" src="{{$current_base}}/assets/pin.svg" alt="">
    {{-- for bs3 --}}
    <div id="grid-helper--bs-3" class="d-none">
        <div>BS 3</div> @yield('bs-grid-helper-usage-info')
        <span class="bs-vp-span visible-lg">LG</span>
        <span class="bs-vp-span visible-md">MD</span>
        <span class="bs-vp-span visible-sm">SM</span>
        <span class="bs-vp-span visible-xs">XS</span>
    </div>
    {{-- for bs4 --}}
    <div id="grid-helper--bs-4" class="hidden-xs hidden-sm hidden-md hidden-lg">
        <div>BS 4</div> @yield('bs-grid-helper-usage-info')
        <span class="bs-vp-span d-block d-sm-none">XS</span>
        <span class="bs-vp-span d-none d-sm-block d-md-none">SM</span>
        <span class="bs-vp-span d-none d-md-block d-lg-none">MD</span>
        <span class="bs-vp-span d-none d-lg-block d-xl-none">LG</span>
        <span class="bs-vp-span d-none d-xl-block">XL</span>
    </div>
    <a class="bs-grid-helper-set-opa" href="javascript:;" title="Default opacity für den boostrap grid helper setzen und in der Session speichern">setDefOpa</a>
</div>
<script>
    $(function () {
        $('#bs-grid-helper').click(function(){
            $(this).css({
                'overflow' : 'hidden',
                'background-color' : 'red',
            })
            $(this).animate({height:0})
        }).mousedown(function(e) { // MIDDLE mouse click
            if(e.which === 2) {
                $(this).css('opacity', 1)
                $('#grid-helper--pin').fadeIn()
                return true
            }
        })

        $('.bs-grid-helper-set-opa').click(function(e) {
            const new_opacity = prompt('Bitte default opacity eingeben.\n0.02 ist der Standardwert', {{$_SESSION['bs-grid-helper--default-opacity']??$default_opacity}})
            if(new_opacity !== null) {
                $('#bs-grid-helper').css('opacity', new_opacity)
                $.post('/bs-grid-helper--set-opacity', {new_opacity}, function() {
                    console.info('Default opacity in Deiner Session gespeichert.')
                })
            }
            e.stopImmediatePropagation()
            e.stopPropagation()
        })

        // remove the grid helper if both helper layers (for BS3+4) are found (and we therefor know that no compatible bootstrap version is loaded in the page)
        if($('#grid-helper--bs-3').is(':visible') && $('#grid-helper--bs-4').is(':visible')) {
            console.info('[BOOTSTRAP GRID HELPER] Es scheint dass weder Bootstrap 3 noch Bootstrap 4 in der Seite verwendet wird. Deaktiviere den Bootstrap Grid Helper.')
            $('#bs-grid-helper').remove()
        }

    })
</script>
