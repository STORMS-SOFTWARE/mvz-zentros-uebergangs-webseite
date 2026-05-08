<?php
/* BOOTSTRAP 4+5 TABS component

usage (in any view or page):

@component('views.tabs', ['active_slot' => -1 , 'class' => 'someClass' ])
    @slot(['title' => 'tab title 1'])
        tab content 1
    @endslot
    @slot(['title' => 'tab title 2'])
        tab content 2
    @endslot
    {{-- alternative usage: --}}
    @slot('tab title 1')
        tab content 1
    @endslot
    @slot('tab title 2')
        tab content 2
    @endslot
@endcomponent
*/

if(isset($id) && !empty($id))
    $tabId = $id;
else
    $tabId = $_->tabId = isset($_->tabId) ? $_->tabId+1 : 0;

$active_slot = $active_slot ?? 0;
?>

<ul class="nav nav-tabs {{$nav_class ?? ''}}" role="tablist">
    @foreach ($slots as $i => $slotObj)
        <li class="nav-item">
            <a data-toggle="tab" data-bs-toggle="tab" href="#{{$slotObj->getData('id', 'tab-'.$tabId.'-'.($loop->index))}}" class="nav-link {{$loop->index==$active_slot?'active':''}}" role="tab">
                @if($t = $slotObj->getData('title'))
                    {!!$t!!}
                @else
                    {!!$slotObj->getName()!!}
                @endif
            </a>
        </li>
    @endforeach
</ul>
<div class="tab-content {{$tab_content_class ?? ''}}">
    @foreach ($slots as $i => $slotObj)
        <div id="{{$slotObj->getData('id', 'tab-'.$tabId.'-'.($loop->index))}}" class="tab-pane fade {{$loop->index==$active_slot?'show active':''}}" role="tabpanel" >
            {!!$slotObj->getContent()!!}
        </div>
    @endforeach
</div>
