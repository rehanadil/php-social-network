$(function()
{
    setInterval(function()
    {
        $("*[data-time]").each(function()
        {
            timeTag = $(this);
            timeSeconds = parseInt(timeTag.attr("data-time"));
            timeMilliseconds = timeSeconds * 1000;
            timeTag.attr("time-title", moment(timeMilliseconds).format());
            timeTag.timeagofull();
            timeTag.removeAttr("data-time");
        });
        $("*[data-time-short]").each(function()
        {
            timeTag = $(this);
            timeSeconds = parseInt(timeTag.attr("data-time-short"));
            timeMilliseconds = timeSeconds * 1000;
            timeTag.attr("time-title", moment(timeMilliseconds).format());
            timeTag.timeagoshort();
            timeTag.removeAttr("data-time-short");
        });
    }, 250);

    $(document).on("click", function(event){
        if(!$(event.target).closest(".popupContainer").length)
        {
            if($(".popupOverlay").is(":visible"))
            {
                $(".popupOverlay:not([data-manual])").fadeOut('fast', function(){
                    $(this).remove();
                });
            }
        }
        
        if(!$(event.target).closest(".storyControls").length)
        {
            if($(".controlBtns").is(":visible"))
            {
                $(".controlBtns").fadeOut('fast');
            }
        }
    });
});

function caretAdjust(el)
{
    el.focus();
    if (typeof window.getSelection != "undefined"
            && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}

function SK_intervalUpdates()
{
    $.get(
        Sk_requestPath(),
        {
            t: "interval"
        },
        function(e)
        {
            if (typeof e.notifications !== "undefined" && e.notifications > 0)
            {
                $("#notificationNav").find(".newAlert").text(e.notifications);
                if (e.notifications !== current_notif_count)
                {
                    document.getElementById("notification-sound").play();
                    current_notif_count = e.notifications;
                }
            }
            else
            {
                $("#notificationNav").find(".newAlert").text("");
            }
            

            if (typeof e.messages !== "undefined" && e.messages > 0)
            {
                $("#messageNav").find(".newAlert").text(e.messages);

                if ($(".chatWrapper").length == 1)
                {
                    loadNewChatMessages();
                }

                if (e.messages !== current_msg_count)
                {
                    document.getElementById("notification-sound").play();
                    current_msg_count = e.messages;
                }
            }
            else
            {
                $("#messageNav").find(".newAlert").text("");
            }


            if (typeof e.follow_requests !== "undefined" && e.follow_requests > 0)
            {
                $("#followersNav")
                    .attr("href", $("#followersNav").attr("href").replace("following", "requests"))
                    .find(".newAlert").text(e.follow_requests);
                if (e.follow_requests !== current_followreq_count)
                {
                    document.getElementById("notification-sound").play();
                    current_followreq_count = e.follow_requests;
                }
            }
            else
            {
                $("#followersNav").find(".newAlert").text("");
            }


            if (e.is_video_called === true)
            {
                if ($("#incomingVideoCall-" + e.video_call.id).length === 0) $(document.body).append(e.video_call.html);
            }
            else
            {
                $(".incomingVideoCallContainer").fadeOut('fast', function(){
                    $(this).remove();
                });
            }

            if (e.is_audio_called === true)
            {
                if ($("#incomingAudioCall-" + e.audio_call.id).length === 0) $(document.body).append(e.audio_call.html);
            }
            else
            {
                $(".incomingAudioCallContainer").fadeOut('fast', function(){
                    $(this).remove();
                });
            }
        }
    )
}

function SK_registerFollow(e) {
    element = $(".follow-" + e), SK_progressIconLoader(element), $.post(Sk_requestPath() + "?t=follow&a=follow", {
        following_id: e
    }, function(e) {
        200 == e.status && (element.after(e.html), element.remove());
        if (typeof(e.url) !== "undefined")
            window.location = e.url;
    })
}

function SK_openLightbox(e) {
    $("#mainHeaderJar").width() < 960 ? window.location = "index.php?a=story&id=" + e : ($(".sc-lightbox-container").remove(), $(document.body).append('<div class="pre_load_wrap"><div class="bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div></div>'), $.get(Sk_requestPath(), {
        t: "post",
        a: "lightbox",
        post_id: e
    }, function(e) {
        200 == e.status ? $(document.body).append(e.html) : $(".pre_load_wrap").remove()
    }))
}

function SK_closeWindow() {
    $(".window-container").remove(), $(document.body).css("overflow", "auto")
}

function SK_progressIconLoader(e) {
    e.each(function() {
        return progress_icon_elem = $(this).find("i.progress-icon"), default_icon = progress_icon_elem.attr("data-icon"), hide_back = !1, 1 == progress_icon_elem.hasClass("hide") && (hide_back = !0), 1 == $(this).find("i.fa-spinner").length ? (progress_icon_elem.removeClass("fa-spinner").removeClass("fa-spin").addClass("fa-" + default_icon), 1 == hide_back && progress_icon_elem.hide()) : progress_icon_elem.removeClass("fa-" + default_icon).addClass("fa-spinner fa-spin").show(), !0
    })
}

function SK_progressImageLoader(e) {
    e.each(function() {
        return elm=$(this),"none"==elm.css("display")?(elm.next("i.progress-icon").remove(),elm.show()):(elm.hide(),elm.after('<i class="fa fa-spinner fa-spin progress-icon"></i>'))
    })
}

function SK_generateUsername(e) {
    var t = e.replace(/[^A-Za-z0-9_\-\.]/gi, "").toLowerCase();
    $(".register-username-textinput").val(t).keyup()
}

function addEmoToInput(e, t) {
    inputTag = $(t), inputVal = inputTag.val(), "undefined" != typeof inputTag.attr("placeholder") && (inputPlaceholder = inputTag.attr("placeholder"), inputPlaceholder == inputVal && (inputTag.val(""), inputVal = inputTag.val())), 0 == inputVal.length ? inputTag.val(e + " ") : inputTag.val(inputVal + " " + e), inputTag.keyup()
}

function updateCountAlert() {
    $(".update-alert").each(function() {
        update_count = $(this).text(), update_count = 1 * update_count, 0 == update_count && $(this).addClass("hidden")
    })
}
documentTitle = document.title, current_notif_count = 0, current_msg_count = 0, current_followreq_count = 0, $(function() {
    setInterval(function() {
        SK_intervalUpdates()
    }, 7500), 1 == $(".chatWrapper").length && $(".chatMessages").scrollTop($(this).prop("scrollHeight")), $(document).on("focusin", "*[data-placeholder]", function() {
        elem = $(this), elem.val() == elem.attr("data-placeholder") && elem.val("")
    }), $(document).on("focusout", "*[data-placeholder]", function() {
        elem = $(this), 0 == elem.val().length && elem.val(elem.attr("data-placeholder"))
    }), $(document).on("keyup", "*[data-copy-to]", function() {
        elem = $(this), elem_val = elem.val(), elem_placeholder = elem.attr("data-placeholder"), elem_val == elem_placeholder ? $(elem.attr("data-copy-to")).val("") : $(elem.attr("data-copy-to")).val(elem_val)
    }), $(document).on("keyup", ".auto-grow-input", function() {
        elem = $(this), initialHeight = "10px", elem.attr("data-height") && (initialHeight = elem.attr("data-height") + "px"), this.style.height = initialHeight, this.style.height = this.scrollHeight + "px"
    })
});


function Sk_popupMedia(t,s)
{
    setTimeout(function(){
        _popupId = guidGenerator();
        _popupHtml = '<div id="' + _popupId + '" class="popupOverlay">';
            _popupHtml += '<div class="popupContainer" data-big="1">';

                if (t === "image")
                {
                    _popupHtml += '<div class="popupImage">';
                        _popupHtml += '<img src="' + s + '">';

                        _popupHtml += '<a class="closeBtn cursorPointer" onclick="Sk_popupClear();">';
                            _popupHtml += '<i class="fa fa-times-circle"></i>';
                         _popupHtml += '</a>';

                    _popupHtml += '</div>';
                }
                else if (t === "video")
                {
                _popupHtml += '<div class="popupVideo">';
                    _popupHtml += '<video id="' + _popupId + '-video" class="cursorPointer" src="' + s + '" controls preload="yes" autoplay="yes" width="100%"></video>';

                    _popupHtml += '<a class="playBtn cursorPointer">';
                        _popupHtml += '<i class="fa fa-pause-circle"></i>';
                    _popupHtml += '</a>';

                    _popupHtml += '<a class="closeBtn cursorPointer" onclick="Sk_popupClear();">';
                        _popupHtml += '<i class="fa fa-times-circle"></i>';
                    _popupHtml += '</a>';

                    _popupHtml += '<script>$(document).on("click", "#' + _popupId + ' .popupVideo", function(){';
                       _popupHtml += '_popupVideo = document.getElementById("' + _popupId + '-video");';
                        _popupHtml += 'if (_popupVideo.paused)';
                        _popupHtml += '{';
                            _popupHtml += '_popupVideo.play();';
                            _popupHtml += '$("#' + _popupId + ' .playBtn i").removeClass("fa-play-circle").addClass("fa-pause-circle");';
                        _popupHtml += '}';
                        _popupHtml += 'else';
                        _popupHtml += '{';
                            _popupHtml += '_popupVideo.pause();';
                            _popupHtml += '$("#' + _popupId + ' .playBtn i").removeClass("fa-pause-circle").addClass("fa-play-circle");';
                        _popupHtml += '}';
                    _popupHtml += '});</script>';

                _popupHtml += '</div>';
                }

            _popupHtml += '</div>';
        _popupHtml += '</div>';

        $(document.body).append(_popupHtml);
        $("#" + _popupId).fadeIn('fast');
    }, 1);
}

function Sk_popupClear()
{
    $(".popupOverlay").fadeOut('fast', function(){
        $(this).remove();
        $(document.body).css("overflow", "auto");
    });
}

function createVideoCall(rid)
{
    $.get(
        Sk_requestPath(),
        {
            t: "video_call",
            a: "call",
            receiver_id: rid
        },
        function (data)
        {
            if (data.status === 200) $(document.body).append(data.html);
        }
    );
}
function createAudioCall(rid)
{
    $.get(
        Sk_requestPath(),
        {
            t: "audio_call",
            a: "call",
            receiver_id: rid
        },
        function (data)
        {
            if (data.status === 200) $(document.body).append(data.html);
        }
    );
}

function guidGenerator()
{
    var S4 = function()
    {
       return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
    };
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
}

function hexToRgb(hex)
{
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function(m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function strip_tags(input, allowed)
{
    allowed = (((allowed || '') + '')
    .toLowerCase()
    .match(/<[a-z][a-z0-9]*>/g) || [])
    .join('');
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '')
    .replace(tags, function($0, $1)
    {
      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
    });
}

function Sk_formatInt(number, targetLength)
{
    output = number + '';
    while (output.length < targetLength) output = '0' + output;
    return output;
}

function readURL(input, img)
{
    if (input.files && input.files[0])
    {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(img).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$(function(){
    SK_intervalUpdates();
});