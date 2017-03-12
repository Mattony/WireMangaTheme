function CommentFormSetCookie(c, d, f) {
    var e = c + "=" + escape(d) + ";path=/";
    if (f == null) {
        f = 0
    }
    if (f > 0) {
        var b = new Date();
        var a = new Date();
        a.setTime(b.getTime() + 3600000 * 24 * f);
        document.cookie = e + ";expires=" + a.toGMTString()
    } else {
        document.cookie = e
    }
}
function CommentFormGetCookie(b) {
    var c = new RegExp("[; ]" + b + "=([^\\s;]*)");
    var a = (" " + document.cookie).match(c);
    if (b && a) {
        return unescape(a[1])
    }
    return ""
}
function CommentFormStars(c) {
    function a(e) {
        if (e.indexOf("&") == -1) {
            return e
        }
        var d = document.createElement("textarea");
        d.innerHTML = e;
        return d.value
    }
    function b(g, f) {
        var h = g.attr("data-onclass");
        var d = g.attr("data-on");
        if (typeof d != "undefined") {
            var e = g.attr("data-off");
            d = a(d);
            e = a(e)
        } else {
            var d = "";
            var e = ""
        }
        g.children("span").each(function() {
            var i = parseInt(c(this).attr("data-value"));
            if (i <= f) {
                if (d.length) {
                    c(this).html(d)
                }
                c(this).addClass(h)
            } else {
                if (e.length) {
                    c(this).html(e)
                }
                c(this).removeClass(h)
            }
        })
    }
    c(".CommentFormStars input").hide();
    c(document).on("click", ".CommentStarsInput span", function(g) {
        var d = parseInt(c(this).attr("data-value"));
        var f = c(this).parent();
        var h = f.prev("input");
        h.val(d);
        b(f, d);
        h.change();
        return false
    });
    c(document).on("mouseover", ".CommentStarsInput span", function(g) {
        var f = c(this).parent();
        var d = parseInt(c(this).attr("data-value"));
        b(f, d)
    }).on("mouseout", ".CommentStarsInput span", function(g) {
        var f = c(this).parent();
        var h = f.prev("input");
        var d = parseInt(h.val());
        b(f, d)
    })
}
jQuery(document).ready(function(c) {
    c(document).on("click", ".CommentActionReply", function() {
        var g = c(this);
        var f = g.parent().next("form");
        if (f.length == 0) {
            f = c("#CommentForm form").clone().removeAttr("id");
            f.hide().find(".CommentFormParent").val(c(this).attr("data-comment-id"));
            c(this).parent().after(f);
            f.slideDown()
        } else {
            if (!f.is(":visible")) {
                f.slideDown()
            } else {
                f.slideUp()
            }
        }
        return false
    });
    c(".CommentFormSubmit button").on("click", function() {
        var n = c(this);
        var p = n.closest("form.CommentForm");
        var m = p.find(".CommentFormStarsRequired");
        if (m.length) {
            var h = parseInt(m.find("input").val());
            if (!h) {
                alert(m.attr("data-note"));
                return false
            }
        }
        var i = p.find(".CommentFormCite input").val();
        var l = p.find(".CommentFormEmail input").val();
        var k = p.find(".CommentFormWebsite input");
        var f = k.length > 0 ? k.val() : "";
        var j = p.find(".CommentFormNotify :checked");
        var o = j.length > 0 ? j.val() : "";
        if (i.indexOf("|") > -1) {
            i = ""
        }
        if (l.indexOf("|") > -1) {
            l = ""
        }
        if (f.indexOf("|") > -1) {
            f = ""
        }
        var g = i + "|" + l + "|" + f + "|" + o;
        CommentFormSetCookie("CommentForm", g, 0)
    });
    var e = CommentFormGetCookie("CommentForm");
    if (e.length > 0) {
        var b = e.split("|");
        var a = c("form.CommentForm");
        a.find(".CommentFormCite input").val(b[0]);
        a.find(".CommentFormEmail input").val(b[1]);
        a.find(".CommentFormWebsite input").val(b[2]);
        a.find(".CommentFormNotify :input[value='" + b[3] + "']").attr("checked", "checked")
    }
    var d = false;
    c(".CommentActionUpvote, .CommentActionDownvote").on("click", function() {
        if (d) {
            return false
        }
        d = true;
        var f = c(this);
        c.getJSON(f.attr("data-url"), function(h) {
            if ("success"in h) {
                if (h.success) {
                    var g = f.closest(".CommentVotes");
                    g.find(".CommentUpvoteCnt").text(h.upvotes);
                    g.find(".CommentDownvoteCnt").text(h.downvotes);
                    f.addClass("CommentVoted")
                } else {
                    if (h.message.length) {
                        alert(h.message)
                    }
                }
            } else {
                d = false;
                return true
            }
            d = false
        });
        return false
    });
    if (c(".CommentStarsInput").length) {
        CommentFormStars(c)
    }
});
