<?php
/*
 * this view is directly appended to the DOM when the preview potection is active and the user logged in successfully.
 * It allows to show a direct access token that bypasses the login page
 */
?>
<style>
    #preview-token {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        padding: 20px;
        text-align: center;
        border: 1px solid #ccc;
        background-color: white;
        z-index: 9999999;

        display: none;
    }
</style>
<div id="preview-token">
    <b>preview-bypass url:</b>
    <br/>
    {{$getDirectAccessUrl()}}
    <a href="javascript:;" id="close-preview-token-link" style="float: right">&times;</a>
</div>

<script>
    $(document).ready(function() {

        $(document).keyup(function (event) {
            // capture {{-- shift+ctrl+enter --}} key combination in order to display the access token
            if (event.keyCode === 13 && event.shiftKey && event.ctrlKey) {
                $('#preview-token').css('display', 'block').hide().fadeIn();
            }
        });

        $('#close-preview-token-link').click(function() {
            $('#preview-token').fadeOut().remove();
        });

    });
</script>
