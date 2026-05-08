<?php
/* @var $_ \STORMS\webframe\Core\WebFrame */

/* @var $id null|string */
/* @var $buttons null|STORMS\webframe\Core\Slot */

/*
*****************************
*****************************
BS 5 Modal
*****************************
*****************************

If needed: BS 4 modal: https://github.com/STORMS-SOFTWARE/webframe/blob/b086a933f002f282f43f2a2c5edef78543f04048/template/views/modal.blade.php

======================================================================================================================

USAGE (in any view or page):

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-foobar">
      Launch demo modal
    </button>

    @component('views.modal', ['id' => 'modal-foobar', 'title' => 'Lorem'])
        Lorem
    @endcomponent

Save button handling implementation (if use_save_button is set to true):

    $('#modal-foobar').on('saveBtnClicked', function(ev, modalElem, closeFunc) {
        console.info("default save button clicked")
        closeFunc();
    });

or

    $('#modal-foobar').on('customBtnClicked', function(ev, modalElem, btnElem, closeFunc) {
        console.info("custom button clicked")
        closeFunc();
    });
*/

if(!(isset($id) && !empty($id)))
    $id = $_->bsModalId = ($_->bsModalId !== null) ? $_->bsModalId + 1 : 0;

if($_->hasBsModal === null) {
    $_->hasBsModal = true;
    $isFirstModal = true;
}
else
    $isFirstModal = false;

$isFullscreen = isset($modal_size) && str_contains($modal_size, 'fullscreen');

$hasButtons = isset($buttons);
?>

@if($isFirstModal) <?php
    $_->on(\STORMS\webframe\Core\WebFrame::EVENT__BODY_BEFORE_CLOSE, function() { ?>
        <script>
            const _modalComponentInternal_doCloseModal = function(modal) {
                bootstrap.Modal.getInstance(modal).hide();
            }

            // handler for default save button
            function _modalComponentInternal_modalSaveBtnClicked(btnElem) {
                // TODO prevent default
                btnElem = $(btnElem);
                const modal = btnElem.parents('.modal');
                modal.trigger('saveBtnClicked', [modal, ()=>{_modalComponentInternal_doCloseModal(modal)}])
            }

            // handler for custom buttons
            [
                document.querySelector('.modal .custom-modal-buttons a'),
                document.querySelector('.modal .custom-modal-buttons button'),
                document.querySelector('.modal .custom-modal-buttons .custom-modal-button')
            ].forEach((elem) => {
                if(elem !== null) {
                    elem.addEventListener('click', function(ev) {
                        ev.preventDefault();
                        const btnElem = $(ev.target);
                        const modal = btnElem.parents('.modal');
                        modal.trigger('customBtnClicked', [modal, btnElem, ()=>{_modalComponentInternal_doCloseModal(modal)}])
                    });
                }
            });
        </script><?php
    }); ?>
@endif

@section('modals')
    <div class="modal fade" id="{{$id}}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog {{$isFullscreen ? 'modal-dialog-scrollable' : 'modal-dialog-centered'}} {{isset($modal_size) ? "modal-$modal_size" : ''}}">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">{{$title}}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body last-paragraph-no-margin">
                    {!! $content !!}
                </div>
                @if($isFullscreen)
                    <div class="modal-footer">
                        <button type="button" class="{{$btn_classes['close'] ?? Config::getProp('MODAL_CLOSE_BTN_CLASS') ?? 'btn btn-secondary'}}" data-bs-dismiss="modal">
                            {{$btn_labels['close'] ?? Config::getProp('MODAL_CLOSE_BTN_LABEL') ?? 'Schließen'}}
                        </button>
                        @if($use_save_button ?? false)
                            <button type="button" class="{{$btn_classes['save'] ?? Config::getProp('MODAL_SAVE_BTN_CLASS') ?? 'btn btn-primary'}}" onclick="_modalComponentInternal_modalSaveBtnClicked(this)">
                                {{$btn_labels['save'] ?? Config::getProp('MODAL_SAVE_BTN_LABEL') ?? 'Speichern'}}
                            </button>
                        @endif
                        @if($hasButtons)
                            <div class="custom-modal-buttons">
                                {!! $buttons !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@append
