<?php
/* @var $_ \STORMS\webframe\Core\WebFrame */

/* BOOTSTRAP 4 & 5 ACCORDION / "COLLAPSE" component

usage (in any view or page):

@component('views.accordion', [ 'open' => -1, 'class' => 'acc-class'])
    @slot(['title' => 'acc title 1'])
        accordion content 1
    @endslot
    @slot(['title' => 'acc title 2'])
        accordion content 2
    @endslot
    {{-- alternative usage: --}}
    @slot('acc title 1')
        accordion content 1
    @endslot
    @slot('acc title 2')
        accordion content 2
    @endslot
@endcomponent

*/

if(isset($id) && !empty($id))
    $accId = $id;
else
    $accId = $_->accId = ($_->accId !== null) ? $_->accId+1 : 0;
?>

<div class="accordion {{$class ?? ''}}" id="accordion-{{$accId}}">
    @foreach ($slots as $i => $slotObj)
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{$accId}}-{{$loop->index}}">
                <button
                    type="button"
                    class="accordion-button {{$loop->index===($open??-1)?'':'collapsed'}}"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-{{$accId}}-{{$loop->index}}"
                    aria-expanded="{{$loop->index===($open??-1)?'true':'false'}}"
                    aria-controls="collapse-{{$accId}}-{{$loop->index}}"
                >
                    @if($t = $slotObj->getData('title'))
                        {!!$t!!}
                    @else
                        {!!$t=$slotObj->getName()!!}
                    @endif
                </button>
            </h2>
            <div
                 id="collapse-{{$accId}}-{{$loop->index}}"
                 class="accordion-collapse collapse {{$loop->index===($open??-1)?'show':''}}"
                 aria-labelledby="heading-{{$accId}}-{{$loop->index}}"
                 data-bs-parent="#accordion-{{$accId}}"
            >
                <div class="accordion-body">
                    {!!$slotObj->getContent()!!}
                </div>
            </div>
        </div>
    @endforeach
</div>

<?php
return;

/*
* Bootstrap 4: */
?>
<div class="accordion {{$class ?? ''}}" id="accordion-{{$accId}}">
    @foreach ($slots as $i => $slotObj)
        <div class="card">
            <div class="card-header" id="headingOne-{{$accId}}">
                <a data-toggle="collapse" href="#collapse-elem-{{$accId}}-{{$loop->index}}">
                    @if($t = $slotObj->getData('title'))
                        {!!$t!!}
                    @else
                        {!!$t=$slotObj->getName()!!}
                    @endif
                </a>
            </div>
            <div id="collapse-elem-{{$accId}}-{{$loop->index}}" class="collapse {{$loop->index===($open??-1)?'show':''}}" data-parent="#accordion-{{$accId}}">
                <div class="card-body">
                    {!!$slotObj->getContent()!!}
                </div>
            </div>
        </div>
    @endforeach
</div>
