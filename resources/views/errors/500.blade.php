<div class="content">
    <div class="title">Something went wrong.</div>
    @unless(empty($sentryID))
    <!-- Sentry JS SDK 2.1.+ required -->
        <script src="https://cdn.ravenjs.com/3.3.0/raven.min.js"></script>

        <script>
            Raven.showReportDialog({
                eventId: '{{ $sentryID }}',

                // use the public DSN (dont include your secret!)
                dsn: 'https://ad7d69ce22604d48b889d126d3fcdbfa@sentry.cs61a.org/16'
            });
        </script>
    @endunless
</div>