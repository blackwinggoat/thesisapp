@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Chat</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Chat</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <!-- container element in which TalkJS will display a chat UI -->
            <div id="talkjs-container" style="width: 90%; margin: 30px; height: 500px"><i>Loading chat...</i></div>
        </div><!-- /.the-box -->
    </div><!-- /.container-fluid -->
</div>
@endsection

<!-- minified snippet to load TalkJS without delaying your page -->
<script>
    (function (t, a, l, k, j, s) {
        s = a.createElement('script');
        s.async = 1;
        s.src = "https://cdn.talkjs.com/talk.js";
        a.head.appendChild(s);
        k = t.Promise;
        t.Talk = {
            v: 3,
            ready: {
                then: function (f) {
                    if (k) return new k(function (r, e) {
                        l.push([f, r, e])
                    });
                    l
                        .push([f])
                },
                catch: function () {
                    return k && new k()
                },
                c: l
            }
        };
    })(window, document, []);
</script>

<script>
Talk.ready.then(function() {
    var me = new Talk.User({
        id: "123456",
        name: "Alice",
        email: "alice@example.com",
        photoUrl: "https://demo.talkjs.com/img/alice.jpg",
        welcomeMessage: "Hey there! How are you? :-)"
    });
    window.talkSession = new Talk.Session({
        appId: "tcWNnhJA",
        me: me
    });
    var other = new Talk.User({
        id: "654321",
        name: "Sebastian",
        email: "Sebastian@example.com",
        photoUrl: "https://demo.talkjs.com/img/sebastian.jpg",
        welcomeMessage: "Hey, how can I help?"
    });

    var conversation = talkSession.getOrCreateConversation(Talk.oneOnOneId(me, other))
    conversation.setParticipant(me);
    conversation.setParticipant(other);
    var inbox = talkSession.createInbox({selected: conversation});
    inbox.mount(document.getElementById("talkjs-container"));
});
</script>