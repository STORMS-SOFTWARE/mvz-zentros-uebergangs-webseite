<?php
/*
 * open graph facebook tags
 */
?>
@if(isset($img) && !$_->isDevOrPreviewServer())
    <meta property="og:url" content="{{WEB_URL_FULL}}" />
    <meta property="og:title" content="{{$_->getTitle()}}" />
    <meta property="og:description" content="{{$_->getDescription()}}" />
    <meta property="og:image" content="{{str_starts_with($img, 'http') ? '' : WEB_URL_FULL}}{{$img}}" />
@else
    <!-- open graph tags omitted due missing img config -->
@endif
