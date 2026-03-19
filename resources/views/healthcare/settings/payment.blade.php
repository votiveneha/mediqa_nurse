<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment</title>
</head>
<body>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<form action="{{ route('medical-facilities.payment.checkout') }}" method="POST">
    @csrf

    <script
        src="https://checkout.stripe.com/checkout.js"
        class="stripe-button"
        data-key="{{ config('services.stripe.key') }}"
        data-amount="10000"
        data-name="Test Payment"
        data-description="Payment Example"
        data-currency="inr">
    </script>
</form>

</body>
</html>