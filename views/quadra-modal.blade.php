<?php
/* Quadra Fullscreen Modal (Quadra TPL)

usage (in any view or page):

    <a class="qfm-trigger" href="#modal-foo">show modal a</a>
    OR
    <a href="javascript:;" data-qfm="#modal-foo">show modal a</a>

    @component('views.quadra-modal', ['id' => 'modal-foo', 'title' => 'Lorem', 'title_text_dark' => true])
        <div class="container">
            Lorem
        </div>
    @endcomponent
*/

if (!($_->quadra_modal_initialized ?? false)) {
    $_->on('body.beforeClose', function() {
        echo _sec('modals');
    });
}

if (!(isset($id) && !empty($id)))
    $id = $_->qModalId = isset($_->qModalId) ? $_->qModalId + 1 : 0;
?>

@section('modals')
    <section id="{{$id}}" class="quadra-fixed-modal slow-qdr {{$class ?? ''}}">
        <div class="quadra-fixed-modal_top slow-qdr no-border">
            <div class="qfm_title" style="color: {{($title_text_dark??false) ? 'black' : 'white'}}">
                <span class="modal_title">{!!$title ?? ''!!}</span>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 252 252" style="fill: {{($title_text_dark??false) ? '#000' : '#fff'}};">
                    <g>
                        <path d="M126,0C56.523,0,0,56.523,0,126s56.523,126,126,126s126-56.523,126-126S195.477,0,126,0z M126,234
                            c-59.551,0-108-48.449-108-108S66.449,18,126,18s108,48.449,108,108S185.551,234,126,234z"/>
                        <path d="M164.612,87.388c-3.515-3.515-9.213-3.515-12.728,0L126,113.272l-25.885-25.885c-3.515-3.515-9.213-3.515-12.728,0
                            c-3.515,3.515-3.515,9.213,0,12.728L113.272,126l-25.885,25.885c-3.515,3.515-3.515,9.213,0,12.728
                            c1.757,1.757,4.061,2.636,6.364,2.636s4.606-0.879,6.364-2.636L126,138.728l25.885,25.885c1.757,1.757,4.061,2.636,6.364,2.636
                            s4.606-0.879,6.364-2.636c3.515-3.515,3.515-9.213,0-12.728L138.728,126l25.885-25.885
                            C168.127,96.601,168.127,90.902,164.612,87.388z"/>
                    </g>
                </svg>
            </div>
        </div>
        <div class="sm_modal-content {{$content_class??''}}">
            <div class="title-strips"></div>
            {!!$component->getContent()!!}
        </div>
    </section>
@append

@if(!($_->quadra_modal_initialized??false))

    <!-- {{-- this is only done once --}} -->

    @dr
    <script>
        $('a.qfm-trigger, [data-qfm]').each(function () {

            var $qfm = null
            if($(this).data('qfm'))
                $qfm = $('.quadra-fixed-modal' + $(this).data('qfm'))
            else
                $qfm = $('.quadra-fixed-modal' + $(this).attr('href'))

            var $qfmtop = $qfm.find('.quadra-fixed-modal_top')
            $(this).add($qfmtop).click(function (e) {
                e.stopImmediatePropagation()
                $qfm.toggleClass('modal-active')
                if($qfm.hasClass('modal-active'))
                    $qfm.trigger('qfm.open', $qfm)
                else
                    $qfm.trigger('qfm.close', $qfm)
                $('body').toggleClass('qdr-modal-open')
                $qfm.find('.qfm_title').delay(100).fadeToggle(900)
                $qfm.animate({scrollTop: 0}, 'fast')
            })
        })
    </script>
    @enddr

    <style>
        @section('quadra-modal-css')

        /*
         * this code will be minified automatically
         */

        body.qdr-modal-open {
            overflow: hidden;
            height: 100%;
        }

        .title-strips {
            width: 100px;
            height: 1px;
            background-color: #c4c4c4;
            margin: 10px auto 30px;
        }

        .title-strips:after {
            content: '';
            width: 70px;
            height: 1px;
            position: relative;
            top: 8px;
            left: 15px;
            display: block;
            background-color: #c4c4c4;
        }

        .qfm_title {
            background-color: var(--main);
            /*position: relative;*/
            border-bottom: 1px solid #ddd;

            width: 100%;
            height: 100%;
            padding: 0 12px;
            line-height: inherit;
            position: absolute;
            text-align: center;
            font-size: 21px;
            text-transform: uppercase;

            display: none; /* toggled via js in order to get a smoother show/hide behav. */
        }

        .qfm_title i, .qfm_title svg {
            position: absolute;
            width: 100px;
            height: 100%;
            right: 0;
            top: 0;
            line-height: inherit;
            -webkit-transition: background 0.3s;
            -moz-transition: background 0.3s;
            transition: background 0.3s;
        }

        .qfm_title i {
            font-size: 23px;
        }
        .qfm_title svg {
            padding: 30px;
        }

        .qfm_title i:hover, .qfm_title svg:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        section.quadra-fixed-modal {
            padding: 0 !important;
        }

        .quadra-fixed-modal {
            visibility: hidden;
            position: fixed;
            will-change: transform;
            z-index: 1037;
            width: 100%;
            top: 100%;
            height: 100%;
            --webkit-transform: translateY(0) translateZ(0);
            --moz-transform: translateY(0) translateZ(0);
            --ms-transform: translateY(0) translateZ(0);
            transform: translateY(0) translateZ(0);
            overflow: hidden;
            background-color: white;
        }

        .quadra-fixed-modal.modal-active {
            top: 0;
            visibility: visible;
            --webkit-transform: translateY(0) translateZ(0);
            --moz-transform: translateY(0) translateZ(0);
            --ms-transform: translateY(0) translateZ(0);
            transform: translateY(0) translateZ(0);
            height: 100%;
            overflow-y: auto;
        }

        .quadra-fixed-modal_top {
            position: relative;
            top: 0;
            height: 60px;
            line-height: 64px;
            cursor: pointer;
        }

        /* HEIGHT OF THE TITLE BAR */
        .quadra-fixed-modal.modal-active .quadra-fixed-modal_top {
            height: 100px;
            line-height: 100px;
        }

        .slow-qdr {
            -webkit-transition: all .8s cubic-bezier(0.77, 0, 0.2, 1) !important;
            -moz-transition: all .8s cubic-bezier(0.77, 0, 0.2, 1) !important;
            transition: all .8s cubic-bezier(0.77, 0, 0.2, 1) !important
        }

        @media screen and (max-width: 480px) {
            .qfm_title {
                font-size: 17px;
            }

            .qfm_title i {
                font-size: 17px;
                width: 80px;
            }
            .qfm_title svg {
                padding: 10px;
                width: 45px;
            }

            .quadra-fixed-modal.modal-active .quadra-fixed-modal_top {
                height: 50px;
                line-height: 50px;
            }
        }
        @endsection

        <?php
        $_->quadra_modal_initialized = true;

        $m = new \MatthiasMullie\Minify\CSS();
        $m->add(_sec('quadra-modal-css'));
        echo $m->minify();
        ?>

    </style>

@endif
