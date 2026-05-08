<?php
if(!isset($main_color))
    throw new Exception('main_color not set');
?>

<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
    </svg>
</div>

@dr
<script>
    var progressPath = document.querySelector('.progress-wrap path');
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    var updateProgress = function () {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var progress = pathLength - (scroll * pathLength / height);
        progressPath.style.strokeDashoffset = progress;
    }
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 50;
    var duration = 350;
    jQuery(window).on('scroll', function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.progress-wrap').addClass('active-progress');
        } else {
            jQuery('.progress-wrap').removeClass('active-progress');
        }
    });
    jQuery('.progress-wrap').on('click', function(event) {
        event.preventDefault();
        jQuery('html, body').animate({scrollTop: 0}, duration);
        return false;
    })
</script>
@enddr

<style>
    .progress-wrap {
        position: fixed;
        right: 40px;
        bottom: 60px;
        height: 46px;
        width: 46px;
        cursor: pointer;
        display: block;
        border-radius: 50px;
        box-shadow: inset  0 0 0 2px rgba(0,0,0,0.1);
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(15px);
        -webkit-transition: all 200ms linear;
        transition: all 200ms linear;
        mix-blend-mode: hard-light;
    }
    .progress-wrap.active-progress {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .progress-wrap::after,
    .progress-wrap::before
    {
        font-weight: 300;
        position: absolute;
        font-family: 'Font Awesome 6 Pro';
        text-align: center;
        line-height: 46px;
        font-size: 20px;
        color: {{$main_color}};
        left: 0;
        top: 0;
        height: 46px;
        width: 46px;
        cursor: pointer;
        display: block;
        z-index: 1;
        -webkit-transition: all 200ms linear;
        transition: all 200ms linear;
    }
    .progress-wrap::after {
        content: '\f0d8';
    }
    .progress-wrap::before {
        opacity: 0;
        content: '\f077';
    }
    .progress-wrap:hover::after {
        opacity: 0;
    }
    .progress-wrap:hover::before {
        opacity: 1;
    }

    .progress-wrap svg path {
        fill: none;
    }
    .progress-wrap svg.progress-circle path {
        stroke: {{$main_color}};
        stroke-width: 4;
        box-sizing:border-box;
        -webkit-transition: all 200ms linear;
        transition: all 200ms linear;
    }
    .progress-wrap::before {
        color: {{$main_color}};
    }

    .progress-wrap svg.progress-circle path {
        stroke: {{$main_color}};
    }
    
    @media (max-width: 575.98px) {
        .to-top-progress-wrap {
            right: 15px;
            bottom: 15px;
        }
        .to-top-progress-wrap, .to-top-progress-wrap::before, .to-top-progress-wrap::after {
            height: 30px;
            width: 30px;
        }
        .to-top-progress-wrap::after {
            line-height: 34px;
        }
    }
</style>
