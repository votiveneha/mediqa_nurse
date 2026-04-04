<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('nurse/assets/imgs/template/favicon.png')}}">
	<meta name="csrf-token" content="{{ csrf_token() }}">

     <link rel="stylesheet" type="text/css" href="{{ asset('nurse/assets/css/stylecd4e.css?version=4.1')}}">
     <link rel="stylesheet" type="text/css" href="{{ asset('nurse/assets/css/new_style.css')}}">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
	 <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">-->
	 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
    <script src="https://kit.fontawesome.com/107d2907de.js" crossorigin="anonymous"></script>
	 <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    @if(Auth::guard('nurse_middle')->check() || Auth::guard('healthcare_facilities')->check() || Auth::check())
    <!-- Pusher & Echo -->
    <script src="https://js.pusher.com/8.4/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster", "mt1") }}',
            forceTLS: true,
            encrypted: true,
            authEndpoint: '{{ url("/broadcasting/auth") }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            },
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
        console.log('✅ Echo initialized in head');
    </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
	
    
     <title>{{ env('APP_NAME') }}</title>
     <style>
      span.d-flex.align-items-center.justify-content-center {
        font-size: 13px;
        gap: 15px;
      }
     </style>
  </head>

<body class="home">
    <div class="page-wrapper">
            @include('nurse.layouts.header')
             @yield('css')
            @include('nurse.layouts.style')
            @yield('content')
        
        @include('nurse.layouts.footer')
        @include('nurse.layouts.js')
        <!-- Bootstrap-select initialization -->
        <script>
          $(document).ready(function() {
            $('.selectpicker').selectpicker();
          });
        </script>
        
        <!-- Site-wide Real-time Notifications -->
        @if(Auth::guard('nurse_middle')->check() || Auth::guard('healthcare_facilities')->check() || Auth::check())
        <script>
            (function() {
                // Echo already initialized in head, just use it
                const userId = {{ Auth::guard('nurse_middle')->id() ?? Auth::guard('healthcare_facilities')->id() ?? Auth::id() }};

                // Listen for new messages on user's private channel (for header notifications)
                Echo.private('user.' + userId)
                    .listen('.message.sent', function(e) {
                        console.log('🔔 Site-wide notification:', e);
                        updateNotificationCount(e);
                        showBrowserNotification(e);
                    });

                // Update notification count in header
                function updateNotificationCount(e) {
                    const badges = document.querySelectorAll('.notification-badge');
                    badges.forEach(badge => {
                        let currentCount = parseInt(badge.textContent) || 0;
                        let newCount = currentCount + 1;
                        badge.textContent = newCount;
                        badge.style.display = 'inline-block';
                    });

                    if (badges.length === 0) {
                        const bells = document.querySelectorAll('#dropdownNotify');
                        bells.forEach(bell => {
                            const badge = document.createElement('span');
                            badge.className = 'notify-badge badge rounded-pill bg-danger notification-badge';
                            badge.textContent = '1';
                            bell.appendChild(badge);
                        });
                    }

                    const countTexts = document.querySelectorAll('.notification-count-text');
                    countTexts.forEach(text => {
                        let currentCount = parseInt(text.textContent) || 0;
                        text.textContent = (currentCount + 1) + ' notifications';
                    });

                    const messageCountTexts = document.querySelectorAll('.message-count-text');
                    messageCountTexts.forEach(text => {
                        let currentCount = parseInt(text.textContent) || 0;
                        text.textContent = (currentCount + 1) + ' messages';
                    });

                    const originalTitle = document.title.replace(/^\(\d+\)\s*/, '');
                    document.title = '(' + (parseInt(badges[0]?.textContent || 0) + 1) + ') ' + originalTitle;
                }

                // Show browser notification
                function showBrowserNotification(e) {
                    if (!("Notification" in window)) return;
                    if (Notification.permission === "granted") {
                        new Notification("New Message from " + e.sender_name, {
                            body: e.message,
                            icon: e.sender_avatar || '{{ asset('nurse/assets/imgs/nurse06.png') }}'
                        });
                    } else if (Notification.permission !== "denied") {
                        Notification.requestPermission();
                    }
                }

                console.log('✅ Site-wide notifications active for user:', userId);
            })();
        </script>
        @endif
        
        @yield('js')
      </body>

</html>


